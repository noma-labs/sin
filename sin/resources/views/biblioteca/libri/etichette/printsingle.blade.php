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
        width:"6cm";
        height:"3cm";
        }
    </style>
</head>
<body>
    @foreach ($etichette as $etichetta)
      <div class="etichetta">
      <!-- <img src="{{ asset('/images/logo-noma.png') }}" height="50px"> -->
        <div style="font-size: 100%;">Biblioteca di Nomadelfia</div>
        <span style="display:inline-block; width:3;"></span>
        <div style="font-size: 15pt;"><b>{{$etichetta->collocazione}}</b></div>
        <span style="display:inline-block; width:3;"></span>
        <div style="font-size: 200%;">{{$etichetta->titolo}}</div>
        <span style="display:inline-block; width:20;"></span>
        <img src="data:image/png;base64,{{DNS1D::getBarcodePNG($etichetta->id, 'C39')}}" alt="barcode" />
      </div>
    @endforeach
</body>
</html>
