<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <style>
        @page {
            margin: 70px 20px 55px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: DejaVu Sans, sans-serif;
            font-size: 8px;
            line-height: 1.35;
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
            line-height: 1.2;
        }

        .pdf-meta {
            margin-bottom: 15px;
            font-size: 8px;
        }

        table {
            width: 100%;
            max-width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        th,
        td {
            padding: 4px;
            border: 1px solid #ccc;
            text-align: left;
            vertical-align: top;

            white-space: normal;
            overflow-wrap: anywhere;
            word-wrap: break-word;
            word-break: break-word;
        }

        th {
            font-size: 7.5px;
            font-weight: 700;
        }

        thead {
            display: table-header-group;
        }

        tbody {
            display: table-row-group;
        }

        tr {
            page-break-inside: avoid;
        }

        img {
            max-width: 100%;
            height: auto;
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
