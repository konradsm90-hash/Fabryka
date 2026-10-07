<?php
if (file_exists(resource_path('views/layouts/header.php'))) {
    include resource_path('views/layouts/header.php');
}
?>

<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6 text-gray-800">Tablica Kanban - Produkcja</h1>

    <!-- Formularz wpisywania własnego zadania -->
    <div class="bg-white p-4 rounded-lg shadow mb-6 border border-gray-200">
        <h2 class="text-lg font-semibold mb-3 text-gray-700">Dodaj nowe zadanie / zlecenie</h2>
        <form action="<?= route('zlecenia.generuj') ?>" method="POST" class="flex flex-wrap gap-3 items-center">
            <?= csrf_field() ?>
            <input type="text" name="nazwa_zadania" placeholder="Co trzeba zrobić? (np. Montaż elementu X)" required class="border border-gray-300 rounded px-3 py-2 flex-1 min-w-[250px] focus:outline-none focus:ring-2 focus:ring-blue-500">
            <input type="text" name="opis_wykonania" placeholder="Opis / Uwagi (opcjonalnie)" class="border border-gray-300 rounded px-3 py-2 flex-1 min-w-[200px] focus:outline-none focus:ring-2 focus:ring-blue-500">
            <input type="number" name="szacowany_czas_minut" placeholder="Czas (min)" value="30" min="1" class="border border-gray-300 rounded px-3 py-2 w-28 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-5 py-2 rounded shadow transition">
                + Dodaj zadanie
            </button>
        </form>
    </div>

    <!-- Kolumny Kanban -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <?php foreach ($kolumny as $statusKey => $col): ?>
            <div class="<?= $col['bg'] ?> p-4 rounded-lg min-h-[600px] border-t-4 <?= $col['border'] ?> shadow-sm">
                <h3 class="font-bold text-gray-700 mb-3 flex justify-between items-center">
                    <span><?= $col['tytul'] ?></span>
                    <span class="bg-white px-2 py-0.5 rounded text-xs font-bold shadow-sm">
                        <?= count($kanban[$statusKey] ?? []) ?>
                    </span>
                </h3>

                <div class="space-y-3">
                    <?php foreach ($kanban[$statusKey] ?? [] as $zadanie): ?>
                        <div class="bg-white p-4 rounded shadow-sm border border-gray-200 hover:shadow-md transition">
                            <h4 class="font-semibold text-gray-800 text-sm mb-1">
                                <?= htmlspecialchars($zadanie->nazwa_zadania ?? $zadanie->nazwa ?? 'Zadanie') ?>
                            </h4>

                            <?php if (!empty($zadanie->opis_wykonania)): ?>
                                <p class="text-xs text-gray-500 mb-2"><?= htmlspecialchars($zadanie->opis_wykonania) ?></p>
                            <?php endif; ?>

                            <?php if (!empty($zadanie->szacowany_czas_minut)): ?>
                                <div class="text-xs text-gray-400 mb-2">⏱ Czas: <?= $zadanie->szacowany_czas_minut ?> min</div>
                            <?php endif; ?>

                            <?php if (!empty($zadanie->imie)): ?>
                                <div class="text-xs text-blue-600 font-medium mb-2">👤 <?= htmlspecialchars($zadanie->imie . ' ' . $zadanie->nazwisko) ?></div>
                            <?php endif; ?>

                            <!-- Zmiana statusu -->
                            <form action="<?= route('kanban.status', $zadanie->id) ?>" method="POST" class="mt-2">
                                <?= csrf_field() ?>
                                <select name="status" onchange="this.form.submit()" class="text-xs border border-gray-300 rounded p-1 w-full bg-gray-50">
                                    <option value="DO_ZROBIENIA" <?= ($statusKey === 'DO_ZROBIENIA') ? 'selected' : '' ?>>Do Zrobienia</option>
                                    <option value="W_TRAKCIE" <?= ($statusKey === 'W_TRAKCIE') ? 'selected' : '' ?>>W trakcie</option>
                                    <option value="KONTROLA" <?= ($statusKey === 'KONTROLA') ? 'selected' : '' ?>>Kontrola Jakości</option>
                                    <option value="ZAKONCZONE" <?= ($statusKey === 'ZAKONCZONE') ? 'selected' : '' ?>>Zakończone</option>
                                </select>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>