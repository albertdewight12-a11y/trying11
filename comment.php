<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';

if (!isLoggedIn()) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $post_id = $_POST['post_id'];
    $comment_text = trim($_POST['comment_text']);
    $user_id = $_SESSION['user_id'];

    if (!empty($comment_text)) {
        $stmt = $pdo->prepare("INSERT INTO comments (user_id, post_id, comment_text) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $post_id, $comment_text]);

        echo json_encode([
            'status' => 'success',
            'username' => $_SESSION['username'],
            'comment_text' => htmlspecialchars($comment_text)
        ]);
    } else {
        echo json_encode(['error' => 'Empty comment']);
    }
}
?>
