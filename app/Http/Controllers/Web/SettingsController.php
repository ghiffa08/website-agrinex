<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /**
     * Display settings page
     * FIX N+1: No eager loading needed for User model (no relations loaded in view)
     */
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->get();
        
        // Treatments data (empty array for now - can be implemented later)
        $treatments = [];
        
        return view('settings.index', compact('users', 'treatments'));
    }

    /**
     * Update user
     */
    public function updateUser(Request $request, $id)
    {
        $validated = $request->validate([
            'username' => 'required|unique:users,username,' . $id,
            'email' => 'required|email|unique:users,email,' . $id,
            'full_name' => 'required',
            'role' => 'required|in:admin,operator,viewer',
            'phone_number' => 'nullable',
            'is_active' => 'boolean',
        ]);

        $user = User::findOrFail($id);
        $user->update($validated);

        return redirect()->route('settings.index')
            ->with('success', 'User updated successfully!');
    }

    /**
     * Create new user
     */
    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            'full_name' => 'required',
            'password' => 'required|min:6',
            'role' => 'required|in:admin,operator,viewer',
            'phone_number' => 'nullable',
        ]);

        $validated['password_hash'] = bcrypt($validated['password']);
        $validated['is_active'] = true;
        unset($validated['password']);

        User::create($validated);

        return redirect()->route('settings.index')
            ->with('success', 'User created successfully!');
    }

    /**
     * Delete user
     */
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        
        // Prevent deleting yourself
        if ($user->id == auth()->id()) {
            return redirect()->route('settings.index')
                ->with('error', 'You cannot delete your own account!');
        }

        $user->delete();

        return redirect()->route('settings.index')
            ->with('success', 'User deleted successfully!');
    }

    /**
     * Toggle user active status
     */
    public function toggleUserStatus($id)
    {
        $user = User::findOrFail($id);
        $user->is_active = !$user->is_active;
        $user->save();

        return redirect()->route('settings.index')
            ->with('success', 'User status updated successfully!');
    }
}
