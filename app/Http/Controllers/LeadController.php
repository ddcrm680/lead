<?php

namespace App\Http\Controllers;

use App\Actions\Leads\CreateLead;
use App\Actions\Leads\ListLeads;
use App\Actions\Leads\GetLeadDetails;
use App\Http\Requests\Leads\LeadDataRequest;
use App\Http\Requests\Leads\StoreLeadRequest;
use App\Models\Lead;
use App\Enums\LeadPriority;
use App\Models\LeadSource;
use App\Models\LeadStatus;
use App\Models\Pipeline;
use App\Models\LeadFollowUpStatus;
use App\Models\LeadFollowUpType;
use App\Models\LeadFieldDefinition;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $statuses = LeadStatus::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'name']);

        $sources = LeadSource::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $pipelines = Pipeline::query()
            ->where('is_active', true)
            ->with([
                'stages' => fn ($query) => $query
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->select(['id', 'pipeline_id', 'name']),
            ])
            ->orderBy('sort_order')
            ->get(['id', 'name']);

        $users = User::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $tags = Tag::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $priorities = LeadPriority::cases();

        $followUpTypes = LeadFollowUpType::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'name']);

        $followUpStatuses = LeadFollowUpStatus::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'name']);

        return view(
            'pages.leads.index',
            compact(
                'statuses',
                'sources',
                'pipelines',
                'users',
                'tags',
                'priorities',
                'followUpTypes',
                'followUpStatuses'
            )
        );
    }

    public function data(LeadDataRequest $request, ListLeads $listLeads,): JsonResponse
    {
         $leads = $listLeads->handle(
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'data'    => $leads,
            'html' => view(
                'pages.leads.components.lead-list',
                compact('leads')
            )->render(),
        ]);
    }

    /**
     * Show the form for creating a new lead (rendered dynamically into DOM).
     */
    public function create(): JsonResponse
    {
        $sources = LeadSource::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $statuses = LeadStatus::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'name']);

        $pipelines = Pipeline::query()
            ->where('is_active', true)
            ->with([
                'stages' => fn ($query) => $query
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->select(['id', 'pipeline_id', 'name']),
            ])
            ->orderBy('sort_order')
            ->get(['id', 'name']);

        $users = User::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $tags = Tag::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $fieldDefinitions = LeadFieldDefinition::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get([
                'id',
                'name',
                'key',
                'type',
                'options',
                'validation_rules',
                'is_required',
                'is_filterable',
            ]);

        return response()->json([
            'success' => true,
            'html'    => view('pages.leads.components.add-lead', compact(
                'sources',
                'statuses',
                'pipelines',
                'users',
                'tags',
                'fieldDefinitions'
            ))->render(),
        ]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(
        StoreLeadRequest $request,
        CreateLead $createLead,
    ): JsonResponse {
        $lead = $createLead->handle(
            $request->validated(),
            auth()->id(),
        );

        return response()->json([
            'success' => true,
            'message' => "{$lead->display_name} has been created successfully.",
        ]);
    }


    /**
     * Display the specified lead.
     */
    public function show(
        string $id,
        GetLeadDetails $getLeadDetails,
    ): JsonResponse {
        $lead = $getLeadDetails->handle($id);

        return response()->json([
            'success' => true,
            'html' => view(
                'pages.leads.components.lead-details',
                compact('lead')
            )->render(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
