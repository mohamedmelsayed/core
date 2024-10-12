<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Playlist;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class PlaylistController extends Controller
{
    // Show the form to create a new playlist
    public function create()
    {
        $categories = Category::all();
        return view('admin.playlists.create', compact('categories'));
    }

    // Store a new playlist
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:audio,video',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'sub_category_id' => 'required|exists:sub_categories,id',
        ]);

        if ($request->hasFile('cover_image')) {
            $coverImage = $request->file('cover_image')->store('playlists', 'public');
        }

        Playlist::create([
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
            'cover_image' => $coverImage ?? null,
            'sub_category_id' => $request->sub_category_id,
        ]);

        return redirect()->route('admin.playlist.index')->with('success', 'Playlist created successfully.');
    }

    // Display playlists with search functionality
    public function index(Request $request)
    {
        $searchTerm = $request->input('search');

        $playlists = Playlist::with('subCategory')
            ->when($searchTerm, function ($query, $searchTerm) {
                return $query->whereHas('subCategory', function ($q) use ($searchTerm) {
                    $q->where('name', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('name_en', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('id', $searchTerm);
                })->orWhereHas('subCategory.category', function ($q) use ($searchTerm) {
                    $q->where('name', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('name_en', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('id', $searchTerm);
                });
            })
            ->get();

        return view('admin.playlists.index', compact('playlists'));
    }

    // Show the form to edit a playlist
    public function edit(Playlist $playlist)
    {
        $categories = Category::all();
        $subCategories = SubCategory::all();
        return view('admin.playlists.edit', compact('playlist', 'categories', 'subCategories'));
    }

    // Update a playlist
    public function update(Request $request, Playlist $playlist)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:audio,video',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'sub_category_id' => 'required|exists:sub_categories,id',
        ]);

        if ($request->hasFile('cover_image')) {
            $coverImage = $request->file('cover_image')->store('playlists', 'public');
        }

        $playlist->update([
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
            'cover_image' => $coverImage ?? $playlist->cover_image,
            'sub_category_id' => $request->sub_category_id,
        ]);

        return redirect()->route('admin.playlist.index')->with('success', 'Playlist updated successfully.');
    }

    // Delete a playlist
    public function destroy(Playlist $playlist)
    {
        $playlist->delete();
        return redirect()->route('admin.playlist.index')->with('success', 'Playlist deleted successfully.');
    }
}
