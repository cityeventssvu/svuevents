<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
	header('Location: login.php');
	exit;
}

require_once '../db.php';

$events = [];
$eventsSql = '
	SELECT id, title, event_date, category, location
	FROM events
	ORDER BY event_date DESC, id DESC
';

// Fetch events for dashboard
$eventsResult = $conn->query($eventsSql);
if ($eventsResult) {
	while ($row = $eventsResult->fetch_assoc()) {
		$events[] = $row;
	}
}
?>
<!doctype html>
<html lang="en" data-bs-theme="dark">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Admin Dashboard - City Events</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
	<link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark sticky-top" id="siteNavbar">
	<!-- Admin navbar section -->
	<div class="container-fluid px-3 px-lg-4">
		<a class="navbar-brand" href="dashboard.php">Admin Panel</a>
		<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar" aria-controls="adminNavbar" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse" id="adminNavbar">
			<ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
				<li class="nav-item">
					<span class="nav-link">Signed in as <?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Admin'); ?></span>
				</li>
				<li class="nav-item">
					<a class="btn btn-outline-secondary btn-sm" href="../index.php">View Site</a>
				</li>
				<li class="nav-item">
					<button class="btn btn-outline-light btn-sm theme-toggle" id="themeToggle" type="button" aria-label="Toggle dark mode" aria-pressed="true">
						<i class="bi bi-moon-stars-fill me-2" id="themeToggleIcon"></i>
						<span id="themeToggleText">Dark</span>
					</button>
				</li>
				<li class="nav-item">
					<a class="btn btn-outline-danger btn-sm" href="logout.php">
						<i class="bi bi-box-arrow-right me-1"></i>Logout
					</a>
				</li>
			</ul>
		</div>
	</div>
</nav>

<main class="content-wrap">
	<!-- Dashboard content section -->
	<div class="container-fluid px-3 px-lg-4">
		<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
			<div>
				<h1 class="h3 mb-1">Events Dashboard</h1>
				<p class="text-secondary mb-0">Manage all city events from one place.</p>
			</div>
			<a href="add_event.php" class="btn btn-info">
				<i class="bi bi-plus-circle me-2"></i>Add New Event
			</a>
		</div>

		<div class="card border-0 shadow-sm">
			<div class="card-body p-0">
				<div class="table-responsive">
					<table class="table table-hover align-middle mb-0">
						<thead>
							<tr>
								<th scope="col">Title</th>
								<th scope="col">Date</th>
								<th scope="col">Category</th>
								<th scope="col">Location</th>
								<th scope="col" class="text-end">Actions</th>
							</tr>
						</thead>
						<tbody>
							<?php if (!empty($events)): ?>
								<?php foreach ($events as $event): ?>
									<tr>
										<td class="fw-semibold"><?php echo htmlspecialchars($event['title']); ?></td>
										<td><?php echo !empty($event['event_date']) ? htmlspecialchars(date('M d, Y', strtotime($event['event_date']))) : 'Date TBD'; ?></td>
										<td><?php echo htmlspecialchars($event['category'] ?: 'General'); ?></td>
										<td><?php echo htmlspecialchars($event['location'] ?: 'City Venue'); ?></td>
										<td class="text-end">
											<div class="btn-group btn-group-sm" role="group" aria-label="Event actions">
												<a href="edit_event.php?id=<?php echo (int)$event['id']; ?>" class="btn btn-outline-info">Edit</a>
												<a href="delete_event.php?id=<?php echo (int)$event['id']; ?>" class="btn btn-outline-danger">Delete</a>
											</div>
										</td>
									</tr>
								<?php endforeach; ?>
							<?php else: ?>
								<tr>
									<td colspan="5" class="text-center text-secondary py-4">No events found. Click "Add New Event" to create your first event.</td>
								</tr>
							<?php endif; ?>
						</tbody>
					</table>
				</div>
			</div>
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
