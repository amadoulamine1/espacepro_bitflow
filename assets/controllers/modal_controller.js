/*import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
	static targets = ["modalElement"];

	connect() {
		console.log(
			"ModalController: connect() - Contrôleur connecté à l'élément:",
			this.element
		);
		if (this.hasModalElementTarget) {
			this.modalElementTarget.addEventListener("click", (e) => {
				if (e.target === this.modalElementTarget) {
					this.close();
				}
			});
		} else {
			console.error(
				"ModalController: connect() - ERREUR: data-modal-target='modalElement' manquant."
			);
		}
		this.boundCloseOnEscape = this.closeOnEscape.bind(this);
		document.addEventListener("keydown", this.boundCloseOnEscape);
	}

	disconnect() {
		document.removeEventListener("keydown", this.boundCloseOnEscape);
	}

	closeOnEscape(e) {
		if (
			e.key === "Escape" &&
			this.hasModalElementTarget &&
			this.modalElementTarget.style.display !== "none"
		) {
			this.close();
		}
	}

	open(event) {
		event.preventDefault();
		event.stopPropagation();

		if (!this.hasModalElementTarget) {
			console.error(
				"ModalController: open() - ERREUR: modalElementTarget manquant."
			);
			return;
		}

		const url = event.currentTarget.dataset.modalUrlParam;
		const frameId =
			event.currentTarget.dataset.modalFrameIdParam || "sfd-details";
		if (!url) {
			console.error(
				"ModalController: open() - ERREUR: data-modal-url-param manquant."
			);
			return;
		}

		const modalFrame = this.modalElementTarget.querySelector(`#${frameId}`);
		if (!modalFrame) {
			console.error(
				`ModalController: open() - ERREUR: <turbo-frame id='${frameId}'> non trouvé.`
			);
			return;
		}

		modalFrame.src = url;
		this.modalElementTarget.style.display = "block";
		document.body.style.overflow = "hidden";
	}

	close() {
		if (!this.hasModalElementTarget) {
			console.error(
				"ModalController: close() - ERREUR: modalElementTarget manquant."
			);
			return;
		}
		this.modalElementTarget.style.display = "none";
		document.body.style.overflow = "auto";

		// Réinitialiser le turbo-frame (id dynamique)
		const frames = this.modalElementTarget.querySelectorAll("turbo-frame");
		frames.forEach((frame) => {
			frame.src = "";
			frame.innerHTML =
				'<div class="text-center py-8"><div class="animate-spin rounded-full h-12 w-12 border-b-2 border-gray-900 mx-auto"></div><p class="mt-4 text-gray-600">Chargement en cours...</p></div>';
		});
	}
}*/

import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
	static targets = ["modalElement"];

	connect() {
		console.log(
			"ModalController: connect() - Contrôleur connecté à l'élément:",
			this.element
		);
		if (this.hasModalElementTarget) {
			this.modalElementTarget.addEventListener("click", (e) => {
				if (e.target === this.modalElementTarget) {
					this.close();
				}
			});
		} else {
			console.error(
				"ModalController: connect() - ERREUR: data-modal-target='modalElement' manquant."
			);
		}
		this.boundCloseOnEscape = this.closeOnEscape.bind(this);
		document.addEventListener("keydown", this.boundCloseOnEscape);
		//ajout
		this.boundCustomCloseEvent = this.close.bind(this); // For custom event
		document.addEventListener("modal:close", this.boundCustomCloseEvent);
	}

	disconnect() {
		document.removeEventListener("keydown", this.boundCloseOnEscape);
		//ajout
		document.removeEventListener("modal:close", this.boundCustomCloseEvent);
	}

	closeOnEscape(e) {
		if (
			e.key === "Escape" &&
			this.hasModalElementTarget &&
			this.modalElementTarget.style.display !== "none"
		) {
			this.close();
		}
	}

	open(event) {
		event.preventDefault();
		event.stopPropagation();

		if (!this.hasModalElementTarget) {
			console.error(
				"ModalController: open() - ERREUR: modalElementTarget manquant."
			);
			return;
		}

		const url = event.currentTarget.dataset.modalUrlParam;
		const frameId =
			event.currentTarget.dataset.modalFrameIdParam || "sfd-details";
		if (!url) {
			console.error(
				"ModalController: open() - ERREUR: data-modal-url-param manquant."
			);
			return;
		}

		const modalFrame = this.modalElementTarget.querySelector(`#${frameId}`);
		if (!modalFrame) {
			console.error(
				`ModalController: open() - ERREUR: <turbo-frame id='${frameId}'> non trouvé.`
			);
			return;
		}

		modalFrame.src = url;
		this.modalElementTarget.style.display = "block";
		document.body.style.overflow = "hidden";
	}

	close() {
		if (!this.hasModalElementTarget) {
			console.error(
				"ModalController: close() - ERREUR: modalElementTarget manquant."
			);
			return;
		}
		this.modalElementTarget.style.display = "none";
		document.body.style.overflow = "auto";

		// Réinitialiser le turbo-frame (id dynamique)
		const frames = this.modalElementTarget.querySelectorAll("turbo-frame");
		frames.forEach((frame) => {
			frame.src = "";
			frame.innerHTML =
				'<div class="text-center py-8"><div class="animate-spin rounded-full h-12 w-12 border-b-2 border-gray-900 mx-auto"></div><p class="mt-4 text-gray-600">Chargement en cours...</p></div>';
		});
	}
}
