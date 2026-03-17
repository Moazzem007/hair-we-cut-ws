<?php

namespace App\Http\Controllers;

use App\Models\AppContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AppContentController extends Controller
{
    public function index()
    {
        $contents = AppContent::orderBy('id', 'desc')->get();
        return view('admin.app_content.index', compact('contents'));
    }

    public function create()
    {
        return view('admin.app_content.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required',
            'title' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = $request->except('image');

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('app_content', 'public');
            $data['image_url'] = Storage::url($imagePath);
        }

        AppContent::create($data);

        return redirect()->route('appcontent.index')->with('success', 'Content created successfully.');
    }

    public function edit($id)
    {
        $content = AppContent::findOrFail($id);
        return view('admin.app_content.edit', compact('content'));
    }

    public function update(Request $request, $id)
    {
        $content = AppContent::findOrFail($id);
        
        $request->validate([
            'type' => 'required',
            'title' => 'required',
        ]);

        $data = $request->except('image');

        if ($request->hasFile('image')) {
            if ($content->image_url) {
                // Delete old image? (Optional, depends on business logic)
            }
            $imagePath = $request->file('image')->store('app_content', 'public');
            $data['image_url'] = Storage::url($imagePath);
        }

        $content->update($data);

        return redirect()->route('appcontent.index')->with('success', 'Content updated successfully.');
    }

    public function destroy($id)
    {
        AppContent::findOrFail($id)->delete();
        return redirect()->route('appcontent.index')->with('success', 'Content deleted successfully.');
    }
}
