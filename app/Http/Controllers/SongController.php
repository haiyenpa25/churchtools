<?php

namespace App\Http\Controllers;

use App\Models\Song;
use Illuminate\Http\Request;

class SongController extends Controller
{
    public function index()
    {
        return view('songs.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'number' => 'nullable|string|max:50',
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'lyrics' => 'required|string',
        ]);

        $song = Song::create($validated);

        return response()->json(['status' => 'success', 'song' => $song]);
    }

    public function update(Request $request, $id)
    {
        $song = Song::findOrFail($id);

        $validated = $request->validate([
            'number' => 'nullable|string|max:50',
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'lyrics' => 'required|string',
        ]);

        $song->update($validated);

        return response()->json(['status' => 'success', 'song' => $song]);
    }

    public function destroy($id)
    {
        $song = Song::findOrFail($id);
        $song->delete();

        return response()->json(['status' => 'success']);
    }
}
