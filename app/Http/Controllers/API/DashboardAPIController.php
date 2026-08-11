<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardAPIController extends Controller
{
    /**
     * Retorna los totales para el dashboard.
     */
    public function getTotals($token)
    {
        // Miembros
        $totalMiembros = DB::table('app_miembros')->where('sino_isActive', 'SI')->count();
        $totalMujeres = DB::table('app_miembros')->where('sino_isActive', 'SI')->where('gender', 'W')->count();
        $totalHombres = DB::table('app_miembros')->where('sino_isActive', 'SI')->where('gender', 'M')->count();
        $totalInstructores = DB::table('app_miembros')->where('sino_isActive', 'SI')->where('sino_isInstructor', 'SI')->count();
        $totalMisioneros = DB::table('app_miembros')->where('sino_isActive', 'SI')->where('sino_isMissionary', 'SI')->count();
        $totalUngidos = DB::table('app_miembros')->where('sino_isActive', 'SI')->where('sino_isPriest', 'SI')->count();

        // Carnets
        $carnetsIniciados = DB::table('app_carnets')->where('estado', 1)->count();
        $carnetsPagados = DB::table('app_carnets')->where('estado', 2)->count();
        $carnetsConfeccionados = DB::table('app_carnets')->where('estado', 3)->count();
        $carnetsEnviados = DB::table('app_carnets')->where('estado', 4)->count();

        // Aportes
        $year = 2026;
        $totalAportesPesos = DB::table('app_miembros_aportes')->where('ejercicio', $year)->where('moneda', 'P')->sum('monto');
        $totalAportesDolares = DB::table('app_miembros_aportes')->where('ejercicio', $year)->where('moneda', 'D')->sum('monto');

        $aportesPorLumisial = DB::table('app_miembros_aportes')
            ->select('id_lumisial', DB::raw('COUNT(*) as CantidadAportes'))
            ->where('ejercicio', $year)
            ->groupBy('id_lumisial')
            ->get();

        return response()->json([
            'miembros' => [
                'total' => $totalMiembros,
                'mujeres' => $totalMujeres,
                'hombres' => $totalHombres,
                'instructores' => $totalInstructores,
                'misioneros' => $totalMisioneros,
                'ungidos' => $totalUngidos,
            ],
            'carnets' => [
                'iniciados' => $carnetsIniciados,
                'pagados' => $carnetsPagados,
                'confeccionados' => $carnetsConfeccionados,
                'enviados' => $carnetsEnviados,
            ],
            'aportes' => [
                'ejercicio' => $year,
                'total_pesos' => $totalAportesPesos ?: 0,
                'total_dolares' => $totalAportesDolares ?: 0,
                'por_lumisial' => $aportesPorLumisial,
            ]
        ], 200);
    }
}
