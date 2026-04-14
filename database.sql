-- Airplane Spotting & Photography Social Media Database Schema

CREATE DATABASE IF NOT EXISTS sky_spotters;
USE sky_spotters;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    bio TEXT,
    profile_pic VARCHAR(255) DEFAULT 'default_profile.png',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Airlines Table
CREATE TABLE IF NOT EXISTS airlines (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    iata_code VARCHAR(3) UNIQUE
);

-- Aircraft Types Table
CREATE TABLE IF NOT EXISTS aircraft_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    icao_code VARCHAR(4) UNIQUE
);

-- Posts Table (Combining Photo and Spotting Log)
CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    photo_url VARCHAR(255),
    caption TEXT,
    flight_number VARCHAR(20),
    tail_number VARCHAR(20),
    aircraft_type_id INT,
    airline_id INT,
    location VARCHAR(100),
    arrival VARCHAR(10),
    destination VARCHAR(10),
    spotting_date DATE,
    spotting_time TIME,
    is_repost BOOLEAN DEFAULT FALSE,
    original_post_id INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (aircraft_type_id) REFERENCES aircraft_types(id) ON DELETE SET NULL,
    FOREIGN KEY (airline_id) REFERENCES airlines(id) ON DELETE SET NULL,
    FOREIGN KEY (original_post_id) REFERENCES posts(id) ON DELETE CASCADE
);

-- Likes Table
CREATE TABLE IF NOT EXISTS likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    post_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_like (user_id, post_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
);

-- Comments Table
CREATE TABLE IF NOT EXISTS comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    post_id INT NOT NULL,
    comment_text TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
);

-- Follows Table
CREATE TABLE IF NOT EXISTS follows (
    id INT AUTO_INCREMENT PRIMARY KEY,
    follower_id INT NOT NULL,
    following_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_follow (follower_id, following_id),
    FOREIGN KEY (follower_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (following_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Seed Data for Airlines
INSERT INTO airlines (name, iata_code) VALUES
('American Airlines', 'AA'),
('Delta Air Lines', 'DL'),
('United Airlines', 'UA'),
('Southwest Airlines', 'WN'),
('Lufthansa', 'LH'),
('British Airways', 'BA'),
('Emirates', 'EK'),
('Qatar Airways', 'QR'),
('Air France', 'AF'),
('Singapore Airlines', 'SQ'),
('Cathay Pacific', 'CX'),
('KLM', 'KL'),
('Turkish Airlines', 'TK'),
('Ryanair', 'FR'),
('EasyJet', 'U2');

-- Seed Data for Aircraft Types
INSERT INTO aircraft_types (name, icao_code) VALUES
('Boeing 737-800', 'B738'),
('Boeing 747-400', 'B744'),
('Boeing 777-300ER', 'B77W'),
('Boeing 787-9 Dreamliner', 'B789'),
('Airbus A320', 'A320'),
('Airbus A321neo', 'A321'),
('Airbus A350-900', 'A359'),
('Airbus A380-800', 'A388'),
('Embraer E190', 'E190'),
('Bombardier CRJ-900', 'CRJ9'),
('Cessna 172', 'C172'),
('Airbus A220-300', 'BCS3');
-- Sample Users (Password is 'password' for all)
INSERT INTO users (username, email, password, bio, profile_pic) VALUES
('jules_spotter', 'jules@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Aviation enthusiast and photographer.', 'default_profile.png'),
('sky_high', 'sky@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Capturing the giants in the sky.', 'default_profile.png'),
('pilot_pete', 'pete@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Pilot and part-time spotter.', 'default_profile.png');

-- Sample Posts
INSERT INTO posts (user_id, caption, flight_number, tail_number, aircraft_type_id, airline_id, location, arrival, destination, spotting_date, spotting_time) VALUES
(1, 'Beautiful day for spotting at LAX!', 'AA123', 'N123AA', 1, 1, 'Los Angeles International Airport', 'KLAX', 'KJFK', '2023-10-01', '14:30:00'),
(2, 'Caught the Queen of the Skies today!', 'LH456', 'D-ABYA', 2, 5, 'Frankfurt Airport', 'EDDF', 'KJFK', '2023-10-02', '10:15:00'),
(3, 'Dreamliner landing at Heathrow.', 'BA789', 'G-ZBJA', 4, 6, 'London Heathrow', 'EGLL', 'KORD', '2023-10-03', '16:45:00');
