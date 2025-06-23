<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\User;
use App\Models\Doctor;
use App\Notifications\NewAppointmentNotification;
use App\Notifications\UpdateAppointmentNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->type === 'admin') {
            $appointments = Appointment::with(['patient', 'doctor.user'])->latest()->get();
        } elseif ($user->type === 'doctor') {
            $appointments = Appointment::where('doctor_id', $user->doctor->id)
                ->with(['patient', 'doctor.user'])
                ->latest()->get();
        } elseif ($user->type === 'patient') {
            $appointments = Appointment::where('patient_id', $user->id)
                ->with(['patient', 'doctor.user'])
                ->latest()->get();
        } else {
            return redirect()->route('dashboard')->with('error', __('Unauthorized access.'));
        }

        $patients = User::patients()->get();
        $doctors = Doctor::with('user')->get();

        return view('appointments.index', compact('appointments', 'patients', 'doctors'));
    }

    public function create()
    {
        $user = Auth::user();

        if ($user->type === 'admin') {
            $patients = User::patients()->get();
            $doctors = Doctor::with('user')->get();
        } elseif ($user->type === 'doctor') {
            $patients = User::patients()->get();
            $doctors = collect([$user->doctor]);
        } elseif ($user->type === 'patient') {
            $patients = collect([$user]);
            $doctors = Doctor::with('user')->get();
        } else {
            return redirect()->route('appointments.index')->with('error', __('Unauthorized.'));
        }

        return view('appointments.create', compact('patients', 'doctors'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'patient_id'  => 'required|exists:users,id',
            'doctor_id'   => 'required|exists:doctors,id',
            'date'        => 'required|date',
            'start_time'  => 'required|date_format:H:i',
            'end_time'    => 'required|date_format:H:i|after:start_time',
            'status'      => 'required|in:pending,confirmed,cancelled,completed',
            'notes'       => 'nullable|string',
        ]);

        if ($user->type === 'patient' && $user->id != $validated['patient_id']) {
            return redirect()->route('appointments.index')->with('error', __('You can only book for yourself.'));
        }

        if ($user->type === 'doctor' && $user->doctor->id != $validated['doctor_id']) {
            return redirect()->route('appointments.index')->with('error', __('You can only manage your own appointments.'));
        }

        $appointment = Appointment::create($validated);

        $doctorUser = $appointment->doctor->user;
        $doctorUser->notify(new NewAppointmentNotification($appointment));

        return redirect()->route('appointments.index')->with('success', __('Appointment created successfully.'));
    }

    public function show(Appointment $appointment)
    {
        $user = Auth::user();

        if (
            ($user->type === 'doctor' && $appointment->doctor_id !== $user->doctor->id) ||
            ($user->type === 'patient' && $appointment->patient_id !== $user->id)
        ) {
            return redirect()->route('appointments.index')->with('error', __('You are not allowed to view this appointment.'));
        }

        return view('appointments.show', compact('appointment'));
    }

    public function edit(Appointment $appointment)
    {
        $user = Auth::user();

        if (
            ($user->type === 'doctor' && $appointment->doctor_id !== $user->doctor->id) ||
            ($user->type === 'patient' && $appointment->patient_id !== $user->id)
        ) {
            return redirect()->route('appointments.index')->with('error', __('Unauthorized to edit this appointment.'));
        }

        $patients = $user->type === 'patient'
            ? collect([$user])
            : User::patients()->get();

        $doctors = $user->type === 'doctor'
            ? collect([$user->doctor])
            : Doctor::with('user')->get();

        return view('appointments.edit', compact('appointment', 'patients', 'doctors'));
    }

    public function update(Request $request, Appointment $appointment)
    {
        if ($request->ajax()) {
            $appointment = $appointment->update([
                'status' => $request->status,
            ]);
            return response()->json($request);
        }

        $user = Auth::user();

        if (
            ($user->type === 'doctor' && $appointment->doctor_id !== $user->doctor->id) ||
            ($user->type === 'patient' && $appointment->patient_id !== $user->id)
        ) {
            return redirect()->route('appointments.index')->with('error', __('Unauthorized to update this appointment.'));
        }

        $data = $request->validate([
            'patient_id'  => 'required|exists:users,id',
            'doctor_id'   => 'required|exists:doctors,id',
            'date'        => 'required|date',
            'start_time'  => 'required|date_format:H:i',
            'end_time'    => 'required|date_format:H:i|after:start_time',
            'status'      => 'required|in:pending,confirmed,cancelled,completed',
            'notes'       => 'nullable|string',
        ]);

        if ($user->type === 'doctor' && $user->doctor->id != $data['doctor_id']) {
            return redirect()->route('appointments.index')->with('error', __('Cannot assign appointment to another doctor.'));
        }

        if ($user->type === 'patient' && $user->id != $data['patient_id']) {
            return redirect()->route('appointments.index')->with('error', __('Cannot assign appointment to another patient.'));
        }

        if($appointment->status != $request->status) {
            $type = 'status';
        } else {
            $type = 'all';
        }
        
        $appointment->update($data);

        $patient = $appointment->patient;
        $patient->notify(new UpdateAppointmentNotification($appointment, $type));

        return redirect()->route('appointments.index')->with('success', __('Appointment updated successfully.'));
    }

    public function destroy(Appointment $appointment)
    {
        $user = Auth::user();

        if (
            ($user->type === 'doctor' && $appointment->doctor_id !== $user->doctor->id) ||
            ($user->type === 'patient' && $appointment->patient_id !== $user->id)
        ) {
            return redirect()->route('appointments.index')->with('error', __('Unauthorized to delete this appointment.'));
        }

        $appointment->delete();

        return redirect()->route('appointments.index')->with('success', __('Appointment deleted successfully.'));
    }
}
