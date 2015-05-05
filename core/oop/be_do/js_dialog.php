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



defined('KIMB_Backend') or die('No clean Request');

class JSforBE{
	
	protected $allgsysconf, $sitecontent, $idfile, $menuenames;
	
	public function __construct( $allgsysconf, $sitecontent ){
		$this->allgsysconf = $allgsysconf;
		$this->sitecontent = $sitecontent;
	}
	
	public function for_menue_list() {
		$allgsysconf = $this->allgsysconf;
		$sitecontent = $this->sitecontent;
	
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
		
		$sitecontent->add_site_content('<div style="display:none;"><div id="del-confirm" title="Löschen?"><p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Möchten Sie das Menue wirklich löschen?</p></div></div>');
		$sitecontent->add_site_content('<div style="display:none;"><div id="del-untermenue" title="Löschen nicht möglich!"><p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Das Menue kann erst gelöscht werden, wenn es keine Untermenues mehr hat!</p></div></div>');
		$sitecontent->add_site_content('<div style="display:none;"><div id="updown" title="Fehler beim Verschieben!"><p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 100px 0;"></span>Achtung, Menues können nur innerhalb ihres Niveaus verschoben werden!<br /><br />Auch ein Verschieben auf einen höheren Platz als den Ersten oder einen tieferen als den Letzten ist nicht möglich!</p></div></div>');
	
	}
	
	public function for_menue_edit( $file ){
		$allgsysconf = $this->allgsysconf;
		$sitecontent = $this->sitecontent;
		
				$sitecontent->add_html_header('<script>
				$(function() {
					$("i#pfadtext").text("(Menuepfad -- OK)");
					$("i#pfadtext").css( "background-color", "green" );
					$("i#pfadtext").css( "color", "white" );
					$("i#pfadtext").css( "padding", "5px" );
				});
				function checkpath(){
					var pathinput = $( "input#pfad" ).val();
					if( normpath != pathinput ){
	
						$( "input#check" ).val( "nok" );
						$("i#pfadtext").text("(Menuepfad -- Überprüfung läuft)");
						$("i#pfadtext").css( "background-color", "orange" );
	
						$.get( "'.$allgsysconf['siteurl'].'/ajax.php?file=menue.php&urlfile='.$_GET['file'].'&search=" + pathinput , function( data ) {
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
					$( "input#pfad" ).val( $( "input#pfad" ).val().replace( /[^0-9A-Za-z_-]/g, "") );
				}
				</script>');
	}
	
	
}
?>
