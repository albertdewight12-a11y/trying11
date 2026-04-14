<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $post_id = $_POST['post_id'];
    $user_id = $_SESSION['user_id'];

    // Ensure the post belongs to the user
    $stmt = $pdo->prepare("SELECT photo_url FROM posts WHERE id = ? AND user_id = ?");
    $stmt->execute([$post_id, $user_id]);
    $post = $stmt->fetch();

    if ($post) {
        // Delete the photo file if it exists
        if ($post['photo_url'] && file_exists('uploads/' . $post['photo_url'])) {
            unlink('uploads/' . $post['photo_url']);
        }

        // Delete from database
        $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
        $stmt->execute([$post_id]);

        header("Location: index.php?deleted=1");
        exit();
    } else {
        die("Unauthorized or post not found.");
    }
}
?>
