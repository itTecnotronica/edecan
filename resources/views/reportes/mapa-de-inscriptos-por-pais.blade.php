


<!DOCTYPE html>
<html>
<head>
      <meta name="description" content="Cursos on line Gratuitos en todo el mundo.">
      <meta property="og:title" content="Mapa de personas inscriptas en todo el mundo">
      <meta property="og:url" content="https://ac.gnosis.is/mapa-de-inscriptos" >
      <meta property="og:description" content="Mapa de personas inscriptas en todo el mundo.">
      <meta property="og:image" content="<?php echo env('PATH_PUBLIC')?>/img/mapasoldeacuario.jpg">
  <title>Mapa de personas inscriptas en todo el mundo</title>
</head>
<body>


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

            foreach ($Paises as $Pais) {
              $info = '<h3>'.__('Pais').': '.$Pais->pais.'</h3>';
              if ($mostrar_inscriptos == 'SI') {
                $info .= 'Inscriptos: '.$Pais->cant;
              }
              $info = str_replace('"', '', $info);
              $ciudad = $Pais->pais;
              $id = $Pais->id;


          ?>

          var infowindow_<?php echo $Pais->id ?> = new google.maps.InfoWindow({
            content: "<?php echo $info ?>"
          });

                
          <?php if ($Pais->latitud <> '' and is_numeric($Pais->latitud) and is_numeric($Pais->longitud)) { ?>
            var marker_<?php echo $id ?> = new google.maps.Marker({
              position: new google.maps.LatLng(<?php echo $Pais->latitud ?>, <?php echo $Pais->longitud ?>),
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

                console.log("UPDATE paises SET latitud = '"+results[0].geometry.location.lat()+"', longitud = '"+results[0].geometry.location.lng()+"' WHERE id = <?php echo $id ?>;");

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


</body>
</html>