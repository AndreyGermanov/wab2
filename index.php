<?
require_once 'boot.php';
if (isset($_POST["object_id"])) {    
    $Objects->start($_POST["object_id"],null,@$_POST["hook"],@$_POST["arguments"]);
}
else {
    if (isset($_GET["object_id"])) {
        $object_id = $_GET["object_id"];
        $hook = @$_GET["hook"];
        $arguments = @$_GET["arguments"];
    }
    else {
        $object_id = "WindowManager";
        if (isset($_GET["path"])) {        	
        	$path = getFmPath($_GET["path"]);
        	if (file_exists($path)) {
        		$arguments = array("path" => $path);
        		$adapter = $Objects->get("DocFlowDataAdapter_DocFlowApplication_Docs_1");
        		$result = PDODataAdapter::makeQuery("SELECT entities FROM fields WHERE @path='".$path."' AND @classname='ReferenceFiles'", $adapter,"DocFlowApplication_Docs");
        		if (count($result)>0) {
        			$result = current($result);
        			$arguments["fileId"] = $result->getId();
        		}
        		$arguments = json_encode($arguments);
        		$hook = "show";
        	} 
        }
    }
?>
<!DOCTYPE wab2 SYSTEM "wab2.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>ЛВА Конструктор Web-приложений 2 (версия <?=trim(file_get_contents('/etc/WAB2/config/version'))?>)</title>
    </head>
    <body style="margin-top:0px;margin-left:0px;margin-right:0px;margin-bottom:0px">
    <?
        require_once "scripts.php";
        $Objects->start($object_id,null,@$hook,@$arguments);
    ?>
    </body>
</html>
<?
}
?>