<?php

namespace App\Http\Controllers;

use App\Models\MedicalRecord;
use App\Models\Doctor;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class MedicalRecordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->type === 'admin') {
            $medicalRecords = MedicalRecord::with(['patient', 'doctor.user'])->latest()->get();
        } elseif ($user->type === 'doctor') {
            $doctorId = $user->doctor->id ?? null;

            if (!$doctorId) {
                return redirect()->route('dashboard')->with('error', __('No associated doctor profile.'));
            }

            $medicalRecords = MedicalRecord::where('doctor_id', $doctorId)
                ->with(['patient', 'doctor.user'])
                ->latest()
                ->get();
        } elseif ($user->type === 'patient') {
            $medicalRecords = MedicalRecord::where('patient_id', $user->id)
                ->with(['patient', 'doctor.user'])
                ->latest()
                ->get();
        } else {
            return redirect()->route('dashboard')->with('error', __('Unauthorized access.'));
        }

        return view('medical_records.index', compact('medicalRecords'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();

        if ($user->type === 'admin') {
            $doctors = Doctor::with('user')->get();
        } elseif ($user->type === 'doctor') {
            $doctors = collect([$user->doctor]);
        } else {
            return redirect()->route('medical-records.index')->with('error', __('Unauthorized to create records.'));
        }

        $patients = User::patients()->get();

        return view('medical_records.create', compact('patients', 'doctors'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:users,id',
            'doctor_id' => 'required|exists:doctors,id',
            'date' => 'required|date',
            'diagnosis' => 'required|string',
            'treatment' => 'required|string',
            'prescription' => 'nullable|string',
        ]);

        if (Auth::user()->type === 'doctor' && Auth::user()->doctor->id != $validated['doctor_id']) {
            return redirect()->route('medical-records.index')->with('error', __('You cannot create records for other doctors.'));
        }

        MedicalRecord::create($validated);

        return redirect()->route('medical-records.index')->with('success', __('Medical record created successfully.'));
    }

    /**
     * Display the specified resource.
     */
    public function show(MedicalRecord $medicalRecord)
    {
        $user = Auth::user();

        if (
            ($user->type === 'doctor' && $medicalRecord->doctor_id !== $user->doctor->id) ||
            ($user->type === 'patient' && $medicalRecord->patient_id !== $user->id)
        ) {
            return redirect()->route('medical-records.index')->with('error', __('You are not allowed to view this record.'));
        }

        return view('medical_records.show', compact('medicalRecord'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MedicalRecord $medicalRecord)
    {
        $user = Auth::user();

        if (
            ($user->type === 'doctor' && $medicalRecord->doctor_id !== $user->doctor->id) ||
            $user->type === 'patient'
        ) {
            return redirect()->route('medical-records.index')->with('error', __('Unauthorized to edit this record.'));
        }

        $patients = User::patients()->get();

        $doctors = $user->type === 'doctor'
            ? collect([$user->doctor])
            : Doctor::with('user')->get();

        return view('medical_records.edit', compact('medicalRecord', 'patients', 'doctors'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MedicalRecord $medicalRecord)
    {
        $user = Auth::user();

        if (
            ($user->type === 'doctor' && $medicalRecord->doctor_id !== $user->doctor->id) ||
            $user->type === 'patient'
        ) {
            return redirect()->route('medical-records.index')->with('error', __('Unauthorized to update this record.'));
        }

        $data = $request->validate([
            'patient_id' => 'required|exists:users,id',
            'doctor_id' => 'required|exists:doctors,id',
            'date' => 'required|date',
            'diagnosis' => 'required|string',
            'treatment' => 'nullable|string',
            'prescription' => 'nullable|string',
        ]);

        if ($user->type === 'doctor' && $user->doctor->id != $data['doctor_id']) {
            return redirect()->route('medical-records.index')->with('error', __('Cannot assign record to another doctor.'));
        }

        $medicalRecord->update($data);

        return redirect()->route('medical-records.index')->with('success', __('Record updated successfully.'));
    }

    public function destroy(MedicalRecord $medicalRecord)
    {
        $user = Auth::user();

        if (
            ($user->type === 'doctor' && $medicalRecord->doctor_id !== $user->doctor->id) ||
            $user->type === 'patient'
        ) {
            return redirect()->route('medical-records.index')->with('error', __('Unauthorized to delete this record.'));
        }

        $medicalRecord->delete();

        return redirect()->route('medical-records.index')->with('success', __('Record deleted successfully.'));
    }
}
