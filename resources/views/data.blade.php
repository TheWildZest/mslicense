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
                    @if ($row['ChargeType'] == 'Cycle instance prora')
                        @foreach ($row as $cell)
                            <td>{{ is_numeric($cell) ? round((float)$cell, 3) : $cell }}</td>
                        @endforeach
                    @endif
                </tr>
            </tbody>
        @endif
    @endforeach
</table>

<h4>Saját napi díjazás összege: {{ round((float)$totals['CIP_total'], 3) }} &euro; {{ number_format(round($totals['CIP_total'] * $euroExchangeRate, 0), 0, ',', ' ') }} HUF</h4>
<br>

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
                    @if ($row['ChargeType'] != 'Cycle instance prora')
                        @foreach ($row as $cell)
                            <td>{{ is_numeric($cell) ? round((float)$cell, 3) : $cell }}</td>
                        @endforeach
                    @endif
                </tr>
            </tbody>
        @endif
    @endforeach
</table>

<h4>Saját havi díjazás összege: {{ round((float)$totals['CF_total'], 3) }} &euro; {{ number_format(round($totals['CF_total'] * $euroExchangeRate, 0), 0, ',', ' ') }} HUF</h4>
<br>

<div>Microsoft végösszeg: <strong>{{ round((float)$totals['ms_total'], 3) }} &euro; | {{ number_format(round((float)( $totals['ms_total']) * $euroExchangeRate, 0), 0, ',', ' ') }} HUF</strong></div>
<div>Saját végösszeg: <strong>{{ round((float)$totals['own_total'], 3) }} &euro; | {{ number_format(round((float)( $totals['own_total']) * $euroExchangeRate, 0), 0, ',', ' ') }} HUF</strong></div>
<hr>
<div>Profit: <strong>{{ round((float)$totals['own_total'] - $totals['ms_total'], 2) }} &euro;</strong></div>
<div>Profit forintban: <strong>{{ number_format(round((float)($totals['own_total'] - $totals['ms_total']) * $euroExchangeRate, 0), 0, ',', ' ') }} HUF</strong></div>
@endsection
