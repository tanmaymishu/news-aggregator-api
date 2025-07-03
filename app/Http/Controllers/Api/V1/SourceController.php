<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Models\Source;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

final class SourceController
{
    /**
     * List all the sources.
     *
     * @unauthenticated
     *
     * @response array{
     * data: Source[],
     * message: string
     * }
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
