<?php

namespace App\Http\Controllers\API;
use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use App\User; 
use App\Pais; 
use Illuminate\Support\Facades\Auth; 
use Validator;

class SeteosController extends Controller 
{
    public $successStatus = 200;
    
    /** 
     * login api 
     * 
     * @return \Illuminate\Http\Response 
     */

    
    public function paises() 
    { 
        $paises = Pais::all(); 
        return response()->json(['success' => $paises], $this-> successStatus); 
    } 
}