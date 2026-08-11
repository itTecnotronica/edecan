<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ApiLogController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->input('date'); // ej. 2026-08-03
        $nivel_error = $request->input('nivel_error');

        $query = DB::table('api_logs')
            ->select(
                'nivel_error',
                'url',
                'method',
                'requestHeaders',
                'request_payload',
                'response_payload',
                DB::raw('(created_at - INTERVAL 3 HOUR) AS created_at_local')
            )
            ->where('method', '<>', 'NAVEGACION');

        if ($date) {
            $nextDate = date('Y-m-d', strtotime($date . ' +1 day'));
            
            $query->whereRaw('(created_at - INTERVAL 3 HOUR) >= ?', [$date])
                  ->whereRaw('(created_at - INTERVAL 3 HOUR) < ?', [$nextDate]);
        }

        if ($nivel_error && $nivel_error !== 'todos' && $nivel_error !== 'all') {
            $query->where('nivel_error', $nivel_error);
        }

        $logs = $query->get();

        return response()->json($logs);
    }
}
