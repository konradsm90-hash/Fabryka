@extends('layouts.app')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <a href="{{ route('listy-plac.index') }}" class="text-sm text-gray-500 hover:underline">&larr; Powrót do listy</a>
        <h2 class="text-2xl font-bold mt-1">Lista Płac #{{ $lista->id }} ({{ sprintf('%02d', $lista->miesiac) }}/{{ $lista->rok }})</h2>
    </div>
    <div class="text-right">
        <span class="text-sm text-gray-500">Suma Razem:</span>
        <div class="text-2xl font-bold text-green-600">{{ number_format($lista->suma_brutto, 2, ',', ' ') }} zł</div>
    </div>
</div>

<div class="bg-white rounded-lg shadow overflow-x-auto">
    <table class="w-full text-left text-sm border-collapse">
        <thead>
            <tr class="bg-gray-50 border-b text-gray-600 text-xs uppercase">
                <th class="p-3">Pracownik</th>
                <th class="p-3">Stawka</th>
                <th class="p-3 text-center">Godz. Podst.</th>
                <th class="p-3 text-center">Nadgodziny</th>
                <th class="p-3 text-center">Nocne</th>
                <th class="p-3 text-right">Korekta Retro</th>
                <th class="p-3 text-right">Brutto Razem</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @foreach($lista->pozycje as $p)
            <tr class="hover:bg-gray-50">
                <td class="p-3 font-medium">
                    {{ $p->pracownik->imie }} {{ $p->pracownik->nazwisko }}
                    <div class="text-xs text-gray-400 font-mono">{{ $p->pracownik->numer_kart_rcp }}</div>
                </td>
                <td class="p-3">{{ number_format($p->zastosowana_stawka_bazowa, 2, ',', ' ') }} zł/h</td>
                <td class="p-3 text-center">{{ $p->godziny_podstawowe }}h</td>
                <td class="p-3 text-center">
                    <span class="text-blue-600">+{{ $p->godziny_nadgodziny_50 + $p->godziny_nadgodziny_100 }}h</span>
                </td>
                <td class="p-3 text-center">{{ $p->godziny_nocne }}h</td>
                <td class="p-3 text-right {{ $p->kwota_korekty_retro != 0 ? 'font-bold text-amber-600' : 'text-gray-400' }}">
                    {{ number_format($p->kwota_korekty_retro, 2, ',', ' ') }} zł
                </td>
                <td class="p-3 text-right font-bold">{{ number_format($p->kwota_brutto_razem, 2, ',', ' ') }} zł</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection