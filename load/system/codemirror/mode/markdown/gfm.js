// CodeMirror, copyright (c) by Marijn Haverbeke and others
// Distributed under an MIT license: http://codemirror.net/LICENSE

(function(mod){if(typeof exports=="object"&&typeof module=="object")
mod(require("../../lib/codemirror"),require("../markdown/markdown"),require("../../addon/mode/overlay"));else if(typeof define=="function"&&define.amd)
define(["../../lib/codemirror","../markdown/markdown","../../addon/mode/overlay"],mod);else mod(CodeMirror)})(function(CodeMirror){"use strict";var urlRE=/^((?:(?:aaas?|about|acap|adiumxtra|af[ps]|aim|apt|attachment|aw|beshare|bitcoin|bolo|callto|cap|chrome(?:-extension)?|cid|coap|com-eventbrite-attendee|content|crid|cvs|data|dav|dict|dlna-(?:playcontainer|playsingle)|dns|doi|dtn|dvb|ed2k|facetime|feed|file|finger|fish|ftp|geo|gg|git|gizmoproject|go|gopher|gtalk|h323|hcp|https?|iax|icap|icon|im|imap|info|ipn|ipp|irc[6s]?|iris(?:\.beep|\.lwz|\.xpc|\.xpcs)?|itms|jar|javascript|jms|keyparc|lastfm|ldaps?|magnet|mailto|maps|market|message|mid|mms|ms-help|msnim|msrps?|mtqp|mumble|mupdate|mvn|news|nfs|nih?|nntp|notes|oid|opaquelocktoken|palm|paparazzi|platform|pop|pres|proxy|psyc|query|res(?:ource)?|rmi|rsync|rtmp|rtsp|secondlife|service|session|sftp|sgn|shttp|sieve|sips?|skype|sm[bs]|snmp|soap\.beeps?|soldat|spotify|ssh|steam|svn|tag|teamspeak|tel(?:net)?|tftp|things|thismessage|tip|tn3270|tv|udp|unreal|urn|ut2004|vemmi|ventrilo|view-source|webcal|wss?|wtai|wyciwyg|xcon(?:-userid)?|xfire|xmlrpc\.beeps?|xmpp|xri|ymsgr|z39\.50[rs]?):(?:\/{1,3}|[a-z0-9%])|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]|\([^\s()<>]*\))+(?:\([^\s()<>]*\)|[^\s`*!()\[\]{};:'".,<>?«»“”‘’]))/i
CodeMirror.defineMode("gfm",function(config,modeConfig){var codeDepth=0;function blankLine(state){state.code=!1;return null}
var gfmOverlay={startState:function(){return{code:!1,codeBlock:!1,ateSpace:!1}},copyState:function(s){return{code:s.code,codeBlock:s.codeBlock,ateSpace:s.ateSpace}},token:function(stream,state){state.combineTokens=null;if(state.codeBlock){if(stream.match(/^```+/)){state.codeBlock=!1;return null}
stream.skipToEnd();return null}
if(stream.sol()){state.code=!1}
if(stream.sol()&&stream.match(/^```+/)){stream.skipToEnd();state.codeBlock=!0;return null}
if(stream.peek()==='`'){stream.next();var before=stream.pos;stream.eatWhile('`');var difference=1+stream.pos-before;if(!state.code){codeDepth=difference;state.code=!0}else{if(difference===codeDepth){state.code=!1}}
return null}else if(state.code){stream.next();return null}
if(stream.eatSpace()){state.ateSpace=!0;return null}
if(stream.sol()||state.ateSpace){state.ateSpace=!1;if(modeConfig.gitHubSpice!==!1){if(stream.match(/^(?:[a-zA-Z0-9\-_]+\/)?(?:[a-zA-Z0-9\-_]+@)?(?:[a-f0-9]{7,40}\b)/)){state.combineTokens=!0;return "link"}else if(stream.match(/^(?:[a-zA-Z0-9\-_]+\/)?(?:[a-zA-Z0-9\-_]+)?#[0-9]+\b/)){state.combineTokens=!0;return "link"}}}
if(stream.match(urlRE)&&stream.string.slice(stream.start-2,stream.start)!="]("&&(stream.start==0||/\W/.test(stream.string.charAt(stream.start-1)))){state.combineTokens=!0;return "link"}
stream.next();return null},blankLine:blankLine};var markdownConfig={underscoresBreakWords:!1,taskLists:!0,fencedCodeBlocks:'```',strikethrough:!0};for(var attr in modeConfig){markdownConfig[attr]=modeConfig[attr]}
markdownConfig.name="markdown";return CodeMirror.overlayMode(CodeMirror.getMode(config,markdownConfig),gfmOverlay);},"markdown");CodeMirror.defineMIME("text/x-gfm","gfm")})

$( document ).trigger( 'cms_codemirror_nextfile' );