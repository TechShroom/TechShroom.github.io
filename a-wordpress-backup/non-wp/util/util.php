<?php
function isarray($var) {
	if(!isset($var) || $var === NULL) {
		return FALSE;
	}
	return (array) $var === $var ;	
}
?>