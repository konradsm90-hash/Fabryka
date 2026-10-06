<?php include resource_path('views/layouts/header.php'); ?>

<h2 class="text-2xl font-bold mb-6">Zarządzanie Nieobecnościami i L4</h2>

<!-- Komunikaty walidacji i sukcesu -->
<?php if (session('error')): ?>
    <div class="mb-6 p-4 bg-red-100 border border-red-300 text-red-800 rounded font-medium text-sm">
         <?= session('error') ?>
    </div>
<?php endif; ?>

<?php if (session('success')): ?>
    <div class="mb-6 p-4 bg-green-100 border border-green-300 text-green-800 rounded font-medium text-sm">
         <?= session('success') ?>
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <!-- Formularz dodawania pojedynczego wniosku -->
    <div class="bg-white p-6 rounded shadow col-span-1">
        <h3 class="text-lg font-bold mb-4">Zgłoś nieobecność</h3>
        <form action="<?= route('nieobecnosci.store') ?>" method="POST" class="space-y-4">
            <?= csrf_field() ?>
            <div>
                <label class="block text-sm font-medium mb-1">Pracownik</label>
                <select name="pracownik_id" class="w-full border p-2 rounded" required>
                    <?php foreach ($pracownicy as $p): ?>
                        <option value="<?= htmlspecialchars($p->id) ?>">
                            <?= htmlspecialchars($p->imie) ?> <?= htmlspecialchars($p->nazwisko) ?> (pozostało: <?= $p->pobierzPozostalyUrlop() ?> d.)
                        </option>
                    <?php endforeach; ?>
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

            <button type="submit" class="w-full bg-emerald-600 text-white py-2 rounded hover:bg-emerald-700 font-medium">Zgłoś Wniosek</button>
        </form>
    </div>

    <!-- Tabela wniosków z możliwością zatwierdzania/odrzucania -->
    <div class="bg-white p-6 rounded shadow col-span-2">
        <h3 class="text-lg font-bold mb-4">Ewidencja i Decyzje ws. Absencji</h3>
        
        <form action="<?= route('nieobecnosci.masowe') ?>" method="POST">
            <?= csrf_field() ?>
            
            <div class="mb-4 p-3 bg-gray-50 border rounded flex justify-between items-center gap-4">
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="selectAll" onclick="toggleAll(this)" class="w-4 h-4 text-blue-600 border-gray-300 rounded">
                    <label for="selectAll" class="text-sm font-medium text-gray-700">Zaznacz wszystkie</label>
                </div>
                <div class="flex items-center gap-2">
                    <select name="akcja" class="border p-1.5 rounded text-sm" required>
                        <option value="">-- Wybierz masową akcję --</option>
                        <option value="ZATWIERDZ">Zatwierdź zaznaczone</option>
                        <option value="ODRZUC">Odrzuć zaznaczone</option>
                        <option value="USUN">Usuń zaznaczone</option>
                    </select>
                    <button type="submit" class="bg-slate-800 text-white text-sm px-3 py-1.5 rounded hover:bg-slate-900">Wykonaj</button>
                </div>
            </div>

            <table class="w-full text-left text-sm border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b text-gray-600 text-xs uppercase">
                        <th class="p-2 w-8"></th>
                        <th class="p-2">Pracownik</th>
                        <th class="p-2">Typ</th>
                        <th class="p-2">Okres</th>
                        <th class="p-2">Status</th>
                        <th class="p-2 text-right">Decyzja</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php if (!empty($nieobecnosci) && count($nieobecnosci) > 0): ?>
                        <?php foreach ($nieobecnosci as $n): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="p-2">
                                <input type="checkbox" name="ids[]" value="<?= htmlspecialchars($n->id) ?>" class="item-checkbox w-4 h-4 border-gray-300 rounded">
                            </td>
                            <td class="p-2 font-medium"><?= htmlspecialchars($n->pracownik->imie ?? '') ?> <?= htmlspecialchars($n->pracownik->nazwisko ?? '') ?></td>
                            <td class="p-2 font-semibold text-xs"><?= htmlspecialchars($n->typ) ?></td>
                            <td class="p-2 text-xs font-mono"><?= htmlspecialchars($n->data_od) ?> do <?= htmlspecialchars($n->data_do) ?> (<?= htmlspecialchars($n->liczba_dni_roboczych ?? 0) ?> d.)</td>
                            <td class="p-2">
                                <?php
                                    $statusCss = match($n->status ?? 'OCZEKUJE') {
                                        'ZATWIERDZONE' => 'bg-green-100 text-green-800',
                                        'ODRZUCONE' => 'bg-red-100 text-red-800',
                                        default => 'bg-yellow-100 text-yellow-800',
                                    };
                                ?>
                                <span class="text-xs font-bold px-2 py-0.5 rounded <?= $statusCss ?>">
                                    <?= htmlspecialchars($n->status ?? 'OCZEKUJE') ?>
                                </span>
                            </td>
                            <td class="p-2 text-right">
                                <div class="inline-flex gap-1">
                                    <?php if (($n->status ?? 'OCZEKUJE') !== 'ZATWIERDZONE'): ?>
                                        <form action="<?= route('nieobecnosci.zatwierdz', $n->id) ?>" method="POST" class="inline">
                                            <?= csrf_field() ?>
                                            <button type="submit" title="Zatwierdź" class="bg-green-600 text-white px-2 py-1 rounded text-xs font-medium hover:bg-green-700">✓ Zatwierdź</button>
                                        </form>
                                    <?php endif; ?>

                                    <?php if (($n->status ?? 'OCZEKUJE') !== 'ODRZUCONE'): ?>
                                        <form action="<?= route('nieobecnosci.odrzuc', $n->id) ?>" method="POST" class="inline">
                                            <?= csrf_field() ?>
                                            <button type="submit" title="Odrzuć" class="bg-red-600 text-white px-2 py-1 rounded text-xs font-medium hover:bg-red-700">✕ Odrzuć</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="p-4 text-center text-gray-500">Brak zarejestrowanych nieobecności.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </form>
    </div>
</div>

<script>
function toggleAll(source) {
    checkboxes = document.getElementsByClassName('item-checkbox');
    for (var i = 0; i < checkboxes.length; i++) {
        checkboxes[i].checked = source.checked;
    }
}
</script>

<?php include resource_path('views/layouts/footer.php'); ?>