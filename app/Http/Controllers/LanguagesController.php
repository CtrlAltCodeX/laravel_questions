<?php

namespace App\Http\Controllers;

use App\Models\Language;
use Illuminate\Http\Request;

class LanguagesController extends Controller
{
    public function index()
    {
        $languages = Language::all();

        return view('languages.index', compact('languages'));
    }

    public function create()
    {
        return view('languages.create');
    }

  

    public function edit(string $id)
    {
        $language = Language::find($id);

        return view('languages.edit', compact('language'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required'
        ]);
    
        $language = Language::create($request->all());
    
        // Return a JSON response
        return response()->json(['success' => true, 'message' => 'Successfully Created', 'language' => $language]);
    }
    
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required'
        ]);
        
        $language = Language::findOrFail($id);
        $language->update($request->all());
    
        // Return a JSON response
        return response()->json(['success' => true, 'message' => 'Successfully Updated', 'language' => $language]);
    }
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Language::find($id)
            ->delete();

        session()->flash('success', 'Successfully Deleted');

        return redirect()->route('languages.index');
    }
}
