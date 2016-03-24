$( function () {

	var dones = 0;
	
	var cssfiles = [
		 codemirrorloader_siteurl+'/load/system/codemirror/lib/codemirror.css'
	];
	
	cssfiles.forEach( function( v,k ) {
		$( "head" ).append( '<link rel="stylesheet" href="'+v+'">' );
		dones++; 
	});
	
	var jsfiles = [ 	  
		codemirrorloader_siteurl+'/load/system/codemirror/lib/codemirror.js',
		codemirrorloader_siteurl+'/load/system/codemirror/mode/xml/xml.js',
		codemirrorloader_siteurl+'/load/system/codemirror/mode/javascript/javascript.js', 
		codemirrorloader_siteurl+'/load/system/codemirror/mode/css/css.js',
		codemirrorloader_siteurl+'/load/system/codemirror/mode/htmlmixed/htmlmixed.js'
	];

	jsfiles.forEach( function( v,k ) {
		$.getScript( v ).done(function( script, textStatus ) {
			dones++;

			if( dones == ( jsfiles.length + cssfiles.length ) ){
				$( document ).trigger( 'cms_codemirror_loaded' );
				codemirrorloader_done = true;
			}			
		});		
	} );
});


