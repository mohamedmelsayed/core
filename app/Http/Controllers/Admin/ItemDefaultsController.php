<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\GeneralSetting;

class ItemDefaultsController extends Controller
{
    public function showForm()
    {
        $generalSetting = GeneralSetting::first();
        $defaults = json_decode($generalSetting->item_template ?? '{}');

        return view('admin.item_defaults', compact('defaults'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'default_description' => 'nullable|string',
            'default_preview_text' => 'nullable|string',
            'default_directors' => 'nullable|array',
            'default_producers' => 'nullable|array',
            'default_genres' => 'nullable|array',
            'default_languages' => 'nullable|array',
            'default_casts' => 'nullable|array',
            'default_tags' => 'nullable|array',
        ]);

        $defaults = [
            'description' => $request->default_description,
            'preview_text' => $request->default_preview_text,
            'directors' => $request->default_directors,
            'producers' => $request->default_producers,
            'genres' => $request->default_genres,
            'languages' => $request->default_languages,
            'casts' => $request->default_casts,
            'tags' => $request->default_tags,
        ];
        $generalSetting = GeneralSetting::first();

      $generalSetting->item_template= json_encode($defaults);
       $generalSetting->update();

        return redirect()->route('admin.item.defaults.form')->with('success', 'Default values saved successfully.');
    }
}
