import { Controller } from "@hotwired/stimulus";
import Datepicker from "flowbite-datepicker/Datepicker";

export default class extends Controller {
	connect() {
		this.datepicker = new Datepicker(this.element, {
			format: "dd/mm/yyyy",
			autohide: true,
			language: "fr",
		});
	}

	disconnect() {
		this.datepicker.destroy();
	}
}
