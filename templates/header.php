<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';

// If user is not logged in, we can still show the landing page or redirect
// For this app, let's require login for the feed
if (!isLoggedIn()) {
    header("Location: login.php");
    exit();
}

$current_user_id = $_SESSION['user_id'];
?>
<nav>
    <a href="index.php" class="nav-brand">SkySpotters</a>
    <div class="nav-links">
        <a href="index.php">Home</a>
        <a href="search.php">Explore</a>
        <a href="map.php">Live Map</a>
        <a href="upload.php">Upload</a>
        <a href="profile.php?id=<?php echo $current_user_id; ?>">Profile</a>
        <a href="logout.php">Logout</a>
    </div>
</nav>
