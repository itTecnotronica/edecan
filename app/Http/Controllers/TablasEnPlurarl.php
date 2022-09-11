<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TablasEnPlurarl extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function tablasEnPlural() {

        $tb_plural_distintas = [
            "Fecha_de_evento" => "fechas_de_evento",
            "Tipo_de_evento" => "tipos_de_eventos", 
            "Pais" => "paises", 
            "Localidad" => "localidades",
            "Opcion" => "opciones",
            "Rol_de_usuario" => "roles_de_usuario",
            "Solicitud" => "solicitudes",
            "Inscripcion" => "inscripciones",
            "Idioma_por_pais" => "idiomas_por_pais",
            "Modelo_de_mensaje" => "modelos_de_mensajes",
            "Formato_de_hora" => "formatos_de_hora",
            "Asistencia" => "asistencias",
            "Codigo_de_envio" => "codigos_de_envio",
            "Medio_de_envio" => "medios_de_envio",
            "Ejecutivo" => "users",
            "Evento_en_sitio" => "eventos_en_sitio",
            "Tipo_de_evento_en_sitio" => "tipos_de_evento_en_sitio",
            "Registro_de_error" => "registros_de_errores",
            "Visualizacion_de_formulario" => "visualizaciones_de_formulario",
            "Tipo_de_campania_facebook" => "tipos_de_campania_facebook",
            "Texto_anuncios" => "textos_anuncios",
            "Encabezado_de_envio" => "encabezados_de_envios",
            "Lista_de_envio" => "listas_de_envios",
            "Instancia_de_envio" => "instancias_de_envios",
            "Envio_a_contacto" => "envios_a_contactos",
            "Tipo_de_lista_de_envio" => "tipos_de_listas_de_envios",
            "Pais_por_equipo" => "paises_por_equipo",
            "Usuario_por_equipo" => "usuarios_por_equipo",
            "Encuesta_de_satisfaccion" => "encuestas_de_satisfaccion",
            "App_nivel_de_acceso" => "app_niveles_de_acceso",
            "App_tipo_de_contenido" => "app_tipos_de_contenido",
            "Rol_extra" => "roles_extra",
            "Modelo_de_mensaje_curso" => "modelos_de_mensajes_de_curso",
            "Tipo_de_curso_online" => "tipos_de_curso_online",
            "Causa_de_baja" => "causas_de_baja",
            "Leccion" => "lecciones",
            "Canal_de_recepcion_del_curso" => "canales_de_recepcion_del_curso",
            "Grupo_de_solicitud" => "grupos_de_solicitud",
            "Evaluacion" => "evaluaciones",
            "Modelo_de_evaluacion" => "modelos_de_evaluacion",
            "Material_de_leccion" => "materiales_de_leccion",
            "Cambio_de_solicitud_de_inscripcion" => "cambios_de_solicitudes_de_inscripciones",
            "Causa_de_cambio_de_solicitud" => "causas_de_cambio_de_solicitud",
            "Leccion_por_pais_e_idioma" => "lecciones_por_pais_e_idioma",
            "Leccion_extra" => "lecciones_extra",
            "Estado_de_seguimiento" => "estados_de_seguimiento",
            "Alumno_avanzado" => "alumnos_avanzados",
            "Institucion" => "instituciones",
            "Capacitacion" => "capacitaciones",
            "Capacitacion_de_personal" => "capacitaciones_de_personal",
            "Autor" => "autores",
            "Material" => "materiales",
            "Tipo_de_material" => "tipos_de_materiales",
            "App_inscripcion_en_evento" => "app_inscripciones_en_eventos",
            "App_tipo_de_carnet" => "app_tipos_de_carnets",
            "App_tipo_de_debito" => "app_tipos_de_debitos",
            "App_tipo_de_evento" => "app_tipos_de_eventos",
            "App_tipo_de_tarjeta" => "app_tipos_de_tarjetas",
            "Modalidad_de_notificacion_de_asistencia" => "modalidades_de_notificacion_de_asistencias",
            "Cuenta_contable" => "cuentas_contables",
            "Subcuenta_contable" => "subcuentas_contables",
            "Persona_juridica" => "personas_juridicas",
            "Caja_contable" => "cajas_contables",
            "Tipo_de_movimiento_contable" => "tipos_de_movimientos_contables",
            "Movimiento_contable" => "movimientos_contables",
            "Coordinador_user" => "users",
            "Log_accion" => "log_acciones",
            
            
        ];
        
        return $tb_plural_distintas;

    }


}
