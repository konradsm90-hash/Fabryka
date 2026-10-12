<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Panel Menedżera - Przesłane CV</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-900 min-h-screen">
    <nav class="bg-slate-900 text-white p-4 shadow flex justify-between items-center">
        <h1 class="text-lg font-bold">Panel Menedżera / Rekrutacja</h1>
        <div class="flex items-center gap-4">
            <a href="{{ route('kanban.index') }}" class="text-sm bg-blue-600 px-3 py-1.5 rounded hover:bg-blue-700">Tablica Kanban</a>
            <form action="{{ route('menadzer.wyloguj') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="text-sm bg-red-600 px-3 py-1.5 rounded hover:bg-red-700">Wyloguj</button>
            </form>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto p-6">
        <h2 class="text-2xl font-bold mb-6">Lista kandydatów</h2>

        <div class="bg-white shadow overflow-hidden sm:rounded-md border border-gray-200">
            <ul class="divide-y divide-gray-200">
                @if (isset($kandydati) && count($kandydati) > 0)
                    @foreach ($kandydati as $kand)
                        <li class="px-6 py-4 flex items-center justify-between hover:bg-gray-50">
                            <div>
                                <p class="text-sm font-bold text-blue-600">{{ $kand->nazwa }}</p>
                                <p class="text-xs text-gray-600 mt-1">{{ $kand->opis }}</p>
                                <span class="inline-block mt-2 bg-emerald-100 text-emerald-800 text-xs px-2 py-0.5 rounded font-semibold">
                                    Plik zabezpieczony szyfrowaniem systemowym
                                </span>
                            </div>
                            <div class="text-xs text-gray-400">
                                Przesłano: {{ $kand->created_at ?? 'Teraz' }}
                            </div>
                        </li>
                    @endforeach
                @else
                    <li class="px-6 py-4 text-center text-gray-500 text-sm">
                        Brak przesłanych CV w systemie. Wypełnij formularz na stronie <a href="{{ route('rekrutacja.form') }}" class="text-blue-600 underline">/rekrutacja</a>.
                    </li>
                @endif
            </ul>
        </div>
    </div>
</body>
</html>