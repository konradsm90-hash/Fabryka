@extends('layouts.app')

@section('content')
<h2 class="text-2xl font-bold mb-6">Korekty i Ręczne Dopisywanie Odbić</h2>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="bg-white p-6 rounded shadow col-span-1">
        <h3 class="text-lg font-bold mb-4">Dodaj / Popraw odbicie</h3>
        <form action="{{ route('korekty.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium mb-1">Pracownik</label>
                <select name="pracownik_id" class="w-full border p-2 rounded" required>
                    @foreach($pracownicy as $p)
                        <option value="{{ $p->id }}">{{ $p->imie }} {{ $p->nazwisko }} ({{ $p->numer_kart_rcp }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Typ Zdarzenia</label>
                <select name="typ" class="w-full border p-2 rounded" required>
                    <option value="WEJSCIE">WEJŚCIE</option>
                    <option value="WYJSCIE">WYJŚCIE</option>
                    <option value="PRZERWA_START">START PRZERWY</option>
                    <option value="PRZERWA_STOP">KONIEC PRZERWY</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Data i Godzina</label>
                <input type="datetime-local" name="czas_odbicia" class="w-full border p-2 rounded" required>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Powód korekty (Ślad Audytowy)</label>
                <textarea name="powod" placeholder="np. Zapomniana karta, awaria czytnika" class="w-full border p-2 rounded h-20" required></textarea>
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 font-medium">Zapisz Korektę</button>
        </form>
    </div>

    <div class="bg-white p-6 rounded shadow col-span-2">
        <h3 class="text-lg font-bold mb-4">Ostatnie Odbicia i Korekty w Systemie</h3>
        <table class="w-full text-left text-sm border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b text-gray-600 text-xs uppercase">
                    <th class="p-2">Data i Czas</th>
                    <th class="p-2">Pracownik</th>
                    <th class="p-2">Typ</th>
                    <th class="p-2">Źródło</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($ostatnieOdbicia as $o)
                <tr class="hover:bg-gray-50">
                    <td class="p-2 font-mono">{{ $o->czas_odbicia }}</td>
                    <td class="p-2">{{ $o->pracownik->imie }} {{ $o->pracownik->nazwisko }}</td>
                    <td class="p-2 font-semibold text-xs">
                        <span class="px-2 py-0.5 rounded {{ str_contains($o->typ, 'WEJSCIE') ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $o->typ }}
                        </span>
                    </td>
                    <td class="p-2 text-xs text-gray-500">{{ $o->zrodlo }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection