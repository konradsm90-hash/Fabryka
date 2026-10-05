<?php

namespace App\Http\Controllers;

use App\Models\ListaPlac;
use App\Services\SilnikListyPlacService;
use Illuminate\Http\Request;

class PlacaController extends Controller
{
    public function index()
    {
        $listy = ListaPlac::orderBy('rok', 'desc')->orderBy('miesiac', 'desc')->get();
        return view('listy-plac.index', compact('listy'));
    }

    public function show(int $id)
    {
        $lista = ListaPlac::with('pozycje.pracownik')->findOrFail($id);
        return view('listy-plac.show', compact('lista'));
    }

    public function przelicz(Request $request, SilnikListyPlacService $silnik)
    {
        // Zwiekszenie limitu czasu wykonywania i pamieci RAM dla duzej liczby pracownikow
        set_time_limit(300);
        ini_set('memory_limit', '512M');

        $request->validate([
            'rok' => 'required|integer',
            'miesiac' => 'required|integer|between:1,12',
        ]);

        $silnik->przeliczMiesiac($request->rok, $request->miesiac);

        return redirect()->back()->with('success', 'Lista płac została przeliczona.');
    }
}