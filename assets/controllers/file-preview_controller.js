import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static targets = ["modal", "content"];
    closeTimeoutId = null;

    connect() {
			//console.log("File Preview Controller CONNECTED!", this.element);
			this.closeTimeoutId = null;
			// Vérifier si les targets sont trouvés
			if (this.hasModalTarget) {
				console.log("Modal target found:", this.modalTarget);
			} else {
				console.error(
					'Modal target NOT found! Make sure you have data-file-preview-target="modal" on the controller\'s element or a child.'
				);
			}
			if (this.hasContentTarget) {
				console.log("Content target found:", this.contentTarget);
			} else {
				console.error(
					'Content target NOT found! Make sure you have data-file-preview-target="content" as a child of the controller\'s element.'
				);
			}
			this.boundCloseOnEscape = this.closeOnEscape.bind(this);
			document.addEventListener("keydown", this.boundCloseOnEscape);

			// Click outside modal content to close (only if modalTarget is the backdrop)
			if (this.hasModalTarget) {
				this.modalTarget.addEventListener("click", (event) => {
					if (event.target === this.modalTarget) {
						// Ensure click is on backdrop, not content
						this.close();
					}
				});
			}
		}

	open(event) {
		event.preventDefault();
        this.cancelScheduledClose(); // Annuler toute fermeture programmée
		const link = event.currentTarget;
		const url = link.dataset.filePreviewUrlParam;
        let type = "";
		const typeAttribute = link.dataset.filePreviewTypeParam;
        
		if (typeAttribute) {
			type = typeAttribute.toLowerCase();
		}

		// Si le type n'a pas été trouvé via l'attribut data-file-type OU si l'attribut était vide,
		// et si une URL est disponible, essayer de déduire l'extension de l'URL.
		if (!type && url) {
			const parts = url.split(".");
			if (parts.length > 1) type = parts.pop().toLowerCase();
		}
        let html = "";
		if (["jpg", "jpeg", "png", "gif", "webp", "bmp", "svg"].includes(type)) { // SVG ajouté ici
			html = `
                    <div style="position: relative; text-align: center;">
                        <img src="${url}" alt="Aperçu de l'image" style="max-width:100%; max-height:70vh; display:block; margin:auto;">
                        <a href="${url}" download
                           class="inline-block bg-blue-500 text-white font-bold py-2 px-4 rounded hover:bg-blue-700 mt-2"
                           style="text-decoration: none; margin-top: 10px;"
                           title="Télécharger l'image">
                            <svg class="inline-block w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M13 8V2H7v6H2l8 8 8-8h-5zM0 18h20v2H0v-2z"/></svg>
                            Télécharger
                        </a>
                    </div>`;
		} else if (type === "pdf") {
			// Utiliser un iframe est généralement plus fiable pour les PDF
			html = `
                    <div style="position: relative; text-align: center;">
                        <iframe src="${url}" style="width:75vw; height:70vh; border:none;" title="Aperçu PDF">
                            <p>Votre navigateur ne peut pas afficher ce PDF. <a href="${url}" target="_blank" rel="noopener noreferrer">Télécharger le PDF</a></p>
                        </iframe>
                        <a href="${url}" download
                           class="inline-block bg-blue-500 text-white font-bold py-2 px-4 rounded hover:bg-blue-700 mt-2"
                           style="text-decoration: none; margin-top: 10px;"
                           title="Télécharger le PDF">
                            <svg class="inline-block w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M13 8V2H7v6H2l8 8 8-8h-5zM0 18h20v2H0v-2z"/></svg>
                            Télécharger
                        </a>
                    </div>`;
		} else {
			html = `<div class="text-center p-4">
                        <p>Aperçu non disponible pour ce type de fichier (${type}).</p>
                        <a href="${url}" target="_blank" rel="noopener noreferrer" download class="inline-block bg-blue-500 text-white font-bold py-2 px-4 rounded hover:bg-blue-700 mt-2">
                            Télécharger le fichier
                        </a>
                    </div>`;
		}

		this.contentTarget.innerHTML = html;
		this.modalTarget.style.display = "flex";
        document.body.style.overflow = "hidden"; // Empêche le défilement de l'arrière-plan
	}

    scheduleClose() {
        // Annuler tout timeout précédent pour éviter les fermetures multiples
        this.cancelScheduledClose();
        this.closeTimeoutId = setTimeout(() => {
            this.close();
        }, 300); // Délai de 300ms avant de fermer
        console.log("Close scheduled", this.closeTimeoutId);
    }

    cancelScheduledClose() {
        if (this.closeTimeoutId) {
            clearTimeout(this.closeTimeoutId);
            console.log("Close canceled", this.closeTimeoutId);
            this.closeTimeoutId = null;
        }
    }

	close() {
        // S'assurer qu'il n'y a pas de timeout en attente qui pourrait rouvrir/fermer
        this.cancelScheduledClose(); 
        console.log("Closing modal NOW");
		this.modalTarget.style.display = "none";
		this.contentTarget.innerHTML = "";
        document.body.style.overflow = "auto"; // Restaure le défilement
	}
    closeOnEscape(event) {
        // Check if the modal is actually visible before trying to close
        if (event.key === "Escape" && this.hasModalTarget && this.modalTarget.style.display !== "none") {
            this.close();
        }
    }

    disconnect() {
        this.cancelScheduledClose(); // Nettoyer le timeout si le contrôleur est déconnecté
        document.removeEventListener("keydown", this.boundCloseOnEscape);
    }
}
