<?php 
        //require_once "scripts.php";
        //$reg = $Objects->get("RegistryBloodDefinitions_DocFlowApplication_Docs_one");
		//$bcl = PDODataAdapter::makeQuery("SELECT entities FROM fields WHERE @code='BCL-ABR'",$reg->adapter,$reg->module_id);
		//reset($bcl);
		//$bcl = current($bcl);
		//$records = $reg->getRecords(0,'1253059200000','regDate,analyzeDefValue',"@analyzeType='BloodAnalyzeTypesReference_100'",'regDate DESC');
		//echo print_r($records);
//		$fs = $Objects->get("FTPServer_ControllerApplication_Network_shares");
//                $folders = $fs->getFolders();
//                $fs->setFolders($folders);
//                $fs->restart();

//function change_time($dir,$time="") {
//    if (is_dir($dir)) {
//        if ($dh = opendir($dir)) {
//            while (($file = readdir($dh)) !== false) {
//                if ($file!="." and $file!="..") {
//                    if (is_dir($dir."/".$file)) {
//                        if (strpos($file, "@GMT-")!==FALSE) {
//                            $tm = str_replace("@GMT-","",$file);
//                            $arr = explode("-",$tm);
//                            $date = explode(".",$arr[0]);
//                            $time = explode(".",$arr[1]);
//                            $year = $date[0];
//                            $month = $date[1];
//                            $day = $date[2];
//                            $hour = $time[0];
//                            $minute = $time[1];
//                            $second = $time[2];
//                            $time = mktime($hour,$minute,$second,$month,$day,$year);
//                        }
//                        change_time($dir."/".$file,$time);
//                    }
//                    touch($dir."/".$file,$time);
//                }
//            }
//            closedir($dh);
//        }
//    }
//}
//change_time("/data/share/trash/snapshots")        
        //$ftpHost = $Objects->get("FTPHost_ControllerApplication_Network_Default");
		//$ftpHost->load();
		//echo print_r($ftpHost->fields);
	//echo print_r($panels);
		//echo getMetadataString(getMetadataInFile($modules["MystixController"]["file"]));
		//echo print_r(getTopItems($models,$modelGroups));
//		$user = $Objects->get("ApacheUser_ControllerApplication_Network_".$_SERVER["PHP_AUTH_USER"]);
  //      $user->load();
    //    echo getDiffArray("userconfig",$user->config,$defaultconfig);
        //echo $addressbooks["Controller"]["file"];        
		require_once 'boot.php';
        require_once 'utils/updates.php';
        require_once 'utils/functions.php';
        require_once 'utils/mail/mreadd.php';
        require_once 'utils/mail/mrim.php';
        //$res = PDODataAdapter::makeQuery("SELECT entities FROM fields WHERE @classname='DocumentBloodAnalyze'",$Objects->get("DocFlowDataAdapter_DocFlowApplication_Docs_1"),"DocFlowApplication_Docs");
        //foreach($res as $key=>$value) {
        	//$value->save();
        //}
        $obj = $Objects->get("InputWebEntity_WebServerApplication_Web_1_20");
        $obj->load();
?>
	<html>
	<head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">	
		<?php include "scripts.php";?>	
	</head>
<?php
        $obj->show();
?>
</html>
    <?    
//		error_reporting(0);
	
	
//    	$host = "{mail.it-port.ru:143/imap/tls/novalidate-cert}";
//     	$login = "andrey@it-port.ru";
//     	$password = "fckmce";
//      	$imap = imap_open("{mail.it-port.ru:143/imap/tls/novalidate-cert}", "andrey@it-port.ru", "fckmce");        
//     	$msg=new mread($host, $login, $password,"INBOX",imap_num_msg($imap),10);
//     	$mess = $msg->getmail();    	
//     	foreach ($mess as $message) {
//     		echo $message["from"]."-".$message["to"]."-".$message["subject"]."<hr/>";
//     		if ($message["html"]!="")
//     			echo $message["html"]."<hr/>";
//     		else
//     			echo $message["plain"]."<hr/>";
//     		echo print_r(array_keys($message["attach"]))."<br/>";
//     	}
//     $imap = imap_open("{mail.it-port.ru:143/imap/tls/novalidate-cert}", "andrey@it-port.ru", "fckmce");        
//     $folders = imap_list($imap, "{mail.it-port.ru:143/imap/tls/novalidate-cert}", "*");
//     echo "<ul>";
//     foreach ($folders as $folder) {
//     	$folder = str_replace("{mail.it-port.ru:143/imap/tls/novalidate-cert}", "", mb_convert_encoding($folder, "UTF8", "UTF7-IMAP"));
//     	echo '<li><a href="mail.php?folder=' . $folder . '&func=view">' . $folder . '</a></li>';
//     }
//     echo "</ul>";
//     $imap = imap_open("{mail.it-port.ru:143/imap/tls/novalidate-cert}INBOX", "andrey@it-port.ru", "fckmce");        
//     $numMessages = imap_num_msg($imap);
//     echo $numMessages;
//     for ($i = $numMessages; $i > ($numMessages - 20); $i--) {
//     	$header = imap_header($imap, $i);
    
//     	$fromInfo = $header->from[0];
//     	$replyInfo = $header->reply_to[0];
    
//     	$details = array(
//     			"fromAddr" => (isset($fromInfo->mailbox) && isset($fromInfo->host))
//     			? $fromInfo->mailbox . "@" . $fromInfo->host : "",
//     			"fromName" => (isset($fromInfo->personal))
//     			? $fromInfo->personal : "",
//     			"replyAddr" => (isset($replyInfo->mailbox) && isset($replyInfo->host))
//     			? $replyInfo->mailbox . "@" . $replyInfo->host : "",
//     			"replyName" => (isset($replyTo->personal))
//     			? $replyto->personal : "",
//     			"subject" => (isset($header->subject))
//     			? str_replace("?=","",preg_replace("~\=\?(.*)\?B\?~U","",$header->subject)) : "",
//     			"udate" => (isset($header->udate))
//     			? $header->udate : ""
//     	);
//     	$subj = $header->subject;
//     	$matches = array();
//     	if (preg_match("~\=\?(.*)\?(.*)\?~U",$subj,$matches)) {
    		
//     	}
    
//     	$uid = imap_uid($imap, $i);
    
//     	echo "<ul>";
//     	echo "<li><strong>From:</strong>"; 
//     	echo " " . $details["fromAddr"] . "</li>";
//     	echo "<li><strong>Subject:</strong> " . $details["subject"] . "</li>";
//     	echo '<li><a href="mail.php?folder=' . $folder . '&uid=' . $uid . '&func=read">Read</a>';
//     	echo " | ";
//     	echo '<a href="mail.php?folder=' . $folder . '&uid=' . $uid . '&func=delete">Delete</a></li>';
//     	echo "</ul>";
//     }        

//        $obj->additionalLinks["9"] = $Objects->get("ReferenceBankAccounts_DocFlowApplication_Docs_9");
//        $obj->save();
        //$links = $obj->removeLinks("ReferenceBankAccounts_11");
        //echo print_r(array_keys($links));
        //$obj->setLinks(array("ReferenceBanks_1","ReferenceAccounts_11","ReferenceBanks_4"));
        
	?>	
    </body>
</html>