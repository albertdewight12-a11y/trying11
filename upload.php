<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

requireLogin();

$airlines = getAirlines($pdo);
$aircraft_types = getAircraftTypes($pdo);
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $caption = $_POST['caption'];
    $flight_number = $_POST['flight_number'];
    $tail_number = $_POST['tail_number'];
    $aircraft_type_id = $_POST['aircraft_type_id'];
    $airline_id = $_POST['airline_id'];
    $location = $_POST['location'];
    $arrival = $_POST['arrival'];
    $destination = $_POST['destination'];
    $spotting_date = $_POST['spotting_date'];
    $spotting_time = $_POST['spotting_time'];
    $user_id = $_SESSION['user_id'];

    $photo_url = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $target_dir = "uploads/";
        $file_extension = pathinfo($_FILES["photo"]["name"], PATHINFO_EXTENSION);
        $file_name = uniqid() . "." . $file_extension;
        $target_file = $target_dir . $file_name;

        if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
            $photo_url = $file_name;
        }
    }

    $stmt = $pdo->prepare("INSERT INTO posts (user_id, photo_url, caption, flight_number, tail_number, aircraft_type_id, airline_id, location, arrival, destination, spotting_date, spotting_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$user_id, $photo_url, $caption, $flight_number, $tail_number, $aircraft_type_id, $airline_id, $location, $arrival, $destination, $spotting_date, $spotting_time])) {
        header("Location: index.php");
        exit();
    } else {
        $error = "Failed to upload post.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Spotting Log - SkySpotters</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'templates/header.php'; ?>

    <main>
        <div class="auth-container" style="max-width: 600px; margin-top: 20px;">
            <h2>Upload Photo & Log</h2>
            <p>Share your latest catch with the community.</p>

            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Photo</label>
                    <input type="file" name="photo" accept="image/*">
                </div>
                <div class="form-group">
                    <label>Caption</label>
                    <textarea name="caption" rows="3"></textarea>
                </div>

                <hr style="margin: 20px 0;">
                <h3>Spotting Log (Optional)</h3>

                <div class="form-group">
                    <label>Flight Number</label>
                    <div style="display: flex; gap: 10px;">
                        <input type="text" name="flight_number" id="flight_number" placeholder="e.g. AA123">
                        <button type="button" id="fetch_flight" class="btn" style="width: auto;">Fetch Info</button>
                    </div>
                    <small>Click "Fetch Info" to auto-fill details from OpenSky API.</small>
                </div>

                <div class="form-group">
                    <label>Tail Number</label>
                    <input type="text" name="tail_number" id="tail_number">
                </div>

                <div class="form-group">
                    <label>Aircraft Type</label>
                    <select name="aircraft_type_id" id="aircraft_type_id">
                        <option value="">Select Aircraft</option>
                        <?php foreach ($aircraft_types as $type): ?>
                            <option value="<?php echo $type['id']; ?>"><?php echo $type['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Airline</label>
                    <select name="airline_id" id="airline_id">
                        <option value="">Select Airline</option>
                        <?php foreach ($airlines as $airline): ?>
                            <option value="<?php echo $airline['id']; ?>"><?php echo $airline['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Location (Airport/City)</label>
                    <input type="text" name="location" id="location">
                </div>

                <div style="display: flex; gap: 10px;">
                    <div class="form-group" style="flex: 1;">
                        <label>Arrival (ICAO)</label>
                        <input type="text" name="arrival" id="arrival" placeholder="e.g. KJFK">
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label>Destination (ICAO)</label>
                        <input type="text" name="destination" id="destination" placeholder="e.g. EGLL">
                    </div>
                </div>

                <div style="display: flex; gap: 10px;">
                    <div class="form-group" style="flex: 1;">
                        <label>Date</label>
                        <input type="date" name="spotting_date" value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label>Time</label>
                        <input type="time" name="spotting_time" value="<?php echo date('H:i'); ?>">
                    </div>
                </div>

                <button type="submit" class="btn">Post to Feed</button>
            </form>
        </div>
    </main>

    <script>
        document.getElementById('fetch_flight').addEventListener('click', function() {
            const flight = document.getElementById('flight_number').value.trim().toUpperCase();
            if (!flight) {
                alert('Please enter a flight number or callsign (e.g. DAL123).');
                return;
            }

            // OpenSky /states/all can take a 'callsign' filter
            // Note: The public API often requires callsigns to be exactly 8 characters (padded with spaces)
            // and it only works for aircraft currently in the air.

            // Note: The public OpenSky API is very limited. Fetching /states/all is heavy.
            // Ideally, we'd use a more specific API if available.
            fetch(`https://opensky-network.org/api/states/all`)
                .then(response => {
                    if (!response.ok) throw new Error('API limit reached or error');
                    return response.json();
                })
                .then(data => {
                    if (!data.states) {
                        alert('No live data available at the moment.');
                        return;
                    }
                    const flightData = data.states.find(s => s[1] && s[1].trim() === flight);
                    if (flightData) {
                        const lat = flightData[6];
                        const lon = flightData[5];
                        const origin = flightData[2];
                        document.getElementById('location').value = `Over ${origin} (${lat.toFixed(2)}, ${lon.toFixed(2)})`;
                        document.getElementById('tail_number').value = flightData[0].toUpperCase(); // ICAO24 as fallback for tail
                        alert('Live flight found! Location and ICAO24 populated.');
                    } else {
                        alert('Flight not found in real-time. Make sure it is currently airborne and you use the callsign (e.g., AAL123 instead of AA123).');
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Error connecting to OpenSky API.');
                });
        });
    </script>
</body>
</html>
