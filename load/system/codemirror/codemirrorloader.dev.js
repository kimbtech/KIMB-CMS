//auf jQuery warten
$( function () {

	//CSS Datei für CodeMirror laden
	$( 'head' ).append('<style>@import url("'+codemirrorloader_siteurl+'/load/system/codemirror/lib/codemirror.css"); </style>');
});

//CodeMirror Instanzen
var codemirrorloader_instances = {};
//Einfaches Laden von CodeMirror
//	id => ID der Textarea, welche mit CodeMirror versehen werden soll
function codemirrorloader_add( domid ){
	$( function () {
		//einfach CodeMirror laden
		codemirrorloader_instances[domid] = CodeMirror.fromTextArea(document.getElementById( domid ), {
			lineNumbers: true,
			mode: "gfm"
		});
		
		setTimeout(function() {
			var dodo = codemirrorloader_instances[domid];
			dodo.refresh();
		}, 10);
	});
}

//hier werden mit der makeloader.php alle Inhalte der CodeMirror JS Dateien angehängt!!
