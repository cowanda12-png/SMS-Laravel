<?php
// app/Http/Controllers/FeeStructureController.php

namespace App\Http\Controllers;

use App\Models\FeeStructure;
use App\Models\Fee;
use App\Models\Classes;
use App\Models\Grade;
use App\Models\Students;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FeeStructureController extends Controller
{
    public function index(Request $request)
    {
        $query = FeeStructure::with(['class', 'grade'])
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
        // Redirect to fee create page since fee structures are managed through fees
        return redirect()->route('fees.create')
            ->with('info', 'Fee structures are managed through the Fee Management system. Please create fees directly.');
    }

    public function store(Request $request)
    {
        // Redirect to fee store route
        return redirect()->route('fees.store')
            ->with('info', 'Please use the Fee Management system to create fees.');
    }

    public function show($id)
    {
        $feeStructure = FeeStructure::with(['class', 'grade'])->findOrFail($id);
        
        // Get related fees for this structure
        $relatedFees = Fee::where('fee_type', $feeStructure->fee_type)
            ->where('term', $feeStructure->term)
            ->where('academic_year', $feeStructure->academic_year)
            ->when($feeStructure->class_id, function($q) use ($feeStructure) {
                return $q->where('class_id', $feeStructure->class_id);
            })
            ->when($feeStructure->grade_id, function($q) use ($feeStructure) {
                return $q->where('grade_id', $feeStructure->grade_id);
            })
            ->get();
        
        return view('fee_structures.show', compact('feeStructure', 'relatedFees'));
    }

    public function edit($id)
    {
        $feeStructure = FeeStructure::findOrFail($id);
        
        // Redirect to fee edit page with the related fee if exists
        $relatedFee = Fee::where('fee_type', $feeStructure->fee_type)
            ->where('term', $feeStructure->term)
            ->where('academic_year', $feeStructure->academic_year)
            ->first();
        
        if ($relatedFee) {
            return redirect()->route('fees.edit', $relatedFee->id)
                ->with('info', 'Edit this fee record instead.');
        }
        
        // If no related fee exists, redirect to fee create with pre-filled data
        return redirect()->route('fees.create')
            ->with('info', 'Please create a fee record instead.')
            ->with('prefill_data', [
                'fee_type' => $feeStructure->fee_type,
                'class_id' => $feeStructure->class_id,
                'grade_id' => $feeStructure->grade_id,
                'term' => $feeStructure->term,
                'academic_year' => $feeStructure->academic_year,
                'amount' => $feeStructure->amount,
                'description' => $feeStructure->description,
                'due_date' => $feeStructure->due_date,
            ]);
    }

    public function update(Request $request, $id)
    {
        // Redirect to fee update instead
        return redirect()->route('fees.index')
            ->with('warning', 'Please update fees through the Fee Management system.');
    }

    public function destroy($id)
    {
        try {
            $feeStructure = FeeStructure::findOrFail($id);
            
            // Check if there are related fees
            $relatedFeesCount = Fee::where('fee_type', $feeStructure->fee_type)
                ->where('term', $feeStructure->term)
                ->where('academic_year', $feeStructure->academic_year)
                ->count();
            
            if ($relatedFeesCount > 0) {
                return redirect()->back()
                    ->with('warning', 'Cannot delete this fee structure because it has related fee records. Delete the fees first.');
            }
            
            $feeStructure->delete();
            
            return redirect()->route('fee-structures.index')
                ->with('success', 'Fee structure deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete fee structure: ' . $e->getMessage());
        }
    }

    // Helper methods
    public function getFeesByClass($classId)
    {
        $fees = FeeStructure::where('class_id', $classId)
            ->active()
            ->get();
        
        return response()->json($fees);
    }

    public function getFeesByGrade($gradeId)
    {
        $fees = FeeStructure::where('grade_id', $gradeId)
            ->active()
            ->get();
        
        return response()->json($fees);
    }

    public function toggleStatus($id)
    {
        try {
            $feeStructure = FeeStructure::findOrFail($id);
            $feeStructure->status = $feeStructure->status === 'active' ? 'inactive' : 'active';
            $feeStructure->save();
            
            return redirect()->back()
                ->with('success', 'Fee structure status updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update fee structure status.');
        }
    }

    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'exists:fee_structures,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Invalid selection'], 422);
        }

        try {
            $feeStructures = FeeStructure::whereIn('id', $request->ids)->get();
            $deletedCount = 0;
            $skippedCount = 0;
            
            foreach ($feeStructures as $feeStructure) {
                // Check if has related fees
                $hasRelatedFees = Fee::where('fee_type', $feeStructure->fee_type)
                    ->where('term', $feeStructure->term)
                    ->where('academic_year', $feeStructure->academic_year)
                    ->exists();
                
                if ($hasRelatedFees) {
                    $skippedCount++;
                } else {
                    $feeStructure->delete();
                    $deletedCount++;
                }
            }
            
            return response()->json([
                'success' => "{$deletedCount} fee structures deleted. {$skippedCount} skipped due to related fee records."
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete fee structures.'], 500);
        }
    }

    // Sync fee structures with fee records
    public function syncWithFees()
    {
        try {
            $fees = Fee::all();
            $created = 0;
            $updated = 0;
            
            foreach ($fees as $fee) {
                $feeStructure = FeeStructure::updateOrCreate(
                    [
                        'fee_type' => $fee->fee_type,
                        'class_id' => $fee->class_id,
                        'grade_id' => $fee->grade_id,
                        'term' => $fee->term,
                        'academic_year' => $fee->academic_year,
                    ],
                    [
                        'amount' => $fee->amount,
                        'description' => $fee->description,
                        'is_compulsory' => $fee->is_compulsory ?? true,
                        'due_date' => $fee->due_date,
                        'status' => $fee->status ?? 'active',
                    ]
                );
                
                if ($feeStructure->wasRecentlyCreated) {
                    $created++;
                } else {
                    $updated++;
                }
            }
            
            return redirect()->route('fee-structures.index')
                ->with('success', "Synced with fees: {$created} created, {$updated} updated.");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to sync with fees: ' . $e->getMessage());
        }
    }
}