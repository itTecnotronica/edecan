
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
        var latlng = new google.maps.LatLng(-0.397, 5.644);
        var options = { zoom: 2, center: latlng,  mapTypeId: google.maps.MapTypeId.ROADMAP }
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
              $contacto .= '<br> Tel:'.$Sede->telefonos_fijos;
              $contacto .= '<br> Cel:'.$Sede->telefonos_celulares;
              $contacto .= '<br> E-Mail:'.$Sede->correos;
              $contacto .= '<br>'.$Sede->otra_informacion_adicional;
              $contacto .= '</p>';
              $contacto = str_replace('"', '', $contacto);
              $ciudad = $Sede->direccion.', '.$Sede->localidad.', '.$Sede->provincia.', Argentina';


          ?>

          var infowindow_<?php echo $Sede->id_sede_de_difusion ?> = new google.maps.InfoWindow({
            content: "<h3><img src='"+iconBase+"sol-de-acuario-chico.png' style='width: 35px; margin-right: 5px'>GNOSIS</h3><h4><?php echo $Sede->localidad ?></h4><h5><?php echo $Sede->provincia ?></h5><?php echo $contacto ?>"
          });

            geocoder.geocode({ 'address': '<?php echo $ciudad ?>' }, function(results, status) {
              if (status === google.maps.GeocoderStatus.OK) {
                console.log('UPDATE tb_sede_de_difusion SET latitud_y_longitud_google_maps = "'+results[0].geometry.location.lat()+', '+results[0].geometry.location.lng()+'" WHERE id_sede_de_difusion = <?php echo $Sede->id_sede_de_difusion ?>;')
                var marker_<?php echo $Sede->id_sede_de_difusion ?> = new google.maps.Marker({
                    position: results[0].geometry.location,
                    icon: icons['marker'].icon,
                    map: map
                });

                marker_<?php echo $Sede->id_sede_de_difusion ?>.addListener('click', function() {
                  infowindow_<?php echo $Sede->id_sede_de_difusion ?>.open(map, marker_<?php echo $Sede->id_sede_de_difusion ?>);
                });

              } else {
                console.log("Something got wrong " + status);
              }
            });
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

