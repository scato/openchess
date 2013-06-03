/*
---
description: Provides useful debug-functions on Natives
license: GPL
authors: Alexander Hofbauer
requires: XMLRPC
provides: XMLRPC.debug
...
*/

Native.implement([Number, String, Date],
{
	debugXML: function() {
		var content = (this.XMLtag == 'string') ? XMLRPC.encode(this.replace(/>/g, '&gt;')) : this;
		return '&lt;' + this.XMLtag + '&gt;,' + ($type(this)) + '|' + content;
	}	
});

Boolean.prototype.debugXML = Number.prototype.debugXML;


Native.implement([Array, Hash],
{
	getXMLIndent: (function(depth) {
		if (!depth || depth == 0) { return ''; }
		var indent = '';
		for (var i = 1; i <= depth; i++) {
			indent += '&nbsp;&nbsp;&nbsp;&nbsp;';
		}
		return indent;
	}),
	
	debugXML: function(depth) {
		var string = '';
		depth = depth || 0;
		var indent = this.getXMLIndent(depth);

		this.each(function(el, name) {
			string += indent;
			if (el.XMLtag == 'struct' || el.XMLtag == 'array') { 
				string += name + ' {\n';
			} else {
				string += name + ': ';
			}
			string += el.debugXML(depth+1);
			if (el.XMLtag == 'struct' || el.XMLtag == 'array') {
				string += indent + '}\n';
			} else {
				string += '\n';
			}
		});
		
		return string;
	}	
});