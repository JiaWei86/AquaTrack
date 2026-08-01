<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateResidentStatusRequest;
use App\Models\User;
use App\Services\UserFactoryProducer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $this->authorizeAdmin();

        $inspectors = User::where('role', 'Inspector')->get();
        $residents = User::where('role', 'Resident')->get();

        return view('users.index', compact('inspectors', 'residents'));
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

    public function inspectorInfo()
    {
        $this->authorizeAdmin();

        return response()->json(User::where('role', 'Inspector')->get());
    }

    public function userInfo()
    {
        $this->authorizeAdmin();

        return response()->json(User::whereIn('role', ['Resident', 'Inspector'])->get());
    }

    protected function authorizeAdmin(): void
    {
        $user = Auth::user();

        if (!$user || !$user->isAdministrator()) {
            abort(403, 'Only administrators may manage users.');
        }
    }
}
