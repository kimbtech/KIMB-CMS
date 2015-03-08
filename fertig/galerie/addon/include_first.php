<?php

/*************************************************/
//KIMB-technologies
//KIMB CMS Add-on
//KIMB ContentManagementSystem
//WWW.KIMB-technologies.eu
/*************************************************/
//CC BY-ND 4.0
//http://creativecommons.org/licenses/by-nd/4.0/
//http://creativecommons.org/licenses/by-nd/4.0/legalcode
/*************************************************/
//THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING
//BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
//IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
//WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR
//IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
/*************************************************/

defined('KIMB_CMS') or die('No clean Request');

function saveklimg( $img, $klimg, $size ){
	
	$image = imagecreatefromstring( file_get_contents( $img ) );
	$width = imagesx($image);
	$height = imagesy($image);

	if( $height < $width ){
		$heightnew = $height * $size / $width ;
		$widthnew = $size;
	}
	elseif( $height > $width ){
		$widthnew = $width * $size / $height ;
		$heightnew = $size;
	}
	else{
		$widthnew = $size;
		$heightnew = $size;
	}
	
	$thumb = imagecreatetruecolor( $widthnew, $heightnew );
	imagecopyresampled( $thumb, $image, 0, 0, 0, 0, $widthnew, $heightnew, $width, $height);
	imagepng( $thumb, $klimg );
	imagedestroy( $thumb );
}

//System

$galerie['file'] = new KIMBdbf( 'addon/galerie__conf.kimb' );

$galerie['id'] = $galerie['file']->search_kimb_xxxid( $allgsiteid , 'siteid');

if( $galerie['id'] != false && $allgerr != '403' && !empty( $allgsiteid ) ){

	$sitecontent->add_html_header('<script language="javascript" src="'.$allgsysconf['siteurl'].'/load/system/jquery/jquery.min.js"></script>');

	$galerie['c'] = $galerie['file']->read_kimb_id( $galerie['id'] );

	if( !is_numeric( $galerie['c']['anz'] ) || $galerie['c']['anz'] <= 0 ){
		$galerie['c']['anz'] = '99999';
	}
	if( !is_numeric( $galerie['c']['size'] ) || $galerie['c']['size'] <= 0 ){
		$galerie['c']['anz'] = '250';
	}

	$echo = '<div id="galleryover" style="display:none;">';
		$echo .= '<div style="position: fixed; top:0; right:0; height:100%; width:100%; background-color:#aaa; opacity:0.7;">';
		$echo .= '</div>';
		$echo .= '<div style="position: absolute; top:0; right:0; height:100%; width:100%; z-index:1;">';
			$echo .= '<div style=" background-color:#888; margin:10px; border-radius:15px;">';
				$echo .= '<span style="float:right; margin-right:5px; margin-top:5px;" >';
					$echo .= '<button onclick="closeover();">Schlie&szlig;en</button>';
				$echo .= '</span><center>';

				$scandir = scandir( __DIR__.'/../../../load/userdata'.$galerie['c']['imgpath'] );

				foreach( $scandir as $file ){
					if( is_file( __DIR__.'/../../../load/userdata'.$galerie['c']['imgpath'].'/'.$file ) && strpos( $file , '--thumb--' ) === false && exif_imagetype( __DIR__.'/../../../load/userdata'.$galerie['c']['imgpath'].'/'.$file ) != false ){
						$files[] = $file;
					}
				}

				$anteile = count( $files ) - 1;

				if( $galerie['c']['rand'] == 'on' ){

					$i = 1;
					while ( $i <= $galerie['c']['anz'] ){

						$num = mt_rand( 0 , $anteile );

						if( count( $oldnums ) > $anteile ){
							break;
						}

						if( !in_array( $num , $oldnums ) ){

							$imggr = $allgsysconf['siteurl'].'/load/userdata'.$galerie['c']['imgpath'].'/'.$files[$num];

							//Gallery Thumb
							$imgkl = __DIR__.'/../../../load/userdata'.$galerie['c']['imgpath'].'/'.$files[$num].'--thumb--100px.png';

							if( !is_file( $imgkl ) ){
								saveklimg( __DIR__.'/../../../load/userdata'.$galerie['c']['imgpath'].'/'.$files[$num], $imgkl , 100 );
							}

							$imgkl = $allgsysconf['siteurl'].'/load/userdata'.$galerie['c']['imgpath'].'/'.$files[$num].'--thumb--100px.png';

							$echo .= '<span style="padding:10px;" ><img onclick="openover( \''.$imggr.'\' );" src="'.$imgkl.'" title="'.$files[$num].'" alt="'.$files[$num].'"></span>';

							//Normal Thumb
							$imgkl = __DIR__.'/../../../load/userdata'.$galerie['c']['imgpath'].'/'.$files[$num].'--thumb--'.$galerie['c']['size'].'px.png';

							if( !is_file( $imgkl ) ){
								saveklimg( __DIR__.'/../../../load/userdata'.$galerie['c']['imgpath'].'/'.$files[$num], $imgkl , $galerie['c']['size'] );
							}

							$imgkl = $allgsysconf['siteurl'].'/load/userdata'.$galerie['c']['imgpath'].'/'.$files[$num].'--thumb--'.$galerie['c']['size'].'px.png';

							$norm .= '<span style="padding:10px;" ><img onclick="openover( \''.$imggr.'\' );" src="'.$imgkl.'" title="'.$files[$num].'" alt="'.$files[$num].'"></span>';

							$oldnums[] = $num;
							$i++;
						}
					}

				}
				else{
					$i = 1;
					$num = 0;
					while ( $i <= $galerie['c']['anz'] ){

						if( count( $oldnums ) > $anteile ){
							break;
						}

						$imggr = $allgsysconf['siteurl'].'/load/userdata'.$galerie['c']['imgpath'].'/'.$files[$num];

						//Gallery Thumb
						$imgkl = __DIR__.'/../../../load/userdata'.$galerie['c']['imgpath'].'/'.$files[$num].'--thumb--100px.png';

						if( !is_file( $imgkl ) ){
							saveklimg( __DIR__.'/../../../load/userdata'.$galerie['c']['imgpath'].'/'.$files[$num], $imgkl , 100 );
						}

						$imgkl = $allgsysconf['siteurl'].'/load/userdata'.$galerie['c']['imgpath'].'/'.$files[$num].'--thumb--100px.png';

						$echo .= '<span style="padding:10px;" ><img onclick="openover( \''.$imggr.'\' );" src="'.$imgkl.'" title="'.$files[$num].'" alt="'.$files[$num].'"></span>';

						//Normal Thumb
						$imgkl = __DIR__.'/../../../load/userdata'.$galerie['c']['imgpath'].'/'.$files[$num].'--thumb--'.$galerie['c']['size'].'px.png';

						if( !is_file( $imgkl ) ){
							saveklimg( __DIR__.'/../../../load/userdata'.$galerie['c']['imgpath'].'/'.$files[$num], $imgkl , $galerie['c']['size'] );
						}

						$imgkl = $allgsysconf['siteurl'].'/load/userdata'.$galerie['c']['imgpath'].'/'.$files[$num].'--thumb--'.$galerie['c']['size'].'px.png';

						$norm .= '<span style="padding:10px;" ><img onclick="openover( \''.$imggr.'\' );" src="'.$imgkl.'" title="'.$files[$num].'" alt="'.$files[$num].'"></span>';

						$oldnums[] = $num;
						$num++;
						$i++;
					}
				}

			$echo .= '</center></div>';
			$echo .= '<div>';
				$echo .= '<img style="display:block; margin:auto;" id="gallerybigimg" src="" alt="Screenshot" title="Screenshot" >';
			$echo .= '</div>';
		$echo .= '</div>';
	$echo .= '</div>';

	$header = '<script>';
	$header .= 'var openover = function( imgurl ) { 
		$( "div#galleryover" ).css( "display", "block" );
		$( "img#gallerybigimg" ).attr( "src" , imgurl );
	}
	function closeover(){
		$( "div#galleryover" ).css( "display", "none" ); 
	}
	$(function () {
		$( "div.imggallerydisplayhere" ).html( $( "div.imggalleryallnone" ).html() ); 
	});
	';
	$header .= '</script>';

	$sitecontent->add_html_header( $header );

	$echo .= '<center><div style="background-color:#ddd; border-radius:15px;">';
	$echo .= $norm;
	$echo .= '</div></center>';

	$sitecontent->add_site_content( '<div class="imggalleryallnone" style="display:none;">'.$echo.'</div>' );

	if( $galerie['c']['pos'] == 'top' ){
		$sitecontent->add_site_content( '<div class="imggallerydisplayhere" style="background-color:#ddd; border-radius:15px;" >Bitte aktivieren Sie für die Bildergalerie JavaScript!</div>' );
	}
}

?>
