/**
 * Allow using namespace.
 * http://blogger.ziesemer.com/2008/05/javascript-namespace-function.html
 */
String.prototype.namespace = function(separator) {
	var ns = this.split(separator || '.'),
    	o = window,
    	i,
    	len;
	for (i = 0, len = ns.length; i < len; i++) {
		o = o[ns[i]] = o[ns[i]] || {};
	}
	return o;
};

String.prototype.isClassExist = function() {
	return eval('typeof ' + this) == 'function';
};
