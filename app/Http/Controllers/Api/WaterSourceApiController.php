<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WaterSource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

/**
 * Web Service provider for Water Source information.
 */
class WaterSourceApiController extends Controller
{
    /**
     * Return one water source in the agreed IFA-style response envelope.
     */
    public function show(Request $request): JsonResponse
    {
        $providedRequestId = $request->input('requestID');
        $requestId = is_string($providedRequestId) && $providedRequestId !== ''
            ? $providedRequestId
            : (string) Str::uuid();
        $timeStamp = now()->toIso8601String();

        $validator = Validator::make([
            'id' => $request->route('id'),
        ], [
            'id' => ['required', 'integer', 'min:1'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'requestID' => $requestId,
                'timeStamp' => $timeStamp,
                'status' => 'E',
                'data' => null,
                'message' => 'The water source ID is invalid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $waterSource = WaterSource::find($validator->validated()['id']);

        if (! $waterSource) {
            return response()->json([
                'requestID' => $requestId,
                'timeStamp' => $timeStamp,
                'status' => 'F',
                'data' => null,
                'message' => 'Water source not found.',
            ], 404);
        }

        return response()->json([
            'requestID' => $requestId,
            'timeStamp' => $timeStamp,
            'status' => 'S',
            'data' => [
                'id' => $waterSource->id,
                'source_name' => $waterSource->source_name,
                'source_type' => $waterSource->source_type,
                'location' => $waterSource->location,
                'latitude' => $waterSource->latitude,
                'longitude' => $waterSource->longitude,
                'notes' => $waterSource->notes,
                'created_at' => $waterSource->created_at?->toIso8601String(),
                'updated_at' => $waterSource->updated_at?->toIso8601String(),
            ],
            'message' => 'Request successful.',
        ], 200);
    }
}
