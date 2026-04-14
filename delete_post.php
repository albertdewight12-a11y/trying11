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
        $photo_url = $post['photo_url'];

        // Check if other posts are using this same photo (reposts or original)
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE photo_url = ? AND id != ?");
        $stmt->execute([$photo_url, $post_id]);
        $usage_count = $stmt->fetchColumn();

        // Only delete the file if no other posts are using it
        if ($usage_count == 0 && $photo_url && file_exists('uploads/' . $photo_url)) {
            // Keep default profile pic and placeholder safe just in case
            if ($photo_url != 'default_profile.png') {
                unlink('uploads/' . $photo_url);
            }
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
