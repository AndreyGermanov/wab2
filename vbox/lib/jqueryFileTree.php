<?php
//
// jQuery File Tree PHP Connector
//
// Version 1.01
//
// Cory S.N. LaViska
// A Beautiful Site (http://abeautifulsite.net/)
// 24 March 2008
//
// History:
//
// 1.01 - updated to work with foreign characters in directory/file names (12 April 2008)
// 1.00 - released (24 March 2008)
//
// Output a list of files for jQuery File Tree
//
//	]--- Modified by Ian Moore for phpVirtualBox.
//
// $Id: jqueryFileTree.php 355 2011-10-24 16:58:58Z imooreyahoo@gmail.com $
//
//

# Turn off PHP notices
error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_WARNING & ~E_DEPRECATED);

global $vbox, $localbrowser, $allowed;

require_once(dirname(__FILE__).'/config.php');
require_once(dirname(__FILE__).'/utils.php');
require_once(dirname(__FILE__).'/vboxconnector.php');

error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_WARNING & ~E_DEPRECATED);

session_init();
if(!$_SESSION['valid']) return;

/*
 * Get Settings
 */
$settings = new phpVBoxConfigClass();


$vbox = new vboxconnector();
$vbox->connect();

/*
 * Clean request
 */
global $vboxRequest;
$vboxRequest = clean_request();

$allowed = $settings->browserRestrictFiles;
if(is_array($allowed) && count($allowed) > 0) $allowed = array_combine($allowed,$allowed);
else $allowed = array();


/* Get list of "allowed" folders from wmic? */
$folders = $settings->browserRestrictFolders;
if($vboxRequest['fullpath'] && !$settings->forceWindowsAllDriveList && !$settings->noWindowsDriveList && (!$folders || !is_array($folders) || !count($folders)) && stripos(PHP_OS,'win') === 0 &&  stripos($vbox->vbox->host->operatingSystem,'win') === 0) {

    exec("wmic logicaldisk get caption", $out);
    if(is_array($out) && count($out) > 2) {
    	
    	$folders = array();
    	
	    // Shift off header
	    array_shift($out);
	    
	    // Shift off footer
	    array_pop($out);
	    
	    // These are now restricted folders
	    foreach ($out as $val) {
	        $folders[] = $val . '\\';
	    }
    }

/* Just show all letters if vboxhost is windows and our web server is not... */
} else if($vboxRequest['fullpath'] && ($settings->forceWindowsAllDriveList || (!$settings->noWindowsDriveList && (!$folders || !is_array($folders) || !count($folders)) && stripos(PHP_OS,'win') === false && stripos($vbox->vbox->host->operatingSystem,'win') === 0))) {
	$folders = array();
    for($i = 67; $i < 91; $i++) {
    	$folders[] = chr($i) .':\\';
    }
}
if(is_array($folders) && count($folders) > 0) $folders = array_combine($folders,$folders);
else $folders = array();



$localbrowser = @$settings->browserLocal;

if($localbrowser) {
	define('DSEP', DIRECTORY_SEPARATOR);
} else {
	define('DSEP',$vbox->getDsep());
}

/* In some cases, "dir" passed is just a file name */
if(strpos($vboxRequest['dir'],DSEP)===false) {
	$vboxRequest['dir'] = DSEP;
}

$dir = $vboxRequest['dir'];
/* Check that folder restriction validates if it exists */
if($vboxRequest['dir'] != DSEP && count($folders)) {
	$valid = false;
	foreach($folders as $f) {
		if(strpos(strtoupper($dir),strtoupper($f)) === 0) {
			$valid = true;
			break;
		}
	}
	if(!$valid) {
		$vboxRequest['dir'] = DSEP;
	}
}

/* Folder Restriction with root '/' requested */
if($vboxRequest['dir'] == DSEP && count($folders)) {
	folder_start();
	foreach($folders as $f) folder_folder($f,true);
	folder_end();
	return;
} else {
	// Eliminate duplicate DSEPs
	$vboxRequest['dir'] = str_replace(DSEP.DSEP,DSEP,$vboxRequest['dir']);
}

/* Full, expanded path to $dir */
if($vboxRequest['fullpath']) {
	folder_start();
	if(count($folders)) {
		folder_start();
		foreach($folders as $f) {
			if((strtoupper($dir) != strtoupper($f)) && strpos(strtoupper($dir),strtoupper($f)) === 0) {
				folder_folder($f,true,true);
				$path = explode(DSEP,substr($dir,strlen($f)));
				printdir($f,$path);
			} else {
				folder_folder($f,true);
			}
		}
		folder_end();
	} else {

		$dir = explode(DSEP,$dir);
		$root = array_shift($dir).DSEP;
		folder_folder($root,true,true);
		printdir($root,$dir);
		echo('</li>');
	}

	folder_end();
	return;
}


/* Default action. Return dir requested */
printdir($dir);


function printdir($dir, $recurse=array()) {

	global $vbox, $localbrowser, $allowed, $vboxRequest;

	if($localbrowser) return printdirlocal($dir,$recurse);

	try {


		if(substr($dir,-1) != DSEP) $dir .= DSEP;
		
		$appl = $vbox->vbox->createAppliance();
		$vfs = $appl->createVFSExplorer('file://'.str_replace(DSEP.DSEP,DSEP,$dir));
		$progress = $vfs->update();
		$progress->waitForCompletion(-1);
		$progress->releaseRemote();
		list($files,$types) = $vfs->entryList();
		$vfs->releaseRemote();
		$appl->releaseRemote();

	} catch (Exception $e) {

		echo($e->getMessage());

		return;

	}

	// Sort files while preserving file / type
	$files = @array_combine($files,$types);
	@uksort($files,'strnatcasecmp');
	$types = @array_combine(range(0,count($files)-1),$files);
	$files = @array_keys($files);


	// Shift . and ..
	while($files[0] == '.' || $files[0] == '..') { array_shift($files); array_shift($types); }

	if(!count($files)) return;

	folder_start();

	// All dirs
	for($i = 0; $i < count($files); $i++) {
		$file = $files[$i];
		$file = $dir.$file;
		
		// Folder
		if($types[$i] == 4) {
			
			if(count($recurse) && (strcasecmp($recurse[0],vbox_basename($file)) == 0)) {
				folder_folder($file,false,true,count($recurse) == 1);
				printdir($dir.array_shift($recurse),$recurse);
				echo('</li>');
			} else {
				folder_folder($file);
			}
		}
	}
	if(!$vboxRequest['dirsOnly']) {
		// All files
		for($i = 0; $i < count($files); $i++) {
			$file = $files[$i];
			$file = str_replace(DSEP.DSEP,DSEP,$dir.DSEP.$file);

			if($types[$i] != 4) {

				$ext = strtolower(preg_replace('/^.*\./', '', $file));

				if(count($allowed) && !@$allowed['.'.strtolower($ext)]) continue;

				folder_file($file);
			}
		}
	}
	folder_end();

}

function printdirlocal($dir, $recurse=array()) {

	global $allowed, $vboxRequest;

	if(!(file_exists($dir) && ($files = @scandir($dir)))) return;

	@natcasesort($files);

	// Shift . and ..
	while($files[0] == '.' || $files[0] == '..') array_shift($files);

	if(!count($files)) return;

	folder_start();

	// All dirs
	foreach( $files as $file ) {
		$file = $dir.DSEP.$file;
		if( file_exists($file) && is_dir($file) ) {
			if(count($recurse) && (strcasecmp($recurse[0],vbox_basename($file)) == 0)) {
				folder_folder($file,false,true,count($recurse) == 1);
				printdir($dir.DSEP.array_shift($recurse),$recurse);
				echo('</li>');
			} else {
				folder_folder($file);
			}
		}
	}
	if(!$vboxRequest['dirsOnly']) {
		// All files
		foreach( $files as $file ) {
			$file = $dir.DSEP.$file;
			if( file_exists($file) && !is_dir($file) ) {

				$ext = strtolower(preg_replace('/^.*\./', '', $file));

				if(count($allowed) && !$allowed['.'.$ext]) continue;

				folder_file($file);
			}
		}
	}
	folder_end();

}

function vbox_basename($b) { return substr($b,strrpos($b,DSEP)+1); }
function folder_file($f) {
	$ext = strtolower(preg_replace('/^.*\./', '', $f));
	echo "<li class=\"file file_{$ext} vboxListItem\"><a href=\"#\" name='".htmlentities($f)."' rel=\"".htmlentities($f)."\">".htmlentities(vbox_basename($f))."</a></li>";
}
function folder_folder($f,$full=false,$expanded=false,$selected=false) {
	echo "<li class=\"directory ".($expanded ? 'expanded' : 'collapsed')." vboxListItem\"><a href=\"#\" class='".($selected ? 'vboxListItemSelected' : '')."' name='".htmlentities($f)."' rel=\"".htmlentities($f)."\">".htmlentities(($full ? $f : vbox_basename($f)))."</a>".($expanded ? '' : '</li>');
}

function folder_start() { echo "<ul class=\"jqueryFileTree\" style=\"display: none;\">"; }
function folder_end() { echo ("</ul>"); }
