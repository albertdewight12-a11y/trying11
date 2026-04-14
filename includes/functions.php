<?php
function getAllPosts($pdo) {
    $stmt = $pdo->query("SELECT p.*, u.username, u.profile_pic,
                        a.name as airline_name, t.name as aircraft_name,
                        (SELECT COUNT(*) FROM likes WHERE post_id = p.id) as likes_count,
                        (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comments_count
                        FROM posts p
                        JOIN users u ON p.user_id = u.id
                        LEFT JOIN airlines a ON p.airline_id = a.id
                        LEFT JOIN aircraft_types t ON p.aircraft_type_id = t.id
                        ORDER BY p.created_at DESC");
    return $stmt->fetchAll();
}

function getUserPosts($pdo, $user_id) {
    $stmt = $pdo->prepare("SELECT p.*, u.username, u.profile_pic,
                        a.name as airline_name, t.name as aircraft_name,
                        (SELECT COUNT(*) FROM likes WHERE post_id = p.id) as likes_count,
                        (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comments_count
                        FROM posts p
                        JOIN users u ON p.user_id = u.id
                        LEFT JOIN airlines a ON p.airline_id = a.id
                        LEFT JOIN aircraft_types t ON p.aircraft_type_id = t.id
                        WHERE p.user_id = ?
                        ORDER BY p.created_at DESC");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll();
}

function hasLiked($pdo, $user_id, $post_id) {
    $stmt = $pdo->prepare("SELECT 1 FROM likes WHERE user_id = ? AND post_id = ?");
    $stmt->execute([$user_id, $post_id]);
    return $stmt->fetchColumn();
}

function getComments($pdo, $post_id) {
    $stmt = $pdo->prepare("SELECT c.*, u.username FROM comments c JOIN users u ON c.user_id = u.id WHERE post_id = ? ORDER BY c.created_at ASC");
    $stmt->execute([$post_id]);
    return $stmt->fetchAll();
}

function searchPosts($pdo, $query) {
    $searchTerm = "%$query%";
    $stmt = $pdo->prepare("SELECT p.*, u.username, u.profile_pic,
                        a.name as airline_name, t.name as aircraft_name
                        FROM posts p
                        JOIN users u ON p.user_id = u.id
                        LEFT JOIN airlines a ON p.airline_id = a.id
                        LEFT JOIN aircraft_types t ON p.aircraft_type_id = t.id
                        WHERE p.flight_number LIKE ? OR t.name LIKE ? OR a.name LIKE ?
                        ORDER BY p.created_at DESC");
    $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
    return $stmt->fetchAll();
}

function getAirlines($pdo) {
    return $pdo->query("SELECT * FROM airlines ORDER BY name ASC")->fetchAll();
}

function getAircraftTypes($pdo) {
    return $pdo->query("SELECT * FROM aircraft_types ORDER BY name ASC")->fetchAll();
}
?>
