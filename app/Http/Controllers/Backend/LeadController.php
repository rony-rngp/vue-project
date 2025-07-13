<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class LeadController extends Controller
{
    public function index()
    {
        $batch = request('batch');
        $status = request('status');
        $offset = request('offset');
        $sortField = 'Name';

        $url = "https://api.airtable.com/v0/" . env('AIRTABLE_BASE_ID') . "/" . env('AIRTABLE_LEADS_TABLE');

        $params = [
            'sort[0][field]' => $sortField,
            'sort[0][direction]' => 'asc',
            'pageSize' => 10,
        ];

        if (!empty($offset)) {
            $params['offset'] = $offset;
        }

        $conditions = [];
        if (!empty($batch)) {
            $conditions[] = "{Batch Name} = '{$batch}'";
        }

        //show all
        if (!empty($status)) {
            $conditions[] = "{Status} = '{$status}'";
        }

        //avoid completed
        /*if (!empty($status)) {
            $conditions[] = "{Status} = '{$status}'";
        } else {
            $conditions[] = "NOT({Status} = 'Completed')";
        }*/

        if (count($conditions) === 1) {
            $params['filterByFormula'] = $conditions[0];
        } elseif (count($conditions) > 1) {
            $params['filterByFormula'] = "AND(" . implode(',', $conditions) . ")";
        }

        $response = Http::withToken(env('AIRTABLE_API_KEY'))->get($url, $params);

        if (!$response->successful()) {
            Log::error('Airtable API error: ' . $response->body());

            return Inertia::render('admin/leads/index', [
                'leads' => [],
                'error_msg' => '⚠️ Airtable API error occurred. Please try again later.',
                'filters' => [
                    'batch' => $batch,
                    'status' => $status,
                ],
                'offset' => $offset,
                'nextOffset' => null,
            ]);
        }

        $leads = $response->json()['records'] ?? [];

        if (empty($leads)) {
            Log::info("❗ No leads found. Batch={$batch}, Status={$status}");

            return Inertia::render('admin/leads/index', [
                'leads' => [],
                'message' => '❗ No leads found. Skipping further processing.',
                'filters' => [
                    'batch' => $batch,
                    'status' => $status,
                ],
                'offset' => $offset,
                'nextOffset' => null,
            ]);
        }


        $nextOffset = $response->json()['offset'] ?? null;

        return Inertia::render('admin/leads/index', [
            'leads' => $leads,
            'filters' => [
                'batch' => $batch,
                'status' => $status,
            ],
            'offset' => $offset,
            'nextOffset' => $nextOffset,
        ]);

        /*$leads = $response->json()['records'] ?? [];
        $nextOffset = $response->json()['offset'] ?? null;

        return Inertia::render('admin/leads/index', [
            'leads' => $leads,
            'filters' => [
                'batch' => $batch,
                'status' => $status,
            ],
            'offset' => $offset,
            'nextOffset' => $nextOffset,
        ]);*/
    }
}
