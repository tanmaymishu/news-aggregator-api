<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\PreferenceStoreRequest;
use App\Models\Preference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class PreferenceController extends Controller
{
    /**
     * Fetch the preferences of the currently logged-in user.
     *
     * @param Request $request
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
     * @param PreferenceStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(PreferenceStoreRequest $request)
    {
        Cache::forget('preferred_articles');

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
