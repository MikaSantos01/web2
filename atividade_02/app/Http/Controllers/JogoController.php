<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Jogos;

class JogoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jogos = Jogos::all();
        return view('jogos.index', compact('jogos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
         return view('jogos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
            $data = $request->all();
        $data['multiplayer'] = $request->has('multiplayer');
        $jogo = Jogos::create($data);
        return redirect()->route('jogos.show', $jogo);
    }

    /**
     * Display the specified resource.
     */
        public function show(string $id)
    {
        $jogo = Jogos::findOrFail($id);
        return view('jogos.show', compact('jogo'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
         $jogo = Jogos::findOrFail($id);
    
         return view('jogos.edit', compact('jogo'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id, Jogos $jogos)
    {
      
    $jogo = Jogos::findOrFail($id);

    $data = $request->all();
    $data['multiplayer'] = $request->has('multiplayer');
    $jogo->update($data);
    return redirect()->route('jogos.show', $jogo);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $jogo = Jogos::findOrFail($id);
    $jogo->delete();
    return redirect()->route('jogos.index');
    }
}
