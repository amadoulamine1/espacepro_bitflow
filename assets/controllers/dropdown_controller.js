import { Controller } from "@hotwired/stimulus";

/* stimulusFetch: 'lazy' */
export default class extends Controller {
	static targets = ["menu"];

	toggle() {
		this.menuTarget.classList.toggle("hidden");
	}
}
