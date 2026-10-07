<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ProdukcjaController extends Controller
{
    
    public function kanban()
    {
        $zadania = Schema::hasTable('zadania_produkcyjne') 
            ? DB::table('zadania_produkcyjne')->get() 
            : collect();

        $zlecenia = Schema::hasTable('zlecenia_produkcyjne') 
            ? DB::table('zlecenia_produkcyjne')->get() 
            : collect();

        $pracownicy = Schema::hasTable('pracownicy') 
            ? DB::table('pracownicy')->get() 
            : collect();

        $kanban = [
            'DO_ZROBIENIA' => $zadania->filter(function($z) {
                $st = strtoupper(str_replace(' ', '_', $z->status ?? ''));
                return $st === 'DO_ZROBIENIA' || $st === '' || $st === 'NEW';
            }),
            'W_TRAKCIE' => $zadania->filter(function($z) {
                $st = strtoupper(str_replace(' ', '_', $z->status ?? ''));
                return $st === 'W_TRAKCIE' || $st === 'IN_PROGRESS';
            }),
            'KONTROLA' => $zadania->filter(function($z) {
                $st = strtoupper(str_replace(' ', '_', $z->status ?? ''));
                return $st === 'KONTROLA';
            }),
            'ZAKONCZONE' => $zadania->filter(function($z) {
                $st = strtoupper(str_replace(' ', '_', $z->status ?? ''));
                return $st === 'ZAKONCZONE' || $st === 'DONE';
            }),
        ];

        $kolumny = [
            'DO_ZROBIENIA' => ['tytul' => 'Do Zrobienia', 'bg' => 'bg-slate-100', 'border' => 'border-slate-400'],
            'W_TRAKCIE'    => ['tytul' => 'W Trakcie Realizacji', 'bg' => 'bg-blue-50', 'border' => 'border-blue-500'],
            'KONTROLA'     => ['tytul' => 'Kontrola Jakości', 'bg' => 'bg-amber-50', 'border' => 'border-amber-500'],
            'ZAKONCZONE'   => ['tytul' => 'Zakończone', 'bg' => 'bg-emerald-50', 'border' => 'border-emerald-500'],
        ];

        if (view()->exists('produkcja.kanban')) {
            return view('produkcja.kanban', compact('zadania', 'zlecenia', 'pracownicy', 'kanban', 'kolumny'));
        }

        return view('kanban', compact('zadania', 'zlecenia', 'pracownicy', 'kanban', 'kolumny'));
    }

    public function mojeZadania()
    {
        $zadania = Schema::hasTable('zadania_produkcyjne') 
            ? DB::table('zadania_produkcyjne')->get() 
            : collect();

        if (view()->exists('produkcja.panel-pracownika')) {
            return view('produkcja.panel-pracownika', compact('zadania'));
        }

        if (view()->exists('produkcja.pracownik')) {
            return view('produkcja.pracownik', compact('zadania'));
        }

        return view('panel-pracownika', compact('zadania'));
    }

    public function generujZlecenie(Request $request)
    {
        if (Schema::hasTable('zadania_produkcyjne')) {
            DB::table('zadania_produkcyjne')->insert([
                'nazwa' => $request->input('nazwa_zadania') ?? $request->input('nazwa', 'Nowe Zadanie'),
                'opis' => $request->input('opis_wykonania') ?? $request->input('opis', ''),
                'szacowany_czas_minut' => $request->input('szacowany_czas_minut', 30),
                'status' => 'DO_ZROBIENIA',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect()->back()->with('success', 'Pomyślnie dodano nowe zadanie.');
    }

    public function zmienStatus(Request $request, $id)
    {
        $status = strtoupper(str_replace(' ', '_', $request->input('status', 'DO_ZROBIENIA')));

        if (Schema::hasTable('zadania_produkcyjne')) {
            DB::table('zadania_produkcyjne')
                ->where('id', $id)
                ->update([
                    'status' => $status,
                    'updated_at' => now(),
                ]);
        }

        return redirect()->back()->with('success', 'Status zadania został zaktualizowany.');
    }

  
    public function przypiszPracownika(Request $request, $id)
    {
        if (Schema::hasTable('zadania_produkcyjne')) {
            $updateData = ['updated_at' => now()];

            if (Schema::hasColumn('zadania_produkcyjne', 'pracownik_id')) {
                $updateData['pracownik_id'] = $request->input('pracownik_id');
            }

            DB::table('zadania_produkcyjne')
                ->where('id', $id)
                ->update($updateData);
        }

        return redirect()->back()->with('success', 'Pracownik został przypisany do zadania.');
    }


    public function dodajKomentarz(Request $request, $id)
    {
        $komentarz = $request->input('komentarz') ?? $request->input('tresc', '');

        if (Schema::hasTable('zadania_komentarze') && !empty($komentarz)) {
            DB::table('zadania_komentarze')->insert([
                'zadanie_id' => $id,
                'tresc' => $komentarz,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect()->back()->with('success', 'Komentarz został dodany.');
    }
}