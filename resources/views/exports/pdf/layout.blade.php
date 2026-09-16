<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <style>
        @page {
            margin: 70px 25px 55px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
            line-height: 1.4;
        }

        .pdf-header {
            position: fixed;
            top: -50px;
            left: 0;
            right: 0;
        }

        .pdf-footer {
            position: fixed;
            bottom: -35px;
            left: 0;
            right: 0;
            font-size: 8px;
        }

        .page-number::after {
            content: "Page " counter(page) " of " counter(pages);
        }

        .pdf-title {
            margin: 0 0 4px;
            font-size: 16px;
        }

        .pdf-meta {
            margin-bottom: 15px;
            font-size: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: auto;
        }

        th,
        td {
            padding: 5px;
            border: 1px solid #ccc;
            text-align: left;
            vertical-align: top;
            overflow-wrap: break-word;
            word-wrap: break-word;
        }

        thead {
            display: table-header-group;
        }

        tr {
            page-break-inside: avoid;
        }
    </style>
</head>

<body>
    <header class="pdf-header">
        @include('exports.pdf.partials.header')
    </header>

    <footer class="pdf-footer">
        @include('exports.pdf.partials.footer')
    </footer>

    <main>
        @yield('content')
    </main>
</body>
</html>