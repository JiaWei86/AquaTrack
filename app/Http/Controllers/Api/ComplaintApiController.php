<?php

/**
 * Web Service (Provider): exposes complaint statistics for a water source,
 * consumed by the Water Source Management module.
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\WaterSource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ComplaintApiController extends Controller
{
    /**
     * Return complaint statistics for a given water source.
     * GET /api/complaints/water-source/{id}
     */
    public function statsByWaterSource(Request $request, int $id): JsonResponse
    {
        $timestamp = now()->format('Y-m-d H:i:s');

        // IFA requirement: request must include requestID or timeStamp
        if (!$request->has('requestID') && !$request->has('timeStamp')) {
            return response()->json([
                'status'    => 'E',
                'data'      => null,
                'message'   => 'Missing requestID or timeStamp.',
                'timeStamp' => $timestamp,
            ], 400);
        }

        // Check the water source exists
        $waterSource = WaterSource::find($id);

        if (!$waterSource) {
            return response()->json([
                'status'    => 'F',
                'data'      => null,
                'message'   => 'Water source not found.',
                'timeStamp' => $timestamp,
            ], 404);
        }

        $base = Complaint::where('water_source_id', $id);

        // Retrieve the complaint list using select -> get -> map
        $complaints = Complaint::where('water_source_id', $id)
            ->select(['id', 'title', 'status', 'created_at'])   // select: only the needed columns
            ->latest()
            ->get()                                             // get: execute the query
            ->map(fn (Complaint $complaint) => [                // map: transform into API format
                'id'        => $complaint->id,
                'title'     => $complaint->title,
                'status'    => $complaint->status,
                'submitted' => $complaint->created_at->format('Y-m-d'),
            ]);

        $data = [
            'waterSourceId'   => $waterSource->id,
            'waterSourceName' => $waterSource->source_name,
            'totalComplaints' => (clone $base)->count(),
            'pending'         => (clone $base)->where('status', 'Pending')->count(),
            'investigating'   => (clone $base)->where('status', 'Investigating')->count(),
            'resolved'        => (clone $base)->where('status', 'Resolved')->count(),
            'rejected'        => (clone $base)->where('status', 'Rejected')->count(),
            'complaints'      => $complaints,   // ← the list from select/get/map
        ];

        return response()->json([
            'status'    => 'S',
            'data'      => $data,
            'message'   => 'Request successful.',
            'timeStamp' => $timestamp,
        ], 200);
    }
}