<?php
/**
 * Класс управляет общим файловым ресурсом. Общий файловый ресурс состоит из
 * уникального идентификатора, имени и пути к нему. Информация об общем ресурсе
 * хранится в каталоге LDAP, в записи, использующей объектные классы top и fileShare.
 * Записи общих ресурсов хранятся в контейнере FileServer->getDN(), который состоит из
 * "ou=".FileServer->shares_base.",".FileServer->base_dn.
 *
 * Ресурс уникально идентифицируется своим уникальным идентификатором.
 * Общие ресурсы можно создавать, редактировать и удалять.  Удаляются общие ресурсы
 * методом removeShare класса FileServer. Для остальных операций предназначены
 * методы этого класса:
 *
 * load() - загружает информацию о ресурсе из базы
 * save() - сохраняет информацию о ресурсе в базу
 * getId() - возвращает идентификатор объекта
 * getDN() - возвращает имя DN-записи объекта
 * getPresentation() - возвращает представление объекта
 * 
 *
 * @author andrey
 */
class FileShare extends WABEntity {

    public $fileServer;
    public $accessRules,$users_access_rules,$groups_access_rules;

    function construct($params) {

        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();

        $this->module_id = $params[0]."_".$params[1];
        $this->idnumber = $params[2];

        $this->fileServer = $Objects->get("FileServer_".$this->module_id."_Shares");

        $this->shares_dn = $this->fileServer->getDN();
        $this->shares_root = $this->fileServer->shares_root;

        $this->name = "";
        $this->old_name = $this->name;
        $this->path = "";
        $this->old_path = "";
        $this->vfsObjects = "";
        $this->recyclePath = "";
        $this->recycleBin = 0;
        $this->recyclePeriod = 0;
        $this->fullAudit = 0;
        $this->fullAuditPeriod = 30;
        $this->ftpFolder = 0;
        $this->icon = $app->skinPath."images/Tree/folder.png";
        $this->skinPath = $app->skinPath;

        $this->template = "templates/controller/FileShare.html";
        $this->css = $app->skinPath."styles/Mailbox.css";
        $this->handler = "scripts/handlers/controller/FileShare.js";

        $this->tabs_string.= "hosts|Хосты|".$app->skinPath."images/spacer.gif;";
        $this->tabs_string.= "users|Пользователи|".$app->skinPath."images/spacer.gif;";
        $this->tabs_string.= "groups|Группы|".$app->skinPath."images/spacer.gif;";
        $this->tabs_string.= "trash|Сетевая корзина|".$app->skinPath."images/spacer.gif;";
        $this->tabs_string.= "audit|Журнал событий|".$app->skinPath."images/spacer.gif;";
        $this->tabs_string.= "info|Информация|".$app->skinPath."images/spacer.gif";

        $this->tabset_id = "WebItemTabset_".$this->module_id."_share".$this->idnumber;
        $this->active_tab = "hosts";
        $this->width = "680";
        $this->height = "400";
        $this->overrided = "width,height";
        $this->usersFrameSrc = "?object_id=ShareAccessRulesTable_".$this->module_id."_".$this->idnumber."_users&init_string=\$object->opener_item='".$this->getId()."';\$object->show();";
        $this->groupsFrameSrc = "?object_id=ShareAccessRulesTable_".$this->module_id."_".$this->idnumber."_groups&init_string=\$object->opener_item='".$this->getId()."';\$object->show();";

        $this->clientClass = "FileShare";
        $this->parentClientClasses = "Entity";
        
        $this->loaded = false;
    }

    function getNextId() {
        global $Objects;
        $this->fileServer = $Objects->get("FileServer_".$this->module_id."_Shares");
        if (!$this->fileServer->loaded)
            $this->fileServer->load();
        $ds = ldap_connect($this->fileServer->ldap_proto."://".$this->fileServer->ldap_host);
        if (ldap_error($ds)!="Success") {
            $this->reportError("Ошибка подключения к каталогу. Нет доступа к серверу.", "load");
            return 0;
        }

        ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
        $r = ldap_bind($ds,$this->fileServer->ldap_user,$this->fileServer->ldap_password);
        if (ldap_error($ds)!="Success") {
            $this->reportError("Ошибка аутентификации на сервере каталога.", "load");
            return 0;
        }

        $res = ldap_list($ds,$this->shares_dn,("(objectClass=fileShare)"));
        $entries = ldap_get_entries($ds,$res);

        if ($entries == FALSE)
            return 1;
        if ($entries["count"]<1)
            return 1;

        $max = 0;
        foreach ($entries as $key=>$value) {
            if (is_numeric($key)) {
                if ($max<$value["idnumber"])
                    $max = $value["idnumber"][0];
            }
        }
        return $max+1;
    }
    
    function save() {        
        global $Objects;
        $app = $Objects->get($this->module_id);
        
        $ftpServer = $Objects->get("FTPServer_".$this->module_id."_ftp");
        
        $this->fileServer = $Objects->get("FileServer_".$this->module_id."_Shares");
        if (!$this->fileServer->loaded)
            $this->fileServer->load();

        if ($this->name == "") {
            $this->reportError("Укажите имя общей папки","save");
            return 0;
        }

        if ($this->path == "") {
            $this->reportError("Укажите путь к общей папке ".$this->name,"save");
            return 0;
        }
        
        if (strpos($this->path," ")!==FALSE) {
            $this->reportError("Путь к общей папке не должен содержать пробелов!","save");
            return 0;            
        }

        if ($this->name != $this->old_name) {
            if ($this->fileServer->contains($this->name)) {
                $this->reportError("Общая папка с именем ".$this->name." уже существует","save");
                return 0;
            }
        }

        if ($this->path != $this->old_path) {
            if ($this->fileServer->containsPath($this->path)) {
                $this->reportError("Общая папка с путем ".$this->path." уже существует","save");
                return 0;
            }
        }

        if ($this->name == "root" and $this->name != $this->old_name) {
            $this->reportError("Недопустимое имя общей папки. Это имя зарезервировано");
            return 0;
        }
        
        if ($this->recycleBin) {
            if (trim($this->recyclePath)=="") {
                $this->reportError("Не указан путь к сетевой корзине","save");
                return 0;                
            }
            if (!file_exists($app->remotePath.$this->recyclePath)) {
                $this->reportError("Каталога, на который указывает путь сетевой корзины не существует","save");
                return 0;                                
            }
            if (trim($this->recyclePeriod)=="") {
                $this->reportError("Не указан период хранения данных в сетевой корзине","save");
                return 0;                
            }
        }

        global $Objects;
        $shell = $Objects->get("Shell_shell");
        $app = $Objects->get($this->module_id);
        
        if ($this->idnumber!="0") {
            if (!file_exists($app->remotePath.$this->fileServer->shares_root."/".$this->path)) {
                $this->reportError("Путь к общей папке не существует","save");
                return 0;
            }
            $ds = ldap_connect($this->fileServer->ldap_proto."://".$this->fileServer->ldap_host);
            if (ldap_error($ds)!="Success") {
                $this->reportError("Ошибка подключения к каталогу. Нет доступа к серверу.", "load");
                return 0;
            }

            ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
            $r = ldap_bind($ds,$this->fileServer->ldap_user,$this->fileServer->ldap_password);
            if (ldap_error($ds)!="Success") {
                $this->reportError("Ошибка аутентификации на сервере каталога.", "load");
                return 0;
            }

            $ap = $Objects->get("Application");
            if (!$ap->initiated)
                $ap->initModules();
            
            $entry = array();
            $entry["objectClass"][0] = "fileShare";
            $entry["comment"] = $this->name;
            $entry["sharePath"] = $this->path;
            $entry["ftpFolder"] = $this->ftpFolder;
            if ($app->remoteSSHCommand!="")
                $entry["shareInode"] = trim($shell->exec_command($app->remoteSSHCommand." \"".str_replace("{path}",$this->fileServer->shares_root."/".$this->path,$ap->getInodeCommand)."\""));
            else
                $entry["shareInode"] = trim($shell->exec_command(str_replace("{path}",$this->fileServer->shares_root."/".$this->path,$ap->getInodeCommand)));
            if ($this->fileServer->contains($this->old_name)) { 
                @ldap_modify($ds,"idnumber=".$this->idnumber.",".$this->shares_dn,$entry);
            } else {
                $entry["idnumber"] = $this->getNextId();
                $this->idnumber = $entry["idnumber"];
                //echo print_r($entry);
                ldap_add($ds,"idnumber=".$entry["idnumber"].",".$this->shares_dn,$entry);
            }
        }
        
        if (!file_exists($app->remotePath.$this->fileServer->smbSharesConfigPath)) {
            if ($app->remoteSSHCommand!="") {
                shell_exec($app->remoteSSHCommand." \"mkdir -p '".$this->fileServer->smbSharesConfigPath."'\"");
            }
            else {
                $shell->exec_command("mkdir -p ``".$this->fileServer->smbSharesConfigPath."``");                
                $shell->exec_command("chown -R guest:smbusers ``".$this->fileServer->smbSharesConfigPath."``");                
            }
        }

        if ($app->remoteSSHCommand!="") {
            shell_exec($app->remoteSSHCommand." \"mkdir -p '".$this->recyclePath."'\"");
            shell_exec($app->remoteSSHCommand." \"chown guest:smbusers '".$this->recyclePath."'\"");
        }
        else {
            $shell->exec_command("mkdir -p ``".$this->recyclePath."``");                
            $shell->exec_command("chown -R guest:smbusers ``".$this->recyclePath."``");                
        }
        
        if ($this->name != $this->old_name or $this->path != $this->old_path) {
            $dhcpSubnets = $Objects->get("DhcpSubnets_".$this->module_id."_Subnets");
            if (!$dhcpSubnets->loaded)
                $dhcpSubnets->load();

            $shares = $this->getNfsSharesFromFile();
            $afpshares = $this->getAfpSharesFromFile();
            if ($this->name!=$this->old_name) {
                if (file_exists($app->remotePath.$this->fileServer->smbSharesConfigPath."/".$this->old_name."_vfs.conf"))
                        @unlink($app->remotePath.$this->fileServer->smbSharesConfigPath."/".$this->old_name."_vfs.conf");
                if (isset($shares[$this->old_name])) {
                    $shares[$this->name] = $shares[$this->old_name];
                    unset($shares[$this->old_name]);
                }
                if (isset($afpshares[$this->old_name])) {
                    $afpshares[$this->name] = $afpshares[$this->old_name];
                    unset($afpshares[$this->old_name]);
                }
            }
            
            if ($this->path!=$this->old_path) {
                $shares[$this->name]["path"] = $this->fileServer->shares_root."/".$this->path;
                $afpshares[$this->name]["path"] = $this->fileServer->shares_root."/".$this->path;
            }
            $this->saveNfsSharesToFile($shares);
            $this->saveAfpSharesToFile($afpshares);
            
            foreach($dhcpSubnets->subnets as $subnet) {
                if (!$subnet->hosts_loaded)
                    $subnet->loadHosts();
                foreach ($subnet->hosts as $host) {
                    if (!$host->loaded)
                        $host->load();
                    if (isset($host->accessRules[$this->old_name])) {
                        $host->accessRules[$this->name]["path"] = $host->fileServer->shares_root."/".$this->path;
                        $host->accessRules[$this->name]["read_only"] = $host->accessRules[$this->old_name]["read_only"];
                        if (isset($host->smbShares[$this->old_name]) and $this->old_name!=$this->name) {
                                $host->smbShares[$this->name] = $host->smbShares[$this->old_name];
                                unset($host->smbShares[$this->old_name]);
                        }
                        if (isset($host->nfsShares[$this->old_name]) and $this->old_name!=$this->name) {
                                $host->nfsShares[$this->name] = $host->nfsShares[$this->old_name];
                                unset($host->nfsShares[$this->old_name]);
                        }
                        if (isset($host->afpShares[$this->old_name]) and $this->old_name!=$this->name) {
                                $host->afpShares[$this->name] = $host->afpShares[$this->old_name];
                                unset($host->afpShares[$this->old_name]);
                        }
                        if ($this->name != $this->old_name)
                            unset($host->accessRules[$this->old_name]);
                        $host->save(true);
                    }
                }
            }
            
            if ($this->old_path!="" and isset($host))
                $this->accessRules[$this->name]["path"] = $host->fileServer->shares_root."/".$this->path;
                                    
            if ($this->old_name != "" and $this->old_name!=$this->name and isset($this->accessRules[$this->old_name])) {
                $this->accessRules[$this->name]["read_only"] = $this->accessRules[$this->old_name]["read_only"];
                unset($this->accessRules[$this->old_name]);
            }
        }

        if ($this->old_name == "") {
            $this->accessRules = array();
            if (file_exists($app->remotePath.$this->fileServer->smbHostsPath."/default.conf")) {
                $strings = file($app->remotePath.$this->fileServer->smbHostsPath."/default.conf");
                $current_name = "";
                foreach($strings as $line) {
                    if (preg_match('/\[(.*)\]/',$line,$matches)==1)
                        $current_name = $matches[1];
                    if (preg_match('/path = (.*)/',$line,$matches)==1)
                        $this->accessRules[$current_name]["path"] = $matches[1];
                    if (preg_match('/read only = (.*)/',$line,$matches)==1)
                        $this->accessRules[$current_name]["read_only"] = $matches[1];
                }
            }
        }
        if ($this->changed_rules!="") {
            $rs = array();
            $rules_arr = explode("|",$this->changed_rules);
            foreach($rules_arr as $rule) {
                $rule_parts = explode("=",$rule);
                $read = explode(",",$rule_parts[1]);
                $smb = $read[1];
                $nfs = $read[2];
                $afp = $read[3];
                $read = $read[0];
                if ($rule_parts[0]=="default") {
                    if ($read!="") {
                        if ($read=="delete") {
                            unset($this->accessRules[$this->name]);
                        }
                        else {
                            $this->accessRules[$this->name] = array();
                            if ($this->name != "root")
                                $this->accessRules[$this->name]["path"] = $this->fileServer->shares_root."/".$this->path;
                            else
                                $this->accessRules[$this->name]["path"] = $this->path;
                            $this->accessRules[$this->name]["read_only"] = $read;
                        }
                        $this->smbShare = $smb;
                        $this->nfsShare = $nfs;
                        $this->afpShare = $afp;
                    }
                } else {
                    if ($read!="") {
                        if (isset($rs[$rule_parts[0]]))
                            continue;
                        $rs[$rule_parts[0]] = "yes";
                        $host = $Objects->get($rule_parts[0]);
                        if (!$host->loaded)
                            $host->load();
                        if (!$host->loaded)
                            continue;                        
                        if ($read=="delete") {
                            unset($host->accessRules[$this->name]);
                        }
                        else {                            
                            if ($this->name != "root")
                                $host->accessRules[$this->name]["path"] = $this->fileServer->shares_root."/".$this->path;
                            else
                                $host->accessRules[$this->name]["path"] = $this->path;
                            $host->accessRules[$this->name]["read_only"] = $read;
                        }
                        if ($smb=="")
                            unset($host->smbShares[$this->name]);
                        else
                         $host->smbShares[$this->name] = "yes";
                        if ($nfs=="")
                            unset($host->nfsShares[$this->name]);
                        else
                         $host->nfsShares[$this->name] = "yes";     
                        if ($afp=="")
                            unset($host->afpShares[$this->name]);
                        else
                         $host->afpShares[$this->name] = "yes";     
                        $host->save(true);
                    }
                }
            }
        }
        $fp = fopen($app->remotePath.$this->fileServer->smbHostsPath."/default.conf","w");
        if (count($this->accessRules)>0) {
            $strings = file($this->fileServer->smbShareTemplateFile);
            foreach($this->accessRules as $key=>$rule) {
                if ($key==$this->name and !$this->smbShare)
                    continue;
                foreach ($strings as $line) {
                    fwrite($fp,strtr($line,array("{name}" => $key, "{path}" => $rule["path"], "{read_only}" => $rule["read_only"], "{recyclePath}")));
                }
            }
        } else fwrite($fp," ");
        fclose($fp);
        
        $shares = $this->getNfsSharesFromFile();
        if ($this->nfsShare) {
            if (isset($shares[$this->name])) {
                if (isset($shares[$this->name]["hosts"]["*"])) {
                    unset($shares[$this->name]["hosts"]["*"]["ro"]);
                    unset($shares[$this->name]["hosts"]["*"]["rw"]);
                    if ($this->accessRules[$this->name]["read_only"]=="yes")
                        $shares[$this->name]["hosts"]["*"]["ro"] = "ro";
                    else
                        $shares[$this->name]["hosts"]["*"]["rw"] = "rw";                    
                } else {
                    if (@$this->accessRules[$this->name]["read_only"]=="yes")
                        $shares[$this->name]["hosts"]["*"]["ro"] = "ro";
                    else
                     $shares[$this->name]["hosts"]["*"]["rw"] = "rw";                                        
                }
            } else {
                $shares[$this->name] = array();
                if ($this->name != "root")
                    $shares[$this->name]["path"] = $this->fileServer->shares_root."/".$this->path;
                else
                    $shares[$this->name]["path"] = $this->path;                    
                $shares[$this->name]["options"] = "-".$this->fileServer->nfsDefaultOptions;
                $shares[$this->name]["hosts"] = array();                
                $shares[$this->name]["hosts"]["*"] = array();
                if ($this->accessRules[$this->name]["read_only"]=="yes")
                    $shares[$this->name]["hosts"]["*"]["ro"] = "ro";
                else
                    $shares[$this->name]["hosts"]["*"]["rw"] = "rw";
            }
        } else {
            if (isset($shares[$this->name])) {
                if (isset($shares[$this->name]["hosts"]["*"]))
                    unset($shares[$this->name]["hosts"]["*"]);
                if (count($shares[$this->name]["hosts"])==0)
                    unset($shares[$this->name]);
            }
        }
        $this->saveNfsSharesToFile($shares);

        $shares = $this->getafpSharesFromFile();
        if ($this->afpShare) {
        	if (isset($shares[$this->name])) {
        		if (!isset($shares[$this->name]["hosts"][$app->afpDefaultNetwork])) {
	   				$shares[$this->name]["hosts"][$app->afpDefaultNetwork] = "rw";
        		}
        	} else {
        		$shares[$this->name] = array();
        		if ($this->name != "root")
        			$shares[$this->name]["path"] = $this->fileServer->shares_root."/".$this->path;
        		else
        			$shares[$this->name]["path"] = $this->path;
        		$shares[$this->name]["hosts"] = array();
       			$shares[$this->name]["hosts"][$app->afpDefaultNetwork] = "rw";
        	}
        } else {
        	if (isset($shares[$this->name])) {
        		if (isset($shares[$this->name]["hosts"][$app->afpDefaultNetwork]))
        			unset($shares[$this->name]["hosts"][$app->afpDefaultNetwork]);
        		if (count($shares[$this->name]["hosts"])==0)
        			unset($shares[$this->name]);
        	}
        }
        $this->saveAfpSharesToFile($shares);
        
        $this->vfsObjects = "";
        if ($this->recycleBin)
            $this->vfsObjects .= "recycle ";
        if ($this->fullAudit)
            $this->vfsObjects .= "full_audit ";
            $this->vfsObjects .= "shadow_copy";

        $this->vfsObjects = trim($this->vfsObjects);
        $fp = fopen($app->remotePath.$this->fileServer->smbSharesConfigPath."/".$this->name."_vfs.conf","w");
        $strings = file($this->fileServer->smbShareVFSTemplateFile);
        $args = $this->getArgs();
        foreach ($strings as $line)
            fwrite($fp,strtr($line,$args));
        fclose($fp);
        if ($app->remoteSSHCommand=="") {
            if ($this->fileServer->smbAutoRestart) {
                $shell->exec_command($this->fileServer->smbReloadCommand);
                $shell->exec_command($this->fileServer->nfsRestartCommand);
                $shell->exec_command($this->fileServer->afpRestartCommand);
            }
        } else {
            $cmd = " \"";
            if ($this->fileServer->smbAutoRestart)
                $cmd .= $this->fileServer->smbReloadCommand.";".$this->fileServer->nfsRestartCommand.";".$this->fileServer->afpRestartCommand.";";
                $cmd .= "\"";
                shell_exec($app->remoteSSHCommand." 'echo ".$cmd." | at now'");            
        }
        if ($this->oldFolders!="") {			
            $ftpServer->setFolders($ftpServer->getFolders(),$this->oldFolders);
            $ftpServer->restart();
        }
        if ($this->old_name=="")
        	$ap->raiseRemoteEvent("FILESHARE_ADDED","object_id=".$this->getId());
        else
        	$ap->raiseRemoteEvent("FILESHARE_CHANGED","object_id=".$this->getId());
        
        $this->old_name = $this->name;        
        echo trim($this->idnumber);        
        $this->loaded = true;
    }

    function load() {
        global $Objects;
        if ($this->loaded)
            return 0;
        
        $this->fileServer = $Objects->get("FileServer_".$this->module_id."_Shares");
        if (!$this->fileServer->loaded)
            $this->fileServer->load();
        
        $shell = $Objects->get("Shell_shell");
        $app = $Objects->get($this->module_id);
        if ($this->idnumber!="0") {
            $ds = ldap_connect($this->fileServer->ldap_proto."://".$this->fileServer->ldap_host);
            if (ldap_error($ds)!="Success") {
                $this->reportError("Ошибка подключения к каталогу. Нет доступа к серверу.", "load");
                return 0;
            }

            ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
            $r = ldap_bind($ds,$this->fileServer->ldap_user,$this->fileServer->ldap_password);
            if (ldap_error($ds)!="Success") {
                $this->reportError("Ошибка аутентификации на сервере каталога.", "load");
                return 0;
            }
            $result = ldap_list($ds,$this->shares_dn,"(idnumber=".$this->idnumber.")");

            if ($result == FALSE)
                return 0;
            $entries = ldap_get_entries($ds,$result);
            if ($entries["count"]==0)
                return 0;
            $this->name = $entries[0]["comment"][0];
            $this->path = $entries[0]["sharepath"][0];
            $this->ftpFolder = @$entries[0]["ftpfolder"][0];
            $this->oldFtpFolder = $this->ftpFolder;
        } else {
            $this->name = "root";
            $this->path = $this->fileServer->shares_root;
        }
		$this->getAccessRules();
        $this->users_access_rules = array();

        if ($this->name=="root")
            $path = $this->path;
        else
            $path = $this->fileServer->shares_root."/".$this->path;
        $change_arr = array("{user_or_group}" => "other", "{user}" => "", "{share}" => $path);
        if ($app->remoteSSHCommand!="")
            $rights = $shell->exec_command(strtr($app->remoteSSHCommand." \"".$this->fileServer->smbGetACLCommand."\"",$change_arr));
        else
            $rights = $shell->exec_command(strtr($this->fileServer->smbGetACLCommand,$change_arr));

        if ($rights!="") {
            $rights = str_replace("-","",substr($rights, 0, 2));
            $this->users_access_rules[$this->name] = $path."~".$rights;
        }
        
        $this->groups_access_rules = array();

        if ($this->name=="root")
            $path = $this->path;
        else
            $path = $this->fileServer->shares_root."/".$this->path;
        $change_arr = array("{user_or_group}" => "group", "{user}" => "", "{share}" => $path);
        if ($app->remoteSSHCommand!="")
            $rights = $shell->exec_command(strtr($app->remoteSSHCommand." \"".$this->fileServer->smbGetACLCommand."\"",$change_arr));
        else
            $rights = $shell->exec_command(strtr($this->fileServer->smbGetACLCommand,$change_arr));

        if ($rights!="") {
            $rights = str_replace("-","",substr($rights, 0, 2));
            $this->groups_access_rules[$this->name] = $path."~".$rights;
        }
        
        $this->old_name = $this->name;
        $this->old_path = $this->path;
        
        $share_access_rules = array();
        $share_access_rules["users"] = $this->getShareAccessRules("user");
        $share_access_rules["groups"] = $this->getShareAccessRules("group");
        $this->share_access_rules = $share_access_rules;
        
        $this->loaded = true;
        
        $this->hosts_access_rules_table = "ShareAccessRulesTable_".$this->module_id."_".$this->idnumber."_hosts";
        $this->users_access_rules_table = "ShareAccessRulesTable_".$this->module_id."_".$this->idnumber."_users";
        $this->groups_access_rules_table = "ShareAccessRulesTable_".$this->module_id."_".$this->idnumber."_groups";
        $this->hosts_rows = str_replace("'","xox",str_replace("\n","yoy",str_replace('"',"zoz",str_replace(",","oao",$this->getRows($this->hosts_access_rules_table)))));
        $this->users_rows = str_replace("'","xox",str_replace("\n","yoy",str_replace('"',"zoz",str_replace(",","oao",$this->getRows($this->users_access_rules_table)))));
        $this->groups_rows = str_replace("'","xox",str_replace("\n","yoy",str_replace('"',"zoz",str_replace(",","oao",$this->getRows($this->groups_access_rules_table)))));        
        
        if (file_exists($app->remotePath.$this->fileServer->smbSharesConfigPath."/".$this->name."_vfs.conf")) {
            $strings = file($app->remotePath.$this->fileServer->smbSharesConfigPath."/".$this->name."_vfs.conf");
            $current_name = "";
            foreach($strings as $line) {
                if (preg_match('/vfs objects = (.*)/',$line,$matches)==1) {
                    $vfsObjects = explode(" ",$matches[1]);
                    if (array_search("recycle",$vfsObjects)!==FALSE)
                        $this->recycleBin = 1;
                    if (array_search("full_audit",$vfsObjects)!==FALSE)
                        $this->fullAudit = 1;
                }
                if (preg_match('/recycle:repository = (.*)/',$line,$matches)==1)
                    $this->recyclePath = trim($matches[1]);
                if (preg_match('/#recycle:period = (.*)/',$line,$matches)==1)
                    $this->recyclePeriod = trim($matches[1]);                
            }            
        } else {
            $this->recyclePath = "";
            $this->recyclePeriod = 0;
        }
    }

    function getNfsSharesFromFile() {
        global $Objects;
        $app = $Objects->get($this->module_id);
        if (!isset($this->fileServer))
                $this->fileServer = $Objects->get("FileServer_".$this->module_id."_Shares");
        if (file_exists($app->remotePath.$this->fileServer->nfsConfigFile)) {
            $strings = file($app->remotePath.$this->fileServer->nfsConfigFile);
            $shares = array();
            foreach ($strings as $line) {
                $line=trim($line);
                if ($line[0]!="#") {
                    $line_parts = explode("#",$line);
                    $shareName = trim($line_parts[1]);
                    $line = $line_parts[0];
                    $line_parts = explode(" ",$line);
                    $path = array_shift($line_parts);
                    $hosts = array();
                    foreach($line_parts as $part) {
                        $part = trim($part);
                        if ($part=="")
                            continue;
                        if ($part{0}=="-") {
                            $shares[$shareName]["options"] = $part;
                            continue;
                        }
                        if ($part!="") {                            
                            $matches = array();
                            if (preg_match("~^(\S+)\((\S+)\)~",$part,$matches)) {
                                $hosts[$matches[1]] = array_flip(explode(",",$matches[2]));
                            }
                        }
                    }
                    $shares[$shareName]["path"] = $path;
                    $shares[$shareName]["hosts"] = $hosts;
                }
            }
            return $shares;
        }
        return 0;
    }
    
    function getAfpSharesFromFile() {
    	global $Objects;
    	$app = $Objects->get($this->module_id);
    	$shares = array();
    	if (file_exists($app->remotePath.$app->afpSharesFile)) {
    		$strings = file($app->remotePath.$app->afpSharesFile);
    		foreach ($strings as $line) {
    			$matches = array();
    			if (preg_match("/(.*) (.*) cnidscheme\:dbd allowed_hosts\:(.*)/",$line,$matches)) {
    				$shares[trim($matches[2])]["path"] = trim($matches[1]);
    				$shares[trim($matches[2])]["hosts"] = array_flip(explode(",",trim($matches[3])));
    			}
    		}
    	}
    	return $shares;
    }
        
    function saveNfsSharesToFile($shares) {
        global $Objects;
        $app = $Objects->get($this->module_id);    
        if (!isset($this->fileServer))
                $this->fileServer = $Objects->get("FileServer_".$this->module_id."_Shares");
        if (file_exists($app->remotePath.$this->fileServer->nfsConfigFile)) {
            $fp = fopen($app->remotePath.$this->fileServer->nfsConfigFile,"w");
            foreach ($shares as $key=>$share) {
                $hosts_string = "";
                if (isset($share["hosts"])) {
                    foreach ($share["hosts"] as $host=>$options) {
                        $hosts_string .= $host."(".implode(",",array_flip($options)).") ";
                    }
                }
                if (isset($share["options"]))
                    fwrite($fp,$share["path"]." ".$share["options"]." ".$hosts_string."# ".$key."\n");
            }
        }
        fclose($fp);
    }

    function saveAfpSharesToFile($shares) {
    	global $Objects;
    	$app = $Objects->get($this->module_id);
    	$fp = fopen($app->remotePath.$app->afpSharesFile,"w");
    	fwrite($fp,":DEFAULT: options:".$app->afpDefaultOptions."\n");
    	foreach($shares as $key=>$value) {
    		if (isset($value["hosts"]))
    			if (is_array($value["hosts"]) and count($value["hosts"])>0)
    				fwrite($fp,$value["path"]." ".$key." cnidscheme:dbd allowed_hosts:".@implode(",",@array_keys($value["hosts"]))."\n");
    	}
    	fclose($fp);
    }
    
    function getAccessRules() {
        global $Objects;        
        if (!$this->fileServer->loaded)
            $this->fileServer->load();        
        $app = $Objects->get($this->module_id);
        $this->accessRules = array();   
        $this->smbShare=false;
        $this->nfsShare=false;
        if (file_exists($app->remotePath.$this->fileServer->smbHostsPath."/default.conf")) {
            $strings = file($app->remotePath.$this->fileServer->smbHostsPath."/default.conf");
            $current_name = "";
            foreach($strings as $line) {
                if (preg_match('/\[(.*)\]/',$line,$matches)==1) {
                    $current_name = $matches[1];
                }
                if (preg_match('/path = (.*)/',$line,$matches)==1)
                    $this->accessRules[$current_name]["path"] = $matches[1];
                if (preg_match('/read only = (.*)/',$line,$matches)==1)
                    $this->accessRules[$current_name]["read_only"] = $matches[1];
            }            
        }
        if (isset($this->accessRules[$this->name]))
            $this->smbShare = true;
        $shares = $this->getNfsSharesFromFile();
        if (isset($shares[$this->name])) {
            if (isset($shares[$this->name]["hosts"]["*"])) {
            	$this->nfsShare = true;
                if (!$this->smbShare) {
                    $this->accessRules[$this->name]["path"] = $shares[$this->name]["path"];
                    if (isset($shares[$this->name]["hosts"]["*"]["rw"]))
                        $this->accessRules[$this->name]["read_only"] = "no";
                    else
                        $this->accessRules[$this->name]["read_only"] = "yes";
                }
            }
        }
        $shares = $this->getAfpSharesFromFile();
        if (isset($shares[$this->name])) {
            if (isset($shares[$this->name]["hosts"][$app->afpDefaultNetwork])) {
            	$this->afpShare = true;
                if (!$this->smbShare and !$this->nfsShare) {
                    $this->accessRules[$this->name]["path"] = $shares[$this->name]["path"];
                    $this->accessRules[$this->name]["read_only"] = "no";
                }
            }
        }
        $share_access_rules = array();
        $share_access_rules["users"] = $this->getShareAccessRules("user");
        $share_access_rules["groups"] = $this->getShareAccessRules("group");
        $this->share_access_rules = $share_access_rules;
    }
    
    function getShareAccessRules($type) {
        global $Objects;
        $app = $Objects->get($this->module_id);
        $shell = $Objects->get("Shell_shell");
        if ($this->path!=$this->fileServer->shares_root)
            $change_arr = array("{type}" => $type, "{share}" => $this->fileServer->shares_root."/".$this->path);
        else
            $change_arr = array("{type}" => $type, "{share}" => $this->path);
        if ($app->remoteSSHCommand!="")
            $rights = shell_exec(strtr($app->remoteSSHCommand." \"".$this->fileServer->smbGetShareACLCommand."\"",$change_arr));
        else
            $rights = $shell->exec_command(strtr($this->fileServer->smbGetShareACLCommand,$change_arr));
        $result = array();
        if ($rights!="") {
            $i=0;
            $rights_array = explode("\n",$rights);
            foreach ($rights_array as $right) {
                $right_parts = explode("~",$right);
                if (!isset($right_parts[1]))
                    $right_parts = explode(";",$right);
                if (isset($right_parts[1]))
                    $result[$right_parts[0]] = $right_parts[1];
            }
        }
        return $result;
    }
    
    function getId() {
        return "FileShare_".$this->module_id."_".$this->idnumber;
    }

    function getDN() {
        return "idnumber=".$this->idnumber.",".$this->shares_dn;
    }

    function getPresentation() {
        if (!$this->loaded)
            $this->load();
        return $this->name;
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
    
    function getArgs() {
        if (!$this->loaded) {
            $share_access_rules = array();
            $share_access_rules["users"] = $this->getShareAccessRules("user");
            $share_access_rules["groups"] = $this->getShareAccessRules("group");
            $this->share_access_rules = $share_access_rules;

            $this->hosts_access_rules_table = "ShareAccessRulesTable_".$this->module_id."_".$this->idnumber."_hosts";
            $this->users_access_rules_table = "ShareAccessRulesTable_".$this->module_id."_".$this->idnumber."_users";
            $this->groups_access_rules_table = "ShareAccessRulesTable_".$this->module_id."_".$this->idnumber."_groups";
            $this->hosts_rows = str_replace("'","xox",str_replace("\n","yoy",str_replace('"',"zoz",str_replace(",","oao",$this->getRows($this->hosts_access_rules_table)))));
            $this->users_rows = str_replace("'","xox",str_replace("\n","yoy",str_replace('"',"zoz",str_replace(",","oao",$this->getRows($this->users_access_rules_table)))));
            $this->groups_rows = str_replace("'","xox",str_replace("\n","yoy",str_replace('"',"zoz",str_replace(",","oao",$this->getRows($this->groups_access_rules_table)))));        
        }
        if ($this->name!="root")
            $dir = $this->fileServer->shares_root."/".$this->path;
        else
            $dir = $this->fileServer->shares_root;
        $this->totalSize = round(disk_total_space($dir."/")/1024/1024,2);
        $this->freeSize = round(disk_free_space($dir."/")/1024/1024,2);
        $this->usedSize = round($this->getDirSize($dir)/1024/1024,2);
        $result = parent::getArgs();        
        return $result;
    }
    
    function getRows($tableId) {    	 
        global $Objects;
                
        $fileShare = $this;
        if (!$this->loaded)
	        $this->load();
            
        $this->type = array_pop(explode("_",$tableId));
        
        switch ($this->type) {
            case "hosts":
                $this->title = "Хост";
                break;
            case "users":
                $this->title = "Пользователь";
                break;
            case "groups":
                $this->title = "Группа";
                break;
        }
        
        $result = array();
        $c = count($result);
        $result[$c][0] = " ~class=header";
        $result[$c][1] = $this->title."~class=header&style=width:100%";
        $result[$c][2] = '<input column="allRead" type="checkbox" id="'.$tableId.'_checkAllRead" onclick="$O(\''.$tableId.'\',\'\').checkAllRead(event)">Чтение~class=header&nowrap';
        $result[$c][2] = str_replace('"','\"',$result[$c][2]);
        $result[$c][3] = '<input column="allWrite" type="checkbox" id="'.$tableId.'_checkAllWrite" onclick="$O(\''.$tableId.'\',\'\').checkAllWrite(event)">Запись~class=header&nowrap';
        $result[$c][3] = str_replace('"','\"',$result[$c][3]);
        if ($this->type=="hosts") {
            $result[$c][4] = '<input column="allSMB" type="checkbox" id="'.$tableId.'_checkAllSMB" onclick="$O(\''.$tableId.'\',\'\').checkAllSMB(event)">SMB~class=header&nowrap';
            $result[$c][4] = str_replace('"','\"',$result[$c][4]);
            $result[$c][5] = '<input column="allNFS" type="checkbox" id="'.$tableId.'_checkAllNFS" onclick="$O(\''.$tableId.'\',\'\').checkAllNFS(event)">NFS~class=header&nowrap';
            $result[$c][5] = str_replace('"','\"',$result[$c][5]);
            $result[$c][6] = '<input column="allAFP" type="checkbox" id="'.$tableId.'_checkAllAFP" onclick="$O(\''.$tableId.'\',\'\').checkAllAFP(event)">AFP~class=header&nowrap';
            $result[$c][6] = str_replace('"','\"',$result[$c][6]);
        }

        $check_read = "";
        $check_write = "";
        $check_smb = "";
        $check_nfs = "";
        $check_afp = "";
        if ($this->type=="hosts") {
            if (isset($fileShare->accessRules[$fileShare->name])) {
                if ($fileShare->accessRules[$fileShare->name]["read_only"]=="yes") {
                    $check_read = 'checked value="checked"';
                    $check_write = "";
                } else {
                    $check_read = 'checked value="checked"';
                    $check_write = 'checked value="checked"';
                }                
                if ($this->smbShare) {
                    $this->smbShareCheck = true;
                	$check_smb = 'checked value="checked"';
                }
                else
                    $check_smb = '';
                if ($this->nfsShare) {                	
                    $this->nfsShareCheck = true;
                	$check_nfs = 'checked value="checked"';
                }
                else
                  $check_nfs = '';
                if ($this->afpShare) {                	
                    $this->afpShareCheck = true;
                	$check_afp = 'checked value="checked"';
                }
                else
                  $check_afp = '';
            }
        }
        if ($this->type=="users") {
            if (isset($fileShare->users_access_rules[$fileShare->name])) {
                if (array_pop(explode("~",$fileShare->users_access_rules[$fileShare->name]))=="r") {
                    $check_read = 'checked value="checked"';
                    $check_write = "";
                } else if (array_pop(explode("~",$fileShare->users_access_rules[$fileShare->name]))=="rw") {
                    $check_read = 'checked value="checked"';
                    $check_write = 'checked value="checked"';
                }
            }
        }
        if ($this->type=="groups") {
            if (isset($fileShare->groups_access_rules[$fileShare->name])) {
                if (array_pop(explode("~",$fileShare->groups_access_rules[$fileShare->name]))=="r") {
                    $check_read = 'checked value="checked"';
                    $check_write = "";
                } else if (array_pop(explode("~",$fileShare->groups_access_rules[$fileShare->name]))=="rw") {
                    $check_read = 'checked value="checked"';
                    $check_write = 'checked value="checked"';
                }
            }
        }
        if ($this->type != "groups") {
            $c = count($result);
            $result[$c][0] = '~class=cell';
            $result[$c][1] = "По умолчанию~class=cell";
            $result[$c][2] = '<input '.$check_read.' column="defaultRead" type="checkbox" id="'.$tableId.'_Default_checkRead" onclick="$O(\''.$tableId.'\',\'\').checkRead(event)">~class=cell&nowrap';
            $result[$c][2] = str_replace('"','\"',$result[$c][2]);
            $result[$c][3] = '<input '.$check_write.' column="defaultWrite" type="checkbox" id="'.$tableId.'_Default_checkWrite" onclick="$O(\''.$tableId.'\',\'\').checkWrite(event)">~class=cell&nowrap';
            $result[$c][3] = str_replace('"','\"',$result[$c][3]);
            if ($this->type=="hosts") {
                $result[$c][4] = '<input '.$check_smb.' column="defaultSMB" type="checkbox" id="'.$tableId.'_Default_checkSMB" onclick="$O(\''.$tableId.'\',\'\').checkSMB(event)">~class=cell&nowrap';
                $result[$c][4] = str_replace('"','\"',$result[$c][4]);
                $result[$c][5] = '<input '.$check_nfs.' column="defaultNFS" type="checkbox" id="'.$tableId.'_Default_checkNFS" onclick="$O(\''.$tableId.'\',\'\').checkNFS(event)">~class=cell&nowrap';
                $result[$c][5] = str_replace('"','\"',$result[$c][5]);                
                $result[$c][6] = '<input '.$check_afp.' column="defaultAFP" type="checkbox" id="'.$tableId.'_Default_checkAFP" onclick="$O(\''.$tableId.'\',\'\').checkAFP(event)">~class=cell&nowrap';
                $result[$c][6] = str_replace('"','\"',$result[$c][6]);                
            }
        } 

        $fileServer = $Objects->get("FileServer_".$this->module_id."_Shares");
        $object_groups = array();
        if ($this->type=="hosts") {
            if (!$fileServer->objectGroupsLoaded)
                $fileServer->loadObjectGroups();
            foreach ($fileServer->objectGroups as $group) {
                $object_groups[$group->name] = $group;
            }
        }
        if ($this->type=="users") {
            if (!$fileServer->usersLoaded)
                $fileServer->loadUsers(false);
            $users = array();
            foreach ($fileServer->users as $user) {
                if ($user->name=="" or $user->name =="root")
                    continue;
                $users[$user->name] = $user;
            }
            $object_groups["users"] = $users;
        }
        if ($this->type=="groups") {
            if (!$fileServer->groupsLoaded)
                $fileServer->loadGroups();
            $groups = array();
            foreach ($fileServer->groups as $group) {
                if ($group->name=="" or $group->name =="root")
                    continue;
                    $groups[$group->name] = $group;
            }
            $object_groups["groups"] = $groups;
        }
        
        ksort($object_groups);

        foreach($object_groups as $group) {
            if ($this->type=="hosts") {
                $hosts = $group->getObjects();
                $parent_group = $group->idnumber;
                $display_style = "none";
            }
            else {
                $hosts = $group;
                $parent_group = "''";
                $display_style = "";
            }
            ksort($hosts);
            if ($this->type=="hosts") {
                $c = count($result);
                $result[$c][0] = '<img src="'.$group->icon.'" onclick="$O(\''.$tableId.'\',\'\').expandGroup(event,\''.$group->idnumber.'\')">~class=group_expand_image';
                $result[$c][0] = str_replace('"','\"',$result[$c][0]);
                if (count($hosts)>0)
                    $result[$c][1] = $group->name."</span>~class=expandable_cell1";
                else
                    $result[$c][1] = $group->name."~class=expandable_cell2";
                $result[$c][2] = '<input class="{read_checkbox_class}" {checked} column="groupRead" type="checkbox" id="'.$tableId.'_'.$group->idnumber.'_checkGroupRead" onclick="$O(\''.$tableId.'\',\'\').checkGroupRead(event,\''.$group->idnumber.'\')">~class={checkbox_class}&nowrap';
                $result[$c][2] = str_replace('"','\"',$result[$c][2]);
                $result[$c][3] = '<input class="{write_checkbox_class}" {checked} column="groupWrite" type="checkbox" id="'.$tableId.'_'.$group->idnumber.'_checkGroupWrite" onclick="$O(\''.$tableId.'\',\'\').checkGroupWrite(event,\''.$group->idnumber.'\')">~class={checkbox_class}&nowrap';
                $result[$c][3] = str_replace('"','\"',$result[$c][3]);
                $result[$c][4] = '<input class="{read_checkbox_class}" {checked} column="groupSMB" type="checkbox" id="'.$tableId.'_'.$group->idnumber.'_checkGroupSMB" onclick="$O(\''.$tableId.'\',\'\').checkGroupSMB(event,\''.$group->idnumber.'\')">~class={checkbox_class}&nowrap';
                $result[$c][4] = str_replace('"','\"',$result[$c][4]);
                $result[$c][5] = '<input class="{write_checkbox_class}" {checked} column="groupNFS" type="checkbox" id="'.$tableId.'_'.$group->idnumber.'_checkGroupNFS" onclick="$O(\''.$tableId.'\',\'\').checkGroupNFS(event,\''.$group->idnumber.'\')">~class={checkbox_class}&nowrap';
                $result[$c][5] = str_replace('"','\"',$result[$c][5]);
                $result[$c][6] = '<input class="{write_checkbox_class}" {checked} column="groupAFP" type="checkbox" id="'.$tableId.'_'.$group->idnumber.'_checkGroupAFP" onclick="$O(\''.$tableId.'\',\'\').checkGroupAFP(event,\''.$group->idnumber.'\')">~class={checkbox_class}&nowrap';
                $result[$c][6] = str_replace('"','\"',$result[$c][6]);
                $current_group = $c;
                if (count($hosts)>0) {
                    $all_read_check = true;$any_read_check=false;
                    $all_write_check = true; $any_write_check = false;
                    $all_smb_check=true;$any_smb_check=false;
                    $all_nfs_check=true;$any_nfs_check=false;
                    $all_afp_check=true;$any_afp_check=false;
                } else {
                    $all_read_check = false;$any_read_check=false;
                    $all_write_check = false; $any_write_check = false;                    
                    $all_smb_check=false;$any_smb_check=false;
                    $all_nfs_check=false;$any_nfs_check=false;
                    $all_afp_check=false;$any_afp_check=false;
                }
            }
            foreach($hosts as $host) {
                $c = count($result);
                $check_read = ""; $check_write="";
                if ($this->type=="hosts") {
                    if (!$host->loaded)
                        $host->getAccessRules();
                    if (isset($host->accessRules[$fileShare->name])) {
                        if ($host->accessRules[$fileShare->name]["read_only"]=="yes") {
                            $check_read = 'checked value="checked"';
                            $any_read_check = true;                            
                            $check_write = "";
                            $all_write_check = false;
                        } else {
                            $any_read_check = true;
                            $any_write_check = true;
                            $check_read = 'checked value="checked"';
                            $check_write = 'checked value="checked"';
                        }
                    } else {
                        $all_read_check = false;
                        $all_write_check = false;
                    }
                    if (isset($host->smbShares[$fileShare->name])) {
                        $any_smb_check=true;
                        $this->smbShareCheck = true;
                        $check_smb = 'checked value="checked"';
                    } else {
                        $all_smb_check=false;                        
                        $check_smb='';
                    }
                    if (isset($host->nfsShares[$fileShare->name])) {
                        $any_nfs_check=true;
                        $this->nfsShareCheck = true;
                        $check_nfs = 'checked value="checked"';
                    } else {
                        $all_nfs_check=false;                        
                        $check_nfs = '';
                    }
                    if (isset($host->afpShares[$fileShare->name])) {
                        $any_afp_check=true;
                        $this->afpShareCheck = true;
                        $check_afp = 'checked value="checked"';
                    } else {
                        $all_afp_check=false;                        
                        $check_afp = '';
                    }
                    $id = $tableId.'_'.$host->getId();
                } else {
                    if (isset($this->share_access_rules[$this->type][$host->name])) {
                        if ($this->share_access_rules[$this->type][$host->name]=="r-x") {
                            $check_read = 'checked value="checked"';
                            $check_write = "";
                        } else if ($this->share_access_rules[$this->type][$host->name]=="rwx") {
                            $check_read = 'checked value="checked"';
                            $check_write = 'checked value="checked"';
                        }
                    }
                    $id = $host->name;
                }

                $result[$c][0] = '<img src="'.$host->icon.'">~class=cell';
                $result[$c][0] = str_replace('"','\"',$result[$c][0]);                 
                $result[$c][1] = "&nbsp;&nbsp;&nbsp;&nbsp;".$host->name."~class=cell";
                $result[$c][2] = '<input '.$check_read.' column="hostRead" parent_group='.$parent_group.' type="checkbox" id="'.$id.'_checkRead" onclick="$O(\''.$tableId.'\',\'\').checkRead(event)">~class=cell&nowrap';
                $result[$c][2] = str_replace('"','\"',$result[$c][2]);
                $result[$c][3] = '<input '.$check_write.' column="hostWrite" parent_group='.$parent_group.' type="checkbox" id="'.$id.'_checkWrite" onclick="$O(\''.$tableId.'\',\'\').checkWrite(event)">~class=cell&nowrap';
                $result[$c][3] = str_replace('"','\"',$result[$c][3]);
                if ($this->type=="hosts") {
                    $result[$c][1] = "<span title='".$host->title."'>&nbsp;&nbsp;&nbsp;&nbsp;".$host->name."</host>~class=cell";
                    $result[$c][4] = '<input '.$check_smb.' column="hostSMB" parent_group='.$parent_group.' type="checkbox" id="'.$id.'_checkSMB" onclick="$O(\''.$tableId.'\',\'\').checkSMB(event)">~class=cell&nowrap';
                    $result[$c][4] = str_replace('"','\"',$result[$c][4]);
                    $result[$c][5] = '<input '.$check_nfs.' column="hostNFS" parent_group='.$parent_group.' type="checkbox" id="'.$id.'_checkNFS" onclick="$O(\''.$tableId.'\',\'\').checkNFS(event)">~class=cell&nowrap';
                    $result[$c][5] = str_replace('"','\"',$result[$c][5]);                    
                    $result[$c][6] = '<input '.$check_afp.' column="hostAFP" parent_group='.$parent_group.' type="checkbox" id="'.$id.'_checkAFP" onclick="$O(\''.$tableId.'\',\'\').checkAFP(event)">~class=cell&nowrap';
                    $result[$c][6] = str_replace('"','\"',$result[$c][6]);                    
                    $result[$c][7] = "row_attrs~parent_group=".$parent_group."&style=display:".$display_style;
                } else
                    $result[$c][4] =  "row_attrs~parent_group=".$parent_group."&style=display:".$display_style;
            }
            if ($this->type=="hosts") {
                if ($all_read_check) {
                    $result[$current_group][2] = str_replace("{checked}","checked",$result[$current_group][2]);
                } else
                    $result[$current_group][2] = str_replace("{checked}","",$result[$current_group][2]);
                if ($all_write_check) {
                    $result[$current_group][3] = str_replace("{checked}","checked",$result[$current_group][3]); 
                } else 
                    $result[$current_group][3] = str_replace("{checked}","",$result[$current_group][3]);                     
                if ($all_smb_check) {
                    $result[$current_group][4] = str_replace("{checked}","checked",$result[$current_group][4]); 
                } else 
                    $result[$current_group][4] = str_replace("{checked}","",$result[$current_group][4]);                                     
                if ($all_nfs_check) {
                    $result[$current_group][5] = str_replace("{checked}","checked",$result[$current_group][5]); 
                } else 
                    $result[$current_group][5] = str_replace("{checked}","",$result[$current_group][5]);                                     
                if ($all_afp_check) {
                    $result[$current_group][6] = str_replace("{checked}","checked",$result[$current_group][6]); 
                } else 
                    $result[$current_group][6] = str_replace("{checked}","",$result[$current_group][6]);                                     
                if ($any_read_check and !$all_read_check) {
                    $result[$current_group][2] = str_replace("{checkbox_class}","expandable_cell3",$result[$current_group][2]);
                } else
                    $result[$current_group][2] = str_replace("{checkbox_class}","expandable_cell1",$result[$current_group][2]);
                if ($any_write_check and !$all_write_check) {
                    $result[$current_group][3] = str_replace("{checkbox_class}","expandable_cell3",$result[$current_group][3]); 
                } else 
                    $result[$current_group][3] = str_replace("{checkbox_class}","expandable_cell1",$result[$current_group][3]);                     
                if ($any_smb_check and !$all_smb_check) {
                    $result[$current_group][4] = str_replace("{checkbox_class}","expandable_cell3",$result[$current_group][4]); 
                } else 
                    $result[$current_group][4] = str_replace("{checkbox_class}","expandable_cell1",$result[$current_group][4]);                     
                if ($any_nfs_check and !$all_nfs_check) {
                    $result[$current_group][5] = str_replace("{checkbox_class}","expandable_cell3",$result[$current_group][5]); 
                } else 
                    $result[$current_group][5] = str_replace("{checkbox_class}","expandable_cell1",$result[$current_group][5]);                     
                if ($any_afp_check and !$all_afp_check) {
                    $result[$current_group][6] = str_replace("{checkbox_class}","expandable_cell3",$result[$current_group][6]); 
                } else 
                    $result[$current_group][6] = str_replace("{checkbox_class}","expandable_cell1",$result[$current_group][6]);                     
            }
        }

        $res = array();
        foreach ($result as $item)
            $res[count($res)] = implode("#",$item);

        return implode("|",$res);
    }
}
?>