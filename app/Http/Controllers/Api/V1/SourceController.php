<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Source;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SourceController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        return response()->json([
            'data' => Cache::remember('sources', now()->addHour(), function () {
                return Source::all();
            }),
            'message' => 'All sources retrieved',
        ]);
    }
}
