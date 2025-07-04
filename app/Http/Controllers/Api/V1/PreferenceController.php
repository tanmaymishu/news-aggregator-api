<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\V1\PreferenceStoreRequest;
use App\Models\Preference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

final class PreferenceController
{
    /**
     * Fetch the preferences of the currently logged-in user.
     *
     * @response array{
     * data: Preference,
     * message: "Fetched preferences",
     * }
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request)
    {
        return response()->json([
            'data' => Auth::user()->preference,
            'message' => 'Fetched preferences',
        ]);
    }

    /**
     * Update user preferences for custom news feed.
     *
     * @response array{
     * data: Preference,
     * message: "Preferences updated!",
     * }
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(PreferenceStoreRequest $request)
    {
        Cache::forget('preferred_articles:'.\auth()->id());

        $pref = Preference::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'user_id' => Auth::id(),
                'sources' => $request->sources,
                'authors' => $request->authors,
                'categories' => $request->categories,
            ],
        );

        return response()->json(['data' => $pref, 'message' => 'Preferences updated!']);
    }
}
