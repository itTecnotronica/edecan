<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FxC extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
public function nombre_de_dia($numero) {
        if ($numero == 1) {
            $dia = 'Lunes';
            }
        if ($numero == 2) {
            $dia = 'Martes';
            }
        if ($numero == 3) {
            $dia = 'Miércoles';
            }
        if ($numero == 4) {
            $dia = 'Jueves';
            }
        if ($numero == 5) {
            $dia = 'Viernes';
            }
        if ($numero == 6) {
            $dia = 'Sábado';
            }
        if ($numero == 7) {
            $dia = 'Domingo';
            }          

        return $dia;
    }


    public function nombre_de_mes($numero) {
        if ($numero == 1) {
            $mes = 'Enero';
            }
        if ($numero == 2) {
            $mes = 'Febrero';
            }
        if ($numero == 3) {
            $mes = 'Marzo';
            }
        if ($numero == 4) {
            $mes = 'Abril';
            }
        if ($numero == 5) {
            $mes = 'Mayo';
            }
        if ($numero == 6) {
            $mes = 'Junio';
            }
        if ($numero == 7) {
            $mes = 'Julio';
            }
        if ($numero == 8) {
            $mes = 'Agosto';
            }
        if ($numero == 9) {
            $mes = 'Septiembre';
            }
        if ($numero == 10) {
            $mes = 'Octubre';
            }
        if ($numero == 11) {
            $mes = 'Noviembre';
            }
        if ($numero == 12) {
            $mes = 'Diciembre';
            }
        return $mes;

    }

// FUNCION CONVERSION NUMERO

    public function unidad($numuero){
        switch ($numuero)
        {
            case 9:
            {
                $numu = "NUEVE";
                break;
            }
            case 8:
            {
                $numu = "OCHO";
                break;
            }
            case 7:
            {
                $numu = "SIETE";
                break;
            }       
            case 6:
            {
                $numu = "SEIS";
                break;
            }       
            case 5:
            {
                $numu = "CINCO";
                break;
            }       
            case 4:
            {
                $numu = "CUATRO";
                break;
            }       
            case 3:
            {
                $numu = "TRES";
                break;
            }       
            case 2:
            {
                $numu = "DOS";
                break;
            }       
            case 1:
            {
                $numu = "UN";
                break;
            }       
            case 0:
            {
                $numu = "";
                break;
            }       
        }
        return $numu;   
    }

    public function decena($numdero){
        
            if ($numdero >= 90 && $numdero <= 99)
            {
                $numd = "NOVENTA ";
                if ($numdero > 90)
                    $numd = $numd."Y ".($this->unidad($numdero - 90));
            }
            else if ($numdero >= 80 && $numdero <= 89)
            {
                $numd = "OCHENTA ";
                if ($numdero > 80)
                    $numd = $numd."Y ".($this->unidad($numdero - 80));
            }
            else if ($numdero >= 70 && $numdero <= 79)
            {
                $numd = "SETENTA ";
                if ($numdero > 70)
                    $numd = $numd."Y ".($this->unidad($numdero - 70));
            }
            else if ($numdero >= 60 && $numdero <= 69)
            {
                $numd = "SESENTA ";
                if ($numdero > 60)
                    $numd = $numd."Y ".($this->unidad($numdero - 60));
            }
            else if ($numdero >= 50 && $numdero <= 59)
            {
                $numd = "CINCUENTA ";
                if ($numdero > 50)
                    $numd = $numd."Y ".($this->unidad($numdero - 50));
            }
            else if ($numdero >= 40 && $numdero <= 49)
            {
                $numd = "CUARENTA ";
                if ($numdero > 40)
                    $numd = $numd."Y ".($this->unidad($numdero - 40));
            }
            else if ($numdero >= 30 && $numdero <= 39)
            {
                $numd = "TREINTA ";
                if ($numdero > 30)
                    $numd = $numd."Y ".($this->unidad($numdero - 30));
            }
            else if ($numdero >= 20 && $numdero <= 29)
            {
                if ($numdero == 20)
                    $numd = "VEINTE ";
                else
                    $numd = "VEINTI".($this->unidad($numdero - 20));
            }
            else if ($numdero >= 10 && $numdero <= 19)
            {
                switch ($numdero){
                case 10:
                {
                    $numd = "DIEZ ";
                    break;
                }
                case 11:
                {               
                    $numd = "ONCE ";
                    break;
                }
                case 12:
                {
                    $numd = "DOCE ";
                    break;
                }
                case 13:
                {
                    $numd = "TRECE ";
                    break;
                }
                case 14:
                {
                    $numd = "CATORCE ";
                    break;
                }
                case 15:
                {
                    $numd = "QUINCE ";
                    break;
                }
                case 16:
                {
                    $numd = "DIECISEIS ";
                    break;
                }
                case 17:
                {
                    $numd = "DIECISIETE ";
                    break;
                }
                case 18:
                {
                    $numd = "DIECIOCHO ";
                    break;
                }
                case 19:
                {
                    $numd = "DIECINUEVE ";
                    break;
                }
                }   
            }
            else
                $numd = $this->unidad($numdero);
        return $numd;
    }

        public function centena($numc){
            if ($numc >= 100)
            {
                if ($numc >= 900 && $numc <= 999)
                {
                    $numce = "NOVECIENTOS ";
                    if ($numc > 900)
                        $numce = $numce.($this->decena($numc - 900));
                }
                else if ($numc >= 800 && $numc <= 899)
                {
                    $numce = "OCHOCIENTOS ";
                    if ($numc > 800)
                        $numce = $numce.($this->decena($numc - 800));
                }
                else if ($numc >= 700 && $numc <= 799)
                {
                    $numce = "SETECIENTOS ";
                    if ($numc > 700)
                        $numce = $numce.($this->decena($numc - 700));
                }
                else if ($numc >= 600 && $numc <= 699)
                {
                    $numce = "SEISCIENTOS ";
                    if ($numc > 600)
                        $numce = $numce.($this->decena($numc - 600));
                }
                else if ($numc >= 500 && $numc <= 599)
                {
                    $numce = "QUINIENTOS ";
                    if ($numc > 500)
                        $numce = $numce.($this->decena($numc - 500));
                }
                else if ($numc >= 400 && $numc <= 499)
                {
                    $numce = "CUATROCIENTOS ";
                    if ($numc > 400)
                        $numce = $numce.($this->decena($numc - 400));
                }
                else if ($numc >= 300 && $numc <= 399)
                {
                    $numce = "TRESCIENTOS ";
                    if ($numc > 300)
                        $numce = $numce.($this->decena($numc - 300));
                }
                else if ($numc >= 200 && $numc <= 299)
                {
                    $numce = "DOSCIENTOS ";
                    if ($numc > 200)
                        $numce = $numce.($this->decena($numc - 200));
                }
                else if ($numc >= 100 && $numc <= 199)
                {
                    if ($numc == 100)
                        $numce = "CIEN ";
                    else
                        $numce = "CIENTO ".($this->decena($numc - 100));
                }
            }
            else
                $numce = $this->decena($numc);
            
            return $numce;  
    }

    public function miles($nummero){
        if ($nummero >= 1000 && $nummero < 2000){
            $numm = "MIL ".($this->centena($nummero%1000));
        }
        if ($nummero >= 2000 && $nummero <10000){
            $numm = $this->unidad(Floor($nummero/1000))." MIL ".($this->centena($nummero%1000));
        }
        if ($nummero < 1000)
            $numm = $this->centena($nummero);
        
        return $numm;
    }

    public function decmiles($numdmero){
        if ($numdmero == 10000)
            $numde = "DIEZ MIL";
        if ($numdmero > 10000 && $numdmero <20000){
            $numde = $this->decena(Floor($numdmero/1000))."MIL ".($this->centena($numdmero%1000));        
        }
        if ($numdmero >= 20000 && $numdmero <100000){
            $numde = $this->decena(Floor($numdmero/1000))." MIL ".($this->miles($numdmero%1000));     
        }       
        if ($numdmero < 10000)
            $numde = $this->miles($numdmero);
        
        return $numde;
    }       

    public function cienmiles($numcmero){
        if ($numcmero == 100000)
            $num_letracm = "CIEN MIL";
        if ($numcmero >= 100000 && $numcmero <1000000){
            $num_letracm = $this->centena(Floor($numcmero/1000))." MIL ".($this->centena($numcmero%1000));        
        }
        if ($numcmero < 100000)
            $num_letracm = $this->decmiles($numcmero);
        return $num_letracm;
    }   

    public function millon($nummiero){
        if ($nummiero >= 1000000 && $nummiero <2000000){
            $num_letramm = "UN MILLON ".($this->cienmiles($nummiero%1000000));
        }
        if ($nummiero >= 2000000 && $nummiero <10000000){
            $num_letramm = $this->unidad(Floor($nummiero/1000000))." MILLONES ".($this->cienmiles($nummiero%1000000));
        }
        if ($nummiero < 1000000)
            $num_letramm = $this->cienmiles($nummiero);
        
        return $num_letramm;
    }   

    public function decmillon($numerodm){
        if ($numerodm == 10000000)
            $num_letradmm = "DIEZ MILLONES";
        if ($numerodm > 10000000 && $numerodm <20000000){
            $num_letradmm = $this->decena(Floor($numerodm/1000000))."MILLONES ".($this->cienmiles($numerodm%1000000));        
        }
        if ($numerodm >= 20000000 && $numerodm <100000000){
            $num_letradmm = $this->decena(Floor($numerodm/1000000))." MILLONES ".($this->millon($numerodm%1000000));      
        }
        if ($numerodm < 10000000)
            $num_letradmm = $this->millon($numerodm);
        
        return $num_letradmm;
    }

    public function cienmillon($numcmeros){
        if ($numcmeros == 100000000)
            $num_letracms = "CIEN MILLONES";
        if ($numcmeros >= 100000000 && $numcmeros <1000000000){
            $num_letracms = $this->centena(Floor($numcmeros/1000000))." MILLONES ".($this->millon($numcmeros%1000000));       
        }
        if ($numcmeros < 100000000)
            $num_letracms = $this->decmillon($numcmeros);
        return $num_letracms;
    }   

    public function milmillon($nummierod){
        if ($nummierod >= 1000000000 && $nummierod <2000000000){
            $num_letrammd = "MIL ".($this->cienmillon($nummierod%1000000000));
        }
        if ($nummierod >= 2000000000 && $nummierod <10000000000){
            $num_letrammd = $this->unidad(Floor($nummierod/1000000000))." MIL ".($this->cienmillon($nummierod%1000000000));
        }
        if ($nummierod < 1000000000)
            $num_letrammd = $this->cienmillon($nummierod);
        
        return $num_letrammd;
    }   
            
            
    public function functionNumeroALetras($numero){
        $numf = $this->milmillon($numero);
        return $numf;
    }

// FIN FUNCION CONVERSION NUMERO


    public function convertirFechaATexto($fecha, $hora = '', $formato24 = 'S', $cod_iso_idioma = null) {




        $fecha = strtotime($fecha);

        if ($cod_iso_idioma == 'en') {
            $nombre_dia = date("l", $fecha);
        }
        else {
            $numero_dia = date("N", $fecha);
            $nombre_dia = __($this->nombre_de_dia($numero_dia));
        }
        $dia = date("d", $fecha);
        $numero_de_mes = date("m", $fecha);
        $mes = $this->nombre_de_mes($numero_de_mes);
        $mes = __($mes);

        if ($hora <> '') {
            $hora = $this->FormatoHora($hora, $formato24, $cod_iso_idioma);
        }

        $inicio = "$nombre_dia $dia de $mes $hora";

        if ($cod_iso_idioma == 'en') {
            $inicio = "$nombre_dia, $dia $mes $hora";
        }
        if ($cod_iso_idioma == 'fr') {
            $inicio = "$nombre_dia $dia $mes $hora";
        }
        if ($cod_iso_idioma == 'it') {
            $inicio = "$nombre_dia $dia $mes $hora";
        }
        if ($cod_iso_idioma == 'al') {
            $inicio = "$nombre_dia, $dia. $mes $hora";
        } 
        if ($cod_iso_idioma == 'sv') {
            $inicio = "$nombre_dia $mes $dia, $hora";
        }
        if ($cod_iso_idioma == 'hu') {
            $inicio = "$nombre_dia $mes $dia, $hora";
        }
        if ($cod_iso_idioma == 'ar') {
            $inicio = "$hora $nombre_dia $dia $mes";
        }  
        if ($cod_iso_idioma == 'ja') {
            $inicio = $mes.$dia.'日';
        }

        
        return  $inicio;
    }



    public function FormatoHora($hora, $formato24 = 'S', $cod_iso_idioma = null) {

        $hora_time = strtotime($hora);

        if ($formato24 == 'S') {
            if (date("i", $hora_time) == '00') {
                $hora_mostrar = date("G", $hora_time);
            }
            else {
                $hora_mostrar = date("G:i", $hora_time);                
            }

            if ($cod_iso_idioma <> 'it') {
                $hora_mostrar .= 'h';
            }
            
        }
        else {
            $hora_mostrar = date("g:ia", $hora_time);
        }                    
        
        return  $hora_mostrar;
    }


    public function CodificarURL($string) {

        $entities = array('%20');
        $replacements = array('+');
        return str_replace($replacements, $entities, urlencode($string));
    }


    public function limpiarAcentos($cadena) {


        // pedido_de_confirmacion_curso
        $patrones = array();
        $patrones[0] = 'á';
        $patrones[1] = 'é';
        $patrones[2] = 'í';
        $patrones[3] = 'ó';
        $patrones[4] = 'ú';
        $patrones[5] = 'Á';
        $patrones[6] = 'É';
        $patrones[7] = 'Í';
        $patrones[8] = 'Ó';
        $patrones[9] = 'Ú';
        $patrones[10] = 'ñ';
        $patrones[11] = 'Ñ';
        $patrones[12] = 'ü';
        $patrones[13] = 'Ü';

        $sustituciones = array();
        $sustituciones[0] = 'a';
        $sustituciones[1] = 'e';
        $sustituciones[2] = 'i';
        $sustituciones[3] = 'o';
        $sustituciones[4] = 'u';
        $sustituciones[5] = 'A';
        $sustituciones[6] = 'E';
        $sustituciones[7] = 'I';
        $sustituciones[8] = 'O';
        $sustituciones[9] = 'U';
        $sustituciones[10] = 'n';
        $sustituciones[11] = 'N';
        $sustituciones[12] = 'u';
        $sustituciones[13] = 'U';

        $cadena_limpia = str_replace($patrones, $sustituciones, $cadena);

        return $cadena_limpia;
        }

}
