#!/usr/bin/php -c /etc/php5/cli/php.ini
<?

// Скрипт выполняет ротацию снимков, с учетом свободного пространства,
// выделенного для них.
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
$app = $Objects->get("ControllerApplication_Network");
$shell = $Objects->get("Shell_shell");
$obj2 = $Objects->get("LVMSnapshotsDataTable_ControllerApplication_Network_table");
    
$obj2->getVgInfo();
$obj2->getSnapshotsData();
while($obj2->freeSize-$obj->snapshotSize<0) {
    if (trim($obj->expiredSnapshotsBackupFolder)!="")
        $obj2->copySnapshotFiles($obj2->snapshots[0]["time"],trim($obj->expiredSnapshotsBackupFolder));
    $obj2->removeSnapshot($obj2->snapshots[0]["time"]);
    $obj2->getVgInfo();                
    $obj2->getSnapshotsData();
}
$obj2->createSnapshot($obj->snapshotSize);
$obj2->getVgInfo();
$obj2->getSnapshotsData();
if (count($obj2->snapshots)>$obj->snapshotsCount) {   
    if (trim($obj->expiredSnapshotsBackupFolder)!="")
        $obj2->copySnapshotFiles($obj2->snapshots[0]["time"],trim($obj->expiredSnapshotsBackupFolder));
    $obj2->removeSnapshot($obj2->snapshots[0]["time"]);        
}

?>