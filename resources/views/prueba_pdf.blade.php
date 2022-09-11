<?php 
use App\Curso;
use App\Inscripcion;
use App\Modelo_de_mensaje;
use App\Idioma_por_pais;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\MauticController;
use App\jobs\SendReminderEmail;
use App\Campaign_lead;

use App\Jobs\ColaProgramarCampaniaMautic;


use setasign\FpdiProtection\FpdiProtection;



error_reporting(E_ALL);
ini_set('display_errors', 1);
set_time_limit(2);
date_default_timezone_set('UTC');
$start = microtime(true);

$files = [
	env('PATH_PUBLIC_INTERNO').'/testpdf.pdf',
];


$pdf = new FpdiProtection();

$ownerPassword = $pdf->setProtection([FpdiProtection::PERM_PRINT], '123456', null, 3);
var_dump($ownerPassword);

foreach ($files as $file) {
    $pageCount = $pdf->setSourceFile($file);
    for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
        $id = $pdf->importPage($pageNo);
        $size = $pdf->getTemplateSize($id);

        $pdf->AddPage($size['orientation'], $size);
        $pdf->useTemplate($id);

        $pdf->SetFont('arial');
        $pdf->Cell(0, 12, 'A simple text!');
    }
}

$pdf->Output('F', 'simple.pdf');

echo microtime(true) - $start;
echo "<br>";
var_dump(memory_get_usage());
unset($pdf);
var_dump(gc_collect_cycles());
echo "<br>";
var_dump(memory_get_usage());
echo "<br>";
echo filesize('simple.pdf');

// setup the autoload function
//require_once('vendor/autoload.php');
/*
$pdf = new FpdiProtection();
$ownerPassword = $pdf->setProtection(
    FpdiProtection::PERM_PRINT | FpdiProtection::PERM_COPY,
    'the user password',
    'the owner password'
);
*/
/*
use Igaster\LaravelCities\Geo;

        $Paises = Geo::getCountries();

        foreach ($Paises as $Pais) {
        	
            $ciudades = Geo::level(Geo::LEVEL_3)
	->orderBy('population','DESC')
	->get();
            dd($ciudades);
        }


$ciudades = Geo::getCountry('US')
	->level(Geo::LEVEL_3)
	->get();               // Get a Collection of all countries
dd($ciudades);             // Get item by Country code
Geo::findName('Nomos Kerkyras');   // Find item by (ascii) name
Geo::searchNames('york');          // Search item by all alternative names. Case insensitive 
Geo::searchNames('vegas', Geo::getCountry('US'));  // ... and belongs to an item
Geo::getByIds([390903,3175395]);   // Get a Collection of items by Ids
*/




/*
$NotificationController = new NotificationController();
$result = $NotificationController->enviarNotificacion(2, 1, 'test');
dd($result);
*/

//dispatch(new SendReminderEmail());


/*

$Inscripciones = DB::table('solicitudes as s')
->select(DB::Raw('i.mautic_contact_id'))
->join('inscripciones as i', 'i.solicitud_id', '=', 's.id')
->leftjoin('users as u', 's.user_id', '=', 'u.id')
->whereRaw('s.localidad_id = 737777 and mautic_contact_id IS NOT NULL')
->get();

dd($Inscripciones->count());

$Campaign_leads = Campaign_lead::all();
dd($Campaign_leads);
foreach ($Campaign_leads as $lead) {
	echo '<br>'.$lead->lead_id;
}

*/

/*
$MauticController = new MauticController();
$solicitud_id = 33;
$newCamnpaignId = $MauticController->programarCampaniaMautic($solicitud_id);
*/



$Solicitudes = [9134];

foreach ($Solicitudes as $solicitud_id) {	
	//dispatch(new ColaProgramarCampaniaMautic($solicitud_id));
	echo $solicitud_id;
}




//$FC = new FormController();

//$FC->ContactDown(4704, 'pagina', 3, 1, 2, 222);

/*
$MauticController = new MauticController();
$systemsource = 'gnosis-incripcion-whatsapp';
$Inscripcion = Inscripcion::find(121761);
$apellido = null;
$nombre = 'Fernando Madoz';
$celular = '+5493804201747';
$email_correo = 'fernaneedomado1233z@hotmail.com';
$pais_id = null;
$ciudad = 'la rioja';
echo substr('X132', 0);
//$MauticController->guardarContacto($Inscripcion->solicitud, $systemsource, $nombre, $apellido, $celular, $email_correo, $pais_id, $ciudad);
*/


?>

