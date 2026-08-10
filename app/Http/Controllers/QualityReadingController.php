<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreQualityReadingRequest;
use App\Models\QualityReading;
use App\Models\WaterSource;
use App\Services\AlertService;
use App\Services\WaterQuality\WaterQualityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class QualityReadingController extends Controller
{
    public function __construct(private WaterQualityService $waterQualityService, private AlertService $alertService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $qualityReadings = QualityReading::with(['inspector', 'waterSource'])->get();

        return view('quality_readings.index', compact('qualityReadings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $inspectors = $this->inspectorsFromApi($request);
        $waterSources = WaterSource::all();

        return view('quality_readings.create', compact('inspectors', 'waterSources'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreQualityReadingRequest $request)
    {
        $validated = $request->validated();

        $waterSource = WaterSource::findOrFail($validated['water_source_id']);

        $result = $this->waterQualityService->evaluate(
            $waterSource->source_type,
            $validated
        );

        $data = array_merge($validated, $result);

        $qualityReading = QualityReading::create($data);

        if ($qualityReading->status === QualityReading::STATUS_SAFE) {
            $this->alertService->resolveAlert($qualityReading);
        } else {
            $this->alertService->createOrUpdateAlert($qualityReading);
        }

        return redirect()
            ->route('quality-readings.index')
            ->with('success', 'Quality reading created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(QualityReading $qualityReading)
    {
        return view('quality_readings.show', compact('qualityReading'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, QualityReading $qualityReading)
    {
        $inspectors = $this->inspectorsFromApi($request);
        $waterSources = WaterSource::all();

        return view('quality_readings.edit', compact('qualityReading', 'inspectors', 'waterSources'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // TODO: Implement updating quality readings.
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // TODO: Implement deleting quality readings.
    }

    private function inspectorsFromApi(Request $request)
    {
        $cookieHeader = $request->headers->get('Cookie');

        if (! $cookieHeader) {
            return collect();
        }

        try {
            $response = Http::acceptJson()
                ->withHeaders([
                    'Cookie' => $cookieHeader,
                ])
                ->connectTimeout(2)
                ->timeout(5)
                ->get(rtrim(config('app.url'), '/') . '/api/inspectors');

            if (! $response->successful()) {
                return collect();
            }

            $inspectors = $response->json();

            if (! is_array($inspectors)) {
                return collect();
            }

            return collect($inspectors)
                ->map(fn ($inspector) => (object) [
                    'id' => $inspector['id'] ?? null,
                    'name' => $inspector['name'] ?? 'Unknown Inspector',
                ])
                ->filter(fn ($inspector) => $inspector->id !== null)
                ->values();
        } catch (\Throwable) {
            return collect();
        }
    }
}
