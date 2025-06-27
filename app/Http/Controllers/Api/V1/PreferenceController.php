<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\PreferenceStoreRequest;
use App\Models\Preference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PreferenceController extends Controller
{
    /**
     * Get the preference of currently logged-in user.
     */
    public function show(Request $request)
    {
        return response()->json([
            'data' => Auth::user()->preference(), // Used for dropdown, small dataset (50-80 entries), skipped pagination
            'message' => 'Fetched all sources',
        ]);
    }

    public function store(PreferenceStoreRequest $request)
    {
        Auth::user()->preference()->updateOrCreate([]);
    }
}
