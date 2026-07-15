<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\AppMiembrosLumisial;
use App\AppMiembrosProvincia;
use Illuminate\Support\Str;

class LumisialController extends Controller
{
    private function validateToken($token) {
        if ($token !== 'gapp') {
            abort(401, 'ERROR: Token inválido');
        }
    }

    public function index($token)
    {
        $this->validateToken($token);
        $lumisiales = AppMiembrosLumisial::all();
        return response()->json($lumisiales, 200);
    }

    public function storeOrUpdate(Request $request, $token)
    {
        $this->validateToken($token);
        
        $data = $request->all();
        
        foreach ($data as $key => $value) {
            if ($value === 'NULL' || $value === 'null') {
                $data[$key] = null;
            }
        }
        
        if (isset($data['uuid']) && !empty($data['uuid'])) {
            $lumisial = AppMiembrosLumisial::where('uuid', $data['uuid'])->first();
            if ($lumisial) {
                $lumisial->update($data);
                return response()->json($lumisial, 200);
            }
        }
        
        // Si no existe, crear uno nuevo
        if (!isset($data['uuid']) || empty($data['uuid'])) {
            $data['uuid'] = (string) Str::uuid();
        }
        $lumisial = AppMiembrosLumisial::create($data);
        return response()->json($lumisial, 201);
    }

    public function destroy($id, $token)
    {
        $this->validateToken($token);
        $lumisial = AppMiembrosLumisial::where('uuid', $id)->first();
        if ($lumisial) {
            $lumisial->delete();
            return response()->json(['message' => 'Eliminado correctamente'], 200);
        }
        return response()->json(['message' => 'No encontrado'], 404);
    }

    public function getProvincias($token)
    {
        $this->validateToken($token);
        $provincias = AppMiembrosProvincia::all();
        return response()->json($provincias, 200);
    }
}
