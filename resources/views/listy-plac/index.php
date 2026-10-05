<?php include resource_path('views/layouts/header.php'); ?>

<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold">Listy Płac</h2>

    <form action="<?= route('listy-plac.przelicz') ?>" method="POST" class="flex gap-2 bg-white p-2 rounded shadow">
        <?= csrf_field() ?>
        <input type="number" name="rok" value="2025" class="border p-1 rounded w-20 text-center" required>
        <input type="number" name="miesiac" value="1" min="1" max="12" class="border p-1 rounded w-16 text-center" required>
        <button type="submit" class="bg-blue-600 text-white px-4 py-1 rounded hover:bg-blue-700">Przelicz okres</button>
    </form>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-gray-50 border-b text-gray-600 uppercase text-xs">
                <th class="p-4">ID</th>
                <th class="p-4">Okres</th>
                <th class="p-4">Status</th>
                <th class="p-4 text-right">Suma Brutto</th>
                <th class="p-4 text-center">Akcja</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            <?php if (!empty($listy) && count($listy) > 0): ?>
                <?php foreach ($listy as $l): ?>
                <tr class="hover:bg-gray-50">
                    <td class="p-4 font-mono">#<?= htmlspecialchars($l->id) ?></td>
                    <td class="p-4 font-semibold"><?= sprintf('%02d', $l->miesiac) ?>/<?= htmlspecialchars($l->rok) ?></td>
                    <td class="p-4">
                        <span class="px-2 py-1 text-xs font-semibold rounded <?= ($l->status ?? '') === 'ZAMKNIETA' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' ?>">
                            <?= htmlspecialchars($l->status ?? 'SZKIC') ?>
                        </span>
                    </td>
                    <td class="p-4 text-right font-bold"><?= number_format($l->suma_brutto ?? 0, 2, ',', ' ') ?> zł</td>
                    <td class="p-4 text-center">
                        <a href="<?= route('listy-plac.show', $l->id) ?>" class="text-blue-600 hover:underline">Szczegóły</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="p-4 text-center text-gray-500">Brak wygenerowanych list płac. Użyj formularza powyżej, aby przeliczyć okres.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include resource_path('views/layouts/footer.php'); ?>