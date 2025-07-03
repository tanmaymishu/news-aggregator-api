<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Author;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AuthorController extends Controller
{
    /**
     * List all the authors.
     *
     * @unauthenticated
     */
    public function __invoke(Request $request): JsonResponse
    {
        return response()->json([
            'data' => Cache::remember('authors', now()->addHour(), function () {
                return Author::latest()->get();
            }),
            'message' => 'Fetched all authors',
        ]);
    }
}
