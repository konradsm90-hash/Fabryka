<?php
use Illuminate\Support\Facades\Route;

// Funkcja zabezpieczająca przed błędem RouteNotFoundException, jeśli dana trasa jeszcze nie istnieje
if (!function_exists('safe_route')) {
    function safe_route($name, $fallbackUrl) {
        return (function_exists('route') && Route::has($name)) ? route($name) : $fallbackUrl;
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System RCP i Listy Płac</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-900 flex flex-col min-h-screen">
    <nav class="bg-slate-800 text-white p-4 shadow-md">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <h1 class="text-xl font-bold tracking-wide">
                <a href="<?= safe_route('pulpit', '/') ?>">Fabryka</a>
            </h1>
            <div class="space-x-3 flex items-center">
                <a href="<?= safe_route('pulpit', '/pulpit') ?>" class="hover:text-blue-300 font-medium">Pulpit</a>
                <a href="<?= safe_route('listy-plac.index', '/listy-plac') ?>" class="hover:text-blue-300 font-medium">Listy Płac</a>
                <a href="<?= safe_route('grafik.index', '/grafik') ?>" class="hover:text-blue-300 font-medium">Grafik i Naruszenia</a>
                <a href="<?= safe_route('korekty.index', '/korekty') ?>" class="hover:text-blue-300 font-medium">Korekty RCP</a>
                <a href="<?= safe_route('nieobecnosci.index', '/nieobecnosci') ?>" class="hover:text-blue-300 font-medium">Nieobecności</a>
                <a href="<?= safe_route('raporty.index', '/raporty') ?>" class="hover:text-blue-300 font-medium">Raporty i Koszty</a>

                <a href="<?= safe_route('kanban.index', '/produkcja/kanban') ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-md text-sm font-semibold transition shadow-sm flex items-center gap-1">
                    Widok Kierownika (Kanban)
                </a>

                <a href="<?= safe_route('pracownik.zadania', safe_route('moje-zadania', '/moje-zadania')) ?>" class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded-md text-sm font-semibold transition shadow-sm flex items-center gap-1">
                    Widok Pracownika (Moje Zadania)
                </a>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto p-6 flex-grow w-full">
        <?php if (session('success')): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm" role="alert">
                <?= htmlspecialchars(session('success')) ?>
            </div>
        <?php endif; ?>