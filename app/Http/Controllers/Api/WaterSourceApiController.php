<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WaterSource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use SimpleXMLElement;

/**
 * Web Service provider for Water Source information.
 */
class WaterSourceApiController extends Controller
{
    /**
     * Return all water sources (id, source_name, source_type only) in the agreed IFA-style response envelope.
     */
    public function index(Request $request): JsonResponse
    {
        $providedRequestId = $request->input('requestID');
        $requestId = is_string($providedRequestId) && $providedRequestId !== ''
            ? $providedRequestId
            : (string) Str::uuid();
        $timeStamp = now()->toIso8601String();

        $waterSources = WaterSource::query()
            ->select(['id', 'source_name', 'source_type'])
            ->get()
            ->map(fn (WaterSource $waterSource) => [
                'id' => $waterSource->id,
                'source_name' => $waterSource->source_name,
                'source_type' => $waterSource->source_type,
            ]);

        return response()->json([
            'requestID' => $requestId,
            'timeStamp' => $timeStamp,
            'status' => 'S',
            'data' => $waterSources,
            'message' => 'Request successful.',
        ], 200);
    }

    /**
     * Same data as index() (id, source_name, source_type only), exposed as
     * XML instead of JSON. SimpleXMLElement::addChild() escapes text
     * content automatically, so no manual escaping is required.
     */
    public function indexXml(): Response
    {
        $waterSources = WaterSource::query()
            ->select(['id', 'source_name', 'source_type'])
            ->get();

        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><waterSources></waterSources>');

        foreach ($waterSources as $waterSource) {
            $node = $xml->addChild('waterSource');
            $node->addChild('id', (string) $waterSource->id);
            $node->addChild('sourceName', (string) $waterSource->source_name);
            $node->addChild('sourceType', (string) $waterSource->source_type);
        }

        return response($xml->asXML(), 200)->header('Content-Type', 'application/xml');
    }

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
