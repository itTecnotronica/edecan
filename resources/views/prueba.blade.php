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


$MauticController = new MauticController();
$Solicitudes = [32399];

foreach ($Solicitudes as $solicitud_id) {	
	//CON ESTO EJECUTO LA PROGRAMACION DE LA CAMPAÑA DE EMAIL SIN CARGARLA EN LA COLA
	$MauticController->programarCampaniaMautic($solicitud_id);

	//CON ESTO SUMO LA SOLICITUD A LA COLA PARA QUE SE EJECUTE
    //dispatch(new ColaProgramarCampaniaMautic($solicitud_id));

}


/*
$Solicitudes = [18653, 18180, 18717, 18723, 18726, 18728, 18728, 18731, 18731, 18732, 18733, 18733, 18734, 18734, 18736, 18738, 18738, 18737, 18742, 18743, 18744, 18745, 18746, 18747, 18748, 18739, 18739, 18740, 18719, 18719, 18764, 18755, 18754, 18778, 18778, 18779, 18779, 18780, 18780, 18781, 18776];

foreach ($Solicitudes as $solicitud_id) {	
	dispatch(new ColaProgramarCampaniaMautic($solicitud_id));
	echo $solicitud_id.'<br>';
}
echo 'listo';
*/


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

