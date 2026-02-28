<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

use Yajra\DataTables\Facades\DataTables;


class UserController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $users = User::with('role')->select('users.*');

            // ✅ Custom filters (coming from your DataTables ajax.data)
            if ($request->filled('name')) {
                $users->where('name', 'like', '%' . $request->name . '%');
            }

            if ($request->filled('email')) {
                $users->where('email', 'like', '%' . $request->email . '%');
            }

            // If role is stored as a relation (roles table)
            if ($request->filled('role')) {
                $role = $request->role;

                $users->whereHas('role', function ($q) use ($role) {
                    $q->where('name', $role);
                });
            }

            return DataTables::of($users)
                ->addColumn('role', function ($user) {
                    return $user->role->name ?? '-';
                })
                ->addColumn('action', function ($user) {
                    return view('pages.users.partials.actions', compact('user'))->render();
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('pages.users.index');
    }

    public function create()
    {
        $roles = Role::orderBy('name')->get();
        return view('pages.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role_id' => ['required', 'exists:roles,id'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role_id' => $validated['role_id'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        $roles = Role::orderBy('name')->get();
        return view('pages.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role_id' => ['required', 'exists:roles,id'],
            'password' => ['nullable', 'confirmed', Password::min(8)],
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role_id' => $validated['role_id'],
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        // Prevent admin deleting themselves
        if (auth()->id() === $user->id) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}