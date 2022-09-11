  <!-- jQuery 3 -->
  <script src="<?php echo env('PATH_PUBLIC')?>bower_components/jquery/dist/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/OverlappingMarkerSpiderfier/1.0.3/oms.min.js"></script>

    <style>
      /* Always set the map height explicitly to define the size of the div
       * element that contains the map. */
       html, body { height: 100%; margin: 0; padding: 0; }
      #map {
        height: 100% !important;
        width: 100% !important;
      }
      /* Optional: Makes the sample page fill the window. */
    </style>


    <div class="col-lg-12" id="map"></div>
    <script>
      var map;
      var markers = [];
      function initMap() {
        var latlng = new google.maps.LatLng(<?php echo $lat_lon ?>);
        var options = { zoom: 4, center: latlng,  mapTypeId: google.maps.MapTypeId.ROADMAP }
        var geocoder = new google.maps.Geocoder();


        map = new google.maps.Map(
            document.getElementById('map'),
            options);

        var iconBase =
            "<?php echo env('PATH_PUBLIC')?>img/";

        var icons = {
          marker: {
            icon: iconBase + 'marker.png'
          },
          marker2: {
            icon: iconBase + 'marker2.png'
          }
        };

        var oms = new OverlappingMarkerSpiderfier(map, {
          markersWontMove: true,
          markersWontHide: true,
          basicFormatEvents: true
        });

        // Create markers.
          <?php

            foreach ($Solicitudes as $Solicitud) {
              $dato = $Solicitud->fechas_de_evento[0]->datos_url_google_maps();
              $DetalleFechasDeEventos = '';
              foreach ($Solicitud->fechas_de_evento as $Fecha_de_evento) {
                $DetalleFechasDeEventos .= $Fecha_de_evento->armarDetalleFechasDeEventos('html', true).'<br>';
              }              
              $texto = str_replace('"', '', $DetalleFechasDeEventos);
              $inscripcion = '<p>'.__('Formulario de Inscripcion').": <a target='_blank' href='".$Solicitud->fechas_de_evento[0]->Solicitud->url_form_inscripcion()."'>".$Solicitud->fechas_de_evento[0]->Solicitud->url_form_inscripcion().'</a></p>';
              $contacto = '<p>'.__('Informes').': <br>'.$Solicitud->celular_responsable_de_inscripciones."<a href='".$Solicitud->url_contacto_whatsapp_form()."' target='_blank'>(Enviar WhatsApp)</a></p>";
              if ($Solicitud->localidad_id <> '') {
                $ciudad = $Solicitud->localidad->localidad.', '.$Solicitud->localidad->provincia->provincia.', '.$Solicitud->localidad->provincia->pais->pais;
              }
              else {

                if ($Solicitud->pais_id <> '') {
                  $ciudad = $Solicitud->pais->pais;
                }
                else {
                  $ciudad = '';
                }

              }


          ?>

          var infowindow_<?php echo $Solicitud->fechas_de_evento[0]->id ?> = new google.maps.InfoWindow({
            content: "<h3><img src='"+iconBase+"sol-de-acuario-chico.png' style='width: 35px; margin-right: 5px'>GNOSIS</h3><h4><?php echo __($Solicitud->tipo_de_evento->tipo_de_evento) ?></h4><h5><?php echo $ciudad ?></h5><?php echo $texto ?><?php echo $inscripcion ?><?php echo $contacto ?>"
          });

                console.log("status");
          <?php if ($dato['latitud'] <> '' and is_numeric($dato['latitud']) and is_numeric($dato['longitud'])) { ?>

                // Set the coordonates of the new point
                var latLng = new google.maps.LatLng(<?php echo $dato['latitud'] ?>,<?php echo $dato['longitud'] ?>);
                //Check Markers array for duplicate position and offset a little


                // Initialize the new marker
                //var marker = new google.maps.Marker({map: map, position: latLng, title: val['TITLE']});

                // Add the marker to the array


            var marker_<?php echo $Solicitud->fechas_de_evento[0]->id ?> = new google.maps.Marker({
              position: latLng,
              icon: icons['marker'].icon,
              map: map
            });
            
            markers.push(marker_<?php echo $Solicitud->fechas_de_evento[0]->id ?>);

          marker_<?php echo $Solicitud->fechas_de_evento[0]->id ?>.addListener('spider_click', function() {
            infowindow_<?php echo $Solicitud->fechas_de_evento[0]->id ?>.open(map, marker_<?php echo $Solicitud->fechas_de_evento[0]->id ?>);
          });
          oms.addMarker(marker_<?php echo $Solicitud->fechas_de_evento[0]->id ?>); 

          <?php }
          else { ?>

            geocoder.geocode({ 'address': '<?php echo $ciudad ?>' }, function(results, status) {
              if (status === google.maps.GeocoderStatus.OK) {
                var marker_<?php echo $Solicitud->fechas_de_evento[0]->id ?> = new google.maps.Marker({
                    position: results[0].geometry.location,
                    icon: icons['marker'].icon,
                    map: map
                });
                console.log("location: "+results[0].geometry.location);

                  $.ajax({
                    url: '<?php echo env('PATH_PUBLIC')?>guardar-lat-y-long',
                    type: 'POST',
                    dataType: 'html',
                    async: true,
                    data:{
                      _token: "{{ csrf_token() }}",
                      fecha_de_evento_id: <?php echo $Solicitud->fechas_de_evento[0]->id ?>,
                      latitud: results[0].geometry.location.lat(),
                      longitud: results[0].geometry.location.lng(),
                    },
                    success: function success(data, status) {
                      console.log("Ajax: "+data);
                    },
                    error: function error(xhr, textStatus, errorThrown) {
                        alert(errorThrown);
                    }
                  });

                marker_<?php echo $Solicitud->fechas_de_evento[0]->id ?>.addListener('click', function() {
                  infowindow_<?php echo $Solicitud->fechas_de_evento[0]->id ?>.open(map, marker_<?php echo $Solicitud->fechas_de_evento[0]->id ?>);
                });

              } else {
                console.log("Geocode unsuccessful"+status);
              }
            });
          <?php } ?>
          <?php } ?>
      }
    </script>


    <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=<?php echo env('API_KEY_GOOGLE_MAPS')?>&callback=initMap">
    </script>

    <!--script src="https://maps.googleapis.com/maps/api/js?v=3.exp&signed_in=true&key=<?php echo env('API_KEY_GOOGLE_MAPS')?>"></script-->
    <!--script async defer
    src="https://maps.googleapis.com/maps/api/js?callback=initMap">
    </script-->

