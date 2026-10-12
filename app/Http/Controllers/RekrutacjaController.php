<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class RekrutacjaController extends Controller
{
    public function index()
    {
        return view('rekrutacja.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'imie' => 'required|string|max:255',
            'nazwisko' => 'required|string|max:255',
            'stanowisko' => 'required|string|max:255',
            'godziny' => 'required|integer|min:1',
            'cv_file' => 'required|file|max:5120', // max 5MB
        ]);

        $file = $request->file('cv_file');
        $originalName = $file->getClientOriginalName();
        
        // Szyfrowanie pliku
        $fileContent = file_get_contents($file->getRealPath());
        $encryptedContent = Crypt::encryptString($fileContent);
        
        $filename = 'cv_' . time() . '_' . uniqid() . '.enc';
        $path = 'encrypted_cvs/' . $filename;
        Storage::put($path, $encryptedContent);

        // Automatyczne utworzenie tabeli kandydatów, jeśli nie istnieje
        if (!Schema::hasTable('kandydati')) {
            Schema::create('kandydati', function ($table) {
                $table->id();
                $table->string('imie');
                $table->string('nazwisko');
                $table->string('stanowisko');
                $table->integer('godziny');
                $table->string('plik_oryginalny');
                $table->string('plik_zaszyfrowany');
                $table->timestamps();
            });
        }

        // Zapis do dedykowanej tabeli kandydatów (całkowicie oddzielonej od produkcji)
        DB::table('kandydati')->insert([
            'imie' => $request->input('imie'),
            'nazwisko' => $request->input('nazwisko'),
            'stanowisko' => $request->input('stanowisko'),
            'godziny' => (int)$request->input('godziny'),
            'plik_oryginalny' => $originalName,
            'plik_zaszyfrowany' => $filename,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('rekrutacja.form')->with('success', 'Twoje CV zostało pomyślnie zaszyfrowane i przesłane do działu rekrutacji!');
    }

    public function pokazLogowanie()
    {
        $dir = resource_path('views/menadzer');
        if (!file_exists($dir)) {
            @mkdir($dir, 0755, true);
        }

        $path = $dir . '/login.php';
        if (!file_exists($path)) {
            $content = <<<'HTML'
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Logowanie Menedżera</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-900 min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8 text-white">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <h2 class="text-center text-3xl font-bold">Panel Menedżera CV</h2>
        <p class="mt-2 text-center text-sm text-slate-400">Wprowadź dane dostępu</p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-slate-800 py-8 px-4 shadow sm:rounded-lg sm:px-10 border border-slate-700">
            <?php if (isset($errors) && $errors->any()): ?>
                <div class="mb-4 bg-red-900 border border-red-700 text-red-200 px-4 py-3 rounded text-sm">
                    <?= $errors->first() ?>
                </div>
            <?php endif; ?>

            <form action="<?= route('menadzer.zaloguj') ?>" method="POST" class="space-y-6">
                <?= csrf_field() ?>
                
                <div>
                    <label class="block text-sm font-medium text-slate-300">Login</label>
                    <input type="text" name="login" value="menadzer" required class="mt-1 block w-full border border-slate-600 bg-slate-900 rounded-md shadow-sm p-2 text-white">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-300">Hasło</label>
                    <input type="password" name="haslo" value="1234" required class="mt-1 block w-full border border-slate-600 bg-slate-900 rounded-md shadow-sm p-2 text-white">
                </div>

                <div>
                    <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                        Zaloguj się
                    </button>
                </div>
            </form>
            
            <div class="mt-4 text-center">
                <a href="<?= route('rekrutacja.form') ?>" class="text-xs text-blue-400 hover:underline">← Powrót do formularza CV</a>
            </div>
        </div>
    </div>
</body>
</html>
HTML;
            @file_put_contents($path, $content);
        }

        ob_start();
        include $path;
        return response(ob_get_clean());
    }

    public function loguj(Request $request)
    {
        if ($request->input('login') === 'menadzer' && $request->input('haslo') === '1234') {
            session(['is_manager' => true]);
            return redirect()->route('menadzer.panel');
        }

        return back()->withErrors(['login' => 'Niepoprawny login lub hasło. Użyj: menadzer / 1234']);
    }

    public function panel()
    {
        if (!session('is_manager')) {
            return redirect()->route('menadzer.login');
        }

        $kandydati = Schema::hasTable('kandydati')
            ? DB::table('kandydati')->orderBy('created_at', 'desc')->get()
            : collect();

        $dir = resource_path('views/menadzer');
        if (!file_exists($dir)) {
            @mkdir($dir, 0755, true);
        }

        // Zawsze aktualizujemy plik panel.php, aby poprawnie obsługiwał nową strukturę tabeli
        $path = $dir . '/panel.php';
        $content = <<<'HTML'
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
HTML;
        @file_put_contents($path, $content);

        ob_start();
        include $path;
        return response(ob_get_clean());
    }

    public function pobierzCv($id)
    {
        if (!session('is_manager')) {
            return redirect()->route('menadzer.login');
        }

        $kandydat = DB::table('kandydati')->where('id', $id)->first();

        if (!$kandydat || !Storage::exists('encrypted_cvs/' . $kandydat->plik_zaszyfrowany)) {
            abort(404, 'Nie znaleziono pliku CV.');
        }

        $encryptedContent = Storage::get('encrypted_cvs/' . $kandydat->plik_zaszyfrowany);
        
        try {
            $decryptedContent = Crypt::decryptString($encryptedContent);
        } catch (\Exception $e) {
            abort(500, 'Błąd odszyfrowywania pliku CV.');
        }

        $extension = pathinfo($kandydat->plik_oryginalny, PATHINFO_EXTENSION);
        $mimeType = 'application/octet-stream';
        if (in_array(strtolower($extension), ['jpg', 'jpeg'])) $mimeType = 'image/jpeg';
        elseif (strtolower($extension) === 'png') $mimeType = 'image/png';
        elseif (strtolower($extension) === 'pdf') $mimeType = 'application/pdf';

        return response($decryptedContent, 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'attachment; filename="' . $kandydat->plik_oryginalny . '"',
        ]);
    }

    public function wyloguj()
    {
        session()->forget('is_manager');
        return redirect()->route('menadzer.login');
    }
}