<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
	header('Location: login.php');
	exit;
}

require_once '../db.php';

$eventId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($eventId > 0) {
	$stmt = $conn->prepare('DELETE FROM events WHERE id = ?');
	if ($stmt) {
		$stmt->bind_param('i', $eventId);
		$stmt->execute();
		$stmt->close();
	}
}

header('Location: dashboard.php');
exit;
?>
