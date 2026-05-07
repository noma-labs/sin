<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Document Preview</title>
        <link
            rel="stylesheet"
            href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
        />
        <style>
            body {
                background-color: #f8f9fa;
                padding: 2rem 0;
            }

            .markdown-container {
                background: white;
                border-radius: 0.5rem;
                box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
                padding: 2rem;
            }

            blockquote {
                border-left: 4px solid #0d6efd;
                padding-left: 1rem;
                color: #6c757d;
                font-style: italic;
                margin-left: 0;
                margin-bottom: 1rem;
            }

            hr {
                margin: 2rem 0;
                border-top: 2px solid #dee2e6;
            }

            h1 {
                color: #0d6efd;
                margin-bottom: 1.5rem;
                border-bottom: 2px solid #e9ecef;
                padding-bottom: 0.5rem;
            }

            code {
                background-color: #f8f9fa;
                padding: 0.2rem 0.4rem;
                border-radius: 0.25rem;
                color: #d63384;
            }

            pre {
                background-color: #f8f9fa;
                padding: 1rem;
                border-radius: 0.5rem;
                overflow-x: auto;
            }

            a {
                color: #0d6efd;
            }

            a:hover {
                text-decoration: underline;
            }
        </style>
    </head>
    <body>
        <div class="container-lg">
            <div class="markdown-container">
                <x-markdown>{{ $markdown }}</x-markdown>
            </div>
        </div>
    </body>
</html>
