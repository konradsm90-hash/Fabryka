@extends('layouts.app')

@section('content')
<h2 class="text-2xl font-bold mb-6">Grafik i Wykrywanie Naruszeń</h2>

<div class="bg-white p-4 rounded shadow mb-6">
    <form method="GET" action="{{ route('grafik.index') }}" class="flex flex-wrap items-end gap-4">
        <div>
            <label for="pracownik_id" class="font-medium block text-sm mb-1">Wybierz pracownika:</label>
            <select name="pracownik_id" id="pracownik_id" class="border p-2 rounded w-80">
                @foreach($pracownicy as $p)
                    <option value="{{ $p->id }}" {{ ($wybranyPracownik?->id ?? null) == $p->id ? 'selected' : '' }}>
                        {{ $p->imie }} {{ $p->nazwisko }} ({{ $p->numer_kart_rcp }})
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="rok" class="font-medium block text-sm mb-1">Rok:</label>
            <input type="number" name="rok" id="rok" value="{{ request('rok', $rok ?? 2025) }}" min="2020" max="2030" class="border p-2 rounded w-28">
        </div>

        <div>
            <label for="miesiac" class="font-medium block text-sm mb-1">Miesiąc:</label>
            <select name="miesiac" id="miesiac" class="border p-2 rounded w-28">
                @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ request('miesiac', $miesiac ?? 1) == $m ? 'selected' : '' }}>
                        {{ str_pad($m, 2, '0', STR_PAD_LEFT) }}
                    </option>
                @endfor
            </select>
        </div>

        <div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 font-medium">
                Analizuj okres
            </button>
        </div>
    </form>
</div>

@if($wybranyPracownik && $analiza)
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="bg-white p-6 rounded shadow">
        <h3 class="text-gray-500 text-sm font-medium">Suma spóźnień</h3>
        <p class="text-3xl font-bold text-red-600 mt-2">{{ $analiza['spoznienia_minuty'] }} min</p>
    </div>

    <div class="bg-white p-6 rounded shadow col-span-2">
        <h3 class="text-lg font-bold mb-4">Wykryte Naruszenia i Błędy</h3>
        @if(count($analiza['anomalie']) > 0)
            <ul class="divide-y divide-gray-200">
                @foreach($analiza['anomalie'] as $a)
                    <li class="py-3 flex justify-between items-center">
                        <div>
                            <span class="font-bold text-sm bg-red-100 text-red-800 px-2 py-0.5 rounded">{{ $a['typ'] }}</span>
                            <span class="text-gray-700 ml-2">{{ $a['opis'] }}</span>
                        </div>
                        <span class="text-sm text-gray-400 font-mono">{{ $a['data'] }}</span>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="text-green-600 font-medium">Brak wykazanych błędów i spóźnień w danym okresie.</p>
        @endif
    </div>
</div>
@endif
@endsection