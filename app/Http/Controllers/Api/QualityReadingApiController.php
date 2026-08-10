<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\QualityReading;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class QualityReadingApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $providedRequestId = $request->input('requestID');
        $requestId = is_string($providedRequestId) && $providedRequestId !== ''
            ? $providedRequestId
            : (string) Str::uuid();

        $qualityReadings = QualityReading::with(['inspector', 'waterSource'])
            ->latest()
            ->get()
            ->map(fn (QualityReading $qualityReading): array => [
                'id' => $qualityReading->id,
                'sample_date' => $qualityReading->sample_date?->toDateString(),
                'water_source' => $qualityReading->waterSource ? [
                    'id' => $qualityReading->waterSource->id,
                    'source_name' => $qualityReading->waterSource->source_name,
                    'source_type' => $qualityReading->waterSource->source_type,
                ] : null,
                'inspector' => $qualityReading->inspector ? [
                    'id' => $qualityReading->inspector->id,
                    'name' => $qualityReading->inspector->name,
                ] : null,
                'assessment_method' => $qualityReading->assessment_method,
                'classification' => $qualityReading->classification,
                'status' => $qualityReading->status,
                'created_at' => $qualityReading->created_at?->toIso8601String(),
            ]);

        return response()->json([
            'requestID' => $requestId,
            'timestamp' => now()->toIso8601String(),
            'status' => 'S',
            'data' => $qualityReadings,
            'message' => 'Request successful.',
        ], 200);
    }

    public function show(int $id): JsonResponse
    {
        $providedRequestId = request()->input('requestID');
        $requestId = is_string($providedRequestId) && $providedRequestId !== ''
            ? $providedRequestId
            : (string) Str::uuid();

        $qualityReading = QualityReading::with(['inspector', 'waterSource'])->find($id);

        if (! $qualityReading) {
            return response()->json([
                'requestID' => $requestId,
                'timestamp' => now()->toIso8601String(),
                'status' => 'F',
                'data' => null,
                'message' => 'Quality reading not found.',
            ], 404);
        }

        return response()->json([
            'requestID' => $requestId,
            'timestamp' => now()->toIso8601String(),
            'status' => 'S',
            'data' => $qualityReading,
            'message' => 'Request successful.',
        ], 200);
    }

    public function byWaterSource(int $id): JsonResponse
    {
        $providedRequestId = request()->input('requestID');
        $requestId = is_string($providedRequestId) && $providedRequestId !== ''
            ? $providedRequestId
            : (string) Str::uuid();

        $qualityReadings = QualityReading::with(['inspector', 'waterSource'])
            ->where('water_source_id', $id)
            ->latest()
            ->get();

        return response()->json([
            'requestID' => $requestId,
            'timestamp' => now()->toIso8601String(),
            'status' => 'S',
            'data' => $qualityReadings,
            'message' => 'Request successful.',
        ], 200);
    }
}
