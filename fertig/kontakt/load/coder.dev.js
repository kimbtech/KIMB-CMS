function encode( d ){for(var c="",a="",b=0,b=0;b<d.length;b++)a=d.charAt(b),"@"==a&&(a="&#x40;"),"a"==a&&(a="&#x61;"),"b"==a&&(a="&#x62;"),"c"==a&&(a="&#x63;"),"d"==a&&(a="&#x64;"),"e"==a&&(a="&#x65;"),"f"==a&&(a="&#x66;"),"g"==a&&(a="&#x67;"),"h"==a&&(a="&#x68;"),"i"==a&&(a="&#x69;"),"j"==a&&(a="&#x6a;"),"k"==a&&(a="&#x6b;"),"l"==a&&(a="&#x6c;"),"m"==a&&(a="&#x6d;"),"n"==a&&(a="&#x6e;"),"o"==a&&(a="&#x6f;"),"p"==a&&(a="&#x70;"),"q"==a&&(a="&#x71;"),"r"==a&&
	(a="&#x72;"),"s"==a&&(a="&#x73;"),"t"==a&&(a="&#x74;"),"u"==a&&(a="&#x75;"),"v"==a&&(a="&#x76;"),"w"==a&&(a="&#x77;"),"x"==a&&(a="&#x78;"),"y"==a&&(a="&#x79;"),"z"==a&&(a="&#x7a;"),"A"==a&&(a="&#x41;"),"B"==a&&(a="&#x42;"),"C"==a&&(a="&#x43;"),"D"==a&&(a="&#x44;"),"E"==a&&(a="&#x45;"),"F"==a&&(a="&#x46;"),"G"==a&&(a="&#x47;"),"H"==a&&(a="&#x48;"),"I"==a&&(a="&#x49;"),"J"==a&&(a="&#x4a;"),"K"==a&&(a="&#x4b;"),"L"==a&&(a="&#x4c;"),"M"==a&&(a="&#x4d;"),"N"==a&&(a="&#x4e;"),"O"==a&&(a="&#x4f;"),"P"==
	a&&(a="&#x50;"),"Q"==a&&(a="&#x51;"),"R"==a&&(a="&#x52;"),"S"==a&&(a="&#x53;"),"T"==a&&(a="&#x54;"),"U"==a&&(a="&#x55;"),"V"==a&&(a="&#x56;"),"W"==a&&(a="&#x57;"),"X"==a&&(a="&#x58;"),"Y"==a&&(a="&#x59;"),"Z"==a&&(a="&#x5a;"),"\u00e4"==a&&(a="&#xe4;"),"\u00c4"==a&&(a="&#xc4;"),"\u00f6"==a&&(a="&#xf6;"),"\u00d6"==a&&(a="&#xd6;"),"\u00fc"==a&&(a="&#xfc;"),"\u00dc"==a&&(a="&#xdc;"),"-"==a&&(a="&#x2d;"),"."==a&&(a="&#x2e;"),"&"==a&&(a="&#x26;"),'"'==a&&(a="&#x22;"),"'"==a&&(a="&#x27;"),"\u00a7"==a&&(a=
	"&#xA7;"),"<"==a&&(a="&#x3C;"),">"==a&&(a="&#x3E;"),"!"==a&&(a="&#x21;"),"#"==a&&(a="&#x23;"),"$"==a&&(a="&#x24;"),"%"==a&&(a="&#x25;"),"("==a&&(a="&#x28;"),")"==a&&(a="&#x29;"),"/"==a&&(a="&#x2F;"),"+"==a&&(a="&#x2B;"),"*"==a&&(a="&#x2A;"),"."==a&&(a="&#x2E;"),","==a&&(a="&#x2C;"),"-"==a&&(a="&#x2D;"),"="==a&&(a="&#x3D;"),":"==a&&(a="&#x3A;"),";"==a&&(a="&#x3B;"),"?"==a&&(a="&#x3F;"),"["==a&&(a="&#x5B;"),"]"==a&&(a="&#x5D;"),"_"==a&&(a="&#x5F;"),"\\"==a&&(a="&#x5C;"),"{"==a&&(a="&#x7B;"),"}"==a&&
	(a="&#x7D;"),"|"==a&&(a="&#x7C;"),"~"==a&&(a="&#x7E;"),"\t"==a&&(a="&#x09;")," "==a&&(a="&#xA0;"),"\u00df"==a&&(a="&#xDF;"),"0"==a&&(a="&#x30;"),"1"==a&&(a="&#x31;"),"2"==a&&(a="&#x32;"),"3"==a&&(a="&#x33;"),"4"==a&&(a="&#x34;"),"5"==a&&(a="&#x35;"),"6"==a&&(a="&#x36;"),"7"==a&&(a="&#x37;"),"8"==a&&(a="&#x38;"),"9"==a&&(a="&#x39;"),"\n"==a&&(a="<br />\n"),"\r"==a&&(a="<br />\r"),c+=a; return c;}

function submitsecure(){
	var val = $( "textarea#othercont").val();
	val = encode( val );
	$( "textarea#othercont").val( val );
}