document.addEventListener('DOMContentLoaded', function() {
	var editor = CodeMirror.fromTextArea(document.getElementById("gwd_codemirror_content"), {
		theme: "dracula", // Use your chosen theme
		mode: "text/x-sass", // Specify SASS mode
		lineNumbers: true, // Show line numbers
		fullScreen: false, // Enable fullscreen mode
		styleActiveLine: true, // Enable the active line feature
		search: true, // Enable search functionality
		lineWrapping: true, // Wrap long lines
		tabSize: 3, // Set tab size to 3 spaces
		indentUnit: 3, // Indentation size
		smartIndent: true,
		electricChars: true,
		indentWithTabs: false, // Use spaces for indentation
		autoCloseBrackets: true, // Automatically close brackets
		matchBrackets: true, // Highlight matching brackets
		extraKeys: {
			"Ctrl-Space": "autocomplete" // Enable autocomplete with Ctrl+Space
		},
		gutters: ["CodeMirror-lint-markers"], // Show linting
	});
	
	document.getElementById("fullscreen-button").addEventListener("click", function() {
		editor.setOption("fullScreen", !editor.getOption("fullScreen"));
	});
});
