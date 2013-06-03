/*
---
description: Implementation of XML-RPC for MooTools. Extends Natives. 
license: GPL
authors: Alexander Hofbauer
provides: [XMLRPC, XMLRPC.call, XMLRPC.encode, XMLRPC.decode]
requires:
  core:1.2.4: [Native, Class, Class.Extras, Event, Request]
...
*/

var XMLRPC = {};
XMLRPC.call = new Class
({
	Implements: [Events, Options],
	
	options: {
		url: '/',
		method: 'post',
		encoding: 'utf-8',
		headers: {
			'Content-Type': 'text/xml',
			'Accept': 'text/xml, application/xml, text/html'
		},
		addEmptyParams: true
	},

	// the two events provided by the mixin "Events"
	success: false,
	failure: false,
	
	// XMLHttpRequest object
	Request: false,
	storedXML: null,
	
	initialize: function(options) {
		this.setOptions(options);
		
		this.Request = new Request({
			url: this.options.url,
			method: this.options.method,
			encoding: this.options.encoding,
			headers: this.options.headers,
			urlEncoded: false,
			link: 'chain',
			
			onFailure: function(xhr) {
				this.fireEvent('failure', xhr);
			}.bind(this),
			
			onSuccess: function(response) {
				this.fireEvent('success', [this.XMLtoObject(response), response]);
			}.bind(this)
		});

		return this;
	},
	
	
	/**
	 * Sends a remote procedure call.
	 * If parameters are left blank it will try to resend the last sent call
	 * using already stored XML.
	 *
	 * @param remoteMethod name of the RP to call.
	 * @param params Array of elements of type XMLDataXMLRPC.
	 */
	send: function(remoteMethod, params) {
		if (remoteMethod) {
			this.storedXML = this.methodCall(remoteMethod, params);
		}
		if (this.storedXML !== null) {
			this.Request.send(this.storedXML);
		}
		return this;
	},
	
	
	/**
	 * Generates a valid methodCall structure that can be sent to
	 * an XML-RPC server.
	 * 
	 * @param String method Class.Method or Method to call
	 * @param Object params Object containing all RPC-types to send
	 */
	methodCall: function(method, params) {
		var xml = '<?xml version="1.0"?>';
		xml += '<methodCall>';
		xml += '<methodName>'+method+'</methodName>';
	
		if (params) {	
			xml += '<params>';
			if (params.length > 0) {
				params.each(function(param) {
					xml += '<param><value>' + param.toXML() + '</value></param>';
				});
			}
			xml += '</params>';
		} else if (this.options.addEmptyParams) {
			xml += '<params />';
		}
		xml += '</methodCall>';
		
		return xml;
	},
	
	
	/**
	 * Takes the pre-parsed response XML and converts it an Object.
	 * There are only fault and params as expected response.
	 * Does some browser dependent stuff.
	 * @see mootool's Request.HTML.processHTML() 
	 * 
	 * @param String xmlText the response retrieved in onSuccess event of Request
	 * @return Object response as object or null if not successful
	 */
	XMLtoObject: function(xmlText) {
		var doc, nodes;
	
		xmlText = xmlText.replace(/\n/g, '');
		xmlText = xmlText.replace(/\r/g, '');
		xmlText = xmlText.replace(/\t/g, '');
		
		if (Browser.Engine.trident){
			doc = new ActiveXObject('Microsoft.XMLDOM');
			doc.async = false;
			doc.loadXML('<root>' + xmlText + '</root>');
			nodes = doc.childNodes[0].childNodes[1];
			
		} else {
			doc = new DOMParser().parseFromString(xmlText, 'text/xml');
			nodes = doc.childNodes[0];
		}
		
		var fault = false;
		var valueContent;
		
		if (nodes.childNodes[0].nodeName == 'params') {
			/* "/methodResponse/params/param/value"; this is somehow stupid, I know */
			valueContent = nodes.childNodes[0].childNodes[0].childNodes[0];
			
		} else if (nodes.childNodes[0].nodeName == 'fault') {
			fault = true;
			/* "/methodResponse/fault/struct" */
			valueContent = nodes.childNodes[0].childNodes[0];
			
		} else {
			return null;
		}
		
		/* only one value is expected as response object, but it
		 * may contain arrays or structs */
		var obj = this.getResponseObject(valueContent);
		obj.fault = fault;
		return obj;
	},

	
	/**
	 * To parse the content of a "<methodResponse>" this method
	 * can convert single parameters to Types and therefor
	 * also goes through structs and arrays recursively.
	 */
	getResponseObject: (function(valueContent) {
		var name = '';
		var content = null;
		
		if (typeof valueContent.childNodes[0] !== 'undefined') {
			// this should make sure empty values are parsed correctly in IE
			if (valueContent.childNodes.length === 0) {
				name = 'string';
				content = '';
				valueContent = [];
				
			} else {
				name = valueContent.childNodes[0].nodeName.toLowerCase();
				if (typeof valueContent.childNodes[0].childNodes[0] !== 'undefined') {
					content = valueContent.childNodes[0].childNodes[0].data;
				} else {
					content = '';
				}
				valueContent = valueContent.childNodes[0];
			}
			
		} else {
			name = 'string';
			content = valueContent.data;
		}
		
		if (typeof content === 'undefined') content = '';
			
		switch (name) {
			case 'int':
			case 'i4':
				return Number(content);
				
			case 'boolean':
				return Boolean(content);
				
			case 'double':
				return Number(content).setXMLtag('double');
				
			case 'string':
			default:
				return String(content);
				
			case 'base64':
				return String(content).setXMLtag('base64');
				
			case 'datetime.iso8601':
				return new Date().setFromXML(content);
				
			case 'struct':
				var elements = new Hash();
				// IE fix
				for (var i = 0; i < valueContent.childNodes.length; i++) {
					elements[valueContent.childNodes[i].childNodes[0].childNodes[0].data] = 
						this.getResponseObject(valueContent.childNodes[i].childNodes[1]);
				}
				/*$each(valueContent.childNodes, function(node) {
					elements[node.childNodes[0].childNodes[0].data] = this.getResponseObject(node.childNodes[1]);
				}, this);*/
				return elements;
				
			case 'array':
				var data = [];
				// IE fix
				for (var i = 0; i < valueContent.childNodes[0].childNodes.length; i++) {
					data.push(this.getResponseObject(valueContent.childNodes[0].childNodes[i]));
				}				
				/*$each(valueContent.childNodes[0].childNodes, function(node) {
					data.push(this.getResponseObject(node));
				}, this);*/
				return data;
		}
	}).protect()
});


/* XML-RPC allows any characters except < and &. */
XMLRPC.encode = function(string) { return string.replace(/&/g, '&amp;').replace(/</g, '&lt;'); };
XMLRPC.decode = function(string) { return string.replace(/&lt;/g, '<').replace(/&amp;/g, '&'); };




/**
 * Implementation of XML-output for Natives
 */

Number.prototype.XMLtag = 'int';
String.prototype.XMLtag = 'string';
Array.prototype.XMLtag = 'array';
Hash.prototype.XMLtag = 'struct';
Boolean.prototype.XMLtag = 'boolean';
Date.prototype.XMLtag = 'dateTime.iso8601';

Native.implement([Number, String],
{
	/**
	 * Sets the tag of any datatype (e.g for boolean, i4, etc...)
	 */
	setXMLtag: function(type) {
		this.XMLtag = type;
		return this;
	},

	toXML: function() {
		var xml = '';
		var tag = this.XMLtag;
		
		xml += '<'+tag+'>';
		if (tag == 'string') {
			xml += XMLRPC.encode(this);
		} else {
			xml += this;
		}
		xml += '</'+tag+'>';
		
		return xml;
	}
});

Boolean.prototype.toXML = Number.prototype.toXML;


/**
 * Traversing for Arrays 
 */
Array.implement
({
	toXML: function() {
		var xml = '<array><data>';
		this.each(function(el) {
			xml += '<value>' + el.toXML() + '</value>';
		});
		xml += '</data></array>';
		
		return xml;
	}
});


/**
 * Traversing for Structs 
 */
Hash.implement
({
	toXML: function() {
		var xml = '<struct>';
		this.each(function(el, name) {
			xml += '<member>';
			xml += '<name>' + name + '</name>';
			xml += '<value>' + el.toXML() + '</value>';
			xml += '</member>';
		});
		xml += '</struct>';
		
		return xml;
	}
});


/**
 * Date is a bit different
 */
Date.implement
({
	timezone: '',
	setTimezone: function(string) {
		this.timezone = string;
		return this;
	},
	
	toXML: function() {
		var hours = this.getHours();
		if (hours < 10 ) hours = '0' + hours;
		var minutes = this.getMinutes();
		if (minutes < 10 ) minutes = '0' + minutes;
		var seconds = this.getSeconds();
		if (seconds < 10 ) seconds = '0' + seconds;
		
		var xml = '<'+this.XMLtag+'>';
		xml += String(this.getFullYear()) + String((this.getMonth()+1)) + String(this.getDate());
		xml += 'T' + hours + ':' + minutes + ':' + seconds + this.timezone;
		xml += '</'+this.XMLtag+'>';
		return xml;
	},
	
	// TODO: correct timezone handling, too lazy to think about it at the moment
	setFromXML: function(string) {
		var parsed = this.parseXML(string);
		this.setTimezone(parsed['timezone']);
		
		if (this.timezone == 'Z') {
			this.setUTCFullYear(parsed['year'], parsed['month']-1, parsed['day']);
			this.setUTCHours(parsed['hours'], parsed['minutes'], parsed['seconds']);
		} else {
			this.setFullYear(parsed['year'], parsed['month']-1, parsed['day']);
			this.setHours(parsed['hours'], parsed['minutes'], parsed['seconds']);
		}
		
		return this;
	},
	
	parseXML: function(string) {
		var year = Number(string.slice(0, 4));
		var month = Number(string.slice(4, 6));
		var day = Number(string.slice(6, 8));
		var hours = Number(string.slice(9, 11));
		var minutes = Number(string.slice(12, 14));
		var seconds = Number(string.slice(15, 17));
		var timezone = String(string.slice(17));
		return {
			year: year,	month: month, day: day,
			hours: hours, minutes: minutes,	seconds: seconds, timezone: timezone
		};
	}
});
