<?
	header("Content-type: image/png");
	$path = @$_GET['path'];
	$im     = imagecreatefrompng($_SERVER["DOCUMENT_ROOT"]."/".$path);
	$img_dst = imagecreatefrompng($_SERVER["DOCUMENT_ROOT"]."/".$path);
	$sharetypes = @$_GET["sharetypes"];
	if ($sharetypes) {
		$sharetypes = explode(",",$sharetypes);
		foreach ($sharetypes as $value) {
			if ($value=="smb")
				$smb = true;
			if ($value=="nfs")
				$nfs = true;
			if ($value=="ftp")
				$ftp = true;
			if ($value=="dav")
				$dav = true;
			if ($value=="afp")
				$afp = true;
		}
		$c = array(array("x" => 3, "y" => 11),array("x" => imagesx($im)-20, "y" => 11),
		               array("x" => 3, "y" => imagesy($im)-25),array("x" => imagesx($im)-20, "y" => imagesy($im)-25));
		$i = 0;
		if (@$smb) {
			$red = imagecolorallocate($im, 180, 0, 0);
			imagestring($im, 2, $c[$i]["x"], $c[$i]["y"], "SMB", $red);
			$i++;
		}
		if (@$nfs) {
			$blue = imagecolorallocate($im, 0, 0, 150);
			imagestring($im, 2, $c[$i]["x"], $c[$i]["y"], "NFS", $blue);
			$i++;
		}
		if (@$ftp) {
			$green = imagecolorallocate($im, 0, 180, 0);
			imagestring($im, 2, $c[$i]["x"], $c[$i]["y"], "FTP", $green);
			$i++;
		}
		if (@$ftp) {
			$black = imagecolorallocate($im, 0, 0, 0);
			imagestring($im, 2, $c[$i]["x"], $c[$i]["y"], "DAV", $black);
			$i++;
		}
		if ($i<4) {
			if (@$afp) {
				$magenta = imagecolorallocate($im, 0, 150, 150);
				imagestring($im, 2, $c[$i]["x"], $c[$i]["y"], "AFP", $magenta);
				$i++;
			}
		}
	}
	
	$white = imagecolorallocate($img_dst,255,255,255);
	imagecolortransparent($img_dst, $white);
	imagealphablending($img_dst, false);
	imagesavealpha($img_dst, true);
	imagecopyresampled($img_dst, $im, 0, 0, 0, 0, imagesx($im), imagesy($im), imagesx($im),imagesy($im));
	imagepng($img_dst);
	imagedestroy($img_dst);
?>
