#!/usr/bin/php -c /etc/php5/cli/php.ini
<?
#defined('__DIR__') or define('__DIR__', dirname(__FILE__));//PHP<5.3.0
#chdir(__DIR__);
chdir("/opt/WAB2");
$_SERVER['DOCUMENT_ROOT'] = dirname(dirname(__FILE__));
$_SERVER['PHP_AUTH_USER'] = "admin";

require_once 'boot.php';
require_once 'utils/functions.php';

function eraseOldFiles($dir,$period) {
    if (is_dir($dir) and file_exists($dir)) {
        if ($dh = opendir($dir)) {
            while (($file = readdir($dh)) !== false) {
                if ($file!="." and $file!="..") {
                    if (is_dir($dir."/".$file)) {
                        eraseOldFiles($dir."/".$file,$period);
                    } else {
                        $dt = time();
                        $dt2 = $dt-$period*24*60*60;
                        if (file_exists($dir."/".$file) and filemtime($dir."/".$file)<=$dt2) {
                            unlink($dir."/".$file);
                        }
                    }
                }
            }
            closedir($dh);
        }
        if (is_dir($dir) and file_exists($dir))
        {
			$dh2 = opendir($dir);
			$cnt = 0;
			while ($f = readdir($dh2)!==false) {
				if ($f!="." and $f!="..")                                
					$cnt++;
			}
			if ($cnt==0)
				rmdir($dir);
        }
    }
}

$obj = $Objects->get("FileServer_ControllerApplication_Network_shares");
$app = $Objects->get("ControllerApplication_Network");
$obj->loadShares(true);
$trashFolders = array();
foreach ($obj->shares as $share) {
    if (!isset($trashFolders[$share->recyclePath])) {
        if ($share->recycleBin and $share->recyclePeriod>0)            
            eraseOldFiles($app->remotePath.$share->recyclePath,$share->recyclePeriod);
    }
}
?>