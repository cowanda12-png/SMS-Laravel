<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('exams.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('exams.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return redirect()->route('exams.index')->with('success', 'Exam created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return view('exams.show', compact('id'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('exams.edit', compact('id'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        return redirect()->route('exams.index')->with('success', 'Exam updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        return redirect()->route('exams.index')->with('success', 'Exam deleted successfully!');
    }
}