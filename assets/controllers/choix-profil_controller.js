import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
	static targets = ["toutLeMonde", "autreProfil"];

	connect() {
		this.toggleAutresProfils();
        this.checkGerantByDefault();
	}

	toggleAutresProfils() {
		const isToutLeMondeChecked = this.toutLeMondeTarget.checked;
		this.autreProfilTargets.forEach((checkbox) => {
			checkbox.disabled = isToutLeMondeChecked;
			if (isToutLeMondeChecked) {
				checkbox.checked = false;
			}
		});
	}

    checkGerantByDefault() {
        // Si aucune case n'est cochée, on coche "Gerant"
        const anyChecked = this.toutLeMondeTarget.checked || this.autreProfilTargets.some(cb => cb.checked);
        if (!anyChecked) {
            const gerantCheckbox = this.autreProfilTargets.find(cb => cb.value === "Gerant");
            if (gerantCheckbox) {
                gerantCheckbox.checked = true;
            }
        }
    }
}
