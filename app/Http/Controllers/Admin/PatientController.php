<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Patient::query()->orderBy('id', 'desc');

            if ($request->filled('patient_code')) {
                $query->where('patient_code', 'like', '%' . $request->patient_code . '%');
            }

            if ($request->filled('full_name')) {
                $query->where('full_name', 'like', '%' . $request->full_name . '%');
            }

            if ($request->filled('phone')) {
                $query->where('phone', 'like', '%' . $request->phone . '%');
            }

            if ($request->filled('gender')) {
                $query->where('gender', $request->gender);
            }

            return DataTables::of($query)
                ->addColumn('action', function ($row) {
                    return view('pages.patients.partials.actions', compact('row'))->render();
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('pages.patients.index');
    }

    public function create()
    {
        return view('pages.patients.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:150'],
            'dob'       => ['required', 'date'],
            'gender'    => ['required', 'in:male,female,other'],
            'phone'     => ['required', 'string', 'max:20'],
            'email'     => ['nullable', 'email', 'max:150'],
            'address'   => ['nullable', 'string'],
        ]);

        $patient = Patient::create([
            ...$validated,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('patients.label', $patient);
    }

    public function label(Patient $patient)
    {
        return view('pages.patients.label', compact('patient'));
    }

    public function scanForm()
    {
        return view('pages.patients.scan');
    }

    public function scan(Request $request)
    {
        $request->validate([
            'patient_code' => ['required', 'string'],
        ]);

        $patient = Patient::where('patient_code', trim($request->patient_code))->first();

        if (! $patient) {
            return back()->withErrors([
                'patient_code' => "No patient found with code: {$request->patient_code}",
            ]);
        }

        return redirect()->route('lab-orders.index', ['patient_id' => $patient->id]);
    }

    public function show(Patient $patient)
    {
        return view('pages.patients.show', compact('patient'));
    }

    public function edit(Patient $patient)
    {
        return view('pages.patients.edit', compact('patient'));
    }

    public function update(Request $request, Patient $patient)
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:150'],
            'dob'       => ['required', 'date'],
            'gender'    => ['required', 'in:male,female,other'],
            'phone'     => ['required', 'string', 'max:20'],
            'email'     => ['nullable', 'email', 'max:150'],
            'address'   => ['nullable', 'string'],
        ]);

        $patient->update($validated);

        return redirect()
            ->route('patients.index')
            ->with('success', "Patient updated successfully ({$patient->patient_code}).");
    }

    public function destroy(Patient $patient)
    {
        $code = $patient->patient_code;
        $patient->delete();

        return redirect()
            ->route('patients.index')
            ->with('success', "Patient deleted successfully ({$code}).");
    }
}