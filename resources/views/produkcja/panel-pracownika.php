<?php 
$headerPath = resource_path('views/layouts/header.php');
if (file_exists($headerPath)) {
    include $headerPath;
} elseif (file_exists(resource_path('views/layouts/header.blade.php'))) {
    include resource_path('views/layouts/header.blade.php');
}

// Zabezpieczenie nazw zmiennych przekazywanych z kontrolera
$listaZadan = $mojeZadania ?? $zadania ?? collect();
?>

<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Moje Zadania Produkcyjne</h2>
        <p class="text-gray-500 text-sm">Lista przypisanych zadań i aktualizacja statusów</p>
    </div>

    <?php if (count($listaZadan) > 0): ?>
        <div class="space-y-6">
            <?php foreach ($listaZadan as $z): ?>
                <?php 
                    $status = $z->status ?? 'DO_ZROBIENIA';
                    $kodZlecenia = $z->kod_zlecenia ?? $z->zlecenie->kod_zlecenia ?? ('ZLE-' . $z->id);
                    $nazwaZadania = $z->nazwa_zadania ?? $z->nazwa ?? ('Zadanie #' . $z->id);
                    $opisZadania = $z->opis_wykonania ?? $z->opis ?? 'Brak szczegółowych instrukcji.';

                    if (isset($z->komentarze) && (is_array($z->komentarze) || $z->komentarze instanceof \Countable)) {
                        $komentarze = $z->komentarze;
                    } else {
                        $komentarze = \Illuminate\Support\Facades\Schema::hasTable('zadania_komentarze') 
                            ? \Illuminate\Support\Facades\DB::table('zadania_komentarze')->where('zadanie_id', $z->id)->get() 
                            : [];
                    }
                ?>
                <div class="bg-white p-5 rounded-lg shadow border-l-4 <?= ($status == 'W_TRAKCIE' || $status == 'W trakcie') ? 'border-blue-500' : 'border-gray-300' ?>">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <span class="text-xs bg-blue-100 text-blue-800 font-bold px-2 py-0.5 rounded">
                                <?= htmlspecialchars($kodZlecenia) ?>
                            </span>
                            <h3 class="text-lg font-bold text-gray-800 mt-1">
                                <?= htmlspecialchars($nazwaZadania) ?>
                            </h3>
                        </div>
                  
                        <!-- Zmiana statusu przez pracownika -->
                        <form action="<?= route('kanban.status', $z->id) ?>" method="POST">
                            <?= csrf_field() ?>
                            <select name="status" onchange="this.form.submit()" class="text-sm font-bold p-2 border rounded bg-gray-50">
                                <option value="DO_ZROBIENIA" <?= ($status == 'DO_ZROBIENIA' || $status == 'Do Zrobienia') ? 'selected' : '' ?>>Do Zrobienia</option>
                                <option value="W_TRAKCIE" <?= ($status == 'W_TRAKCIE' || $status == 'W trakcie') ? 'selected' : '' ?>>W Trakcie</option>
                                <option value="KONTROLA" <?= ($status == 'KONTROLA' || $status == 'Kontrola') ? 'selected' : '' ?>>Oddaj do Kontroli</option>
                                <option value="ZAKONCZONE" <?= ($status == 'ZAKONCZONE' || $status == 'Zakończone') ? 'selected' : '' ?>>Zakończone</option>
                            </select>
                        </form>
                    </div>

                    <p class="text-sm text-gray-600 mb-4"><?= htmlspecialchars($opisZadania) ?></p>

                    <!-- Sekcja komentarzy / wiadomości -->
                    <div class="bg-gray-50 p-4 rounded mt-4">
                        <h4 class="text-xs font-bold text-gray-500 uppercase mb-2">Wiadomości i uwagi</h4>
                        
                        <div class="space-y-2 mb-3 max-h-40 overflow-y-auto">
                            <?php if (count($komentarze) === 0): ?>
                                <p class="text-xs text-gray-400 italic">Brak uwag do tego zadania.</p>
                            <?php else: ?>
                                <?php foreach ($komentarze as $k): ?>
                                    <?php 
                                        $autor = $k->pracownik->imie ?? $k->autor ?? 'Pracownik';
                                        $tresc = $k->tresc ?? $k->komentarz ?? '';
                                        $data = '';
                                        if (!empty($k->created_at)) {
                                            $data = (is_object($k->created_at) && method_exists($k->created_at, 'format')) 
                                                ? $k->created_at->format('d.m.Y H:i') 
                                                : date('d.m.Y H:i', strtotime($k->created_at));
                                        }
                                    ?>
                                    <div class="text-xs bg-white p-2 rounded shadow-sm">
                                        <span class="font-bold text-gray-700"><?= htmlspecialchars($autor) ?>:</span>
                                        <span class="text-gray-600"><?= htmlspecialchars($tresc) ?></span>
                                        <?php if ($data): ?>
                                            <span class="text-[10px] text-gray-400 block mt-0.5"><?= $data ?></span>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <!-- Formularz dodawania komentarza -->
                        <form action="<?= route('kanban.komentarz', $z->id) ?>" method="POST" class="flex gap-2">
                            <?= csrf_field() ?>
                            <input type="text" name="komentarz" placeholder="Wpisz uwagę lub zgłoś problem..." class="flex-1 text-xs p-2 border rounded" required>
                            <button type="submit" class="bg-gray-800 text-white text-xs px-3 py-2 rounded font-bold hover:bg-black">Wyślij</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="bg-white p-8 text-center rounded shadow">
            <p class="text-gray-500 font-medium">Nie masz obecnie żadnych aktywnych zadań produkcyjnych.</p>
        </div>
    <?php endif; ?>
</div>

<?php 
$footerPath = resource_path('views/layouts/footer.php');
if (file_exists($footerPath)) {
    include $footerPath;
} elseif (file_exists(resource_path('views/layouts/footer.blade.php'))) {
    include resource_path('views/layouts/footer.blade.php');
}
?>