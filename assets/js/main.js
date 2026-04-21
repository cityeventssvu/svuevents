document.addEventListener('DOMContentLoaded', function () {
	var root = document.documentElement;
	var siteNavbar = document.getElementById('siteNavbar');
	var toggleButton = document.getElementById('themeToggle');
	var toggleIcon = document.getElementById('themeToggleIcon');
	var toggleText = document.getElementById('themeToggleText');
	var scrollTopButton = document.getElementById('scrollTopButton');

	// Returns 'dark' or 'light' based on saved preference or defaults to 'dark'
	function getPreferredTheme() {
		var savedTheme = localStorage.getItem('theme');
		if (savedTheme === 'dark' || savedTheme === 'light') {
			return savedTheme;
		}
		return 'dark';
	}

	// Updates the toggle button's appearance based on the current theme
	function updateToggleUi(theme) {
		var isDark = theme === 'dark';

		if (toggleButton) {
			toggleButton.setAttribute('aria-pressed', isDark ? 'true' : 'false');
			toggleButton.classList.toggle('btn-outline-light', isDark);
			toggleButton.classList.toggle('btn-outline-dark', !isDark);
		}

		if (toggleIcon) {
			toggleIcon.className = isDark ? 'bi bi-moon-stars-fill me-2' : 'bi bi-sun-fill me-2';
		}

		if (toggleText) {
			toggleText.textContent = isDark ? 'Dark' : 'Light';
		}
	}

	// Applies the specified theme to the document and updates related UI elements
	function applyTheme(theme) {
		var isDark = theme === 'dark';

		root.setAttribute('data-bs-theme', theme);
		localStorage.setItem('theme', theme);

		if (siteNavbar) {
			siteNavbar.classList.toggle('navbar-dark', isDark);
			siteNavbar.classList.toggle('navbar-light', !isDark);
		}

		updateToggleUi(theme);
	}

	applyTheme(getPreferredTheme());

	// Shows or hides the scroll-to-top button based on the current scroll position
	function updateScrollTopButton() {
		if (!scrollTopButton) {
			return;
		}

		scrollTopButton.hidden = window.scrollY < 320;
	}

	if (scrollTopButton) {
		updateScrollTopButton();

		window.addEventListener('scroll', updateScrollTopButton, { passive: true });

		scrollTopButton.addEventListener('click', function () {
			window.scrollTo({
				top: 0,
				behavior: 'smooth'
			});
		});
	}

	if (toggleButton) {
		toggleButton.addEventListener('click', function () {
			var currentTheme = root.getAttribute('data-bs-theme') || 'dark';
			var nextTheme = currentTheme === 'dark' ? 'light' : 'dark';
			applyTheme(nextTheme);
		});
	}

	// Contact form validation
	var contactForm = document.querySelector('.js-contact-form');
	if (contactForm) {
		var nameInput = contactForm.querySelector('#name');
		var emailInput = contactForm.querySelector('#email');
		var messageInput = contactForm.querySelector('#message');

		// Validates the email format using a regular expression
		function isValidEmail(email) {
			return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
		}

		function markInvalid(input, shouldMark) {
			if (!input) {
				return;
			}
			input.classList.toggle('is-invalid', shouldMark);
		}

		function validateEmailField() {
			if (!emailInput) {
				return false;
			}

			var emailValue = emailInput.value.trim();
			var emailInvalid = emailValue === '' || !isValidEmail(emailValue);
			markInvalid(emailInput, emailInvalid);
			return !emailInvalid;
		}

		function validateNameField() {
			if (!nameInput) {
				return false;
			}

			var nameValue = nameInput.value.trim();
			var nameInvalid = nameValue === '';
			markInvalid(nameInput, nameInvalid);
			return !nameInvalid;
		}

		function validateMessageField() {
			if (!messageInput) {
				return false;
			}

			var messageValue = messageInput.value.trim();
			var messageInvalid = messageValue === '';
			markInvalid(messageInput, messageInvalid);
			return !messageInvalid;
		}

		contactForm.addEventListener('submit', function (event) {
			var nameInvalid = !validateNameField();
			var emailInvalid = !validateEmailField();
			var messageInvalid = !validateMessageField();

			if (nameInvalid || emailInvalid || messageInvalid) {
				event.preventDefault();

				if (nameInvalid && nameInput) {
					nameInput.focus();
				} else if (emailInvalid && emailInput) {
					emailInput.focus();
				} else if (messageInvalid && messageInput) {
					messageInput.focus();
				}
			}
		});

		if (emailInput) {
			emailInput.addEventListener('blur', function () {
				validateEmailField();
			});
		}

		if (nameInput) {
			nameInput.addEventListener('blur', function () {
				validateNameField();
			});
		}

		if (messageInput) {
			messageInput.addEventListener('blur', function () {
				validateMessageField();
			});
		}

		[nameInput, emailInput, messageInput].forEach(function (input) {
			if (!input) {
				return;
			}
			input.addEventListener('input', function () {
				input.classList.remove('is-invalid');
			});
		});
	}

	// Admin sign-in form validation
	var signInForm = document.querySelector('form[action="login.php"]');
	if (signInForm) {
		var signInUsernameInput = signInForm.querySelector('#username');
		var signInPasswordInput = signInForm.querySelector('#password');

		function markSignInInvalid(input, shouldMark, message) {
			if (!input) {
				return;
			}
			input.classList.toggle('is-invalid', shouldMark);

			var feedback = input.parentElement ? input.parentElement.querySelector('.invalid-feedback') : null;
			if (feedback && typeof message === 'string' && message !== '') {
				feedback.textContent = message;
			}
		}

		function validateSignInUsername() {
			if (!signInUsernameInput) {
				return false;
			}

			var usernameValue = signInUsernameInput.value.trim();
			var usernameInvalid = usernameValue === '';
			markSignInInvalid(signInUsernameInput, usernameInvalid, 'Username is required.');
			return !usernameInvalid;
		}

		function validateSignInPassword() {
			if (!signInPasswordInput) {
				return false;
			}

			var passwordValue = signInPasswordInput.value.trim();
			var passwordInvalid = passwordValue === '';
			markSignInInvalid(signInPasswordInput, passwordInvalid, 'Password is required.');
			return !passwordInvalid;
		}

		signInForm.addEventListener('submit', function (event) {
			var usernameInvalid = !validateSignInUsername();
			var passwordInvalid = !validateSignInPassword();

			if (usernameInvalid || passwordInvalid) {
				event.preventDefault();

				if (usernameInvalid && signInUsernameInput) {
					signInUsernameInput.focus();
				} else if (passwordInvalid && signInPasswordInput) {
					signInPasswordInput.focus();
				}
			}
		});

		[signInUsernameInput, signInPasswordInput].forEach(function (input) {
			if (!input) {
				return;
			}

			input.addEventListener('blur', function () {
				if (input === signInUsernameInput) {
					validateSignInUsername();
				} else {
					validateSignInPassword();
				}
			});

			input.addEventListener('input', function () {
				input.classList.remove('is-invalid');
			});
		});
	}

	// Admin sign-up form validation
	var signUpForm = document.querySelector('form[action="signup.php"]');
	if (signUpForm) {
		var signUpUsernameInput = signUpForm.querySelector('#username');
		var signUpPasswordInput = signUpForm.querySelector('#password');
		var confirmPasswordInput = signUpForm.querySelector('#confirm_password');

		function markSignUpInvalid(input, shouldMark, message) {
			if (!input) {
				return;
			}
			input.classList.toggle('is-invalid', shouldMark);

			var feedback = input.parentElement ? input.parentElement.querySelector('.invalid-feedback') : null;
			if (feedback && typeof message === 'string' && message !== '') {
				feedback.textContent = message;
			}
		}

		function validateSignUpUsername() {
			if (!signUpUsernameInput) {
				return false;
			}

			var usernameValue = signUpUsernameInput.value.trim();
			var usernameInvalid = usernameValue === '';
			markSignUpInvalid(signUpUsernameInput, usernameInvalid, 'Username is required');
			return !usernameInvalid;
		}

		function validateSignUpPassword() {
			if (!signUpPasswordInput) {
				return false;
			}

			var passwordValue = signUpPasswordInput.value.trim();
			var passwordInvalid = passwordValue === '';
			markSignUpInvalid(signUpPasswordInput, passwordInvalid, 'Password is required');
			return !passwordInvalid;
		}

		function validateConfirmPassword() {
			if (!confirmPasswordInput || !signUpPasswordInput) {
				return false;
			}

			var passwordValue = signUpPasswordInput.value.trim();
			var confirmValue = confirmPasswordInput.value.trim();
			var confirmInvalid = confirmValue === '' || passwordValue !== confirmValue;
			if (confirmValue === '') {
				markSignUpInvalid(confirmPasswordInput, true, 'Please confirm your password');
				return false;
			}

			if (passwordValue !== confirmValue) {
				markSignUpInvalid(confirmPasswordInput, true, 'Passwords do not match');
				return false;
			}

			markSignUpInvalid(confirmPasswordInput, false, '');
			return !confirmInvalid;
		}

		signUpForm.addEventListener('submit', function (event) {
			var usernameInvalid = !validateSignUpUsername();
			var passwordInvalid = !validateSignUpPassword();
			var confirmInvalid = !validateConfirmPassword();

			if (usernameInvalid || passwordInvalid || confirmInvalid) {
				event.preventDefault();

				if (usernameInvalid && signUpUsernameInput) {
					signUpUsernameInput.focus();
				} else if (passwordInvalid && signUpPasswordInput) {
					signUpPasswordInput.focus();
				} else if (confirmInvalid && confirmPasswordInput) {
					confirmPasswordInput.focus();
				}
			}
		});

		if (signUpUsernameInput) {
			signUpUsernameInput.addEventListener('blur', function () {
				validateSignUpUsername();
			});
		}

		if (signUpPasswordInput) {
			signUpPasswordInput.addEventListener('blur', function () {
				validateSignUpPassword();
				validateConfirmPassword();
			});
		}

		if (confirmPasswordInput) {
			confirmPasswordInput.addEventListener('blur', function () {
				validateConfirmPassword();
			});
		}

		[signUpUsernameInput, signUpPasswordInput, confirmPasswordInput].forEach(function (input) {
			if (!input) {
				return;
			}

			input.addEventListener('input', function () {
				input.classList.remove('is-invalid');
				if (input === signUpPasswordInput || input === confirmPasswordInput) {
					if (confirmPasswordInput) {
						confirmPasswordInput.classList.remove('is-invalid');
					}
				}
			});
		});
	}
});
