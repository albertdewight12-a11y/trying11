<?php
header('Content-Type: application/json');

$lamin = $_GET['lamin'] ?? null;
$lomin = $_GET['lomin'] ?? null;
$lamax = $_GET['lamax'] ?? null;
$lomax = $_GET['lomax'] ?? null;

$url = "https://opensky-network.org/api/states/all";
if ($lamin && $lomin && $lamax && $lomax) {
    $url .= "?lamin=$lamin&lomin=$lomin&lamax=$lamax&lomax=$lomax";
}

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_USERAGENT, 'SkySpotters/1.0');
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpcode == 200) {
    echo $response;
} else {
    echo json_encode(['error' => 'Failed to fetch data from OpenSky', 'code' => $httpcode]);
}
?>
