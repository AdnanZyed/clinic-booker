<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::latest()->get();
        return view('users.index', compact('users'));
    }

    /**
     * Display a listing of the patients related to the logged-in doctor.
     */
    public function patients()
    {
        $user = auth()->user();
        if (!$user->doctor) {
            return redirect()->back()->with('error', __('No doctor profile associated with this account.'));
        }

        $patients = User::whereHas('appointments', function ($query) use ($user) {
            $query->where('doctor_id', $user->doctor->id);
        })->distinct()->get();

        return view('users.patients', compact('patients'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

        return view('users.create', compact('days'));
    }

    public function store(Request $request)
    {
        $baseRules = [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'type' => 'required|in:admin,doctor,patient',
            'password' => 'required|confirmed|min:6',
        ];

        if ($request->type === 'doctor') {
            $baseRules = array_merge($baseRules, [
                'specialization'    => 'required|string|max:255',
                'qualifications'    => 'required|string|max:255',
                'available_days'    => 'required|array|min:1',
                'available_days.*'  => 'in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
                'session_duration'  => 'required|integer|min:5',
            ]);
        }

        $data = $request->validate($baseRules);

        $data['password'] = Hash::make($data['password']);

        DB::beginTransaction();

        try {
            $user = User::create($data);

            if ($user->type === 'doctor') {
                Doctor::create([
                    'user_id'          => $user->id,
                    'specialization'   => $data['specialization'],
                    'qualifications'   => $data['qualifications'],
                    'available_days'   => json_encode($data['available_days']),
                    'session_duration' => $data['session_duration'],
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => __('Something went wrong: ') . $e->getMessage()])->withInput();
        }

        if ($request->ajax()) {
            return response()->json(['user' => $user]);
        }

        return redirect()->route('users.index')->with('success', __('User created successfully.'));
    }


    public function show(User $user)
    {
        $auth = auth()->user();

        if ($auth->type === 'admin') {
            $user->load('doctor');
            return view('users.show', ['user' => $user, 'doctor' => $user->doctor]);
        }

        if ($auth->type === 'doctor') {
            $isMyPatient = $user->appointments()->where('doctor_id', $auth->doctor->id)->exists();

            if (! $isMyPatient) {
                return redirect()->back()->with('error', __('You are not authorized to view this patient.'));
            }

            $user->load('doctor');
            return view('users.show', ['user' => $user, 'doctor' => $user->doctor]);
        }

        return redirect()->route('dashboard')->with('error', __('Unauthorized.'));
    }

    public function edit(User $user)
    {
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

        return view('users.edit', compact('user', 'days'));
    }

    public function update(Request $request, User $user)
    {
        DB::beginTransaction();

        try {
            $baseRules = [
                'name'     => 'required',
                'email'    => 'required|email|unique:users,email,' . $user->id,
                'type'     => 'required|in:admin,doctor,patient',
                'password' => 'nullable|confirmed|min:6',
            ];

            if ($request->type === 'doctor') {
                $baseRules = array_merge($baseRules, [
                    'specialization'   => 'required|string|max:255',
                    'qualifications'   => 'required|string|max:255',
                    'available_days'   => 'required|array|min:1',
                    'available_days.*' => 'in:Sunday,Monday,Tuesday,Wednesday,Thursday,Friday,Saturday',
                    'session_duration' => 'required|integer|min:5',
                ]);
            }

            $data = $request->validate($baseRules);

            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            } else {
                unset($data['password']);
            }

            $user->update($data);

            if ($user->type === 'doctor') {
                $user->doctor()->updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'specialization'   => $data['specialization'],
                        'qualifications'   => $data['qualifications'],
                        'available_days'   => json_encode($data['available_days']),
                        'session_duration' => $data['session_duration'],
                    ]
                );
            } else {
                $user->doctor()->delete();
            }

            DB::commit();

            return redirect()->route('users.index')->with('success', __('User updated successfully.'));
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => __('An error occurred while updating the user: ') . $e->getMessage()]);
        }
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted.');
    }
}
