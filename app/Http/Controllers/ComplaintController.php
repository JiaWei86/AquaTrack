<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreComplaintRequest;
use App\Http\Requests\UpdateComplaintRequest;
use App\Services\WaterSourceApiClient;
use App\Models\Complaint;
use App\Models\WaterSource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ComplaintController extends Controller
{
    /**
     * Display a listing of complaints.
     * Residents only see their own complaints; Admin / Inspector see all.
     */
    public function index()
    {
        $user = Auth::user();

        $complaints = $user->isResident()
            ? Complaint::with('waterSource')
                ->where('resident_id', $user->id)   // Authorization: residents can only query their own records
                ->latest()
                ->paginate(10)
            : Complaint::with(['resident', 'waterSource'])->latest()->paginate(10);

        return view('complaints.index', compact('complaints'));
    }

    /**
     * Show the form for creating a new complaint.
     * Only residents are allowed to submit complaints.
     */
    public function create()
    {
        // Authorization (RBAC): only residents can submit complaints
        if (!Auth::user()->isResident()) {
            abort(403, 'Only residents can submit complaints.');
        }

        $waterSources = WaterSource::all();

        return view('complaints.create', compact('waterSources'));
    }

    /**
     * Store a newly created complaint in the database.
     */
    public function store(StoreComplaintRequest $request)
    {
        // At this point, StoreComplaintRequest has already performed:
        // - Input Validation (required, max, exists rules)
        // - File Upload Validation (image, mimes, max size)
        // - RBAC check (authorize() verifies the user is a Resident)
        $validated = $request->validated();

        if ($request->hasFile('photo')) {
            // store() generates a random filename automatically,
            // preventing filename injection and path traversal attacks
            $validated['photo'] = $request->file('photo')->store('complaints', 'public');
        }

        // resident_id is assigned on the server side;
        // never trust the value submitted from the client
        $validated['resident_id'] = Auth::id();
        $validated['status'] = 'Pending';

        Complaint::create($validated);

        return redirect()->route('complaints.index')
            ->with('success', 'Complaint submitted successfully.');
    }

    /**
     * Display the specified complaint.
     */
    public function show(Complaint $complaint)
    {
        $user = Auth::user();

        // Authorization: prevent IDOR
        if ($user->isResident() && $complaint->resident_id !== $user->id) {
            abort(403, 'You can only view your own complaints.');
        }

        $complaint->load(['resident', 'waterSource']);

        // Web Service (Consumer): fetch live water source details via API
        $apiClient = new WaterSourceApiClient();
        $waterSourceApiData = $apiClient->getWaterSource($complaint->water_source_id);

        return view('complaints.show', compact('complaint', 'waterSourceApiData'));
    }

    /**
     * Show the form for editing the complaint status.
     * Only administrators are allowed to update complaint status.
     */
    public function edit(Complaint $complaint)
    {
        if (!Auth::user()->isAdministrator()) {
            abort(403, 'Only administrators can update complaint status.');
        }

        // State Pattern: only show transitions the current state allows
        $allowedStatuses = $complaint->state()->allowedTransitions();

        return view('complaints.edit', compact('complaint', 'allowedStatuses'));
    }

    /**
     * Update the specified complaint status.
     */
    public function update(UpdateComplaintRequest $request, Complaint $complaint)
    {
        $newStatus = $request->validated()['status'];

        // State Pattern: the current state decides whether the transition is legal
        if (!$complaint->transitionTo($newStatus)) {
            return back()->withErrors([
                'status' => "A {$complaint->status} complaint cannot be changed to {$newStatus}.",
            ]);
        }

        return redirect()->route('complaints.index')
            ->with('success', 'Complaint status updated.');
    }

    /**
     * Remove the specified complaint from the database.
     */
    public function destroy(Complaint $complaint)
    {
        // State Pattern: the current state decides who may delete the complaint
        if (!$complaint->canBeDeletedBy(Auth::user())) {
            abort(403, 'This complaint can no longer be deleted.');
        }

        // Delete the associated photo file together with the complaint
        if ($complaint->photo) {
            Storage::disk('public')->delete($complaint->photo);
        }

        $complaint->delete();

        return redirect()->route('complaints.index')
            ->with('success', 'Complaint deleted.');
    }
}