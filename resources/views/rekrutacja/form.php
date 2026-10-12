<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Strefa Kariery - Prześlij CV</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <h2 class="text-center text-3xl font-extrabold text-gray-900">Dołącz do naszego zespołu</h2>
        <p class="mt-2 text-center text-sm text-gray-600">Prześlij swoje CV – dokument zostanie w pełni zaszyfrowany w systemie</p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10 border border-gray-200">
            <?php if (session('success')): ?>
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded text-sm">
                    <?= htmlspecialchars(session('success')) ?>
                </div>
            <?php endif; ?>

            <form action="<?= route('rekrutacja.store') ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
                <?= csrf_field() ?>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Imię</label>
                    <input type="text" name="imie" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Nazwisko</label>
                    <input type="text" name="nazwisko" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Stanowisko</label>
                    <input type="text" name="stanowisko" placeholder="np. Operator CNC / Montażysta" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Deklarowane godziny w tygodniu</label>
                    <input type="number" name="godziny" value="40" min="1" max="60" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Plik CV (PDF / DOCX)</label>
                    <input type="file" name="cv_file" required class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>

                <div>
                    <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none">
                        Wyślij zaszyfrowane CV
                    </button>
                </div>
            </form>
            
            <div class="mt-4 text-center">
                <a href="<?= route('menadzer.login') ?>" class="text-xs text-blue-600 hover:underline">Logowanie dla Menedżera</a>
            </div>
        </div>
    </div>
</body>
</html>