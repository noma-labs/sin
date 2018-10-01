<!DOCTYPE html>
<!-- dompPDF got Error "No block-level parent found.  Not good." ù
   look at the issue: https://github.com/dompdf/dompdf/issues/1494
   Solution: no space after html, head. body.
  -->
<html><head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Page Title</title>
    <style>
    .my-body  {
      margin: 0;
      padding: 0;
    }
      .etichetta {
        /* padding-top: 1em; */
        /* border-style: solid; */
        font-family: verdana;
        text-align: center;
        page-break-after: always;
        page-break-inside: avoid;
        width:"400px";
        height:"600px";
        }
        .my-table  {
          /* border-collapse:collapse;  */
          /* border-spacing:0;  */
          border: 2px solid
        }
.my-table td{font-family:Arial, sans-serif;
    font-size:18px;
    padding:5px 5px;
    border-style:solid;
    border-width:1px;
    overflow:hidden;
    word-break:break-word;
    border-color:black;
    }
    .my-table .my-table-logo{
   padding:3
   border-color:black;
   max-width: 100%
}
.my-table .my-table-header{
  font-family:Arial, 
  sans-serif;
  font-size:24px;
  font-weight:normal;
  padding:5px 5px;
  border-style:solid;
  border-width:1px;
  overflow:hidden;
  word-break:break-word;
  border-color:black;
  }
.my-table .my-table-collocazione{
  border-color:inherit;
  font-size:38px;
  text-align:center;
  vertical-align:top
  }
.my-table .my-table-titolo{
  border-color:inherit;
  font-size:24px;
  text-align:center;
  vertical-align:middle;
  word-break:break-word;
}
    </style>
</head>
<body class="my-body">
    @foreach ($etichette as $etichetta)
      <!-- <div class="etichetta">
      <!- - <img src="{{ asset('/images/logo-noma.png') }}" height="50px"> - ->
      <span style="display:inline-block; width:2;"></span>
        <div style="font-size: 100%;"></div>
        <span style="display:inline-block; width:3;"></span>
        <div style="font-size: 15pt;"><b>/b></div>
        <span style="display:inline-block; width:3;"></span>
        <div style="font-size: 200%;">{{$etichetta->titolo}}</div>
        <!- - <span style="display:inline-block; width:20;"></span> -->
        <!-- <img src="data:image/png;base64,{{DNS1D::getBarcodePNG($etichetta->id, 'C39')}}" alt="barcode" /> - ->
      </div> -->
      <div class="etichetta">
      <table class="my-table">
        <tr>
          <!-- <th class="my-table-header">Biblioteca di Nomadelfia</th> -->
          <th  class="my-table-header"> <img class="my-table-logo" src="{{ asset('/images/logo-bilblioteca.png') }}"> </th>
          
        </tr>
        <tr>
          <td class="my-table-collocazione">{{$etichetta->collocazione}}</td>
        </tr>
        <tr>
          <td class="my-table-titolo" height=290 >{{$etichetta->titolo}}</td>
        </tr>
      </table>

      </div>
    @endforeach

</body>
</html>
