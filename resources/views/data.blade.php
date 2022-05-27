@extends('layouts.app')

@section('content')
<table class="table table-hover">
    @foreach ($data as $row)
        @if ($loop->first)
            <thead>
                <tr>
                    @foreach ($row as $columnName)
                        <th>{{ $columnName }}</th>
                    @endforeach
                </tr>
            </thead>
        @else

            <tbody>
                <tr>
                    @foreach ($row as $cell)
                        <td>{{ is_numeric($cell) ? round((float)$cell, 3) : $cell }}</td>
                    @endforeach
                </tr>
            </tbody>

        @endif
    @endforeach
</table>

<br>

<div>Microsoft végösszeg: <strong>{{ round((float)$totals['ms_total'], 3) }} &euro;</strong></div>
<div>Saját végösszeg: <strong>{{ round((float)$totals['own_total'], 3) }} &euro;</strong></div>
<hr>
<div>Profit: <strong>{{ round((float)$totals['own_total'] - $totals['ms_total'], 2) }} &euro;</strong></div>
<div>Profit forintban: <strong>{{ round((float)($totals['own_total'] - $totals['ms_total']) * $euroExchangeRate, 0) }} HUF</strong></div>
@endsection
