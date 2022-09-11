
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
        var latlng = new google.maps.LatLng(-38.416097, -63.616672);
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

        // Create markers.
          <?php

            foreach ($Sedes as $Sede) {
              $dato = $Sede['datos'];
              $contacto = '<p>'.__('Dirección').': '.$Sede['direccion'];
              if ($Sede['telefonos_fijos'] <> '') {
                $contacto .= '<br> Tel:'.$Sede['telefonos_fijos'];
              }
              if ($Sede['telefonos_celulares'] <> '') {
                $contacto .= '<br> Cel:'.$Sede['telefonos_celulares'];
              }
              if ($Sede['correos'] <> '') {
                $contacto .= '<br> E-Mail:'.$Sede['correos'];
              }
              if ($Sede['otra_informacion_adicional'] <> '') {
                $contacto .= '<br>'.$Sede['otra_informacion_adicional'];
              }
              if ($dato['url'] <> '') {
                $contacto .= '<br><a href="'.$dato['url'].'" target="_blank">Ver ubicación en Google Maps</a>';
              }
              $contacto .= '</p>';
              $contacto = str_replace('"', '', $contacto);
              $ciudad = $Sede['localidad'].', '.$Sede['provincia'].', Argentina';
              $id = $Sede['id_sede_de_difusion'];


          ?>

          var infowindow_<?php echo $Sede['id_sede_de_difusion'] ?> = new google.maps.InfoWindow({
            content: "<h3><img src='"+iconBase+"sol-de-acuario-chico.png' style='width: 35px; margin-right: 5px'>Sede de GNOSIS</h3><h4><?php echo $Sede['localidad'] ?>, <?php echo $Sede['provincia'] ?></h4><?php echo $contacto ?>"
          });

                console.log("status");
          <?php if ($dato['latitud'] <> '' and is_numeric($dato['latitud']) and is_numeric($dato['longitud'])) { ?>
            var marker_<?php echo $id ?> = new google.maps.Marker({
              position: new google.maps.LatLng(<?php echo $dato['latitud'] ?>, <?php echo $dato['longitud'] ?>),
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

