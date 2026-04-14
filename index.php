<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

requireLogin();

$posts = getAllPosts($pdo);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkySpotters - Home</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/main.js" defer></script>
</head>
<body>
    <?php include 'templates/header.php'; ?>

    <main>
        <?php foreach ($posts as $post): ?>
            <div class="post">
                <div class="post-header">
                    <img src="uploads/<?php echo $post['profile_pic']; ?>" alt="Avatar" class="user-avatar">
                    <a href="profile.php?id=<?php echo $post['user_id']; ?>" class="username"><?php echo htmlspecialchars($post['username']); ?></a>
                </div>

                <?php if ($post['photo_url']): ?>
                    <img src="uploads/<?php echo $post['photo_url']; ?>" alt="Post Image" class="post-image">
                <?php endif; ?>

                <div class="post-actions">
                    <button class="action-btn like-btn" data-post-id="<?php echo $post['id']; ?>">
                        <?php echo hasLiked($pdo, $current_user_id, $post['id']) ? '❤️' : '🤍'; ?>
                        <?php echo $post['likes_count']; ?>
                    </button>
                    <button class="action-btn comment-toggle" data-post-id="<?php echo $post['id']; ?>">
                        💬 <?php echo $post['comments_count']; ?>
                    </button>
                    <form action="repost.php" method="POST" style="display:inline;">
                        <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                        <button type="submit" class="action-btn">🔄</button>
                    </form>
                </div>

                <div class="post-content">
                    <div class="post-caption">
                        <strong><?php echo htmlspecialchars($post['username']); ?></strong> <?php echo htmlspecialchars($post['caption']); ?>
                    </div>

                    <?php if ($post['flight_number']): ?>
                    <div class="spotting-details">
                        <strong>Spotting Log:</strong>
                        <div>Flight: <?php echo htmlspecialchars($post['flight_number']); ?></div>
                        <div>Aircraft: <?php echo htmlspecialchars($post['aircraft_name']); ?></div>
                        <div>Airline: <?php echo htmlspecialchars($post['airline_name']); ?></div>
                        <div>Route: <?php echo htmlspecialchars($post['arrival']); ?> -> <?php echo htmlspecialchars($post['destination']); ?></div>
                        <div>Location: <?php echo htmlspecialchars($post['location']); ?></div>
                        <div>Date: <?php echo $post['spotting_date']; ?> at <?php echo $post['spotting_time']; ?></div>
                    </div>
                    <?php endif; ?>

                    <div class="post-meta">
                        <?php echo date("F j, Y", strtotime($post['created_at'])); ?>
                    </div>

                    <div id="comments-<?php echo $post['id']; ?>" class="comment-section" style="display:none; margin-top:10px; border-top:1px solid #eee; padding-top:10px;">
                        <div id="comment-list-<?php echo $post['id']; ?>">
                            <?php $comments = getComments($pdo, $post['id']); ?>
                            <?php foreach($comments as $comment): ?>
                                <div style="font-size:0.9rem; margin-bottom:5px;">
                                    <strong><?php echo htmlspecialchars($comment['username']); ?></strong> <?php echo htmlspecialchars($comment['comment_text']); ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <form class="comment-form" method="POST" style="margin-top:10px; display:flex; gap:5px;">
                            <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                            <input type="text" name="comment_text" placeholder="Add a comment..." required style="flex:1; padding:5px; border:1px solid #dbdbdb; border-radius:4px;">
                            <button type="submit" class="btn" style="width:auto; padding:5px 10px;">Post</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </main>
</body>
</html>
