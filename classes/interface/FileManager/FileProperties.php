<?
class FileProperties extends WABEntity {

    public $titles = array();
    public $paths = array();
    public $dims = array("б","Кб","Мб","Гб","Тб");
    function construct($params) {
        parent::construct($params);
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
        	$app->initModules();
        $this->capp = $Objects->get($this->module_id);
        $this->Shell = $Objects->get("Shell_".$this->module_id."_shell");
        $this->fileServer = $Objects->get("FileServer_".$this->module_id."_Shares");        
        $this->template = "templates/interface/FileManager/FileProperties.html";
        $this->handler = "scripts/handlers/interface/FileManager/FileProperties.js";
        $this->icon = $this->skinPath."images/Tree/list.png";
        $this->app = $app;
        $this->skinPath = $app->skinPath;
        $this->width = "600";
        $this->height = "400";
        $this->overrided = "width,height";

        $this->tabs_string = "main|Сведения|".$this->skinPath."images/spacer.gif;";
        $this->tabs_string.= "rights|Права|".$this->skinPath."images/spacer.gif";
        $this->tabset_id = "WebItemTabset_".$this->module_id."_".$this->name."Fp";
        $this->active_tab = "main";
        $this->rules_tabs_string = "users|Пользователи|".$this->skinPath."images/spacer.gif;";
        $this->rules_tabs_string.= "groups|Группы|".$this->skinPath."images/spacer.gif";
        $this->rules_tabset_id = "WebItemTabset_".$this->module_id."_".$this->name."Rules";
        $this->rules_active_tab = "users";
        $this->shares_tabs_string = "localShares|Локально|".$this->skinPath."images/spacer.gif;";
        $this->shares_tabs_string.= "internetShares|Через Интернет|".$this->skinPath."images/spacer.gif;";
        $this->shares_tabs_string.= "trash|Сетевая корзина|".$this->skinPath."images/spacer.gif;";
        $this->shares_tabs_string.= "audit|Журнал событий|".$this->skinPath."images/spacer.gif";
        $this->shares_tabset_id = "WebItemTabset_".$this->module_id."_".$this->name."Shares";
        $this->shares_active_tab = "localShares";

        $this->referenceFileCode = "";
        $this->path = "";
        $this->title = "";
        $this->clientClass = "FileProperties";
        $this->parentClientClasses = "Entity";       
        $this->classTitle = "Окно свойств файла";
        $this->classListTitle = "Окно свойств файла";
    }

    function getArgs() {
    	$result = parent::getArgs();
        $result["{|userRightsTable}"] = $this->userRightsTable;
        $result["{|groupRightsTable}"] = $this->groupRightsTable;
        $result["{|referenceFileCode}"] = $this->referenceFileCode;        
        return $result;
    }

    function getHookProc($number) {
        switch ($number) {
                case '2': return "initWindow";
                case '3': return "save";
        }
        return parent::getHookProc($number);
    }

    function getDirSize($dir) {
        $size = 0;
        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                    if ($file!="." and $file!="..") {
                        if (is_dir($dir."/".$file) and !is_link($dir."/".$file)) {
                            $size += @filesize($dir."/".$file);
                            $size += $this->getDirSize($dir."/".$file);
                        } else if (is_file($dir."/".$file) and !is_link($dir."/".$file))
                            $size += @filesize($dir."/".$file);
                    }
                }
                closedir($dh);
            }
        }
        return $size;
    }

    function getFilesCount($dir) {
        $count = 0;
        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                if ($file!="." and $file!="..") {
                    if (is_dir($dir."/".$file) and !is_link($dir."/".$file)) {
                        $count++;
                        $count += $this->getFilesCount($dir."/".$file);
                    } else if (is_file($dir."/".$file))
                        $count++;
                    }
                }
                closedir($dh);
            }
        }
        return $count;
    }
    
    function getAcl($user,$type,$text,$is_default=false) {
        if ($is_default)
            $search_string = "default:";
        else
            $search_string = "";
        $search_string .= $type;
        $search_string .= ":".$user.":";        
        
        foreach ($text as $line) {
            if (strpos($line,$search_string)!==FALSE) {
                $res = array_pop(explode(":",$line));
                $result = array();
                if ($res[0]=="r")
                    $result["r"] = "r";
                if ($res[1]=="w")
                    $result["w"] = "w";
                if ($res[2]=="x")
                    $result["x"] = "x";
                return $result;
            }
        }
        return 0;
    }
    
    function drawRightsTable($object_type,$draw_default,$path="") {
        if (!$this->fileServer->usersLoaded)
            $this->fileServer->loadUsers(false);
        $users = $this->fileServer->users;
        if (!$this->fileServer->groupsLoaded) {
            $this->fileServer->loadGroups();
        }
        $groups = $this->fileServer->groups;
        $users_array = array();
        foreach ($users as $user)
            $users_array[] = $user->name;
        $groups_array = array();
        foreach ($groups as $group)
            $groups_array[] = $group->name;
        $users_list = " ~".implode("~",$users_array)."| ~".implode("~",$users_array);
        $owner_name = "";
        $groups_list = " ~".implode("~",$groups_array)."| ~".implode("~",$groups_array);
        if ($object_type=="users") {
            $object_typ = "user";
            $object_title = "Пользователь";
            $all_read_id = "allUsers_ReadCheck";
            $all_write_id = "allUsers_WriteCheck";
            $all_execute_id = "allUsers_ExecuteCheck";
            $objects = $this->fileServer->users;
            $img_src = $this->app->skinPath."images/Tree/user.png";
            $owner_title = "userOwner";
            $other_title = "userOther";
            $objects_list = $users_list;
            if ($path!="") {
                $owner = posix_getpwuid(fileowner($this->capp->remotePath.$this->path));     
                $owner_name = $owner["name"];
            }            
        }
        else {
            $object_typ = "group";
            $object_title = "Группа";
            $all_read_id = "allGroups_ReadCheck";
            $all_write_id = "allGroups_WriteCheck";
            $all_execute_id = "allGroups_ExecuteCheck";
            $objects = $this->fileServer->groups;
            $img_src = $this->app->skinPath."images/Tree/group.png";
            $owner_title = "groupOwner";
            $other_title = "groupOther";
            $objects_list = $groups_list;
            if ($path!="") {
                $owner = posix_getgrgid(filegroup($this->capp->remotePath.$this->path));            
                $owner_name = $owner["name"];
            }
        }      
        if ($path!="") {
            if ($this->capp->remoteSSHCommand!="") {
                $aclsText = explode("\n",shell_exec($this->capp->remoteSSHCommand." getfacl '".$path."'"));
            } else {
                $aclsText = explode("\n",$this->Shell->exec_command(" getfacl ``".$path."``"));
            }
        }
        
        $blocks = getPrintBlocks(file_get_contents("templates/interface/FileManager/FileACLTable.html"));
        $result = "";
        $result = strtr($blocks["header"], array("{ACL_object_name}" => $object_title,"{skinPath}" => $this->app->skinPath, "{object_type}" => $object_type, "{objects_list}" => $objects_list, "{owner_name}" => $owner_name));
        if ($draw_default) {
            $result .= $blocks["defaultrights"];
        }
        $all_read_checked = "";
        $all_write_checked = "";
        $all_execute_checked = "";
        $result .= $blocks["endrow"];
        $result .= $blocks["startrow"];
        $result .= $blocks["empty_selected_column"];
        //$result .= $blocks["empty_column"];
        $result .= strtr($blocks["rwxheaderrow"],array("{object_id}" => $this->getId(), "{all_read_checked}" => $all_read_checked, "{all_write_checked}" => $all_write_checked, "{all_execute_checked}" => $all_execute_checked, "{all_read_id}" => $all_read_id, "{all_write_id}" => $all_write_id, "{all_execute_id}" => $all_execute_id));
        if ($draw_default) {
            if ($object_type=="users") {
                $object_title = "Пользователь";
                $all_read_id = "allUsers_DefaultReadCheck";
                $all_write_id = "allUsers_DefaultWriteCheck";
                $all_execute_id = "allUsers_DefaultExecuteCheck";
            }
            else {
                $object_title = "Группа";
                $all_read_id = "allGroups_DefaultReadCheck";
                $all_write_id = "allGroups_DefaultWriteCheck";
                $all_execute_id = "allGroups_DefaultExecuteCheck";
            }            
            $result .= strtr($blocks["rwxheaderrow"],array("{object_id}" => $this->getId(), "{all_read_checked}" => $all_read_checked, "{all_write_checked}" => $all_write_checked, "{all_execute_checked}" => $all_execute_checked, "{all_read_id}" => $all_read_id, "{all_write_id}" => $all_write_id, "{all_execute_id}" => $all_execute_id));
        }
        $result .= $blocks["endrow"];
        $result .= $blocks["startrow"];
        $result .= strtr($blocks["ACLImageColumn"],array("{img_src}" => $img_src));
        $result .= strtr($blocks["ACLObjectColumn"],array("{title}" => "Владелец"));
        if ($path!="") {
            $results = $this->getACL("",$object_typ,$aclsText);
            if (isset($results["r"]))
                $readCheck = "true";
            else
                $readCheck = "false";
            if (isset($results["w"]))
                $writeCheck = "true";
            else
                $writeCheck = "false";
            if (isset($results["x"]))
                $executeCheck = "true";
            else
                $executeCheck = "false";            
        } else {
            $readCheck = "false";
            $writeCheck = "false";
            $executeCheck = "false";
        }
        $result .= strtr($blocks["ACLRightColumn"],array("{id}" => $object_typ."_".$owner_title."_ReadCheck","{checked}" => $readCheck));
        $result .= strtr($blocks["ACLRightColumn"],array("{id}" => $object_typ."_".$owner_title."_WriteCheck","{checked}" => $writeCheck));
        $result .= strtr($blocks["ACLRightColumn"],array("{id}" => $object_typ."_".$owner_title."_ExecuteCheck","{checked}" => $executeCheck));
        if ($draw_default) {
            if ($path!="") {
                $results = $this->getACL("",$object_typ,$aclsText,true);
                if (isset($results["r"]))
                    $readCheck = "true";
                else
                    $readCheck = "false";
                if (isset($results["w"]))
                    $writeCheck = "true";
                else
                    $writeCheck = "false";
                if (isset($results["x"]))
                    $executeCheck = "true";
                else
                    $executeCheck = "false";            
            } else {
                $readCheck = "false";
                $writeCheck = "false";
                $executeCheck = "false";
            }
            $result .= strtr($blocks["ACLRightColumn"],array("{id}" => $object_typ."_".$owner_title."_DefaultReadCheck","{checked}" => $readCheck));
            $result .= strtr($blocks["ACLRightColumn"],array("{id}" => $object_typ."_".$owner_title."_DefaultWriteCheck","{checked}" => $writeCheck));
            $result .= strtr($blocks["ACLRightColumn"],array("{id}" => $object_typ."_".$owner_title."_DefaultExecuteCheck","{checked}" => $executeCheck));  
        }
        $result .= $blocks["endrow"];
        if ($other_title=="userOther") {
            $result .= $blocks["startrow"];
            if ($path!="") {
                $results = $this->getACL("","other",$aclsText);
                if (isset($results["r"]))
                    $readCheck = "true";
                else
                    $readCheck = "false";
                if (isset($results["w"]))
                    $writeCheck = "true";
                else
                    $writeCheck = "false";
                if (isset($results["x"]))
                    $executeCheck = "true";
                else
                    $executeCheck = "false";            
            } else {
                $readCheck = "false";
                $writeCheck = "false";
                $executeCheck = "false";
            }
            $result .= strtr($blocks["ACLImageColumn"],array("{img_src}" => $img_src));
            $result .= strtr($blocks["ACLObjectColumn"],array("{title}" => "Остальные"));
            $result .= strtr($blocks["ACLRightColumn"],array("{id}" => $object_typ."_".$other_title."_ReadCheck","{checked}" => $readCheck));
            $result .= strtr($blocks["ACLRightColumn"],array("{id}" => $object_typ."_".$other_title."_WriteCheck","{checked}" => $writeCheck));
            $result .= strtr($blocks["ACLRightColumn"],array("{id}" => $object_typ."_".$other_title."_ExecuteCheck","{checked}" => $executeCheck));
            if ($draw_default) {
                if ($path!="") {
                    $results = $this->getACL("","other",$aclsText,true);
                    if (isset($results["r"]))
                        $readCheck = "true";
                    else
                        $readCheck = "false";
                    if (isset($results["w"]))
                        $writeCheck = "true";
                    else
                        $writeCheck = "false";
                    if (isset($results["x"]))
                        $executeCheck = "true";
                    else
                        $executeCheck = "false";            
                } else {
                    $readCheck = "false";
                    $writeCheck = "false";
                    $executeCheck = "false";
                }
                $result .= strtr($blocks["ACLRightColumn"],array("{id}" => $object_typ."_".$other_title."_DefaultReadCheck","{checked}" => $readCheck));
                $result .= strtr($blocks["ACLRightColumn"],array("{id}" => $object_typ."_".$other_title."_DefaultWriteCheck","{checked}" => $writeCheck));
                $result .= strtr($blocks["ACLRightColumn"],array("{id}" => $object_typ."_".$other_title."_DefaultExecuteCheck","{checked}" => $executeCheck));                
            }
            $result .= $blocks["endrow"];
        }
        
        foreach ($objects as $value) {                       
            $result .= $blocks["startrow"];
            $result .= strtr($blocks["ACLImageColumn"],array("{img_src}" => $img_src));
            $result .= strtr($blocks["ACLObjectColumn"],array("{title}" => $value->name));
            if ($path!="") {
                $results = $this->getACL($value->name,$object_typ,$aclsText);
                if (isset($results["r"]))
                    $readCheck = "true";
                else
                    $readCheck = "false";
                if (isset($results["w"]))
                    $writeCheck = "true";
                else
                    $writeCheck = "false";
                if (isset($results["x"]))
                    $executeCheck = "true";
                else
                    $executeCheck = "false";            
            } else {
                $readCheck = "false";
                $writeCheck = "false";
                $executeCheck = "false";
            }            
            $result .= strtr($blocks["ACLRightColumn"],array("{id}" => $object_typ."_".$value->name."_ReadCheck","{checked}" => $readCheck));
            $result .= strtr($blocks["ACLRightColumn"],array("{id}" => $object_typ."_".$value->name."_WriteCheck","{checked}" => $writeCheck));
            $result .= strtr($blocks["ACLRightColumn"],array("{id}" => $object_typ."_".$value->name."_ExecuteCheck","{checked}" => $executeCheck));
            if ($draw_default) {
                if ($path!="") {
                    $results = $this->getACL($value->name,$object_typ,$aclsText,true);
                    if (isset($results["r"]))
                        $readCheck = "true";
                    else
                        $readCheck = "false";
                    if (isset($results["w"]))
                        $writeCheck = "true";
                    else
                        $writeCheck = "false";
                    if (isset($results["x"]))
                        $executeCheck = "true";
                    else
                        $executeCheck = "false";            
                } else {
                    $readCheck = "false";
                    $writeCheck = "false";
                    $executeCheck = "false";
                }            
                $result .= strtr($blocks["ACLRightColumn"],array("{id}" => $object_typ."_".$value->name."_DefaultReadCheck","{checked}" => $readCheck));
                $result .= strtr($blocks["ACLRightColumn"],array("{id}" => $object_typ."_".$value->name."_DefaultWriteCheck","{checked}" => $writeCheck));
                $result .= strtr($blocks["ACLRightColumn"],array("{id}" => $object_typ."_".$value->name."_DefaultExecuteCheck","{checked}" => $executeCheck));                
            }
            $result .= $blocks["endrow"];
        }
        $result .= $blocks["endtable"];  
        return $result;
    }

    function initWindow($arguments) {
        global $Objects;
        $this->titles = array();
        $this->paths = array();
		$this->freeSizeTitle = "";
		$this->totalSizeTitle = "";
        $arguments["files"] = (array)$arguments["files"];
        $this->filesCount = count($arguments["files"]);
        $this->hosts_access_rules_table = "ShareAccessRulesTable_".$this->module_id."_".$this->name."_hosts";
        clearstatcache();
        if (count($arguments["files"])>1) {
            foreach ($arguments["files"] as $value) {
                $value = (array)$value;
                $this->titles[] = urldecode($value["title"]);
                $this->paths[] = urldecode($arguments["path"])."/".urldecode($value["title"]);
            }
            $this->path_title = substr(implode(",",$this->paths),1,40);
            $this->path_title_full = implode(",",$this->paths);
            $this->fileatime = "";
            $this->filemtime = "";
            $this->mimetype = "";
            $this->img_title = $this->skinPath."images/FileManager/folder.png";
            $this->title = "Группа объектов";
            $all_size = 0;$all_count=0;
            foreach ($this->paths as $path) {
                if (is_dir($path)) {
                    $all_size += $this->getDirSize($this->capp->remotePath.$path);
                    $all_count += $this->getFilesCount($this->capp->remotePath.$path);
                }
                else if (is_file($this->capp->remotePath.$path) and !is_link($this->capp->remotePath.$path)) {
                    $all_size += @filesize($this->capp->remotePath.$path);
                    $all_count++;
                }
            }
            $all_size_dims = $all_size;
            $i=0;
            while ($all_size_dims>=1) {
                $all_size_dims = $all_size_dims/1024;
                $i++;
            }
            if ($i==0) $i=1;
            $all_size_dims = round($all_size_dims*1024,2);
            $this->sizeTitle = $all_size_dims." ".$this->dims[$i-1]." (".$all_count." шт.)";
            $this->userRightsTable = $this->drawRightsTable("users",true);
            $this->groupRightsTable = $this->drawRightsTable("groups",true);
            $this->dirs = implode("~",$this->paths);
        } else if (count($arguments["files"])==1) {        	
            $obj = (array)current($arguments["files"]);
            $this->path = $arguments["path"]."/".$obj["title"];
            $this->dirs = $this->path;
            $this->path_title_full = $this->path;
            $this->path_title = substr($this->path,1,40);
            $this->title = $obj["title"]; 
            $this->share = @$obj["share"];
            
            if (is_object($this->capp->docFlowApp)) {
            	$this->adapter = $Objects->get("DocFlowDataAdapter_".$this->capp->docFlowApp->getId()."_1");
            	$query = "SELECT entities FROM fields WHERE @path='".$this->path."' AND @classname='ReferenceFiles'";
            	$res = PDODataAdapter::makeQuery($query, $this->adapter,$this->capp->docFlowApp->getId());           	
            	if (count($res)>0) {
            		$file = current($res);
            		$this->referenceFileCode = "<input type='button' fileid='".$file->getId()."' id='referenceFileButton' value='Открыть описание'/>";
            	} else {
            		$this->referenceFileCode = "<input style='width:100%' type='button' fileid='ReferenceFiles_".$this->capp->docFlowApp->getId()."_' id='referenceFileButton' value='Открыть описание'/>";
            	}
            }
            
            if (is_dir($this->capp->remotePath.$this->path)) {
                $this->type="directory";
                $this->userRightsTable = $this->drawRightsTable("users",true,$this->path);
                $this->groupRightsTable = $this->drawRightsTable("groups",true,$this->path);
            }
            else {
                $this->type = "file";
                $this->userRightsTable = $this->drawRightsTable("users",false,$this->path);
                $this->groupRightsTable = $this->drawRightsTable("groups",false,$this->path);
            }
            $this->filemtime = date("d.m.Y h:i:s",filemtime($this->capp->remotePath.$this->path));
            $this->fileatime = date("d.m.Y h:i:s",fileatime($this->capp->remotePath.$this->path));
            $this->mimetype = mime_content_type($this->capp->remotePath.$this->path);
            if ($this->type!="directory") {
                if (file_exists($this->skinPath."images/FileManager/mimetypes/".str_replace("/","-",@mime_content_type($this->capp->remotePath.$this->path).".png")))
                        $this->img_title = $this->skinPath."images/FileManager/mimetypes/".str_replace("/","-",@mime_content_type($this->capp->remotePath.$this->path).".png");
                else
                     $this->img_title = $this->skinPath."images/FileManager/mimetypes/text-plain.png";
            } else {
                $this->img_title = $this->skinPath."images/FileManager/folder.png";            
            }
            
            $this->sharetypes = @$obj["sharetypes"]; 
            $all_count = 0;
            if (is_file($this->capp->remotePath.$this->path) and !is_link($this->capp->remotePath.$this->path)) {
                    $all_size = filesize($this->capp->remotePath.$this->path);
                    $all_count = 1;
            }
            else if (is_dir($this->capp->remotePath.$this->path)) {
                    $all_size = $this->getDirSize($this->capp->remotePath.$this->path);
                    $all_count = $this->getFilesCount($this->capp->remotePath.$this->path);
            }
            $all_size_dims = $all_size;
            $i=0;
            while ($all_size_dims>=1) {
                    $all_size_dims = $all_size_dims/1024;
                    $i++;
            }
            if ($i==0) $i=1;
            $all_size_dims = round($all_size_dims*1024,2);
            $this->sizeTitle = $all_size_dims." ".$this->dims[$i-1]." (".$all_count." шт.)";;
	        $totalSize = disk_total_space($this->capp->remotePath.$this->path);
    	    $freeSize = disk_free_space($this->capp->remotePath.$this->path);
            $all_size_dims = $totalSize;
            $i=0;
            while ($all_size_dims>=1) {
                    $all_size_dims = $all_size_dims/1024;
                    $i++;
            }
            if ($i==0) $i=1;
            $all_size_dims = round($all_size_dims*1024,2);
            $this->totalSizeTitle = $all_size_dims." ".$this->dims[$i-1];
            $all_size_dims = $freeSize;
            $i=0;
            while ($all_size_dims>=1) {
                    $all_size_dims = $all_size_dims/1024;
                    $i++;
            }
            if ($i==0) $i=1;
            $all_size_dims = round($all_size_dims*1024,2);
            $this->freeSizeTitle = $all_size_dims." ".$this->dims[$i-1];

            if ($this->type=="directory") {
                if ($this->share!="") {
                    $this->shareCheckBox = "1";
                    $this->shareDisplay = "";
                } else {
                    $this->shareCheckBox = "0";
                    $this->shareDisplay = "none";                
                }
                $this->tabs_string.= ";shares|Общий доступ|".$this->skinPath."images/spacer.gif";
                $fshare = $this->fileServer->containsPath(str_replace($this->fileServer->shares_root."/","",$this->path));
                if (!is_object($fshare)) {
                    $fshare = $Objects->get("FileShare_".$this->module_id."_");
                }
                if (!$fshare->loaded)
                    $fshare->load();
                $this->recycleBin = $fshare->recycleBin;
                $this->recyclePath = $fshare->recyclePath;
                $this->recyclePeriod = $fshare->recyclePeriod;
                $this->fullAudit = $fshare->fullAudit;
                $this->ftpFolder = $fshare->ftpFolder;
                $this->hosts_access_rules_table = "ShareAccessRulesTable_".$this->module_id."_".$this->name."_hosts";
                $this->hosts_rows = str_replace("'","xox",str_replace("\n","yoy",str_replace('"',"zoz",str_replace(",","oao",$fshare->getRows($this->hosts_access_rules_table)))));
            }
            $this->tabs_string.= ";backup|Резервные копии|".$this->skinPath."images/spacer.gif";

			$shadowCopyManager = $Objects->get("ShadowCopyManager_".$this->module_id."_shadow");
			$this->backupFrameSrc = $shadowCopyManager->backupViewerAddress."/?path=".urlencode($this->path);            
        }
    }

    function getPresentation() {
        return "Свойства ".$this->title;
    }
    
    function save($arguments) {
        global $Objects;
        $this->setArguments($arguments);
        $paths = explode("~",$arguments["paths"]);
        $rights = $arguments["rights"]."\nuser:www-data:rwx\ndefault:user:www-data:rwx\nuser:root:rwx\ndefault:user:root:rwx";
        if ($arguments["reverseRules"]=="1")
            $r = "-R";
        else
            $r = "";
		$ftpServer = $Objects->get("FTPServer_".$this->module_id."_ftp");
        $oldFolders = $ftpServer->getFolders();
        foreach ($paths as $path) {
            $owner = "";
            file_put_contents($this->capp->remotePath."/tmp/".$this->getId(),$rights);
            if (isset($arguments["userOwner"]) and $arguments["userOwner"]!="")
                $owner = $arguments["userOwner"];
            if (isset($arguments["groupOwner"]) and $arguments["groupOwner"]!="")
                $owner .= ":".$arguments["groupOwner"];
            if ($this->capp->remoteSSHCommand) {
                shell_exec($this->capp->remoteSSHCommand." 'setfacl $r -b \"".$path."\";setfacl $r --set-file=/tmp/".$this->getId()." ".$path."'");
                if ($owner!="")
                    shell_exec($this->capp->remoteSSHCommand." 'chown $r $owner \"".$path."\""."'");
            } else {
                $this->Shell->exec_command("setfacl $r -b ``".$path."``; setfacl $r --set-file=/tmp/".$this->getId()." ``".$path."``");
                if ($owner!="")
                    $this->Shell->exec_command("chown $r $owner ``".$path."``");                
            }
        }
        unlink($this->capp->remotePath."/tmp/".$this->getId());
        if (!$this->fileServer->loaded)
            $this->fileServer->load();
        if ($arguments["shareCheckBox"]=="1") {
            $fshare = $this->fileServer->containsPath(str_replace($this->fileServer->shares_root."/","",$paths[0]));
            if (!is_object($fshare)) {
                $fshare = $Objects->get("FileShare_".$this->module_id."_");
            }       
            if (!$fshare->loaded)
				$fshare->load();
            $fshare->oldFolders = $oldFolders;
            $paths[0] = str_replace("//","/",$paths[0]);
            $fshare->name = trim($arguments["share"]);
          
            $fshare->path = str_replace($this->fileServer->shares_root."/","",$paths[0]);
            $fshare->recycleBin = $arguments["recycleBin"];
            $fshare->recyclePath = $arguments["recyclePath"];
            $fshare->recyclePeriod = $arguments["recyclePeriod"];
            $fshare->fullAudit = $arguments["fullAudit"];
            $fshare->ftpFolder = $arguments["ftpFolder"];
            $fshare->changed_rules = $arguments["changed_rules"];
            $fshare->save();
        } else {
            $fshare = $this->fileServer->containsPath(str_replace($this->fileServer->shares_root."/","",$paths[0]));
           	if (is_object($fshare)) {
            	if (!$fshare->loaded)
   			        $fshare->load();
        		$this->fileServer->removeShare($fshare->idnumber);
       		}
        }
        $app = $Objects->get("Application");
        if (!$app->initiated)
        	$app->initModules();
        $app->raiseRemoteEvent("FM_CHANGEPROPERTIES","message=Пользователь изменил свойства файлов/каталогов `".str_replace("~","-",$arguments["paths"])."`");
    }
}
?>