<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller {
    public function index() {
        $pageTitle  = "All Category";
        $categories = Category::latest()->searchable(['name'])->paginate(getPaginate());
        return view('admin.category.index', compact('pageTitle', 'categories'));
    }

    public function store(Request $request, $id = 0) {
        $request->validate([
            'name' => 'required',
            'type' => 'required',
            'status' => 'required',
            'name_en' => 'required',
        ]);

        if ($id == 0) {
            $category     = new Category();
            $notification = 'Category added successfully.';
        } else {
            $category     = Category::findOrFail($id);
            $notification = 'Category updated successfully';
        }
        $category->type  = $request->type;

        $category->name = $request->name;
        $category->status = $request->status;
        $category->name_en = $request->name_en;
        $category->save();

        $notify[] = ['success', $notification];
        return back()->withNotify($notify);
    }

    public function status($id) {
        return Category::changeStatus($id);
    }
}
