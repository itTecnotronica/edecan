<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\AppMiembrosLumisial;
use App\AppMiembrosProvincia;
use App\AppMiembrosLumisialDocumento;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

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
                // Si cambiaron datos relevantes para geocodificar y no tiene lat/long manual
                if (empty($data['latitud'])) {
                    $coords = $this->geocode($data);
                    if ($coords) {
                        $data['latitud'] = $coords['lat'];
                        $data['longitud'] = $coords['lon'];
                    }
                }
                
                $lumisial->update($data);
                return response()->json($lumisial, 200);
            }
        }
        
        // Si no existe, crear uno nuevo
        if (!isset($data['uuid']) || empty($data['uuid'])) {
            $data['uuid'] = (string) Str::uuid();
        }
        
        if (empty($data['latitud'])) {
            $coords = $this->geocode($data);
            if ($coords) {
                $data['latitud'] = $coords['lat'];
                $data['longitud'] = $coords['lon'];
            }
        }

        $lumisial = AppMiembrosLumisial::create($data);
        return response()->json($lumisial, 201);
    }
    
    private function geocode($data) {
        $address = $data['address'] ?? '';
        $city = $data['city'] ?? '';
        $provincia = '';
        if (isset($data['stateUuid'])) {
            $provObj = AppMiembrosProvincia::where('uuid', $data['stateUuid'])->first();
            if ($provObj) {
                $provincia = $provObj->name;
            }
        }
        
        $query = trim("$address, $city, $provincia, Argentina", ", ");
        try {
            $url = 'https://nominatim.openstreetmap.org/search?' . http_build_query([
                'q' => $query,
                'format' => 'json',
                'limit' => 1
            ]);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_USERAGENT, 'EdecanApp/1.0');
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            $response = curl_exec($ch);
            curl_close($ch);
            
            if ($response !== false) {
                $results = json_decode($response, true);
                if (is_array($results) && count($results) > 0) {
                    return [
                        'lat' => $results[0]['lat'],
                        'lon' => $results[0]['lon']
                    ];
                }
            }
        } catch (\Exception $e) {
            // Ignorar errores para que no aborte el guardado del Lumisial
        }
        
        return null;
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

    public function updateAllCoordinates()
    {
        // Muchos servidores compartidos bloquean set_time_limit, lo que causa un Error 500 inmediato.
        // En su lugar, procesaremos en lotes de a 20 registros por vez para no superar el max_execution_time de PHP (30segs).
        $lumisiales = AppMiembrosLumisial::whereNull('latitud')->take(20)->get();
        $count = 0;
        
        foreach ($lumisiales as $lumisial) {
            $data = $lumisial->toArray();
            $coords = $this->geocode($data);
            
            if ($coords) {
                $lumisial->latitud = $coords['lat'];
                $lumisial->longitud = $coords['lon'];
                $lumisial->save();
                $count++;
            }
            
            // Pausar 1 segundo para respetar el límite de 1 req/sec de Nominatim
            sleep(1);
        }

        return response()->json(['message' => "Se han actualizado $count lumisiales."], 200);
    }

    public function uploadDocument(Request $request, $lumisial_id, $token)
    {
        $this->validateToken($token);

        if (!$request->hasFile('file')) {
            return response()->json(['error' => 'No file uploaded'], 400);
        }

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $mimeType = $file->getClientMimeType();
        $fileData = file_get_contents($file->getRealPath());

        $documento = AppMiembrosLumisialDocumento::create([
            'lumisial_uuid' => $lumisial_id,
            'file_data' => $fileData,
            'mime_type' => $mimeType,
            'original_name' => $originalName
        ]);

        // Evitar devolver todo el binario en la respuesta
        $documento->file_data = null; 
        $documento->url = url("api/GAPP/LUMISIALES_DOCS/view/{$documento->id}/{$token}");

        return response()->json($documento, 201);
    }

    public function getDocuments($lumisial_id, $token)
    {
        $this->validateToken($token);
        
        // Obtenemos todos menos file_data para no saturar memoria
        $documentos = AppMiembrosLumisialDocumento::select('id', 'lumisial_uuid', 'original_name', 'mime_type', 'created_at')
            ->where('lumisial_uuid', $lumisial_id)->get();
            
        foreach ($documentos as $doc) {
            $doc->url = url("api/GAPP/LUMISIALES_DOCS/view/{$doc->id}/{$token}");
        }

        return response()->json($documentos, 200);
    }

    public function viewDocument($doc_id, $token)
    {
        $this->validateToken($token);
        
        $documento = AppMiembrosLumisialDocumento::find($doc_id);
        if ($documento && $documento->file_data) {
            return response($documento->file_data)
                ->header('Content-Type', $documento->mime_type)
                ->header('Content-Disposition', 'inline; filename="' . $documento->original_name . '"');
        }

        return response()->json(['message' => 'No encontrado'], 404);
    }

    public function deleteDocument($doc_id, $token)
    {
        $this->validateToken($token);
        
        $documento = AppMiembrosLumisialDocumento::find($doc_id);
        if ($documento) {
            $documento->delete();
            return response()->json(['message' => 'Documento eliminado'], 200);
        }

        return response()->json(['message' => 'No encontrado'], 404);
    }

    public function resolveMapsUrl(Request $request, $token)
    {
        $this->validateToken($token);
        $url = $request->input('url');
        
        if (empty($url)) {
            return response()->json(['error' => 'URL vacía'], 400);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_exec($ch);
        $finalUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        curl_close($ch);
        
        // Formato @lat,lon
        if (preg_match('/@(-?\d+\.\d+),(-?\d+\.\d+)/', $finalUrl, $matches)) {
            return response()->json(['lat' => $matches[1], 'lon' => $matches[2]], 200);
        } 
        // Formato query=lat,lon
        else if (preg_match('/query=(-?\d+\.\d+),(-?\d+\.\d+)/', $finalUrl, $matches)) {
            return response()->json(['lat' => $matches[1], 'lon' => $matches[2]], 200);
        }
        // Formato ll=lat,lon
        else if (preg_match('/ll=(-?\d+\.\d+),(-?\d+\.\d+)/', $finalUrl, $matches)) {
            return response()->json(['lat' => $matches[1], 'lon' => $matches[2]], 200);
        }

        return response()->json(['error' => 'No se pudo extraer coordenadas de la URL provista.'], 400);
    }
}
