<?php
// The list of allowed image MIME types associated to
// file extensions.
$imgallowedtypes = array(
		'image/png'  => 'png',
		'image/jpeg' => 'jpg'
);

$imgdataurl = &$_POST['dataURL'];

if (empty($imgdataurl)) {
	die('error');
}

// A data URL starts like this:
// data:[<MIME-type>][;charset="<encoding>"][;base64],<data>

// Here we find the comma delimiter.
$comma = strpos($imgdataurl, ',');
if (!$comma) {
	die('error');
}

$imginfo = substr($imgdataurl, 0, $comma);
if (empty($imginfo) || !isset($imgdataurl{($comma+2)})) {
	die('error');
}

// Split by ':' to find the 'data' prefix and the rest of
// the info.
$imginfo = explode(':', $imginfo);

// The array must have exactly two elements and the
// second element must not be empty.
if (count($imginfo) !== 2 || $imginfo[0] !== 'data' ||
		empty($imginfo[1])) {
	die('error');
}

// The MIME type must be given and it must be base64-encoded.
$imginfo = explode(';', $imginfo[1]);

if (count($imginfo) < 2 ||
		!array_key_exists($imginfo[0], $imgallowedtypes) ||
		($imginfo[1] !== 'base64' && $imginfo[2] !== 'base64')) {
	die('error');
}

$imgdest = 'tmp/' . sha1($imgdataurl) . '.' .
		$imgallowedtypes[$imginfo[0]];
$imgdataurl = substr($imgdataurl, $comma + 1);

if (!file_put_contents($imgdest, base64_decode($imgdataurl))) {
	die('error');
}

echo json_encode(array('successful' => true, 'urlNew' => $imgdest));