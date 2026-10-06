<?php include resource_path('views/layouts/header.php'); ?>

<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Pulpit Główny</h2>
        <p class="text-gray-500 text-sm">Przegląd obecności, wniosków i anomalii RCP</p>
    </div>
    <span class="bg-blue-50 text-blue-700 px-3 py-1 rounded text-xs font-semibold">Dzisiaj: <?= date('d.m.Y') ?></span>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

    <!-- Obecni Dzisiaj -->
    <div class="bg-white p-5 rounded shadow border-l-4 border-blue-500">
        <p class="text-xs font-bold text-gray-500 uppercase">Obecni Dzisiaj</p>
        <div class="flex items-baseline justify-between mt-1">
            <p class="text-3xl font-bold text-gray-800"><?= $obecniDzisiaj ?></p>
            <span class="text-xs font-medium text-gray-500">z <?= $liczbaPracownikow ?> kadry</span>
        </div>
    </div>

    <!-- Nieobecni Dzisiaj -->
    <div class="bg-white p-5 rounded shadow border-l-4 border-orange-500">
        <p class="text-xs font-bold text-gray-500 uppercase">Nieobecni Dzisiaj</p>
        <div class="flex items-baseline justify-between mt-1">
            <p class="text-3xl font-bold text-orange-600"><?= $liczbaNieobecnychDzisiaj ?></p>
            <span class="text-xs font-medium text-gray-500">Frekwencja: <?= $procentObecnosci ?>%</span>
        </div>
    </div>

    <!-- Wnioski do decyzji -->
    <div class="bg-white p-5 rounded shadow border-l-4 border-amber-500">
        <p class="text-xs font-bold text-gray-500 uppercase">Wnioski do decyzji</p>
        <p class="text-3xl font-bold text-amber-600 mt-1"><?= count($oczekujaceWnioski) ?></p>
    </div>

</div>

<!-- Sekcja Główna -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- Wnioski oczekujące na akceptację  -->
    <div class="bg-white p-6 rounded shadow lg:col-span-2">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-800">⏳ Wnioski oczekujące na akceptację</h3>
            <a href="<?= route('nieobecnosci.index') ?>" class="text-xs text-blue-600 hover:underline">Przejdź do wniosków</a>
        </div>

        <?php if (session('success')): ?>
            <div class="p-3 mb-4 text-sm text-green-800 bg-green-100 rounded"><?= session('success') ?></div>
        <?php endif; ?>

        <?php if (count($oczekujaceWnioski) > 0): ?>
            <table class="w-full text-left text-sm border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b text-gray-600 text-xs uppercase">
                        <th class="p-2">Pracownik</th>
                        <th class="p-2">Typ</th>
                        <th class="p-2">Okres</th>
                        <th class="p-2 text-right">Decyzja</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($oczekujaceWnioski as $w): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="p-2 font-medium">
                                <?= htmlspecialchars($w->pracownik->imie ?? '') ?> <?= htmlspecialchars($w->pracownik->nazwisko ?? '') ?>
                            </td>
                            <td class="p-2 text-xs font-semibold"><?= htmlspecialchars($w->typ) ?></td>
                            <td class="p-2 text-xs text-gray-600"><?= htmlspecialchars($w->data_od) ?> do <?= htmlspecialchars($w->data_do) ?> (<?= $w->liczba_dni_roboczych ?> d.)</td>
                            <td class="p-2 text-right">
                                <div class="inline-flex gap-1">
                                    <form action="<?= route('nieobecnosci.zatwierdz', $w->id) ?>" method="POST" class="inline">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="bg-green-600 text-white px-2.5 py-1 rounded text-xs hover:bg-green-700">✓ Zatwierdź</button>
                                    </form>
                                    <form action="<?= route('nieobecnosci.odrzuc', $w->id) ?>" method="POST" class="inline">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="bg-red-600 text-white px-2.5 py-1 rounded text-xs hover:bg-red-700">✕ Odrzuć</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-sm text-gray-500 py-3">Brak nowych wniosków do rozpatrzenia.</p>
        <?php endif; ?>
    </div>

    <div class="space-y-6 lg:col-span-1">

        <!-- Anomalie RCP -->
        <div class="bg-white p-6 rounded shadow border-t-4 border-red-500">
            <div class="flex justify-between items-center mb-3">
                <h3 class="text-md font-bold text-gray-800">⚠ Anomalie RCP (Brak Wyjścia)</h3>
                <a href="<?= route('korekty.index') ?>" class="text-xs text-blue-600 hover:underline">Korekty</a>
            </div>
            <?php if (count($anomalieRcp) > 0): ?>
                <ul class="divide-y divide-gray-100 text-sm">
                    <?php foreach ($anomalieRcp as $a): ?>
                        <li class="py-2 flex justify-between items-center">
                            <div>
                                <p class="font-medium"><?= htmlspecialchars($a->pracownik->imie ?? '') ?> <?= htmlspecialchars($a->pracownik->nazwisko ?? '') ?></p>
                                <p class="text-xs text-gray-500">Wejście: <?= $a->czas_odbicia ? $a->czas_odbicia->format('H:i:s') : '-' ?></p>
                            </div>
                            <span class="text-xs bg-red-100 text-red-700 px-2 py-0.5 rounded font-bold">Brak wyjścia</span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="text-xs text-gray-500">Brak wykrytych anomalii w odbiciach.</p>
            <?php endif; ?>
        </div>

        <!-- nieobecni -->
        <div class="bg-white p-6 rounded shadow">
            <div class="flex justify-between items-center mb-3">
                <h3 class="text-md font-bold text-gray-800">🌴 Nieobecni dzisiaj</h3>
                <a href="<?= route('nieobecnosci.index') ?>" class="text-xs text-blue-600 hover:underline">Zobacz wszystkich</a>
            </div>
            <?php if (count($nieobecniDzisiaj) > 0): ?>
                <ul class="divide-y divide-gray-100 text-sm">
                    <?php foreach ($nieobecniDzisiaj as $n): ?>
                        <li class="py-2 flex justify-between items-center">
                            <div>
                                <p class="font-medium"><?= htmlspecialchars($n->pracownik->imie ?? '') ?> <?= htmlspecialchars($n->pracownik->nazwisko ?? '') ?></p>
                                <p class="text-xs text-gray-500">Do <?= htmlspecialchars($n->data_do) ?></p>
                            </div>
                            <span class="text-xs bg-orange-100 text-orange-800 px-2 py-0.5 rounded font-semibold"><?= htmlspecialchars($n->typ) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="text-xs text-gray-500">Wszyscy pracownicy są dzisiaj obecni.</p>
            <?php endif; ?>
        </div>

    </div>
</div>

<?php include resource_path('views/layouts/footer.php'); ?>