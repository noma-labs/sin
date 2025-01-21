<!DOCTYPE html>
<!-- dompPDF got Error "No block-level parent found.  Not good." ù
   look at the issue: https://github.com/dompdf/dompdf/issues/1494
   Solution: no space after html, head. body.
  -->
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Page Title</title>
        <style>
            .my-body  {
              margin: 0;
              padding: 0;
            }
            .etichetta {
              font-family: verdana;
              text-align: center;
              page-break-after: always;
              page-break-inside: avoid;
              width:"400px";
              height:"600px";
              }
            .my-table  {
              border: 2px solid
            }
            .my-table td{
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
              padding:5px 5px;
              border-style:solid;
              border-width:1px;
              border-color:black;
            }
            .my-table .my-table-collocazione{
              border-color:inherit;
              font-size:{{ config("etichette.collocazione.font-size") }};
              text-align:center;
              vertical-align:top;
              max-height:100px
              }
            .my-table .my-table-titolo{
              border-color:inherit;
              font-size:{{ config("etichette.titolo.font-size") }};
              text-align:center;
              vertical-align:middle;
              word-break:break-word;
            }
        </style>
    </head>
    <body class="my-body">
        @foreach ($etichette as $etichetta)
            <div class="etichetta">
                <table class="my-table">
                    <tr>
                        <th class="my-table-header">
                            <img
                                class="my-table-logo"
                                src="{{ asset("/images/logo-noma.png") }}"
                            />
                        </th>
                    </tr>
                    <tr>
                        <td class="my-table-collocazione">
                            {{ $etichetta->foglio }}
                        </td>
                    </tr>
                    <tr>
                        <td
                            class="my-table-titolo"
                            height="{{ config("etichette.titolo.height") }}"
                        >
                            {{ $etichetta->titolo }}
                        </td>
                    </tr>
                </table>
            </div>
        @endforeach
    </body>
</html>
