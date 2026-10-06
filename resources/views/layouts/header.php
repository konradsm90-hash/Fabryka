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
            <h1 class="text-xl font-bold tracking-wide">Fabryka</h1>
            <div class="space-x-4">
                <a href="<?= route('pulpit') ?>" class="hover:text-blue-300 font-medium">Pulpit</a>
                <a href="<?= route('listy-plac.index') ?>" class="hover:text-blue-300 font-medium">Listy Płac</a>
                <a href="<?= route('grafik.index') ?>" class="hover:text-blue-300 font-medium">Grafik i Naruszenia</a>
                <a href="<?= route('korekty.index') ?>" class="hover:text-blue-300 font-medium">Korekty RCP</a>
                <a href="<?= route('nieobecnosci.index') ?>" class="hover:text-blue-300 font-medium">Nieobecności</a>
                <a href="<?= route('raporty.index') ?>" class="hover:text-blue-300 font-medium">Raporty i Koszty</a>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto p-6 flex-grow w-full">
        <?php if (session('success')): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                <?= htmlspecialchars(session('success')) ?>
            </div>
        <?php endif; ?>