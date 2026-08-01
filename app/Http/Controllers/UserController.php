<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateResidentStatusRequest;
use App\Models\User;
use App\Services\UserFactoryProducer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $this->authorizeAdmin();

        $inspectors = User::where('role', 'Inspector')->get();
        $residents = User::where('role', 'Resident')->get();

        return view('users.index', compact('inspectors', 'residents'));
    }

    public function showProfile(User $user)
    {
        $this->authorizeProfile($user);

        return view('profiles.show', compact('user'));
    }

    /** Display the profile for the currently signed-in user. */
    public function profile()
    {
        $user = $this->authorizePersonalProfile();

        return view('profiles.show', compact('user'));
    }

    /** Show the profile editing form for the currently signed-in user. */
    public function editProfile()
    {
        $user = $this->authorizePersonalProfile();

        return view('profiles.edit', [
            'user' => $user,
            'states' => User::malaysianStates(),
        ]);
    }

    /** Update only the currently signed-in user's editable account details. */
    public function updateProfile(Request $request)
    {
        $user = $this->authorizePersonalProfile();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'regex:/^(?:\+?6?01)[02-46-9]\d{7,8}$/'],
            'state' => ['nullable', Rule::in(User::malaysianStates())],
            'current_password' => ['nullable', 'required_with:password', 'current_password'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed', 'regex:/^(?=.*[a-z])(?=.*[A-Z]).+$/'],
        ], [
            'phone.regex' => 'Please enter a valid Malaysian phone number, e.g. 0123456789 or +60123456789.',
            'password.regex' => 'The password must contain at least one uppercase letter and one lowercase letter.',
        ]);

        $attributes = collect($validated)->only(['name', 'email', 'phone', 'state'])->all();

        if (!empty($validated['password'])) {
            $attributes['password'] = $validated['password'];
        }

        $user->update($attributes);

        return redirect()->route('profile')->with('status', 'Profile updated successfully.');
    }

    public function create()
    {
        $this->authorizeAdmin();

        return view('users.create');
    }

    public function store(StoreUserRequest $request)
    {
        $this->authorizeAdmin();

        $attributes = $request->only(['name', 'email', 'phone']);
        $attributes['password'] = Hash::make($request->input('password'));

        $factory = UserFactoryProducer::factory('Inspector');
        $factory->create($attributes);

        return redirect()->route('users.index');
    }

    public function updateStatus(UpdateResidentStatusRequest $request, User $user)
    {
        $this->authorizeAdmin();

        if (!$user->isResident()) {
            abort(403, 'Only resident status may be updated through this endpoint.');
        }

        $user->update($request->only('status'));

        return redirect()->route('users.index')->with('status', 'Resident status updated.');
    }

    public function destroy(User $user)
    {
        $this->authorizeAdmin();

        if (!$user->isInspector()) {
            abort(403, 'Only inspectors may be deleted by administrators.');
        }

        $user->delete();

        return redirect()->route('users.index');
    }

    protected function authorizeAdmin(): void
    {
        $user = Auth::user();

        if (!$user || !$user->isAdministrator()) {
            abort(403, 'Only administrators may manage users.');
        }
    }

    protected function authorizeProfile(User $user): void
    {
        $currentUser = Auth::user();

        if (!$currentUser || (!($currentUser->isAdministrator() || $currentUser->id === $user->id))) {
            abort(403, 'You can only view your own profile or an administrator can view others.');
        }
    }

    /** Allow personal profiles only for residents and inspectors. */
    protected function authorizePersonalProfile(): User
    {
        $user = Auth::user();

        if (!$user || $user->isAdministrator()) {
            abort(403, 'Administrators do not have a personal profile page.');
        }

        return $user;
    }
}
