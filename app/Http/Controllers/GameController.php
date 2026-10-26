<?php

namespace App\Http\Controllers;

use App\Events\GameJoined;
use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class GameController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return inertia('Dashboard',[
            'games' => Game::with(['playerOne', 'playerTwo'])
                ->whereNull('player_two_id')
                ->where('player_one_id', '!=', $request->user()->id)
                ->latest()
                ->simplePaginate(5)
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $game = Game::create([
            'player_one_id' => $request->user()->id,
            'player_two_id' => null, // You can set this to a random user ID or leave it null for now
        ]);

        return to_route('games.show', $game);
    }

    public function join(Game $game, Request $request)
    {

        Gate::authorize('join', $game);

        if ($game->player_two_id) {
            return redirect()->back()->with('error', 'This game already has two players.');
        }

        $game->player_two_id = $request->user()->id;
        $game->save();

        GameJoined::dispatch($game);

        return to_route('games.show', $game);
    }

    /**
     * Display the specified resource.
     */
    public function show(Game $game)
    {
        return inertia('Games/Show', [
            'game' => $game->load(['playerOne', 'playerTwo']),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Game $game)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Game $game)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Game $game)
    {
        //
    }
}
