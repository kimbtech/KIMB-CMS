<?php

/*************************************************/
//KIMB CMS
//KIMB ContentManagementSystem
//Copyright (c) 2014 by KIMB-technologies
/*************************************************/
//This program is free software: you can redistribute it and/or modify
//it under the terms of the GNU General Public License version 3
//published by the Free Software Foundation.
//
//This program is distributed in the hope that it will be useful,
//but WITHOUT ANY WARRANTY; without even the implied warranty of
//MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//GNU General Public License for more details.
//
//You should have received a copy of the GNU General Public License
//along with this program.
/*************************************************/
//www.KIMB-technologies.eu
//www.bitbucket.org/kimbtech
//http://www.gnu.org/licenses/gpl-3.0
//http://www.gnu.org/licenses/gpl-3.0.txt
/*************************************************/

defined('KIMB_CMS') or die('No clean Request');

//Diese Datei beherbergt die Backend Klasse für alle Backend JavaScript Codes.
//Die Methoden werden von den anderen Backend Klassen aufgerufen.

//Die Dialoge basieren auf jQuery-UI $.dialog();

class JSforBE{
	
	//Klasse init.
	protected $allgsysconf, $sitecontent, $idfile, $menuenames;
	//nicht zu vergebende Menüpfade
	protected $nomenuepaths = 'var array = [ "ajax.php", "cron.php", "configurator.php", "index.php", "LICENSE.txt", "readme", "robots.txt","core","kimb-cms-backend", "load" ];';
	
	public function __construct( $allgsysconf, $sitecontent ){
		$this->allgsysconf = $allgsysconf;
		$this->sitecontent = $sitecontent;
	}
	
	//Menü neu erstellen
	public function for_menue_new(){
		$allgsysconf = $this->allgsysconf;
		$sitecontent = $this->sitecontent;

		//Überprüfung des Pfades auf nicht zu vergebenden Pfade
		//Generierung eines Pfades aus dem eingegebenen Namen des Menüs
		$sitecontent->add_html_header('<script>
		$(function() {
			$("i#pfad").css( "background-color", "gray" );
			$("i#pfad").css( "color", "white" );
			$("i#pfad").css( "padding", "5px" );
		} );
		'.$this->nomenuepaths.'
		function checkpath() {
			var umg = $( "input[name=pfad]" ).val();
			var check = $( "input[name=check]" ).val();
			
			pfad = umg.replace( /[^0-9A-Za-z_.-]/g, "");
		
			if(  jQuery.inArray( pfad, array  ) !== -1 ){
				$( "input[name=check]" ).val("nok");
				$("i#pfad").css( "background-color", "red" );
				$("i#pfad").text("(Dieser Pfad ist für das System reserviert!)");
			}
			else{
				$( "input[name=check]" ).val("ok");
				$("i#pfad").css( "background-color", "green" );
				$("i#pfad").text("(Dieser Pfad ist okay!)");
			}
			
			$( "input[name=pfad]" ).val( pfad );
		}
		
		function makepfad() {
			var name, klein, pfad, umg;
	
			name = $( "input[name=name]" ).val();
	
			klein = name.toLowerCase();
			umg = klein.replace( / /g , "-");
	
			$( "input[name=pfad]" ).val( umg );
			
			checkpath();
		}
		
		function checksubmit(){
			var check = $( "input[name=check]" ).val();
			if( check == "nok" ){
				return false;
			}
			return true;
		}
		</script>');
	}
	
	//Menüs auflisten
	public function for_menue_list() {
		$allgsysconf = $this->allgsysconf;
		$sitecontent = $this->sitecontent;
	
		//Ausführung des Löschvorgangs mit Abfragedialog
		//Abbruch des Löschvorgangs mit Hinweis bei vorhandenem Untermenü
		//verschieben des Menüs nach oben oder unten per AJAX und reload oder Fehlerdialog
		$sitecontent->add_html_header('<script>
		var del = function( fileid , requid , fileidbefore) {
			$( "#del-confirm" ).show( "fast" );
			$( "#del-confirm" ).dialog({
			resizable: false,
			height:200,
			modal: true,
			buttons: {
				"Delete": function() {
					$( this ).dialog( "close" );
					window.location = "'.$allgsysconf['siteurl'].'/kimb-cms-backend/menue.php?todo=del&file=" + fileid + "&reqid=" + requid + "&fileidbefore=" + fileidbefore;
					return true;
				},
				Cancel: function() {
					$( this ).dialog( "close" );
					return false;
				}
			}
			});
		}
		function delimp() {
			$( "#del-untermenue" ).show( "fast" );
			$( "#del-untermenue" ).dialog({
			resizable: false,
			height:200,
			modal: true,
			buttons: {
				"OK": function() {
					$( this ).dialog( "close" );
					return false;
				}
			}
			});
		}
		var updown = function( fileid , updo , requid ){
			$.get( "'.$allgsysconf['siteurl'].'/ajax.php?file=menue.php&fileid=" + fileid + "&updo=" + updo + "&requid=" + requid , function( data ) {
				if( data == "ok" ){
					location.reload();
				}
				else{
					$( "#updown" ).show( "fast" );
					$( "#updown" ).dialog({
					resizable: false,
					height:270,
					modal: true,
					buttons: {
						"OK": function() {
							$( this ).dialog( "close" );
							return false;
						}
					}
					});
				}
			});
		}
		</script>');
		
		//Inhalte der Dialoge
		$sitecontent->add_site_content('<div style="display:none;"><div id="del-confirm" title="Löschen?"><p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Möchten Sie das Menue wirklich löschen?</p></div></div>');
		$sitecontent->add_site_content('<div style="display:none;"><div id="del-untermenue" title="Löschen nicht möglich!"><p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Das Menue kann erst gelöscht werden, wenn es keine Untermenues mehr hat!</p></div></div>');
		$sitecontent->add_site_content('<div style="display:none;"><div id="updown" title="Fehler beim Verschieben!"><p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 100px 0;"></span>Achtung, Menues können nur innerhalb ihres Niveaus verschoben werden!<br /><br />Auch ein Verschieben auf einen höheren Platz als den Ersten oder einen tieferen als den Letzten ist nicht möglich!</p></div></div>');
	
	}
	
	//Menü berabeiten
	public function for_menue_edit( $file ){
		$allgsysconf = $this->allgsysconf;
		$sitecontent = $this->sitecontent;
		
				//Überprüfung des Pfades auf nicht zu vergebende Pfade und schon vergebene Pfade (AJAX)
				//Hinweis auf Gültigkeit des Pfades geben
				$sitecontent->add_html_header('<script>
				$(function() {
					$("i#pfadtext").text("(Menuepfad -- OK)");
					$("i#pfadtext").css( "background-color", "green" );
					$("i#pfadtext").css( "color", "white" );
					$("i#pfadtext").css( "padding", "5px" );
				});
				
				'.$this->nomenuepaths.'
				function checkpath(){
					var pathinput = $( "input#pfad" ).val();
					if(  jQuery.inArray( pathinput, array  ) !== -1 ){
						$( "input#check" ).val( "nok" );
						$("i#pfadtext").text("(Menuepfad -- Dieser Pfad ist für das System reserviert!)");
						$("i#pfadtext").css( "background-color", "red" );
					}
					else if( normpath != pathinput ){
	
						$( "input#check" ).val( "nok" );
						$("i#pfadtext").text("(Menuepfad -- Überprüfung läuft)");
						$("i#pfadtext").css( "background-color", "orange" );
	
						$.get( "'.$allgsysconf['siteurl'].'/ajax.php?file=menue.php&urlfile='.$file.'&search=" + pathinput , function( data ) {
							$( "input#check" ).val( data );
							if( data == "nok" ){
								$("i#pfadtext").text("(Menuepfad -- Achtung dieser Pfad ist schon vergeben!!)");
								$("i#pfadtext").css( "background-color", "red" );
							}
							else{
								$( "input#check" ).val( "ok" );
								$("i#pfadtext").text("(Menuepfad -- OK)");
								$("i#pfadtext").css( "background-color", "green" );
							}
						});
					}
					else{
						$( "input#check" ).val( "ok" );
						$("i#pfadtext").text("(Menuepfad -- OK)");
						$("i#pfadtext").css( "background-color", "green" );
					}
				}
				function pfadreplace() {
					$( "input#check" ).val( "nok" );
					$( "i#pfadtext" ).html("(Menuepfad -- Überprüfung ausstehend) <button onclick=\'checkpath(); return false;\'>Prüfen</button>");
					$( "i#pfadtext" ).css( "background-color", "blue" );
					$( "input#pfad" ).val( $( "input#pfad" ).val().replace( /[^0-9A-Za-z_.-]/g, "") );
				}
				</script>');
	}
	
	//Seite neu erstellen
	public function for_site_new( $selectmen, $selectunte, $achtung ){
		$sitecontent = $this->sitecontent;
		
		//Code für EasyMenü
		//Auswahl für Menü/Untermenü einblenden
		$sitecontent->add_html_header('<script>
			$(function() { 
				$( "ul#easymenue li" ).button();
			}); 
			function make_viewable( id ){
				$( "div#menueee" ).css( "display", "none" );
				$( "div#untermenueee" ).css( "display", "none" );
				$( "select[name=menue]" ).val( "none" );
				$( "select[name=untermenue]" ).val( "none" );
				$( "div#" + id ).css( "display", "block" );
				
				return false;
			}
		</script>');
		
		//HTML Code für EasyMenü
		$sitecontent->add_site_content('
			<h4>Easy Menue</h4>
			<ul id="easymenue">
				<li onclick="return make_viewable( \'menueee\' );">Neues Menü</li>
				<li onclick="return make_viewable(\'untermenueee\' );">Neues Untermenü</li>
				<li onclick="return make_viewable( \'eee\' );">Ausblenden</li>
			</ul>
			<div id="menueee" style="display:none;">
				Bitte wählen Sie bei welchem Menü das neue Menü für diese Seite erstellt werden soll!<br />
				'.$selectmen.$achtung.'
			</div>
			<div id="untermenueee" style="display:none;">
				Bitte wählen Sie unter welchem Menü das neue Menü für diese Seite erstellt werden soll!<br />
				'.$selectunte.$achtung.'
			</div>
		 <h4>Seitenerstellung</h4>');
	}
	
	//Seiten auflisten
	public function for_site_list(){
		$allgsysconf = $this->allgsysconf;
		$sitecontent = $this->sitecontent;
		
		//Dialog mit Löschabfrage
		//'Suche'
		$sitecontent->add_html_header('<script>
		var del = function( id ) {
			$( "#del-confirm" ).show( "fast" );
			$( "#del-confirm" ).dialog({
			resizable: false,
			height:200,
			modal: true,
			buttons: {
				"Delete": function() {
					$( this ).dialog( "close" );
					window.location = "'.$allgsysconf['siteurl'].'/kimb-cms-backend/sites.php?todo=del&id="+id;
					return true;
				},
				Cancel: function() {
					$( this ).dialog( "close" );
					return false;
				}
			}
			});
		}
		function search(){
			var search = $( "input.search" ).val();
			window.location = "'.$allgsysconf['siteurl'].'/kimb-cms-backend/sites.php?todo=list#" + search;
		}
		</script>');

		//Dialoginhalt
		$sitecontent->add_site_content('<div style="display:none;"><div id="del-confirm" title="Löschen?"><p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Möchten Sie die Seite wirklich löschen?</p></div></div>');
	}
	
	//Seite berabeiten
	public function for_site_edit(){
		$allgsysconf = $this->allgsysconf;
		$sitecontent = $this->sitecontent;
		
		//Dialog mit Löschabfrage
		//Hinzufügen von JavaScript Header Platzhaltern
		$sitecontent->add_html_header('<script>
		var del = function( id ) {
			$( "#del-confirm" ).show( "fast" );
			$( "#del-confirm" ).dialog({
			resizable: false,
			height:200,
			modal: true,
			buttons: {
				"Delete": function() {
					$( this ).dialog( "close" );
					window.location = "'.$allgsysconf['siteurl'].'/kimb-cms-backend/sites.php?todo=del&id="+id;
					return true;
				},
				Cancel: function() {
					$( this ).dialog( "close" );
					return false;
				}
			}
			});
		}
	
		$( function() {
			$( "#libs" ).on( "change", function() {
				var valadd, valold, valnew;
	
				valadd = $( "#libs" ).val();
				valold = $( "textarea[name=header]" ).val();
	
				valnew = valold + valadd;
	
				$( "textarea[name=header]" ).val( valnew );
	
				return false;
			});
		});
		</script>');
		
		//Dialoginhalt
		$sitecontent->add_site_content('<div style="display:none;"><div id="del-confirm" title="Löschen?"><p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Möchten Sie die Seite wirklich löschen?</p></div></div>');
	}
	
	//User erstellen Passwortindikator (Stärkebalken)
	protected function for_user_new_edit_passwind(){
		$allgsysconf = $this->allgsysconf;
		$sitecontent = $this->sitecontent;
		
		//Design
		//Hinzufügen des Balkens und setzen des Wertes
		//Errechnen des Wertes und anpassen des Balkens
		//Entfernen des Balkens
		$sitecontent->add_html_header('
		<style>
		#passbar{
			position: relative;
		}
		div.barlabel{
			position: absolute;
			left: 50%;
    			font-weight: bold;
			top:4px;
		}
		#passbar .ui-widget-header{
			background: url( "'.$allgsysconf['siteurl'].'/load/system/jquery/images/ui-bg_diagonals-medium_100_0f0_40x40.png" ) repeat scroll 50% 50% #0f0;
		}
		</style>
		<script>
		var added = false;
		function setbar( val, title ){
			if( added == false ){
				$( "div#pwind" ).append( "<div id=\'passbar\'><div class=\'barlabel\'></div></div>" );
				$( "div#passbar" ).css( "margin", "5px 0" );
				added = true;
			}
			$( "#passbar" ).progressbar({ value: val });
			$( ".barlabel" ).text( title );
			var barlabelcorr = $( ".barlabel" ).width() / 2;
			$( ".barlabel" ).css( "left", "calc( 50% - " + barlabelcorr + "px )" );
			
			$( "#passbar" ).css( "display", "block" );	
			return;
		}
		
		function passwordbarchange( id ){
			var inputval = $( "input#" + id ).val();
			var inputvallen = $( "input#" + id ).val().length;
			var passbarval = 0;
			var text;

			if( inputval.match(/([a-zA-Z])/) ){
				passbarval += 10;
			}
			if( inputval.match(/([A-Z])/) ){
				passbarval += 10;
			}
			if( inputval.match(/([0-9])/) ){
				passbarval += 5;
			}
			if( inputval.match(/([0-9].*[0-9])/) ){
				passbarval += 10;
			}
			if( inputval.match(/([0-9].*[0-9].*[0-9])/) ){
				passbarval += 10;
			}			
			if( inputval.match(/([!,%,&,@,#,*,?,_,])/) ){
				passbarval += 15;
			}
			if( inputval.match(/([!,%,&,@,#,*,?,_,].*[!,%,&,@,#,*,?,_,])/) ){
				passbarval += 15;
			}
			if( inputval.match(/([!,%,&,@,#,*,?,_,].*[!,%,&,@,#,*,?,_,].*[!,%,&,@,#,*,?,_,])/) ){
				passbarval += 15;
			}
			
			if( inputvallen > 5 ){
				inputvallen = inputvallen - 5;
				passbarval += inputvallen * 5;
			}
			else{
				passbarval = 0;
			}
			
			if( passbarval <= 25 ){
				text = "Das soll ein Passwort sein?";
			}
			else if( passbarval <= 50 ){
				text = "Gut, aber es geht noch besser!";
			}
			else if( passbarval <= 75 ){
				text = "Das sieht doch gut aus!";
			}
			else if( passbarval <= 100 ){
				text = "Da werden die Hacker schwitzen!";
			}
			
			setbar( passbarval, text );
			return;
		}
		function passbar_weg(){
			$( "#passbar" ).css( "display", "none" );
			return;
		}
		</script>');
	}
	
	//User erstellen
	public function for_user_new(){
		$allgsysconf = $this->allgsysconf;
		$sitecontent = $this->sitecontent;
		
		//Passwortindikator
		$this->for_user_new_edit_passwind();
		
		//Überprüfung des Usernamens (AJAX)
		//Passwörter vergleichen
		//E-Mail überprüfen
		//Alles vorm Submit prüfen, Passwort Hash
		//Hinweis auf Status des Usernamen 
		$sitecontent->add_html_header('<script>
		function checkuser(){
			var userinput = $( "input#user" ).val();
			if( "" != userinput ){
	
				$( "input#check" ).val( "nok" );
				$("i#textuser").text("Username -- Überprüfung läuft");
				$("i#textuser").css( "background-color", "orange" );
				$("i#textuser").css( "color", "white" );
				$("i#textuser").css( "padding", "5px" );
	
				$.get( "'.$allgsysconf['siteurl'].'/ajax.php?file=user.php&user=" + userinput , function( data ) {
					$( "input#check" ).val( data );
					if( data == "nok" ){
						$("i#textuser").text("Username - Achtung, dieser Name ist schon vergeben!!");
						$("i#textuser").css( "background-color", "red" );
					}
					else{
						$( "input#check" ).val( "ok" );
						$("i#textuser").text("Username - OK");
						$("i#textuser").css( "background-color", "green" );
					}
				});
			}
			else{
				$( "input#check" ).val( "ok" );
				$("i#pfadtext").text("(Menuepfad -- OK)");
				$("i#pfadtext").css( "background-color", "green" );
			}
		}
		function checkpw() {
			var valeins = $( "input#passwort1" ).val();
			var valzwei = $( "input#passwort2" ).val();
	
			if( valzwei == "" || valeins == "" ){
				$("i#pwtext").text( "Das Passwort darf nicht leer sein!!" );
				$("i#pwtext").css( "background-color", "red" );
				$("i#pwtext").css( "color", "white" );
				$("i#pwtext").css( "padding", "5px" );
			}
			else if( valzwei != valeins ){
				$("i#pwtext").text("Passwörter stimmen nicht überein!");
				$("i#pwtext").css( "background-color", "red" );
				$("i#pwtext").css( "color", "white" );
				$("i#pwtext").css( "padding", "5px" );
			}
			else{
				$("i#pwtext").text("Passwörter - OK");
				$("i#pwtext").css( "background-color", "green" );
				$("i#pwtext").css( "color", "white" );
				$("i#pwtext").css( "padding", "5px" );
			}
		}
		function checkmail(){
			var valmail = $( "input#mail" ).val();
			var mailmatch = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
	
			if( mailmatch.test( valmail ) ){
				$("i#mailadr").text( "E-Mail Adresse - OK" );
				$("i#mailadr").css( "background-color", "green" );
				$("i#mailadr").css( "color", "white" );
				$("i#mailadr").css( "padding", "5px" );
				
			}
			else{
				$("i#mailadr").text( "Die E-Mail Adresse ist fehlerhaft!" );
				$("i#mailadr").css( "background-color", "red" );
				$("i#mailadr").css( "color", "white" );
				$("i#mailadr").css( "padding", "5px" );
			}
		}
	
		function checksumbit(){
	
			var valeins = $( "input#passwort1" ).val();
			var valzwei = $( "input#passwort2" ).val();
			var valmail = $( "input#mail" ).val();
			var salt = $( "input#salt" ).val();
			
			if( $( "input#check" ).val() == "nok" ){
				return false;
			}
	
			var mailmatch = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
			if( mailmatch.test( valmail ) == false ){
				return false;
			}
	
			if( valzwei != valeins ){ 
				return false;
			}
			if( valeins != "" ) {
				$( "input#passwort1" ).val( SHA1( salt + valeins ) );
				$( "input#passwort2" ).val( "" );
				return true;
			}
			else{
				return false;
			}
	
		}
		function userreplace() {
			var usern;
	
			$( "input#check" ).val( "nok" );
	
			$("i#textuser").css( "color", "white" );
			$("i#textuser").css( "padding", "5px" );
			$( "i#textuser" ).html("(Username -- Überprüfung ausstehend) <button onclick=\'checkuser(); return false;\'>Prüfen</button>");
			$( "i#textuser" ).css( "background-color", "blue" );
			usern = $( "input#user" ).val().toLowerCase();
			$( "input#user" ).val(  usern.replace( /[^a-z]/g, "") );
		}
		</script>');
	}
	
	//User bearbeiten
	public function for_user_edit( $user ){
		$allgsysconf = $this->allgsysconf;
		$sitecontent = $this->sitecontent;
		
		//Passwortindikator
		$this->for_user_new_edit_passwind();
		
		//Dialog mit Löschabfrage
		//Passwörter überprüfen
		//Alles vorm Submit prüfen, Passwort Hash
		$sitecontent->add_html_header('<script>
			function deluser() {
				$( "#del-confirm" ).show( "fast" );
				$( "#del-confirm" ).dialog({
				resizable: false,
				height:220,
				modal: true,
				buttons: {
					"Delete": function() {
						$( this ).dialog( "close" );
						window.location = "'.$allgsysconf['siteurl'].'/kimb-cms-backend/user.php?todo=edit&user='.$user.'&del";
						return true;
					},
					Cancel: function() {
						$( this ).dialog( "close" );
						return false;
					}
				}
				});
			}
			function checkpw() {
				var valeins = $( "input#passwort1" ).val();
				var valzwei = $( "input#passwort2" ).val();
	
				if( valzwei == "" || valeins == "" ){
					$("i#pwtext").text( "Passwort - keine Änderung" );
					$("i#pwtext").css( "background-color", "white" );
					$("i#pwtext").css( "color", "black" );
				}
				else if( valzwei != valeins ){
					$("i#pwtext").text("Passwörter stimmen nicht überein!");
					$("i#pwtext").css( "background-color", "red" );
					$("i#pwtext").css( "color", "white" );
					$("i#pwtext").css( "padding", "5px" );
				}
				else{
					$("i#pwtext").text("Passwörter - OK");
					$("i#pwtext").css( "background-color", "green" );
					$("i#pwtext").css( "color", "white" );
					$("i#pwtext").css( "padding", "5px" );
				}
			}
			function changesub() {
				var valeins = $( "input#passwort1" ).val();
				var valzwei = $( "input#passwort2" ).val();
				var salt = $( "input#salt" ).val();
	
				if( valeins != valzwei ){
					return false;
				}
				if( valeins != "" ) {
					var valneu = SHA1( salt + valeins );
	
					$( "input#passwort1" ).val( valneu );
					$( "input#passwort2" ).val( "" );
	
					return true;
				}
				return true;
			}	
			</script>');
			
			//Dialoginhalt
			$sitecontent->add_site_content('<div style="display:none;"><div id="del-confirm" title="Löschen?"><p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 40px 0;"></span>Möchten Sie den User "'.$user.'" wirklich löschen?<br /><b>Sollten Sie alle User löschen verliehren Sie den Systemzugriff!</b></p></div></div>');
	}
	
	//Konfiguration
	public function for_syseinst_all(){
		$allgsysconf = $this->allgsysconf;
		$sitecontent = $this->sitecontent;
		
		//Dialog zum löschen von Inhalten
		$sitecontent->add_html_header('<script>
		var del = function( teil ) {
			$( "#del-confirm" ).show( "fast" );
			$( "#del-confirm" ).dialog({
			resizable: false,
			height: 250,
			modal: true,
			buttons: {
				"Delete": function() {
					$( this ).dialog( "close" );
					window.location = "'.$allgsysconf['siteurl'].'/kimb-cms-backend/syseinst.php?konf&todo=del&teil=" + teil;
					return true;
				},
				Cancel: function() {
					$( this ).dialog( "close" );
					return false;
				}
			}
			});
		}
		</script>');
		
		//Dialoginhalt
		$sitecontent->add_site_content('<div style="display:none;"><div id="del-confirm" title="Löschen?"><p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 50px 0;"></span>Möchten Sie den Wert wirklich löschen?<br />Tun Sie dies nur wenn Sie genau wissen was Sie tun!!</p></div></div>');
	}
	
	//Add-ons Installieren
	public function for_addon_inst(){
		$allgsysconf = $this->allgsysconf;
		$sitecontent = $this->sitecontent;
		
		//Dialog zum Löschen
		$sitecontent->add_html_header('<script>
		var del = function( addon ) {
			$( "#del-confirm" ).show( "fast" );
			$( "#del-confirm" ).dialog({
			resizable: false,
			height:200,
			modal: true,
			buttons: {
				"Delete": function() {
					$( this ).dialog( "close" );
					window.location = "'.$allgsysconf['siteurl'].'/kimb-cms-backend/addon_inst.php?addon=" + addon + "&del";
					return true;
				},
				Cancel: function() {
					$( this ).dialog( "close" );
					return false;
				}
			}
			});
		}
		</script>');
		
		//Dialoginhalt
		$sitecontent->add_site_content('<div style="display:none;"><div id="del-confirm" title="Löschen?"><p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Möchten Sie dieses Addon wirklich löschen?</p></div></div>');	
	}
}
?>
