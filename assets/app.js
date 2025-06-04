import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';

// start the Stimulus application
import './bootstrap';

// enable the interactive UI components from Flowbite
import 'flowbite';
import "flowbite-datepicker";
import "flowbite-typography";
//import "@tailwindcss-typography";
// enable the interactive UI components from Flowbite with Turbo
import 'flowbite/dist/flowbite.turbo.js';

import { SimpleDatatables } from "simple-datatables";
import { Typography } from "@tiptap/extension-typography";
//Quill editeur wysiwig
import Quill from "quill";

import ElementPlus from "element-plus";
import "element-plus/dist/index.css";


document.addEventListener("DOMContentLoaded", () => {
	const editorContainer = document.getElementById("quill-editor");
	if (editorContainer) {
		new Quill(editorContainer, {
			theme: "snow",
		});
	}
});

document.addEventListener("DOMContentLoaded", () => {
	const table = document.querySelector("#datatable");
	if (table) {
		new DataTable(table, {
			perPage: 10,
			searchable: true,
			fixedHeight: true,
		});
	}
});