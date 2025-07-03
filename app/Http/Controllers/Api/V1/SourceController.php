<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Source;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SourceController extends Controller
{
    /**
     * List all the sources.
     *
     * @unauthenticated
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {
        return response()->json([
            'data' => Cache::remember('sources', now()->addHour(), function () {
                return Source::latest()->get();
            }),
            'message' => 'All sources retrieved',
        ]);
    }
}
