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
      .etichetta {
        padding: 4cm;
    } 
    </style>
</head>
<body>
    @foreach ($etichette as $etichetta)
        <div class="etichetta">{{$etichetta->collocazione}}</div>
    @endforeach
</body>
</html>
