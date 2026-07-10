<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\User;
use App\Models\WaterSource;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the dashboard according to the user's role.
     * Residents and Admins see different dashboards (RBAC at view level).
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->isAdministrator()) {
            // Admin dashboard: system-wide statistics
            $stats = [
                'totalComplaints'   => Complaint::count(),
                'pendingComplaints' => Complaint::where('status', 'Pending')->count(),
                'resolvedComplaints'=> Complaint::where('status', 'Resolved')->count(),
                'totalUsers'        => User::count(),
                'totalWaterSources' => WaterSource::count(),
            ];

            $recentComplaints = Complaint::with(['resident', 'waterSource'])
                ->latest()
                ->take(5)
                ->get();

            return view('dashboard.admin', compact('stats', 'recentComplaints'));
        }

        // Resident dashboard: personal statistics only
        $stats = [
            'myTotal'    => Complaint::where('resident_id', $user->id)->count(),
            'myPending'  => Complaint::where('resident_id', $user->id)->where('status', 'Pending')->count(),
            'myResolved' => Complaint::where('resident_id', $user->id)->where('status', 'Resolved')->count(),
        ];

        $myRecent = Complaint::with('waterSource')
            ->where('resident_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.resident', compact('stats', 'myRecent'));
    }
}