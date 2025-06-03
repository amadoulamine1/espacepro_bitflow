import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
	static targets = ["container"];

	connect() {
		// Compte le nombre de pièces jointes déjà présentes
		this.index = this.containerTarget.querySelectorAll(
			'[data-pieces-jointes-target="item"]'
		).length;
		// Récupère le prototype pour les nouveaux champs
		this.prototype = this.containerTarget.dataset.prototype;
	}

	add() {
		// Remplace __name__ dans le prototype par l'index courant
		const html = this.prototype.replace(/__name__/g, this.index);
		const div = document.createElement("div");
		div.classList.add("mb-2");
		div.setAttribute("data-pieces-jointes-target", "item");
		div.innerHTML = html;
		this.containerTarget.appendChild(div);
		this.index++;
	}
}
