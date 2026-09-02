<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use Illuminate\Http\Request;

class AlertController extends Controller
{
    /**
     * Columns the index table may be sorted by. 'water_source' is a
     * related-model column and is sorted in PHP after eager loading; the
     * rest map directly to an alerts column (COLUMN_MAP) and are sorted
     * at the query level. 'quality_reading' sorts by the alerts table's
     * own quality_reading_id foreign key, so no relation lookup is needed.
     */
    private const SORTABLE_COLUMNS = ['id', 'water_source', 'severity', 'status', 'quality_reading', 'created_at'];

    private const COLUMN_MAP = [
        'id' => 'id',
        'severity' => 'severity',
        'status' => 'status',
        'quality_reading' => 'quality_reading_id',
        'created_at' => 'created_at',
    ];

    /**
     * Display a listing of the resource, sorted server-side via the
     * `sort`/`direction` query parameters. Defaults to newest first
     * (created_at desc) when neither parameter is supplied.
     */
    public function index(Request $request)
    {
        $sort = $request->query('sort', 'created_at');

        if (! in_array($sort, self::SORTABLE_COLUMNS, true)) {
            $sort = 'created_at';
        }

        $direction = $request->query('direction') === 'asc' ? 'asc' : 'desc';

        $query = Alert::with(['waterSource', 'qualityReading']);

        $alerts = match ($sort) {
            'water_source' => $query->get()->sortBy(
                fn (Alert $alert) => optional($alert->waterSource)->source_name ?? '',
                SORT_NATURAL | SORT_FLAG_CASE,
                $direction === 'desc'
            )->values(),
            default => $query->orderBy(self::COLUMN_MAP[$sort], $direction)->get(),
        };

        return view('alerts.index', compact('alerts', 'sort', 'direction'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Alert $alert)
    {
        $alert->load(['waterSource', 'qualityReading']);

        return view('alerts.show', compact('alert'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Alert $alert)
    {
        $user = $request->user();

        if (! $user || ! ($user->isAdministrator() || $user->isInspector())) {
            abort(403, 'Only administrators and inspectors may resolve alerts.');
        }

        $alert->status = 'Resolved';
        $alert->save();

        return redirect()
            ->route('alerts.index')
            ->with('success', 'Alert resolved successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
