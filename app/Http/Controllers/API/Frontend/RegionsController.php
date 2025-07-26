<?php

namespace App\Http\Controllers\API\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Region;
use Illuminate\Http\Request;

class RegionsController extends Controller
{
    public function index(Request $request)
    {
       $governorateId = $request->query('governorate_id');
        $locale = $request->header('Accept-Language', app()->getLocale());
        $regions = Region::where('governorate_id', $governorateId)->get()->map(function ($region) use ($locale){
            return [
                'id' => $region->id,
                'translated_name' => $region->getTranslatedNameAttribute($locale ?? app()->getLocale()),
            ];
        });
        return response()->json($regions);
    }
}
