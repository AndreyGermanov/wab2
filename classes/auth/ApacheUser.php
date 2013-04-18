<?php
/**
 * Данный класс управляет аутентификационной записью пользователя Apache.
 * Записи хранятся в файле Application->apacheUsersTable. Пользователи Apache
 * используются для входа в интерфейс управления почтовым сервером.
 *
 * Файл имеет формат:
 *
 * имя:пароль
 *
 * Пароль зашифрован с помощью функции crypt.
 *
 * Класс позволяет загружать информацию о пользователе из файла, сохранять
 * информацию в файл. Используются следующие функции:
 *
 * load() - загружает информацию о пользователе
 * save() - сохраняет информацию о пользователе
 * getId() - получает идентификатор объекта
 * getPresentation - получает строковое представление объекта
 *
 * @author andrey
 */
class ApacheUser extends WABEntity {	
	
	public $config = array();
	public $modules = array();
	public $roles = array();
	
    function construct($params) {
        if (count($params)>=2)
            $this->module_id = array_shift($params)."_".array_shift($params);
        else
            $this->module_id = "";
        $this->name = implode("_",$params);
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        $this->password = "";
        $this->config_file = "";
        $this->template = "RenderForm";
        $this->css = $app->skinPath."styles/Mailbox.css";
        $this->skinPath = $app->skinPath;
        $this->handler = "scripts/handlers/auth/ApacheUser.js";
        $this->loaded = false;
        $this->icon = $app->skinPath."images/Tree/user.gif";
        $this->skin = $app->skinPath;
        $this->width = "700";
        $this->height = "550";
        $this->overrided = "width,height";
        $this->tabset_id = "WebItemTabset_".$this->module_id."_".$this->name."ApacheUser";
        $this->tabs_string = "main|Основные|".$app->skinPath."images/spacer.gif;";
        $this->tabs_string .= "system|Система|".$app->skinPath."images/spacer.gif;";
        $this->tabs_string .= "modules|Модули|".$app->skinPath."images/spacer.gif;";
        $this->tabs_string .= "activity|Активность|".$app->skinPath."images/spacer.gif";
        $this->active_tab = "main";
        $this->appconfigTab = "ModelConfig_".$this->module_id."_userconfig_appconfig";
        $this->models[] = "ApacheUser";
        $this->clientClass = "ApacheUser";
        $this->parentClientClasses = "Entity";
        $this->logon_time_string = "";
        $this->isActive = "";
        $this->hasAdminAccess = "1";
        $this->active_duration = "";        
    }

    function load() {
        global $Objects,$appconfig,$panels,$modules,$interfaces,$userconfig,$defaultconfig,$roles,$userSettings;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
		if (file_exists("/etc/WAB2/config/".$this->name.".php"))
        	$this->config_file = "/etc/WAB2/config/".$this->name.".php";
		if (file_exists($this->config_file))
			include($this->config_file);  
		if (file_exists("/etc/WAB2/config/".$this->name."_settings.php"))
        	$this->settings_file = "/etc/WAB2/config/".$this->name."_settings.php";
		if (file_exists($this->settings_file))
			include($this->settings_file);  
		$config = $userconfig["appconfig"];

		if ($this->name==$app->User) {
        	$this->config = $userconfig;
		}
		else {
			$this->config = $GLOBALS["defaultconfig"];
			$GLOBALS["userconfig"] = $GLOBALS["defaultconfig"];
		}
		if (@$config["apacheAdminsBase"]=="ldap") {
        	$con = ldap_connect($config["apacheAdminsLdapHost"],$config["apacheAdminsLdapPort"]);
        	if (!$con)
        		return 0;
        	ldap_set_option($con, LDAP_OPT_PROTOCOL_VERSION, 3);
        	$bind = ldap_bind($con,$config["apacheAdminsLdapUser"],$config["apacheAdminsLdapPassword"]);
        	if (!$bind)
        		return 0;
        	$res = ldap_list($con,$config["apacheAdminsLdapBase"],"(objectClass=*)");
        	if (!$res)
        		return 0;
        	$entries = ldap_get_entries($con,$res);
        	$strings = array();
        	foreach($entries as $value) {
        		$strings[] = trim($value["uid"][0]).":";
        	}
        } else
        	$strings = file($appconfig["apacheUsersTable"]);
        $user_modules = array();
        $default_modules = array();
        foreach ($strings as $value) {
            $parts = explode(":",$value);
            if (@$parts[0][0]=="#") {
            	$hasAdminAccess = 0;
            	$parts[0] = str_replace("#","",$parts[0]);
            } else
            	$hasAdminAccess = 1;
            if ($this->name==trim($parts[0])) {
                $this->password = str_replace("\n","",trim($parts[1]));
                if (isset($parts[2]))
                	$this->config_file = str_replace("\n","",trim($parts[2]));
                else if (file_exists("/etc/WAB2/config/".$this->name.".php"))
                	$this->config_file = "/etc/WAB2/config/".$this->name.".php";
				if (file_exists($this->config_file)) {          
                	include($this->config_file);
                	$GLOBALS["userconfig"] = $userconfig;
        			$this->config = $userconfig;
				}
				$this->hasAdminAccess = $hasAdminAccess;
				$this->logon_time = 0;
				$this->active_time = 0;
				$this->active = false;		
				$current_time = time();		
				if (file_exists($app->variablesPath."users/".$this->name."/logon_time"))
					$this->logon_time = filemtime($app->variablesPath."users/".$this->name."/logon_time");
				if (file_exists($app->variablesPath."users/".$this->name."/active_time"))				
					$this->active_time = filemtime($app->variablesPath."users/".$this->name."/active_time");
				if ($current_time-$this->active_time<60)
					$this->active = true;
				$this->active_seconds = $this->active_time-$this->logon_time;				
				if ($this->active) {
					$this->isActive = "Да";
					$this->active_duration = date("H:i:s",$this->active_time-$this->logon_time);
                	$this->active_duration = execAlgo("secondsToDuration",array("seconds" => $this->active_seconds));
                	$this->logon_time_string = date("d.m.Y H:i:s",$this->logon_time);
				} else {
					$this->active_time = "";
					$this->isActive = "Нет";
					$this->active_duration = "";
                	$this->logon_time_string = date("d.m.Y H:i:s",$this->logon_time);
				}
				break;
            }
        }
        if (isset($GLOBALS["bannedUsers"][$this->name])) {
        	$this->banned = "1";
        	$this->isActive = "Заблокирован";
        	$this->banButtonText = "Включить";
        } else {
        	$this->banned = "0";
        	$this->banButtonText = "Отключить";
        }        
      	$this->loaded_name = $this->name;
        $this->loaded_password = str_replace("\n","",trim($parts[1]));
        $this->language = @$this->config["appconfig"]["language"];                
        $this->skin = $this->config["appconfig"]["skinPath"];
        $this->wabBackgroundColor = $this->config["appconfig"]["wabBackgroundColor"];
        $this->interface = $this->config["interface"]["name"]; 
        $this->loaded = true;
        if (isset($this->config["modules"])) {
 	      	foreach ($this->config["modules"] as $key=>$value) {
    	   		if (!isset($user_modules[$key]))
        			$user_modules[$key] = $value;
               	}
        }
        if (isset($defaultconfig["modules"])) {
          	foreach ($defaultconfig["modules"] as $key=>$value) {
       		if (!isset($default_modules[$key]))
       			$default_modules[$key] = $value;
          	}
        }                
        if (isset($this->config["interface"]["controlPanel"])) {
           	foreach ($panels[$this->config["interface"]["controlPanel"]]["modules"] as $key => $value) {
           		if (!isset($user_modules[$key]))
           			$user_modules[$key] = $value;
           		else {
           			foreach($value as $key1=>$val)
           				if (!isset($user_modules[$key][$key1]))
           					$user_modules[$key][$key1] = $val;
           		}
           	}
        }
        if (isset($defaultconfig["interface"]["controlPanel"])) {
          	foreach ($panels[$defaultconfig["interface"]["controlPanel"]]["modules"] as $key => $value) {
           		if (!isset($default_modules[$key]))
           			$default_modules[$key] = $value;
           		else {
           			foreach($value as $key1=>$val)
           				if (!isset($default_modules[$key][$key1]))
            				$default_modules[$key][$key1] = $val;
   	       		}
           	}
        }                
        if (isset($this->config["interface"]["showControlPanel"])) {
           	$this->showControlPanel = "1";
           	$this->controlPanel = $this->config["interface"]["controlPanel"];
        }
        else
          	$this->showControlPanel = "0";
        if (isset($this->config["interface"]["showMainMenu"])) {
           	$this->showMainMenu = "1";
           	$this->mainMenuName = $this->config["interface"]["mainMenuName"];
        }
        else
          	$this->showMainMenu = "0";     
        if (isset($this->config["interface"]["showInfoPanel"])) {
           	$this->showInfoPanel = "1";
        }
        else
          	$this->showInfoPanel = "0";     
        if (isset($this->config["interface"]["customObjectName"])) {
           	$arr = explode("_",$this->config["interface"]["customObjectName"]);
           	$class = array_shift($arr);
           	$this->customObjectName = $class."_".$this->module_id."_".implode("_",$arr);
        }           
        if (count($user_modules)>0)
        	$this->modules = $user_modules;
        $this->old_skin = $this->skin;
            
        if ($this->name==$app->User)	 
        	$GLOBALS["userconfig"]["modules"] = $this->modules;
        $GLOBALS["defaultconfig"]["modules"] = $default_modules;
        $this->modulesTabset_id = "WebItemTabset_".$this->module_id."_".$this->name."UserModules";
        $tab_array = array();
        $i = 0;
        if (isset($this->modules) and (is_array($this->modules))) {
	        foreach ($this->modules as $key=>$value) {
	        	if ($i==0)
	        		$this->modulesActiveTab = $key;
	        	$tab_array[] = $key."|".@$value["title"]."|".$app->skinPath."images/spacer.gif";
	        	$i++;
	        }             
        	$this->modulesTabsString = implode(";",$tab_array);
        }
        $this->modulesTable = "MetadataArrayTable_".$this->module_id."_".$this->name."Table";
        $this->rolesTable = "MetadataArrayTable_".$this->module_id."_".$this->name."Roles";
        $arr = array();
    }

    function save($arguments=null) {    	
        global $Objects,$userconfig,$appconfig,$defaultconfig,$modules,$panels,$interfaces,$models,$roles;
        
        if (isset($arguments)) {
        	if (!$this->loaded)
				$this->load();
			$this->setArguments($arguments);
			$arguments["modules"] = (array)$arguments["modules"];						
			$arguments["roles"] = (array)$arguments["roles"];						
			if (is_array($arguments["modules"])) {
				$user_modules = array();
				foreach ($arguments["modules"] as $key=>$value) {
					if (is_object($value))
						$value = (array)$value;
					if (!is_array($value))
						$value = @$modules[$value];
					if (is_array($value)) {
						foreach ($value as $key1=>$value1) {
							if (isset($models[$key][$key1])) {
								$user_modules[$key][$key1] = $value1;
							}
						}
						if (isset($value["md_name"]))
							$user_modules[$key]["name"] = $value["md_name"];
						else
							$user_modules[$key]["name"] = $value["name"];
						$user_modules[$key]["collection"] = "modules";
					}
				}
			}
			if (count($user_modules)>0) {
				$this->config["modules"] = $user_modules;			
				$this->modules = $this->config["modules"];
			}
			if (is_array($arguments["roles"])) {
				$user_roles = array();
				foreach ($arguments["roles"] as $key=>$value) {
					if (is_object($value))
						$value = (array)$value;
					if (!is_array($value))
						$user_roles[$value] = @$roles[$value];											
				}
			}
			$this->config["roles"] = $user_roles;
			if (is_object($arguments["appconfig"])) {
				$arguments["appconfig"] = (array)$arguments["appconfig"];
				foreach($arguments["appconfig"] as $key=>$value) {
					if (isset($models["appconfig"][$key])) {
						$this->config["appconfig"][$key] = $value;
					}
				}
			}
		};		
		
        if ($this->name == "") {
            $this->reportError("Укажите имя пользователя !","save1");
            return 0;
        }

        if ($appconfig["apacheAdminsBase"]!="ldap" and $this->password == "") {
            $this->reportError("Укажите пароль !","save2");
            return 0;
        }
        
        if (!preg_match("/[a-fA-F0-9]{6}/",$this->wabBackgroundColor)) {
        	$this->reportError("Цвет фона указан не верно !","save3");
        	return 0;        	 
        }

        if ($this->name != $this->loaded_name) {
            $apache_users = $Objects->get("ApacheUsers_".$this->module_id);
            if ($apache_users->contains($this->name)) {
                $this->reportError("Пользователь ".$this->name." уже существует !","save4");
                return 0;
            }
        }

        $app = $Objects->get("Application");
        
        if ($this->loaded_name=="")
        	$app->raiseRemoteEvent("APACHEUSER_ADDED","object_id=".$this->getId());
        else
        	$app->raiseRemoteEvent("APACHEUSER_CHANGED","object_id=".$this->getId());
        
        if ($appconfig["apacheAdminsBase"]!="ldap") {
	        $strings = file("/etc/WAB2/config/admins");
	        $fp = fopen("/etc/WAB2/config/admins","w");
	        $found = false;
	        if (!$this->hasAdminAccess)
	           	$name = "#".$this->name;
	        else 
	          	$name = $this->name;
	        foreach ($strings as $str) {
	            $parts = explode(":",$str);
	            $parts[0] = str_replace("#","",$parts[0]);
	            if ($parts[0]==$this->loaded_name) {
	                $found = true;
	                if ($this->password==$this->loaded_password)
	                	$pass = $this->loaded_password;
	                else
	                  $pass = $this->password;
	                fwrite($fp,trim($name).":".trim($pass)."\n");
	            }
	            else
	          		fwrite($fp,str_replace("\n","",trim($str))."\n");
	        }
	        $app = $Objects->get("Application");
	        if (!$app->initiated)
	            $app->initModules();
	        if (!$found) {
	                $shell = $Objects->get("Shell_shell");
	                if ($this->password==$this->loaded_password)
	                        $pass = $this->loaded_password;
	                else
	                    $pass = $this->password;
	                fwrite($fp,trim($this->name).":".$pass."\n");
	        }
	        fclose($fp);
        }
        
        if ($this->loaded_name!=$this->name) {
            if (file_exists($app->variablesPath."users/".$this->loaded_name))
                shell_exec($app->moveDirCommand.' '.$app->variablesPath."users/".$this->loaded_name." ".$app->variablesPath."users/".$this->name);
            if (file_exists('/etc/WAB2/config/'.$this->loaded_name."_settings.php"))
            	shell_exec($app->moveDirCommand.' /etc/WAB2/config/'.$this->loaded_name."_settings.php /etc/WAB2/config/".$this->name."_settings.php");
        }
        	
        @unlink("/etc/WAB2/config/".$this->loaded_name.".php");
        if ($this->skin != $this->old_skin) {
        	$this->config["appconfig"]["skinPath"] = $this->skin;
        }
        $this->config["appconfig"]["wabBackgroundColor"] = $this->wabBackgroundColor;
        $this->config["appconfig"]["language"] = $this->language;
        $this->config["interface"] = $interfaces[$this->interface];
        if (count($this->config["modules"])==0)
        	$this->config["modules"][$defaultconfig["appconfig"]["defaultModule"]] = $defaultconfig["modules"][$defaultconfig["appconfig"]["defaultModule"]]; 
        $string = trim(getDiffArray("userconfig",$this->config,$defaultconfig));
        $modules_string = array();
        $roles_strings = array();
        foreach($this->config["modules"] as $key=>$value) {
        	$value2 = $value;
        	if (isset($modules[$key]["settings"]))
        		$value2["settings"] = $modules[$key]["settings"];
        	if (!isset($defaultconfig["modules"][$key]))
        		$modules_string[] = trim(getDiffArray("userconfig['modules']['".$key."']",$value2,$modules[$key]));
        }               
        $string .= "\n".implode("\n",$modules_string);    
        foreach($this->config["roles"] as $key=>$value) {
        	if (!isset($defaultconfig["roles"][$key])) {
        		$roles_string[] = trim(getDiffArray("userconfig['roles']['".$key."']",$value,$roles[$key]));
        	}
        }               
        $string .= "\n".implode("\n",$roles_string);       
        if ($string!="")
       		file_put_contents("/etc/WAB2/config/".$this->name.".php","<?php\n".$string."\n?>");     
        $this->loaded_name = $this->name;
        $this->loaded = true;
    }

    function getId() {
        return "ApacheUser_".$this->module_id."_".$this->name;
    }

    function getPresentation() {
        return $this->name;
    }

    function getArgs() {
        global $Objects,$appconfig,$interfaces;
        $result = parent::getArgs();        
        $strings = file("/etc/WAB2/config/skins");
        $arr = array();
        foreach($strings as $line) {
            $parts = explode("|",$line);
            $arr[$parts[0]] = $parts[1];
        }
        $result["{skins}"] = implode(",",array_keys($arr))."|".implode(",",array_values($arr));
        $result["{authType}"] = @$appconfig["apacheAdminsBase"];
        if (@$appconfig["apacheAdminsBase"]=="ldap")
        	$result["{displayStyle}"] = "none";
        else
        	$result["{displayStyle}"] = "";
        $app = $Objects->get("Application");
        $result["{languages}"] = implode(",",array_keys($app->languages))."|".implode(",",array_values($app->languages));
        $arr = array();
        $arr2 = array();
        foreach ($this->modules as $key=>$value) {
        	$arr[] = '"'.array_shift(explode("_",$value["class"])).'ModuleConfig_'.$this->module_id.'_userconfig_modules~'.$key.'"';
        }
        $result["{modulesString}"] = implode(",",$arr);
        $arr = array();
        foreach($interfaces as $key=>$value) {
        	$arr[$key] = $value["metaTitle"];
        };
        $result["{interfacesList}"] = implode("~",array_keys($arr))."|".implode("~",array_values($arr));
        return $result;
    }
    
    function getOpenedObjects() {
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        
        $dir = $app->variablesPath."users/".$this->name."/windows";
        $objects = array();
        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                    if ($file!="." and $file!="..") {
                        if (!is_dir($dir."/".$file)) {
                            $objects[$file] = $file;
                        }
                    }
                }
            }
        }
        return $objects;
    } 
    
    function getLinkedDocflowObjects($module="",$class="",$field="account") {
    	global $Objects,$modules;
    	$modules_array = array();    	
    	if ($module!="") {
    		$app = $Objects->get("Application");
    		if (!$app->initiated)
    			$app->initModules();
    		$modules_array[$module] = $app->getModuleByClass($module);
    	}
    	else {
    		foreach ($modules as $key=>$value) {
    			if (array_shift(explode("_",$value["class"]))=="DocFlowApplication")
    				$modules_array[$key] = $value;
    		}    		
    	}
    	$result = array();
    	foreach($modules_array as $key=>$value) {
    		$adapter = $Objects->get("DocFlowDataAdapter_".$value["class"]."_1");
    		$sql = "SELECT entities FROM fields WHERE @".$field."='".$this->name."'";
    		if ($class!="")
    			$sql .= " AND @classname='".$class."'";
    		$res = PDODataAdapter::makeQuery($sql, $adapter, $value["class"]);
    		foreach($res as $value) {
    			$result[] = $value;
    		}
    	}
    	return $result;
    }
    
    function renderForm() {
    	$blocks = getPrintBlocks(file_get_contents("templates/auth/ApacheUser.html"));
    	$out = $blocks["header"];
    	$out .= $blocks["main"];
    	$out .= $blocks["system"];
    	$out .= $blocks["modulesHeader"];
    	$i=0;
     	foreach ($this->modules as $key=>$value) {
     		$this->moduleTab = array_shift(explode("_",$value["class"]))."ModuleConfig_".$this->module_id."_userconfig_modules~".$key;
     		$this->moduleTabId = $key;
     		if ($i==0)
     			$this->moduleDisplay = "";
     		else 
     			$this->moduleDisplay = "none";
     		$out .= strtr($blocks["moduleRow"],array("{moduleTab}" => $this->moduleTab, "{moduleTabId}" => $key, "{moduleDisplay}" => $this->moduleDisplay));
     		$i++;
     	}
     	$out .= $blocks["modulesFooter"];
     	$out .= $blocks["activity"];
    	$out .= $blocks["footer"];
    	return $out;
    } 

    function remove() {
    	global $Objects;
    	$users = $Objects->get("ApacheUsers_".$this->module_id."_users");
    	$users->remove($this->name);
    }
    
    function banUser($arguments) {
    	global $Objects;
    	$switch = $arguments["switch"];
    	$app = $Objects->get("Application");    		
    	if ($switch=="ban") {
    		$GLOBALS["bannedUsers"][$this->name] = $this->name;
    		saveMetadataFile($GLOBALS["bannedUsers"]["file"]);
    	} else {
    		unset($GLOBALS["bannedUsers"][$this->name]);
    		saveMetadataFile($GLOBALS["bannedUsers"]["file"]);
    	}  	
    	if (!$app->initiated)
    		$app->initModules();
    	if ($switch=="ban")
    		$app->raiseRemoteEvent("USER_BAN","message=Пользователь заблокировал пользователя ".$this->name);
    	else
    		$app->raiseRemoteEvent("USER_BAN","message=Пользователь снял блокировку пользователя ".$this->name);
    }
    
    function getHookProc($number) {
		switch ($number) {
			case '3': return "save";
			case '4': return "banUser";
		}
		return parent::getHookProc($number);
	} 
}
?>