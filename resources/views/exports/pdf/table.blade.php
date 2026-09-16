@extends('exports.pdf.layout')

@section('content')
    <h1 class="pdf-title">
        {{ $title }}
    </h1>

    @if (!empty($generatedAt))
        <div class="pdf-meta">
            Generated: {{ $generatedAt }}
        </div>
    @endif

    <table>
        <thead>
            <tr>
                @foreach ($headings as $heading)
                    <th>{{ $heading }}</th>
                @endforeach
            </tr>
        </thead>

        <tbody>
            @foreach ($rows as $row)
                <tr>
                    @foreach ($row as $cell)
                        <td>{{ $cell }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection