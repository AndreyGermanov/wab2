#!/usr/bin/php -c /etc/php5/cli/php.ini
<?
#defined('__DIR__') or define('__DIR__', dirname(__FILE__));//PHP<5.3.0
#chdir(__DIR__);
chdir("/opt/WAB2");
$_SERVER['DOCUMENT_ROOT'] = dirname(dirname(__FILE__));
$_SERVER['PHP_AUTH_USER'] = "admin";

require_once 'boot.php';
require_once 'utils/functions.php';
$obj = $Objects->get("FullAuditReport_ControllerApplication_Network_report");
$obj->getData();
?>