<?php
/*************************************************/
//KIMB-technologies
//KIMB Miniskript
//www.KIMB-technologies.eu
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
//
//Dieses Skript beinhaltet eine PHP-Funktion, die die Versionsbezeichnungen des KIMB-CMS vergleichen kann. 
//
//
//	Verhaeltnis von $v1 zu $v2, z.B.:
//
//		return 'newer'	-> 	$v1 neuer als $v2
//		return 'older'	-> 	$v1 aelter als $v2
//		return 'same'	-> 	$v1 gleich wie $v2
//		return false	-> 	$v1 oder $v2 haben eine fehlerhafte Syntax
//

function compare_cms_vers( $v1 , $v2 ) {

	$v[0] = $v1;
	$v[1] = $v2;

	foreach( $v as $ver ){

		//Ganze erste Nummer
		$vpos = stripos( $ver , 'V' );
		$ppos = strpos( $ver , '.', $vpos );

		$lv = $ppos - $vpos;

		$teil['eins'] = substr( $ver , $vpos + 1 , $lv - 1 );

		//Kommastelle & A,B,F
		$ppos = strpos( $ver , '.' , $vpos );
		$apos = stripos( $ver , 'A', $ppos );
		$bpos = stripos( $ver , 'B', $ppos );
		$fpos = stripos( $ver , 'F', $ppos );

		if ( $apos !== false ){
			$lpos = $apos;
			$teil['bst'] = '1';
		}
		elseif( $bpos !== false ){
			$lpos = $bpos;
			$teil['bst'] = '2';
		}
		elseif( $fpos !== false ){
			$lpos = $fpos;
			$teil['bst'] = '3';
		}
		else{
			return false;
		}

		$lv = $lpos - $ppos;

		$teil['komma'] = substr( $ver , $ppos + 1 , $lv - 1 );

		//Patch
		$papos = stripos( $ver , '-p', $lpos );
	
		$patch = substr( $ver , $papos + 2 );

		$kpos = strrpos( $patch , ',' );

		if( $kpos !== false ){
			$patch = substr( $patch , $kpos + 1 );
		}

		$patch = preg_replace( "/\D/", '', $patch );  

		$teil['patch'] = $patch;

		//fertig

		foreach( $teil as $tei ){
			if( !is_numeric( $tei ) ){
				return false;
			}
		}

		$varr[] = $teil;
	}

	//Ganze erste Nummer
	if( $varr[0]['eins'] > $varr[1]['eins'] ){

		return 'newer';

	}
	elseif( $varr[0]['eins'] < $varr[1]['eins'] ){

		return 'older';

	}
	elseif( $varr[0]['eins'] == $varr[1]['eins'] ){

		//Kommastelle
		if( $varr[0]['komma'] > $varr[1]['komma'] ){

			return 'newer';

		}
		elseif( $varr[0]['komma'] < $varr[1]['komma'] ){

			return 'older';

		}
		elseif( $varr[0]['komma'] == $varr[1]['komma'] ){

			//A,B,F
			if( $varr[0]['bst'] > $varr[1]['bst'] ){

				return 'newer';

			}
			elseif( $varr[0]['bst'] < $varr[1]['bst'] ){

				return 'older';

			}
			elseif( $varr[0]['bst'] == $varr[1]['bst'] ){

				//Patch
				if( $varr[0]['patch'] > $varr[1]['patch'] ){

					return 'newer';

				}
				elseif( $varr[0]['patch'] < $varr[1]['patch'] ){

					return 'older';

				}
				elseif( $varr[0]['patch'] == $varr[1]['patch'] ){

					return 'same';

				}
				else{
					return false;
				}

			}
			else{
				return false;
			}

		}
		else{
			return false;
		}

	}
	else{
		return false;
	}
}

?>
