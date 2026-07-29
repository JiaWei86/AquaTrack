<?php

namespace App\Http\Controllers;

use App\Models\WaterSource;
use App\Http\Requests\StoreWaterSourceRequest;
use App\Http\Requests\UpdateWaterSourceRequest;

class WaterSourceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $waterSources = WaterSource::latest()->paginate(10);

        return view('water-sources.index', compact('waterSources'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
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
    public function show(WaterSource $waterSource)
    {
        return view('water-sources.show', compact('waterSource'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(WaterSource $waterSource)
    {
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
        $waterSource->delete();

        return redirect()
            ->route('water-sources.index')
            ->with('success', 'Water source deleted successfully.');
    }
}