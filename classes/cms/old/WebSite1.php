<?php
/* 
 * Класс управляет информацией о Web-сайте. Для каждого Web-сайта создается файл
 * в каталоге WebServerApplication->apacheVirtualHostsConfigDir (по умолчанию
 * /etc/httpd/conf/vhosts.d) с информацией о сайте. В него записываются свойства
 * этого объекта:
 *
 * name - имя приложения сайта (именно по этому имени сайт идентифицируется в системе)
 * domain_name - имя домена, на которое отзывается сайт
 * alias - псевдонимы сайта
 * port - порт, который будет слушать сайт
 * path - путь к файлам сайта
 * is_ssl - будет ли сайт защищен SSL
 * is_auth - необходима ли будет аутентификация на сайте
 * main_page - название основного раздела сайта, который загружается по умолчанию
 *             при открытии сайта
 *
 * db_type - тип базы данных
 * dh_host - хост и порт базы данных
 * db_name - имя базы данных
 * db_user - имя пользователя для доступа к базе данных
 * db_password - пароль для доступа к базе данных
 *
 * При необходимости создается каталог сайта и в нем создаются все необходимые
 * первоначальные файлы и каталоги, чтобы сайт мог подключаться к базе данных
 * и получать информацию о разделах, элементах и шаблонах их отображения.
 *
 * Также в этом классе производится подключение сайта к базе данных с помощью
 * ORM Doctrine
 * 
 * Для управления сайтом существуют методы
 *
 * load() - загружает информацию о сайте из файла
 * save() - сохраняет информацию о сайте в файл
 * getId() - получает идентификатор сайта
 * getPresentation() - получает строковое представление наименования сайта
 *
 * connect() - подключается к базе данных и возвращает ссылку на соединение
 * show() - отображает страницу сайта по умолчанию
*/

class WebSite extends WABEntity {

    public $db_types;
    function construct($params) {

	if (count($params)>2 and $params[0]!="") {
	        $this->module = $params[0]."_".$params[1];	
	        $this->name = @$params[2];
	}
	else {
		$this->module = "";
		$this->name = $params[0];
	}
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        $this->module_id = $this->module;
        $this->domain_name = "";
        $this->port = 80;
        $this->db_type = "mysql";
        $this->db_host = "localhost";
        $this->is_ssl = false;
        $this->is_auth = false;
        $this->path = "/var/www";
        $this->loaded = false;
        $this->db_name = "";
        $this->db_user = "";
        $this->alias = "";
        $this->db_password = "";
        $this->auth_file = "";
        $this->connection = "";
        $this->main_page = "";
        $this->template = "templates/WebSite.html";
        $this->handler = "scripts/handlers/Mailbox.js";
        $this->css = $app->skinPath."styles/Mailbox.css";
        $this->icon = $app->skinPath."images/Tree/sites.gif";
        $this->skinPath = $app->skinPath;
        $this->overrided = "width,height";
        $this->width = 500;
        $this->height = 425;
        $this->db_types = array();
        $this->db_types["fbsql"] = "FrontBase";
        $this->db_types["ibase"] = "Interbase / Firebird";
        $this->db_types["mssql"] = "Microsoft SQL Server";
        $this->db_types["mysql"] = "MySQL";
        $this->db_types["oci"] = "Oracle 7/8/9/10";
        $this->db_types["pgsql"] = "PostgreSQL";
        $this->db_types["sqlite"] = "SQLite 2";        
    }

    function load() {
        global $Objects;                      
	
        $vhosts_path = @$_SERVER["CONFIG_FILE_PATH"];
	if ($this->module!="") {
	        $app = $Objects->get($this->module);
	        $vhosts_path = $app->apacheVirtualHostsDir;
	}
        if (!file_exists($vhosts_path.$this->name.".conf"))
            return 0;
        //echo "site - ".$vhosts_path.$this->name.".conf<br>";
        $strings = file($vhosts_path.$this->name.".conf");
        foreach($strings as $line) {
            $matches = array();
            if (preg_match("/\<VirtualHost \*:(.*)\>/",$line,$matches)==1)
                $this->port = trim($matches[1]);
            $matches = array();
            if (preg_match("/ServerName (.*)/",$line,$matches)==1)
                $this->domain_name = trim($matches[1]);
            $matches = array();
            if (preg_match("/ServerAlias (.*)/",$line,$matches)==1)
                $this->alias = trim($matches[1]);
            $matches = array();
            if (preg_match('/AuthUserFile "(.*)"/',$line,$matches)==1)
                $this->auth_file = trim($matches[1]);
            $matches = array();
            if (preg_match("/SSLEngine (.*)/",$line,$matches)==1) {
                if (trim($matches[1])=="On")
                    $this->is_ssl = true;
            }
            $matches = array();
            if (preg_match("/(.*)AuthType/",$line,$matches)==1) {
                if (trim($matches[1])=="")
                    $this->is_auth = true;
            }
            $matches = array();
            if (preg_match('/#db_type (.*)/',$line,$matches)==1)
                $this->db_type = trim($matches[1]);
            $matches = array();
            if (preg_match('/#db_name (.*)/',$line,$matches)==1)
                $this->db_name = trim($matches[1]);
            $matches = array();
            if (preg_match('/#db_host (.*)/',$line,$matches)==1)
                $this->db_host = trim($matches[1]);
            $matches = array();
            if (preg_match('/#db_user (.*)/',$line,$matches)==1)
                $this->db_user = trim($matches[1]);
            $matches = array();
            if (preg_match('/#db_password (.*)/',$line,$matches)==1)
                $this->db_password = trim($matches[1]);
            $matches = array();
            if (preg_match('/#app_name (.*)/',$line,$matches)==1) {
                $this->name = trim($matches[1]);
		if ($this->module!="")
	                $this->path = $app->sitesBasePath.$this->name;
		else
	                $this->path = $_SERVER["DOCUMENT_ROOT"].$this->name;
            }
            $matches = array();
            if (preg_match('/#main_page (.*)/',$line,$matches)==1)
                $this->main_page = trim($matches[1]);
        }
        $this->loaded = true;
        $this->loaded_name = $this->name;
        $this->loaded_domain_name = $this->domain_name;
        return $this;
    }

    function save() {
        global $Objects;
        $app = $Objects->get($this->module);
        $vhosts_path = $app->apacheVirtualHostsDir;
        if ($this->name!=$this->loaded_name) {
            if ($app->contains($this->name))
                if ($Objects->get("WebSite_".$this->module."_".$this->name)->loaded) {
                    $this->reportError("Сайт с указанным именем уже существует!","save");
                    return 0;
                }
                $Objects->set("WebSite_".$this->module."_".$this->name,$this);
                $Objects->remove("WebSite_".$this->module."_".$this->loaded_name);
        }

        if ($this->domain_name!=$this->loaded_domain_name) {
            $res = $Objects->query("WebSite",array("domain_name"=>$this->domain_name));
            if (count($res)>0) {
                $res = array_shift($res);
                if ($Objects->get("WebSite_".$this->module_name."_".$res->name)->loaded && $res->name != $this->name){
                    $this->reportError("Сайт с указанным доменным именем уже существует!","save");
                    return 0;
                }
            }
        }


        $this->fields["path"] = $app->sitesBasePath.$this->name;
        if ($this->fields["path"]!=$app->sitesBasePath.$this->loaded_name) {
            if (file_exists($app->sitesBasePath.$this->loaded_name))
                    shell_exec("mv ".$app->sitesBasePath.$this->loaded_name." ".$this->fields["path"]);
            shell_exec("rm ".$app->apacheVirtualHostsDir.$this->loaded_name.".conf");
        }
        
        $tpl = file_get_contents("templates/site/VirtualHost.html");
        $arr = $this->getArgs();
        if ($this->is_ssl)
                $arr["{is_ssl}"] = "On";
        else
                $arr["{is_ssl}"] = "Off";
        if ($this->is_auth)
                $arr["{is_auth}"] = "";
        else
                $arr["{is_auth}"] = "#";
        $arr["{config_file_path}"] = $vhosts_path;
        $tpl = strtr($tpl,$arr);

        $arr = explode("\n",$tpl);
        $fp = fopen($vhosts_path.$this->name.".conf","w");
        foreach($arr as $line) {
            fwrite($fp,$line."\n");
        }
        fclose($fp);

        if (!file_exists($this->path)) {
            shell_exec('mkdir -p '.$this->path);
            shell_exec('ln -s '.$_SERVER["DOCUMENT_ROOT"]."/content ".$this->path."/content");
            shell_exec('ln -s '.$_SERVER["DOCUMENT_ROOT"]."/classes ".$this->path."/classes");
            shell_exec('ln -s '.$_SERVER["DOCUMENT_ROOT"]."/Doctrine ".$this->path."/Doctrine");
            shell_exec('ln -s '.$_SERVER["DOCUMENT_ROOT"]."/images ".$this->path."/images");
            shell_exec('ln -s '.$_SERVER["DOCUMENT_ROOT"]."/scripts ".$this->path."/scripts");
            shell_exec('ln -s '.$_SERVER["DOCUMENT_ROOT"]."/styles ".$this->path."/styles");
            shell_exec('ln -s '.$_SERVER["DOCUMENT_ROOT"]."/skins ".$this->path."/skins");
            shell_exec('ln -s '.$_SERVER["DOCUMENT_ROOT"]."/config ".$this->path."/config");
            shell_exec('ln -s '.$_SERVER["DOCUMENT_ROOT"]."/templates ".$this->path."/templates");
            shell_exec('ln -s '.$_SERVER["DOCUMENT_ROOT"]."/tools ".$this->path."/tools");
            shell_exec('cp '.$_SERVER["DOCUMENT_ROOT"]."/templates/site/index.php ".$this->path);
        }
        $this->loaded = true;
        $this->loaded_name = $this->name;
        $this->loaded_domain_name = $this->domain_name;

    }

    function show($is_item=false) {
        global $Objects;
        if ($is_item) {
            if (!$this->loaded)
                $this->load();

	    if ($this->module!="")		
	            $item = $Objects->get("WebItem_".$this->module."_".$this->name."_".$this->main_page);
	    else
	            $item = $Objects->get("WebItem_".$this->name."_".$this->main_page);
            $item->load();
            $item->show();
        }
        else
            parent::show();
    }

    function getId() {
	if ($this->module!="")
	        return "WebSite_".$this->module."_".$this->name;
	else
		    return "WebSite_".$this->name;
    }

    function getPresentation() {
        if (!$this->loaded)
            $this->load();
        return $this->domain_name;
    }

    function connect() {
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
         $servers = array(
              'host'        => 'localhost',
              'port'        => 11211,
              'persistent'  => true
            );
            $cacheDriver = new Doctrine_Cache_Memcache( array(
              'servers'     => $servers,
              'compression' => false
            ));
        date_default_timezone_set("Europe/Moscow");
        $manager = Doctrine_Manager::getInstance();
            $manager->setAttribute( Doctrine::ATTR_QUERY_CACHE, $cacheDriver );
            $manager->setAttribute( Doctrine::ATTR_RESULT_CACHE, $cacheDriver);
        if ($this->connection!="") {
            $manager->setCurrentConnection($this->connection->getName());
            return $this->connection;
        }

        if (!$this->loaded)
            $this->load();
        $this->connection = Doctrine_Manager::connection($this->db_type."://".$this->db_user.":".$this->db_password."@".$this->db_host."/".$this->db_name,$this->name."<br>");
        try {
            $this->connection->createDatabase();
            Doctrine::createTablesFromModels('classes/site');
            $root = new WebItemRecord();
            $root = $root->getTable()->find(1);
            if (@$root->id!=1) {
                $root = new WebItemRecord();
                $root->name = "root";
                $root->class = "WebItem";
                $root->item_class = "WebItem";
                $root->icon = $app->skinPath."images/Tree/folder.gif";
                $root->item_icon = $app->skinPath."images/Tree/item.gif";
                $root->admin_template = 49;
                $root->admin_list_template = 50;
                $root->admin_item_template = 49;
                $root->getTable()->getTree()->createRoot($root);
                $root = new WebItemMetadataRecord();
                $root->name = "root";
                $root->is_group = true;
                $root->getTable()->getTree()->createRoot($root);
            }
        } catch (Exception $e) {
        }

        $manager->setCurrentConnection($this->connection->getName());
        return $this->connection;
    }

    function getArgs() {
        $result = parent::getArgs();
        if ($this->is_auth)
            $result["{is_auth_checked}"] = "checked";
        else
            $result["{is_auth_checked}"] = "";
        if ($this->is_ssl)
            $result["{is_ssl_checked}"] = "checked";
        else
            $result["{is_ssl_checked}"] = "";

        $db_types_keys = array_keys($this->db_types);
        $db_types_values = array_values($this->db_types);
        $result["{db_types}"] = implode(",",$db_types_keys)."|".implode(",",$db_types_values);
        return $result;
    }
}
?>