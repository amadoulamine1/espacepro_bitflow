// assets/controllers/modal_controller.js
import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
	static targets = ["modalElement"];

	connect() {
		console.log(
			"ModalController: connect() - Contrôleur connecté à l'élément:",
			this.element
		);
		if (this.hasModalElementTarget) {
			console.log(
				"ModalController: connect() - La cible modalElementTarget EST TROUVÉE:",
				this.modalElementTarget
			);
			this.modalElementTarget.addEventListener("click", (e) => {
				// Fermer seulement si le clic est directement sur le backdrop (modalElementTarget)
				if (e.target === this.modalElementTarget) {
					console.log(
						"ModalController: Clic sur le backdrop, fermeture du modal."
					);
					this.close();
				}
			});
		} else {
			console.error(
				"ModalController: connect() - ERREUR CRITIQUE: La cible modalElementTarget N'A PAS ÉTÉ TROUVÉE. Vérifiez data-modal-target='modalElement' dans votre HTML."
			);
		}

		// Fermer avec la touche Escape
		this.boundCloseOnEscape = this.closeOnEscape.bind(this);
		document.addEventListener("keydown", this.boundCloseOnEscape);
		console.log(
			"ModalController: connect() - Écouteur d'événement 'keydown' (Escape) ajouté."
		);
	}

	disconnect() {
		console.log("ModalController: disconnect() - Contrôleur déconnecté.");
		document.removeEventListener("keydown", this.boundCloseOnEscape);
		// Si vous ajoutiez l'écouteur de clic sur le backdrop dynamiquement et que this.modalElementTarget pouvait être retiré
		// avant le contrôleur, il faudrait aussi le nettoyer ici. Mais il est lié à la durée de vie du target.
	}

	closeOnEscape(e) {
		if (
			e.key === "Escape" &&
			this.hasModalElementTarget &&
			this.modalElementTarget.style.display === "block"
		) {
			console.log(
				"ModalController: closeOnEscape() - Touche Escape détectée, fermeture du modal."
			);
			this.close();
		}
	}

	open(event) {
		console.log(
			"ModalController: open() - Méthode appelée. Action déclenchée par:",
			event.currentTarget
		);
		event.preventDefault(); // Empêche le comportement par défaut (utile pour les liens ou boutons de formulaire)
		event.stopPropagation(); // Empêche l'événement de remonter aux éléments parents (ex: de bouton à TR) si vous avez des actions imbriquées.

		if (!this.hasModalElementTarget) {
			console.error(
				"ModalController: open() - ERREUR: La cible modalElementTarget N'A PAS ÉTÉ TROUVÉE au moment d'ouvrir."
			);
			return;
		}
		console.log(
			"ModalController: open() - modalElementTarget est présent:",
			this.modalElementTarget
		);

		const url = event.currentTarget.dataset.modalUrlParam;
		if (!url) {
			console.error(
				"ModalController: open() - ERREUR: L'attribut 'data-modal-url-param' est manquant sur l'élément cliqué:",
				event.currentTarget
			);
			return;
		}
		console.log("ModalController: open() - URL à charger dans le modal:", url);

		const modalFrame = this.modalElementTarget.querySelector("#sfd-details");
		if (!modalFrame) {
			console.error(
				"ModalController: open() - ERREUR: L'élément <turbo-frame id='sfd-details'> N'A PAS ÉTÉ TROUVÉ à l'intérieur de modalElementTarget."
			);
			return;
		}
		console.log(
			"ModalController: open() - turbo-frame #sfd-details trouvé:",
			modalFrame
		);

		// Charger les données
		modalFrame.src = url;
		console.log(
			"ModalController: open() - L'attribut 'src' du turbo-frame a été mis à jour."
		);

		// Afficher le modal
		this.modalElementTarget.style.display = "block";
		console.log(
			"ModalController: open() - Le style 'display' de modalElementTarget est maintenant 'block'."
		);
		document.body.style.overflow = "hidden";
		console.log("ModalController: open() - 'overflow' du body mis à 'hidden'.");
	}

	close() {
		console.log("ModalController: close() - Méthode appelée.");
		if (!this.hasModalElementTarget) {
			console.error(
				"ModalController: close() - ERREUR: La cible modalElementTarget N'A PAS ÉTÉ TROUVÉE au moment de fermer."
			);
			return;
		}
		this.modalElementTarget.style.display = "none";
		console.log(
			"ModalController: close() - Le style 'display' de modalElementTarget est maintenant 'none'."
		);
		document.body.style.overflow = "auto";
		console.log("ModalController: close() - 'overflow' du body mis à 'auto'.");

		// Réinitialiser le frame
		const modalFrame = this.modalElementTarget.querySelector("#sfd-details");
		if (modalFrame) {
			modalFrame.src = ""; // Important pour éviter de recharger l'ancien contenu si le modal est réouvert rapidement
			modalFrame.innerHTML =
				'<div class="text-center py-8"><div class="animate-spin rounded-full h-12 w-12 border-b-2 border-gray-900 mx-auto"></div><p class="mt-4 text-gray-600">Chargement en cours...</p></div>';
			console.log(
				"ModalController: close() - Contenu du turbo-frame #sfd-details réinitialisé."
			);
		} else {
			console.warn(
				"ModalController: close() - Attention: turbo-frame #sfd-details non trouvé pour réinitialisation lors de la fermeture."
			);
		}
	}
}
