<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AiAnalyticsService;
use Illuminate\Http\JsonResponse;

class DashboardApiController extends Controller
{
    public function stats(): JsonResponse
    {
        $metrics = AiAnalyticsService::gatherGroundTruthData('executive_headmaster');

        return response()->json([
            'status' => 'success',
            'data' => $metrics,
        ]);
    }
}
