<!doctype>
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <title>Google Maps - pin cities</title>
    <style type="text/css">
      html, body { height: 100%; margin: 0; padding: 0; }
      #map_canvas { height: 100%; }
    </style>
  </head>
  <body onload="initialize()">
    <div id="map_canvas"></div>

    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&signed_in=true&key=<?php echo env('API_KEY_GOOGLE_MAPS')?>"></script>

    <script type="text/javascript">

      var citylist = [
        ["Roma","Italia"],
        ["Toronto","Canada"],
        ["Inglewood","Estados Unidos"],
      ];

      var geocoder, map;

      function initialize() {
        geocoder = new google.maps.Geocoder();
        var latlng = new google.maps.LatLng(-0.397, 5.644);
        var options = { zoom: 2, center: latlng,  mapTypeId: google.maps.MapTypeId.ROADMAP }
        map = new google.maps.Map(document.getElementById("map_canvas"), options);
        for (var i = 0; i < 10; i++) {
          var address = citylist[i][0] + ', ' + citylist[i][1];
          codeAddress(address);
        }
      };

      function codeAddress(address) {
        geocoder.geocode({ 'address': address }, function(results, status) {
          if (status === google.maps.GeocoderStatus.OK) {
            map.setCenter(results[0].geometry.location);
            var marker = new google.maps.Marker({
                map: map,
                position: results[0].geometry.location
            });
          } else {
            alert("Geocode unsuccessful");
          }
        });
      };

    </script>



  </body>
</html>