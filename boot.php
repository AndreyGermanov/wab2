<?php
session_start();
function __autoload($class_name) {
    load_class($class_name);
}
function load_class($class_name,$dir="") {
    $class_name = str_replace("/","",str_replace("?","",$class_name));
    if ($dir=="")
        $dir = "classes/";
    if (file_exists($dir."/".$class_name . '.php')) {
        require_once $dir."/".$class_name . '.php';
        return 0;
    }
    if (is_dir($dir)) {
        if ($dh = opendir($dir)) {
            while (($file = readdir($dh)) !== false) {
                if ($file!="." and $file!="..") {
                    if (is_dir($dir."/".$file)) {
                        load_class($class_name,$dir."/".$file);
                    }
                }
            }
            closedir($dh);
        }
    }
}
require_once("metadata/md.php");
require_once("/etc/WAB2/config/default.php");

$Objects = new Objects();
require_once('utils/functions.php');
#require_once 'utils/updates.php';

spl_autoload_register('__autoload');
$dataAdapterClass = "PDODataAdapter";
$defaultCacheDataAdapter = $Objects->get($dataAdapterClass."_Default");
?>