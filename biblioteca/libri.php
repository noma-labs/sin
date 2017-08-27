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
  <h1>Ricerca Libri</h1>
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
 include ("connection-libri.php");
 $con=mysql_connect($server,$user,$pass) or die ("Error di connessione alla db".mysql_error());
 mysql_select_db($db,$con);
 $access=0;
?>

<div class="container">
 <form method="POST" class="form-inline">

   <div class="form-group">
     <label for="xCollocazione" class=" control-label"  >Collocazione </label>
     <input class="form-control" name="xCollocazione" type="text" id="xCollocazione" placeholder="Ins. Collocazione" >
  </div>

   <div class="form-group">
     <label for="xTitolo" class=" control-label" >Titolo </label>
    <input class="form-control" name="xTitolo" type="text" id="xTitolo"  placeholder="Inserisci Titolo Libro" >
  </div>



 <div class="form-group">
   <label for="xAutore" class="control-label" >Autore</label>
    <input class="form-control" name="xAutore" type="text" id="xAutore" size="10" maxlength="10" placeholder="Ins. Autore" />
 </div>

 <div class="form-group">
  <label for="xEditore" class="control-label">Editore</label>
  <input class="form-control" name="xEditore" type="text" id="xEditore" size="10" maxlength="10" placeholder="Ins. Editore" />
</div>

<div class="form-group">
  <label for="xClassificazione"  class="control-label" >Classificazione </label>
       <select class="form-control"   name="xClassificazione" type="text" id="xClassificazione">
         <option value=""></option>
          <?php
           $sql = mysql_query("SELECT descrizione FROM classificazione ORDER BY descrizione");
             while ($row = mysql_fetch_array($sql)){
                 echo "<option value='".$row["descrizione"]."'>".$row["descrizione"]."</option>";
             }
            ?>
       </select>
 </div>

 <div class="form-group">
   <label for="xNote" class="control-label"  >Note </label>
   <input class="form-control" name="xNote" type="text" id="xNote" size="20" maxlength="100" placeholder="Ins. Parola da ricercare nelle note" />
 </div>

<div class="form-group">
  <!-- <input class="col-sm-2 control-label btn btn-default"  name="biblioteca" type="submit" value="CERCA" > -->
  <button class="btn btn-success"   name="biblioteca"  type="submit">Cerca</button>
</div>
</form>
</div>


<?php

if(isset($_POST['biblioteca']))
{
 $titolo= $_POST['xTitolo'];
 $collocazione= $_POST['xCollocazione'];
 $autore= $_POST['xAutore'];
 $editore =  $_POST['xEditore'];
 $classificazione = $_POST['xClassificazione'];
 $note =  $_POST['xNote'];
;
 $control=$titolo.$collocazione.$autore.$editore.$note.$classificazione;

// String printed after the query to show the query terms to the users
$msgSearch = "Ricerca per: ";
if ($titolo!=''){$msgSearch= $msgSearch."Titolo=".$titolo;}
if ($collocazione!=''){$msgSearch= $msgSearch." Collocazione=".$collocazione;}
if ($autore!=''){$msgSearch= $msgSearch." Autore=".$autore;}
if ($editore!=''){$msgSearch= $msgSearch." Editore=".$editore;}
if ($classificazione!=''){$msgSearch= $msgSearch." Classificazione=".$classificazione;}
if ($note!=''){$msgSearch= $msgSearch." Note=".$note;}
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
     $select = "select * from biblio, classificazione ";
     $selectCount = "select COUNT(ID_BIBLIO) AS TOTAL from biblio, classificazione";
     $where = " where classificazione=ID_CLASSE  ";
     $order = " ORDER BY titolo";
     $count = 0;

      if ($count == 0){
        if($titolo !=''){$where = $where." and TITOLO like rtrim('%$titolo%')";}
        if($collocazione !=''){$where = $where." and COLLOCAZIONE like rtrim('%$collocazione%')";}
        if($autore !=''){$where = $where." and AUTORE like rtrim('%$autore%')";}
        if($editore !=''){$where = $where." and EDITORE like rtrim('%$editore%')";}
        if($classificazione !=''){$where = $where." and DESCRIZIONE like rtrim('%$classificazione%')";}

        if($note !=''){$where = $where." and NOTE like rtrim('%$note%')";}
      }

    // Costruzione query
    $queryCount = $selectCount.$where.$order;   // query per contare i risultati
    $query = $select.$where.$order;            // query per ottenere i risultati

    // echo '<div class="container ">
    //           <div class="alert alert-info alert-dismissable fade in">Query: <strong>'.$query.'</strong>
    //           <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    //           </div>
    //       </div>';
    $result = mysql_query($queryCount);//"select count(ID_BIBLIO) as TOTAL  from biblio where TITOLO like rtrim('%$titolo%')");
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

          //echo "<div class='container'>";
          echo "<table class='table table-sm  table-striped table-bordered'>"; // table-sm   'table-responsive
          echo "<thead class='thead-inverse'>";
          echo "<tr>
                  <th>COLLOC.</th>
                  <th>TITOLO</th>
                   <th>AUTORE</th>
                   <th>EDITORE</th>
                   <th>CLASSIFICAZIONE</th>
                   <th>NOTE</th>
                </tr>";
          echo"</thead>";
          echo "<tbody>";
          do {
            echo "<tr>
              <td  width=\"10\">".$registro['COLLOCAZIONE']."</td>
              <td  width=\"90\">".utf8_encode($registro['TITOLO'])."</td>
              <td width=\"20\" >".$registro['AUTORE']."</td>
              <td width=\"20\" >".$registro['EDITORE']."</td>
              <td width=\"80\" >".$registro['DESCRIZIONE']."</td>
              <td width=\"50\" >".$registro['NOTE']."</td>
              </tr> \n";
          } while ($registro = mysql_fetch_array($registros));

          echo " </tbody>
               </table>";
          // echo "</div>"; // end div container table

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
