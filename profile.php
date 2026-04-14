<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

requireLogin();

$profile_id = $_GET['id'] ?? $_SESSION['user_id'];

// Fetch user info
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$profile_id]);
$user = $stmt->fetch();

if (!$user) {
    die("User not found.");
}

$posts = getUserPosts($pdo, $profile_id);

// Fetch follower/following counts
$stmt = $pdo->prepare("SELECT COUNT(*) FROM follows WHERE following_id = ?");
$stmt->execute([$profile_id]);
$followerCount = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM follows WHERE follower_id = ?");
$stmt->execute([$profile_id]);
$followingCount = $stmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $user['username']; ?> - Profile</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/main.js" defer></script>
</head>
<body>
    <?php include 'templates/header.php'; ?>

    <main style="max-width: 900px;">
        <div class="profile-header">
            <img src="uploads/<?php echo $user['profile_pic']; ?>" alt="Profile" class="profile-pic-large">
            <div class="profile-info">
                <h1><?php echo $user['username']; ?></h1>
                <div class="profile-stats">
                    <span><strong><?php echo count($posts); ?></strong> posts</span>
                    <span><strong><?php echo $followerCount; ?></strong> followers</span>
                    <span><strong><?php echo $followingCount; ?></strong> following</span>
                </div>
                <p style="margin-top: 15px;"><?php echo htmlspecialchars($user['bio'] ?? 'No bio yet.'); ?></p>
                <?php if ($profile_id == $_SESSION['user_id']): ?>
                    <button class="btn" style="width: auto; margin-top: 10px;">Edit Profile</button>
                <?php else: ?>
                    <?php
                    $stmt = $pdo->prepare("SELECT 1 FROM follows WHERE follower_id = ? AND following_id = ?");
                    $stmt->execute([$_SESSION['user_id'], $profile_id]);
                    $isFollowing = $stmt->fetchColumn();
                    ?>
                    <button class="btn follow-btn" data-user-id="<?php echo $profile_id; ?>" style="width: auto; margin-top: 10px;">
                        <?php echo $isFollowing ? 'Unfollow' : 'Follow'; ?>
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <div class="post-grid">
            <?php foreach ($posts as $post): ?>
                <div class="grid-item">
                    <?php if ($post['photo_url']): ?>
                        <a href="index.php#post-<?php echo $post['id']; ?>">
                            <img src="uploads/<?php echo $post['photo_url']; ?>" alt="Photo">
                        </a>
                    <?php else: ?>
                        <div style="background: #eee; height: 100%; display: flex; align-items: center; justify-content: center; text-align: center; padding: 10px;">
                            <span>Log: <?php echo $post['flight_number']; ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </main>
</body>
</html>
