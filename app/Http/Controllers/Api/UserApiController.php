<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UserApiController extends Controller
{
    /** Return all inspector accounts for an authenticated administrator. */
    public function inspectors(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user || ! ($user->isAdministrator() || $user->isInspector())) {
            return $this->forbiddenResponse('Only administrators and inspectors may access inspector information.');
        }

        return $this->successResponse(User::where('role', 'Inspector')->latest()->get());
    }

    /** Return resident and inspector accounts for an authenticated administrator. */
    public function users(Request $request): JsonResponse
    {
        $forbiddenResponse = $this->authorizeAdmin($request);

        if ($forbiddenResponse) {
            return $forbiddenResponse;
        }

        return $this->successResponse(
            User::whereIn('role', ['Resident', 'Inspector'])->latest()->get()
        );
    }

    private function authorizeAdmin(Request $request): ?JsonResponse
    {
        if (! $request->user() || ! $request->user()->isAdministrator()) {
            return $this->forbiddenResponse('Only administrators may access user information.');
        }

        return null;
    }

    private function successResponse(mixed $data): JsonResponse
    {
        return response()->json([
            'requestID' => $this->requestId(request()),
            'timestamp' => now()->toIso8601String(),
            'status' => 'S',
            'data' => $data,
            'message' => 'Request successful.',
        ], 200);
    }

    private function forbiddenResponse(string $message): JsonResponse
    {
        return response()->json([
            'requestID' => $this->requestId(request()),
            'timestamp' => now()->toIso8601String(),
            'status' => 'F',
            'data' => null,
            'message' => $message,
        ], 403);
    }

    private function requestId(Request $request): string
    {
        $providedRequestId = $request->input('requestID');

        return is_string($providedRequestId) && $providedRequestId !== ''
            ? $providedRequestId
            : (string) Str::uuid();
    }
}
