<?php
/**
 * Описывает учетную запись пользователя POSIX
 *
 * Данные пользователя разбиты по следующим разделам:
 *
 * name - Имя пользователя
 *
 * - Основное
 *
 * gecos - Полное имя пользователя
 * home_dir - Домашний каталог
 * shell - Используемая оболочка
 * first_name - Имя и отчество
 * surname - Фамилия
 * uid - идентификатор пользователя
 * gid - группа пользователя
 * password - пароль пользователя (два раза)
 *
 * - Дополнительно
 *
 * mailAddresses - почтовые ящики пользователя, разделенные запятой
 * mailToaddresses - почтовые ящики для перенаправления
 * sambaHomePath - путь к общей домашней папке
 * sambaHomeDrive - буква домашнего диска пользователя
 * sambaLogonScript - скрипт для входа пользователя в систему (путь к скрипту
 *                    относительно каталога [netlogon]
 * sambaProfilePath - путь к профилю пользователя ('\\\\PDC_SRV\\profiles\\foo')
 * sambaAcctFlags - флаги учетной записи Samba ('[NDHTUMWSLKI]')
 * - Псевдонимы пользователя
 *
 * aliases - массив имен пользователей
 *
 * - Группы пользователя
 *
 * user_groups - массив групп, в которые входит пользователь
 * 
 * changed_user_groups - строка изменений в группах в формате 'имя_группы=add;имя_группы=remove...'
 * 
 * - Права доступа пользователя к общим папкам
 *
 * shares_access_rules - текущие права доступа к папкам в формате "имя-папки~путь-к-папке~rw|..."
 * changed_shares_access_rules - измененные права доступа к папкам в формате:
 *                               "имя-папки~путь-к-папке~+r-w|..."
 *
 * - Привилегии пользователя
 *
 * seMachineAccountPrivilege - добавление компьютеров в домен
 * sePrintOperatorPrivilege - управление принтерами
 * seAddUsersPrivilege - добавление пользователей и групп в домен
 * seRemoteShutdownPrivilege - удаленное завершение работы компьютера
 * seDiskOperatorPrivilege - управление дисковыми ресурсами
 * seTakeOwnershipPrivilege - становиться владельцем файлов и папок
 * 
 * @author andrey
 */
class User extends WABEntity {

    public $fileServer,$Shell,$shares_access_rules,$user_groups,$old_shares_access_rules,$old_user_groups,$app;

    function construct($params) {
        $this->module_id = array_shift($params)."_".array_shift($params);
        $this->name = implode("_",$params);
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        $this->app = $app;

        $this->skinPath = $app->skinPath;
        $this->icon = $app->skinPath."images/Tree/user.png";

        $this->gecos = "";
        $this->home_dir = "";
        $this->shell = "/bin/bash";
        $this->first_name = "";
        $this->surname = "";
        $this->uid = "";
        $this->gid = "";
        $this->password = "";
        $this->mailAddresses = "";
        $this->mailToAddresses = "";
        $this->sambaHomePath = "";
        $this->sambaHomeDrive = "H";
        $this->sambaLogonScript = "";
        $this->sambaProfilePath = "";
        $this->sambaAcctFlags = "";
        $this->aliases = "";
        $this->user_groups = "";
        $this->shares_access_rules = "";
        $this->seMachineAccountPrivilege = "";
        $this->sePrintOperatorPrivilege = "";
        $this->seAddUsersPrivilege = "";
        $this->seRemoteShutdownPrivilege = "";
        $this->seDiskOperatorPrivilege = "";
        $this->seBackupPrivilege = "";
        $this->seRestorePrivilege = "";
        $this->seTakeOwnershipPrivilege = "";
        $this->seMachineAccountPrivilege_checked = "";
        $this->sePrintOperatorPrivilege_checked = "";
        $this->seAddUsersPrivilege_checked = "";
        $this->seRemoteShutdownPrivilege_checked = "";
        $this->seDiskOperatorPrivilege_checked = "";
        $this->seBackupPrivilege_checked = "";
        $this->seRestorePrivilege_checked = "";
        $this->seTakeOwnershipPrivilege_checked = "";

        $this->organization = "";
        $this->postalAddress = "";
        $this->mobile = "";
        $this->departmentNumber = "";
        $this->postalCode = "";
        $this->homePostalAddress = "";        
        $this->mail = "";
        
        $this->homePhone = "";                
        $this->telexNumber = "";
        $this->telephoneNumber = "";
        $this->facsimileTelephoneNumber = "";
        $this->disableSsh = 1;
        $this->disableSMB = 0;
        $this->disableFTP = 1;
        $this->ftpHome = "";
        $this->ftpPassword = "";
		$this->wabUser = 0;
		$this->referenceCode = "";
        $this->update();

        $this->Shell = $Objects->get("Shell_shell");
        
        $this->fileServer = $Objects->get("FileServer_".$this->module_id."_Shares");
        if (!$this->fileServer->loaded)
            $this->fileServer->load();
        $this->domain = str_replace("dc=","",str_replace(",",".",$this->fileServer->ldap_base));            
        $app = $Objects->get($this->module_id);
        if (file_exists($app->remotePath.$this->app->rootPasswordFile)) {
            $strings = file($app->remotePath.$this->app->rootPasswordFile);
            foreach ($strings as $line) {
                if (preg_match('/echo (.*)/',$line,$matches)==1)
                    $this->root_password = trim($matches[1]);
            }
        }
        $this->skinPath = $this->app->skinPath;       
        $this->template = "templates/controller/User.html";
        $this->css = $this->app->skinPath."styles/Mailbox.css";
        $this->handler = "scripts/handlers/controller/User.js";

        $this->tabs_string = "main|Основное|".$app->skinPath."images/spacer.gif;";
        $this->tabs_string.= "advanced|Дополнительно|".$app->skinPath."images/spacer.gif;";
        $this->tabs_string.= "aliases|Псевдонимы|".$app->skinPath."images/spacer.gif;";
        $this->tabs_string.= "groups|Группы|".$app->skinPath."images/spacer.gif;";
        $this->tabs_string.= "access_rules|Права доступа|".$app->skinPath."images/spacer.gif;";
        $this->tabs_string.= "privileges|Привилегии|".$app->skinPath."images/spacer.gif;";
        $this->tabs_string.= "addresses|Адреса|".$app->skinPath."images/spacer.gif;";
        $this->tabs_string.= "phones|Телефоны|".$app->skinPath."images/spacer.gif";
        
        if ($app->mailIntegration)
            $this->tabs_string.= ";mail|Почта|".$app->skinPath."images/spacer.gif";
            
        $this->tabset_id = "WebItemTabset_".$this->module_id."_User".$this->name;
        $this->active_tab = "main";
        $this->access_rules_table = "UserAccessRulesTable_".$this->module_id."_user_".$this->name;
        $this->groups_table = "ObjectsSelectTable_".$this->module_id."_user".$this->name;
        $this->width = "680";
        $this->height = "480";
        $this->overrided = "width,height";
        $this->new_mailbox = false;
        $capp = $Objects->get($this->module_id);
        $this->mailIntegration = $capp->mailIntegration;
        $app = $Objects->get("Application");
		if (!$this->app->initiated)
			$this->app->initModules();
        if ($capp->mailIntegration and is_array($this->app->modules)) {		
            foreach ($this->app->modules as $key => $module) {
                if ($key == $capp->mailIntegration) {
                    $mailapp = $Objects->get($module["class"]);
                    $this->mailModule = $module["class"];
                }
            }
        }
        $this->new_mailbox = "true";
        
        $this->clientClass = "User";
        $this->parentClientClasses = "Entity";
        
        $this->loaded = false;
    }
    
    function findDenyUser($DenyUser) {        
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        $capp = $Objects->get($this->module_id);
        $shell = $Objects->get("Shell_shell");
        $sshdConfig = file_get_contents($capp->remotePath.$app->sshdConfigFile);
        $matches = array();
        if (preg_match("/DenyUsers (.*)$/",$sshdConfig,$matches)) {
            $denyUsers = $matches[1];
            $denyUsersList = explode(" ",str_replace("\n","",$matches[1]));
            foreach ($denyUsersList as $denyUser) {
                if ($denyUser==$DenyUser) {
                    return $matches[0];
                }
            }
        }
        return false;
    }    
    
    function getArgs() {
        global $Objects,$appconfig;
        $app = $Objects->get($this->module_id);
        $credentials = $this->getCredentials();
        if (!$this->fileServer->groups_loaded)
            $this->fileServer->loadGroups(false);
        $gids = array();$groups = array();
        foreach ($this->fileServer->groups as $group) {
            $gids[count($gids)] = $group->gidNumber;
            $groups[count($groups)] = $group->name;
        }
        $this->groups_list = implode(",",$gids)."|".implode(",",$groups);
        if ($this->disableSsh)
            $this->disableSshStr = "1";
        else
            $this->disableSshStr = "0";       
        $this->authType = @$appconfig["apacheAdminsBase"];
        if ($this->name=="")
        	$this->inetDisplay = "none";
        else
        	$this->inetDisplay = "";
        $result = parent::getArgs();
        $result["{|referenceCode}"] = $this->referenceCode;
        return $result;
    }

    function update() {
        $this->old_name = $this->name;
        $this->old_gecos = $this->gecos;
        $this->old_home_dir = $this->home_dir;
        $this->old_shell = $this->shell;
        $this->old_first_name = $this->first_name;
        $this->old_surname = $this->surname;
        $this->old_uid = $this->uid;
        $this->old_gid = $this->gid;
        $this->old_password = $this->password;
        $this->old_mailAddresses = $this->mailAddresses;
        $this->old_mailToAddresses = $this->mailToAddresses;
        $this->old_sambaHomePath = $this->sambaHomePath;
        $this->old_sambaHomeDrive = $this->sambaHomeDrive;
        $this->old_sambaLogonScript = $this->sambaLogonScript;
        $this->old_sambaProfilePath = $this->sambaProfilePath;
        $this->old_sambaAcctFlags = $this->sambaAcctFlags;
        $this->old_aliases = $this->aliases;
        $this->old_user_groups = $this->user_groups;
        $this->old_user_groups_string = $this->user_groups_string;
        $this->old_shares_access_rules = $this->shares_access_rules;
        $this->old_seMachineAccountPrivilege = $this->seMachineAccountPrivilege;
        $this->old_sePrintOperatorPrivilege = $this->sePrintOperatorPrivilege;
        $this->old_seAddUsersPrivilege = $this->seAddUsersPrivilege;
        $this->old_seRemoteShutdownPrivilege = $this->seRemoteShutdownPrivilege;
        $this->old_seDiskOperatorPrivilege = $this->seDiskOperatorPrivilege;
        $this->old_seBackupPrivilege = $this->seBackupPrivilege;
        $this->old_seRestorePrivilege = $this->seRestorePrivilege;
        $this->old_seTakeOwnershipPrivilege = $this->seTakeOwnershipPrivilege;
        $this->old_seMachineAccountPrivilege_checked = $this->seMachineAccountPrivilege_checked;
        $this->old_sePrintOperatorPrivilege_checked = $this->sePrintOperatorPrivilege_checked;
        $this->old_seAddUsersPrivilege_checked = $this->seAddUsersPrivilege_checked;
        $this->old_seRemoteShutdownPrivilege_checked = $this->seRemoteShutdownPrivilege_checked;
        $this->old_seDiskOperatorPrivilege_checked = $this->seDiskOperatorPrivilege_checked;
        $this->old_seBackupPrivilege_checked = $this->seBackupPrivilege_checked;
        $this->old_seRestorePrivilege_checked = $this->seRestorePrivilege_checked;
        $this->old_seTakeOwnershipPrivilege_checked = $this->seTakeOwnershipPrivilege_checked;
        $this->oldFtpHome = $this->ftpHome;
        $this->oldDisableSMB = $this->disableSMB;
        $this->oldDisableFTP = $this->disableFTP;
        $this->oldDisableSsh = $this->disableSsh;
        $this->oldWabUser = $this->wabUser;
        $this->old_ftpPassword = $this->ftpPassword;
    }

    function getCredentials() {
        global $Objects;
        $this->app = $Objects->get("Application");
        $app = $Objects->get($this->module_id);
        if (!$this->app->initiated)
            $this->app->initModules();
        if (file_exists($app->remotePath.$this->app->rootPasswordFile)) {
            $strings = file($app->remotePath.$this->app->rootPasswordFile);
            foreach ($strings as $line) {
                if (preg_match('/echo (.*)/',$line,$matches)==1)
                    $this->root_password = trim($matches[1]);
            }
        }
        return "root%".$this->root_password;

    }

    function load() {
        // Получаем основные и дополнительные параметры для первых двух закладок
        global $Objects;
        
        $shell = $this->Shell;
        $app = $Objects->get($this->module_id);
        $this->fileServer = $Objects->get("FileServer_".$this->module_id."_Shares");
        if (!$this->fileServer->loaded)
            $this->fileServer->load();
        
        $ftpServer = $Objects->get("FTPServer_".$this->module_id."_ftp");
        $denyUsers = $ftpServer->getDenyUsers();
        $ftpHomes = $ftpServer->getHomes();
        
        $invalidUsers = $this->fileServer->getInvalidUsers();
        if (isset($invalidUsers[$this->name]))
            $this->disableSMB = 1;
        else
            $this->disableSMB = 0;
        
        $credentials = $this->getCredentials();
        
        if ($this->name != "") {
            $arr = array("{user}" => $this->name, "{credentials}" => $credentials);
            if ($app->remoteSSHCommand!="")
                $strings_array = explode("|",$this->Shell->exec_command(strtr($app->remoteSSHCommand." '".$this->fileServer->smbGetUserInfoCommand.' 2>/dev/null~echo \|;'.$this->fileServer->smbGetUserGroupsCommand.";echo \|;".$this->fileServer->smbGetPrivilegesCommand."'",$arr)));
            else
                $strings_array = explode("|",$this->Shell->exec_command(strtr($this->fileServer->smbGetUserInfoCommand.' 2>/dev/null;echo \|;'.$this->fileServer->smbGetUserGroupsCommand.";echo \|;".$this->fileServer->smbGetPrivilegesCommand."",$arr)));
            $strings = explode("\n",$strings_array[0]);
            $params = array();
            foreach ($strings as $line) {
                $line = explode(":",$line);
                $params[@trim($line[0])] = trim(@$line[1]);
            }
            $this->uid = @$params["uidNumber"];
            $this->home_dir = @$params["homeDirectory"];
            $this->shell = @$params["loginShell"];
            $this->sambaAcctFlags = @$params["sambaAcctFlags"];
            $this->gid = @$params["gidNumber"];
            $this->gecos = htmlentities(iconv("utf8","utf8",@$params["gecos"]),ENT_QUOTES,"UTF-8");
            $this->first_name = htmlentities(iconv("utf8","utf8",@$params["givenName"]),ENT_QUOTES,"UTF-8");
            $this->surname = htmlentities(iconv("utf8","utf8",@$params["sn"]),ENT_QUOTES,"UTF-8");
            $this->sambaHomePath = @$params["sambaHomePath"];
            $this->sambaProfilePath = @$params["sambaProfilePath"];
            $this->sambaLogonScript = @$params["sambaLogonScript"];
            $this->sambaHomeDrive = @$params["sambaHomeDrive"];
            if (isset($denyUsers[$this->name]))
                $this->disableFTP = 1;
            else
                $this->disableFTP = 0;
            if (isset($ftpHomes[$this->home_dir]))
                $this->ftpHome = $ftpHomes[$this->home_dir];
            $this->mail = $this->name."@".str_replace("dc=","",str_replace(",",".",$this->fileServer->ldap_base));
            // Получаем псевдонимы пользователя
            $strings = file($app->remotePath.$this->fileServer->smbUserMapFile);
            $params = array();
            foreach ($strings as $line) {
                $line = explode("=",$line);
                $params[@trim($line[0])] = trim(str_replace('"','',str_replace(',',chr(13),@$line[1])));
            }

            $this->aliases = trim(@$params[$this->name]);
            
            // Получаем группы, в которые входит пользователь
            $user_groups = explode("\n",$strings_array[1]);
            $this->user_groups = array();
            foreach($user_groups as $group) {
                if ($group!="(null)" and $group!= "")
                    $this->user_groups[count($this->user_groups)] = $group;
            }
            $this->user_groups_string = implode(",",$this->user_groups);
                        
            $this->shares_access_rules = array();
            $shares_arr = array();
            $share_names = array();
                        
            // Получаем привилегии пользователя
            if ($this->old_name!="") {
            	$privileges = array_flip(explode("\n",$strings_array[2]));
            	$this->seMachineAccountPrivilege = @$privileges["SeMachineAccountPrivilege"];
	            $this->sePrintOperatorPrivilege = @$privileges["SePrintOperatorPrivilege"];
	            $this->seAddUsersPrivilege = @$privileges["SeAddUsersPrivilege"];
	            $this->seRemoteShutdownPrivilege = @$privileges["SeRemoteShutdownPrivilege"];
	            $this->seDiskOperatorPrivilege = @$privileges["SeDiskOperatorPrivilege"];
	            $this->seBackupPrivilege = @$privileges["SeBackupPrivilege"];
	            $this->seRestorePrivilege = @$privileges["SeRestorePrivilege"];
	            $this->seTakeOwnershipPrivilege = @$privileges["SeTakeOwnershipPrivilege"];
            }

            if ($this->seMachineAccountPrivilege) { $this->seMachineAccountPrivilege_checked="checked";};
            if ($this->sePrintOperatorPrivilege) { $this->sePrintOperatorPrivilege_checked="checked";};
            if ($this->seAddUsersPrivilege) { $this->seAddUsersPrivilege_checked="checked";};
            if ($this->seRemoteShutdownPrivilege) { $this->seRemoteShutdownPrivilege_checked="checked";};
            if ($this->seDiskOperatorPrivilege) { $this->seDiskOperatorPrivilege_checked="checked";};
            if ($this->seBackupPrivilege) { $this->seBackupPrivilege_checked="checked";};
            if ($this->seRestorePrivilege) { $this->seRestorePrivilege_checked="checked";};
            if ($this->seTakeOwnershipPrivilege) { $this->seTakeOwnershipPrivilege_checked="checked";};

            $this->update();
            
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
            
            $result = ldap_search($ds,$this->fileServer->ldap_base,"(uid=".$this->name.")");
            
            if ($result == FALSE)
                return 0;
                        
            $entries = ldap_get_entries($ds,$result);
            
            if ($entries["count"]==0)
                return 0;
            
            $this->jpegPhoto = @$entries[0]["jpegphoto"][0];
            $this->organization = @$entries[0]["o"][0];
            $this->postalAddress = @$entries[0]["postaladdress"][0];
            $this->departmentNumber = @$entries[0]["departmentnumber"][0];
            $this->postalCode = @$entries[0]["postalcode"][0];
            $this->homePostalAddress = @$entries[0]["homepostaladdress"][0];        
            $this->homePhone = @$entries[0]["homephone"][0];                
            $this->mobile = @$entries[0]["mobile"][0];                
            $this->telexNumber = @$entries[0]["telexnumber"][0];
            $this->telephoneNumber = @$entries[0]["telephonenumber"][0];
            $this->facsimileTelephoneNumber = @$entries[0]["facsimiletelephonenumber"][0];            
            $this->userPassword = $entries[0]["userpassword"][0];            
            $this->ftpPassword = @$entries[0]["ftppassword"][0];
            $this->wabUser = @$entries[0]["wabuser"][0];
            
            $this->old_jpegPhoto = $this->jpegPhoto;
            $this->old_organization = $this->organization;
            $this->old_postalAddress = $this->postalAddress;
            $this->old_departmentNumber = $this->departmentNumber;
            $this->old_postalCode = $this->postalCode;
            $this->old_homePostalAddress = $this->homePostalAddress;
            $this->old_homePhone = $this->homePhone;
            $this->old_mobile = $this->mobile;
            $this->old_telexNumber = $this->telexNumber;
            $this->old_telephoneNumber = $this->telephoneNumber;
            $this->old_facsimileTelephoneNumber = $this->facsimileTelephoneNumber;
            $this->old_ftpPassword = $this->ftpPassword;
            $this->oldWabUser = $this->wabUser;
            $this->loaded = true;
        }
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();

        $capp = $Objects->get($this->module_id);
        $this->mailIntegration = $capp->mailIntegration;
        $this->new_mailbox = "true";
        // Запись информации о почтовом ящике
        if ($capp->mailIntegration) {
            foreach ($app->modules as $key=>$module) {
                if ($key == $capp->mailIntegration) {
                    $mailapp = $Objects->get($module["class"]);
                    $this->mailModuleId = $module["class"];
                }
            }
            if (isset($mailapp) and file_exists($mailapp->remotePath."etc/hostname")) {
                $mailbox = $Objects->get("Mailbox_".$mailapp->id."_".$this->name."_".$this->domain);
                $mailbox->load();
                if ($mailbox->loaded)
                    $this->new_mailbox = "false";
            }
        }
        
        if ($this->findDenyUser($this->name)!=false) {
            $this->disableSsh = true;
        } else {
            if ($this->loaded)
                $this->disableSsh = false;
        }
        $this->oldDisableSsh = $this->disableSsh;
        $this->oldFolders = $ftpServer->getFolders();
        $args = array();
        $args["window_id"] = $this->window_id;
        $args["opener_item"] = $this->getId();
        $args["selected_items"] = $this->user_groups_string;
        $this->groupsFrameSrc = "index.php?object_id=ObjectsSelectTable_".$this->module_id."_user".$this->name."&hook=4&arguments=".urlencode(json_encode($args));
        
		$app = $Objects->get($this->module_id);
        if ($this->loaded and is_object($app->docFlowApp)) {
        	$this->adapter = $Objects->get("DocFlowDataAdapter_".$app->docFlowApp->getId()."_1");
        	$query = "SELECT entities FROM fields WHERE @objectId='".$this->getId()."' AND @classname='ReferenceUserInfoCard'";
        	$res = PDODataAdapter::makeQuery($query, $this->adapter,$app->docFlowApp->getId());
        	if (count($res)>0) {
        		$card = current($res);
        		$this->referenceCode = "<input type='button' fileid='".$card->getId()."' id='referenceButton' value='Открыть описание'/>";
        	} else {
        		$this->referenceCode = "<input type='button' fileid='ReferenceUserInfoCard_".$app->docFlowApp->getId()."_' id='referenceButton' value='Открыть описание'/>";
        	}
        }        
    }
    
    function getAccessRules() {
        global $Objects;
        $this->shares_access_rules = array();
        $shares_arr = array();
        $share_names = array();
        if (!$this->fileServer->sharesLoaded)
            $this->fileServer->loadShares();
        foreach($this->fileServer->shares as $share) {
            if ($share->name=="root")
                $path = $share->path;
            else
                $path = $this->fileServer->shares_root."/".$share->path;
            $shares_arr[] = $path;
            $share_names[] = $share->name;
        }
        $app = $Objects->get($this->module_id);
        $change_arr = array("{user_or_group}" => "user", "{user}" => $this->name, "{shares_list}" => implode("~",$shares_arr));
        if ($app->remoteSSHCommand!="")
            $rights = shell_exec(strtr($app->remoteSSHCommand." \"".$this->fileServer->smbGetUserACLCommand."\"",$change_arr));
        else
            $rights = $this->Shell->exec_command(strtr($this->fileServer->smbGetUserACLCommand,$change_arr));
        if ($rights!="") {
            $i=0;
            $rights_array = explode("\n",$rights);
            foreach ($rights_array as $right) {
                $right_parts = explode("~",$right);
                $rule = str_replace("-","",substr(@$right_parts[1], 0, 2));
                @$this->shares_access_rules[$share_names[$i]] = $right_parts[0]."~".$rule;
                $i++;
            }
        }
    }

    function containsUser($user) {
        global $Objects;
        $app = $Objects->get($this->module_id);
        $credentials = $this->getCredentials();
        $shell = $Objects->get("Shell_shell");
        $users = array_flip(explode("\n",$shell->exec_command(str_replace("{credentials}",$credentials,$app->remoteSSHCommand." ".$this->fileServer->smbListUsersCommand))));
        return isset($users[$user]);        
    }

    function remove() {
        global $Objects,$appconfig;
        $ftpServer = $Objects->get("FTPServer_".$this->module_id."_ftp");
        $oldFolders = $ftpServer->getFolders();
        $app =  $Objects->get("Application");
        if (!$app->initiated)
        	$app->initModules();
        $app->raiseRemoteEvent("USER_DELETED","object_id="."User_".$this->module_id."_".$this->name);
        
        if ($this->name=="guest")
            return 0;
        
        if (!$this->loaded)
            $this->load();
            
        $this->disableFTP = true;
        $this->save();
        
        if (!$this->fileServer->loaded)
            $this->fileServer->load();

		$hosts = $ftpServer->getHosts();
		$resultHosts = array();
		foreach ($hosts as $host) {
			if (@$host["anonymousUser"]==$this->name) {
				$this->reportError("Данный пользователь используется виртуальным FTP-хостом в качестве анонимного!","remove");
				return 0;
			}
			$arr = explode("~",$host["userList"]);
			$resultArr = array();
			foreach($arr as $value) {
				if ($value!=$this->name)
					$resultArr[] = $value;
			}
			$host["userList"] = implode("~",$resultArr);
			$arr = explode("|",$host["userTransferRates"]);
			$resultArr = array();
			foreach($arr as $value) {
				$parts = explode("~",$value);
				if ($parts[0]!=$this->name)
					$resultArr[] = $value;
			}
			$host["userTransferRates"] = implode("|",$resultArr);
			$resultHosts[$host["ServerName"]] = $host;
		}

        $ftpServer->setHosts($resultHosts);

        $this->getAccessRules();
        
        $capp = $Objects->get($this->module_id);
        $shell = $Objects->get("Shell_shell");
        
        foreach($this->shares_access_rules as $key => $rule) {
            $rule_parts = explode("~",$rule);
            $acl = "user:".$this->name.":";
            $arr = array("{share}" => $rule_parts[0], "{acl}" => $acl);
            if ($key =="root")
                $arr["-R"] = "";
            $arr["{acl}"] = str_replace(":x",":",$arr["{acl}"]);      
            if ($capp->remoteSSHCommand=="")
                $shell->exec_command(strtr($this->fileServer->smbRemoveACLCommand,$arr));
            else
                shell_exec(strtr($capp->remoteSSHCommand." \"".$this->fileServer->smbRemoveACLCommand."\"",$arr));
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
                
        $result = @ldap_search($ds,$this->fileServer->ldap_base,"(&(objectClass=dhcpHost)(defaultUser=".$this->uid."))");
        $entries = ldap_get_entries($ds, $result);
        if ($entries["count"]>0) {
            foreach($entries as $entry) {
                $dn = explode(",",str_replace("cn=","",$entry["dn"]));
                if ($entry["dn"]=="")
                    continue;
                $host = $Objects->get("DhcpHost_".$this->module_id."_".$dn[1]."_".$dn[0]);
                if (!$host->loaded)
                    $host->load();
                $host->defaultUser="0";
                $host->save(true);
            }
        }
        
        $denyUsers = $ftpServer->getDenyUsers();
        if (isset($denyUsers[$this->name])) {
            unset($denyUsers[$this->name]);
            $ftpServer->setDenyUsers($denyUsers);
        }
        $ftpHomes = $ftpServer->getHomes();
        if (isset($ftpHomes[$this->home_dir])) {
            unset($ftpHomes[$this->home_dir]);
            $ftpServer->setHomes($ftpHomes);
        }        
        $ftpServer->setFolders($ftpServer->getFolders(),$oldFolders);
        
        $davServer = $Objects->get("DAVServer_".$this->module_id."_dav");
        $davFolders = $davServer->getFolders();
        if (isset($davFolders[$this->name])) {
            unset($davFolders[$this->name]);
            $davServer->setFolders($davFolders);
        }
                        
        if (@$appconfig["apacheAdminsBase"]=="ldap") {
        	$apacheUsers = $Objects->get("ApacheUsers_".$this->module_id);
        	$apacheUsers->remove($this->name);
        }
        
        $credentials = $this->getCredentials();
        $arr = array("{user}" => $this->name, "{credentials}" => $credentials, "{home_dir}" => $this->home_dir);
        if ($capp->remoteSSHCommand=="") {
            $this->Shell->exec_command(strtr($this->fileServer->smbRemoveUserCommand,$arr));
        }
        else
            shell_exec(strtr($capp->remoteSSHCommand." \"".$this->fileServer->smbRemoveUserCommand."\"",$arr));
        
        $strings = file($capp->remotePath.$this->fileServer->smbUserMapFile);
        $params = array();
        foreach ($strings as $line) {
            $line = explode("=",$line);
            if (trim(@$line[0])!=$this->old_name)
                $params[trim(@$line[0])] = @$line[1];
        }
        $fp = fopen($capp->remotePath.$this->fileServer->smbUserMapFile,"w");
        foreach($params as $value)
            fwrite($fp,$value);
        $Objects->remove("User_".$this->module_id."_".$this->name);
        $capp = $Objects->get($this->module_id);
        // Запись информации о почтовом ящике
        if ($capp->mailIntegration) {
            foreach ($app->modules as $key=>$module) {
                if ($key == $capp->mailIntegration) {
                    $mailapp = $Objects->get($module["class"]);
                }
            }
            if (isset($mailapp) and (file_exists($mailapp->remotePath."etc/hostname"))) {
                $mboxes = $Objects->get("Mailboxes_".$mailapp->id);
                $mboxes->remove($this->name,$this->domain);
            }
        }
        $denyUser = $this->findDenyUser($this->name);
        if ($denyUser) {
            $arr = explode(" ",$denyUser);
            $arr2 = array();
            for ($i=0;$i<count($arr);$i++) {
                if ($arr[$i]!=$this->name)
                    $arr2[] = $arr[$i];                            
            }
            if (count($arr2)>1)
                $newDenyUser = implode(" ",$arr2);                    
            else
                $newDenyUser = "";
            file_put_contents($capp->remotePath.$app->sshdConfigFile,str_replace($denyUser,$newDenyUser,file_get_contents($capp->remotePath.$app->sshdConfigFile)));                        
        }
        
        if (is_object($capp->docFlowApp)) {
        	$adapter = $Objects->get("DocFlowDataAdapter_".$capp->docFlowApp->getId()."_1");
        	if (!$adapter->connected)
        		$adapter->connect();
        	if ($adapter->connected) {
        		$entities = PDODataAdapter::makeQuery("SELECT entities FROM fields WHERE @objectId='".$this->getId()."' AND @classname='ReferenceUserInfoCard'",$adapter,$capp->docFlowApp->getId());
        		foreach ($entities as $entity) {
        			$entity->loaded = false;
        			$entity->load();
        			$entity->deleted = 1;
        			$entity->save(true);
        		}
        	}
        }
        
        $shell->exec_command($capp->remoteSSHCommand." ".$app->sshdRestartCommand);       
        
        $ftpServer->restart();
        $davServer->restart();
    }

    function save($arguments=null) {
        $update_command = "";
        if (isset($arguments)) {
        	$this->load();
        	$this->setArguments($arguments);
        }
        global $Objects,$appconfig;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        $shell = $Objects->get("Shell_shell");
        $capp = $Objects->get($this->module_id);
        $credentials = $this->getCredentials();
        
        $ftpServer = $Objects->get("FTPServer_".$this->module_id."_ftp");        
        // Проверяем имя пользователя
        if ($this->name != $this->old_name) {
            if ($this->old_name=="guest") {
                $this->reportError("Системного пользователя guest запрещено переименовывать !","save");
                return 0;
            }
            if ($this->containsUser($this->name)) {
                $this->reportError("Пользователь с именем ".$this->name." уже существует !","save");
                return 0;
            }
        }
        
        if (!$this->disableFTP) {
            if (!file_exists($capp->remotePath.$this->ftpHome)) {
                $this->reportError("Указанная домашняя папка FTP не существует !","save");
                return 0;
			}
        }
        
        if ($this->gid != $this->old_gid)
        	$update_command .= " -g ".$this->gid;
        
        if ($this->gecos != $this->old_gecos)
                $update_command .= " -c '".$this->gecos."'";

        if ($this->shell != $this->old_shell) {
            if (!file_exists($capp->remotePath.$this->shell)) {
                $this->reportError("Указанная оболочка не найдена!","save");
                return 0;
            }
            $update_command .= " -s '".$this->shell."'";
        }
        if ($this->first_name=="")
        	$this->first_name = " ";
        if ($this->surname=="")
        	$this->surname = " ";
        
        if ($this->first_name != $this->old_first_name)
            $update_command .= " -N '".iconv("utf8","utf8",$this->first_name)."'";
        if ($this->surname != $this->old_surname)
            $update_command .= " -S '".iconv("utf8","utf8",$this->surname)."'";
        if ($this->sambaHomePath != $this->old_sambaHomePath)
            $update_command .= " -C '".$this->sambaHomePath."'";
        if ($this->sambaHomeDrive != $this->old_sambaHomeDrive)
            $update_command .= " -D '".$this->sambaHomeDrive."'";
        if ($this->sambaLogonScript != $this->old_sambaLogonScript)
            $update_command .= " -E '".$this->sambaLogonScript."'";
        if ($this->sambaProfilePath != $this->old_sambaProfilePath)
            $update_command .= " -F '".$this->sambaProfilePath."'";
        if ($this->user_groups_string != $this->old_user_groups_string)
            $update_command .= " -G '".$this->user_groups_string."'";

        if ($appconfig["apacheAdminsBase"]=="ldap" and ($this->name!=$this->old_name)) {
        	$apacheUser = $Objects->get("ApacheUser_".$this->module_id."_".$this->old_name);
        	$apacheUser->load();
        	$apacheUser->name = $this->name;
        	$apacheUser->save();
        }
        
        if ($appconfig["apacheAdminsBase"]=="ldap" and ($this->oldWabUser==1 and $this->wabUser==0)) {
        	$apacheUsers = $Objects->get("ApacheUsers_".$this->module_id);
        	$apacheUsers->remove($this->old_name);
        }
        
        // Запись информации о почтовом ящике
        if ($capp->mailIntegration) {
            foreach ($app->modules as $key=>$module) {
                if ($key == $capp->mailIntegration) {
                    $mailapp = $Objects->get($module["class"]);
                }
            }
            if (isset($mailapp) and file_exists($mailapp->remotePath."etc/hostname")) {
                $mbox = $Objects->get("Mailbox_".$mailapp->id."_".$this->old_name."_".$this->domain);
                $mbox->load();
                $res = $mbox->usedBy();
                if (is_object($res)) {
                    $this->reportError("Данные почтового ящика ".$this->old_name."@".$this->domain." в данный момент редактируются в другом окне пользователем ".$res->name,"save");
                    return 0;
                }
                $mbox->name = $this->name;
                $mbox->domain = $this->domain;
                if ($this->password!=$this->old_password)
                    $mbox->password = crypt($this->password);
                else
                    $mbox->password = $this->userPassword;
                $mbox->save();
            }
        }
        
        // обновляем информацию о пользователе (закладки "Основное" и "Дополнительно" и "Группы")
        $arr = array("{user}" => $this->name, "{credentials}" => $credentials, "{password}" => $this->password);
        if ($this->password!=$this->old_password and $this->old_name != "") {
			if ($capp->remoteSSHCommand!="")
            	shell_exec(strtr($capp->remoteSSHCommand." '".$this->fileServer->smbChangeUserPasswordCommand."'",$arr));
			else {
				$shell->exec_command(strtr($this->fileServer->smbChangeUserPasswordCommand,$arr));
			}
        }
        
        // если прошлого имени не было, значит пользователь новый и нужно создать его
        if ($this->old_name=="") {
            if ($capp->remoteSSHCommand!="") {
                shell_exec(strtr($capp->remoteSSHCommand." '".$this->fileServer->smbAddUserCommand."'",$arr));
                shell_exec($capp->remoteSSHCommand." ".$this->app->chownCommand." -R ".$this->name.":".$this->gid." /home/".$this->name);
            } else {
                $shell->exec_command(strtr($this->fileServer->smbAddUserCommand,$arr));
                $shell->exec_command($this->app->chownCommand." -R ".$this->name.":".$this->gid." /home/".$this->name);
            }
			$app->raiseRemoteEvent("USER_ADDED","object_id=".$this->getId());
            $this->old_name = $this->name;
            $this->is_new = true;
        } else {
			$app->raiseRemoteEvent("USER_CHANGED","object_id=".$this->getId());
        	// иначе добавим переименование пользователя к команде обновления информации о пользователе
            $update_command .= " -r ".$this->name;
            
        }
        
        $denyUsers = $ftpServer->getDenyUsers();
        $ftpChanged = false;
        $davServer = $Objects->get("DAVServer_".$this->module_id."_dav");
        $davFolders = $davServer->getFolders();
        if ($this->name != $this->old_name and $this->old_name!="") {
            $denyUser = $this->findDenyUser($this->old_name);
            if ($denyUser) {
                $arr = explode(" ",$denyUser);
                $arr2 = array();
                for ($i=0;$i<count($arr);$i++) {
                    if ($arr[$i]!=$this->old_name)
                        $arr2[] = $arr[$i];                            
                }
                if (count($arr2)>1)
                    $newDenyUser = implode(" ",$arr2);                    
                else
                    $newDenyUser = "";
                file_put_contents($capp->remotePath.$app->sshdConfigFile,str_replace($denyUser,$newDenyUser,file_get_contents($capp->remotePath.$app->sshdConfigFile)));
                if (isset($denyUsers[$this->old_name])) {
                    $ftpChanged = true;
                    unset($denyUsers[$this->old_name]);
                }               
            }            
            if (isset($davFolders[$this->old_name])) {
            	$davFolders[$this->name] = $davFolders[$this->old_name];
	            unset($davFolders[$this->old_name]);
            }
        }
         
        if ($this->disableFTP)
            $denyUsers[$this->name] = $this->name;
            
        if ($this->disableFTP!=$this->oldDisableFTP) {
            $ftpChanged = true;
            if ($this->disableFTP) {
                $denyUsers[$this->name] = $this->name;
                unset($davFolders[$this->name]);
			}
            if (!$this->disableFTP) {
                unset($denyUsers[$this->name]);
                $davFolders[$this->name] = $this->ftpHome;
			}
        }
		$davFolders[$this->name] = $this->ftpHome;
		if ($this->oldFolders == "")
			$this->oldFolders = $ftpServer->getFolders();        
			
		$ftpServer->setDenyUsers($denyUsers);
        
        $ftpHomes = $ftpServer->getHomes();
        if ($this->home_dir != $this->old_home_dir and isset($ftpHomes[$this->old_home_dir])) {
            $ftpChanged = true;
            $ftpHomes[$this->home_dir] = $ftpHomes[$this->old_home_dir];            
            unset($ftpHomes[$this->old_home_dir]);
        }
        if ($this->ftpHome!=$this->oldFtpHome) {
            $ftpChanged = true;
            if ($this->ftpHome!="") {
                $ftpHomes[$this->home_dir] = $this->ftpHome;
				$davFolders[$this->name] = $this->ftpHome;
			}
            else {
                if (isset($ftpHomes[$this->home_dir]))
                    unset($ftpHomes[$this->home_dir]);
				unset($davFolders[$this->name]);
            }
        }
        if ($this->disableFTP) {
            unset($ftpHomes[$this->home_dir]);
			unset($davFolders[$this->name]);
        }
        $ftpServer->unsetFolders($this->oldFolders);
        $ftpServer->setHomes($ftpHomes);
        $ftpServer->setFolders($ftpServer->getFolders(),$this->oldFolders);
        $ftpServer->getHomes();
        $ftpServer->setHomes($ftpHomes);        
        $ftpServer->setFolders($ftpServer->getFolders(),$this->oldFolders);
        $davServer->setFolders($davFolders);
        if ($this->home_dir != $this->old_home_dir) {
            if (!file_exists($capp->remotePath.$this->old_home_dir))
                $shell->exec_command($capp->remoteSSHCommand." ".$app->makeDirCommand." ".$this->home_dir);
            else
                $shell->exec_command($capp->remoteSSHCommand." ".$app->moveDirCommand." ".$this->old_home_dir." ".$this->home_dir);
            if (!file_exists($this->home_dir))
                $shell->exec_command($capp->remoteSSHCommand." ".$app->makeDirCommand." ".$this->home_dir);
            $update_command .= " -d ".$this->home_dir;
        }
        $update_command .= " ".$this->old_name;

        if ($capp->remoteSSHCommand!="")
            shell_exec(str_replace("{params}",$update_command,$capp->remoteSSHCommand." ".'"'.$this->fileServer->smbChangeUserOptionsCommand.'"'));        
        else
            $shell->exec_command(str_replace("{params}",$update_command,$this->fileServer->smbChangeUserOptionsCommand));
            
        // Обновляем псевдонимы
        $aliases_arr = explode("\n",$this->aliases);
        $result_aliases = array();
        foreach ($aliases_arr as $alias)
            if ($alias!="")
                $result_aliases[count($result_aliases)] = '"'.$alias.'"';
        $result_aliases = implode(",",$result_aliases);

        $strings = file($capp->remotePath.$this->fileServer->smbUserMapFile);
        $params = array();
        foreach ($strings as $line) {
            $line = explode("=",$line);
            if (trim(@$line[0])!=$this->old_name)
                $params[trim(@$line[0])] = str_replace("\n",'',@$line[1]);
        }
        if ($result_aliases!="")
            $params[$this->name] = $result_aliases;
        $fp = fopen($capp->remotePath.$this->fileServer->smbUserMapFile,"w");
        foreach($params as $key => $value)
            fwrite($fp,$key."=".$value."\n");

        // Записываем привилегии пользователя
        $revoke_arr = array(); $grant_arr = array();
        
        if ($this->seMachineAccountPrivilege != $this->old_seMachineAccountPrivilege) {
            if ($this->seMachineAccountPrivilege == 'true')
                $grant_arr[count($grant_arr)] = "SeMachineAccountPrivilege";
            else
              $revoke_arr[count($revoke_arr)] = "SeMachineAccountPrivilege";
        };
        if ($this->sePrintOperatorPrivilege != $this->old_sePrintOperatorPrivilege) {
            if ($this->sePrintOperatorPrivilege == 'true')
                $grant_arr[count($grant_arr)] = "SePrintOperatorPrivilege";
            else
              $revoke_arr[count($revoke_arr)] = "SePrintOperatorPrivilege";
        };
        if ($this->seAddUsersPrivilege != $this->old_seAddUsersPrivilege) {
            if ($this->seAddUsersPrivilege == 'true')
                $grant_arr[count($grant_arr)] = "SeAddUsersPrivilege";
            else
                $revoke_arr[count($revoke_arr)] = "SeAddUsersPrivilege";
        };
        if ($this->seRemoteShutdownPrivilege != $this->old_seRemoteShutdownPrivilege) {
            if ($this->seRemoteShutdownPrivilege == 'true')
                $grant_arr[count($grant_arr)] = "SeRemoteShutdownPrivilege";
            else
                $revoke_arr[count($revoke_arr)] = "SeRemoteShutdownPrivilege";
        };
        if ($this->seDiskOperatorPrivilege != $this->old_seDiskOperatorPrivilege) {
            if ($this->seDiskOperatorPrivilege == 'true')
                $grant_arr[count($grant_arr)] = "SeDiskOperatorPrivilege";
            else
                $revoke_arr[count($revoke_arr)] = "SeDiskOperatorPrivilege";
        };
        if ($this->seBackupPrivilege != $this->old_seBackupPrivilege) {
            if ($this->seBackupPrivilege == 'true')
                $grant_arr[count($grant_arr)] = "SeBackupPrivilege";
            else
                $revoke_arr[count($revoke_arr)] = "SeBackupPrivilege";
        };
        if ($this->seRestorePrivilege != $this->old_seRestorePrivilege) {
            if ($this->seRestorePrivilege == 'true')
                $grant_arr[count($grant_arr)] = "SeRestorePrivilege";
            else
                $revoke_arr[count($revoke_arr)] = "SeRestorePrivilege";
        };
        if ($this->seTakeOwnershipPrivilege != $this->old_seTakeOwnershipPrivilege) {
            if ($this->seTakeOwnershipPrivilege == 'true')
                $grant_arr[count($grant_arr)] = "SeTakeOwnershipPrivilege";
            else
                $revoke_arr[count($revoke_arr)] = "SeTakeOwnershipPrivilege";
        };
        $grant_arr = implode(" ",$grant_arr);
        $revoke_arr = implode(" ",$revoke_arr);
        $arr = array("{user}" => $this->name, "{credentials}" => $credentials, "{privileges}" => $grant_arr);
        $shell->exec_command(strtr($capp->remoteSSHCommand." ".$this->fileServer->smbAddPrivilegesCommand,$arr));
        $arr = array("{user}" => $this->name, "{credentials}" => $credentials, "{privileges}" => $revoke_arr);
        $shell->exec_command(strtr($capp->remoteSSHCommand." ".$this->fileServer->smbRemovePrivilegesCommand,$arr));
        
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
        $this->mail = $this->name."@".$this->domain;
        
		if ($this->name!=$this->old_name and $this->old_name!="") {
			$result = @ldap_search($ds,$this->fileServer->ldap_base,"(&(objectClass=dhcpHost)(defaultUser=".$this->uid."))");
			$entries = ldap_get_entries($ds, $result);
			if ($entries["count"]>0) {
				foreach($entries as $entry) {
					$dn = explode(",",str_replace("cn=","",$entry["dn"]));
					if ($entry["dn"]=="")
						continue;
					$host = $Objects->get("DhcpHost_".$this->module_id."_".$dn[1]."_".$dn[0]);
					if (!$host->loaded)
						$host->load();
					$host->save(true);
				}
			}				
		}
        
        $entry = array();
        if ($this->organization!="")
            $entry["o"] = $this->organization;
        else {
            if ($this->old_organization!="") {
                $del = array();
                $del["o"] = $this->old_organization;
                @ldap_mod_del($ds,"uid=".$this->name.",".$this->fileServer->ldap_user_suffix.",".$this->fileServer->ldap_base,$del);
            }
        }
        if ($this->postalAddress!="")
            $entry["postaladdress"] = $this->postalAddress;
        else {
            if ($this->old_postalAddress!="") {
                $del = array();
                $del["postalAddress"] = $this->old_postalAddress;
                @ldap_mod_del($ds,"uid=".$this->name.",".$this->fileServer->ldap_user_suffix.",".$this->fileServer->ldap_base,$del);
            }
        }
        if ($this->departmentNumber!="")
            $entry["departmentnumber"] = $this->departmentNumber;
        else {
            if ($this->old_departmentNumber!="") {
                $del = array();
                $del["departmentnumber"] = $this->old_departmentNumber;
                @ldap_mod_del($ds,"uid=".$this->name.",".$this->fileServer->ldap_user_suffix.",".$this->fileServer->ldap_base,$del);
            }
        }
        if ($this->postalCode)
            $entry["postalcode"] = $this->postalCode;
        else {
            if ($this->old_postalCode!="") {
                $del = array();
                $del["postalcode"] = $this->old_postalCode;
                @ldap_mod_del($ds,"uid=".$this->name.",".$this->fileServer->ldap_user_suffix.",".$this->fileServer->ldap_base,$del);
            }
        }
        if ($this->homePostalAddress)
            $entry["homepostaladdress"] = $this->homePostalAddress;
        else {
            if ($this->old_homePostalAddress!="") {
                $del = array();
                $del["homepostaladdress"] = $this->old_homePostalAddress;
                @ldap_mod_del($ds,"uid=".$this->name.",".$this->fileServer->ldap_user_suffix.",".$this->fileServer->ldap_base,$del);
            }
        }
        if ($this->mail)
            $entry["mail"] = $this->mail;
        else {
            if ($this->old_mail!="") {
                $del = array();
                $del["mail"] = $this->old_mail;
                @ldap_mod_del($ds,"uid=".$this->name.",".$this->fileServer->ldap_user_suffix.",".$this->fileServer->ldap_base,$del);
            }
        }
        if ($this->homePhone!="")
            $entry["homephone"] = $this->homePhone;
        else {
            if ($this->old_homePhone!="") {
                $del = array();
                $del["homephone"] = $this->old_homePhone;
                @ldap_mod_del($ds,"uid=".$this->name.",".$this->fileServer->ldap_user_suffix.",".$this->fileServer->ldap_base,$del);
            }
        }
        if ($this->mobile!="")
            $entry["mobile"] = $this->mobile;
        else {
            if ($this->old_mobile!="") {
                $del = array();
                $del["mobile"] = $this->old_mobile;
                @ldap_mod_del($ds,"uid=".$this->name.",".$this->fileServer->ldap_user_suffix.",".$this->fileServer->ldap_base,$del);
            }
        }
        if ($this->telephoneNumber!="")
            $entry["telephonenumber"] = $this->telephoneNumber;
        else {
            if ($this->old_telephoneNumber!="") {
                $del = array();
                $del["telephonenumber"] = $this->old_telephoneNumber;
                @ldap_mod_del($ds,"uid=".$this->name.",".$this->fileServer->ldap_user_suffix.",".$this->fileServer->ldap_base,$del);
            }
        }
        if ($this->facsimileTelephoneNumber!="")
            $entry["facsimiletelephonenumber"] = $this->facsimileTelephoneNumber;
        else {
            if ($this->old_facsimileTelephoneNumber!="") {
                $del = array();
                $del["facsimiletelephonenumber"] = $this->old_facsimileTelephoneNumber;
                @ldap_mod_del($ds,"uid=".$this->name.",".$this->fileServer->ldap_user_suffix.",".$this->fileServer->ldap_base,$del);
            }
        }
        if ($this->wabUser!="")
            $entry["wabuser"] = $this->wabUser;
        else {
            if ($this->oldWabUser!="") {
                $del = array();
                $del["wabuser"] = $this->oldWabUser;
                @ldap_mod_del($ds,"uid=".$this->name.",".$this->fileServer->ldap_user_suffix.",".$this->fileServer->ldap_base,$del);
            }
        }
        if ($this->password!=$this->old_password and $this->old_name != "") {
	        if ($this->password!="")
    	        $entry["ftppassword"] = $this->password;
        	else {
            	if ($this->old_ftppassword!="") {
                	$del = array();
                	$del["ftppassword"] = $this->old_password;
                	@ldap_mod_del($ds,"uid=".$this->name.",".$this->fileServer->ldap_user_suffix.",".$this->fileServer->ldap_base,$del);
            	}
        	}
		}

		$old_gid = $this->old_gid;
		$old_name = $this->old_name;

        @ldap_modify($ds,"uid=".$this->name.",".$this->fileServer->ldap_user_suffix.",".$this->fileServer->ldap_base,$entry);
        
        if (ldap_error($ds)!="Success") {
        	$this->reportError("Произошла ошибка при сохранении данных! Проверьте правильность ввода: '".ldap_error($ds)."'","save");
        	return 0;
        }
        
        if ($this->disableSsh==0) {                
            $denyUser = $this->findDenyUser($this->old_name);
            if ($denyUser) {
                $arr = explode(" ",$denyUser);
                $arr2 = array();
                for ($i=0;$i<count($arr);$i++) {
                    if ($arr[$i]!=$this->old_name)
                        $arr2[] = $arr[$i];                            
                }
                if (count($arr2)>1)
                    $newDenyUser = implode(" ",$arr2);                    
                else
                    $newDenyUser = "";
                file_put_contents($capp->remotePath.$app->sshdConfigFile,str_replace($denyUser,$newDenyUser,file_get_contents($capp->remotePath.$app->sshdConfigFile)));                        
            }
        } else {
            if ($this->disableSsh != $this->oldDisableSsh or $this->is_new or $this->name!=$this->old_name) {
                $sshdConfig = file_get_contents($capp->remotePath.$app->sshdConfigFile);
                $matches = array();
                if (preg_match("/DenyUsers (.*)$/",$sshdConfig,$matches)) {
                    $denyUser = $matches[0];
                    $arr = explode(" ",$matches[0]);
                    array_shift($arr);
                    $key = array_search($this->old_name, $arr);
                    if ($key!==FALSE) {
                    	unset($arr[$key]);
                    }
                    $arr[] = $this->name;
                    $arr = array_unique($arr);
                    $arr = implode(" ",$arr);
                    $newDenyUser = "DenyUsers ".$arr;
                    file_put_contents($capp->remotePath.$app->sshdConfigFile,str_replace($denyUser,$newDenyUser,file_get_contents($capp->remotePath.$app->sshdConfigFile)));             
                } else {
                    file_put_contents($capp->remotePath.$app->sshdConfigFile,file_get_contents($capp->remotePath.$app->sshdConfigFile)."\nDenyUsers ".$this->name);
                }                    
            }
        }
        $this->update();
        if ($capp->remoteSSHCommand=="") {
            $shell->exec_command($app->sshdRestartCommand);
        }
        else {
            $cmd = " \"";            
            $cmd .= $app->sshdRestartCommand.";";
            $cmd .= "\"";
            shell_exec($capp->remoteSSHCommand." 'echo ".$cmd." | at now'");            
        }                    
        if ($ftpChanged) {
            $ftpServer->restart();
            $davServer->restart();
        }
        
        $invalidUsers = $this->fileServer->getInvalidUsers();
        $invalidsChanged = false;
        if ($this->disableSMB) {
            if (!isset($invalidUsers[$this->name])) {
                $invalidUsers[$this->name] = $this->name;
                $invalidsChanged = true;
            }
        }
        else {
            if (isset($invalidUsers[$this->name])) {
                unset($invalidUsers[$this->name]);
                $invalidsChanged = true;
            }
        }
        if ($invalidsChanged) {
            $this->fileServer->setInvalidUsers($invalidUsers);
            if ($capp->remoteSSHCommand=="") {
                if ($this->fileServer->smbAutoRestart) {
                    $shell->exec_command($this->fileServer->smbReloadCommand);
                    $shell->exec_command($this->fileServer->nfsRestartCommand);
                }
            } else {
                $cmd = " \"";
                if ($this->fileServer->smbAutoRestart)
                    $cmd .= $this->fileServer->smbReloadCommand.";".$this->fileServer->nfsRestartCommand.";";
                    $cmd .= "\"";
                    shell_exec($capp->remoteSSHCommand." 'echo ".$cmd." | at now'");            
            }
        }

		if ($this->gid!=$old_gid or $this->name!=$old_name) {
			$ftpServer = $Objects->get("FTPServer_".$this->module_id."_ftp");
			$hosts = $ftpServer->getHosts();
			$resultHosts = array();
			foreach ($hosts as $host) {
				$arr = explode("~",$host["userList"]);
				for ($i=0;$i<count($arr);$i++) {
					if ($arr[$i]==$old_name)
						$arr[$i] = $this->name;
				}
				$host["userList"] = implode("~",$arr);
				$arr = explode("|",$host["userTransferRates"]);
				for ($i=0;$i<count($arr);$i++) {
					$parts = explode("~",$arr[$i]);
					if ($parts[0]==$old_name)
						$arr[$i] = $this->name."~".$parts[1]."~".$parts[2];
				}
				$host["userTransferRates"] = implode("|",$arr);
				if (@$host["anonymousUser"]==$old_name)
					@$host["anonymousUser"] = $this->name;
				$resultHosts[@$host["ServerName"]] = $host;
			}
			$ftpServer->setHosts($resultHosts);
			$ftpServer->restart();
			if (is_object($capp->docFlowApp)) {
				$adapter = $Objects->get("DocFlowDataAdapter_".$capp->docFlowApp->getId()."_1");
				if (!$adapter->connected)
					$adapter->connect();
				if ($adapter->connected) {
					$sql = "UPDATE fields SET value='User_".$this->module_id."_".$this->name."' WHERE name='objectId' AND value='User_".$this->module_id."_".$old_name."' AND classname='ReferenceUserInfoCard'";
					$stmt = $adapter->dbh->prepare($sql,array(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true));
					$stmt->execute();
				}
			}				
		}
        $this->loaded = true;
    }

    function getId() {
        return "User_".$this->module_id."_".$this->name;
    }

    function getPresentation() {
        return $this->name;
    }

    function getHookProc($number) {
    	switch($number) {
    		case '3': return "save";
    		case '4': return "remove";
    	}
    	return parent::getHookProc($number);
    }
}
?>