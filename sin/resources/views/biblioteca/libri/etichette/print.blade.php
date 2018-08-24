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
      body {
      font-family: sans-serif;
      margin-left: 0.4cm;
      margin-top: 0.9cm;
     }
     @page { margin: 0px; }

      #table {
        border-spacing: 0.2em 0em;
        font-size: 20px;
        /* table-layout: fixed; */
      }

      #table td {
        /* border: 1px solid black; */
        /* border: 0.2cm solid black; */
        /* border-radius: 0.4em; */
        width: 3.6cm;
        height: 1.4cm;
        text-align: center;
        /* padding: 1em;
        width:150px;
        height:18px;
        text-align: center; */
      }
    </style>
</head><body>
    <table id="table">
      <tbody>
        @foreach ($etichette->chunk(5) as $chunk)
            <tr>
              @foreach ($chunk as $product)
                  <td >{{$product->collocazione}}</td>
              @endforeach
            </tr>
       @endforeach
    </tbody>
    </table>
  </body></html>
