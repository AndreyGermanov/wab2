<?php
/**
 * Common PHP utilities.
 * 
 * @author Ian Moore (imoore76 at yahoo dot com)
 * @copyright Copyright (C) 2011 Ian Moore (imoore76 at yahoo dot com)
 * @version $Id: utils.php 363 2011-11-04 18:43:32Z imooreyahoo@gmail.com $
 * @see phpVBoxConfigClass
 * @package phpVirtualBox
 * 
*/

require_once(dirname(__FILE__).'/config.php');

/**
 * Initialize session.
 * @param boolean $keepopen keep session open? The default is
 * 			to close the session after $_SESSION has been populated.
 * @uses $_SESSION
 */
function session_init($keepopen = false) {
	
	$settings = new phpVBoxConfigClass();

	// No session support? No login...
	if(@$settings->noAuth) {
		global $_SESSION;
		$_SESSION['valid'] = true;
		$_SESSION['authCheckHeartbeat'] = time();
		$_SESSION['admin'] = true;
		return;
	}
	
	// Sessions provided by auth module?
	if(@$settings->auth->capabilities['sessionStart']) {
		call_user_func(array($settings->auth, $settings->auth->capabilities['sessionStart']), $keepopen);
		return;
	}
	
	// Session is auto-started by PHP?
	if(!ini_get('session.auto_start')) {
	
		ini_set('session.use_trans_sid', 0);
		ini_set('session.use_only_cookies', 1);
		
		// Session path
		if(isset($settings->sessionSavePath)) {
			session_save_path($settings->sessionSavePath);
		}
	
		session_name((isset($settings->session_name) ? $settings->session_name : md5('phpvbx'.$_SERVER['DOCUMENT_ROOT'].$_SERVER['HTTP_USER_AGENT'])));
		session_start();
	
	}
	
	if(!$keepopen) session_write_close();
	
}

/**
 * Strip slashes from string. Needed to pass to array_walk
 * @param string $a string to strip slashes from
 */
function __vbx_stripslash(&$a) { $a = stripslashes($a); }

/**
 * Clean (strip slashes from if applicable) $_GET and $_POST and return
 * an array containing a merged array of both.
 * @return array
 */
function clean_request($r = null) {
	if(!$r) $r = array_merge($_GET,$_POST);
	if(function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) {array_walk_recursive($r,'__vbx_stripslash');}
	return $r;
}

if(!function_exists('hash')) {
// Lower security, but better than nothing
/**
 * Return a hash of the passed string. Mimmics PHP's hash() params
 * @param unused $type
 * @param string $str string to hash
 */
function hash($type,$str='') {
	return sha1(json_encode($str));
}
}
/*
 * Support for PHP compiled with --disable-json
 */
if(!function_exists('json_encode')) {
/**
 * Mimmics PHP's json_encode
 * @link http://au.php.net/manual/en/function.json-encode.php#82904
 * @param mixed $a variable to JSON encode
 * @param boolean $force_string force $a to be treated like a string.
 */
function json_encode($a=false,$force_string=false) {
    if (is_null($a)) return 'null';
    if ($a === false) return 'false';
    if ($a === true) return 'true';
    if (is_scalar($a)) {

      if (is_float($a)) {
        // Always use "." for floats.
        return floatval(str_replace(",", ".", strval($a)));
      }

      if (is_string($a) || $force_string) {
        static $jsonReplaces = array(array("\\", "/", "\n", "\t", "\r", "\b", "\f", '"'), array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"'));
        return '"' . json_encodeUnicodeString(str_replace($jsonReplaces[0], $jsonReplaces[1], $a)) . '"';
      } else return $a;

    }

    $isList = true;
    for ($i = 0, reset($a); $i < count($a); $i++, next($a)) {
      if (key($a) !== $i) {
        $isList = false;
        break;
      }
    }

    $result = array();
    if ($isList) {
      foreach ($a as $v) $result[] = json_encode($v);
      return '[' . join(',', $result) . ']';
    } else {
      foreach ($a as $k => $v) $result[] = json_encode($k,true).':'.json_encode($v);
      return '{' . join(',', $result) . '}';
    }

  }

    function json_encodeUnicodeString($value)
    {
        $strlen_var = strlen($value);
        $ascii = "";

        /**
         * Iterate over every character in the string,
         * escaping with a slash or encoding to UTF-8 where necessary
         */
        for($i = 0; $i < $strlen_var; $i++) {
            $ord_var_c = ord($value[$i]);

            switch (true) {
                case (($ord_var_c >= 0x20) && ($ord_var_c <= 0x7F)):
                    // characters U-00000000 - U-0000007F (same as ASCII)
                    $ascii .= $value[$i];
                    break;

                case (($ord_var_c & 0xE0) == 0xC0):
                    // characters U-00000080 - U-000007FF, mask 110XXXXX
                    // see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                    $char = pack('C*', $ord_var_c, ord($value[$i + 1]));
                    $i += 1;
                    $utf16 = json_utf82utf16($char);
                    $ascii .= sprintf('\u%04s', bin2hex($utf16));
                    break;

                case (($ord_var_c & 0xF0) == 0xE0):
                    // characters U-00000800 - U-0000FFFF, mask 1110XXXX
                    // see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                    $char = pack('C*', $ord_var_c,
                                 ord($value[$i + 1]),
                                 ord($value[$i + 2]));
                    $i += 2;
                    $utf16 = json_utf82utf16($char);
                    $ascii .= sprintf('\u%04s', bin2hex($utf16));
                    break;

                case (($ord_var_c & 0xF8) == 0xF0):
                    // characters U-00010000 - U-001FFFFF, mask 11110XXX
                    // see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                    $char = pack('C*', $ord_var_c,
                                 ord($value[$i + 1]),
                                 ord($value[$i + 2]),
                                 ord($value[$i + 3]));
                    $i += 3;
                    $utf16 = json_utf82utf16($char);
                    $ascii .= sprintf('\u%04s', bin2hex($utf16));
                    break;

                case (($ord_var_c & 0xFC) == 0xF8):
                    // characters U-00200000 - U-03FFFFFF, mask 111110XX
                    // see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                    $char = pack('C*', $ord_var_c,
                                 ord($value[$i + 1]),
                                 ord($value[$i + 2]),
                                 ord($value[$i + 3]),
                                 ord($value[$i + 4]));
                    $i += 4;
                    $utf16 = json_utf82utf16($char);
                    $ascii .= sprintf('\u%04s', bin2hex($utf16));
                    break;

                case (($ord_var_c & 0xFE) == 0xFC):
                    // characters U-04000000 - U-7FFFFFFF, mask 1111110X
                    // see http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                    $char = pack('C*', $ord_var_c,
                                 ord($value[$i + 1]),
                                 ord($value[$i + 2]),
                                 ord($value[$i + 3]),
                                 ord($value[$i + 4]),
                                 ord($value[$i + 5]));
                    $i += 5;
                    $utf16 = json_utf82utf16($char);
                    $ascii .= sprintf('\u%04s', bin2hex($utf16));
                    break;
            }
        }

        return $ascii;
     }
    /**
     * Convert a string from one UTF-8 char to one UTF-16 char.
     *
     * Normally should be handled by mb_convert_encoding, but
     * provides a slower PHP-only method for installations
     * that lack the multibye string extension.
     *
     * This method is from the Solar Framework by Paul M. Jones
     *
     * @link   http://solarphp.com
     * @param string $utf8 UTF-8 character
     * @return string UTF-16 character
     */
    function json_utf82utf16($utf8)
    {
        // Check for mb extension otherwise do by hand.
        if( function_exists('mb_convert_encoding') ) {
            return mb_convert_encoding($utf8, 'UTF-16', 'UTF-8');
        }

        switch (strlen($utf8)) {
            case 1:
                // this case should never be reached, because we are in ASCII range
                // see: http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                return $utf8;

            case 2:
                // return a UTF-16 character from a 2-byte UTF-8 char
                // see: http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                return chr(0x07 & (ord($utf8{0}) >> 2))
                     . chr((0xC0 & (ord($utf8{0}) << 6))
                         | (0x3F & ord($utf8{1})));

            case 3:
                // return a UTF-16 character from a 3-byte UTF-8 char
                // see: http://www.cl.cam.ac.uk/~mgk25/unicode.html#utf-8
                return chr((0xF0 & (ord($utf8{0}) << 4))
                         | (0x0F & (ord($utf8{1}) >> 2)))
                     . chr((0xC0 & (ord($utf8{1}) << 6))
                         | (0x7F & ord($utf8{2})));
        }

        // ignoring UTF-32 for now, sorry
        return '';
    }

}
