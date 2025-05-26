// assets/controllers/textarea_controller.js
import { Controller } from "@hotwired/stimulus";
import { Editor } from "@tiptap/core";
import StarterKit from "@tiptap/starter-kit";
import Highlight from "@tiptap/extension-highlight";
import Underline from "@tiptap/extension-underline";
import Link from "@tiptap/extension-link";
import TextAlign from "@tiptap/extension-text-align";
import Image from "@tiptap/extension-image";
import YouTube from "@tiptap/extension-youtube";
import TextStyle from "@tiptap/extension-text-style";
import FontFamily from "@tiptap/extension-font-family";
import { Color } from "@tiptap/extension-color";

export default class extends Controller {
	static targets = ["editor", "input", "viewer"];
	static values = {
		editMode: Boolean,
		content: String,
	};

	connect() {
		if (this.editModeValue) {
			this.initializeEditor();
		} else {
			this.initializeViewer();
		}
	}

	initializeEditor() {
		this.editor = new Editor({
			element: this.editorTarget,
			extensions: [
				StarterKit.configure({
					textStyle: false,
				}),
				TextStyle,
				Color,
				FontFamily,
				Highlight,
				Underline,
				Link.configure({
					openOnClick: false,
					autolink: true,
				}),
				TextAlign.configure({
					types: ["heading", "paragraph"],
				}),
				Image,
				YouTube,
			],
			content: this.contentValue,
			onUpdate: () => {
				this.inputTarget.value = this.editor.getHTML();
			},
			editorProps: {
				attributes: {
					class: "focus:outline-none min-h-[300px]",
				},
			},
		});
	}

	initializeViewer() {
		this.viewerTarget.innerHTML = this.contentValue;
	}

	// Toolbar actions
	toggleBold() {
		this.editor.chain().focus().toggleBold().run();
	}

	toggleItalic() {
		this.editor.chain().focus().toggleItalic().run();
	}

	toggleUnderline() {
		this.editor.chain().focus().toggleUnderline().run();
	}

	// Ajouter toutes les autres méthodes d'action...

	disconnect() {
		if (this.editor) {
			this.editor.destroy();
		}
	}
}
