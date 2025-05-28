import { Controller } from "@hotwired/stimulus";
import { Editor } from "@tiptap/core";
import StarterKit from "@tiptap/starter-kit";
import Typography from "@tiptap/extension-typography";

export default class extends Controller {
	static targets = [
		"input",
		"editor",
		"boldButton",
		"italicButton",
		"headingButton",
	];
	static values = {
		content: String,
		editMode: { type: Boolean, default: true },
	};

	connect() {
		if (this.editModeValue) {
			this.initEditor();
		} else {
			this.initStaticContent();
		}
	}

	initEditor() {
		this.editorInstance = new Editor({
			element: this.editorTarget,
			extensions: [
				StarterKit.configure({
					heading: { levels: [2, 3, 4] },
				}),
				Typography,
			],
			content: this.contentValue,
			onUpdate: () => {
				if (this.hasInputTarget) {
					this.inputTarget.value = this.editorInstance.getHTML();
				}
				this.updateButtonStates();
			},
			onSelectionUpdate: () => {
				this.updateButtonStates();
			},
			editorProps: {
				attributes: {
					class:
						"focus:outline-none min-h-[120px] prose dark:prose-invert max-w-none p-3",
				},
			},
		});
		this.updateButtonStates();
	}

	initStaticContent() {
		// Applique le contenu directement avec le style
		this.editorTarget.innerHTML = this.contentValue || "<p>Non renseigné</p>";
		this.editorTarget.classList.add(
			"prose",
			"dark:prose-invert",
			"max-w-none",
			"p-3",
			"border",
			"border-gray-300",
			"dark:border-gray-600",
			"rounded-b-md"
		);
	}

	disconnect() {
		if (this.editorInstance) {
			this.editorInstance.destroy();
		}
	}

	// Toolbar actions
	toggleBold() {
		this.editorInstance?.chain().focus().toggleBold().run();
	}

	toggleItalic() {
		this.editorInstance?.chain().focus().toggleItalic().run();
	}

	setHeading(event) {
		const level = parseInt(event.currentTarget.dataset.level, 10);
		this.editorInstance?.chain().focus().toggleHeading({ level }).run();
	}

	updateButtonStates() {
		if (!this.editorInstance) return;

		if (this.hasBoldButtonTarget) {
			this.boldButtonTarget.classList.toggle(
				"is-active",
				this.editorInstance.isActive("bold")
			);
		}
		if (this.hasItalicButtonTarget) {
			this.italicButtonTarget.classList.toggle(
				"is-active",
				this.editorInstance.isActive("italic")
			);
		}
		if (this.hasHeadingButtonTarget) {
			this.headingButtonTarget.classList.toggle(
				"is-active",
				this.editorInstance.isActive("heading", { level: 2 })
			);
		}
	}
}
