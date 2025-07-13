<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    // Register new user (public)
    public function register(Request $request)
    {
        // قواعد تحقق المستخدم الأساسية
        $rules = [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'type'     => 'in:admin,doctor,patient',
        ];

        if ($request->type === 'doctor') {
            $rules = array_merge($rules, [
                'specialization'    => 'required|string|max:255',
                'qualifications'    => 'required|string|max:255',
                'available_days'    => 'required|array|min:1',
                'available_days.*'  => 'in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
                'session_duration'  => 'required|integer|min:5',
            ]);
        }

        $validated = $request->validate($rules);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => $validated['password'],
            'type'     => $validated['type'] ?? 'patient',
        ]);

        if ($user->type === 'doctor') {
            Doctor::create([
                'user_id'          => $user->id,
                'specialization'   => $validated['specialization'],
                'qualifications'   => $validated['qualifications'],
                'available_days'   => json_encode($validated['available_days']),
                'session_duration' => $validated['session_duration'],
            ]);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully',
            'user'    => $user,
            'token'   => $token,
        ], 201);
    }

    // Login user and create token (public)
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json(['message' => 'wrong password'], 401);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
        ], 200);
    }

    // Logout user (protected)
    public function logout(Request $request)
    {
        // Revoke current token
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully'], 200);
    }

    // List all users (protected)
    public function index(Request $request)
    {
        if ($request->route()->named('doctors')) {
            $users = Doctor::with('user')->get();
        } elseif ($request->route()->named('patients')) {
            $users = User::patients()->get();
        } else {
            $users = User::all();
        }

        return response()->json($users, 200);
    }

    // Show single user (protected)
    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json($user, 200);
    }

    // Create new user (protected)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json(['message' => 'User created', 'user' => $user], 201);
    }

    // Update user (protected)
    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // القواعد الأساسية
        $rules = [
            'name' => 'sometimes|required|string|max:255',
            'email' => ['sometimes', 'required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'sometimes|nullable|string|min:6|confirmed',
        ];

        if ($user->type === 'doctor') {
            $rules = array_merge($rules, [
                'specialization'    => 'sometimes|required|string|max:255',
                'qualifications'    => 'sometimes|required|string|max:255',
                'available_days'    => 'sometimes|required|array|min:1',
                'available_days.*'  => 'in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
                'session_duration'  => 'sometimes|required|integer|min:5',
            ]);
        }

        $validated = $request->validate($rules);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        if ($user->type === 'doctor') {
            $doctor = $user->doctor;

            if ($doctor) {
                $doctorData = [];
                if ($request->has('specialization')) $doctorData['specialization'] = $validated['specialization'];
                if ($request->has('qualifications')) $doctorData['qualifications'] = $validated['qualifications'];
                if ($request->has('available_days')) $doctorData['available_days'] = json_encode($validated['available_days']);
                if ($request->has('session_duration')) $doctorData['session_duration'] = $validated['session_duration'];

                $doctor->update($doctorData);
            }
        }

        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user->load('doctor')
        ], 200);
    }

    // Delete user (protected)
    public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) return response()->json(['message' => 'User not found'], 404);

        $user->delete();

        return response()->json(['message' => 'User deleted'], 200);
    }
}
