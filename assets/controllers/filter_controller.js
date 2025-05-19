/*import { Controller } from "@hotwired/stimulus";
import debounce from "lodash/debounce"; // You might need to install lodash: yarn add lodash

export default class extends Controller {
	static targets = ['input','select'];
	static values = {
		delay: { type: Number, default: 500 }, // Debounce delay in milliseconds
		// Ajout d'un tracking des valeurs précédentes
        previousValues: { type: Object, default: {} } 
	};

	connect() {
		console.log("Filter controller connected 🎉");
		// Debounce the submit method
		this.submitForm = debounce(this._submitForm, this.delayValue);
	}

	// Action triggered by input events on filter fields
	filter() {
		console.log(
			"Filter input changed, submitting form in",
			this.delayValue,
			"ms"
		);
		this.submitForm();
	}

	// The actual form submission logic
	_submitForm() {
		// Use requestSubmit() to submit the form, respecting Turbo Frames
		this.element.requestSubmit();
	}

	// Action to reset filters (optional, if you keep a reset button)
	reset() {
		// Clear all input fields within the form
		this.element
			.querySelectorAll('input[type="text"]')
			.forEach((input) => (input.value = ""));
		this.element
			.querySelectorAll("select")
			.forEach((select) => (select.selectedIndex = 0)); // Reset selects

		// Submit the form after resetting
		this._submitForm();
	}

	disconnect() {
		// Cancel any pending debounced submissions
		this.submitForm.cancel();
		console.log("Filter controller disconnected");
	}
}*/
// assets/controllers/filter_controller.js
import { Controller } from '@hotwired/stimulus';
import { debounce } from 'lodash';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['input'];
    static values = { 
        delay: { type: Number, default: 500 },
        previousValues: { type: Object, default: {} }
    };

    connect() {
        this.initializeValueTracking();
        this.debouncedSubmit = debounce(() => this.submitForm(), this.delayValue);
        
        // Écouteur spécifique pour le select (déclenche immédiatement sans debounce)
        this.limitSelect = this.element.querySelector('select[name="limit"]');
        if (this.limitSelect) {
            this.limitSelect.addEventListener('change', () => {
                this.debouncedSubmit.cancel(); // Annule tout debounce en attente
                this.submitForm(); // Soumet immédiatement
            });
        }
    }

    initializeValueTracking() {
        this.previousValuesValue = {};
        this.inputTargets.forEach(input => {
            this.previousValuesValue[input.name] = input.value;
        });
    }

	// Méthode renommée pour correspondre à l'action
    filter(event) {
        // Ne pas gérer le select limit ici
        if (event.target.name === 'limit') return;
        
        const input = event.target;
        const currentValue = input.value;
        const previousValue = this.previousValuesValue[input.name];

        if (currentValue !== previousValue) {
            this.previousValuesValue[input.name] = currentValue;
            this.debouncedSubmit();
        }
    }

	handleLimitChange(event) {
		this.debouncedSubmit.cancel(); // Annule les debounces en attente
		this.previousValuesValue[event.target.name] = event.target.value;
		this.submitForm(); // Soumet immédiatement
	}
    submitForm() {
        this.debouncedSubmit.cancel();
        this.element.requestSubmit();
    }

    disconnect() {
        this.debouncedSubmit.cancel();
        if (this.limitSelect) {
            this.limitSelect.removeEventListener('change', this.handleLimitChange);
        }
    }
}