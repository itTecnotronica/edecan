<?php
$url = 'https://maps.app.goo.gl/e7y2NJAiZedjFPtN6';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
$finalUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
curl_close($ch);

echo "Final URL: " . $finalUrl . "\n";

// Parse coordinates
if (preg_match('/@(-?\d+\.\d+),(-?\d+\.\d+)/', $finalUrl, $matches)) {
    echo "Lat: " . $matches[1] . "\n";
    echo "Lon: " . $matches[2] . "\n";
} else {
    echo "Could not find coordinates.\n";
}
