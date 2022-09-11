@extends('layouts.app_gnosis')

@section('title')
Asistencia - <?php echo $Solicitud->descripcion_sin_estado() ?>
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <h1>Registro de asistencia</h1>
            <h2><?php echo $Solicitud->descripcion_sin_estado() ?></h2>
            <p>Haga click en la lección que desea notificar como vista</p>
            <br>

            <?php foreach ($Lecciones as $Leccion) { ?>
                <p>
                    <a href="<?php echo $Leccion->url_notificacion_leccion_finalizada($Solicitud, 2, $Idioma_por_pais) ?>" target="_blank">
                        <button type="button" class="btn btn-primary"><?php echo $Leccion->nombre_de_la_leccion ?></button>
                    </a>
                </p>
            <?php } ?>
            <br>

            <?php if (count($Lecciones_extra)>0) { ?>
                <h2>Lecciones Extra</h2>
                <p>Si tu curso tiene lecciones extra, previas o posteriores al curso puedes utilizar estos enlaces para notificar la asistencia de esas lecciones. </p>
                <?php foreach ($Lecciones_extra as $Leccion_extra) { ?>
                    <p>
                        <a href="<?php echo $Leccion->url_notificacion_leccion_extra_finalizada($Solicitud, 2, $Idioma_por_pais) ?>" target="_blank">
                            <button type="button" class="btn btn-primary"><?php echo $Leccion_extra->titulo ?></button>
                        </a>
                    </p>
                <?php } ?>                                
            <?php } ?>                                

            <br>
            <h2>Aún no estas inscripto?</h2>
            <p>Elige como quieres inscribirte</p>
            <p>
                <a href="<?php echo $Solicitud->url_wabot_inscripcion() ?>" target="_blank">
                    <button type="button" class="btn btn-primary">Inscribirme mediante whatsApp</button>
                </a>
            </p>
            

        </div>
    </div>
</div>
@endsection