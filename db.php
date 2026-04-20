<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbName = 'city_events';

// Connect to MySQL server 
$conn = new mysqli($host, $user, $pass);

if ($conn->connect_error) {
	die('Connection failed: ' . $conn->connect_error);
}

// Create database if it does not exist
$createDbSql = "CREATE DATABASE IF NOT EXISTS `$dbName`";
if (!$conn->query($createDbSql)) {
	die('Database creation failed: ' . $conn->error);
}

// Select the city_events database
if (!$conn->select_db($dbName)) {
	die('Database selection failed: ' . $conn->error);
}

// Create users table if it does not exist
$createUsersTable = "
	CREATE TABLE IF NOT EXISTS users (
		id INT AUTO_INCREMENT PRIMARY KEY,
		username VARCHAR(100) NOT NULL UNIQUE,
		password VARCHAR(255) NOT NULL
	) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
";

if (!$conn->query($createUsersTable)) {
	die('Creating users table failed: ' . $conn->error);
}

// Create events table if it does not exist
$createEventsTable = "
	CREATE TABLE IF NOT EXISTS events (
		id INT AUTO_INCREMENT PRIMARY KEY,
		title VARCHAR(255) NOT NULL,
		description TEXT,
		category VARCHAR(100),
		location VARCHAR(255),
		event_date DATE,
		image VARCHAR(255)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
";

if (!$conn->query($createEventsTable)) {
	die('Creating events table failed: ' . $conn->error);
}

// Insert default admin user if users table is empty
$usersCountResult = $conn->query("SELECT COUNT(*) AS total FROM users");
if (!$usersCountResult) {
	die('Counting users failed: ' . $conn->error);
}

// Fetch the count result and check if it's zero to determine if we need to insert the default admin user
$row = $usersCountResult->fetch_assoc();
if ((int)$row['total'] === 0) {
	$defaultUsername = 'admin';
	$defaultPassword = 'admin';

	$stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
	if (!$stmt) {
		die('Preparing default admin insert failed: ' . $conn->error);
	}

	$stmt->bind_param('ss', $defaultUsername, $defaultPassword);
	if (!$stmt->execute()) {
		die('Inserting default admin failed: ' . $stmt->error);
	}

	$stmt->close();
}

?>
