<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';

requireLogin();

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Fetch current user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $bio = trim($_POST['bio']);

    // Basic validation
    if (empty($username)) {
        $error = "Username cannot be empty.";
    } else {
        // Handle Profile Picture Upload
        $profile_pic = $user['profile_pic'];
        if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['profile_pic']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            if (in_array($ext, $allowed)) {
                $target_dir = "uploads/";
                $file_name = "profile_" . $user_id . "_" . uniqid() . "." . $ext;
                $target_file = $target_dir . $file_name;

                if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
                    // Delete old pic if it's not the default
                    if ($user['profile_pic'] != 'default_profile.png' && file_exists('uploads/' . $user['profile_pic'])) {
                        unlink('uploads/' . $user['profile_pic']);
                    }
                    $profile_pic = $file_name;
                }
            } else {
                $error = "Invalid file type. Only JPG, PNG, and GIF allowed.";
            }
        }

        // Update database
        try {
            $stmt = $pdo->prepare("UPDATE users SET username = ?, bio = ?, profile_pic = ? WHERE id = ?");
            $stmt->execute([$username, $bio, $profile_pic, $user_id]);
            $_SESSION['username'] = $username;
            $success = "Profile updated successfully!";
            // Refresh local user data
            $user['username'] = $username;
            $user['bio'] = $bio;
            $user['profile_pic'] = $profile_pic;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $error = "Username already taken.";
            } else {
                $error = "An error occurred. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - SkySpotters</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'templates/header.php'; ?>

    <main>
        <div class="auth-container" style="max-width: 500px; margin-top: 20px;">
            <h2>Edit Profile</h2>
            <?php if ($error): ?>
                <p style="color: red;"><?php echo $error; ?></p>
            <?php endif; ?>
            <?php if ($success): ?>
                <p style="color: green;"><?php echo $success; ?></p>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" style="text-align: left;">
                <div style="text-align: center; margin-bottom: 20px;">
                    <img src="uploads/<?php echo $user['profile_pic']; ?>" alt="Current Profile" class="user-avatar" style="width: 100px; height: 100px;">
                    <div class="form-group" style="margin-top: 10px;">
                        <label>Change Profile Photo</label>
                        <input type="file" name="profile_pic" accept="image/*">
                    </div>
                </div>

                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Bio</label>
                    <textarea name="bio" rows="4"><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                </div>

                <button type="submit" class="btn">Save Changes</button>
                <p style="margin-top: 15px; text-align: center;"><a href="profile.php">Back to Profile</a></p>
            </form>
        </div>
    </main>
</body>
</html>
