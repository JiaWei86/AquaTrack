<?php

namespace App\Http\Controllers;

use App\Models\WaterSource;
use App\Http\Requests\StoreWaterSourceRequest;
use App\Http\Requests\UpdateWaterSourceRequest;
use App\Services\Facade\WaterSourceProfileFacade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $waterSource = WaterSource::create($request->validated());

        $actor = Auth::user();

        Log::channel('admin_actions')->info('water_source.created', [
            'action'          => 'created',
            'water_source_id' => $waterSource->getKey(),
            'actor_id'        => $actor?->id,
            'actor_name'      => $actor?->name,
            'actor_role'      => $actor?->role,
            'ip_address'      => request()?->ip(),
            'attributes'      => $waterSource->getAttributes(),
        ]);

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

        $summary = (new WaterSourceProfileFacade())->getSummary($waterSource);

        return view('water-sources.show', compact(
            'waterSource',
            'summary',
            'complaintStatistics',
            'complaintStatisticsError'
        ));
    }

    /**
     * Consume Complaint Management's statistics service without allowing
     * network or service failures to prevent the Water Source page loading.
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
     * Display this water source's records for one related type
     * (complaints, quality readings, or alerts) on a page owned entirely
     * by the Water Source module.
     */
    public function summary(WaterSource $waterSource, string $type)
    {
        abort_unless(
            in_array($type, ['complaints', 'quality-readings', 'alerts'], true),
            404
        );

        $facade = new WaterSourceProfileFacade();
        $items = $facade->getItems($waterSource, $type);
        $statusBreakdown = $facade->getStatusBreakdown($items, $type);

        $qualityHistory = $type === 'quality-readings'
            ? $items
                ->whereNotNull('wqi')
                ->sortBy(fn ($item) => $item->sample_date ?? $item->created_at)
                ->values()
                ->map(fn ($item) => [
                    'date' => ($item->sample_date ?? $item->created_at)->format('Y-m-d'),
                    'wqi'  => (float) $item->wqi,
                ])
                ->all()
            : null;

        return view('water-sources.summary', compact(
            'waterSource',
            'type',
            'items',
            'statusBreakdown',
            'qualityHistory'
        ));
    }
        
    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateWaterSourceRequest $request, WaterSource $waterSource)
    {
        // Captured before update() runs: once the model is saved, Eloquent
        // syncs its "original" attributes to the new values, so this is the
        // only point at which getOriginal() still reflects the pre-update row.
        $original = $waterSource->getOriginal();

        $waterSource->update($request->validated());

        $changes = $waterSource->getChanges();
        unset($changes['updated_at']);

        if (! empty($changes)) {
            $before = collect($original)->only(array_keys($changes))->all();
            $actor = Auth::user();

            Log::channel('admin_actions')->info('water_source.updated', [
                'action'          => 'updated',
                'water_source_id' => $waterSource->getKey(),
                'actor_id'        => $actor?->id,
                'actor_name'      => $actor?->name,
                'actor_role'      => $actor?->role,
                'ip_address'      => request()?->ip(),
                'before'          => $before,
                'after'           => $changes,
            ]);
        }

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

        $attributes = $waterSource->getAttributes();

        $waterSource->delete();

        $actor = Auth::user();

        Log::channel('admin_actions')->info('water_source.deleted', [
            'action'          => 'deleted',
            'water_source_id' => $waterSource->getKey(),
            'actor_id'        => $actor?->id,
            'actor_name'      => $actor?->name,
            'actor_role'      => $actor?->role,
            'ip_address'      => request()?->ip(),
            'attributes'      => $attributes,
        ]);

        return redirect()
            ->route('water-sources.index')
            ->with('success', 'Water source deleted successfully.');
    }
}