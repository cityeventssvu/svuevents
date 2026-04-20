document.addEventListener('DOMContentLoaded', function () {
	var root = document.documentElement;
	var siteNavbar = document.getElementById('siteNavbar');
	var toggleButton = document.getElementById('themeToggle');
	var toggleIcon = document.getElementById('themeToggleIcon');
	var toggleText = document.getElementById('themeToggleText');

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

	if (toggleButton) {
		toggleButton.addEventListener('click', function () {
			var currentTheme = root.getAttribute('data-bs-theme') || 'dark';
			var nextTheme = currentTheme === 'dark' ? 'light' : 'dark';
			applyTheme(nextTheme);
		});
	}
});
