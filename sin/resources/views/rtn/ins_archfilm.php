<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
 <head>
<meta charset="utf-8" />
<title>INSERIMIENTO ARCHIVIO FILM</title>

<link rel="stylesheet" href="calendar/jquery-ui.css" />
<script src="calendar/jquery-1.9.1.js"></script>
<script src="calendar/jquery-ui.js"></script>

<script>
$(function () {
$("#datar").datepicker();
});
</script>


<script>
 $.datepicker.regional['es'] = {
 closeText: 'Cerrar',
 prevText: '< Ant',
 nextText: 'Sig >',
 currentText: 'Oggi',
 monthNames: ['Gennaio', 'Febbraio', 'Marzo', 'Aprile', 'Maggio', 'Giugno', 'Luglio', 'Agosto', 'Settembre', 'Ottobre', 'Novembre', 'Dicembre'],
 monthNamesShort: ['Gen','Feb','Mar','Apr', 'Mag','Giu','Lug','Ago','Set', 'Ott','Nov','Dic'],
 dayNames: ['Domenica', 'Lunedi', 'Martedi', 'Mercoledi', 'Giovedi', 'Venerdi', 'Sabato'],
 dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mer', 'Gio', 'Ven', 'Sab'],
 dayNamesMin: ['Do','Lu','Ma','Me','Gi','Ve','Sa'],
 weekHeader: 'Sm',
 dateFormat: 'yy-mm-dd',
 firstDay: 1,
 isRTL: false,
 showMonthAfterYear: false,
 yearSuffix: ''
 };
 $.datepicker.setDefaults($.datepicker.regional['es']);
$(function () {
$("datau").datepicker();
});
</script>


<script>
$(function () {
$("#datau").datepicker();
});
</script>


<script>
 $.datepicker.regional['es'] = {
 closeText: 'Cerrar',
 prevText: '< Ant',
 nextText: 'Sig >',
 currentText: 'Oggi',
 monthNames: ['Gennaio', 'Febbraio', 'Marzo', 'Aprile', 'Maggio', 'Giugno', 'Luglio', 'Agosto', 'Settembre', 'Ottobre', 'Novembre', 'Dicembre'],
 monthNamesShort: ['Gen','Feb','Mar','Apr', 'Mag','Giu','Lug','Ago','Set', 'Ott','Nov','Dic'],
 dayNames: ['Domenica', 'Lunedi', 'Martedi', 'Mercoledi', 'Giovedi', 'Venerdi', 'Sabato'],
 dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mer', 'Gio', 'Ven', 'Sab'],
 dayNamesMin: ['Do','Lu','Ma','Me','Gi','Ve','Sa'],
 weekHeader: 'Sm',
 dateFormat: 'yy-mm-dd',
 firstDay: 1,
 isRTL: false,
 showMonthAfterYear: false,
 yearSuffix: ''
 };
 $.datepicker.setDefaults($.datepicker.regional['es']);
$(function () {
$("datau").datepicker();
});
</script>

</head>
  <body>

<body background="">

 <center>
  <div id="accordion">
    <font color = "navy" face="Calibri"><h1>INSERIMENTO TV RTN (FILM) </h1></font><br>
  <div>
 </center>
 
<center><TABLE BORDER CELLPADDING=5 CELLSPACING=0 bordercolor=gray></center>
<tr><td width=\"110\"  bgcolor=white><font size=2 color=\"black\" face=\"Courier New\"></font>
<strong><form action="ins_archfilm_bd.php" method="POST"  NAME="form">
<?php
 include ("conexion.php");
 $con=mysql_connect($server,$user,$pass) or die ("Error di connessione alla db".mysql_error());
 mysql_select_db($db,$con);
?>



<font face="Calibri" color="navy">CATEGORIA / NUMERO : </font>
     <select class="select" name="xSerie" type="text" id="xSerie">

        <?php
          $query = mysql_query("SELECT Series ,max(numeros)+1 as numeros FROM trasmissioni_tv where series='FA' group by Series UNION SELECT Series ,max(numeros)+1 as numeros FROM trasmissioni_tv where series ='NO' group by Series UNION SELECT Series ,max(numeros)+1 as numeros FROM trasmissioni_tv where series ='AT' group by Series UNION SELECT Series ,max(numeros)+1 as numeros FROM trasmissioni_tv where series ='CI' group by Series UNION SELECT Series ,max(numeros)+1 as numeros FROM trasmissioni_tv where series ='AM' group by Series UNION SELECT Series ,max(numeros)+1 as numeros FROM trasmissioni_tv where series ='AN' group by Series UNION SELECT Series ,max(numeros)+1 as numeros FROM trasmissioni_tv where series ='MU' group by Series UNION SELECT Series ,max(numeros)+1 as numeros FROM trasmissioni_tv where series ='SP' group by Series
");
          while ($valores = mysql_fetch_array($query)) {
            echo '<option value="'.$valores[Series].$valores[numeros].'">'.$valores[Series].' -- '.$valores[numeros].'</option>';
          }
          $valores=$_POST ['Series'];
          echo $_POST ['numeros'];
        ?>
     </select>
   

<font face="Calibri" color="navy">RECORD :</font>
  <select class="select" type="number" name="xRecord" id="xRecord">
    <option selected value="1" >1</option>
    <option value="2">2</option>
    <option value="3">3</option>
    <option value="4">4</option>
    <option value="5">5</option>
    <option value="6">6</option>
    <option value="7">7</option>
    <option value="8">8</option>
    <option value="9">9</option>
    <option value="10">10</option>
    <option value="11">11</option>
    <option value="12">12</option>
    <option value="13">13</option>
    <option value="14">14</option>
    <option value="15">15</option>
   </select> <br/><br/>
	  
<font face="Calibri" color="navy">INIZIO MINUTI :&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font>
<input type "number" placeholder="0" size="5" maxlength="5" id="xMinini" value="0" name="xMinini"/>

<font face="Calibri" color="navy">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;FIN MINUTI :</font>
<input type "number"  placeholder="0" size="5" maxlength="5" id="xMinfin" value="0"  name="xMinfin"/><br/><br/>

<font face="Calibri" color="navy">DATA REGISTRAZIONE&nbsp;&nbsp;&nbsp;:</font>
<input type="text" size="8" maxlength="8" name="datar" id="datar">

<font face="Calibri" color="navy">DATA ULTIMA TRASMISSIONE :</font>
<input type="text" size="8" maxlength="8" name="datau" id="datau"> <br/><br/>

<font face="Calibri" color="navy">VISIONE :</font>

   <select class="select" name="xVisione" type="text" id="xVisione">
    <option selected value="VISIONE">VISIONE</option>
    <option value="Tutti">Tutti</option>
    <option value="14Anni">14Anni</option>
    <option value="12Anni">12Anni</option>
    <option value="Superiori">Superiori</option>
    <option value="Medie">Medie</option>
    <option value="Elementari">Elementari</option>
    <option value="NonVisionato">NonVisionato</option>
    <option value="NonVisibile">NonVisibile</option>
    </select>
    
<font face="Calibri" color="navy">CRITICA :</font>
	     <select class="select" name="xCritica" type="text" id="xCritica">
    <option selected value="CRITICA">CRITICA</option>
    <option value="10">10</option>
    <option value="9">9</option>
    <option value="8">8</option>
    <option value="7">7</option>
    <option value="6">6</option>
   </select>

<font face="Calibri" color="navy">SUPPORTO :</font>
<select class="select" name="xSup" type="text" id="xSup">
   <option selected value="SUPPORTO">SUPPORTO</option>
   <option value="VHS">VHS</option>
   <option value="DVD">DVD</option>
   <option value="SD">SD</option>
   <option value="HD">HD</option>
   <option value="FILE">FILE</option>
</select>




<font face="Calibri" color="navy">CATEGORIA :</font>
   <select class="select" name="xCat" type="text" id="xCat">
    <option selected value="CATEGORIA">CATEGORIA</option>
<option value="FiReligiosi">FiReligiosi</option>
<option value="FiComico">FiComico</option>
<option value="FiCommedia">FiCommedia</option>
<option value="FiStorico">FiStorico</option>
<option value="FiAzione">FiAzione</option>
<option value="FiNonClass">FiNonClass</option>
<option value="FiAvventura">FiAvventura</option>
<option value="FiWestern">FiWestern</option>
<option value="FiDrammatico">FiDrammatico</option>
<option value="FiFantascienza">FiFantascienza</option>
<option value="FiMusical">FiMusical</option>
<option value="FiThriller">FiThriller</option>
<option value="FiSportivo">FiSportivo</option>
<option value="FiRomantico">FiRomantico</option>
<option value="FiPoliziesco">FiPoliziesco</option>
<option value="FiAnimazione">FiAnimazione</option>
<option value="FiDocumentario">FiDocumentario</option>
<option value="CiAC">CiAC</option>
<option value="CiRisorgimento">CiRisorgimento</option>
<option value="CiContemporanea">CiContemporane</option>
<option value="CiMedioevoRinascimento">CiMedioevoRinascimento</option>
<option value="CiGenerale">CiGenerale</option>
<option value="CiReligione">CiReligione</option>
<option value="AmNatura">AmNatura</option>
<option value="AmEcologia">AmEcologia</option>
<option value="AnAnimali">AnAnimali</option>
<option value="AmAstronomia">AmAstronomia</option>
<option value="MuLeggera">MuLeggera</option>
<option value="MuClassica">MuClassica</option>
<option value="SpComici">SpComici</option>
   </select>
   
<font face="Calibri" color="navy">VISIONATORE :</font>
<select class="select" name="xVisionatore" type="text" id="xVisionatore">
    <option selected value="VISIONATORE">VISIONATORE</option>
    <option value="11">Nico</option>
    <option value="13">Maria C.</option>
    <option value="1">Elena F.</option>
    <option value="23">Pietro L.</option>
    <option value="3">Carlo M.</option>
    <option value="15">Tommaso</option>
    <option value="21">Zeno S.</option>
    <option value="5">Roberto N.</option>
    <option value="24">Nazzarena R.</option>
    <option value="17">Antonietta</option>
    <option value="20">Altri</option>
   </select>
    <br/><br/>
    
<font face="Calibri" color="navy">DESCRIZIONE FILM:</font>
<input size="100" maxlength="1200" type="text" id="xDescriz" name="xDescriz"/> <br/><br/>

<font face="Calibri" color="navy">NOTE :&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font>
<input size="100" maxlength="1200" type="text" id="xNote" name="xNote"/> <br/><br/>


<center><input type="submit" value="INSERIRE FILM"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<a href="menurtn.php"><input href="menurtn.php" type="button" value="TORNA AL MENU"/></a></center><br>


</form></strong>

</td>
   </tr>

<?php
 include ("conexion.php");
 $con=mysql_connect($server,$user,$pass) or die ("Error di connessione alla db".mysql_error());
 mysql_select_db($db,$con);
?>


<?php

?>


</body>
</html>

