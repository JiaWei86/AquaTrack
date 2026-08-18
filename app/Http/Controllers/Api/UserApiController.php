<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use SimpleXMLElement;

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

    /**
     * Same authorization and records as inspectors(), exposing only id/name
     * as XML instead of JSON. SimpleXMLElement::addChild() escapes text
     * content automatically, so no manual escaping is required.
     */
    public function inspectorsXml(Request $request): Response
    {
        $user = $request->user();

        if (! $user || ! ($user->isAdministrator() || $user->isInspector())) {
            abort(403, 'Only administrators and inspectors may access inspector information.');
        }

        $inspectors = User::where('role', 'Inspector')->get();

        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><inspectors></inspectors>');

        foreach ($inspectors as $inspector) {
            $node = $xml->addChild('inspector');
            $node->addChild('id', (string) $inspector->id);
            $node->addChild('name', (string) $inspector->name);
        }

        return response($xml->asXML(), 200)->header('Content-Type', 'application/xml');
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
