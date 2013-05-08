<?php

/*
 * To enable URL rewriting, please set the $rewriting variable on 'true'
 *
 * Ensure you have done every other steps described on
 * https://github.com/Bioshox/Raspcontrol/wiki/Enable-URL-Rewriting#configure-your-web-server
 */


// check if rewrite URL is enable
if (function_exists('apache_get_modules')) {
  $mod_rewrite = in_array('mod_rewrite', apache_get_modules()) && strtolower(getenv('HTTP_MOD_REWRITE')) == 'on';
} else {
  $mod_rewrite = strtolower(getenv('HTTP_MOD_REWRITE')) == 'on';
}
define('HTTP_MOD_REWRITE', $mod_rewrite);

/*
 * Do NOT change the following lines
 */
error_reporting(0);
define('INDEX', './');
define('LOGIN', 'login.php');
define('FILE_PASS', '/etc/raspcontrol/database.aptmnt');

if ( HTTP_MOD_REWRITE ) {
  define('LOGOUT', './logout');
  define('DETAILS', './details');
}
else {
  define('LOGOUT', './login.php?logout');
  define('DETAILS', './?page=details');
}

?>