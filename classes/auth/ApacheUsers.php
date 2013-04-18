<?php
/**
 * Класс управляет списком пользователей, которые используются для авторизации
 * на сервере Apache при входе в интерфейс управления сервером.
 *
 * Этот список хранится в файле Application->ApacheUsersTable в следующем формате:
 *
 * имя:пароль
 *
 * Список хранится в коллекции $apacheUsers, которая является динамической
 * коллекцией, возвращаемой методом $Objects->query("ApacheUser").
 *
 * Класс содержит следующие методы:
 *
 * load() - загружает список пользователей из файла
 * save() - сохраняет список пользователей в файл
 * contains() - проверяет, содержится ли указанный пользователь в файле
 * add() - добавляет нового пользователя в коллекцию
 * remove() - удаляет пользователя из коллекции
 * __get() - метод для получения динамической коллекции $apacheUsers
 *
 * @author andrey
 */

class ApacheUsers extends WABEntity {

    function construct($params) {
    	global $Objects;
        $this->module_id = array_shift($params)."_".array_shift($params);
        $this->name = implode("_",$params);
        $this->loaded = false;
        $this->template = "templates/auth/ApacheUsers.html";
        $this->app = $Objects->get("Application");
        if (!$this->app->initiated)
        	$this->app->initModules();
        $this->skinPath = $this->app->skinPath;
        $this->childClass = "ApacheUser";
        $this->css = $this->skinPath."styles/Mailbox.css";
        $this->icon = $this->skinPath."images/Tree/users.gif";
        $this->itemsPerPage = 10;
        $this->sortOrder = "name";
        $this->fieldList = "name Имя~logon_time_string Время подключения~isActive Активен~active_duration Время работы";
        $this->collectionLoadMethod = "load";
        $this->collectionGetMethod = "getUsers";
        $this->tableClass = "ApacheUser";
        $this->width = "600";
        $this->height = "400";
        $this->overrided = "width,height";
        $this->clientClass = "ApacheUsers";
        $this->parentClientClasses = "Entity";
    }
    
    function afterInit($arguments) {    	
    }

    function load() {
        global $Objects,$appconfig,$userconfig;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        $config = $userconfig["appconfig"];
        if (@$config["apacheAdminsBase"]=="ldap") {
        	$con = ldap_connect($config["apacheAdminsLdapHost"],$config["apacheAdminsLdapPort"]);
        	if (!$con)
        		return 0;
        	ldap_set_option($con, LDAP_OPT_PROTOCOL_VERSION, 3);
        	$bind = ldap_bind($con,$config["apacheAdminsLdapUser"],$config["apacheAdminsLdapPassword"]);
        	if (!$bind)
        		return 0;
        	$res = ldap_list($con,$config["apacheAdminsLdapBase"],$config["apacheAdminsLdapFilter"]);
        	if (!$res)
        		return 0;
        	$entries = ldap_get_entries($con,$res);
        	$strings = array();
        	foreach($entries as $value) {
        		$strings[] = trim($value["uid"][0]).":".trim($value["userpassword"][0]); 
        	}
        } else
       		$strings = file($appconfig["apacheUsersTable"]);
        foreach ($strings as $str) {        	        	
            $parts = explode(":",$str);
            if ($parts[0]=="")
            	continue;
            $load = true;
            if ($Objects->contains("ApacheUser_".$this->module_id."_".trim($parts[0]))) {
                $user = $Objects->get("ApacheUser_".$this->module_id."_".trim($parts[0]));
                if ($user->loaded)
                    $load = false;
            }
            if ($load) {
                $user = $Objects->get("ApacheUser_".$this->module_id."_".trim($parts[0]));
                $user->password = str_replace("\n","",trim($parts[1]));
                if (isset($parts[2]))
                	$user->config_file = str_replace("\n","",trim($parts[2]));
                elseif (file_exists("/etc/WAB2/config/".trim($parts[0]).".php"))
                	$user->config_file = "/etc/WAB2/config/".trim($parts[0]).".php";
                $user->loaded_name = trim($parts[0]);
                $user->name = $user->loaded_name;
                $user->logon_time = 0;
                $user->active_time = 0;
                $user->logon_time_string = "";
                $user->active = false;
                $current_time = time();
                if (file_exists($app->variablesPath."users/".$user->name."/logon_time"))
                	$user->logon_time = filemtime($app->variablesPath."users/".$user->name."/logon_time");
                if (file_exists($app->variablesPath."users/".$user->name."/active_time"))
                	$user->active_time = filemtime($app->variablesPath."users/".$user->name."/active_time");
                if ($current_time-$user->active_time<60)
                	$user->active = true;
                if ($user->active) {
                	$user->active_duration = execAlgo("secondsToDuration",array("seconds" => $user->active_time-$user->logon_time));
                	if ($user->logon_time!="")
                		$user->logon_time_string = date("d.m.Y H:i:s",$user->logon_time);
                	$user->isActive = "Да";                	                	
                } else {
                	$user->isActive = "нет";
                	$user->active_time = "";
                	$user->active_duration = "";
                	if ($user->logon_time!="")
                		$user->logon_time_string = date("d.m.Y H:i:s",$user->logon_time);
               	}
               	if (isset($GLOBALS["bannedUsers"][$user->name])) {
               		$user->banned = "true";
               		$user->isActive = "Заблокирован";
               		$user->banButtonText = "Включить";
               	} else {
               		$user->banned = "false";
               		$user->banButtonText = "Отключить";               		 
               	}
                $user->loaded = true;
            }
        }
        $this->loaded = true;
    }

    function save() {
        global $Objects;
        $app = $Objects->get("Application");
        $fp = fopen($app->apacheUsersTable,"w");
        foreach($this->apacheUsers as $user) {
        	if ($user->module_id==$this->module_id and trim($user->password)!="") {
            	fwrite($fp,trim($user->name).":".trim($user->password)."\n");
            	$user->loaded = true;
            	$user->loaded_name = $user->name;
        	}
        }
        fclose($fp);
        $this->loaded = true;
    }

    function contains($name) {
        if (!$this->loaded)
                $this->load();
        global $Objects;
        return $Objects->contains("ApacheUser_".$this->module_id."_".$name);
    }

    function add($name,$password) {
        global $Objects;
        if ($Objects->contains("ApacheUser_".$this->module_id."_".trim($name))) {
            reportError("Пользователь ".trim($name)." уже существует!","save");
            return 0;
        }
        $user=$Objects->get("ApacheUser_".$this->module_id."_".trim($name));
        $user->password = trim($password);
        return $user;
    }

    function remove($name) {
        global $Objects,$appconfig;
        if (is_array($name)) {
			$this->load();
			$name = $name["name"];
		}
        if (!$this->contains(trim($name))) {
            $this->reportError("Пользователь ".trim($name)." не существует","remove");
            return 0;
        }
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        $app->raiseRemoteEvent("APACHEUSER_DELETED","object_id="."ApacheUser_".$this->module_id."_".trim($name));
        $shell = $Objects->get("Shell_shell");
        $shell->exec_command($app->deleteCommand." ".$Objects->get("ApacheUser_".$this->module_id."_".trim($name))->config_file);
        $shell->exec_command($app->deleteCommand." ".$Objects->get("ApacheUser_".$this->module_id."_".trim($name))->settings_file);
        $shell->exec_command($app->deleteCommand." -rf ".$app->variablesPath."users/".$name);
        $Objects->remove("ApacheUser_".$this->module_id."_".trim($name));
        if (@$appconfig["apacheAdminsBase"]!="ldap")
        	$this->save();
    }

    function __get($name) {
        global $Objects;
        switch ($name) {
            case "apacheUsers":
                return $Objects->query("ApacheUser");
                break;
            default:
                if (isset($this->fields[$name]))
                        return $this->fields[$name];
                else
                    return "";
        }
    }
    
    function getUsers() {
    	if (!$this->loaded)
    		$this->load();
    	$result = array();
    	foreach ($this->apacheUsers as $key=>$value) {  		
   			$result[$key] = $value;
    	}
    	return $result;
    }
    
    function getHookProc($number) {
		switch($number) {
			case '3': return "remove";
		}
		return parent::getHookProc($number);
	}
}
?>