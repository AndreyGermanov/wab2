<?php
/**
 * phpVirtualBox example configuration. 
 * @version $Id: config.php-example 365 2011-11-16 02:21:42Z imooreyahoo@gmail.com $
 *
 * rename to config.php and edit as needed.
 *
 */
class phpVBoxConfig {

/* Username / Password for system user that runs VirtualBox */
var $username = 'root';
var $password = '111111';

/* SOAP URL of vboxwebsrv (not phpVirtualBox's URL) */
var $location = 'http://127.0.0.1:18083/';

/* Default language. See languages folder for more language options.
 * Can also be changed in File -> Preferences -> Language in
 * phpVirtualBox.
 */
var $language = 'ru';

/*
 *
 * Not-so-common options / tweaking
 *
 */

// Multiple servers example config. Uncomment (remove /* and */) to use.
// Add ALL the servers you want to use. Even if you have the server set
// above. The default server will be the first one in the list.
/*
var $servers = array(
        array(
                'name' => 'London',
                'username' => 'user',
                'password' => 'pass',
                'location' => 'http://192.168.1.1:18083/',
                'authMaster' => true // Use this server for authentication
        ),
        array(
                'name' => 'New York',
                'username' => 'user2',
                'password' => 'pass2',
                'location' => 'http://192.168.1.2:18083/'
        ),
);
*/

// Disable authentication
var $noAuth = true;

// Host / ip to use for console connections
#var $consoleHost = '192.168.1.40';

// Disable "preview" box
#var $noPreview = true;

// Default preview box update interval in seconds
#var $previewUpdateInterval = 30;

// Preview box pixel width
#var $previewWidth = 180;

// Change default preview aspect ratio to 1. 
// http://www.wikipedia.org/wiki/Aspect_ratio_%28image%29#Previous_and_presently_used_aspect_ratios
#var $previewAspectRatio = 1.6;

// Enable custom VM icons
#var $enableCustomIcons = true;

/* Enable HardDisk IgnoreFlush configuration. This controls the "ExtraData" setting
 * in "VBoxInternal/Devices/[controller type]/0/LUN#[x]/Config/IgnoreFlush". See
 * Responding to guest IDE/SATA flush requests at:
 * http://www.virtualbox.org/manual/ch12.html#idp12757424
 *
 */
#var $enableHDFlushConfig = true;

/*
Allow to prompt deletion hard disk files on removal from Virtual Media Manager.
If this is not set, files are always kept. If this is set, you will be PROMPTED
to decide whether or not you would like to delete the hard disk file(s) when you
remove a hard disk from virtual media manager. You may still choose not to delete
the file when prompted.
*/
var $deleteOnRemove = true;

/*
 * File / Folder browser settings
 */

// Restrict file types
var $browserRestrictFiles = array('.iso','.vdi','.vmdk','.img','.bin','.vhd','.hdd','.ovf','.ova','.xml','.vbox','.cdr','.dmg','.ima','.dsk','.vfd');

// Restrict locations / folders
#var $browserRestrictFolders = array('D:\\','C:\\Users\\Ian'); // Or something like array('/home/vbox','/var/ISOs')

// Force use of local, web server based file browser instead of going through vboxwebsrv
#var $browserLocal = true;

// Disable file / folder browser.
#var $browserDisable = true;

// Disable Windows drive detection
#var $noWindowsDriveList = true;

// Just list all drives from C:\ - Z:\ without checking if they exist or not.
// This may be required on older Windows systems with more than one drive.
#var $forceWindowsAllDriveList = true;

/*
 * Misc
 */
 
/*
 * Auto-refresh interval in seconds for VirtualBox host memory usage information.
 * Any value below 3 will be ignored.
 */
var $hostMemInfoRefreshInterval = 5;

/* Show % of free host memory instead of % used */
#var $hostMemInfoShowFreePct = true;

/*
 * VM Memory warnings.
 * 
 * If $vmMemoryStartLimitWarn is enabled, each time a VM is started through
 * phpVirtualBox, it will check that the available host memory is greater than
 * the base and video memory of the VM + 50MB (a little bit of overhead). If it
 * is not, a confirmation dialog will be presented to confirm that you want to
 * start the VM.
 *
 * If $vmMemoryOffset is set (and $vmMemoryStartLimitWarn), $vmMemoryOffset
 * megabytes is subtracted from the available host memory before the check is
 * performed by $vmMemoryStartLimitWarn logic. For instance it may be a good
 * idea to always have VM memory requirements + 100MB free. 100 is the default.
 */
#var $vmMemoryStartLimitWarn = true;
#var $vmMemoryOffset = 100;


/*
 * Display guest additions version of a running VM on its Details tab
 */
#var $enableGuestAdditionsVersionDisplay = true;

/*
 * Display a "minimal" VM list. This will shrink the size of VMs in the VM list
 * and exclude some information so that they take up less space.
 */
#var $vmListMinimal = true;

/* Enable Firefox's "App Tab" notification support by changing the browser's title
 * when something in phpVirtualBox's VM list changes or an alert is triggered.
 */
#var $enableAppTabSupport = true;

/* Disable any of phpVirtualBox's main tabs */
#var $disableTabVMSnapshots = true; // Snapshots tab
#var $disableTabVMConsole = true; // Console tab

/* Screen resolutions for console tab */
var $consoleResolutions = array('640x480','800x600','1024x768','1280x720','1440x900');

/* Console tab keyboard layout. Currently Oracle's RDP client only supports EN and DE. */
var $consoleKeyboardLayout = 'EN';

/* Max number of network cards per VM. Do not set above VirtualBox's limit (typically 8) or below 1 */
var $nicMax = 4;

/* Enable advanced configuration items (normally hidden in the VirtualBox GUI)
 * Note that some of these items may not be translated to languages other than English. 
 */
#var $enableAdvancedConfig = true;

/* 
	Sorting VM List options

	var $vmListSort = 'name'; // Default. Sort VM list by VM name
	var $vmListSort = 'running'; // Place running VMs at the top of the list
	var $vmListSort = 'gui'; // Use drag-and-drop / manual vm ordering
	var $vmListSort = 'stateChange'; // Order by VMs' last state change
	var $vmListSort = 'os'; // Sort by OS type
	var $vmListSort = 'function(..){...}' // uses custom javascript function. Example follows:
	
	// This places running VMs at the top of the list, then orders by
	// the last VM state change, then by name. 
	var $vmListSort = 'function(a,b) {
		if(a.state == "Running" && b.state != "Running") return -1;
		if(b.state == "Running" && a.state != "Running") return 1;
		if(a.lastStateChange < b.lastStateChange) return 1;
		if(b.lastStateChange < a.lastStateChange) return -1;
		return strnatcasecmp(a.name,b.name);
	}';
	
	NOTE: In a multi-user situation, 'gui' is probably a bad idea.
	
*/
#var $vmListSort = 'name';

// Authentication library.
var $authLib = 'Builtin';

// VM ownership
#var $enforceVMOwnership = true;

// Per-user VM quota
#var $vmQuotaPerUser = 2;


// Allow VDE network configuration. This must be supported by the underlying VirtualBox installation!
// If you do not know what VDE networking is - you do not need it, it is probably not supported by your
// VirtualBox installation and will cause errors if enabled.
#var $enableVDE = true; 

// Disable setting SATA controllers port count to the max port number found when saving VMs.
#var $disableSataPortCount = true;

/* Enable Parallel Port configuration - EXPERIMENTAL
LPT support may or may not work for you. 
!!! VirtualBox LPT support only works in Linux. !!!
*/
#var $enableLPTConfig = true;

/* Enable HardDisk IgnoreFlush configuration. This controls the "ExtraData" setting
 * in "VBoxInternal/Devices/[controller type]/0/LUN#[x]/Config/IgnoreFlush". See
 * Responding to guest IDE/SATA flush requests at:
 * http://www.virtualbox.org/manual/ch12.html#idp12757424
*/
#var $enableHDFlushConfig = true;


/*
 * Cache tweaking.
 *
 */
// Refresh VM cache when VM Settings window is loaded. Default is true. Set to false to disable.
var $vmConfigRefresh = true;

// Path
#var $cachePath = '/tmp';

/* END SETTINGS  */


}



