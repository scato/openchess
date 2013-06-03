(function() {

var METHOD_NOT_ALLOWED = "Method Not Allowed\n";
var INVALID_REQUEST = "Invalid Request\n";

var JSONRPCClient = function(path) {
	this.path = path;
    
    this.call = function(method, params) {
        var request;
        
        if(typeof(XMLHttpRequest) != "undefined") {
            request = new XMLHttpRequest();
        } else if(typeof(ActiveXObject) != "undefined") {
            request = new ActiveXObject("Microsoft.XMLHTTP");
        } else {
            throw new Error("No XMLHTTPRequest support detected");
        }
        
        // First we encode the request into JSON
        var requestJSON = JSON.encode({
            'id': '' + (new Date()).getTime(),
            'method': method,
            'params': params
        });
        // Then we build some basic headers.
        var headers = {
            'Content-Length': requestJSON.length
        }
        request.open("POST", this.path, false);
        for(var headerName in headers) {
            if(!headers.hasOwnProperty(headerName)) continue;
            request.setRequestHeader(headerName, headers[headerName]);
        }
        request.send(requestJSON);
        var response = JSON.decode(request.responseText);
        if(response.result !== undefined) {
        	return response.result;
        } else {
        	throw new Error(response.error.message);
        }
    };
}

JSONRPC = {
    
    trace: function(direction, message) {
        sys.puts('   ' + direction + '   ' + message);
    },
    
    getClient: function(port, host) {
        return new JSONRPCClient(port, host);
    }
};

})();
