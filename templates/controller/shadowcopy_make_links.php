#!/usr/bin/php -c /etc/php5/cli/php.ini
<?

// Скрипт делает две вещи
// 1. Создает ссылки в общих папках на их текущие активные теневые копии,
// которые либо находятся в активном состоянии в каталоге снимков, либо 
// уже удалены, но сброшены в архив
// 
// 2. Проверяет размеры снимков и их заполненность, и если заполненность 
// превышает определенную величину (указанную в настройках), увеличивает
// размер снимка на определенную величину (указанную в настройках), проверяя
// при этом наличие свободного пространства для увеличения. Если его недостаточно,
// удаляет самый старый снимок. Также при выполнении этой процедуры удаляются
// все неактивные снимки.
// 
// (C) 2012 ООО ЛВА
// Данный скрипт является частью модуля "Контроллер" платформы "ЛВА Конструктор
// Web-приложений" и работает только в связке с этой платформой и ее модулями.
// 
// Платформа "ЛВА Конструктор Web-приложений" и все зависимые от нее модули
// охраняются законом об авторских правах и в соответствии с лицензионным
// соглашением могут использоваться только на оборудовании, на котором были
// изначально предустановлены специалистами ООО "ЛВА".
#defined('__DIR__') or define('__DIR__', dirname(__FILE__));//PHP<5.3.0
#chdir(__DIR__);
chdir("/opt/WAB2");
$_SERVER['DOCUMENT_ROOT'] = dirname(dirname(__FILE__));
$_SERVER['PHP_AUTH_USER'] = "admin";

require_once 'boot.php';
require_once 'utils/functions.php';

$obj = $Objects->get("ShadowCopyManager_ControllerApplication_Network_manager");
$obj2 = $Objects->get("LVMSnapshotsDataTable_ControllerApplication_Network_table");
$obj3 = $Objects->get("FileServer_ControllerApplication_Network_Shares");
$app = $Objects->get("ControllerApplication_Network");
$shell = $Objects->get("Shell_shell");

// Часть 1
$obj3->loadShares(true);
if ($obj->enableShadowCopy) {
    $snapshots = array();
    $dir = $app->remotePath.$obj->snapshotsFolder;
    if ($dh = opendir($dir)) {    
        while (($file = readdir($dh)) !== false) {
            if ($file!="." and $file!="..") {
                if (is_dir($dir."/".$file) and stripos($file,"@")!==FALSE)
                    $snapshots[$file] = $file;
            }
        }
    }
    $trashed_snapshots = array();
    if ($obj->expiredSnapshotsBackupFolder!="") {
        $dir = $app->remotePath.$obj->expiredSnapshotsBackupFolder;
        if ($dh = opendir($dir)) {    
            while (($file = readdir($dh)) !== false) {
                if ($file!="." and $file!="..") {
                    if (is_dir($dir."/".$file) and stripos($file,"@")!==FALSE)
                        $trashed_snapshots[$file] = $file;
                }
            }
        }
        closedir($dh);
    }
    
    foreach ($obj3->shares as $share) {
        if ($share->name=="root")
            continue;        
        chdir(str_replace("//","/",$obj3->shares_root."/".$share->path."/"));
        $shell->exec_command("rm @GMT*");
        foreach ($snapshots as $snapshot) {
            if ($app->remoteSSHCommand!="") {
                if (file_exists($app->remotePath.str_replace("//","/",$obj->snapshotsFolder."/".$snapshot."/".str_replace("/data","",$obj3->shares_root)."/".$share->path."")))
                    shell_exec($app->remoteSSHCommand." \"ln -s ".str_replace("//","/",$obj->snapshotsFolder."/".$snapshot."/".str_replace("/data","",$obj3->shares_root)."/".$share->path."")." ".$snapshot."\"");
            }
            else {
                if (file_exists(str_replace("//","/",$obj->snapshotsFolder."/".$snapshot."/".str_replace("/data","",$obj3->shares_root)."/".$share->path."")))
                    $shell->exec_command("ln -s ``".str_replace("//","/",$obj->snapshotsFolder."/".$snapshot."/".str_replace("/data","",$obj3->shares_root)."/".$share->path."")."`` ``".$snapshot."``");        
            }
        }
        foreach ($trashed_snapshots as $snapshot) {           
            if ($app->remoteSSHCommand!="") {
                if (file_exists($app->remotePath.str_replace("//","/",$obj->expiredSnapshotsBackupFolder."/".$snapshot."/".str_replace("/data","",$obj3->shares_root)."/".$share->path."")))
                    shell_exec($app->remoteSSHCommand." \"ln -s ".str_replace("//","/",$obj->expiredSnapshotsBackupFolder."/".$snapshot."/".str_replace("/data","",$obj3->shares_root)."/".$share->path."")." ".$snapshot."\"");
            }
            else {
                if (file_exists($app->remotePath.str_replace("//","/",$obj->expiredSnapshotsBackupFolder."/".$snapshot."/".str_replace("/data","",$obj3->shares_root)."/".$share->path."")))
                    $shell->exec_command("ln -s ``".str_replace("//","/",$obj->expiredSnapshotsBackupFolder."/".$snapshot."/".str_replace("/data","",$obj3->shares_root)."/".$share->path."")."`` ``".$snapshot."``");        
            }
        }
    }
    
    // Часть 2
    $obj2->getVgInfo();
    $obj2->getSnapshotsData();
    foreach($obj2->snapshots as $snapshot) {
        if (isset($snapshot)) {
            if ($snapshot["active"]!="active") {
                $obj2->removeSnapshot($snapshots["time"]);
                continue;
            }
            if ($snapshot["usedPercents"]>$obj->shadowCopyResizeLimit) {
                while($obj2->freeSize-$obj->shadowCopyResizeSize<0) {
                    if (trim($obj->expiredSnapshotsBackupFolder)!="")
                        $obj2->copySnapshotFiles($obj2->snapshots[0]["time"],trim($obj->expiredSnapshotsBackupFolder));                    
                    $obj2->removeSnapshot($obj2->snapshots[0]["time"]);
                    $obj2->getVgInfo();                
                    $obj2->getSnapshotsData();
                }
                $obj2->resizeSnapshot($snapshot["time"],$snapshot["size"]+$obj->shadowCopyResizeSize);
            }
        }
    }
}
?>