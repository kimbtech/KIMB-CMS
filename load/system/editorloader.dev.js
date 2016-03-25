
//Dialog zur Auswahl der Editoren anzeigen
$( function () {
	
	//Stift auf Seite oben rechts
	$( "div#content" ).prepend( '<div style="position:relative;"><span class="ui-icon ui-icon-pencil" id="editorloader_dialog_btn" style="display:inline-block; position:absolute;; right:5px; top:5px;" title="Editoren für Inhalte verwalten"></span></div>' );
	
	//Dialog durch Klick öffnen
	$( "span#editorloader_dialog_btn" ).click( function (){
		
		//Dialog HTML
		var html = '<div id="editorloader_dialog" title="Editoren verwalten">';
		html += '<p>Bitte wählen Sie einen der folgenden Editoren, mit dem Sie die HTML und Markdown Inhalte der Seiten bearbeiten wollen:</p>';
		
		//Auswahlmöglichkeiten
		html += '<input type="radio" name="editorloader_wahl" value="tinymce">TinyMCE ';
		html += '<span class="ui-icon ui-icon-info" style="display:inline-block;" title="TinyMCE ist ein vollständig ausgestatteter WYSIWYG HTML Editor. So können Sie auch ohne HTML Kenntnisse Ihre Seiten bearbeiten."></span><br />';
		
		html += '<input type="radio" name="editorloader_wahl" value="codemirror">CodeMirror ';
		html += '<span class="ui-icon ui-icon-info" style="display:inline-block;" title="CodeMirror ist ein guter HTML und Markdown Editor mit Syntaxhilighting."></span><br />';
		
		html += '<input type="radio" name="editorloader_wahl" value="textarea">Textfeld ';
		html += '<span class="ui-icon ui-icon-info" style="display:inline-block;" title="Nutzen Sie ein einfaches Textfeld zur Eingabe der Inhalte."></span><br />';
		
		html += '</div>';
		
		//Dialog HTML der Seite anfügen
		$( "body" ).append( html );
		
		//aktuellen Editor laden (aus localStorage)
		var editor = window.localStorage.getItem( "editorloader" );
		//	wenn leer => Standard TinyMCE
		if( typeof editor !== "undefined" ){
			$( "input[name=editorloader_wahl][value="+editor+"]" ).prop("checked", true)
		}
		//	sonst wie aus localStorage
		else{
			$( "input[name=editorloader_wahl][value=tinymce]" ).prop("checked", true)
		}
		
		//Dialog anzeigen
		$( "div#editorloader_dialog" ).dialog({
			modal: true,
			minWidth: 500,
			maxWidth: 500,
			//beim Schließen, div aus DOM entfernen
			beforeClose: function( event, ui ) {
				$( this ).remove();
			}, 
			//button übernehmen
			buttons: [
				{
					text: "Übernehmen",
					icons: {
						primary: "ui-icon-circle-check"
					},
					click: function() {
						save_settings();
					}
				}
			]
					
		});
	});
	
	function save_settings(){
		var editor = $( "input[name=editorloader_wahl]:checked" ).val();
		window.localStorage.setItem( "editorloader", editor );
	}
});

//Liste aller IDs
var editorloader_ids = Array();
//Editor zu einer ID laden
//	id => ID der Textarea
function editorloader_add( id ){
	//ID merken
	editorloader_ids.push( id );

	//aktuell gewünschten Editor lesen
	var editor = window.localStorage.getItem( "editorloader" );
	//Tiny?
	if( editor == "tinymce" ){
		//TinyMCE laden
		tinymceloader_add( id );
	}
	//CodeMirror?
	else if( editor == "codemirror" ){
		//CodeMirror laden lassen
		codemirrorloader_add( id );
	}
	//Area?
	else if( editor == "textarea" ){
		//Nichts
	}
	else{
		//TinyMCE laden
		tinymceloader_add( id );
	}
}


//TinyMCE Loader
//	id => ID der Textarea für TinyMCE
function tinymceloader_add( id ){
	
	tinymce.init({
		selector: "#"+id,
		theme: "modern",
		plugins: [
			"advlist autosave autolink lists link image charmap preview hr anchor pagebreak",
			"searchreplace wordcount visualblocks visualchars fullscreen",
			"insertdatetime media nonbreaking save table contextmenu directionality",
			"emoticons paste textcolor colorpicker textpattern code"
		],
		toolbar1: "styleselect | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent hr",
		toolbar2: "fontselect | undo redo | forecolor backcolor | link image emoticons | preview fullscreen | code searchreplace",
		image_advtab: true,
		language : "de",
		width : 680,
		height : 300,
		resize: "horizontal",
		content_css : codemirrorloader_siteurl+"/load/system/theme/design_for_tiny.min.css",
		browser_spellcheck : true,
		image_list: codemirrorloader_siteurl+"/ajax.php?file=sites.php",
		autosave_interval: "20s",
		autosave_restore_when_empty: true,
		autosave_retention: "60m",
		menubar: "file edit insert view format table",
		convert_urls: false,
		setup: function(ed) {
			ed.on('init', function(e) {
				$( "iframe#"+id+"_ifr" ).tooltip({ disabled: true });
			});
		}
	});
}
