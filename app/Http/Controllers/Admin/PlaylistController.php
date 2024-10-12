<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Playlist;
use App\Models\Category;
use App\Models\Item;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use File;

class PlaylistController extends Controller
{
    // Show the form to create a new playlist
    public function create()
    {
        // Eager-load the 'subCategories' relationship
        $categories = Category::with('subCategories')->get();
        $pageTitle = 'Create New Playlist';
        return view('admin.playlists.create', compact('categories', 'pageTitle'));
    }

    // Store a new playlist
    public function store(Request $request)
    {
        // Validate request input
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:audio,video',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'sub_category_id' => 'required|exists:sub_categories,id',
        ]);

        // Handle image upload
        $imageData = $this->imageUpload($request, null, 'store');

        // Create playlist
        Playlist::create([
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
            'cover_image' => $imageData['portrait'] ?? null, // Store portrait as cover image
            'sub_category_id' => $request->sub_category_id,
        ]);

        return redirect()->route('admin.playlist.index')->with('success', 'Playlist created successfully.');
    }

    // Display playlists with search functionality
    public function index(Request $request)
    {
        $searchTerm = $request->input('search');
        $pageTitle = 'Manage Playlists';

        // Search for playlists by category, sub-category name, or ID
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

        return view('admin.playlists.index', compact('playlists', 'pageTitle'));
    }

    // Show the form to edit a playlist
    public function edit(Playlist $playlist)
    {
        $categories = Category::with('subCategories')->get();
        $subCategories = SubCategory::all();
        $pageTitle = 'Edit Playlist: ' . $playlist->title;
        return view('admin.playlists.edit', compact('playlist', 'categories', 'subCategories', 'pageTitle'));
    }

    // Update a playlist
    public function update(Request $request, Playlist $playlist)
    {
        // Validate request input
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:audio,video',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'sub_category_id' => 'required|exists:sub_categories,id',
        ]);

        // Handle image upload
        $imageData = $this->imageUpload($request, $playlist, 'update');

        // Update playlist
        $playlist->update([
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
            'cover_image' => $imageData['portrait'] ?? $playlist->cover_image, // Store portrait as cover image
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

    // Handle image upload logic
    private function imageUpload($request, $item, $type)
    {
        $portrait = $item->cover_image ?? null; // We're using portrait as cover image

        // Handle portrait image upload
        if ($request->hasFile('cover_image')) {
            $maxPortraitSize = $request->cover_image->getSize() / 3000000;

            if ($maxPortraitSize > 3) {
                throw ValidationException::withMessages(['cover_image' => 'Cover image size cannot be greater than 3MB']);
            }

            try {
                $date = date('Y') . '/' . date('m') . '/' . date('d');
                // Remove old image on update
                if ($type == 'update') {
                    Storage::delete(getFilePath('item_portrait') . $portrait);
                }
                // Store new image
                $portrait = $date . '/' . fileUploader($request->cover_image, getFilePath('item_portrait') . $date);
            } catch (\Exception $e) {
                throw ValidationException::withMessages(['cover_image' => 'Cover image could not be uploaded']);
            }
        }

        return [
            'portrait' => $portrait, // Return portrait as cover image
        ];
    }


    // Show the form to add an item to the playlist
    public function addItem($type, $id)
    {
        // Validate the type of playlist (audio or video)
        if (!in_array($type, ['audio', 'video'])) {
            return redirect()->back()->with('error', 'Invalid playlist type.');
        }

        // Find the item by its ID
        $item = Item::find($id);

        if (!$item) {
            return redirect()->back()->with('error', 'Item not found.');
        }

        // Find the playlist of the given type
        $playlists = Playlist::where('type', $type)->get();

        $pageTitle = 'Add Item to ' . ucfirst($type) . ' Playlist';

        // Fetch all items that are already in the playlist
        $playlistItems = $playlists->mapWithKeys(function ($playlist) {
            return [$playlist->id => $playlist->items];
        });


        // Render a view to choose which playlist to add the item to
        return view('admin.playlists.add-item', compact('playlists', 'item', 'type', 'pageTitle','playlistItems'));
    }

    // Process adding the item to a playlist
    public function storeItemInPlaylist(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'playlist_id' => 'required|exists:playlists,id',
            'item_id' => 'required|exists:items,id',
        ]);

        // Find the playlist and the item
        $playlist = Playlist::find($request->playlist_id);
        $item = Item::find($request->item_id);

        // Check if the item is already attached to the playlist
        if ($playlist->items()->where('item_id', $item->id)->exists()) {
            return redirect()->back()->with('error', 'This item is already in the playlist.');
        }

        // Attach the item to the playlist (using the pivot table)
        $playlist->items()->attach($item->id);

        return redirect()->route('admin.playlist.index')->with('success', 'Item added to the playlist successfully.');
    }

}
