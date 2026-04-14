# SkySpotters - Airplane Spotting & Photography Social Media

SkySpotters is a complex, feature-rich web application built with HTML, CSS, JavaScript, and PHP. It is designed for aviation enthusiasts to share photos, maintain spotting logs, and track live aircraft.

## Features
- **Social Feed**: View photos and spotting logs from other users.
- **Photo Uploads**: Share your airplane photography with captions.
- **Spotting Logs**: Detailed logs including flight number, aircraft type, airline, and route.
- **OpenSky Integration**: Auto-fill flight details using the OpenSky Network API.
- **Live Aircraft Map**: Real-time aircraft positions using Leaflet.js and OpenSky API.
- **User Profiles**: Track your posts and follows.
- **Search**: Explore posts by aircraft type, airline, or flight number.

## Deployment on XAMPP

Follow these steps to get SkySpotters running on your local machine:

### 1. File Placement
- Download or clone this repository.
- Move the project folder into your XAMPP's `htdocs` directory (usually `C:\xampp\htdocs\skyspotters`).

### 2. Database Setup
- Open your browser and go to `http://localhost/phpmyadmin`.
- Click on the **SQL** tab.
- Open the `database.sql` file from the project in a text editor, copy all its content, and paste it into the phpMyAdmin SQL box.
- Click **Go** to create the database and populate it with seed data.

### 3. Folder Permissions
- Ensure the `uploads/` directory in the project folder has write permissions (on Windows, this is usually default; on Linux/Mac, run `chmod 777 uploads/`).
- Make sure a default profile picture named `default_profile.png` exists in the `uploads/` folder or update the database default.

### 4. Open in Dreamweaver
- Open Adobe Dreamweaver.
- Go to **Site > New Site**.
- Set the Site Name to "SkySpotters" and the Local Site Folder to where you placed the files in `htdocs`.
- You can now edit the code and preview it.

### 5. Access the Website
- Start the **Apache** and **MySQL** modules in your XAMPP Control Panel.
- Open your browser and navigate to `http://localhost/skyspotters`.

### 6. Login Credentials (Sample Data)
- **Username**: `jules_spotter`
- **Password**: `password`

## Technologies Used
- **Frontend**: Vanilla HTML5, CSS3 (Responsive Design), JavaScript.
- **Backend**: PHP 7.4+ (PDO for database security).
- **Database**: MySQL.
- **APIs**: OpenSky Network (Flight Data), Leaflet.js (Live Map).
