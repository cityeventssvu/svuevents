<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
	header('Location: login.php');
	exit;
}

require_once '../db.php';

$predefinedCategories = ['Culture', 'Sports', 'Music', 'Family', 'Food', 'Community', 'Business', 'Education'];

$eventId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($eventId <= 0) {
	header('Location: dashboard.php');
	exit;
}

$event = null;
$fetchStmt = $conn->prepare('SELECT id, title, description, category, location, event_date, image FROM events WHERE id = ? LIMIT 1');
if ($fetchStmt) {
	$fetchStmt->bind_param('i', $eventId);
	$fetchStmt->execute();
	$result = $fetchStmt->get_result();
	$event = $result ? $result->fetch_assoc() : null;
	$fetchStmt->close();
}

if (!$event) {
	header('Location: dashboard.php');
	exit;
}

$title = $event['title'];
$description = (string)$event['description'];
$eventDate = (string)$event['event_date'];
$location = (string)$event['location'];
$category = (string)$event['category'];
$imageUrl = (string)$event['image'];
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$title = trim($_POST['title'] ?? '');
	$description = trim($_POST['description'] ?? '');
	$eventDate = trim($_POST['event_date'] ?? '');
	$location = trim($_POST['location'] ?? '');
	$category = trim($_POST['category'] ?? '');
	$manualImageUrl = trim($_POST['image_url'] ?? '');
	$finalImage = $manualImageUrl !== '' ? $manualImageUrl : $imageUrl;

	if ($title === '' || $eventDate === '') {
		$errorMessage = 'Title and date are required.';
	} elseif (!in_array($category, $predefinedCategories, true)) {
		$errorMessage = 'Please select a valid category.';
	} else {
		if (!isset($_FILES['image_file']) || (int)$_FILES['image_file']['error'] === UPLOAD_ERR_NO_FILE) {
			// no file uploaded; keep existing image or image URL input
		} elseif ((int)$_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
			$uploadDir = '../uploads/';
			if (!is_dir($uploadDir)) {
				mkdir($uploadDir, 0777, true);
			}

			$originalName = $_FILES['image_file']['name'];
			$extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
			$allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

			if (!in_array($extension, $allowedExtensions, true)) {
				$errorMessage = 'Unsupported image file type.';
			} else {
				$filename = 'event_' . time() . '_' . mt_rand(1000, 9999) . '.' . $extension;
				$targetPath = $uploadDir . $filename;

				if (move_uploaded_file($_FILES['image_file']['tmp_name'], $targetPath)) {
					$finalImage = 'uploads/' . $filename;
				} else {
					$errorMessage = 'Image upload failed. Please try again.';
				}
			}
		} else {
			$errorMessage = 'Image upload failed. Please try again.';
		}

		if ($errorMessage === '') {
			$updateStmt = $conn->prepare('UPDATE events SET title = ?, description = ?, category = ?, location = ?, event_date = ?, image = ? WHERE id = ?');
			if ($updateStmt) {
				$updateStmt->bind_param('ssssssi', $title, $description, $category, $location, $eventDate, $finalImage, $eventId);
				if ($updateStmt->execute()) {
					$updateStmt->close();
					header('Location: dashboard.php');
					exit;
				}
				$updateStmt->close();
			}

			$errorMessage = 'Unable to update event right now.';
		}

		$imageUrl = $finalImage;
	}
}
?>
<!doctype html>
<html lang="en" data-bs-theme="dark">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Edit Event - Admin</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
	<link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
<main class="content-wrap">
	<div class="container">
		<div class="d-flex justify-content-between align-items-center mb-4">
			<h1 class="h3 mb-0">Edit Event</h1>
			<a href="dashboard.php" class="btn btn-outline-secondary btn-sm">Back to Dashboard</a>
		</div>

		<div class="card border-0 shadow-sm p-4">
			<?php if ($errorMessage !== ''): ?>
				<div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($errorMessage); ?></div>
			<?php endif; ?>

			<form method="post" action="edit_event.php?id=<?php echo (int)$eventId; ?>" enctype="multipart/form-data" class="row g-3">
				<div class="col-12">
					<label for="title" class="form-label">Title</label>
					<input type="text" id="title" name="title" class="form-control" value="<?php echo htmlspecialchars($title); ?>" required>
				</div>

				<div class="col-md-6">
					<label for="event_date" class="form-label">Date</label>
					<input type="date" id="event_date" name="event_date" class="form-control" value="<?php echo htmlspecialchars($eventDate); ?>" required>
				</div>

				<div class="col-md-6">
					<label for="category" class="form-label">Category</label>
					<select id="category" name="category" class="form-select" required>
						<option value="">Select a category</option>
						<?php foreach ($predefinedCategories as $option): ?>
							<option value="<?php echo htmlspecialchars($option); ?>" <?php echo $category === $option ? 'selected' : ''; ?>><?php echo htmlspecialchars($option); ?></option>
						<?php endforeach; ?>
					</select>
				</div>

				<div class="col-12">
					<label for="location" class="form-label">Location</label>
					<input type="text" id="location" name="location" class="form-control" value="<?php echo htmlspecialchars($location); ?>" placeholder="City Hall, Main Arena...">
				</div>

				<div class="col-12">
					<label for="description" class="form-label">Description</label>
					<textarea id="description" name="description" rows="5" class="form-control" placeholder="Enter full event details..."><?php echo htmlspecialchars($description); ?></textarea>
				</div>

				<div class="col-md-6">
					<label for="image_url" class="form-label">Image URL</label>
					<input type="url" id="image_url" name="image_url" class="form-control" value="<?php echo htmlspecialchars($imageUrl); ?>" placeholder="https://example.com/image.jpg">
				</div>

				<div class="col-md-6">
					<label for="image_file" class="form-label">Or Upload New Image</label>
					<input type="file" id="image_file" name="image_file" class="form-control" accept=".jpg,.jpeg,.png,.gif,.webp">
				</div>

				<div class="col-12 d-flex gap-2">
					<button type="submit" class="btn btn-info">
						<i class="bi bi-save me-2"></i>Update Event
					</button>
					<a href="dashboard.php" class="btn btn-outline-secondary">Cancel</a>
				</div>
			</form>
		</div>
	</div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="../assets/js/main.js"></script>
</body>
</html>
