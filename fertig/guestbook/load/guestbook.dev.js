/*
//KIMB CMS Add-on
//KIMB ContentManagementSystem Add-on
//Copyright (c) 2014 by KIMB-technologies.eu
//https://www.KIMB-technologies.eu
//http://www.gnu.org/licenses/gpl-3.0
*/
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
	
	dis();

	if( place == 'new'){
		var loadto = 'div#guestadd';
	}
	else{
		var loadto = 'div#answer_' + place +'_add' ;
	}
	
	if( !addmitt ){	
		$( loadto ).html( noaddrights );
	}
	else{
		$( loadto ).html( '<img src="' + siteurl + '/load/system/spin_load.gif" title="Loading ..." title="Loading ...">' );
		
		$.get( siteurl + "/ajax.php?addon=guestbook&loadadd&pl=" + place )
		 	.done(function( data ) {
				$( loadto ).html( data );
				
				loadsumbit();
			})
			.fail(function() {
				$( loadto ).html( noload );
			});
	}
	
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
	
	$( "div.answer_add" ).css( 'display', 'none' );
	
	$.each( guestbook_answerdata.none, function (k,v) {
		$( "div.answer_" + v ).css( "display", "none" );		
	} );
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
		$( "div#prew_" + id ).html( '<img src="' + siteurl + '/load/system/spin_load.gif" title="Loading ..." title="Loading ...">' );
		
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

var guestbook_answerdata = { 'block' : [], 'none' : [] }

function answer( id, file ){
	
	if( file != 'none' ){
		loadto =  'div#answer_' + id +'_dis' ;
		
		$( loadto ).html( '<img src="' + siteurl + '/load/system/spin_load.gif" title="Loading ..." title="Loading ...">' );
		
		$.get( siteurl + "/ajax.php?addon=guestbook&answer&id=" + id + "&siteid=" + siteid )
	 	.done(function( data ) {
			 if( data == '' ){
				$( loadto ).html( unveroef );
				if( guestbook_answerdata.none.indexOf( id ) == -1 ){
					guestbook_answerdata.none.push( id );	
				} 
			 }
			 else{
				 $( loadto ).html( data );
			 }
		})
		.fail(function() {
			$( loadto ).html( answerr );
		});
		
		if( guestbook_answerdata.block.indexOf( id ) == -1 ){
			 guestbook_answerdata.block.push( id );	
		}
	}
	else{
		if( guestbook_answerdata.none.indexOf( id ) == -1 ){
			 guestbook_answerdata.none.push( id );	
		}
	}
	
	add( id );
	
	$( "div.answer_" + id ).css( "display", "block" );
	
	$( "div#answer_" + id + "_add" ).css( 'display', 'block' );
	
	return true;
}
