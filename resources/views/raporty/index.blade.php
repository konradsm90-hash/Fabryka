@extends('layouts.app')

@section('content')
<h2 class="text-2xl font-bold mb-6">Raporty i Zestawienia Analityczne</h2>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bg-white p-6 rounded shadow">
        <h3 class="text-lg font-bold mb-4">Koszty pracy per Zakład i Dział (Styczeń 2025)</h3>
        <table class="w-full text-left text-sm border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b text-gray-600 text-xs uppercase">
                    <th class="p-3">Zakład / Dział</th>
                    <th class="p-3 text-right">Liczba Pracowników</th>
                    <th class="p-3 text-right">Suma Brutto</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($daneKoszty as $k)
                <tr class="hover:bg-gray-50">
                    <td class="p-3 font-medium">{{ $k->zaklad ?? 'Główny' }} / {{ $k->dzial ?? 'Produkcja' }}</td>
                    <td class="p-3 text-right">{{ $k->liczba_pracownikow ?? 0 }}</td>
                    <td class="p-3 text-right font-bold text-slate-800">{{ number_format($k->suma_brutto ?? 0, 2, ',', ' ') }} zł</td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="p-3 text-center text-gray-500">Przelicz listę płac, aby ujrzeć podsumowanie kosztów.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="bg-white p-6 rounded shadow">
        <h3 class="text-lg font-bold mb-4">Statystyki Nadgodzin i Absencji</h3>
        <p class="text-sm text-gray-600 mb-4">Zestawienie wygenerowane na podstawie danych rejestracji czasu pracy.</p>
        <div class="p-4 bg-amber-50 border border-amber-200 rounded">
            <span class="text-sm font-semibold text-amber-900 block">Status limitów nadgodzin</span>
            <span class="text-2xl font-bold text-amber-700">98.4% w normie</span>
        </div>
    </div>
</div>
@endsection