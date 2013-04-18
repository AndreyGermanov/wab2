<?php
/**
 * Описывает учетную запись группы пользователя POSIX
 *
 * В качестве основных данных:
 * 
 * - Имя группы
 *
 * Данные группы разбиты на следующие разделы:
 *
 * - Пользователи группы
 * - Права доступа группы к общим папкам
 * - Привилегии группы
 *
 *
 * @author andrey
 */
class Group extends WABEntity {
    
    public $fileServer,$Shell,$shares_access_rules,$group_users,$old_shares_access_rules,$old_group_users,$app;

    function construct($params)
    {
        $this->module_id = $params[0]."_".$params[1];
        $this->name = $params[2];

        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        $this->skinPath = $app->skinPath;
        $this->icon = $app->skinPath."images/Tree/group.png";

        $this->group_users = "";
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
        $this->referenceCode = "";

        $this->update();

        global $Objects;

        $this->Shell = $Objects->get("Shell_shell");

        $this->app = $Objects->get("Application");
        if (!$this->app->initiated)
            $this->app->initModules();
        $settings = $Objects->get("SystemSettings_".$this->module_id."_Settings");
        if (!$settings->loaded)
            $settings->load();
        $this->root_password = $settings->password;

        $this->template = "templates/controller/Group.html";
        $this->css = "styles/Mailbox.css";
        $this->handler = "scripts/handlers/controller/Group.js";

        $this->tabs_string.= "users|Пользователи|".$app->skinPath."images/spacer.gif;";
        $this->tabs_string.= "privileges|Привилегии|".$app->skinPath."images/spacer.gif";

        $this->tabset_id = "WebItemTabset_".$this->module_id."_Group".$this->name;
        $this->active_tab = "users";
        $this->users_table = "ObjectsSelectTable_".$this->module_id."_group".$this->name;
        $this->width = "680";
        $this->height = "400";
        $this->overrided = "width,height";
        
        $this->clientClass = "Group";
        $this->parentClientClasses = "Entity";
                
        $this->loaded = false;
    }

    function update() {
        $this->old_name = $this->name;
        $this->old_group_users = $this->group_users;
        $this->old_group_users_string = $this->group_users_string;
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
    }

    function getCredentials() {
        global $Objects;
        $settings = $Objects->get("SystemSettings_".$this->module_id."_Settings");
        if (!$settings->loaded)
            $settings->load();
        return "root%".$settings->password;
    }
    
    function getArgs() {
		if (!$this->loaded)
			$this->load();    		
   	    $params = array();   	    
        $params["window_id"] = $this->window_id;
	    $params["opener_item"] = $this->getId();     
        $params["selected_items"] = $this->group_users_string;
        $this->usersFrameSrc = "?object_id=ObjectsSelectTable_".$this->module_id."_group".$this->name."&hook=3&arguments=".urlencode(json_encode($params));
        $result = parent::getArgs();
        $result["{|referenceCode}"] = $this->referenceCode;
        return $result;
    }

    function load() {
        // Получаем основные и дополнительные параметры для первых двух закладок
        global $Objects;
        $this->app = $Objects->get("Application");
        $this->Shell = $Objects->get("Shell_shell");
        $app = $Objects->get($this->module_id);

        $this->fileServer = $Objects->get("FileServer_".$this->module_id."_Shares");
        if (!$this->fileServer->loaded)
            $this->fileServer->load();

        $credentials = $this->getCredentials();
        if ($this->name != "") {
            $arr = array("{group}" => $this->name, "{credentials}" => $credentials,"{user_or_group}" => "group", "{user}" => trim($this->name));

            // Получаем пользователей, которые входят в эту группу
            if ($app->remoteSSHCommand!="")
                $strings_array = explode("|",shell_exec(strtr($app->remoteSSHCommand." \"".$this->fileServer->smbGetGroupUsersCommand.";echo \|;".$this->fileServer->smbGetPrivilegesCommand."\"",$arr)));
            else
                $strings_array = explode("|",$this->Shell->exec_command(strtr($this->fileServer->smbGetGroupUsersCommand.";echo \|;".$this->fileServer->smbGetPrivilegesCommand."",$arr)));
            
            $group_users = explode("\n",$strings_array[0]);
            $this->group_users = array();
            foreach($group_users as $user) {
                if ($user!="(null)" and $user!= "")
                    $this->group_users[count($this->group_users)] = $user;
            }
            $this->group_users_string = implode(",",$this->group_users);
            
            // Получаем привилегии группы
            $arr["{user}"] = $this->name;
            if ($this->old_name!="") {
	            $privileges = array_flip(explode("\n",$strings_array[1]));
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
            $this->loaded = true;
            if ($this->loaded and is_object($app->docFlowApp)) {
            	$this->adapter = $Objects->get("DocFlowDataAdapter_".$app->docFlowApp->getId()."_1");
            	$query = "SELECT entities FROM fields WHERE @objectId='".$this->getId()."' AND @classname='ReferenceGroupInfoCard'";
            	$res = PDODataAdapter::makeQuery($query, $this->adapter,$app->docFlowApp->getId());
            	if (count($res)>0) {
            		$card = current($res);
            		$this->referenceCode = "<input type='button' fileid='".$card->getId()."' id='referenceButton' value='Открыть описание'/>";
            	} else {
            		$this->referenceCode = "<input type='button' fileid='ReferenceGroupInfoCard_".$app->docFlowApp->getId()."_' id='referenceButton' value='Открыть описание'/>";
            	}
            }            
        }
    }
    
    function getAccessRules() {
        global $Objects;
        $app = $Objects->get($this->module_id);
        $this->fileServer = $Objects->get("FileServer_".$this->module_id."_Shares");
        $this->shares_access_rules = array();
        if (!$this->fileServer->sharesLoaded)
            $this->fileServer->loadShares();
        $shares_array = array();
        foreach($this->fileServer->shares as $share) {
            if ($share->name=="root")
                $path = $share->path;
            else
                $path = $this->fileServer->shares_root."/".$share->path;
            $shares_array[] = $path;
            $share_names[] = $share->name;
        }
        $change_arr = array("{user_or_group}" => "group", "{user}" => trim($this->name), "{shares_list}" => implode("~",$shares_array));
        if ($app->remoteSSHCommand=="")
            $rights = $this->Shell->exec_command(strtr($this->fileServer->smbGetUserACLCommand,$change_arr));
        else
            $rights=shell_exec(strtr($app->remoteSSHCommand." \"".$this->fileServer->smbGetUserACLCommand."\"",$change_arr));
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

    function containsGroup($group) {
        global $Objects;
        $this->fileServer = $Objects->get("FileServer_".$this->module_id."_Shares");
        if (!$this->fileServer->loaded)
            $this->fileServer->load();
        $app = $Objects->get($this->module_id);
        $credentials = $this->getCredentials();
        $shell = $Objects->get("Shell_shell");
        $groups = array_flip(explode("\n",$shell->exec_command(str_replace("{credentials}",$credentials,$app->remoteSSHCommand." ".$this->fileServer->smbListGroupsCommand))));
        return isset($groups[$group]);

    }

    function remove() {
        global $Objects;
        $shell = $Objects->get("Shell_shell");
        $this->fileServer = $Objects->get("FileServer_".$this->module_id."_Shares");
        if (!$this->fileServer->loaded)
            $this->fileServer->load();
        $app = $Objects->get($this->module_id);
        if (!$this->loaded)
            $this->load();
        $gapp = $Objects->get("Application");
        if (!$gapp->initiated)
        	$gapp->initModules();
        $gapp->raiseRemoteEvent("GROUP_DELETED","object_id=".$this->getId());
        $credentials = $this->getCredentials();
        $arr = array("{group}" => $this->name, "{credentials}" => $credentials);
        $this->Shell->exec_command(strtr($app->remoteSSHCommand." ".$this->fileServer->smbRemoveGroupCommand,$arr));
        
        if (is_object($app->docFlowApp)) {
        	$adapter = $Objects->get("DocFlowDataAdapter_".$app->docFlowApp->getId()."_1");
        	if (!$adapter->connected)
        		$adapter->connect();
        	if ($adapter->connected) {
        		$entities = PDODataAdapter::makeQuery("SELECT entities FROM fields WHERE @objectId='".$this->getId()."' AND @classname='ReferenceGroupInfoCard'",$adapter,$app->docFlowApp->getId());
        		foreach ($entities as $entity) {
        			$entity->loaded = false;
        			$entity->load();
        			$entity->deleted = 1;
        			$entity->save(true);
        		}
        	}
        }        
    }

    function save($arguments=null) {
        $update_command = "";
        global $Objects;
        if (isset($arguments)) {
        	$this->load();
        	$this->setArguments($arguments);
        }
        $this->fileServer = $Objects->get("FileServer_".$this->module_id."_Shares");
        if (!$this->fileServer->loaded)
            $this->fileServer->load();
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        $capp = $Objects->get($this->module_id);
        $shell = $Objects->get("Shell_shell");
        $credentials = $this->getCredentials();
        $arr = array("{group}" => $this->name, "{credentials}" => $credentials);
        $arr["{group}"] = $this->old_name;
        $arr["{user}"] = $this->old_name;
        $gid="";$rights="";
        if ($this->old_name!="") {
            if ($capp->remoteSSHCommand=="") {
                $gid = str_replace("\n","",$shell->exec_command(strtr($this->fileServer->smbGetGidOfGroupCommand,$arr)));
                $rights = str_replace("\n"," ",$shell->exec_command(strtr($this->fileServer->smbGetPrivilegesCommand,$arr)));
            } else {
                $gid = str_replace("\n","",shell_exec(strtr($capp->remoteSSHCommand." \"".$this->fileServer->smbGetGidOfGroupCommand."\"",$arr)));
                $rights = str_replace("\n"," ",shell_exec(strtr($capp->remoteSSHCommand." \"".$this->fileServer->smbGetPrivilegesCommand."\"",$arr)));            
            }
        }
        $arr["{group}"] = $this->name;
        $arr["{privileges}"] = $rights;
        $arr["{user}"] = $this->name;
        $old_name = "";
        
        if ($this->old_name=="")
        	$app->raiseRemoteEvent("GROUP_ADDED","object_id=".$this->getId());
        else
        	$app->raiseRemoteEvent("GROUP_CHANGED","object_id=".$this->getId());
        
        // Проверяем имя группы
        if ($this->name != $this->old_name) {
            if ($this->containsGroup($this->name)) {
                $this->reportError("Группа с именем ".$this->name." уже существует !","save");
                return 0;
            }
            // если прошлого имени не было, значит группа новая и нужно создать ее
            $old_name = $this->old_name;
            if ($this->old_name=="") {
                $shell->exec_command(strtr($capp->remoteSSHCommand." ".$this->fileServer->smbAddGroupCommand,$arr));
                $this->old_name = $this->name;
            } else {
                // иначе добавим переименование группы к команде обновления информации о группе
                $arr["{group}"] = $this->old_name;
            	$arr["{new_group}"] = $this->name;
                $shell->exec_command(strtr($capp->remoteSSHCommand." ".$this->fileServer->smbRenameGroupCommand,$arr));				                
                $shell->exec_command(str_replace("{params}","-x '".$this->old_group_users_string."' -n ".$this->name,$capp->remoteSSHCommand." ".$this->fileServer->smbChangeGroupOptionsCommand." ".$this->old_name));
                if (is_object($capp->docFlowApp)) {
					$adapter = $Objects->get("DocFlowDataAdapter_".$capp->docFlowApp->getId()."_1");
					if (!$adapter->connected)
						$adapter->connect();
					if ($adapter->connected) {
						$sql = "UPDATE fields SET value='Group_".$this->module_id."_".$this->name."' WHERE name='objectId' AND value='Group_".$this->module_id."_".$this->old_name."' AND classname='ReferenceGroupInfoCard'";
						$stmt = $adapter->dbh->prepare($sql,array(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true));
						$stmt->execute();
					}
				}				
                $this->old_name = $this->name;
            }
        }

        if ($this->group_users_string != $this->old_group_users_string) {
            $shell->exec_command(str_replace("{params}","-x ``".$this->old_group_users_string."`` -m ``".$this->group_users_string."``",$capp->remoteSSHCommand." ".$this->fileServer->smbChangeGroupOptionsCommand." ".$this->name));
        }
       
        // Записываем привилегии группы
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
        if ($grant_arr!="") {
            $arr = array("{user}" => $this->name, "{credentials}" => $credentials, "{privileges}" => $grant_arr);
            $shell->exec_command(strtr($capp->remoteSSHCommand." ".$this->fileServer->smbAddPrivilegesCommand,$arr));
        }
        if ($revoke_arr!="") {
            $arr = array("{user}" => $this->name, "{credentials}" => $credentials, "{privileges}" => $revoke_arr);
            $shell->exec_command(strtr($capp->remoteSSHCommand." ".$this->fileServer->smbRemovePrivilegesCommand,$arr));
        }
		if ($this->name != $this->old_name) {
			$ftpServer = $Objects->get("FTPServer_".$this->module_id."_ftp");
			$ftpServer->setHosts($ftpServer->getHosts());
		}
        $this->update();
        $this->loaded = true;
    }

    function getId() {
        return "Group_".$this->module_id."_".$this->name;
    }

    function getPresentation() {
        return $this->name;
    }
    
    function getHookProc($number) {
    	switch($number) {
    		case '3': return "save";
    		case '4': return "remove";
    	}
    }
}
?>