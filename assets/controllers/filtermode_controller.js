import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    static targets = ["filterMode", "sfds", "reseau"];

    connect() {
        this.toggleFields();
        if (this.hasFilterModeTarget) {
            this.filterModeTarget.addEventListener('change', () => this.toggleFields());
        }
    }

    toggleFields() {
        if (!this.hasFilterModeTarget) return;
        const value = this.filterModeTarget.value;
        console.log("La valeur de value : !",value);

        if (value === "all") {
            this.hide(this.sfdsTarget);
            this.hide(this.reseauTarget);
        } else if (value === "sfds") {
            this.show(this.sfdsTarget);
            this.hide(this.reseauTarget);
            this.clearSelect(this.reseauTarget);
        } else if (value === "reseau") {
            this.hide(this.sfdsTarget);
            this.show(this.reseauTarget);
            this.clearSelect(this.sfdsTarget);
        }
    }

    hide(element) {
        element.style.display = "none";
    }

    show(element) {
        element.style.display = "";
    }

    /*clearSelect(container) {
        if (!container) return;
        const select = container.querySelector('select');
        if (select && select.multiple) {
            Array.from(select.options).forEach(opt => opt.selected = false);
        }
        const input = container.querySelector('input');
        if (input && input.type === "text") {
            input.value = "";
        }
    }*/
        clearSelect(container) {
            if (!container) return;
        
            // 1. Désélectionner toutes les options du <select multiple>
            const select = container.querySelector('select');
            if (select && select.multiple) {
                Array.from(select.options).forEach(opt => opt.selected = false);
        
                // Déclencher un événement 'change' pour que UX Autocomplete se mette à jour
                select.dispatchEvent(new Event('change', { bubbles: true }));
            }
        
            // 2. Vider les champs input[type="hidden"] ou input[type="text"] utilisés par UX Autocomplete
            const hiddenInputs = container.querySelectorAll('input[type="hidden"][name], input[type="text"].autocomplete-input');
            hiddenInputs.forEach(input => {
                input.value = "";
                input.dispatchEvent(new Event('change', { bubbles: true }));
            });
        
            // 3. Si UX Autocomplete ajoute des badges ou des éléments visuels, les retirer (optionnel)
            const badges = container.querySelectorAll('.autocomplete-badge, .autocomplete__selected');
            badges.forEach(badge => badge.remove());
        }
}