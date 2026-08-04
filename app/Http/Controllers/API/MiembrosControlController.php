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
}
