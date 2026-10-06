<?php

namespace App\Http\Controllers;

use App\Actions\Leads\Import\StartLeadImport;
use App\Actions\Leads\Import\ReviewLeadImport;
use App\Actions\Leads\Import\StoreLeadImport;
use App\Http\Requests\Leads\Import\ParseLeadImportRequest;
use App\Http\Requests\Leads\Import\PrepareLeadImportRequest;
use App\Http\Requests\Leads\Import\StoreLeadImportRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\Request;

class LeadImportController extends Controller
{
    /**
     * Display the Lead Import workflow.
     */
    public function index(): View
    {
        return view('pages.lead-import.index');
    }

    /**
     * Upload, parse and prepare a Lead import.
     */
    public function parse(
        ParseLeadImportRequest $request,
        StartLeadImport $startLeadImport,
    ): JsonResponse {
        $result = $startLeadImport->handle(
            file: $request->file('file'),
            userId: $request->user()->id,
            previousPath: $request->session()->get(
                'lead_import.path'
            ),
        );

        $request->session()->put(
            'lead_import',
            $result['session'],
        );

        $html = view(
            'pages.lead-import.components.mapping',
            $result['view_data'],
        )->render();

        return response()->json([
            'success' => true,
            'message' => 'Lead import file prepared successfully.',
            'html' => $html,
        ]);
    }

    /**
     * Save the selected Lead import defaults.
     */
    public function prepare(
        PrepareLeadImportRequest $request,
    ): JsonResponse {
        $import = $request->session()->get(
            'lead_import'
        );

        if (
            ! is_array($import)
            || empty($import['path'])
            || empty($import['column_plan'])
        ) {
            throw ValidationException::withMessages([
                'file' => [
                    'The Lead import session is no longer available. Please upload the file again.',
                ],
            ]);
        }

        $import['defaults'] =
            $request->validated();

        $request->session()->put(
            'lead_import',
            $import,
        );

        return response()->json([
            'success' => true,
            'message' => 'Lead import defaults saved successfully.',
        ]);
    }


    /**
     * Review the prepared Lead import.
     */
    public function review(
        Request $request,
        ReviewLeadImport $reviewLeadImport,
    ): JsonResponse {
        $import = $request->session()->get(
            'lead_import',
            []
        );

        $review = $reviewLeadImport->handle(
            is_array($import)
                ? $import
                : []
        );

        $html = view(
            'pages.lead-import.components.review',
            [
                'review' => $review,
            ],
        )->render();

        return response()->json([
            'success' => true,
            'message' => 'Lead import reviewed successfully.',
            'html' => $html,
        ]);
    }


    /**
     * Persist the reviewed Lead import.
     */
    public function store(
        StoreLeadImportRequest $request,
        StoreLeadImport $storeLeadImport,
    ): JsonResponse {
        $import = $request->session()->get(
            'lead_import',
            []
        );

        $validated =
            $request->validated();

        $result = $storeLeadImport->handle(
            import: is_array($import)
                ? $import
                : [],
            matchDecisions:
                $validated['match_decisions']
                ?? [],
            createdBy:
                $request->user()->id,
        );

        /*
         * The import has now been consumed.
         *
         * Clear the private temporary file and session
         * so the same import cannot accidentally run twice.
         */
        $path = is_array($import)
            ? ($import['path'] ?? null)
            : null;

        if ($path) {
            Storage::disk('local')->delete(
                $path
            );
        }

        $request->session()->forget(
            'lead_import'
        );

        return response()->json([
            'success' => true,
            'message' => 'Lead import completed.',
            'result' => $result,
        ]);
    }

}
