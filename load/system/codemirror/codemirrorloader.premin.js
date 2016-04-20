$(function(){$('head').append('<style>@import url("'+codemirrorloader_siteurl+'/load/system/codemirror/lib/codemirror.css"); </style>')});var codemirrorloader_instances={};function codemirrorloader_add(domid){$(function(){codemirrorloader_instances[domid]=CodeMirror.fromTextArea(document.getElementById(domid),{lineNumbers:!0,mode:"gfm"});setTimeout(function(){var dodo=codemirrorloader_instances[domid];dodo.refresh()},1000)})}
//hier werden mit der makeloader.php alle Inhalte der CodeMirror JS Dateien angehängt!!
// =====================================
