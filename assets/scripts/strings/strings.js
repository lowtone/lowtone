/**
 * String functions.
 */

/**
 * Remove whitespace from begin and end of a string.
 * @return {string} Returns the subject string.
 */
String.prototype.trim = function(){
	return this.replace(/^\s+|\s+$/g, "");
};

/**
 * Convert a string to camel-case.
 * @return {string} Returns the subject string.
 */
String.prototype.toCamel = function(){
	return this.replace(/(\-[a-z])/g, function($1) {
		return $1.toUpperCase().replace('-','');
	});
};

/**
 * Convert a camel-case string to dashed.
 * @return {string} Returns the subject string.
 */
String.prototype.toDash = function(){
	return this.replace(/([A-Z])/g, function($1) {
		return "-"+$1.toLowerCase();
	});
};

/**
 * Convert a camel-case string to underscored.
 * @return {string} Returns the subject string.
 */
String.prototype.toUnderscore = function(){
	return this.replace(/([A-Z])/g, function($1) {
		return "_"+$1.toLowerCase();
	});
};