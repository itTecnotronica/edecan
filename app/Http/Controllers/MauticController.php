<?php

namespace App\Http\Controllers;
use App\Solicitud;
use App\Fecha_de_evento;
use App\Inscripcion;
use App\Localidad;
use App\Asistencia;
use Auth;
use Session;
use App;
use PDF;
use QrCode;
use URL;
use Storage;
use Filesystem;
use DateTime;
use DateInterval;
use DB;
use App\Http\Controllers\GenericController;
use App\Http\Controllers\ListasController;
use App\Http\Controllers\FxC; 
use App\Http\Controllers\Mautic\MauticApi;
use App\Http\Controllers\Mautic\Auth\AuthInterface;
use App\Http\Controllers\Mautic\Auth\ApiAuth;
use App\Campaign_lead;

//use App\Libraries\mauticApi\lib\MauticApi;

//use Mautic\Auth\ApiAuth;


class MauticController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }


    public function connMautic($elemento) {

        $settings = array(
            'userName'   => 'fmadoz',             // Create a new user       
            'password'   => 'fM@d0Z'              // Make it a secure password
        );

        // Initiate the auth object specifying to use BasicAuth
        $initAuth = new ApiAuth();
        $auth = $initAuth->newAuth($settings, 'BasicAuth');

        $api = new MauticApi();

        $url = 'https://forms.gnosis.is';

        $res = $api->newApi($elemento, $auth, $url);

        return $res;

    }


    public function aprobacionPublished($campania_mautic_id, $sino_is_published) {

        if ($sino_is_published == 'SI') {
            $isPublished = 1;   
            $return_sino_is_published = 'SI';     
        }
        else {
            $isPublished = 0;
            $return_sino_is_published = 'NO';     
        }

        $this->onOffCampaniaMautic($campania_mautic_id, $isPublished);


        return $return_sino_is_published;
    }



    public function onOffCampaniaMautic($campania_mautic_id, $isPublished) {

        $campaignApi = $this->connMautic('campaigns');
        
        $data = array(
            'isPublished' => $isPublished
        );
        // Create new a campaign of ID 1 is not found?
        $createIfNotFound = false;

        $campaign = $campaignApi->edit($campania_mautic_id, $data, $createIfNotFound);        

    }



    public function programarCampaniaMautic($solicitud_id) {

        $Solicitud = Solicitud::find($solicitud_id);
        $newCamnpaignId = null;
        $email_id = null;        

        $localidad_id = $Solicitud->localidad_id;
        $institucion_id = $Solicitud->institucion_id;
        $Idioma_por_pais = $Solicitud->idioma_por_pais();
        $Pais = $Solicitud->pais_de_solicitud();
        $sino_federacion = '';

        $enviar_mailing_pais = false;
        if ($Pais <> null) {
            $sino_federacion = $Pais->sino_federacion;
            if ($Pais->sino_enviar_mailing_por_apertura_de_nuevo_curso_online_en_pais == 'SI') {
                $enviar_mailing_pais = true;                
            }
        }

        $excepcion_ids = [13454, 13502, 16277];
        
        $excepcion = in_array($solicitud_id, $excepcion_ids);
        
        if (($localidad_id <> '' or $excepcion or $enviar_mailing_pais) and $Solicitud->tipo_de_evento_id <> 4 and $Solicitud->campania_mautic_id == '' and $Solicitud->mautic_email_id == '' and ($Solicitud->sino_es_campania_de_capacitacion <> '' or $Solicitud->sino_es_campania_de_capacitacion == 'NO')) {


            $idioma = $Idioma_por_pais->idioma->mnemo;
            App::setLocale($idioma); 

            $txt_invita = $Idioma_por_pais->nombre_de_la_institucion.' '.__('te invita');
            $descripcion_sin_estado = $Solicitud->descripcion_sin_estado();
            



            if ($Solicitud->tipo_de_evento_id <> 3 or ($Solicitud->tipo_de_evento_id == 3 and $Solicitud->tipo_de_curso_online_id == 4) or ($Solicitud->tipo_de_evento_id == 3 and in_array($Solicitud->tipo_de_curso_online_id, [2,3,5]) and $Solicitud->fecha_de_inicio_del_curso_online <> '')) {


                if ($Solicitud->tipo_de_evento_id <> 3 or ($Solicitud->tipo_de_evento_id == 3 and $Solicitud->tipo_de_curso_online_id == 4)) {

                    // Detecto la primera fecha de Inicio para programar el envio de mautic 7 dias antes de esa fecha
                    $fechas_de_evento_ordenadas = $Solicitud->fechas_de_evento->sortBy('fecha_de_inicio');
                    $fechas_de_evento_ordenadas = $fechas_de_evento_ordenadas->values()->all();      
                    
                    $fecha_de_inicio = $fechas_de_evento_ordenadas[0]->fecha_de_inicio;
                }          
                else {
                    $fecha_de_inicio = $Solicitud->fecha_de_inicio_del_curso_online;
                }

                $fecha = date_create($fecha_de_inicio);
                $fecha->sub(new DateInterval('P7D'));
                $fecha_now = new DateTime();

                if ($fecha < $fecha_now) {
                    $fecha = $fecha_now->add(new DateInterval('P2D'));
                }
                $fecha_de_publicacion_de_campania = $fecha->format('Y-m-d');

                $fecha_de_inicio = strtotime($fecha_de_inicio);
                $fecha_de_inicio = strtotime(date("d-m-Y 00:00:00", $fecha_de_inicio));
                $now = strtotime(date("d-m-Y 00:00:00",time()));
                //dd($fecha_de_inicio->format('Y-m-d'));
                if ($now < $fecha_de_inicio) {
                    $programar = true;
                }
                else {
                    $programar = false;    
                }
            }
            else {
                
                $fecha_de_solicitud = date_create($Solicitud->fecha_de_solicitud);

                $now = date_create();
                $interval = $fecha_de_solicitud->diff($now);
                $cant_dias = $interval->format('%a');
                if ($cant_dias < 30) {
                    $programar = true;
                    $fecha = new DateTime(date("d-m-Y 00:00:00",time()));
                    $fecha->add(new DateInterval('P2D'));
                    $fecha_de_publicacion_de_campania = $fecha->format('Y-m-d'); 
                }
                else {
                    $programar = false;  
                }
               
            }

            //dd($fecha_de_publicacion_de_campania);

            //Si la fecha de Inicio es posterior a hoy programo la campaña
            if ($programar) {

                $hola = __('Hola');
                $url_click = $Solicitud->url_form_inscripcion_con_campania_id(244);
                $txt_click = __('Inscribirme');
                $txt_click_aqui = __('Clic aquí');
                $txt_no_responder = __('No responda a este correo, si necesita comunicarse con nosotros envienos un mensaje de texto, whatsapp o llamenos al').' '.$Solicitud->celular_responsable_de_inscripciones;
                $txt_pie = $Idioma_por_pais->nombre_de_la_institucion;

                // DETERMINO LA IMAGEN
                $imagen = '';
                if ($Solicitud->file_imagen_del_formulario_personalizada == '') {
                    $imagen = 'https://forms.gnosis.is/media/images/0d25b0fce90353ad1313a03613dcdbb0.jpeg';
                }
                else {
                    $imagen = env('PATH_PUBLIC').'storage/'.$Solicitud->file_imagen_del_formulario_personalizada;
                }

                // VERIFICO SI HAY UN CONTENIDO ESPECIFICO PARA EL MAIL SINO LO CONSTRUYO
                if ($Solicitud->rtf_contenido_difusion_por_mail <> '') {
                    $contenido_difusion_por_mail = $Solicitud->rtf_contenido_difusion_por_mail;
                }
                else {
                    
                    // DETERMINO EL RESUMEN
                    $resumen = '';
                    if ($Solicitud->rtf_resumen_del_formulario_personalizado == '') {
                        if ($Solicitud->tipo_de_evento_id == 1 or $Solicitud->tipo_de_evento_id == 3) {
                            $resumen = '';
                            /*
                            if ($Solicitud->tipo_de_evento_id == 3) {
                                $resumen .= __('messages.info_mail_invitacion_curso_online');
                            }
                            */
                            $resumen .= '<center><hr>'.__('Algunos temas a desarrollar').'<br>'.__('messages.algunos_temas').'<hr>'.__('CURSO LIBRE Y GRATUITO').'<hr></center>';
                        }
                    }
                    else {
                        $resumen = '<br>'.$Solicitud->rtf_resumen_del_formulario_personalizado.'<hr>';
                    }

                    // DETERMINO EL TEXTO
                    $texto = '';
                    if ($Solicitud->rtf_texto_del_formulario_personalizado <> '') {
                        $texto = $Solicitud->rtf_texto_del_formulario_personalizado;
                    }


                    // Construllo los detalles de cada evento
                    $detalle_horarios_y_lugar = '';
                    if ($Solicitud->fechas_de_evento->count() > 0) {
                        $detalle_horarios_y_lugar = '<BR>';
                        foreach ($Solicitud->fechas_de_evento as $Fecha_de_evento) {
                            $tipo = 'html';
                            $con_inicio = true;
                            $Idioma_por_pais = $Idioma_por_pais;
                            $idioma = $Idioma_por_pais->idioma; 
                            $ver_mapa = true;
                            $con_dir_inicio_distinto = true;

                            $detalle_horarios_y_lugar .= $Fecha_de_evento->armarDetalleFechasDeEventos($tipo, $con_inicio, $Idioma_por_pais, $Solicitud, $idioma, $ver_mapa, $con_dir_inicio_distinto);
                            
                            $detalle_horarios_y_lugar .= "<hr>";

                            //$detalle_horarios_y_lugar .= $Fecha_de_evento->dias_y_horarios()."\n";
                            
                        }
                    }
                
                    $contenido_difusion_por_mail = $resumen.$texto.$detalle_horarios_y_lugar;
                }


                $email_template = '<table align="center" border="0" cellpadding="0" cellspacing="0" class="shrinker" style="width: 100%; max-width:550px;" width="100%"><tbody><tr><td height="45" style="font-size: 45px; line-height: 45px;">&nbsp;</td></tr><tr class="header-split left"><td align="left" style="text-align: left;" valign="center"></td></tr><tr class="header-split right"><td align="right" style="text-align: left" valign="center"><p style="text-align: left; font-family: '."'".'Open Sans'."'".', Verdana, Arial, sans-serif; font-size:16px; line-height:30px; font-weight:400; color:#b2b2b2; padding: 0; margin: 0;"><br></p></td></tr><tr><td height="15" style="font-size: 15px; line-height: 15px;">&nbsp;</td></tr></tbody></table><table align="center" border="0" cellpadding="0" cellspacing="0" style="max-width:550px; background: #ffffff" width="100%"><tbody><tr><td align="center" style="padding: 0; text-align: center;" valign="top"><img src="https://ac.gnosis.is/img/sol-de-acuario-chico-isologo.png" style="width: 100%; height: auto;" alt="Placeholder" class="fr-fic fr-dii" width="100%" height="auto"><img src="'.$imagen.'" style="width: 100%; height: auto;" alt="Placeholder" class="fr-fic fr-dii" width="100%" height="auto"></td></tr><tr><td height="30" style="font-size: 30px; line-height: 30px;">&nbsp;</td></tr><tr><td align="center" style="padding: 0 30px; text-align: center;" valign="top"><h2 style="text-align: center; font-family: '."'".'Open Sans'."'".', Verdana, Arial, sans-serif; font-size:24px; line-height:24px; font-weight:700; color:#212121; padding:0; margin:0;">'.$hola.' {contactfield=firstname}</h2><br><h2 style="text-align: center; font-family: '."'".'Open Sans'."'".', Verdana, Arial, sans-serif; font-size:15px; line-height:20px; font-weight:700; color:#212121; padding:0; margin:0;">'.$txt_invita.'</h2><br>
                <h2 style="text-align: center; font-family: '."'".'Open Sans'."'".', Verdana, Arial, sans-serif; font-size:24px; line-height:24px; font-weight:700; color:#212121; padding:0; margin:0;">'.$descripcion_sin_estado.'</h2><br style="line-height: 18px; height: 18px; font-size: 18px;"><p style="text-align: center; font-family: '."'".'Open Sans'."'".', Verdana, Arial, sans-serif; font-size:16px; line-height:30px; font-weight:400; color:#212121; padding: 0; margin: 0;">'.$contenido_difusion_por_mail.'</p><br style="line-height: 18px; height: 18px; font-size: 18px;"></td></tr><tr><td align="center"><table align="center" border="0" cellpadding="0" cellspacing="0"><tbody><tr><td><table align="center" border="0" cellpadding="0" cellspacing="0"><tbody><tr><td align="left" bgcolor="#00bf9a" class="body-text" style="-webkit-border-radius: 25px; -moz-border-radius: 25px; border-radius: 25px;mso-hide:all;"><a href="'.$url_click.'" style="font-size: 12px; font-family: '."'".'Open Sans'."'".', Verdana, Arial, sans-serif; color: #ffffff; text-decoration: none; text-decoration: none; border-radius: 25px; padding: 16px 25px; display: inline-block; text-transform:uppercase; font-weight: bold; letter-spacing: 1px;" target="_blank">&nbsp;'.$txt_click.'&nbsp;</a></td><!-- Alternate Button for Outlook 2013, 2016--><!--[if mso]><td align="center" valign="top" style="text-align: center;">    <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com🏢word" href="'.$url_click.'" style="height:50px;v-text-anchor:middle;width:150px;" arcsize="25px" strokecolor="#00bf9a" fillcolor="#00bf9a"><w:anchorlock></w:anchorlock><center style="text-transform: uppercase; color:#ffffff;font-family:Helvetica, Arial,sans-serif;font-size:16px;">'.$txt_click_aqui.'</center></v:roundrect></td><![endif]--><!-- End Alternate Button --></tr></tbody></table></td></tr></tbody></table></td></tr><tr><td height="45" style="font-size: 45px; line-height: 45px;">&nbsp;</td></tr><tr><td style="text-align: center;"><hr><span style="font-size: 14px;">'.$txt_no_responder.'</span><hr></td></tr><tr><td style="text-align: center;"><span style="color: rgb(250, 197, 28);"><span style="font-size: 18px;"><strong><span style="font-family: Tahoma,Geneva,sans-serif;"><a class="fr-green fr-strong" href="https://gnosis.is" rel="noopener noreferrer" target="_blank">www.gnosis.is</a></span></strong>&nbsp;</span>&nbsp;</span><br><br></td></tr></tbody></table><br>'.$txt_pie.'<br><br><a href="https://gnosis.is/" style="font-family: '."'".'Open Sans'."'".', Verdana, Arial, sans-serif; font-size:10px; line-height:20px; color:#212121; text-transform: uppercase; text-decoration:underline;" target="_blank">www.gnosis.is</a><span style="font-family:arial, sans-serif; font-size:10px; line-height:20px; color:#dddddd;">&nbsp;|&nbsp;</span><a href="{unsubscribe_url}" style="font-family: '."'".'Open Sans'."'".', Verdana, Arial, sans-serif; font-size:10px; line-height:20px; color:#212121; text-transform: uppercase; text-decoration:underline;" target="_blank">Unsubscribe</a><br><br><p style="font-family: '."'".'Open Sans'."'".', Verdana, Arial, sans-serif; font-size:12px; line-height:18px; color:#212121; text-transform: uppercase; padding:0; margin:0;"><span style="font-size:9px;">© 2017 Mautic - All Rights Reserved.</span><br><span style="font-size:9px;">10 Cabot Road | Medford, MA | 02155</span></p><br style="line-height: 18px; height: 18px; font-size: 18px;"><br><br>';

                //INICIO MAUTIC

                    $campaignApi = $this->connMautic('campaigns');
                    $contactApi = $this->connMautic('contacts');
                    $emailApi = $this->connMautic('emails');


                    $masterCamnpaignId = 10;
                    $campaign = $campaignApi->get($masterCamnpaignId);
                    //dd($campaign);

                    $newCampaign = $campaignApi->cloneCampaign($masterCamnpaignId);

                    $newCamnpaignId = $newCampaign['campaign']['id'];

                    //dd($newCampaign);

                    $descripcion_sin_estado = mb_substr($descripcion_sin_estado, 0, 60, "UTF-8");


                    $title = 'GNOSIS '.$descripcion_sin_estado;
                    $name = 'AC Invitacion Solicitud: '.$Solicitud->id.' - '.$descripcion_sin_estado;
                    $subject = 'GNOSIS '.$descripcion_sin_estado;

                    $data = array(
                        'title' => $title,
                        'name' => $name,
                        'description' => 'Email para envio de invitaciones masivas a todas las personas de la Ciudad de la Solicitud',
                        'isPublished' => 1,
                        'subject' => $subject,
                        'fromAddress' => 'info@gnosis.is',
                        'fromName' => 'Gnosis',
                        'customHtml' => $email_template
                    );
                    $email = $emailApi->create($data);
                    //dd($email);

                    $email_id = $email['email']['id'];
                    //dd($email_id);


                    $data = $campaign['campaign'];
                    $data['isPublished'] = true;
                    $data['publishUp'] = $fecha_de_publicacion_de_campania." 00:00";
                    //$data['publishUp'] = "2021-12-12 15:00";
                    $data['name'] = 'AC Invitacion Solicitud: '.$Solicitud->id.' - '.$descripcion_sin_estado;
                    //$data['events'][0]['id'] = null;
                    $data['events'][0]['channelId'] = $email_id;
                    $data['events'][0]['properties']['properties']['email'] = $email_id;
                    $data['events'][0]['properties']['email'] = $email_id;
                    $data['events'][0]['email'] = $email_id;

                    //dd($data);


                    $createIfNotFound = false;

                    $newCampaignEdited = $campaignApi->edit($newCamnpaignId, $data, $createIfNotFound);


                    $where_raw = "mautic_contact_id IS NOT NULL and sino_notificar_proximos_eventos = 'SI'";
                    $where_raw .= " AND s.institucion_id = $institucion_id";
                    $para_todos_en_el_pais = '';

                    if ($localidad_id <> '') {                        
                        //Si es un pais fuera de la federacion invito a todos de ese pais
                        if ($sino_federacion <> 'SI' and $Pais <> null) {
                            $para_todos_en_el_pais = 'OR (i.pais_id = '.$Pais->id.')';
                        }

                        //Si el idioma del formulario es arabe invito a todos estos paises
                        if ($Idioma_por_pais->idioma->id == 12) {
                            $para_todos_en_el_pais = 'OR (i.pais_id in (40, 41, 48, 68, 75, 86, 87, 119, 124, 129, 133, 135, 149, 153, 172, 176, 181, 203, 204, 208, 223, 236, 71, 88, 118, 122, 226, 41, 68, 135, 149, 208, 223))';
                        }
 
                        $pais_id = $Solicitud->localidad->provincia->pais_id;
                        $localidad = $Solicitud->localidad->localidad;                        
                        $where_raw .= " AND (s.localidad_id = $localidad_id OR (i.pais_id = $pais_id AND LOWER(i.ciudad) = LOWER('$localidad')) $para_todos_en_el_pais)";
                    }
                    else {

                        //Si el idioma del formulario es arabe invito a todos estos paises
                        if ($Idioma_por_pais->idioma->id == 12) {
                            $para_todos_en_el_pais = 'OR (i.pais_id in (40, 41, 48, 68, 75, 86, 87, 119, 124, 129, 133, 135, 149, 153, 172, 176, 181, 203, 204, 208, 223, 236, 71, 88, 118, 122, 226, 41, 68, 135, 149, 208, 223))';
                        }

                        $pais_id = $Solicitud->pais_id;
                        $where_raw .= " AND (s.pais_id = $pais_id OR (i.pais_id = $pais_id) $para_todos_en_el_pais)";
                    }
                    
                    $Inscripciones = DB::table('solicitudes as s')
                    ->select(DB::Raw('DISTINCT i.mautic_contact_id'))
                    ->join('inscripciones as i', 'i.solicitud_id', '=', 's.id')
                    ->leftjoin('users as u', 's.user_id', '=', 'u.id')
                    ->whereRaw($where_raw)
                    ->get();

                    $array_leads_id = array();
                    $i = 0;
                    $var_ids_in = '-1';
                    foreach ($Inscripciones as $Inscripcion) {
                        $var_ids_in .= ', '.$Inscripcion->mautic_contact_id;
                        $i++;
                    }
                    

                    $Leads = DB::connection('mautic')
                    ->table('leads')
                    ->select('id')
                    ->whereRaw(DB::Raw("id in ($var_ids_in)"))
                    ->get();


                    /*
                    if ($Inscripciones->count() > 0) {

                        $searchFilter = 'ids:'.$Inscripcion[0]->mautic_contact_id;
                        $contacts = $contactApi->getList($searchFilter);      

                        foreach ($contacts["contacts"] as $contact) {
                            $contactId = $contact['id'];
                            $response = $campaignApi->addContact($newCamnpaignId, $contactId);
                            //echo $i.' id: '.$contact['id'].' Nombre:'.$contact['fields']['all']['email'].'<BR>';
                        }  

                        
                        $campania_mautic_id = $newCampaignEdited["campaign"]["id"];


                        //dd($newCampaignEdited);

                    }                
                    */

                    foreach ($Leads as $Lead) {

                        $now = new \DateTime();
                        $fecha_now = $now->format('Y-m-d H:i:s');



                        DB::connection('mautic')->insert('insert into campaign_leads (campaign_id, lead_id, date_added, manually_removed, manually_added, date_last_exited, rotation) values (?, ?, NOW(), ?, ?, NULL, ?)', [$newCamnpaignId, $Lead->id, 0, 1, 1]);
                        
                        /*
                        $Campaign_lead = new Campaign_lead();
                        $Campaign_lead->campaign_id = $newCamnpaignId;
                        $Campaign_lead->lead_id = $Inscripcion->mautic_contact_id;
                        $Campaign_lead->date_added = $fecha_now;
                        $Campaign_lead->manually_removed = 0;
                        $Campaign_lead->manually_added = 1;
                        $Campaign_lead->date_last_exited = NULL;
                        $Campaign_lead->rotation = 1;
                        $Campaign_lead->save();
                        */
                        
                    }
                    
                    /*
                    // ENVIAR SOLO A UN CONTACTO (PARA PRUEBA)
                    $searchFilter = 'tag:"ACtestSEND"';    
                           
                    $searchFilter = 'email: fernandomadoz@hotmail.com';
                    $contacts = $contactApi->getList($searchFilter);
                    

                    // INICIO ENVIAR A LOS CONTACTOS POR TAGs (PARA PRUEBA)
                    $start = 0;
                    $limit = 3;
                    
                    if ($Solicitud->localidad_id > 0) {
                        $searchFilter = 'tag:"'.$Solicitud->localidad->localidad.'"';
                    }
                    else {
                        if ($Solicitud->pais_id > 0) {
                        $searchFilter = 'tag:"'.$Solicitud->pais->pais.'"';
                        }
                    }
                    
                    $contacts = $contactApi->getList($searchFilter, $start, $limit);
                    // FIN ENVIAR A LOS CONTACTOS POR TAGs (PARA PRUEBA)
                    //dd($contacts);

                    // Cargo Los Contactos
                    $i = 0;               

                    foreach ($contacts["contacts"] as $contact) {
                        $contactId = $contact['id'];
                        $response = $campaignApi->addContact($newCamnpaignId, $contactId);
                        //echo $i.' id: '.$contact['id'].' Nombre:'.$contact['fields']['all']['email'].'<BR>';
                    }
                    


                    $createIfNotFound = false;

                    $newCampaignEdited = $campaignApi->edit($newCamnpaignId, $data, $createIfNotFound);

                    //dd($newCampaignEdited);

                    $campania_mautic_id = $newCampaignEdited["campaign"]["id"];
                    */

                //FIN MAUTIC

            }


            if ($newCamnpaignId <> null) {
                $Solicitud->campania_mautic_id = $newCamnpaignId;
                $Solicitud->mautic_email_id = $email_id;
                $Solicitud->save();
            }
        }

        return $newCamnpaignId;

    }




    public function guardarContacto($inscripcion_id, $Solicitud, $systemsource, $nombre, $apellido, $celular, $email_correo, $pais_id, $ciudad) {

        $Inscripcion = Inscripcion::find($inscripcion_id);

        //INICIO MAUTIC
            if ($email_correo <> '') {
                $tags_mautic = ['id'.$Solicitud->id];

                $contactApi = $this->connMautic('contacts');

                $searchFilter = 'email:'.$email_correo;
                $contacts = $contactApi->getList($searchFilter);

                $Pais = '';
                if ($Solicitud->tipo_de_evento_id == 3 or $Solicitud->tipo_de_evento_id == 4) {
                    if ($pais_id <> '') {
                        $Pais = Pais::find($pais_id);
                        $pais = $Pais->pais;
                    }
                    else {
                        if ($Solicitud->pais_id <> '') {
                            $pais = $Solicitud->pais->pais;
                        }  
                        else {                  
                            $pais = '';
                        }
                    }
                }
                else {
                    $pais = $Solicitud->localidad->provincia->pais->pais;
                }            
                $countrystateregionlocal = $pais.' / ';
                array_push($tags_mautic, $pais);

                if (strpos($Solicitud->tags_mautic, '#') >= 0) {
                    $tags_de_solicitud = explode('#', trim($Solicitud->tags_mautic));
                    foreach ($tags_de_solicitud as $tag) {
                        if ($tag <> '') {
                            array_push($tags_mautic, $tag); 
                        }
                    }                
                }

                $provincia = '';
                if ($Solicitud->tipo_de_evento_id <> 3) {
                    if ($Solicitud->tipo_de_evento_id == 4) {
                        $provincia = $Localidad->provincia->provincia;
                    }
                    else {
                        $provincia = $Solicitud->localidad->provincia->provincia;
                    }
                    $countrystateregionlocal .= $provincia.' / ';
                    array_push($tags_mautic, $provincia);
                }       

                if ($Solicitud->tipo_de_evento_id == 3 or $Solicitud->tipo_de_evento_id == 4) {
                    $localidad = $ciudad;
                }
                else {
                    $localidad = $Solicitud->localidad->localidad;
                }                
                $countrystateregionlocal .= $localidad;
                array_push($tags_mautic, $localidad);

                $last_active = date("Y-m-d H:i:s");


                if ($Solicitud->Tipo_de_evento->id == 2) {
                    $Fecha_de_evento = Fecha_de_evento::where('solicitud_id', $Solicitud->id)->get();
                    $themeofinterest = $Fecha_de_evento[0]->titulo_de_conferencia_publica;
                    array_push($tags_mautic, $themeofinterest);
                }
                else {
                    $themeofinterest = $Solicitud->Tipo_de_evento->tipo_de_evento;
                    array_push($tags_mautic, $themeofinterest);
                }

                if ($contacts['total'] == "0") {

                    //dd($contacts['total']);
                    //$id = 759;
                    //$response = $contactApi->get($id);
                    //$contact = $response[$contactApi->itemName()];
                    //$response = $contactApi->getList('', 0, 1);

                    //$systemsource = 'gnosis-incripcion-sistemaAC';

                    $data = array(
                        "email" => $email_correo,
                        "firstname" => $nombre,
                        "lastname" => $apellido,
                        "mobile" => $celular,
                        "themeofinterest" => $themeofinterest,
                        //"description" => $themeofinterest,
                        "countrystateregionlocal" => $countrystateregionlocal,
                        "pais" => $pais,
                        "provincia" => $provincia,
                        "ciudad" => $localidad,
                        "campaign_id" => $Solicitud->id,
                        //"eventid" => $fecha_de_evento_id,
                        "systemsource" => $systemsource,
                        //"date_of_interest" => $date_of_interest,
                        "last_active" => $last_active,
                        //"notificar_proximos_evento" => $notificar_proximos_eventos,                    
                        "tags" => $tags_mautic,
                    );


                    $asset = $contactApi->create($data);
                    //dd($data);

                    if (isset($Inscripcion)) {
                        $Inscripcion->mautic_contact_id = $asset['contact']['id'];
                        $Inscripcion->save(); 
                    }

                }
                else {
                    $contactId = key($contacts['contacts']);

                    $data = array(
                        'tags' => $tags_mautic,
                        'last_active' => $last_active,
                        //"notificar_proximos_evento" => $notificar_proximos_eventos,  
                        "info_log_actualizacion" => 'MauticController Actualizacion de Contacto'.'inscripcion_id: '.$inscripcion_id.' - '.$email_correo.' - '.rand(0,1000),  


                    );

                    $createIfNotFound = false;

                    $contact = $contactApi->edit($contactId, $data, $createIfNotFound);
                    //dd($contactId);

                    if (isset($Inscripcion)) {
                        $Inscripcion->mautic_contact_id = $contactId;
                        $Inscripcion->save(); 
                    }

                }
            }

        //FIN MAUTIC      

    }


    public function enviarMailMautic($Inscripcion) {


        
        if (ENV('APP_ENV') == 'development') {
            try {

                $contactApi = $this->connMautic('contacts');
                $emailApi = $this->connMautic('emails');

                $searchFilter = 'email:'.$Inscripcion->email_correo;
                //dd($searchFilter);
                $contacts = $contactApi->getList($searchFilter);
                //dd($contacts);
                $contactId = key($contacts['contacts']);
                dd($contactId);

                //$searchFilter = 'emailname:fernandomadoz@hotmail.com';
                //$emails = $emailApi->getList($searchFilter);
                //dd($emails);

                //$email = $emailApi->create($data);
                //$emailId = $email['email']['id'];
                
                //$email = $emailApi->get($emailId);
                //dd($email);

                //$asset = $contactApi->create($data);

                //$emailApi = $api->newApi("emails", $auth, $apiUrl);

   
                $asunto = __('Inscripción registrada');
                $titulo = __('Inscripción registrada').' | '.$Inscripcion->solicitud->descripcion_sin_estado();   
                $mensaje = '';          

                
                $tipo = 'whatsapp';
                $con_inicio = true;
                $Idioma_por_pais = null;
                $idioma = null;
                $ver_mapa = true;
                $con_dir_inicio_distinto = true;

                $infoInscripcion = $Inscripcion->InfoInscripcion($tipo, $con_inicio, $Idioma_por_pais, $Inscripcion->solicitud, $idioma, $ver_mapa, $con_dir_inicio_distinto);
            
                foreach ($infoInscripcion as $info) {
                    if ($info[0] <> '') {
                        $mensaje .= $info[0].': ';    
                    }
                    $mensaje .= $info[1];
                }
                

                if ($Inscripcion->fecha_de_evento_id <> null) {
                    $url_whatsapp = $Inscripcion->url_whatsapp();
                }
                else {
                    $url_whatsapp = $Inscripcion->url_whatsapp_sin_evento();
                }

                $url_click = '';
                $txt_click = '';
                $txt_no_responder = __('No responda a este correo, si necesita comunicarse con nosotros envienos un mensaje de texto, whatsapp o llamenos al').' '.$Inscripcion->Solicitud->celular_responsable_de_inscripciones;
                $txt_pie = '';

                $hash = md5(ENV('PREFIJO_HASH').$Inscripcion->id);

                //$mensaje = preg_replace('@(https?://([-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#-~]*(\?\S+)?)?)?)@', '<a href="$1">$1</a>', $url_whatsapp['mail_pedido_de_confirmacion']);

                if ($Inscripcion->solicitud->tipo_de_evento_id == 4) {
                    $url_click = $Inscripcion->solicitud->idioma->url_form_curso_online;
                    $txt_click = __('Click aquí para inscribirme a los cursos');    
                }
                else {
                    $url_click = ENV('PATH_PUBLIC').'f/auto/confirmar-asistencia/'.$Inscripcion->id.'/'.$hash;
                    $txt_click = __('Click aqui para confirmar su asistencia');
                }

                //dd($mensaje);

                $data = array(
                    'tokens' => array(
                        '{asunto}' => $asunto,
                        '{titulo}' => $titulo,
                        '{mensaje}' => $mensaje,
                        '{url_click}' => $url_click,
                        '{txt_click}' => $txt_click,
                        '{txt_no_responder}' => $txt_no_responder,
                        '{txt_pie}' => $txt_pie

                    )
                );



                //dd($data);
                $emailId = 1521;
                $email = $emailApi->makeRequest('emails/'.$emailId.'/contact/'.$contactId.'/send', $data, 'POST');
                //dd($email);

                //$email = $emailApi->sendToContact($emailId, $contactId);

                $tit_mensaje_envio = __('Email Enviado Exitosamente');
                //dd($email);
            
            } catch (Exception $e) {
                $tit_mensaje_envio = __('Error en el envio del Email');
                //dd('error');
                // Do Error handling
            }


            dd($email);

            return $tit_mensaje_envio;
        }
    

        $mensaje_envio = '<div class="col-xs-12 col-lg-12">';
        $mensaje_envio .= '<br>';
        $mensaje_envio .= '<div class="alert alert-success alert-dismissible">';
        $mensaje_envio .= '<h4><i class="icon fa fa-send"></i> '.$tit_mensaje_envio.'</h4><p>'.$Inscripcion->email_correo.' ('.$asunto.')</p>';
        $mensaje_envio .= '</div>';
        $mensaje_envio .= '</div>';

        return $mensaje_envio;

    }

}

