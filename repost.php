<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $original_post_id = $_POST['post_id'];
    $user_id = $_SESSION['user_id'];

    // Fetch original post details
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
    $stmt->execute([$original_post_id]);
    $original = $stmt->fetch();

    if ($original) {
        $stmt = $pdo->prepare("INSERT INTO posts (user_id, photo_url, caption, flight_number, tail_number, aircraft_type_id, airline_id, location, arrival, destination, spotting_date, spotting_time, is_repost, original_post_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, TRUE, ?)");
        $stmt->execute([
            $user_id,
            $original['photo_url'],
            $original['caption'],
            $original['flight_number'],
            $original['tail_number'],
            $original['aircraft_type_id'],
            $original['airline_id'],
            $original['location'],
            $original['arrival'],
            $original['destination'],
            $original['spotting_date'],
            $original['spotting_time'],
            $original_post_id
        ]);
        header("Location: index.php");
        exit();
    }
}
?>
