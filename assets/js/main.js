document.addEventListener('DOMContentLoaded', function () {
	var root = document.documentElement;
	var siteNavbar = document.getElementById('siteNavbar');
	var toggleButton = document.getElementById('themeToggle');
	var toggleIcon = document.getElementById('themeToggleIcon');
	var toggleText = document.getElementById('themeToggleText');
	var scrollTopButton = document.getElementById('scrollTopButton');

	function getPreferredTheme() {
		var savedTheme = localStorage.getItem('theme');
		if (savedTheme === 'dark' || savedTheme === 'light') {
			return savedTheme;
		}
		return 'dark';
	}

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

	var contactForm = document.querySelector('.js-contact-form');
	if (contactForm) {
		var nameInput = contactForm.querySelector('#name');
		var emailInput = contactForm.querySelector('#email');
		var messageInput = contactForm.querySelector('#message');

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
});
