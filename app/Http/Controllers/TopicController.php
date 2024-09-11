<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Topic;
use App\Models\Subject;

class TopicController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $topics = Topic::all();
        return view('topics.index', compact('topics'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $subjects = Subject::all();
        return view('topics.create', compact('subjects'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        request()->validate([
            'name' => 'required',
            'subject_id' => 'required'
        ]);

        $topic = Topic::create(request()->all());

        if ($request->hasFile('photo')) {
            $fileName = "site/" . time() . "_photo.jpg";
        
            $request->file('photo')->storePubliclyAs('public', $fileName);
        
            $topic->photo = $fileName;

            $topic->save();
        }


        session()->flash('success', 'Topic Successfully Created');

        return redirect()->route('topic.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $topic = Topic::with('subject')->find($id);

        $subjects = Subject::all();

        return view('topics.edit', compact('topic', 'subjects'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        request()->validate([
            'name' => 'required',
            'subject_id' => 'required'
        ]);

        $topic =  Topic::find($id);
        
        $topic->update(request()->all());

        if ($request->hasFile('photo')) {
            $fileName = "site/" . time() . "_photo.jpg";
        
            $request->file('photo')->storePubliclyAs('public', $fileName);
        
            $topic->photo = $fileName;

            $topic->save();
        }


        session()->flash('success', 'Topic Successfully Updated');

        return redirect()->route('topic.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Topic::destroy($id);

        session()->flash('success', 'Topic Successfully Deleted');

        return redirect()->route('topic.index');
    }
}
