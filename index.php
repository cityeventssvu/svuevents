<?php
$pageTitle = 'Svu City Events - Home';
require_once 'db.php';

$featuredEvents = [];
$featuredSql = "
	SELECT id, title, description, category, location, event_date, image
	FROM events
	WHERE event_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
	ORDER BY event_date ASC
	LIMIT 5
";

$featuredResult = $conn->query($featuredSql);
if ($featuredResult) {
	while ($row = $featuredResult->fetch_assoc()) {
		$featuredEvents[] = $row;
	}
}

$latestEvents = [];
$latestSql = "
	SELECT id, title, description, category, location, event_date, image
	FROM events
	ORDER BY id DESC
	LIMIT 3
";

$latestResult = $conn->query($latestSql);
if ($latestResult) {
	while ($row = $latestResult->fetch_assoc()) {
		$latestEvents[] = $row;
	}
}

function eventImageUrl($image)
{
	if (!empty($image)) {
		if (strpos($image, 'http://') === 0 || strpos($image, 'https://') === 0) {
			return $image;
		}

		if (strpos($image, 'uploads/') === 0) {
			return $image;
		}

		return 'uploads/' . ltrim($image, '/');
	}

	return 'https://images.unsplash.com/photo-1472653431158-6364773b2a56?auto=format&fit=crop&w=1200&q=80';
}

include 'include/navbar.php';
?>

<section class="mb-5">
	<div class="p-4 p-md-5 rounded-4 border hero-surface">
		<div class="row g-4 align-items-center">
			<div class="col-lg-8">
				<p class="text-uppercase small fw-semibold text-info mb-2">Discover This Week</p>
				<h1 class="display-6 fw-bold mb-3">Featured Events this week</h1>
				<p class="text-secondary mb-0">Stay in the loop with cultural, sports, music, and family-friendly experiences happening around the city.</p>
			</div>
			<div class="col-lg-4 text-lg-end">
				<a href="events.php" class="btn btn-info px-4">Browse All Events</a>
			</div>
		</div>
	</div>
</section>

<section class="mb-5">
	<?php if (!empty($featuredEvents)): ?>
		<div id="featuredEventsCarousel" class="carousel slide" data-bs-ride="carousel">
			<div class="carousel-indicators">
				<?php foreach ($featuredEvents as $index => $event): ?>
					<button type="button" data-bs-target="#featuredEventsCarousel" data-bs-slide-to="<?php echo $index; ?>" class="<?php echo $index === 0 ? 'active' : ''; ?>" <?php echo $index === 0 ? 'aria-current="true"' : ''; ?> aria-label="Slide <?php echo $index + 1; ?>"></button>
				<?php endforeach; ?>
			</div>

			<div class="carousel-inner rounded-4 overflow-hidden border">
				<?php foreach ($featuredEvents as $index => $event): ?>
					<div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
						<img src="<?php echo htmlspecialchars(eventImageUrl($event['image'])); ?>" class="d-block w-100 featured-image" alt="<?php echo htmlspecialchars($event['title']); ?>">
						<div class="carousel-caption text-start">
							<span class="badge text-bg-info mb-2"><?php echo htmlspecialchars($event['category'] ?: 'General'); ?></span>
							<h5 class="fw-bold"><?php echo htmlspecialchars($event['title']); ?></h5>
							<p class="mb-1"><?php echo htmlspecialchars($event['location'] ?: 'City Venue'); ?></p>
							<small><?php echo !empty($event['event_date']) ? date('M d, Y', strtotime($event['event_date'])) : 'Date to be announced'; ?></small>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	<?php else: ?>
		<div class="card border rounded-4 p-4">
			<h3 class="h5 mb-2">Featured Events this week</h3>
			<p class="text-secondary mb-0">No featured events for this week yet. Check back soon for updates.</p>
		</div>
	<?php endif; ?>
</section>

<section class="mb-5">
	<div class="d-flex justify-content-between align-items-center mb-3">
		<h2 class="h4 mb-0">Quick Categories</h2>
	</div>
	<div class="d-flex flex-wrap gap-2">
		<a href="events.php?filter=Culture" class="btn btn-outline-light category-btn"><i class="bi bi-palette me-2"></i>Culture</a>
		<a href="events.php?filter=Sports" class="btn btn-outline-light category-btn"><i class="bi bi-trophy me-2"></i>Sports</a>
		<a href="events.php?filter=Music" class="btn btn-outline-light category-btn"><i class="bi bi-music-note-beamed me-2"></i>Music</a>
		<a href="events.php?filter=Family" class="btn btn-outline-light category-btn"><i class="bi bi-people me-2"></i>Family</a>
		<a href="events.php?filter=Food" class="btn btn-outline-light category-btn"><i class="bi bi-cup-hot me-2"></i>Food</a>
		<a href="events.php?filter=Community" class="btn btn-outline-light category-btn"><i class="bi bi-geo-alt me-2"></i>Community</a>
	</div>
</section>

<section class="mb-4">
	<div class="d-flex justify-content-between align-items-center mb-3">
		<h2 class="h4 mb-0">Latest Events</h2>
		<a href="events.php" class="btn btn-sm btn-outline-info">View all</a>
	</div>

	<div class="row g-4">
		<?php if (!empty($latestEvents)): ?>
			<?php foreach ($latestEvents as $event): ?>
				<div class="col-md-6 col-lg-4">
					<div class="card h-100 border-0 shadow-sm overflow-hidden">
						<img src="<?php echo htmlspecialchars(eventImageUrl($event['image'])); ?>" class="card-img-top latest-image" alt="<?php echo htmlspecialchars($event['title']); ?>">
						<div class="card-body d-flex flex-column">
							<span class="badge text-bg-secondary align-self-start mb-2"><?php echo htmlspecialchars($event['category'] ?: 'General'); ?></span>
							<h3 class="h5 card-title"><?php echo htmlspecialchars($event['title']); ?></h3>
							<p class="card-text text-secondary mb-3"><?php echo htmlspecialchars(substr((string)($event['description'] ?: 'No description available yet.'), 0, 120)); ?>...</p>
							<div class="mt-auto">
								<p class="mb-1 small text-secondary"><?php echo htmlspecialchars($event['location'] ?: 'City Venue'); ?></p>
								<p class="mb-3 small text-secondary"><?php echo !empty($event['event_date']) ? date('M d, Y', strtotime($event['event_date'])) : 'Date to be announced'; ?></p>
								<a href="event.php?id=<?php echo (int)$event['id']; ?>" class="btn btn-info btn-sm">View details</a>
							</div>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		<?php else: ?>
			<div class="col-12">
				<div class="card border rounded-4 p-4">
					<p class="text-secondary mb-0">No events have been posted yet. Add events from the admin dashboard to populate this section.</p>
				</div>
			</div>
		<?php endif; ?>
	</div>
</section>

<?php include 'include/footer.php'; ?>
