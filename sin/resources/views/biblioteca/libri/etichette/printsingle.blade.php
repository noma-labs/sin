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
        padding: 1em;
        /* border-style: solid; */
        font-family: verdana;
        text-align: center;
        page-break-after: always;
        page-break-inside: avoid;
        }
    </style>
</head>
<body>
    @foreach ($etichette as $etichetta)
      <div class="etichetta">
      <!-- <img src="{{ asset('/images/logo-noma.png') }}" height="50px"> -->
        <div style="font-size: 100%;">Biblioteca di Nomadelfia</div>
        <span style="display:inline-block; width:5;"></span>
        <div style="font-size: 200%;"><b>{{$etichetta->collocazione}}</b></div>
        <span style="display:inline-block; width:5;"></span>
        <div style="font-size: 200%;">{{$etichetta->titolo}}</div>
      </div>
    @endforeach
</body>
</html>
