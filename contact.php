<?php
$pageTitle = 'City Events - Contact';

$name = '';
$email = '';
$message = '';
$successMessage = '';
$errorMessage = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$name = trim($_POST['name'] ?? '');
	$email = trim($_POST['email'] ?? '');
	$message = trim($_POST['message'] ?? '');

	$isEmailValid = filter_var($email, FILTER_VALIDATE_EMAIL);

	if ($name === '' || $email === '' || $message === '') {
		$errorMessage = 'Please fill in all required fields.';
	} elseif (!$isEmailValid) {
		$errorMessage = 'Please enter a valid email address.';
	} else {
		$successMessage = 'Message received. This is a demo submission, so no email was sent.';
		$name = '';
		$email = '';
		$message = '';
	}
}

include 'include/navbar.php';
?>

<!-- Set page title for navbar -->
<section class="mb-4">
	<div class="p-4 rounded-4 border hero-surface">
		<h1 class="h3 mb-2">Contact Us</h1>
		<p class="text-secondary mb-0">Have a question about events or partnerships? Send us a message.</p>
	</div>
</section>

<!-- Contact form section -->
<section class="mb-4">
	<div class="row g-4">
		<div class="col-lg-7">
			<div class="card border-0 shadow-sm p-4">
				<h2 class="h5 mb-3">Send a Message</h2>

				<?php if ($successMessage !== ''): ?>
					<div class="alert alert-success" role="alert"><?php echo htmlspecialchars($successMessage); ?></div>
				<?php endif; ?>

				<?php if ($errorMessage !== ''): ?>
					<div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($errorMessage); ?></div>
				<?php endif; ?>

				<form method="post" action="contact.php" id="contactForm" class="js-contact-form" novalidate>
					<div class="mb-3">
						<label for="name" class="form-label">Name</label>
						<input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($name); ?>" required>
						<div class="invalid-feedback">Please enter your name.</div>
					</div>

					<div class="mb-3">
						<label for="email" class="form-label">Email</label>
						<input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" required>
						<div class="invalid-feedback">Please enter a valid email address.</div>
					</div>

					<div class="mb-3">
						<label for="message" class="form-label">Message</label>
						<textarea id="message" name="message" class="form-control" rows="5" required><?php echo htmlspecialchars($message); ?></textarea>
						<div class="invalid-feedback">Please enter your message.</div>
					</div>

					<button type="submit" class="btn btn-info">
						<i class="bi bi-send me-2"></i>Submit
					</button>
				</form>
			</div>
		</div>

		<div class="col-lg-5">
			<div class="card border-0 shadow-sm p-4 h-100">
				<h2 class="h5 mb-3">Alternative Contact Info</h2>
				<ul class="list-unstyled mb-0 d-grid gap-3">
					<li>
						<p class="small text-secondary mb-1">General Email</p>
						<p class="mb-0"><i class="bi bi-envelope me-2"></i>svu@cityevents.local</p>
					</li>
					<li>
						<p class="small text-secondary mb-1">Phone</p>
						<p class="mb-0"><i class="bi bi-telephone me-2"></i>+963 111 222 333</p>
					</li>
				</ul>
			</div>
		</div>
	</div>
</section>

<?php include 'include/footer.php'; ?>
