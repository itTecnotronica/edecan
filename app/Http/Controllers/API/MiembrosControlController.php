<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller; 
use App\Miembros;
use App\HistorialIngreso;
use Illuminate\Http\Request;

class MiembrosControlController extends Controller
{
    /**
     * Inactiva a un miembro por faltas (3 meses).
     * Si ya tiene 3 ingresos, se le da de baja definitiva.
     */
    public function inactivar($id)
    {
        $miembro = Miembros::findOrFail($id);

        // Si ya alcanzó el límite de 3 ingresos, la baja es definitiva
        if ($miembro->cantidad_ingresos >= 3) {
            $miembro->sino_isActive = 'no';
            $mensaje = 'Miembro dado de baja definitiva por límite de ingresos alcanzado.';
        } else {
            $miembro->sino_isActive = 'no';
            $mensaje = 'Miembro marcado como inactivo correctamente.';
        }

        $miembro->save();

        return response()->json([
            'status' => 'success',
            'estado_actual' => $miembro->sino_isActive,
            'message' => $mensaje
        ]);
    }

    /**
     * Readmite a un miembro que estaba inactivo.
     * Guarda la fecha del nuevo ingreso.
     */
    public function readmitir(Request $request, $id)
    {
        $miembro = Miembros::findOrFail($id);

        // Validar que no haya excedido el límite o ya sea baja definitiva
        if ($miembro->cantidad_ingresos >= 3 && $miembro->sino_isActive === 'no') {
            return response()->json([
                'status' => 'error',
                'message' => 'Este miembro ya no puede ser readmitido. Alcanzó el límite de 3 ingresos.'
            ], 403);
        }

        $fechaIngreso = $request->input('fecha_ingreso', date('Y-m-d'));

        // Actualizar miembro
        $miembro->sino_isActive = 'si';
        $miembro->cantidad_ingresos += 1;
        $miembro->save();

        // Registrar el ingreso en el historial
        HistorialIngreso::create([
            'miembro_id' => $miembro->id,
            'fecha_ingreso' => $fechaIngreso,
            'tipo_ingreso' => 'readmision',
            'numero_ingreso' => $miembro->cantidad_ingresos,
            'observaciones' => $request->input('observaciones', 'Readmisión automática')
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Miembro readmitido exitosamente (Ingreso #' . $miembro->cantidad_ingresos . ').'
        ]);
    }

    /**
     * Trae velozmente los miembros activos con campos específicos.
     */
    public function listadoRapido($token)
    {
        // Se asume que el token se validará mediante middleware o algún otro mecanismo si fuera necesario.
        $miembros = Miembros::select(
                'app_miembros.id',
                'app_miembros.registration',
                'app_miembros.lumisialUuid as id_lumisial',
                'app_miembros.documentNumber as dni',
                'app_miembros.name as nombre',
                'app_miembros.id as id_federacion',
                'app_miembros.updated_at as update_at',
                'app_miembros.sino_isInstructor',
                'app_miembros.sino_isMissionary',
                'app_miembros.sino_isPriest'
            )
            ->join('app_miembros_lumisial', 'app_miembros_lumisial.uuid', '=', 'app_miembros.lumisialUuid')
            ->whereIn('app_miembros.sino_isActive', ['si', 'SI'])
            ->whereRaw('LOWER(app_miembros_lumisial.status) = ?', ['abierto'])
            ->get();

        return response()->json([
            'status' => 'success',
            'total' => $miembros->count(),
            'data' => $miembros
        ], 200);
    }
}
