<?php

namespace App\Http\Controllers;

use App\Models\University;
use App\Services\CostCalculator;

class UniversityController extends Controller
{
    public function index()
    {
        $universities = University::active()
            ->withCount(['programs' => fn ($q) => $q->where('is_active', true)])
            ->orderBy('name')
            ->get();

        return view('universities.index', compact('universities'));
    }

    public function show(University $university, CostCalculator $calculator)
    {
        abort_unless($university->is_active, 404);

        $programs = $university->programs()
            ->active()
            ->with('feeItems')
            ->orderBy('name')
            ->get()
            ->each(fn ($program) => $program->cost = $calculator->calculate($program));

        $facilities = $university->facilities()->orderBy('type')->orderBy('name')->get();

        return view('universities.show', compact('university', 'programs', 'facilities'));
    }
}
