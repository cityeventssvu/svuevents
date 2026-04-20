<?php
session_start();
require_once '../db.php';

if (isset($_SESSION['admin_id'])) {
	header('Location: dashboard.php');
	exit;
}

$errorMessage = '';
$successMessage = '';
$username = '';

// Check if redirected from registration page
if (isset($_GET['registered']) && $_GET['registered'] === '1') {
	$successMessage = 'Account created successfully. You can sign in now.';
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$username = trim($_POST['username'] ?? '');
	$password = trim($_POST['password'] ?? '');

	if ($username === '' || $password === '') {
		$errorMessage = 'Please enter both username and password.';
	} else {
		$stmt = $conn->prepare('SELECT id, username, password FROM users WHERE username = ? LIMIT 1');

		if ($stmt) {
			$stmt->bind_param('s', $username);
			$stmt->execute();
			$result = $stmt->get_result();
			$user = $result ? $result->fetch_assoc() : null;
			$stmt->close();

			if ($user && $user['password'] === $password) {
				$_SESSION['admin_id'] = (int)$user['id'];
				$_SESSION['admin_username'] = $user['username'];
				header('Location: dashboard.php');
				exit;
			}
		}

		$errorMessage = 'Invalid username or password.';
	}
}
?>
<!doctype html>
<html lang="en" data-bs-theme="dark">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Admin Login - City Events</title>
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
						<h1 class="h3 mb-1">Admin Login</h1>
						<p class="text-secondary mb-0">Sign in to manage events.</p>
					</div>

					<?php if ($errorMessage !== ''): ?>
						<div class="alert alert-danger" role="alert">
							<?php echo htmlspecialchars($errorMessage); ?>
						</div>
					<?php endif; ?>

					<?php if ($successMessage !== ''): ?>
						<div class="alert alert-success" role="alert">
							<?php echo htmlspecialchars($successMessage); ?>
						</div>
					<?php endif; ?>

					<form method="post" action="login.php" class="d-grid gap-3">
						<div>
							<label for="username" class="form-label">Username</label>
							<input type="text" id="username" name="username" class="form-control" value="<?php echo htmlspecialchars($username); ?>" required>
						</div>

						<div>
							<label for="password" class="form-label">Password</label>
							<input type="password" id="password" name="password" class="form-control" required>
						</div>

						<button type="submit" class="btn btn-info">
							<i class="bi bi-box-arrow-in-right me-2"></i>Login
						</button>
					</form>

					<div class="text-center mt-3">
						<p class="mb-2 text-secondary">Don't have an account?</p>
						<a href="signup.php" class="btn btn-sm btn-outline-info me-2">Sign Up</a>
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
