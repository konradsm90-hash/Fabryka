<?php

namespace App\Http\Controllers;

use App\Models\ListaPlac;
use App\Services\SilnikListyPlacService;
use Illuminate\Http\Request;
use App\Http\Controllers\PlacaController;

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
    public function masoweAkcje(Request $request)
    {
        $ids = $request->input('ids', []);
        $akcja = $request->input('akcja');

        if (empty($ids)) {
            return back()->with('success', 'Nie wybrano żadnej listy płac.');
        }

        if ($akcja === 'ZAMKNIJ') {
            \App\Models\ListaPlac::whereIn('id', $ids)->update(['status' => 'ZAMKNIETA']);
        } elseif ($akcja === 'OTWORZ') {
            \App\Models\ListaPlac::whereIn('id', $ids)->update(['status' => 'SZKIC']);
        } elseif ($akcja === 'USUN') {
            \App\Models\ListaPlac::whereIn('id', $ids)->where('status', '!=', 'ZAMKNIETA')->delete();
        }

        return back()->with('success', 'Zaktualizowano status wybranych list płac.');
    }
}