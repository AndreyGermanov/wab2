<?
	include "qrlib.php";
	header("Content-type: image/png");
	QRcode::png(@$_GET["text"],'','H',4,2);
?>
