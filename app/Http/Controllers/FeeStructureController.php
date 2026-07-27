<?php
// app/Http/Controllers/FeeStructureController.php

namespace App\Http\Controllers;

use App\Models\FeeStructure;
use App\Models\Classes;
use App\Models\Grade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FeeStructureController extends Controller
{
    public function index(Request $request)
    {
        $query = FeeStructure::with(['class', 'grade'])
            ->active()
            ->when($request->search, function($q) use ($request) {
                $q->where('fee_type', 'LIKE', "%{$request->search}%")
                  ->orWhere('academic_year', 'LIKE', "%{$request->search}%")
                  ->orWhere('description', 'LIKE', "%{$request->search}%");
            })
            ->when($request->class_id, function($q) use ($request) {
                $q->where('class_id', $request->class_id);
            })
            ->when($request->grade_id, function($q) use ($request) {
                $q->where('grade_id', $request->grade_id);
            })
            ->when($request->term, function($q) use ($request) {
                $q->where('term', $request->term);
            })
            ->when($request->academic_year, function($q) use ($request) {
                $q->where('academic_year', $request->academic_year);
            });

        $feeStructures = $query->orderBy('created_at', 'desc')->paginate(15);
        $classes = Classes::all();
        $grades = Grade::all();
        $terms = ['Term 1', 'Term 2', 'Term 3'];
        $academicYears = FeeStructure::distinct('academic_year')
            ->pluck('academic_year')
            ->filter()
            ->values();

        return view('fee_structures.index', compact(
            'feeStructures',
            'classes',
            'grades',
            'terms',
            'academicYears'
        ));
    }

    public function create()
    {
        $feeTypes = ['Tuition', 'Activity', 'Exam', 'Library', 'Sports', 'Transport', 'Boarding', 'Other'];
        $classes = Classes::all();
        $grades = Grade::all();
        $terms = ['Term 1', 'Term 2', 'Term 3'];
        $academicYears = range(date('Y') - 1, date('Y') + 1);

        return view('fee_structures.create', compact(
            'feeTypes',
            'classes',
            'grades',
            'terms',
            'academicYears'
        ));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fee_type' => 'required|string|max:255',
            'class_id' => 'required|exists:classes,id',
            'grade_id' => 'required|exists:grades,id',
            'term' => 'required|string',
            'academic_year' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'is_compulsory' => 'boolean',
            'due_date' => 'nullable|date',
            'status' => 'required|in:active,inactive'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            FeeStructure::create($request->all());
            return redirect()->route('fee-structures.index')
                ->with('success', 'Fee structure created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to create fee structure: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit($id)
    {
        $feeStructure = FeeStructure::findOrFail($id);
        $feeTypes = ['Tuition', 'Activity', 'Exam', 'Library', 'Sports', 'Transport', 'Boarding', 'Other'];
        $classes = Classes::all();
        $grades = Grade::all();
        $terms = ['Term 1', 'Term 2', 'Term 3'];
        $academicYears = range(date('Y') - 1, date('Y') + 1);

        return view('fee_structures.edit', compact(
            'feeStructure',
            'feeTypes',
            'classes',
            'grades',
            'terms',
            'academicYears'
        ));
    }

    public function update(Request $request, $id)
    {
        $feeStructure = FeeStructure::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'fee_type' => 'required|string|max:255',
            'class_id' => 'required|exists:classes,id',
            'grade_id' => 'required|exists:grades,id',
            'term' => 'required|string',
            'academic_year' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'is_compulsory' => 'boolean',
            'due_date' => 'nullable|date',
            'status' => 'required|in:active,inactive'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $feeStructure->update($request->all());
            return redirect()->route('fee-structures.index')
                ->with('success', 'Fee structure updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update fee structure: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $feeStructure = FeeStructure::findOrFail($id);
            $feeStructure->delete();
            return redirect()->route('fee-structures.index')
                ->with('success', 'Fee structure deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete fee structure.');
        }
    }
}