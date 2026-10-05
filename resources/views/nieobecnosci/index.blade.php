@extends('layouts.app')

@section('content')
<h2 class="text-2xl font-bold mb-6">Zarządzanie Nieobecnościami i L4</h2>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="bg-white p-6 rounded shadow col-span-1">
        <h3 class="text-lg font-bold mb-4">Zgłoś nieobecność</h3>
        <form action="{{ route('nieobecnosci.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium mb-1">Pracownik</label>
                <select name="pracownik_id" class="w-full border p-2 rounded" required>
                    @foreach($pracownicy as $p)
                        <option value="{{ $p->id }}">{{ $p->imie }} {{ $p->nazwisko }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Rodzaj Nieobecności</label>
                <select name="typ" class="w-full border p-2 rounded" required>
                    <option value="URLOP_WYPOCZYNKOWY">Urlop Wypoczynkowy</option>
                    <option value="ZWOLNIENIE_LEKARSKIE">Zwolnienie Lekarskie (L4)</option>
                    <option value="URLOP_NA_ZADANIE">Urlop na Żądanie</option>
                    <option value="OKOLICZNOSCIOWY">Urlop Okolicznościowy</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Data Od</label>
                <input type="date" name="data_od" class="w-full border p-2 rounded" required>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Data Do</label>
                <input type="date" name="data_do" class="w-full border p-2 rounded" required>
            </div>

            <button type="submit" class="w-full bg-emerald-600 text-white py-2 rounded hover:bg-emerald-700 font-medium">Zapisz Nieobecność</button>
        </form>
    </div>

    <div class="bg-white p-6 rounded shadow col-span-2">
        <h3 class="text-lg font-bold mb-4">Ewidencja Wprowadzonych Absencji</h3>
        <table class="w-full text-left text-sm border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b text-gray-600 text-xs uppercase">
                    <th class="p-2">Pracownik</th>
                    <th class="p-2">Typ</th>
                    <th class="p-2">Okres</th>
                    <th class="p-2">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($nieobecnosci as $n)
                <tr class="hover:bg-gray-50">
                    <td class="p-2 font-medium">{{ $n->pracownik->imie }} {{ $n->pracownik->nazwisko }}</td>
                    <td class="p-2 font-semibold text-xs">{{ $n->typ }}</td>
                    <td class="p-2 text-xs font-mono">{{ $n->data_od }} do {{ $n->data_do }}</td>
                    <td class="p-2"><span class="bg-green-100 text-green-800 text-xs font-bold px-2 py-0.5 rounded">{{ $n->status }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection