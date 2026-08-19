<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\AppMiembrosDiocesis;
use Illuminate\Support\Str;

class DiocesisController extends Controller
{
    private function validateToken($token) {
        if ($token !== 'gapp') {
            abort(401, 'ERROR: Token inválido');
        }
    }

    public function index($token)
    {
        $this->validateToken($token);
        $diocesis = AppMiembrosDiocesis::with('encargado')->get();
        return response()->json($diocesis, 200);
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
        
        // Los campos multiples pueden venir como array, los convertimos a string separado por coma
        if (isset($data['Lumisial']) && is_array($data['Lumisial'])) {
            $data['Lumisial'] = implode(',', $data['Lumisial']);
        }
        if (isset($data['State']) && is_array($data['State'])) {
            $data['State'] = implode(',', $data['State']);
        }

        if (isset($data['UUID']) && !empty($data['UUID'])) {
            $diocesis = AppMiembrosDiocesis::where('UUID', $data['UUID'])->first();
            if ($diocesis) {
                $diocesis->update($data);
                return response()->json($diocesis, 200);
            }
        }
        
        // Si no existe, crear uno nuevo
        if (!isset($data['UUID']) || empty($data['UUID'])) {
            $data['UUID'] = (string) Str::uuid();
        }
        $diocesis = AppMiembrosDiocesis::create($data);
        
        return response()->json($diocesis, 201);
    }

    public function destroy($id, $token)
    {
        $this->validateToken($token);
        $diocesis = AppMiembrosDiocesis::where('UUID', $id)->first();
        if ($diocesis) {
            $diocesis->delete();
            return response()->json(['message' => 'Eliminado correctamente'], 200);
        }
        return response()->json(['message' => 'No encontrado'], 404);
    }
}
