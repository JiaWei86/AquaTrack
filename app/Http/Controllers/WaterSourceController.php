<?php

namespace App\Http\Controllers;

use App\Models\WaterSource;
use App\Http\Requests\StoreWaterSourceRequest;
use App\Http\Requests\UpdateWaterSourceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WaterSourceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $waterSources = WaterSource::query()
            ->when(request('source_type'), function ($query, $sourceType) {
                $query->where('source_type', $sourceType);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('water-sources.index', compact('waterSources'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        abort_unless(
            auth()->user()?->isAdministrator(),
            403,
            'Only administrators may create water sources.'
        );

        return view('water-sources.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreWaterSourceRequest $request)
    {
        WaterSource::create($request->validated());

        return redirect()
            ->route('water-sources.index')
            ->with('success', 'Water source created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, WaterSource $waterSource)
    {
        [$complaintStatistics, $complaintStatisticsError] = $this->fetchComplaintStatistics(
            $request,
            $waterSource->id
        );

        return view('water-sources.show', compact(
            'waterSource',
            'complaintStatistics',
            'complaintStatisticsError'
        ));
    }

    /**
     * Consume Complaint Management's statistics service without allowing
     * network or service failures to prevent the Water Source page loading.
     *
     * This HTTP interaction is the Web Service integration. It is separate
     * from the Observer Pattern, which reacts to model lifecycle events.
     *
     * @return array{0: ?array, 1: ?string}
     */
    private function fetchComplaintStatistics(Request $request, int $waterSourceId): array
    {
        try {
            $response = Http::acceptJson()
                ->connectTimeout(2)
                ->timeout(5)
                ->get(
                    $request->getSchemeAndHttpHost()
                        . "/api/complaints/water-source/{$waterSourceId}",
                    ['requestID' => (string) Str::uuid()]
                );

            $payload = $response->json();

            if (
                ! $response->successful()
                || ! is_array($payload)
                || ($payload['status'] ?? null) !== 'S'
                || ! isset($payload['data'])
                || ! is_array($payload['data'])
            ) {
                Log::warning('water_source.complaint_statistics_unavailable', [
                    'water_source_id' => $waterSourceId,
                    'http_status' => $response->status(),
                    'service_status' => is_array($payload)
                        ? ($payload['status'] ?? null)
                        : null,
                ]);

                return [null, 'Complaint statistics are currently unavailable.'];
            }

            return [$payload['data'], null];
        } catch (\Throwable $exception) {
            Log::warning('water_source.complaint_statistics_request_failed', [
                'water_source_id' => $waterSourceId,
                'exception' => $exception->getMessage(),
            ]);

            return [null, 'Complaint statistics are currently unavailable.'];
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(WaterSource $waterSource)
    {
        abort_unless(
            auth()->user()?->isAdministrator(),
            403,
            'Only administrators may edit water sources.'
        );

        return view('water-sources.edit', compact('waterSource'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateWaterSourceRequest $request, WaterSource $waterSource)
    {
        $waterSource->update($request->validated());

        return redirect()
            ->route('water-sources.index')
            ->with('success', 'Water source updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WaterSource $waterSource)
    {
        abort_unless(
            auth()->user()?->isAdministrator(),
            403,
            'Only administrators may delete water sources.'
        );

        $waterSource->delete();

        return redirect()
            ->route('water-sources.index')
            ->with('success', 'Water source deleted successfully.');
    }
}
