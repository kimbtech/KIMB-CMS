function savesubmit(){
	var cont = $( "textarea#cont" ).val();
	var name = $( "input#name" ).val();
	var mail = $( "input#mail" ).val();
	
	localStorage.setItem( 'name' , name );
	localStorage.setItem( 'mail' , mail );
	localStorage.setItem( 'cont' , cont );
	
	return true;
}
function loadsumbit(){
	var name = localStorage.getItem( 'name' );
	var mail = localStorage.getItem( 'mail' );
	var cont = localStorage.getItem( 'cont' );
	
	$( "textarea#cont" ).val( cont );
	if( name != 'undefined' ){
		$( "input#name" ).val( name );
	}
	if( mail != 'undefined'){
		$( "input#mail" ).val( mail );
	}
	
	return true;
}
function delsubmit(){
	localStorage.removeItem( 'name' );
	localStorage.removeItem( 'mail' );
	localStorage.removeItem( 'cont' );
	
	 return true;
}

function add( place ){

	if( place == 'new'){
		var loadto = 'div#guestadd';
	}
	else{
		var loadto = 'div#answer_' + place +'_add' ;
	}
		
	$.get( siteurl + "/ajax.php?addon=guestbook&loadadd&pl=" + place + "&lang=" + langfile )
	 	.done(function( data ) {
			$( loadto ).html( data );
			
			loadsumbit();
		})
		.fail(function() {
			$( loadto ).html( noload );
		});
		
	if( place == 'new'){
		$( loadto ).css( "display" , "block" );
		
		$( "button#guestbuttdis" ).css( "display" , "block" );
		$( "button#guestbuttadd" ).css( "display" , "none" );
	}
}
function dis(){
	$( "div#guestadd" ).css( "display" , "none" );
	$( "button#guestbuttdis" ).css( "display" , "none" );
	$( "button#guestbuttadd" ).css( "display" , "block" );
}
function preview( id ){
	var cont = $( "textarea.cont_" + id ).val();
	var name = $( "input.name_" + id ).val();
	
	if( typeof name == 'undefined' ){
		name = '---username---'
	}
	
	if( cont == '' || name == '' ){
		$( "div#prew_" + id ).html( nameinh );
	}
	else{	
		$.post( siteurl + "/ajax.php?addon=guestbook", { "vorschau_name": name, "vorschau_cont": cont } )
	 		.done(function( data ) {
				$( "div#prew_" + id ).html( data );
	  		})
			.fail(function() {
				$( "div#prew_" + id ).html( ajaxerr );
			});
	}
		
	$( "div#prewarea_" + id ).css( "display" , "block" );

	savesubmit();

	return false;
}

function answer( id, file ){
	
	if( file != 'none' ){
		loadto =  'div#answer_' + id +'_dis' ;
		
		$.get( siteurl + "/ajax.php?addon=guestbook&answer&id=" + id + "&siteid=" + siteid )
	 	.done(function( data ) {
			$( loadto ).html( data );
			
			loadsumbit();
		})
		.fail(function() {
			$( loadto ).html( answerr );
		});
	}
	
	add( id );
	
	$( "div.answer_" + id ).css( "display", "block" );
	
	return true;
}
