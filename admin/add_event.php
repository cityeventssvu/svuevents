<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
	header('Location: login.php');
	exit;
}

require_once '../db.php';

$predefinedCategories = ['Culture', 'Sports', 'Music', 'Family', 'Food', 'Community', 'Business', 'Education'];

$title = '';
$description = '';
$eventDate = '';
$location = '';
$category = '';
$imageUrl = '';
$errorMessage = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$title = trim($_POST['title'] ?? '');
	$description = trim($_POST['description'] ?? '');
	$eventDate = trim($_POST['event_date'] ?? '');
	$location = trim($_POST['location'] ?? '');
	$category = trim($_POST['category'] ?? '');
	$imageUrl = trim($_POST['image_url'] ?? '');

	if ($title === '' || $eventDate === '') {
		$errorMessage = 'Title and date are required';
	} elseif (!in_array($category, $predefinedCategories, true)) {
		$errorMessage = 'Please select a valid category';
	} else {
		$finalImage = $imageUrl;
		// Handle image upload if a file was provided
		if (!isset($_FILES['image_file']) || (int)$_FILES['image_file']['error'] === UPLOAD_ERR_NO_FILE) {
			// no file uploaded; use image URL if provided
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
		// If no image URL provided and no file uploaded, set finalImage to empty string
		if ($errorMessage === '') {
			$stmt = $conn->prepare('INSERT INTO events (title, description, category, location, event_date, image) VALUES (?, ?, ?, ?, ?, ?)');

			if ($stmt) {
				$stmt->bind_param('ssssss', $title, $description, $category, $location, $eventDate, $finalImage);
				if ($stmt->execute()) {
					$stmt->close();
					header('Location: dashboard.php');
					exit;
				}
				$stmt->close();
			}

			$errorMessage = 'Unable to add event right now.';
		}
	}
}
?>
<!doctype html>
<html lang="en" data-bs-theme="dark">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Add Event - Admin</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
	<link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
<main class="content-wrap">
	<div class="container">
		<!-- Header section -->
		<div class="d-flex justify-content-between align-items-center mb-4">
			<h1 class="h3 mb-0">Add New Event</h1>
			<a href="dashboard.php" class="btn btn-outline-secondary btn-sm">Back to Dashboard</a>
		</div>
		<!-- Event form section -->
		<div class="card border-0 shadow-sm p-4">
			<?php if ($errorMessage !== ''): ?>
				<div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($errorMessage); ?></div>
			<?php endif; ?>

			<form method="post" action="add_event.php" enctype="multipart/form-data" class="row g-3">
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
					<label for="image_file" class="form-label">Or Upload Image</label>
					<input type="file" id="image_file" name="image_file" class="form-control" accept=".jpg,.jpeg,.png,.gif,.webp">
				</div>

				<div class="col-12 d-flex gap-2">
					<button type="submit" class="btn btn-info">
						<i class="bi bi-plus-circle me-2"></i>Save Event
					</button>
					<a href="dashboard.php" class="btn btn-outline-secondary">Cancel</a>
				</div>
			</form>
		</div>
	</div>
</main>

<button type="button" class="btn btn-info scroll-top-btn" id="scrollTopButton" aria-label="Scroll to top" hidden>
	<i class="bi bi-arrow-up-short" aria-hidden="true"></i>
</button>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="../assets/js/main.js"></script>
</body>
</html>
