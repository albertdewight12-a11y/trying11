<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

requireLogin();

$query = $_GET['q'] ?? '';
$results = [];

if ($query) {
    $results = searchPosts($pdo, $query);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explore - SkySpotters</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'templates/header.php'; ?>

    <main>
        <div class="auth-container" style="border: none; background: transparent; margin: 20px auto;">
            <form method="GET">
                <div class="form-group" style="display: flex; gap: 10px;">
                    <input type="text" name="q" placeholder="Search flight #, aircraft, or airline..." value="<?php echo htmlspecialchars($query); ?>">
                    <button type="submit" class="btn" style="width: auto;">Search</button>
                </div>
            </form>
        </div>

        <?php if ($query): ?>
            <h3>Search results for "<?php echo htmlspecialchars($query); ?>"</h3>
            <div style="margin-top: 20px;">
                <?php foreach ($results as $post): ?>
                    <div class="post">
                        <!-- Simplified post view for search results -->
                        <div class="post-header">
                            <img src="uploads/<?php echo $post['profile_pic']; ?>" alt="Avatar" class="user-avatar">
                            <a href="profile.php?id=<?php echo $post['user_id']; ?>" class="username"><?php echo htmlspecialchars($post['username']); ?></a>
                        </div>
                        <?php if ($post['photo_url']): ?>
                            <img src="uploads/<?php echo $post['photo_url']; ?>" alt="Post Image" class="post-image">
                        <?php endif; ?>
                        <div class="post-content">
                            <p><strong><?php echo $post['airline_name']; ?></strong> - <?php echo $post['aircraft_name']; ?> (<?php echo $post['flight_number']; ?>)</p>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($results)): ?>
                    <p>No results found.</p>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <p>Try searching for "Boeing", "Delta", or a flight number like "AA123".</p>
        <?php endif; ?>
    </main>
</body>
</html>
