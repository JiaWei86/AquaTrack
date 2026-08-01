<?php

/**
 * Web Service (Consumer): consumes the Water Source Management module's
 * REST API to retrieve water source details.
 */

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class WaterSourceApiClient
{
    private string $baseUrl = 'http://aquatrack.test/api';

    /**
     * Fetch a water source by ID from the Water Source module's API.
     * Returns the 'data' array on success, or null on failure.
     */
    public function getWaterSource(int $id): ?array
    {
        try {
            // IFA requirement: send a unique requestID with every request
            $response = Http::timeout(5)->get("{$this->baseUrl}/water-sources/{$id}", [
                'requestID' => (string) Str::uuid(),
                'timestamp' => now()->toIso8601String(),
            ]);

            $body = $response->json();

            // IFA envelope: only use data when status is 'S'
            if (isset($body['status']) && $body['status'] === 'S') {
                return $body['data'];
            }

            return null;

        } catch (\Exception $e) {
            return null;
        }
    }
}