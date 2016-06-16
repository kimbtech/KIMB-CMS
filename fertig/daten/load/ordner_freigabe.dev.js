$( function () {
	$( "li.dir" ).prepend( '<span class="ui-icon ui-icon-folder-collapsed"></span>' );
	$( "li.file" ).prepend( '<span class="ui-icon ui-icon-document"></span>' );
	$( "li.table" ).prepend( '<span class="ui-icon ui-icon-calculator"></span>' );
});

function open_file( url ){
	//Datei in PopUp öffnen
	window.open( url, "_blank", "width=900px,height=500px,top=20px,left=20px");
}

function ordner_hoch( path, restlink ){
	
	//letzten Slash am Ende weg (nur wenn vorhanden)
	if( path[(path.length-1)] == '/' ){
		//weg machen
		path = path.substr(0, (path.length-1));
	}
	//letzten Slash im Str suche
	var slash = path.lastIndexOf("/");

	//keiner mehr da, oder ganz am Anfang?
	if( slash == -1 || slash == 0 ){
		//Grundordner
		path = '/';
	}
	else{
		//Rest abschneiden
		path = path.substr(0, slash );
		path += '/';
	}

	window.location.href = restlink + encodeURIComponent( path );
}