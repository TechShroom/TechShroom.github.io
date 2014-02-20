/*!
 * jQuery iFrame Inheritance
 *
 * Copyright (c) 2009 Eric Garside (http://eric.garside.name)
 * Dual licensed under:
 *      MIT: http://www.opensource.org/licenses/mit-license.php
 *      GPLv3: http://www.opensource.org/licenses/gpl-3.0.html
 */

function fetch() {
	if (!parent.parent.jQuery) {
		return false;
	}
	jQuery = parent.parent.jQuery;
	$ = jQuery;
	return true;
}

function setWhenAvaliable() {
	if (!fetch()) {
		setTimeout(setWhenAvaliable, 10);
	}
}

try {
	if (jQuery);
} catch (err) {
	setWhenAvaliable();
}