<?
	header("Content-type: image/png");
	$path = @$_GET['path'];
	$style = @$_GET['style'];
	$im     = imagecreatefrompng($_SERVER["DOCUMENT_ROOT"]."/".$path);
	$img_dst = imagecreatefrompng($_SERVER["DOCUMENT_ROOT"]."/".$path);
	$red = imagecolorallocate($im, 255, 0, 0);	
	$green = imagecolorallocate($im, 0, 128, 0);	
	if ($style=="deleted") {
		imageline($im, 2, 2, 14, 14, $red);
		imageline($im, 3, 2, 15, 14, $red);
		imageline($im, 14, 2, 2, 14, $red);
		imageline($im, 13, 2, 1, 14, $red);
	}	
	if ($style=="registered") {
		imageline($im, 2, 2, 7, 12, $green);
		imageline($im, 3, 2, 8, 12, $green);
		imageline($im, 14, 2, 8, 12, $green);
		imageline($im, 13, 2, 7, 12, $green);
	}
	
	//$white = imagecolorallocate($im,255,255,255);
	
	imagealphablending($img_dst, false);
	imagesavealpha($img_dst, true);
	imagecopyresampled($img_dst, $im, 0, 0, 0, 0, imagesx($im), imagesy($im), imagesx($im),imagesy($im));
	imagepng($img_dst);
	imagedestroy($img_dst);
?>