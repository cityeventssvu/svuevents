<?php
session_start();
require_once '../db.php';

if (isset($_SESSION['admin_id'])) {
	header('Location: dashboard.php');
	exit;
}

// Initialize variables for form handling
$errorMessage = '';
$username = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$username = trim($_POST['username'] ?? '');
	$password = trim($_POST['password'] ?? '');
	$confirmPassword = trim($_POST['confirm_password'] ?? '');

	if ($username === '' || $password === '' || $confirmPassword === '') {
		$errorMessage = 'Please fill in all fields.';
	} elseif ($password !== $confirmPassword) {
		$errorMessage = 'Passwords do not match.';
	} else {
		$checkStmt = $conn->prepare('SELECT id FROM users WHERE username = ? LIMIT 1');
		if ($checkStmt) {
			$checkStmt->bind_param('s', $username);
			$checkStmt->execute();
			$checkResult = $checkStmt->get_result();
			$existingUser = $checkResult ? $checkResult->fetch_assoc() : null;
			$checkStmt->close();

			if ($existingUser) {
				$errorMessage = 'Username already exists. Please choose another one.';
			}
		}

		if ($errorMessage === '') {
			$insertStmt = $conn->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
			if ($insertStmt) {
				$insertStmt->bind_param('ss', $username, $password);
				if ($insertStmt->execute()) {
					$insertStmt->close();
					header('Location: login.php?registered=1');
					exit;
				}
				$insertStmt->close();
			}

			$errorMessage = 'Unable to create account right now.';
		}
	}
}
?>

<!doctype html>
<html lang="en" data-bs-theme="dark">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Admin Sign Up - City Events</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
	<link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
<main class="content-wrap">
	<div class="container">
		<!-- Sign up form section -->
		<div class="row justify-content-center">
			<div class="col-md-8 col-lg-5">
				<div class="card border-0 shadow-sm p-4">
					<div class="text-center mb-3">
						<h1 class="h3 mb-1">Create Admin Account</h1>
						<p class="text-secondary mb-0">Sign up to access the dashboard.</p>
					</div>

					<?php if ($errorMessage !== ''): ?>
						<div class="alert alert-danger" role="alert">
							<?php echo htmlspecialchars($errorMessage); ?>
						</div>
					<?php endif; ?>

					<form method="post" action="signup.php" class="d-grid gap-3">
						<div>
							<label for="username" class="form-label">Username</label>
							<input type="text" id="username" name="username" class="form-control" value="<?php echo htmlspecialchars($username); ?>" required>
							<div class="invalid-feedback">Username is required.</div>
						</div>

						<div>
							<label for="password" class="form-label">Password</label>
							<input type="password" id="password" name="password" class="form-control" required>
							<div class="invalid-feedback">Password is required.</div>
						</div>

						<div>
							<label for="confirm_password" class="form-label">Confirm Password</label>
							<input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
							<div class="invalid-feedback">Please confirm your password.</div>
						</div>

						<button type="submit" class="btn btn-info">
							<i class="bi bi-person-plus me-2"></i>Sign Up
						</button>
					</form>

					<div class="text-center mt-3">
						<p class="mb-2 text-secondary">Already have an account?</p>
						<a href="login.php" class="btn btn-sm btn-outline-info me-2">Login</a>
						<a href="../index.php" class="btn btn-sm btn-outline-secondary">Back to Site</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="../assets/js/main.js"></script>
</body>
</html>
