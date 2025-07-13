<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index()
    {
        $appoinments = Appointment::with(['patient', 'doctor.user'])->get();
        return response()->json($appoinments, 200);
    }

    public function show($id)
    {
        $appoinment = Appointment::find($id);

        if (!$appoinment) {
            return response()->json(['message' => 'Appoinment not found'], 404);
        }

        return response()->json($appoinment, 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id'  => 'required|exists:users,id',
            'doctor_id'   => 'required|exists:doctors,id',
            'date'        => 'required|date',
            'start_time'  => 'required|date_format:H:i',
            'end_time'    => 'required|date_format:H:i|after:start_time',
            'status'      => 'required|in:pending,confirmed,cancelled,completed',
            'notes'       => 'nullable|string',
        ]);

        $appoinment = Appointment::create($validated);
        return response()->json(['message' => 'Appoinment created', 'appoinment' => $appoinment], 201);
    }

    public function update(Request $request, Appointment $appointment)
    {
        $validated = $request->validate([
            'patient_id'  => 'required|exists:users,id',
            'doctor_id'   => 'required|exists:doctors,id',
            'date'        => 'required|date',
            'start_time'  => 'required|date_format:H:i',
            'end_time'    => 'required|date_format:H:i|after:start_time',
            'status'      => 'required|in:pending,confirmed,cancelled,completed',
            'notes'       => 'nullable|string',
        ]);
        $appointment = $appointment->update($validated);

        return response()->json(['message' => 'Appoinment updated'], 200);
    }

    public function destroy($id)
    {
        $appointment = Appointment::find($id);
        if (!$appointment) return response()->json(['message' => 'Appoinment not found'], 404);

        $appointment->delete();

        return response()->json(['message' => 'Appoinment deleted'], 200);
    }
}
