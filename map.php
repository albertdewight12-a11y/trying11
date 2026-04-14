<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';

requireLogin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Map - SkySpotters</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Leaflet.js CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <style>
        #map { height: calc(100vh - 120px); width: 100%; }
        .airplane-marker {
            transition: transform 0.5s linear;
        }
    </style>
</head>
<body>
    <?php include 'templates/header.php'; ?>

    <main style="max-width: 100%; padding: 20px;">
        <h2>Live Aircraft Traffic</h2>
        <p>Real-time data from OpenSky Network API.</p>
        <div id="map"></div>
    </main>

    <!-- Leaflet.js JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        var map = L.map('map').setView([20, 0], 2);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        var markers = {};

        // Simple airplane emoji as icon
        var airplaneIcon = L.divIcon({
            className: 'airplane-marker',
            html: '<div style="font-size: 24px; transform: rotate(45deg);">✈️</div>',
            iconSize: [24, 24],
            iconAnchor: [12, 12]
        });

        function fetchPlanes() {
            // Get bounds of current map view to limit API request
            var bounds = map.getBounds();
            var lamin = bounds.getSouth();
            var lomin = bounds.getWest();
            var lamax = bounds.getNorth();
            var lomax = bounds.getEast();

            fetch(`proxy.php?lamin=${lamin}&lomin=${lomin}&lamax=${lamax}&lomax=${lomax}`)
                .then(response => response.json())
                .then(data => {
                    if (data.states) {
                        data.states.forEach(plane => {
                            var icao = plane[0];
                            var callsign = plane[1] ? plane[1].trim() : "Unknown";
                            var lat = plane[6];
                            var lon = plane[5];
                            var track = plane[10];

                            if (lat && lon) {
                                if (markers[icao]) {
                                    markers[icao].setLatLng([lat, lon]);
                                    if (track) {
                                        markers[icao].getElement().style.transform += ` rotate(${track}deg)`;
                                    }
                                } else {
                                    markers[icao] = L.marker([lat, lon], {
                                        title: callsign,
                                        icon: airplaneIcon
                                    }).addTo(map)
                                    .bindPopup(`<b>Flight: ${callsign}</b><br>ICAO: ${icao}<br>Heading: ${track ? track + '°' : 'N/A'}`);

                                    if (track) {
                                        markers[icao].on('add', function() {
                                            this.getElement().style.transform += ` rotate(${track}deg)`;
                                        });
                                    }
                                }
                            }
                        });
                    }
                })
                .catch(err => console.error("Error fetching aircraft:", err));
        }

        // Fetch initial data
        fetchPlanes();

        // Refresh every 10 seconds
        setInterval(fetchPlanes, 10000);

        // Update on map move
        map.on('moveend', fetchPlanes);
    </script>
</body>
</html>
