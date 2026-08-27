<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreQualityReadingRequest;
use App\Http\Requests\UpdateQualityReadingRequest;
use App\Models\QualityReading;
use App\Models\WaterSource;
use App\Services\AlertService;
use App\Services\WaterQuality\WaterQualityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class QualityReadingController extends Controller
{
    /**
     * Columns the index table may be sorted by. 'water_source', 'source_type'
     * and 'inspector' are related-model columns and are sorted in PHP after
     * eager loading; the rest map directly to a quality_readings column
     * and are sorted at the query level.
     */
    private const SORTABLE_COLUMNS = ['id', 'water_source', 'source_type', 'inspector', 'result', 'classification', 'status', 'created_at'];

    public function __construct(private WaterQualityService $waterQualityService, private AlertService $alertService)
    {
    }

    /**
     * Display a listing of the resource, sorted server-side via the
     * `sort`/`direction` query parameters. Defaults to newest first
     * (created_at desc) when neither parameter is supplied.
     */
    public function index(Request $request)
    {
        $requestedSort = $request->query('sort');
        $activeSort = in_array($requestedSort, self::SORTABLE_COLUMNS, true) ? $requestedSort : null;
        $sort = $activeSort ?? 'created_at';
        $direction = $request->query('direction') === 'asc' ? 'asc' : 'desc';

        $user = $request->user();

        $query = QualityReading::with(['inspector', 'waterSource']);

        if ($user->isInspector()) {
            $query->where('inspector_id', $user->id);
        }

        $qualityReadings = match ($sort) {
            'water_source' => $query->get()->sortBy(
                fn (QualityReading $reading) => optional($reading->waterSource)->source_name ?? '',
                SORT_NATURAL | SORT_FLAG_CASE,
                $direction === 'desc'
            )->values(),
            'source_type' => $query->get()->sortBy(
                fn (QualityReading $reading) => optional($reading->waterSource)->source_type ?? '',
                SORT_NATURAL | SORT_FLAG_CASE,
                $direction === 'desc'
            )->values(),
            'inspector' => $query->get()->sortBy(
                fn (QualityReading $reading) => optional($reading->inspector)->name ?? '',
                SORT_NATURAL | SORT_FLAG_CASE,
                $direction === 'desc'
            )->values(),
            'result' => $query->orderBy('wqi', $direction)->get(),
            default => $query->orderBy($sort, $direction)->get(),
        };

        return view('quality_readings.index', compact('qualityReadings', 'sort', 'direction', 'activeSort'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $this->authorizeInspectorOrAdministrator($request);

        $inspectors = $this->inspectorsFromApi($request);
        $waterSources = $this->waterSourcesFromApi();

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

        $redirect = redirect()
            ->route('quality-readings.index')
            ->with('success', 'Quality reading created successfully.');

        if ($qualityReading->status === QualityReading::STATUS_SAFE) {
            $this->alertService->resolveAlert($qualityReading);
        } else {
            $alert = $this->alertService->createOrUpdateAlert($qualityReading);

            if ($alert) {
                $alertMessage = $qualityReading->status === QualityReading::STATUS_CRITICAL
                    ? 'New critical water quality alert detected.'
                    : 'New water quality warning detected.';

                $redirect->with('alert_id', $alert->id)
                    ->with('alert_severity', $alert->severity)
                    ->with('alert_message', $alertMessage);
            }
        }

        return $redirect;
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, QualityReading $qualityReading)
    {
        $this->authorizeOwnReadingForInspector($request, $qualityReading);

        return view('quality_readings.show', compact('qualityReading'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, QualityReading $qualityReading)
    {
        $this->authorizeInspectorOrAdministrator($request);
        $this->authorizeOwnReadingForInspector($request, $qualityReading);

        $inspectors = $this->inspectorsFromApi($request);
        $waterSources = $this->waterSourcesFromApi();

        return view('quality_readings.edit', compact('qualityReading', 'inspectors', 'waterSources'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateQualityReadingRequest $request, QualityReading $qualityReading)
    {
        $this->authorizeOwnReadingForInspector($request, $qualityReading);

        $validated = $request->validated();

        $user = $request->user();

        if ($user->isInspector() && (int) $validated['water_source_id'] !== (int) $qualityReading->water_source_id) {
            abort(403, 'You may not change the water source of an existing quality reading.');
        }

        $waterSource = WaterSource::findOrFail($validated['water_source_id']);

        $result = $this->waterQualityService->evaluate(
            $waterSource->source_type,
            $validated
        );

        $data = array_merge($validated, $result);

        $oldStatus = $qualityReading->status;

        $qualityReading->update($data);

        $newStatus = $qualityReading->status;

        $redirect = redirect()
            ->route('quality-readings.index')
            ->with('success', 'Quality reading updated successfully.');

        if ($newStatus === QualityReading::STATUS_SAFE) {
            $this->alertService->resolveAlert($qualityReading);

            if ($oldStatus !== QualityReading::STATUS_SAFE) {
                $redirect->with('safe_status_message', 'Water quality returned to Safe.');
            }
        } else {
            $alert = $this->alertService->createOrUpdateAlert($qualityReading);

            if ($alert && $oldStatus !== $newStatus) {
                $alertMessage = $newStatus === QualityReading::STATUS_CRITICAL
                    ? 'Water quality status changed to Critical.'
                    : 'Water quality status changed to Warning.';

                $redirect->with('alert_id', $alert->id)
                    ->with('alert_severity', $alert->severity)
                    ->with('alert_message', $alertMessage);
            }
        }

        return $redirect;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $this->authorizeAdministrator($request);

        $qualityReading = QualityReading::findOrFail($id);

        $this->alertService->resolveAlert($qualityReading);

        $qualityReading->delete();

        return redirect()
            ->route('quality-readings.index')
            ->with('success', 'Quality reading deleted successfully.');
    }

    private function authorizeInspectorOrAdministrator(Request $request): void
    {
        $user = $request->user();

        if (! $user || ! ($user->isAdministrator() || $user->isInspector())) {
            abort(403, 'Only administrators and inspectors may access quality reading forms.');
        }
    }

    private function authorizeAdministrator(Request $request): void
    {
        $user = $request->user();

        if (! $user || ! $user->isAdministrator()) {
            abort(403, 'Only administrators may delete quality readings.');
        }
    }

    /**
     * Inspectors may only access a quality reading they are assigned to;
     * Administrators and Residents are unrestricted here. The comparison
     * always uses the existing record's inspector_id and the authenticated
     * user's id, never any inspector_id supplied via request input.
     */
    private function authorizeOwnReadingForInspector(Request $request, QualityReading $qualityReading): void
    {
        $user = $request->user();

        if ($user && $user->isInspector() && (int) $qualityReading->inspector_id !== (int) $user->id) {
            abort(403, 'You may only access your own quality readings.');
        }
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

            $inspectors = $response->json('data');

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

    private function waterSourcesFromApi()
    {
        try {
            $response = Http::acceptJson()
                ->connectTimeout(2)
                ->timeout(5)
                ->get(rtrim(config('app.url'), '/') . '/api/water-sources');

            if (! $response->successful()) {
                return collect();
            }

            $waterSources = $response->json('data');

            if (! is_array($waterSources)) {
                return collect();
            }

            return collect($waterSources)
                ->map(fn ($waterSource) => (object) [
                    'id' => $waterSource['id'] ?? null,
                    'source_name' => $waterSource['source_name'] ?? 'Unknown Water Source',
                    'source_type' => $waterSource['source_type'] ?? null,
                ])
                ->filter(fn ($waterSource) => $waterSource->id !== null)
                ->values();
        } catch (\Throwable) {
            return collect();
        }
    }
}