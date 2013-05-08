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

/**
 * _url('page')
 * _url('page', array('a' => 'b'))
 * _url('page', null, false, '&')
 */
function _url($page, $arguments = null, $display = true, $separator = '&amp;') {
    $page = explode('#', ltrim($page, '/') . '#', 2);

    foreach( (array) $arguments as $key => $val )
        if( is_numeric($key) ) {
            unset($arguments[$key]);
            $arguments[$val] = 1;
        }
    $arguments = $arguments ? '?' . http_build_query((array) $arguments, null, $separator) : null;
    
    $url  = HTTP_MOD_REWRITE ? $page[0] : ($page[0] ? 'index.php?page=' . $page[0] : null);
    $url .= $arguments;
    $url .= '#' . $page[1];
    $url = './' . trim($url, ' #');

    if( !$display )
      return $url;
    echo $url;
}

/**
 * _link('Text') = _link('Text', '#')
 * _link('Text', 'page')
 * _link('page', array('a' => 'b'))
 * _link('page', null, false, '&')
 */
function _link($text, $path = '#', $arguments = null, array $options = null) {
  $options = array_map(function($key, $value) { return ' ' . $key . '="' . addslashes($value) . '"'; }, (array) $options);
  $options = implode(' ', $options);

  echo '<a href="', _url($path, $arguments, false), '"', $options, '>', $text, '</a>';
}

?>