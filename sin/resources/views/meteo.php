<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="A web interface for MQTT over Websockets">
    <meta name="author" content="Fabian Affolter">

    <title>Meteo</title>

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <!-- jQuery -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <!-- Sparkline -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-sparklines/2.1.2/jquery.sparkline.min.js"></script>
    <!-- jgPlot -->
    <link class="include" rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/jqPlot/1.0.9/jquery.jqplot.min.css" />
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jqPlot/1.0.9/jquery.jqplot.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jqPlot/1.0.9/plugins/jqplot.canvasTextRenderer.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jqPlot/1.0.9/plugins/jqplot.canvasAxisLabelRenderer.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jqPlot/1.0.9/plugins/jqplot.dateAxisRenderer.js"></script>
    <!-- MQTT Websocket -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/paho-mqtt/1.0.2/mqttws31.js"></script>
    <script type="text/javascript">
        var host = "192.168.11.7";
        var port = 9001; //3000;
        var topic = '#'; // subscribe too al the topic
        var useTLS = false;
        var cleansession = true;
        var mqtt;
        var reconnectTimeout = 2000;

        var livingTemp = new Array();
        var anemometroValues = new Array();


        function MQTTconnect() {
	        if (typeof path == "undefined") {
                 path = '/';// '/mqtt';
            }
	        mqtt = new Paho.MQTT.Client(host, port, path, "mqtt_panel" + parseInt(Math.random() * 100, 10)
	    );
            var options = {
                timeout: 3,
                useSSL: useTLS,
                cleanSession: cleansession,
                onSuccess: onConnect,
                onFailure: function (message) {
                    $('#status').html("Connection failed: " + message.errorMessage + "Retrying...");
                    setTimeout(MQTTconnect, reconnectTimeout);
                }
            };

            mqtt.onConnectionLost = onConnectionLost;
            mqtt.onMessageArrived = onMessageArrived;
            console.log("Host: "+ host + ", Port: " + port + ", Path: " + path + " TLS: " + useTLS);
            mqtt.connect(options);
        };

        function onConnect() {
            $('#status').html('Connected to ' + host + ':' + port + path);
            mqtt.subscribe(topic, {qos: 0});
            $('#topic').html(topic);
        };

        function onConnectionLost(response) {
            setTimeout(MQTTconnect, reconnectTimeout);
            $('#status').html("Connection lost: " + response.errorMessage + ". Reconnecting...");
        };

        function onMessageArrived(message) {
            var topic = message.destinationName;
            var payload = message.payloadString;
            console.log("Topic: " + topic + ", Message payload: " + payload);
            $('#message').html(topic + ', ' + payload);
            var message = topic.split('/');
            var area = message[0];
            var sensor = message[1];

            var timestamp = Math.round((new Date()).getTime() / 1000);
            switch (sensor) {
                case 'temperatura': 
                    if (payload >= 25) {
                            $('#temperatura_label').text(payload + '°C - too hot');
                            $('#temperatura_label').removeClass('badge-warning badge-success badge-info badge-primary').addClass('badge-danger');
                    } else if (payload >= 21) {
                            $('#temperatura_label').text(payload + '°C - hot');
                            $('#temperatura_label').removeClass('badge-danger badge-success badge-info badge-primary').addClass('badge-warning');
                    } else if (payload >= 18) {
                            $('#temperatura_label').text(payload + '°C - normal');
                            $('#temperatura_label').removeClass('badge-danger badge-warning badge-info badge-primary').addClass('badge-success');
                    } else if (payload >= 15) {
                            $('#temperatura_label').text(payload + '°C - low');
                            $('#temperatura_label').removeClass('badge-danger badge-warning badge-success badge-primary').addClass('badge-info');
                    } else if (mpayload <= 12) {
                            $('#temperatura_label').text(payload + '°C - too low');
                            $('#temperatura_label').removeClass('badge-danger badge-warning badge-success badge-info').addClass('badge-primary');
                    }
                    $('#temperatura_value').html('(Sensor value: ' + payload + ')');
                    // $('#temperatura_label').text(payload + '°C');
                    var entry = new Array();
                    entry.push(timestamp);
                    entry.push(parseInt(payload));
                    console.log("Living temp");

                    livingTemp.push(entry);

                    // Show only 3 values
                    if (livingTemp.length >= 20) {
                        livingTemp.shift()
                    }

                    var livingTempPlot = $.jqplot('TemperaturaChart', [livingTemp], {
                        axes:{
                            xaxis:{
                                
                                renderer:$.jqplot.DateAxisRenderer,
                                tickOptions:{
                                    formatString:'%T'
                                } 
                            },
                            yaxis:{
                                min:-10, 
                                max:45,
                                tickOptions:{
                                    formatString:'%.2f°'
                                }
                            }
                        },
                        highlighter: {
                            show: true,
                            sizeAdjust: 7.5
                        },
                        cursor: {
                            show: false
                        }
                    });

                    break;
                case 'umidita': 
                    $('#umidita_label').text(payload );
                    $('#umidita_label').removeClass('badge-secondary').addClass('badge-primary');
                    $('#umidita_value').html('(valore: ' + payload + ' %)');
                    break;
                case 'anemometro':
                    $('#anemometro_value').html('(valore: ' + payload + ' km/h)');
                    $('#anemometro_value').removeClass('badge-secondary').addClass('badge-primary');
                    $('#anemometro_label').html(payload );
                    
                    var entry = new Array();
                    entry.push(timestamp);
                    entry.push(parseInt(payload));
                    anemometroValues.push(entry);

                    // // Show only 3 values
                    // if (livingTemp.length >= 20) {
                    //     livingTemp.shift()
                    // }

                    var livingTempPlot = $.jqplot('AnemometroChart', [anemometroValues], {
                        axes:{
                            xaxis:{
                                
                                renderer:$.jqplot.DateAxisRenderer,
                                tickOptions:{
                                    formatString:'%T'
                                } 
                            },
                            yaxis:{
                                min:-10, 
                                max:45,
                                tickOptions:{
                                    formatString:'%.2f'
                                }
                            }
                        },
                        highlighter: {
                            show: true,
                            sizeAdjust: 7.5
                        },
                        cursor: {
                            show: false
                        }
                    });

                    break;
              case 'pressione':
                    $('#pressione_value').html('(Pascal value: ' + payload + ')');

                    var bar = parseFloat(payload/ 100000).toFixed(2);
                    $('#pressione_bar_label').text(bar + ' bar');
                    var atmosfere = parseFloat(payload/ 101325).toFixed(2);
                    $('#pressione_bar_label').removeClass('badge-secondary').addClass('badge-primary');
                    $('#pressione_pressione_label').text(atmosfere +' atmosfere');
                    $('#pressione_pressione_label').removeClass('badge-secondary').addClass('badge-primary');
                    break;
            case 'IR':
                $('#luce_ir_value').html('Infrarossi: ' + payload );
                $('#luce_ir_value').removeClass('badge-secondary').addClass('badge-primary');
            case 'UV':
                $('#luce_uv_value').html('Ultra Violetti: ' + payload);
                $('#luce_uv_value').removeClass('badge-secondary').addClass('badge-primary');
            case 'visibile':
                $('#luce_visibile_value').html('Visibile: ' + payload );
                $('#luce_visibile_value').removeClass('badge-secondary').addClass('badge-primary');
                break;
            case "pluviometro":
                $('#pluviometro_value').html('Millimetri: ' + payload );
                $('#pluviometro_value').removeClass('badge-secondary').addClass('badge-primary');
                break;
            default: 
                console.log('Error: <'+ sensor +'> do not match the MQTT topic.'); 
                break;
            }
        };
        $(document).ready(function() {
            MQTTconnect();
        });
    </script>

  </head>

  <body>
    <div id="wrap">
      <div class="container">
        <div class="page-header my-4"><h1><b>Stazione Meteo Nomadelfia</b></h1></div>
            <div class="panel panel-default">
              <div class="panel-body">
                    <table class="table table-striped">
                         <!-- Temperatura-->
                        <tr>
                            <td width="40%" style="vertical-align:middle;"><h3>Temperatura</h3><small id="temperatura_value">(no value received)</small></td>
                            <td style="vertical-align:middle;"><div id="TemperaturaChart" style="height:150px; width:400px;"></div></td>
                            <td width="30%" style="vertical-align:middle;"><h4>&nbsp;<span id="temperatura_label" class="badge badge-secondary">Unknown</span></h4></td>
                        </tr>
                        <!-- Anemometro door -->
                        <tr>
                                <td width="40%" style="vertical-align:middle;"><h3>Anemometro</h3><small id="anemometro_value">(no value received)</small></td>
                                <td style="vertical-align:middle;"><div id="AnemometroChart" style="height:150px; width:400px;"></div></td>
                                <td width="30%" style="vertical-align:middle;"><h4>&nbsp;<span id="anemometro_label" class="badge badge-secondary">Unknown</span></h4></td>
                        </tr>
                        <!-- Pluviometro door -->
                        <tr>
                                <td width="40%" style="vertical-align:middle;"><h3>Pluviometro</h3><small id="pluviometro_value">(no value received)</small></td>
                                <td style="vertical-align:middle;"></td>
                                <td width="30%" style="vertical-align:middle;">
                                    <h4>&nbsp;<span id="pluviometro_label" class="badge badge-secondary">Unknown</span></h4>
                                </td>
                        </tr>
                        <!-- Umidità door -->
                        <tr>
                            <td width="40%" style="vertical-align:middle;"><h3>Umidità</h3><small id="umidita_value">(no value received)</small></td>
                            <td style="vertical-align:middle;"></td>
                            <td width="30%" style="vertical-align:middle;"><h4>&nbsp;<span id="umidita_label" class="badge badge-secondary">Unknown</span></h4></td>
                        </tr>
                        <!-- pressione -->
                        <tr>
                            <td width="40%" style="vertical-align:middle;"><h3>Pressione</h3><small id="pressione_value">(no value received)</small></td>
                            <td style="vertical-align:middle;"></td>
                            <td width="30%" style="vertical-align:middle;">
                                <h4>&nbsp;<span id="pressione_bar_label" class="badge badge-secondary">Unknown</span></h4>
                                <h4>&nbsp;<span id="pressione_pressione_label" class="badge badge-secondary">Unknown</span></h4>
                            </td>
                        </tr>
                        <!-- Intensita luminosita -->
                        <tr>
                            <td width="40%" style="vertical-align:middle;"><h3>Intensità luce</h3><small id="luce_label">(no value received)</small></td>
                            <td style="vertical-align:middle;"></td>
                            <td width="30%" style="vertical-align:middle;">
                                <h4><span id="luce_ir_value" class="badge badge-secondary">Unknown</span></h4>
                                <h4><span id="luce_uv_value" class="badge badge-secondary">Unknown</span></h4>
                                <h4><span id="luce_visibile_value" class="badge badge-secondary">Unknown</span></h4>
                        </td>
                        </tr>
                    </table>
              </div>
            </div>
        <div class="panel panel-default">
          <div class="panel-body">
              <div class="row">
                <div class="col-md-6"><b>Latest MQTT message:  </b> <small id="message">no message received</small></div>
                <div class="col-md-6"><b>Status: </b>  <small id='status'></small></div>
              </div>
          </div>
        </div>
      <div class="footer">
        <small><p class="text-center">&copy; <a href="http://affolter-engineering.ch">Affolter Engineering</a> 2013 - 2019</p></small>
    </div>
  </body>
</html>
