//Bei Markdown soll CodeMirror statt TinyMCE der
//Standardeditor sein!
//	PHP definiert _hashmd als true, wenn Markdownseite
function editorloader_setcodem(){
	// Markdownseite??
	if( typeof editorloader_hasmd !== "undefined" && editorloader_hasmd ){
		//noch keine Auswahl?
		var locst = window.localStorage.getItem( "editorloader" );
		if(
			locst != "tinymce" &&
			locst != "codemirror" &&
			locst != "textarea"
		){
			window.localStorage.setItem( "editorloader", "codemirror" );
		}
	}
}
editorloader_setcodem();

//Dialog zur Auswahl der Editoren anzeigen
$( function () {
	
	//Stift auf Seite oben rechts
	$( "div#content" ).prepend( '<div style="position:relative;"><button id="editorloader_dialog_btn" title="Editoren für Inhalte verwalten" style="display:inline-block; position:absolute; right:5px; top:5px;"><span class="ui-icon ui-icon-pencil"></span></button></div>' );
	
	//Dialog durch Klick öffnen
	$( "button#editorloader_dialog_btn" ).click( function (){
		
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
		
		html += '<p><small>Ihre Auswahl wird lokal in Ihrem Browser gespeichert. (Speicherdauer je nach Einstellung; mindestens jedoch für diese Session)</small></p>';
		html += '</div>';
		
		//Dialog HTML der Seite anfügen
		$( "body" ).append( html );
		
		//aktuellen Editor laden (aus localStorage)
		var editor = window.localStorage.getItem( "editorloader" );
		//	wenn leer => Standard TinyMCE
		if( editor !== null ){
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
					text: "Speichern & Übernehmen",
					icons: {
						primary: "ui-icon-circle-check"
					},
					click: function() {
						save_settings();
						$( this ).dialog( "close" );
					}
				},
				{
					text: "Schließen",
					icons: {
						primary: "ui-icon-circle-close"
					},
					click: function() {
						$( this ).dialog( "close" );
					}
				}
			]
					
		});
	});
	
	//Editorwahl aus Dialog lesen und ablegen
	function save_settings(){
		//alter Werte
		var old = window.localStorage.getItem( "editorloader" );
		//neuer Werte
		var editor = $( "input[name=editorloader_wahl]:checked" ).val();
		//neuen Wert ablegen
		window.localStorage.setItem( "editorloader", editor );
		
		//wenn Änderung, Editoren neu laden
		if( old != editor ){
			reload_all( old , editor );
		}
	}
	
	//Alle Editoren auf der Seite neu laden
	//	old => aktuell noch geladener Editor
	//	editor => neu zu ladender Editor
	function reload_all( old, editor ){
		
		//TinyMCE aktuell?
		if( old == "tinymce" ){
			
			//alle IDs durchgehen	
			editorloader_ids.forEach( function( v ){
				//Tiny weg
				tinymce.EditorManager.execCommand( "mceRemoveEditor", true, v);
			});
		}
		//CodeMirror aktuell
		else if( old == "codemirror" ){
			//alle IDs durchgehen	
			editorloader_ids.forEach( function( v ){
				//CodeMirror weg
				var cm = codemirrorloader_instances[v];
				cm.toTextArea();
			});
		}
		else if( old == "textarea" ){
			//Textarea muss nicht entladen werden 
		}
		//nicht gesetzt bedeutet TinyMCE
		else{
			//alle IDs durchgehen	
			editorloader_ids.forEach( function( v ){
				//Tiny weg
				tinymce.EditorManager.execCommand( "mceRemoveEditor", true, v);
			});
		}
		
		//wenn Textarea als Neues,
		//	kein laden nötig 
		if( editor != "textarea" ) {
			//alle IDs durchgehen	
			editorloader_ids.forEach( function( v ){
				//Editor jeweils neu laden
				editorloader_add( v, "no" );
			});
		}
		
		return true;
	}
});

//Liste aller IDs
var editorloader_ids = Array();
//Editor zu einer ID laden
//	id => ID der Textarea
//	save => wenn nicht leer, werden die IDs des Textareas gespeichert
function editorloader_add( id, save ){
	if( typeof save === "undefined" ){
		//ID merken
		editorloader_ids.push( id );
	}

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
			"emoticons paste textcolor colorpicker textpattern code",
			"fontawesome noneditable"
		],
		toolbar1: "styleselect | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent hr ",
		toolbar2: "fontselect | undo redo | forecolor backcolor | link image emoticons fontawesome | preview fullscreen | code searchreplace",
		image_advtab: true,
		language : "de",
		width : 680,
		height : 300,
		resize: "horizontal",
		content_css : codemirrorloader_siteurl+"/load/system/theme/design_for_tiny.min.css,"+codemirrorloader_siteurl+"/load/system/fontawesome/font-awesome.min.css",
		browser_spellcheck : true,
		image_list: codemirrorloader_siteurl+"/ajax.php?file=sites.php&img",
		link_list: codemirrorloader_siteurl+"/ajax.php?file=sites.php&links",
		autosave_interval: "20s",
		autosave_restore_when_empty: true,
		autosave_retention: "60m",
		menubar: "file edit insert view format table",
		convert_urls: false,
		setup: function(ed) {
			ed.on('init', function(e) {
				$( "iframe#"+id+"_ifr" ).tooltip({ disabled: true });
			});
		},
		extended_valid_elements: 'span[class]',
	});
}
