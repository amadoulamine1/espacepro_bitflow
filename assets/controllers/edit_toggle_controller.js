import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
	static targets = ["editor", "viewer", "textarea"];

	connect() {
		this.updateView();
	}

	toggle() {
		this.editing = !this.editing;
		this.updateView();
	}

	updateView() {
		this.editorTarget.classList.toggle("hidden", !this.editing);
		this.viewerTarget.classList.toggle("hidden", this.editing);

		if (!this.editing) {
			const content = this.textareaTarget.value;
			this.viewerTarget.innerHTML = content;
		}
	}

	get editing() {
		return this.data.get("editing") === "true";
	}

	set editing(value) {
		this.data.set("editing", value);
	}
}
