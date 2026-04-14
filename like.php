<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';

if (!isLoggedIn()) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $post_id = $_POST['post_id'];
    $user_id = $_SESSION['user_id'];

    // Check if already liked
    $stmt = $pdo->prepare("SELECT id FROM likes WHERE user_id = ? AND post_id = ?");
    $stmt->execute([$user_id, $post_id]);
    $like = $stmt->fetch();

    if ($like) {
        // Unlike
        $stmt = $pdo->prepare("DELETE FROM likes WHERE id = ?");
        $stmt->execute([$like['id']]);
        $status = 'unliked';
    } else {
        // Like
        $stmt = $pdo->prepare("INSERT INTO likes (user_id, post_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $post_id]);
        $status = 'liked';
    }

    // Get new like count
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM likes WHERE post_id = ?");
    $stmt->execute([$post_id]);
    $count = $stmt->fetchColumn();

    echo json_encode(['status' => $status, 'count' => $count]);
}
?>
