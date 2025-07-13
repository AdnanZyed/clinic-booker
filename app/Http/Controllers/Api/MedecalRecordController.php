<?php

namespace App\Http\Controllers\api;

use App\Models\MedicalRecord;
use App\Models\Doctor;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MedecalRecordController extends Controller
{
    public function index()
    {
        $medicalRecords = MedicalRecord::with(['patient', 'doctor.user'])->get();
        return response()->json($medicalRecords, 200);
    }

    public function show($id)
    {
        $medicalRecord = MedicalRecord::find($id);

        if (!$medicalRecord) {
            return response()->json(['message' => 'Medical Record not found'], 404);
        }

        return response()->json($medicalRecord, 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id'   => 'required|exists:users,id',
            'doctor_id'    => 'required|exists:doctors,id',
            'date'         => 'required|date',
            'diagnosis'    => 'required|string',
            'treatment'    => 'required|string',
            'prescription' => 'nullable|string',
        ]);

        $medicalRecord = MedicalRecord::create($validated);
        return response()->json(['message' => 'Medical Record created', 'medicalRecord' => $medicalRecord], 201);
    }

    public function update(Request $request, MedicalRecord $medicalRecord)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:users,id',
            'doctor_id' => 'required|exists:doctors,id',
            'date' => 'required|date',
            'diagnosis' => 'required|string',
            'treatment' => 'nullable|string',
            'prescription' => 'nullable|string',
        ]);
        $medicalRecord = $medicalRecord->update($validated);

        return response()->json(['message' => 'Medical Record updated'], 200);
    }
    
    public function destroy($id)
    {
        $medicalRecord = MedicalRecord::find($id);
        if (!$medicalRecord) return response()->json(['message' => 'Medical Record not found'], 404);

        $medicalRecord->delete();

        return response()->json(['message' => 'Medical Record deleted'], 200);
    }
}
