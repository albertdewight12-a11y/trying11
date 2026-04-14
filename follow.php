<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';

if (!isLoggedIn()) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $following_id = $_POST['following_id'];
    $follower_id = $_SESSION['user_id'];

    if ($following_id == $follower_id) {
        echo json_encode(['error' => 'Cannot follow yourself']);
        exit();
    }

    $stmt = $pdo->prepare("SELECT id FROM follows WHERE follower_id = ? AND following_id = ?");
    $stmt->execute([$follower_id, $following_id]);
    $follow = $stmt->fetch();

    if ($follow) {
        $stmt = $pdo->prepare("DELETE FROM follows WHERE id = ?");
        $stmt->execute([$follow['id']]);
        $status = 'unfollowed';
    } else {
        $stmt = $pdo->prepare("INSERT INTO follows (follower_id, following_id) VALUES (?, ?)");
        $stmt->execute([$follower_id, $following_id]);
        $status = 'followed';
    }

    echo json_encode(['status' => $status]);
}
?>
