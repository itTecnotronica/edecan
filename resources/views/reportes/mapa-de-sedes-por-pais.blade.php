<?php 
use \App\Http\Controllers\GenericController; 
$gCont = new GenericController();


?>
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
      function initMap() {

        var latlng = new google.maps.LatLng(0,0);

        //var latlng = new google.maps.LatLng(results[0].geometry.location.lat, results[0].geometry.location.lng);
        var options = { zoom: 3, center: latlng,  mapTypeId: google.maps.MapTypeId.ROADMAP }
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

        // Create markers.
          <?php

            foreach ($Sedes as $Sede) {
              $contacto = '<p>'.__('Dirección').': '.$Sede->direccion;
              if ($Sede->telefono_con_whatsapp <> '') {
                $contacto .= '<br> Tel: <a href="tel:'.$Sede->telefono_con_whatsapp.'" target="_blank">'.$Sede->telefono_con_whatsapp.'</a>';
                $contacto .= '<br> Whatsapp: <a href="https://api.whatsapp.com/send?phone='.$gCont->celular_wa($Sede->telefono_con_whatsapp, null).'" target="_blank">'.$Sede->telefono_con_whatsapp.'</a>';
              }
              if ($Sede->email_correo <> '') {
                $contacto .= '<br> E-Mail:'.$Sede->email_correo;
              }
              if ($Sede->informacion_adicional <> '') {
                $contacto .= '<br>'.$Sede->informacion_adicional;
              }
              if ($Sede->url_enlace_a_google_maps <> '') {
                $contacto .= '<br><a href="'.$Sede->url_enlace_a_google_maps.'" target="_blank">Ver ubicación en Google Maps</a>';
              }
              $contacto .= '</p>';
              $contacto = str_replace('"', '', $contacto);
              $ciudad = $Sede->ciudad.', '.$Sede->provincia_estado_o_region.', '.$Sede->pais;
              $id = $Sede->id;


          ?>

          var infowindow_<?php echo $Sede->id ?> = new google.maps.InfoWindow({
            content: "<h3><img src='"+iconBase+"sol-de-acuario-chico.png' style='width: 35px; margin-right: 5px'>Sede de GNOSIS</h3><h4><?php echo $Sede->ciudad ?>, <?php echo $Sede->provincia_estado_o_region ?></h4><?php echo $contacto ?>"
          });

                console.log("status");
          <?php if ($Sede->latitud <> '' and is_numeric($Sede->latitud) and is_numeric($Sede->longitud)) { ?>
            var marker_<?php echo $id ?> = new google.maps.Marker({
              position: new google.maps.LatLng(<?php echo $Sede->latitud ?>, <?php echo $Sede->longitud ?>),
              icon: icons['marker'].icon,
              map: map
            });

          marker_<?php echo $id ?>.addListener('click', function() {
            infowindow_<?php echo $id ?>.open(map, marker_<?php echo $id ?>);
          });

          <?php }
          else { ?>

            geocoder.geocode({ 'address': '<?php echo $ciudad ?>' }, function(results, status) {
              if (status === google.maps.GeocoderStatus.OK) {
                var marker_<?php echo $id ?> = new google.maps.Marker({
                    position: results[0].geometry.location,
                    icon: icons['marker'].icon,
                    map: map
                });

                marker_<?php echo $id ?>.addListener('click', function() {
                  infowindow_<?php echo $id ?>.open(map, marker_<?php echo $id ?>);
                });

              } else {
                console.log("Something got wrong " + status);
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

