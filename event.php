<?php
$pageTitle = 'City Events - Event Details';
require_once 'db.php';

$eventId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

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

	return 'https://images.unsplash.com/photo-1511578314322-379afb476865?auto=format&fit=crop&w=1200&q=80';
}

// Fetch event details
$event = null;
if ($eventId > 0) {
	$stmt = $conn->prepare('SELECT id, title, description, category, location, event_date, image FROM events WHERE id = ? LIMIT 1');
	if ($stmt) {
		$stmt->bind_param('i', $eventId);
		$stmt->execute();
		$result = $stmt->get_result();
		$event = $result ? $result->fetch_assoc() : null;
		$stmt->close();
	}
}

// Set page title based on event title
if ($event) {
	$pageTitle = 'City Events - ' . $event['title'];
}

$relatedEvents = [];
if ($event && !empty($event['category'])) {
	$relatedStmt = $conn->prepare('
		SELECT id, title, category, location, event_date, image
		FROM events
		WHERE category = ? AND id <> ?
		ORDER BY event_date DESC, id DESC
		LIMIT 3
	');

	if ($relatedStmt) {
		$relatedStmt->bind_param('si', $event['category'], $eventId);
		$relatedStmt->execute();
		$relatedResult = $relatedStmt->get_result();

		if ($relatedResult) {
			while ($row = $relatedResult->fetch_assoc()) {
				$relatedEvents[] = $row;
			}
		}

		$relatedStmt->close();
	}
}

include 'include/navbar.php';
?>

<!-- Function to generate event image URL -->
<?php if (!$event): ?>
	<section class="mb-4">
		<div class="card border rounded-4 p-4">
			<h1 class="h4 mb-2">Event not found</h1>
			<p class="text-secondary mb-3">The event you requested does not exist or the link is invalid.</p>
			<a href="events.php" class="btn btn-info btn-sm">Back to Events</a>
		</div>
	</section>
<?php else: ?>
	<!-- Prepare event details and related events -->
	<?php
	$eventDateText = !empty($event['event_date']) ? date('M d, Y', strtotime($event['event_date'])) : 'Date to be announced';
	$eventLocation = $event['location'] ?: 'City Venue';
	$eventCategory = $event['category'] ?: 'General';
	$eventDescription = trim((string)$event['description']) !== '' ? $event['description'] : 'No description available yet.';

	$calendarStart = !empty($event['event_date']) ? date('Ymd', strtotime($event['event_date'])) : date('Ymd');
	$calendarEnd = !empty($event['event_date']) ? date('Ymd', strtotime($event['event_date'] . ' +1 day')) : date('Ymd', strtotime('+1 day'));
	$calendarTitle = rawurlencode($event['title']);
	$calendarLocation = rawurlencode($eventLocation);
	$calendarDetails = rawurlencode(strip_tags($eventDescription));
	$calendarUrl = 'https://calendar.google.com/calendar/render?action=TEMPLATE&text=' . $calendarTitle . '&dates=' . $calendarStart . '/' . $calendarEnd . '&details=' . $calendarDetails . '&location=' . $calendarLocation;
	$shareUrl = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/event.php?id=' . (int)$event['id'];
	?>

	<!-- Display event details -->
	<section class="mb-4">
		<div class="card border-0 shadow-sm overflow-hidden">
			<img src="<?php echo htmlspecialchars(eventImageUrl($event['image'])); ?>" class="card-img-top featured-image" alt="<?php echo htmlspecialchars($event['title']); ?>">
			<div class="card-body p-4 p-lg-5">
				<div class="d-flex flex-wrap gap-2 mb-3">
					<span class="badge text-bg-secondary"><?php echo htmlspecialchars($eventCategory); ?></span>
					<span class="badge text-bg-info"><?php echo htmlspecialchars($eventDateText); ?></span>
				</div>

				<h1 class="display-6 fw-bold mb-3"><?php echo htmlspecialchars($event['title']); ?></h1>
				<p class="mb-2"><i class="bi bi-geo-alt me-2"></i><?php echo htmlspecialchars($eventLocation); ?></p>
				<p class="text-secondary mb-4"><?php echo nl2br(htmlspecialchars($eventDescription)); ?></p>

				<div class="d-flex flex-wrap gap-2">
					<a href="<?php echo htmlspecialchars($calendarUrl); ?>" class="btn btn-info" target="_blank" rel="noopener noreferrer">
						<i class="bi bi-calendar-plus me-2"></i>Add to Calendar
					</a>
					<button type="button" class="btn btn-outline-secondary" id="shareEventBtn" data-share-url="<?php echo htmlspecialchars($shareUrl); ?>" data-share-title="<?php echo htmlspecialchars($event['title']); ?>">
						<i class="bi bi-share me-2"></i>Share
					</button>
					<a href="events.php" class="btn btn-outline-light category-btn">Back to Events</a>
				</div>
			</div>
		</div>
	</section>
	<!-- Related events section -->
	<section class="mb-4">
		<div class="d-flex justify-content-between align-items-center mb-3">
			<h2 class="h4 mb-0">Related Events</h2>
			<a href="events.php?filter=<?php echo urlencode($eventCategory); ?>" class="btn btn-sm btn-outline-info">View Category</a>
		</div>

		<div class="row g-4">
			<?php if (!empty($relatedEvents)): ?>
				<?php foreach ($relatedEvents as $item): ?>
					<div class="col-md-6 col-lg-4">
						<div class="card h-100 border-0 shadow-sm overflow-hidden">
							<img src="<?php echo htmlspecialchars(eventImageUrl($item['image'])); ?>" class="card-img-top latest-image" alt="<?php echo htmlspecialchars($item['title']); ?>">
							<div class="card-body d-flex flex-column">
								<span class="badge text-bg-secondary align-self-start mb-2"><?php echo htmlspecialchars($item['category'] ?: 'General'); ?></span>
								<h3 class="h5 card-title"><?php echo htmlspecialchars($item['title']); ?></h3>
								<p class="mb-1 small text-secondary"><i class="bi bi-geo-alt me-1"></i><?php echo htmlspecialchars($item['location'] ?: 'City Venue'); ?></p>
								<p class="mb-3 small text-secondary"><?php echo !empty($item['event_date']) ? date('M d, Y', strtotime($item['event_date'])) : 'Date TBD'; ?></p>
								<div class="mt-auto">
									<a href="event.php?id=<?php echo (int)$item['id']; ?>" class="btn btn-info btn-sm">Details</a>
								</div>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			<?php else: ?>
				<div class="col-12">
					<div class="card border rounded-4 p-4">
						<p class="text-secondary mb-0">No related events found in this category right now.</p>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</section>

	<!-- JavaScript for share button functionality -->
	<script>
		document.addEventListener('DOMContentLoaded', function () {
			var shareButton = document.getElementById('shareEventBtn');
			if (!shareButton) {
				return;
			}

			shareButton.addEventListener('click', async function () {
				var shareUrl = shareButton.getAttribute('data-share-url');
				var shareTitle = shareButton.getAttribute('data-share-title') || 'Event';

				if (navigator.share) {
					try {
						await navigator.share({
							title: shareTitle,
							url: shareUrl
						});
						return;
					} catch (error) {
					}
				}

				if (navigator.clipboard && navigator.clipboard.writeText) {
					navigator.clipboard.writeText(shareUrl).then(function () {
						shareButton.innerHTML = '<i class="bi bi-check2 me-2"></i>Link Copied';
						setTimeout(function () {
							shareButton.innerHTML = '<i class="bi bi-share me-2"></i>Share';
						}, 1800);
					});
				} else {
					window.prompt('Copy this event link:', shareUrl);
				}
			});
		});
	</script>
<?php endif; ?>

<?php include 'include/footer.php'; ?>
