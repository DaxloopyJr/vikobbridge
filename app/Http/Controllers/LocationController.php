<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location\Region;
use App\Models\Location\District;
use App\Models\Location\Ward;
use App\Models\Location\Village;

class LocationController extends Controller
{
    /**
     * Get all regions
     */
    public function regions()
    {
        $regions = Region::where('is_deleted', false)
            ->orderBy('region')
            ->get(['id', 'region']);

        return response()->json($regions);
    }

    /**
     * Get districts by region ID
     */
    public function districtsByRegion($regionId)
    {
        $districts = District::where('region_id', $regionId)
            ->orderBy('district')
            ->get(['id', 'district']);

        return response()->json($districts);
    }

    /**
     * Get wards by district ID
     */
    public function wardsByDistrict($districtId)
    {
        $wards = Ward::where('district_id', $districtId)
            ->orderBy('ward')
            ->get(['id', 'ward']);

        return response()->json($wards);
    }

    /**
     * Get villages by ward ID
     */
    public function villagesByWard($wardId)
    {
        $villages = Village::where('ward_id', $wardId)
            ->orderBy('village')
            ->get(['id', 'village']);

        return response()->json($villages);
    }

    /**
     * Search regions by query term
     */
    public function searchRegions(Request $request)
    {
        $query = $request->get('q', '');
        $regions = Region::where('is_deleted', false)
            ->where('region', 'like', "%{$query}%")
            ->orderBy('region')
            ->limit(50)
            ->get(['id', 'region as text']);

        return response()->json(['results' => $regions]);
    }

    /**
     * Search districts by region and query term
     */
    public function searchDistricts(Request $request)
    {
        $query = $request->get('q', '');
        $regionId = $request->get('region_id');

        $districts = District::when($regionId, function ($q) use ($regionId) {
                $q->where('region_id', $regionId);
            })
            ->where('district', 'like', "%{$query}%")
            ->orderBy('district')
            ->limit(50)
            ->get(['id', 'district as text']);

        return response()->json(['results' => $districts]);
    }

    /**
     * Search wards by district and query term
     */
    public function searchWards(Request $request)
    {
        $query = $request->get('q', '');
        $districtId = $request->get('district_id');

        $wards = Ward::when($districtId, function ($q) use ($districtId) {
                $q->where('district_id', $districtId);
            })
            ->where('ward', 'like', "%{$query}%")
            ->orderBy('ward')
            ->limit(50)
            ->get(['id', 'ward as text']);

        return response()->json(['results' => $wards]);
    }

    /**
     * Search villages by ward and query term
     */
    public function searchVillages(Request $request)
    {
        $query = $request->get('q', '');
        $wardId = $request->get('ward_id');

        $villages = Village::when($wardId, function ($q) use ($wardId) {
                $q->where('ward_id', $wardId);
            })
            ->where('village', 'like', "%{$query}%")
            ->orderBy('village')
            ->limit(50)
            ->get(['id', 'village as text']);

        return response()->json(['results' => $villages]);
    }
}
