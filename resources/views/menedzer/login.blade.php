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
            @if ($errors->any())
                <div class="mb-4 bg-red-900 border border-red-700 text-red-200 px-4 py-3 rounded text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('menadzer.zaloguj') }}" method="POST" class="space-y-6">
                @csrf
                
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
                <a href="{{ route('rekrutacja.form') }}" class="text-xs text-blue-400 hover:underline">← Powrót do formularza CV</a>
            </div>
        </div>
    </div>
</body>
</html>