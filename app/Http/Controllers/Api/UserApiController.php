<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserApiController extends Controller
{
    /** Return all inspector accounts for an authenticated administrator. */
    public function inspectors(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user || ! ($user->isAdministrator() || $user->isInspector())) {
            abort(403, 'Only administrators and inspectors may access inspector information.');
        }

        return response()->json(User::where('role', 'Inspector')->get());
    }

    /** Return resident and inspector accounts for an authenticated administrator. */
    public function users(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);

        return response()->json(User::whereIn('role', ['Resident', 'Inspector'])->get());
    }

    private function authorizeAdmin(Request $request): void
    {
        if (!$request->user() || !$request->user()->isAdministrator()) {
            abort(403, 'Only administrators may access user information.');
        }
    }
}
