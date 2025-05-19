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
