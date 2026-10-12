<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Panel Menedżera - Przesłane CV</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-900 text-white min-h-screen">
    <nav class="bg-slate-800 border-b border-slate-700 p-4 shadow flex justify-between items-center">
        <h1 class="text-xl font-bold text-indigo-400">Panel Menedżera / Rekrutacja</h1>
        <div class="flex items-center gap-4">
            <a href="<?= route('kanban.index') ?>" class="text-sm bg-slate-700 hover:bg-slate-600 px-3 py-1.5 rounded transition">Tablica Kanban</a>
            <form action="<?= route('menadzer.wyloguj') ?>" method="POST" class="inline">
                <?= csrf_field() ?>
                <button type="submit" class="text-sm bg-red-600 hover:bg-red-500 px-3 py-1.5 rounded transition">Wyloguj</button>
            </form>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto p-6">
        <h2 class="text-2xl font-bold mb-6">Lista kandydatów (Dedykowana baza zgłoszeń)</h2>

        <div class="bg-slate-800 shadow-xl rounded-xl overflow-hidden border border-slate-700">
            <?php if (isset($kandydati) && count($kandydati) > 0): ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-300">
                        <thead class="bg-slate-700 text-indigo-300 uppercase text-xs">
                            <tr>
                                <th class="p-4">ID</th>
                                <th class="p-4">Imię i Nazwisko</th>
                                <th class="p-4">Stanowisko</th>
                                <th class="p-4">Dostępność</th>
                                <th class="p-4">Plik CV</th>
                                <th class="p-4">Data zgłoszenia</th>
                                <th class="p-4 text-right">Akcja</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-700">
                            <?php foreach ($kandydati as $kand): ?>
                                <tr class="hover:bg-slate-750 transition">
                                    <td class="p-4 font-mono text-xs text-slate-400">#<?= $kand->id ?></td>
                                    <td class="p-4 font-semibold text-white"><?= htmlspecialchars($kand->imie . ' ' . $kand->nazwisko) ?></td>
                                    <td class="p-4"><?= htmlspecialchars($kand->stanowisko) ?></td>
                                    <td class="p-4"><?= $kand->godziny ?> h/tyg</td>
                                    <td class="p-4">
                                        <span class="inline-flex items-center gap-1.5 bg-indigo-500/10 text-indigo-300 px-2.5 py-1 rounded-full text-xs font-medium border border-indigo-500/20">
                                            🔒 <?= htmlspecialchars($kand->plik_oryginalny) ?>
                                        </span>
                                    </td>
                                    <td class="p-4 text-xs text-slate-400"><?= $kand->created_at ?></td>
                                    <td class="p-4 text-right">
                                        <a href="<?= route('menadzer.cv', $kand->id) ?>" class="bg-indigo-600 hover:bg-indigo-500 text-white px-3 py-1.5 rounded text-xs font-medium transition inline-flex items-center gap-1">
                                            📥 Pobierz CV
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="p-8 text-center text-slate-400">
                    Brak przesłanych CV w systemie. Wypełnij formularz rekrutacyjny na stronie <a href="<?= route('rekrutacja.form') ?>" class="text-indigo-400 underline">/rekrutacja</a>.
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>