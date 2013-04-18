<?php
/* 
 * Основной класс приложения. Управляет подключаемыми модулями (MailApplication,
 * WebServerApplication, ControllerApplication и др.)
 */

/**
 * Description of Application
 *
 * @author andrey
 */
class Application extends WABEntity {
    public $modules;
    public $config;
    public $languages;
    
    function construct($params="") {
        global $Objects;
        
        $this->object_id = @$params[0];
        $this->template = "templates/core/Application.html";
        $this->handler = "scripts/handlers/core/Application.js";
        $this->icon = "images/spacer.gif";
        
        $this->languages["rus"] = "Русский";
        $this->languages["eng"] = "Английский";

        $this->clientClass = "Application";
        $this->parentClientClasses = "Mailbox~Entity";
        $this->classTitle = "Приложение";
        $this->classListTitle = "Приложение";        
        $this->initiated = false;
    }

    function initModules() {
        global $Objects;
        $this->initiated=true;
        $this->variablesPath = "/var/WAB2/";
        if ($this->User=="")
        	$this->User =@$_SESSION["user"];
        if ($this->User=="") {        	
            $this->config = $Objects->get("AdminConfig_".@$_SERVER["PHP_AUTH_USER"]);
            $this->User = @$_SERVER["PHP_AUTH_USER"];
            if ($this->User=="")
            	$this->User = "default";
            $_SESSION["user"] = $this->User;        
        } else
            $this->config = $Objects->get("AdminConfig_".$this->User);
        $this->user = $this->config->user;
        $this->config = $this->config->config;

        $this->active_tab = @$this->config["appconfig"]["defaultModule"];
        $this->modules = $this->user->modules;
		$this->defaultModuleName = @$this->user->modules[$this->active_tab]["class"];
        $counter = 0;
        
        foreach ($this->config["appconfig"] as $key=>$value)
        	$this->fields[$key] = $value;
        
        $this->root_path = $this->rootPath;
        $this->sshRemoteCommand = $this->sSHRemoteCommand;
        $this->md5SumCommand = $this->mD5SumCommand;
        $this->adminsFile = "/etc/WAB2/config/admins";
        $this->apacheRestartCommand = "/etc/init.d/apache2 reload";
        $this->icon=$this->skinPath."images/Window/header-lva.gif";
        $this->css = $this->skinPath."styles/Mailbox.css";

        $this->apacheUsersTable = $this->adminsFile;
        $this->variablesPath = "/var/WAB2/";
        if ($this->language=="")
            $this->language = "rus";
        if ($this->language!="rus")
            if (file_exists("l10n/".$this->language."/dict.php")) {
                require_once("l10n/".$this->language."/dict.php");
            }             
        $this->initiated = true;
		$this->processUser();
    }

    function processUser($arguments=array()) {
        global $Objects;
        if ($this->systemScript)
        	return 0;
        $locked_objects = array();
        if (count($arguments)>0) {
        	// Получаем список объектов, с которыми в данный момент работает пользователь
        	if (is_object($arguments["locked_objects"])) {
        		$locked_objects = (array)$arguments["locked_objects"]; 
        	}
        }
        if (!$this->initiated)
                $this->initModules();
        $shell = $Objects->get("Shell_shell");
        
        // Если папки переменных нет, создаем ее
        if (!file_exists($this->variablesPath)) {
            $shell->exec_command($this->makeDirCommand." ".$this->variablesPath);
            $shell->exec_command($this->chownCommand." -R ".$this->apacheServerUser." ".$this->variablesPath);
        }
        // Если нет папки переменных пользователей, создаем ее
        if (!file_exists($this->variablesPath."users")) {
            $shell->exec_command($this->makeDirCommand." ".$this->variablesPath."users");
            $shell->exec_command($this->chownCommand." -R ".$this->apacheServerUser." ".$this->variablesPath."users");
        }        
        // Если нет самого пользователя, нет смысла продолжать
        if ($this->User=="")
            return 0;
        // Если нет папки пользователя создаем ее
        if (!file_exists($this->variablesPath."users/".$this->User)) {
            $shell->exec_command($this->makeDirCommand." ".$this->variablesPath."users/".$this->User);
            $shell->exec_command($this->chownCommand." -R ".$this->apacheServerUser." ".$this->variablesPath."users/".$this->User);
        }
        
        // Если нет папки событий, переданных пользователю, создаем ее
        if (!file_exists($this->variablesPath."users/".$this->User."/events")) {
            $shell->exec_command($this->makeDirCommand." ".$this->variablesPath."users/".$this->User."/events");
            $shell->exec_command($this->chownCommand." -R ".$this->apacheServerUser." ".$this->variablesPath."users/".$this->User."/events");
        }
        
        // Если нет папки открытых окон пользователя создаем ее
        if (!file_exists($this->variablesPath."users/".$this->User."/windows")) {
            $shell->exec_command($this->makeDirCommand." ".$this->variablesPath."users/".$this->User."/windows");
            $shell->exec_command($this->chownCommand." -R ".$this->apacheServerUser." ".$this->variablesPath."users/".$this->User."/windows");
        }
        
        // Если пользователь просрочил, то есть, время его последней активности меньше текущего на 30 секунд,
        // удаляем файл logon_time - признак того, что он вошел в систему, а также все заблокированные им объекты из папки windows
        $current_time = time();
        if (isset($GLOBALS["bannedUsers"][$this->User])) {
        	$shell->exec_command($this->deleteCommand." ".$this->variablesPath."users/".$this->User."/logon_time");        	 
        	$shell->exec_command($this->deleteCommand." ".$this->variablesPath."users/".$this->User."/active_time");        	
        	$this->denyAccess = true;
        	exit("Пользователь с учетной записью ".$this->User." заблокирован. Доступ запрещен.");
        }
        if (file_exists($this->variablesPath."users/".$this->User."/active_time")) {
            $active_time = filemtime($this->variablesPath."users/".$this->User."/active_time");
            if ($current_time-$active_time>60) {
                $shell->exec_command($this->deleteCommand." ".$this->variablesPath."users/".$this->User."/logon_time");
                $shell->exec_command($this->deleteCommand." -rf ".$this->variablesPath."users/".$this->User."/windows/*");
            }                
        }
        else {
            $shell->exec_command($this->deleteCommand." ".$this->variablesPath."users/".$this->User."/logon_time");
            $shell->exec_command($this->deleteCommand." -rf ".$this->variablesPath."users/".$this->User."/windows/*");
        }
        // Если пользователь только что подключился, создаем признак этого
        if (!file_exists($this->variablesPath."users/".$this->User."/logon_time")) {
            $fp = fopen($this->variablesPath."users/".$this->User."/logon_time","w");fwrite($fp,session_id());fclose($fp);
            $shell->exec_command($this->chownCommand." -R ".$this->apacheServerUser." ".$this->variablesPath."users/".$this->User."/logon_time");
            //$this->raiseRemoteEvent("USER_ONLINE","name=".$this->User.",time=".date("d.m.Y H:m:s"));
        }
        // Обновляем время активности текущего пользователя
        $storedSID = trim(file_get_contents($this->variablesPath."users/".$this->User."/logon_time"));
        if ($storedSID==session_id()) {
        	$fp = fopen($this->variablesPath."users/".$this->User."/active_time","w");fwrite($fp," ");fclose($fp);
        	$active_time = filemtime($this->variablesPath."users/".$this->User."/active_time");        	
        	$shell->exec_command($this->chownCommand." -R ".$this->apacheServerUser." ".$this->variablesPath."users/".$this->User."/active_time");
        	$this->denyAccess = false;
        } else {
        	if ($this->User!="default") {
        		$this->denyAccess = false;
        		//exit("Пользователь с учетной записью ".$this->User." уже работает в системе. Доступ запрещен");
        	}
        }

        // Просматриваем папку оповещений о произошедших событиях, ожидающих пользователя
        $dir = $this->variablesPath."users/".$this->User."/events";
        $logon_time = filemtime($this->variablesPath."users/".$this->User."/logon_time");
        $events = array();
        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                    if ($file!="." and $file!="..") {
                        if (!is_dir($dir."/".$file)) {
                            $event_file = $dir."/".$file;
                            $event_time = filemtime($event_file);
                            // Если событие произошло до времени входа пользователя в систему,
                            // просто удаляем его файл
                            if ($event_time<$logon_time) {
                                $shell->exec_command($this->deleteCommand." ".$event_file);
                                continue;
                            } else {
                            	// Иначе добавляем его в список текущих событий, требуемых выполнения
                                $strings = file($event_file);
                                $event = str_replace("\n","",$strings[0]);
                                $params = str_replace("\n","",str_replace("xoxox",",",$strings[1]));
                                $events[count($events)] = $event."~".$params;
                                $shell->exec_command($this->deleteCommand." ".$event_file);
                            }

                        }
                    }
                }
                closedir($dh);
            }
        }
        // Выводим список событий в виде строки, для того чтобы клиент подхватил ее и сгенерировал
        // их для исполнения
        if (count($events)>0 and isset($_POST["ajax"]))
            echo implode("|",$events);

        $dir = $this->variablesPath."users/".$this->User."/windows";
        if (is_dir($dir)) {
        	if ($dh = opendir($dir)) {
        		while (($file = readdir($dh)) !== false) {
        			if ($file!="." and $file!="..") {
        				if (!is_dir($dir."/".$file)) {
        					$window_file = $file;
        					$current_time = time();
        					$file_time = filemtime($dir."/".$file);
        					// Если файла с объектом нет в переданном списке, удаляем этот файл
        					if (array_search($window_file,$locked_objects)===FALSE and ($current_time-$file_time)>25) { 
        						$shell->exec_command($this->deleteCommand." ".$dir."/".$file);
        						unset($locked_objects[array_search($window_file,$locked_objects)]);
        					}	        
        				}
        			}
        		}
        		closedir($dh);
        	}
        }
        foreach ($locked_objects as $value) {
        	if (!file_exists($this->variablesPath."users/".$this->User."/windows/".$value)) {
        		$fp = fopen($this->variablesPath."users/".$this->User."/windows/".$value,"w");
        		fwrite($fp," ");
        		fclose($fp);
        	}         		
        }
        
        // Проверяем всех остальных пользователей
        $dir = $this->variablesPath."users";
        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                    if ($file!="." and $file!=".." and $file != $this->User) {
                        if (is_dir($dir."/".$file)) {
                            if (file_exists($dir."/".$file."/active_time")) {
                                $current_time = time();
                                $active_time = filemtime($dir."/".$file."/active_time");
                                // если пользователь заблокирован
                                if (isset($GLOBALS["bannedUsers"][$file])) {
                                	// Удаляем информацию о подключении пользователя
        							$shell->exec_command($this->deleteCommand." ".$this->variablesPath."users/".$file."/logon_time");        	 
        							$shell->exec_command($this->deleteCommand." ".$this->variablesPath."users/".$file."/active_time");
                                    // удаляем список окон, которые он заблокировал
                                    $shell->exec_command($this->deleteCommand." -rf ".$dir."/".$file."/windows/*");
        							continue;        							        	
                                }
                                // если время последней активности пользователя меньше текущего на 60 секунд,
        						if ($current_time-$active_time>60) {                                	
                                	// удаляем признак активности пользователя
                                    $shell->exec_command($this->deleteCommand." ".$dir."/".$file."/active_time");
                                    // удаляем список окон, которые он заблокировал
                                    $shell->exec_command($this->deleteCommand." -rf ".$dir."/".$file."/windows/*");
                                    // генерируем событие об отключении пользователя
                                    //$this->raiseRemoteEvent("USER_OFFLINE","name=".$file.",time=".date("d.m.Y H:m:s"),$file);
                                }
                            } else {
                            	// удаляем список окон, которые заблокировал пользователь
                            	$shell->exec_command($this->deleteCommand." -rf ".$dir."/".$file."/windows/*");                            	 
                            }
                        }
                    }
                }
                closedir($dh);
            }
        }        
    }

    function raiseRemoteEvent($event,$params,$sender="",$receivers="") {
        global $Objects,$events;
        if (!$this->initiated)
                $this->initModules();
        if ($sender=="")
            $sender = $this->User;
        $dir = $this->variablesPath."users";
        if ($receivers!="")
        	$receivers = array_flip(explode(",",$receivers));
        $eventLog = $Objects->get("EventLog_".$this->defaultModuleName."_Events");
        $p = getParams($params);
        if (isset($p["object_id"])) {
        	if (isset($p["mark"]) and $event=="ENTITY_DELETED") {
        		$objs = explode("~",$p["object_id"]);
        		foreach ($objs as $obj_name) {
        			if ($p["mark"]=="1")
        				$this->raiseRemoteEvent("ENTITY_MARK_DELETED","object_id=".$obj_name,$sender);
        			else
        				$this->raiseRemoteEvent("ENTITY_MARK_UNDELETED","object_id=".$obj_name,$sender);
        		}
        		return 0;
        	}
        	else if (isset($p["mark"]) and $event=="ENTITY_REGISTERED") {
        		$objs = explode("~",$p["object_id"]);
        		foreach ($objs as $obj_name) {
        			if ($p["mark"]=="1")
        				$this->raiseRemoteEvent("ENTITY_MARK_REGISTERED","object_id=".$obj_name,$sender);
        			else
        				$this->raiseRemoteEvent("ENTITY_MARK_UNREGISTERED","object_id=".$obj_name,$sender);
        		}
        		return 0;
        	} else {
        		$obj = $Objects->get($p["object_id"]);
        		if (is_object($obj) and method_exists($obj,"processEvent"))
        			$message = $obj->processEvent($event,$params,$sender,"none");   
        		else if (isset($p["message"]))
        			$message = $p["message"];     	
        		$eventLog->logEvent($event,time(),$sender,$params,$message);
        	}			        	        		
        } else {
        	if (isset($p["message"]))
        		$message = $p["message"];
        	else
        		$message = @$events[$event]["comment"];
        	$eventLog->logEvent($event,time(),$sender,$params,$message);        	 
        }
        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                	$message = "";
                    if ($file!="." and $file!=".." and ($file != $sender or $receivers!="")) {
                    	if ($receivers!="" and !isset($receivers[$file]))
                    		continue;
                    	if (is_dir($dir."/".$file)) {
                        	$p = getParams(str_replace("xoxox",",",$params));
                        	if (isset($p["object_id"])) {
                        		if (isset($p["mark"]) and $event=="ENTITY_DELETED") {
                        			$objs = explode("~",$p["object_id"]);
                        			foreach ($objs as $obj_name) {
                        				if ($p["mark"]=="1")
                        					$this->raiseRemoteEvent("ENTITY_MARK_DELETED","object_id=".$obj_name,$sender,$file);
                        				else
                        					$this->raiseRemoteEvent("ENTITY_MARK_UNDELETED","object_id=".$obj_name,$sender,$file);
                        			}
                        			continue;
                        		}
                        		else if (isset($p["mark"]) and $event=="ENTITY_REGISTERED") {
                        			$objs = explode("~",$p["object_id"]);
                        			foreach ($objs as $obj_name) {
                        				if ($p["mark"]=="1")
                        					$this->raiseRemoteEvent("ENTITY_MARK_REGISTERED","object_id=".$obj_name,$sender,$file);
                        				else
                        					$this->raiseRemoteEvent("ENTITY_MARK_UNREGISTERED","object_id=".$obj_name,$sender,$file);
                        			}
                        			continue;
                        		
                         		} else { 
                        			$obj = $Objects->get($p["object_id"]);
                        			if (is_object($obj) and method_exists($obj,"processEvent")) {
                        				$result = $obj->processEvent($event,$params,$sender,$file);
                        				if (!$result)
                        					continue;
                        				if (strlen($result)>5)
                        					$message = str_replace(",","xoxox",$result);
                        			}   
                         		}                     		
                        	}
                            if (!file_exists($dir."/".$file."/active_time"))
                                continue;
                            $cnt = 1;
                            $current_time = time();
                            while (file_exists($dir."/".$file."/events/".$current_time."_".$cnt))
                                $cnt++;
                            $fp = fopen($dir."/".$file."/events/".$current_time."_".$cnt,"w");
                            fwrite($fp,$event."\n");
                            if ($params=="")
                            	$params = "fromUser=".$sender.",eventTime=".time();
                            else
                            	$params .= ",fromUser=".$sender.",eventTime=".time();
                            if ($message!="")
                            	$params .= ",message=".$message;
                            fwrite($fp,$params."\n");
                            fclose($fp);
                        }
                    }
                }
                closedir($dh);
            }
        }        
    }

    function raiseRemoteEventHook($arguments) {
		$this->raiseRemoteEvent(@$arguments["event"],@$arguments["params"],@$arguments["sender"],@$arguments["receivers"]);
    }

    function releaseObject($object_id) {
    	if (is_array($object_id))
    		$object_id = $object_id["php_object_id"];
        if (!$this->initiated)
            $this->initModules();
        unlink($this->variablesPath."users/".$this->User."/windows/".$object_id);
        $this->raiseRemoteEvent("ENTITY_CLOSED","object_id=".$object_id);
    }

    function load() {
        
    }

    function getPresentation() {
        return "Панель управления";
    }

    function __get($name) {
    	global $panels;
        if (isset($this->user->config["interface"]["controlPanel"]))
        	$modules = $panels[$this->user->config["interface"]["controlPanel"]]["modules"];
        else
        	$modules = $this->modules;
    	if ($name=="tabs_string") {
            $result = array();
            $counter = 0;
            	
            foreach($modules as $key=>$value) {
                	$result[$counter]= $key."|".$this->modules[$key]["title"]."|".$this->modules[$key]["image"];
                	$counter++;
            }
            return implode(";",$result);
        }
        if ($name=="modules_string") {
            $result = array();
            $counter = 0;
            foreach($modules as $key=>$value) {
                $result[$counter] = $this->modules[$key]["class"]."|".$key;
                $counter++;
            }
            return implode(";",$result);
        }
        if (isset($this->fields[$name]))
            return $this->fields[$name];
        else
            return "";
    }

    function getArgs() {
        global $Objects;
        $result = parent::getArgs();        
        $result['{tabs_string}'] = $this->tabs_string;
        $result['{modules_string}'] = $this->modules_string;
        return $result;
    }

    function checkUpdates() {
        global $Objects;
        $shell = $Objects->get("Shell_shell");
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        if ($shell->exec_command(strtr($app->pingPortTestCommand,array("{address}" => "95.71.125.121","{port}" => 8081)))!=0) {
            echo "noconnect";
            return 0;
        }               
        $current_version = str_replace("\n","",trim(file_get_contents("/etc/WAB2/config/version")));
        $remote_version = str_replace("\n","",trim(file_get_contents("http://repo.lvacompany.ru:8081/updates/version")));
        if ($current_version != $remote_version) {
            echo "yes";
        } else
            echo "no";
    }

    function update() {                
        $md5sum = str_replace("\n","",trim(file_get_contents("http://repo.lvacompany.ru:8081/updates/md5sum")));
        $update = file("http://repo.lvacompany.ru:8081/updates/wab2.tar.gz");
        $remote_version = str_replace("\n","",trim(file_get_contents("http://repo.lvacompany.ru:8081/updates/version")));
        $remote_build = str_replace("\n","",trim(file_get_contents("http://repo.lvacompany.ru:8081/updates/build")));
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        $shell = $Objects->get("Shell_shell");
        $shell->exec_command($this->deleteCommand." ".$this->root_path."/wab2.tar.gz");
        $fp = fopen("wab2.tar.gz","w");
        foreach($update as $str) {
            fwrite($fp,$str);
        }
        fclose($fp);
        $md5sum2 = str_replace("\n","",trim($shell->exec_command(str_replace("{file}",$this->root_path."/wab2.tar.gz",$app->md5SumCommand))));
        if ($md5sum!=$md5sum2) {
            echo "Произошел сбой при загрузке обновления! Система не обновлена.";
            return 0;
        }

        $shell->exec_command($this->tarCommand." xzf wab2.tar.gz --overwrite");
        $shell->exec_command($this->chownCommand." -R ".$this->apacheServerUser." ".$this->root_path);
        $shell->exec_command($this->deleteCommand." ".$this->root_path."/wab2.tar.gz");
        $shell->exec_command($this->copyCommand." /etc/WAB2/config/build /etc/WAB2/config/old_build");
        if (file_exists("script")) {
            $strings = file("script");
            foreach($strings as $line) {
                $shell->exec_command($line);
            }
        }
        $shell->exec_command($this->deleteCommand." ".$this->root_path."/script");

        $fp=fopen("/etc/WAB2/config/version","w");
        fwrite($fp,$remote_version);
        fclose($fp);
        $fp=fopen("/etc/WAB2/config/build","w");
        fwrite($fp,$remote_build);
        fclose($fp);
    }

    function findFile($search_file,$dir="") {
        if ($dir=="")
            $dir = "scripts/classes";
        if (file_exists($dir."/".$search_file)) {
            return trim(str_replace("\n","",$dir."/".$search_file)) ;
        }
        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                    if ($file!="." and $file!="..") {
                        if (is_dir($dir."/".file)) {
                            return $this->findFile($search_file,$dir."/".$file);
                        }
                    }
                }
                closedir($dh);
            }
        }
        return 0;
    }
    
    function getModuleId($moduleName) {    	
        if (!$this->initiated)
            $this->initModules();
        if (isset($this->modules[$moduleName]))
        	return $this->modules[$moduleName]["class"];
        return 0;
    }
    
    function getModuleByClass($class) {
    	if (!$this->initiated)
    		$this->initModules();
    	 foreach($this->modules as $key=>$value) {
    	 	if ($value["class"]==$class) {
    	 		$value["name"] = $key;
    	 		return $value;
    	 	}
    	 }
    	 return 0;
    }

    function getModuleNameByClass($class) {
    	if (!$this->initiated)
    		$this->initModules();
    	foreach($this->modules as $key=>$value) {
    		if ($value["class"]==$class) {
    			$value["name"] = $key;
    			return $key;
    		}
    	}
    	return 0;
    }
    
    function getModuleIp($moduleName) {
        if (!$this->initiated)
            $this->initModules();
        foreach ($this->modules as $key=>$value) {
            if ($key==$moduleName) {
            	if (isset($value["remoteAddress"])) {
            		return $value["remoteAddress"];            	
                }
            }
        }
        return 0;        
    }
    
    function changeModuleIp($moduleName,$ip) {
        global $Objects;
        $app = $this;
        if (!$app->initiated)
            $app->initModules();
        $app->user->config["modules"][$moduleName]["remoteAddress"] = $ip;
        $app->user->save();        
    }    
    
    function getHookProc($number) {
		switch ($number) {
			case '3': return "getModuleIpHook";
			case '4': return "raiseRemoteEventHook";
			case '5': return "processUser";
			case '6': return "initModules";
			case '7': return "releaseObject";
			case '8': return "checkUpdates";
			case '9': return 'update';
			case '10': return 'changeModuleIpHook';
		}
		return parent::getHookProc($number);
	}
	
	function getModuleIpHook($arguments) {
		echo $this->getModuleIp($arguments["moduleName"]);
	}
	
	function changeModuleIpHook($arguments) {
		$this->changeModuleIp($arguments["moduleName"],$arguments["ip"]);
	}
}
?>