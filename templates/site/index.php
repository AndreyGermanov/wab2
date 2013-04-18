<?
$siteId = array_pop(explode("/",$_SERVER["DOCUMENT_ROOT"]));
$module_id = $_SERVER["MODULE_ID"];
$object_id = "";$title="";$keywords="";$description="";$meta="";$header="";
if (isset($_POST["object_id"])) {
    require_once 'boot.php';
    $Objects = new Objects();
    $Objects->start();
}
else {
    if (isset($_GET["i"])) {
        if (file_exists("/var/WAB2/cache/".$siteId."/".$_SERVER["QUERY_STRING"])) {
                echo file_get_contents("/var/WAB2/cache/".$siteId."/".$_SERVER["QUERY_STRING"]);
                exit;			
        } else {	
            require_once 'boot.php';
            $Objects = new Objects();
            $adapter = $Objects->get("SiteDataAdapter_".$module_id."_".$siteId."_".$_GET["i"]);
            if (is_numeric($_GET["i"])) {
        	$items = PDODataAdapter::makeQuery("SELECT entities FROM fields WHERE @name=".$_GET["i"],$adapter,"1");
            }
            else {
        	$items = PDODataAdapter::makeQuery("SELECT entities FROM fields WHERE @sysname='".$_GET["i"]."'",$adapter,"1");
            }

            if (count($items)>0) {
                 $item = array_shift($items);
                 if (!$item->loaded)
                        $item->load();
                 if ($item->static)
                         ob_start();
            }        
        }
    }
    else {
        if (file_exists("/var/WAB2/cache/".$siteId."/mainpage")) {
                echo file_get_contents("/var/WAB2/cache/".$siteId."/mainpage");
                exit;		
        } else {
                require_once 'boot.php';
                $Objects = new Objects();
                $site = $Objects->get("WebSite_".$module_id."_".$siteId);
                $adapter = $Objects->get("SiteDataAdapter_".$module_id."_".$siteId."_".$siteId);
                if (!$site->loaded)
                        $site->load();	    
                if ($site->mainpage!="") {            	
#                        $item = $Objects->get($site->mainpage."_".$siteId);
			$item = $Objects->get(array_shift(explode("_",$site->mainpage))."_".$siteId."_".array_pop(explode("_",$site->mainpage)));
                        if (!$item->loaded)
                        $item->load();
                        if ($item->static)
                                ob_start();
                }
        }    	
    }
    if (isset($item)) {
        $title = $item->htmlTitle;
        $keywords = $item->htmlKeywords;
        $description = $item->htmlDescription;
        $meta = $item->htmlMeta."\n";
        $header = $item->htmlHeader."\n";
        $encoding = $item->htmlEncoding;
    }
?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=<?=@$encoding?>">
        <meta name="Keywords" content="<?=@$keywords?>">
        <meta name="Description" content="<?=@$description?>">
        <?=$meta?>
        <title><?=$title?></title>
        <?=$header?>
   </head>
   <body>
        <script language="javascript" type="text/javascript" src="scripts/template_functions.js"></script>
        <script language="javascript" type="text/javascript" src="tools/prototype/prototype.js"></script>               
<? 
    if (isset($item) and !$item->static) { 		
        $item->show();
?>
   </body>
</html>
<?    
	} else {
        @$item->show();
?>
   </body>
</html>
<?
            $contents = ob_get_contents();
            ob_end_clean();
            echo $contents;
            if (isset($_GET["i"]))		
                $id = $_SERVER["QUERY_STRING"];
            else
                $id = "mainpage";
            shell_exec("mkdir -p /var/WAB2/cache/".$siteId);
            file_put_contents("/var/WAB2/cache/".$siteId."/".$id,$contents);
	}
?>
<?
}
?>