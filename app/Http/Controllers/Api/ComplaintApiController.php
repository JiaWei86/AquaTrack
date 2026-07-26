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
                'status'    => 'E',                          // Error
                'data'      => null,
                'message'   => 'Missing requestID or timeStamp.',
                'timeStamp' => $timestamp,
            ], 400);
        }

        // Check the water source exists
        $waterSource = WaterSource::find($id);

        if (!$waterSource) {
            return response()->json([
                'status'    => 'F',                          // Fail
                'data'      => null,
                'message'   => 'Water source not found.',
                'timeStamp' => $timestamp,
            ], 404);
        }

        // Gather complaint statistics for this water source
        $base = Complaint::where('water_source_id', $id);

        $data = [
            'waterSourceId'   => $waterSource->id,
            'waterSourceName' => $waterSource->source_name,
            'totalComplaints' => (clone $base)->count(),
            'pending'         => (clone $base)->where('status', 'Pending')->count(),
            'investigating'   => (clone $base)->where('status', 'Investigating')->count(),
            'resolved'        => (clone $base)->where('status', 'Resolved')->count(),
            'rejected'        => (clone $base)->where('status', 'Rejected')->count(),
        ];

        return response()->json([
            'status'    => 'S',                              // Success
            'data'      => $data,
            'message'   => 'Request successful.',
            'timeStamp' => $timestamp,
        ], 200);
    }
}