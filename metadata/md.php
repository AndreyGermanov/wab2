<?php
function getFiles($dir="") {
	$result = array();
	if ($dir=="")
		$dir = "metadata/";
	if (is_dir($dir)) {
		if ($dh = opendir($dir)) {
			while (($file = readdir($dh)) !== false) {
				if ($file!="." and $file!="..") {
					if (is_dir($dir."/".$file)) {
						$result = array_merge($result,getFiles($dir."/".$file));
					} else {
						$result[] = $dir."/".$file;
					}
				}
			}
			closedir($dh);
		}
	}
	return $result;
}

function getTopGroups($arr) {
	$result = array();
	if (is_array($arr)) {
		foreach ($arr as $key=>$value) {
			$found = false;
			foreach($arr as $value1) {
				if (isset($value1["groups"])) {
					if (array_search($key,@$value1["groups"])!==FALSE) {
						$found = true;
						break;
					}
				}
			} 
			if (!$found)
				$result[$key] = $value;
		}
	}
	return $result;
}

function getTopItems($items,$groups) {
	$result = array();
	foreach ($items as $key=>$value) {
		$found = false;
		if (is_array($groups)) {
			foreach($groups as $value1) {
				if (isset($value1["fields"])) {
					if (array_search($key,@$value1["fields"])!==FALSE) {
						$found = true;
						break;
					}
				}
			}
		}
		if (!$found)
			$result[$key] = $value;
	}
	return $result;	
}

$fields = array();
$groups = array();
$modelGroups=array();
$codes = array();
$codesGroups = array();
$models = array();
$modules = array();
$panels = array();
$interfaces = array();
$roles = array();
$addressbooks = array();
$tags = array();
$tagGroups = array();
$userSettings = array();

$metadata_classes = array
(
	"fields",
	"groups",
	"modelGroups",
	"codes",
	"codeGroups",
	"appconfig",
	"models",
	"modules",
	"objGroups",
	"panels",
	"interfaces",
	"roles",
	"addressbooks",
	"addressbookFieldsTemplate",
	"addressBookDefaultFields",
	"bannedUsers",
	"events",
	"profileItems",
	"tags",
	"tagGroups",
	"userSettings"
);

$files = array();
$files = array_merge($files,getFiles("metadata/classes"));
$files = array_merge($files,getFiles("metadata/events/"));
$files = array_merge($files,getFiles("metadata/fields/"));
$files = array_merge($files,getFiles("metadata/codes/models/"));
$files = array_merge($files,getFiles("metadata/codes/modules/"));
$files = array_merge($files,getFiles("metadata/codes/utils/"));
$files = array_merge($files,getFiles("metadata/global/"));
$files = array_merge($files,getFiles("metadata/modules/"));
$files = array_merge($files,getFiles("metadata/objgroups/"));
$files = array_merge($files,getFiles("metadata/panels/"));
$files = array_merge($files,getFiles("metadata/interfaces/"));
$files = array_merge($files,getFiles("roles/"));
$files = array_merge($files,getFiles("metadata/tags/"));

foreach($files as $value) {
	require_once($value);
	
}
?>
