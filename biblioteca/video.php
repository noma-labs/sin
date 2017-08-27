<!doctype>
<html>
 <head>
  <title>Biblioteca Nomadelfia</title>
<link rel="shortcut icon" href="/img/favicon.ico" type="image/x-icon" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- CSS personalizzato -->
  <link rel="stylesheet" type="text/css" href="/css/mystyle.css">
  <!-- Bootstrap -->
  <link href="/css/bootstrap.css" rel="stylesheet">

  <!-- Jquery -->
  <script src="/js/jquery.js"></script>
  <script src="/js/bootstrap.js"></script>
 </head>
 <body>

<div class="container">
<div class="well text-center text-muted">
  <h1>Ricerca Video</h1>
  <p>Biblioteca di Nomadelfia</p>
</div>
  </div>


<script type="text/javascript">
  function toTop() {
      window.scrollTo(0, 0)
  }
</script>


<script type="text/javascript">
function clearSearch(){
  // clear the  search result
  var myNode=document.getElementById("form-result");
  myNode.parentNode.removeChild(myNode);
}
</script>

<?php
 include ("connection-video.php");
 $con=mysql_connect($server,$user,$pass) or die ("Error di connessione alla db".mysql_error());
 mysql_select_db($db,$con);
 $access=0;
?>

<div class="container">
 <form method="POST" class="form-inline">

   <div class="form-group">
     <label for="xCass" class=" control-label"  >Cass </label>
     <input class="form-control" name="xCass" type="text" id="xCass" placeholder="Ins. Collocazione" >
  </div>

   <div class="form-group">
     <label for="xRecord" class=" control-label" >Record </label>
    <input class="form-control" name="xRecord" type="text" id="xRecord"  placeholder="Ins. Record" >
  </div>

 <div class="form-group">
   <label for="xIni" class="control-label"  >Ini</label>
    <input class="form-control" name="xIni" type="text" id="xIni" size="5" maxlength="10" placeholder="Ins. Minuti Iniziali" />
 </div>

 <div class="form-group">
  <label for="xFin" class="control-label">Fin</label>
  <input class="form-control" name="xFin" type="text" id="xFin" size="5" maxlength="10" placeholder="Ins. Minuti Finali" />
</div>

<div class="form-group">
  <label for="xDataReg" class="control-label">DataReg </label>
  <input class="form-control" name="xDataReg" type="text" id="xDataReg" size="20" maxlength="100" placeholder="Ins. Data Registrazione" />
</div>

<div class="form-group">
  <!-- campo v -->
  <label for="xVisione" class="control-label">Visione </label>
  <input class="form-control" name="xVisione" type="text" id="xVisione" size="20" maxlength="100" placeholder="Ins. Visione" />
</div>

  <!-- campo Q -->
<!-- <div class="form-group">
  <label for="xCritica" class="control-label" >Critica </label>
  <input class="form-control" name="xCritica" type="text" id="xCritica" size="20" maxlength="100" placeholder="Ins. Critica" />
</div> -->

<!-- campo k  -->
<!-- <div class="form-group">
  <label for="xVisionatore" class="control-label">Visionatore </label>
  <input class="form-control" name="xVisionatore" type="text" id="xVisionatore" size="20" maxlength="100" placeholder="Ins. Parola da ricercare nelle note" />
</div> -->


<!-- <div class="form-group">
  <label for="xUTrasm" class="control-label" >UTrasm </label>
  <input class="form-control" name="xUTrasm" type="text" id="xUTrasm" size="20" maxlength="100" placeholder="Ins. Parola da ricercare nelle note" />
</div> -->

<div class="form-group">
  <!-- campo mic  -->
  <label for="xMic" class="control-label">Minuti </label>
  <input class="form-control" name="xMic" type="text" id="xMic" size="5" maxlength="100" placeholder="Ins. Minuti Cassetta" />
</div>

<!-- campo CAT  -->
<!-- <div class="form-group">
  <label for="xCategoria" class="control-label">Categoria </label>
  <input class="form-control" name="xCategoria" type="text" id="xCategoria" size="20" maxlength="100" placeholder="Ins. Parola da ricercare nelle note" />
</div> -->

<div class="form-group">
  <!-- campo MIN  -->
  <label for="xMin" class="control-label">Min </label>
  <input class="form-control" name="xMin" type="text" id="xMin" size="5" maxlength="100" placeholder="Ins. Minuti Video" />
</div>

<div class="form-group">
  <!-- campo Descriz  -->
  <label for="xDescrizione" class="control-label">Descrizione </label>
  <input class="form-control" name="xDescrizione" type="text" id="xDescrizione" size="100" maxlength="200" placeholder="Inserisci Descrizione" />
</div>


<div class="form-group">
  <button class="btn btn-success"   name="biblioteca"  type="submit">Cerca</button>
</div>
</form>
</div>


<?php

if(isset($_POST['biblioteca']))
{
 $record= $_POST['xRecord'];
 $cass= $_POST['xCass'];
 $ini= $_POST['xIni'];
 $fin =  $_POST['xFin'];
 $datareg =  $_POST['xDataReg'];
 $visione=   $_POST['xVisione'];
 // $critica=   $_POST['xCritica'];
 // $visionatore =   $_POST['xVisionatore'];
 // $utrasm =   $_POST['xUTrasm'];
 $cassmin=   $_POST['xMic'];  // minuti casssetta
// Categoria
 $videomin=   $_POST['xMin'];  //video minuti
 // N
 $descrizione=   $_POST['xDescrizione'];

 $control=$record.$cass.$ini.$fin.$datareg.$visione.$cassmin.$videomin.$descrizione; // UTrasm $visionatore $critica

// String printed after the query to show the query terms to the users
$msgSearch = "Ricerca per: ";
if ($record!=''){$msgSearch= $msgSearch."Record=".$record;}
if ($cass!=''){$msgSearch= $msgSearch." Cass=".$cass;}
if ($ini!=''){$msgSearch= $msgSearch." ini=".$ini;}
if ($fin!=''){$msgSearch= $msgSearch." Fin=".$fin;}
if ($datareg!=''){$msgSearch= $msgSearch." DataReg=".$datareg;}
if ($visione!=''){$msgSearch= $msgSearch." Visione=".$visione;}
// if ($critica!=''){$msgSearch= $msgSearch." Critica=".$critica;}
// if ($visionatore!=''){$msgSearch= $msgSearch." Visionatore=".$visionatore;}
// if ($utrasm!=''){$msgSearch= $msgSearch." UTrasm=".$utrasm;}
if ($cassmin!=''){$msgSearch= $msgSearch." Minuti Tot=".$cassmin;}
if ($videomin!=''){$msgSearch= $msgSearch." Minuti Video=".$videomin;}
if ($descrizione!=''){$msgSearch= $msgSearch." Descrizione=".$descrizione;}

$access=1;
}
?>

<?php
  if ($access ==1)
  {
    echo "<div id='form-result'>";  // div contains all the result of the query , used by JS for clear
     if($control==''){
      echo '<div class="container"><div class="alert alert-danger">
            <strong>Inserire Dati!</strong>
            </div></div>';
     }
     else{
     $select = "select * from video ";
     $selectCount = "select COUNT(ID_VIDEO) AS TOTAL from video";
     $where = " where 1=1 ";  // 1=1 is for construction of where clause
     $order = " ORDER BY CASS";
     $count = 0;

      if ($count == 0){
        if($record !=''){$where = $where." and R like rtrim('%$record%')";}
        if($cass !=''){$where = $where." and CASS like rtrim('%$cass%')";}
        if($ini !=''){$where = $where." and INI like rtrim('%$ini%')";}
        if($fin !=''){$where = $where." and FIN like rtrim('%$fin%')";}
        if($datareg !=''){$where = $where." and DATAREG like rtrim('%$datareg%')";}
        if($visione !=''){$where = $where." and V like rtrim('%$visione%')";}
        // if($critica !=''){$where = $where." and Q like rtrim('%$critica%')";}
        // if($visionatore !=''){$where = $where." and K like rtrim('%$visionatore%')";}
        // if($utrasm !=''){$where = $where." and UTRASM like rtrim('%$utrasm%')";}
        if($cassmin !=''){$where = $where." and MIC like rtrim('%$cassmin%')";}
        if($videomin !=''){$where = $where." and MIN like rtrim('%$videomin%')";}
        if($descrizione !=''){$where = $where." and DESCRIZ like rtrim('%$descrizione%')";}
      }

    // Costruzione query
    $queryCount = $selectCount.$where.$order;   // query per contare i risultati
    $query = $select.$where.$order;            // query per ottenere i risultati

    // echo '<div class="container ">
    //           <div class="alert alert-info alert-dismissable fade in">Query: <strong>'.$query.'</strong>
    //           <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    //           </div>
    //       </div>';
    $result = mysql_query($queryCount);//"select count(ID_BIBLIO) as TOTAL  from biblio where TITOLO like rtrim('%$record%')");

    if($result>0){
         $r = mysql_fetch_assoc($result);
         $count = $r['TOTAL'];
         $registros = mysql_query($query);
    }

    echo '<div class="container">
            <div class="alert alert-info alert-dismissable fade in"><strong> '.$msgSearch.'</strong>
              <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            </div>
          </div>';

    // lista di risultati
    if ($registro = mysql_fetch_array($registros)){
          echo '<div class="container">
              <div class="alert alert-success"> LIBRI TROVATI: <strong> '.$count.'</strong></div>
              </div>';


        //  echo "<div class='container'>";
          echo "<table class='table  table-striped table-bordered '>";
          echo "<thead class='thead-inverse'>";
          echo "<tr>
                  <th>CASS.</th>
                  <th>Record</th>
                   <th>INI</th>
                   <th>Fin</th>
                   <th>DataReg</th>
                   <th>Visione</th>
                   <!-- <th>Critica</th> -->
                   <!--<th>Visionatore</th>-->
                   <!--<th>UTrasm</th>-->
                   <th>Minuti</th>
                   <th>Descrizione</th>
                </tr>";
          echo"</thead>";
          echo "<tbody>";
          do {
            echo "<tr>
              <td>".$registro['CASS']."</td>
              <td>".utf8_encode($registro['R'])."</td>
              <td>".$registro['INI']."</td>
              <td>".$registro['FIN']."</td>
              <td>".$registro['DATAREG']."</td>
              <td>".$registro['V']."</td>
              <!--<td>".$registro['Q']."</td>-->
              <!--<td>".$registro['K']."</td>-->
              <!--<td>".$registro['UTRASM']."</td>-->
              <td>".$registro['MIC']."</td>
              <td>".$registro['DESCRIZ']."</td>
              </tr> \n";
          } while ($registro = mysql_fetch_array($registros));

          echo " </tbody>
               </table>";
          // echo "</div>"; // div container table

          echo '<font face="Calibri" color="navy" ><h3><P ALIGN=CENTER>FINE RICERCA </P></h3></font>' ;

          echo '<center>
                <button class="btn btn-success"  name="inizio"  onClick=toTop()>INIZIO</button>
                <button class="btn btn-success"  name="inizio"  onClick=clearSearch()>Nuova Ricerca</button>
                </center>' ;
          // echo '<br>';

       } else{

         echo '<div class="container"><div class="alert alert-danger">
               <strong>Nessun risultato ottenuto</strong>
               </div></div>';
       } // end if registros
     }  // end else Control
     echo "</div>";  // end div
  } // end if access
?>

 </body>
</html>
