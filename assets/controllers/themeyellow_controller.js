import { Controller } from "@hotwired/stimulus";

/* stimulusFetch: 'lazy' */
export default class extends Controller {
	static targets = ["toggle", "label"];

	connect() {
		console.log("Le contrôleur theme-toggle est chargé !");

		this.htmlElement = document.documentElement;

		// Sécurisation turbo : toujours réévaluer à chaque visit
		//document.addEventListener("turbo:load", () => this.initializeTheme());
		// Sécurisation turbo : toujours réévaluer à chaque visit
		document.addEventListener("turbo:load", () => {
			console.log("Événement turbo:load déclenché 🎉");
			this.initializeTheme();
		});

		// Init immédiat au chargement initial
		this.initializeTheme();
	}

	initializeTheme() {
		console.log("Initialise theme");
		if (localStorage.getItem("theme") === "dark") {
            console.log("Init Appliquer le thème sombre");
			this.htmlElement.classList.add("dark");
			this.toggleTarget.checked = true;
		} else {
            console.log("Init Appliquer le thème clair");
			this.htmlElement.classList.remove("dark");
			this.toggleTarget.checked = false;
		}
		this.updateLabel();
	}

	toggleMode() {
		console.log("Toogle 🎉");
		if (this.toggleTarget.checked) {
            console.log("Ajout de la classe 'dark'");
			this.htmlElement.classList.add("dark");
			localStorage.setItem("theme", "dark");
		} else {
            console.log("Suppression de la classe 'dark'");
			this.htmlElement.classList.remove("dark");
			localStorage.setItem("theme", "light");
		}
		this.updateLabel();
        console.log("toggleMode exécuté 🎉");
		console.log("Valeur actuelle de toggleTarget.checked :",
					this.toggleTarget.checked
				);

             /*   var themeToggleDarkIcon = document.getElementById(
									"theme-toggle-dark-icon"
								);
								var themeToggleLightIcon = document.getElementById(
									"theme-toggle-light-icon"
								);

								// Change the icons inside the button based on previous settings
								if (
									localStorage.getItem("color-theme") === "dark" ||
									(!("color-theme" in localStorage) &&
										window.matchMedia("(prefers-color-scheme: dark)").matches)
								) {
									themeToggleLightIcon.classList.remove("hidden");
								} else {
									themeToggleDarkIcon.classList.remove("hidden");
								}

								var themeToggleBtn = document.getElementById("theme-toggle");

								themeToggleBtn.addEventListener("click", function () {
									// toggle icons inside button
									themeToggleDarkIcon.classList.toggle("hidden");
									themeToggleLightIcon.classList.toggle("hidden");

									// if set via local storage previously
									if (localStorage.getItem("color-theme")) {
										if (localStorage.getItem("color-theme") === "light") {
											document.documentElement.classList.add("dark");
											localStorage.setItem("color-theme", "dark");
										} else {
											document.documentElement.classList.remove("dark");
											localStorage.setItem("color-theme", "light");
										}

										// if NOT set via local storage previously
									} else {
										if (document.documentElement.classList.contains("dark")) {
											document.documentElement.classList.remove("dark");
											localStorage.setItem("color-theme", "light");
										} else {
											document.documentElement.classList.add("dark");
											localStorage.setItem("color-theme", "dark");
										}
									}
								});*/
	}

	updateLabel() {
		console.log("update label 🎉");
		if (this.htmlElement.classList.contains("dark")) {
			this.labelTarget.textContent = "☀️ Mode Jour";
		} else {
			this.labelTarget.textContent = "🌙 Mode Nuit";
		}
	}
}
