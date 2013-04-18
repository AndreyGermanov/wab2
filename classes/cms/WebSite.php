<?php
/**
 * Сущность, реализующая форму настроек Web-сайта
 *
 */
class WebSite extends WABEntity {    
    function construct($params) {                
        parent::construct($params);
        global $Objects;
        if ($this->module_id=="") {
            $this->adapter = $Objects->get("PDODataAdapter_".get_class($this)."_".$this->name);
        }
        else {
            $this->adapter = $Objects->get("PDODataAdapter_".$this->module_id."_".get_class($this)."_".$this->name);
        }
     
        if ($this->module_id!="") {
            $this->webapp = $Objects->get($this->module_id);
        }
        else {
            $this->webapp = $Objects->get(@$_SERVER["MODULE_ID"]);  
        }
        if ($this->webapp=="")
        	$this->webapp = "WebServerApplication_Web";
        @$this->adapter->path = @$this->webapp->sitesDB;
        $this->template = "templates/cms/WebSite.html";
        $this->handler = "scripts/handlers/cms/WebSite.js";
        //echo $this->getId()."-".$this->adapter->path;
        $this->title = "";
        $this->mainpage = "";
        $this->port = 80;
        $this->db_type = "mysql";
        $this->db_host = "localhost";
        $this->is_ssl = 0;
        $this->is_auth = 0;
        $this->is_cached = 0;
        $this->path = "/var/www";
        $this->loaded = false;
        $this->db_name = "";
        $this->db_user = "";
        $this->alias = "";
        $this->db_password = "";
        $this->path = "";
        $this->fieldList = 'presentation Наименование strings';
        $this->presentationField = "title";       

        $this->clientClass = "WebSite";
        $this->parentClientClasses = "Entity";        
    }
            
    function getArgs() {
        $this->icon = $this->skinPath."images/Tree/sites.gif";
        $this->document_root = $this->webapp->sitesBasePath.$this->name;
        if ($this->is_auth) {
        	if ($this->webapp->serverType=="apache")
            	$this->auth = "AuthType Basic\nAuthName \"Secured site\"\nAuthUserFile \"{auth_file}\"\nRequire valid-user";
        	elseif ($this->webapp->serverType=="lighttpd")
        		$this->auth = 'auth.require = ( "/" => ( "method" => "basic", "realm" => "Mystix Spiderman", "require" => "valid-user"))';
        }
        else
            $this->auth = "";
        if ($this->is_ssl) {
        	if ($this->webapp->serverType=="apache")
            	$this->ssl = "SSLEngine on\nSSLCertificateFile    /etc/ssl/certs/ssl-cert-snakeoil.pem\nSSLCertificateKeyFile /etc/ssl/private/ssl-cert-snakeoil.key";
        	elseif ($this->webapp->serverType=="lighttpd")
        		$this->ssl = 'ssl.engine  = "enable"\nssl.pemfile = "/etc/lighttpd/ssl-cert-snakeoil.pem"\nssl.cipher-list = "ECDHE-RSA-AES256-SHA384:AES256-SHA256:RC4:HIGH:!MD5:!aNULL:!EDH:!AESGCM"\nssl.honor-cipher-order = "enable"';        	 
        } else {
            $this->ssl = "";
        }
        $this->webapp_module_id = $this->module_id;
        if ($this->db_type=="pdo_sqlite") {
            $this->sqlite_display = "";
            $this->db_display = "none";
        } else {
            $this->sqlite_display = "none";
            $this->db_display = "";            
        }
        if ($this->module_id!="")
        $this->mainpage = array_shift(explode("_",$this->mainpage))."_".$this->module_id."_".$this->name."_".array_pop(explode("_",$this->mainpage));
        $result = parent::getArgs();
        return $result;
    }      
    
    function checkData() {
        if ($this->title=="") {
            $this->reportError("Укажите доменное имя","save");
            return false;
        }
        if ($this->port=="") {
            $this->reportError("Укажите порт сервера","save");
            return false;
        }
        if ($this->db_type!="pdo_sqlite") {
            if ($this->db_host=="") {
                $this->reportError("Укажите хост, на котором расположена база данных","save");
                return false;
            }
            if ($this->db_name=="") {
                $this->reportError("Укажите имя базы данных","save");
                return false;
            }
            if ($this->db_user=="") {
                $this->reportError("Укажите имя пользователя","save");
                return false;
            }
            if ($this->db_password=="") {
                $this->reportError("Укажите пароль","save");
                return false;
            }
        } else {
            if ($this->path=="") {
                $this->reportError("Укажите путь к файлу с базой данных","save");
                return false;
            }            
        }
        global $Objects;
        if ($this->module_id!="")
            $className = get_class($this)."_".$this->module_id;
        else
            $className = get_class($this);
        if ($this->name=="")
            $name = 0;
        else
            $name = $this->name;
        $results = $Objects->query($className,"@title='".$this->title."' AND @name!=".$name,$this->adapter);
        if (count($results)>0) {
            $this->reportError("Указанное 'Доменное имя' уже используется сайтом ".$results[0]->title,"save");
            return false;
        }
        
        if ($this->mainpage=="-1")
            $this->mainpage = "";
        
        $this->mainpage = array_shift(explode("_",$this->mainpage))."_".array_pop(explode("_",$this->mainpage));
        return true;        
    }
    
    function afterSave() {
        global $Objects;
        if ($this->module_id!="")
            $this->webapp = $Objects->get($this->module_id);
        else
            $this->webapp = $Objects->get(@$_SERVER["MODULE_ID"]);  
        file_put_contents($this->webapp->apacheVirtualHostsDir.$this->name.".conf", strtr(file_get_contents("templates/site/VirtualHost.html"),$this->getArgs()));
        $dir = $this->webapp->sitesBasePath.$this->name;
        $shell = $Objects->get("Shell_shell");
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        if (!file_exists($dir)) {
            $shell->exec_command($app->makeDirCommand." -p ".$dir);
            $shell->exec_command($app->lnCommand." -s ".$_SERVER["DOCUMENT_ROOT"]."/classes ".$dir."/classes");
            $shell->exec_command($app->lnCommand." -s ".$_SERVER["DOCUMENT_ROOT"]."/content ".$dir."/content");
            $shell->exec_command($app->lnCommand." -s ".$_SERVER["DOCUMENT_ROOT"]."/scripts ".$dir."/scripts");
            $shell->exec_command($app->lnCommand." -s ".$_SERVER["DOCUMENT_ROOT"]."/styles ".$dir."/styles");
            $shell->exec_command($app->lnCommand." -s ".$_SERVER["DOCUMENT_ROOT"]."/skins ".$dir."/skins");
            $shell->exec_command($app->lnCommand." -s ".$_SERVER["DOCUMENT_ROOT"]."/tools ".$dir."/tools");
            $shell->exec_command($app->lnCommand." -s ".$_SERVER["DOCUMENT_ROOT"]."/templates ".$dir."/templates");
            $shell->exec_command($app->lnCommand." -s ".$_SERVER["DOCUMENT_ROOT"]."/metadata ".$dir."/metadata");
            $shell->exec_command($app->lnCommand." -s ".$_SERVER["DOCUMENT_ROOT"]."/roles ".$dir."/roles");
            $shell->exec_command($app->lnCommand." -s ".$_SERVER["DOCUMENT_ROOT"]."/boot.php ".$dir."/boot.php");            
            $shell->exec_command($app->lnCommand." -s ".$_SERVER["DOCUMENT_ROOT"]."/scripts.php ".$dir."/scripts.php");            
            $shell->exec_command($app->copyCommand." ".$_SERVER["DOCUMENT_ROOT"]."/templates/site/index.php ".$dir."/");            
            $shell->exec_command($app->chownCommand." -R ".$app->apacheServerUser." ".$dir);
        }
    }
    
    function remove() {
        global $Objects;
        if ($this->siteAdapter=="") {
            if ($this->module_id!="") {
                $this->siteAdapter = $Objects->get("SiteDataAdapter_".$this->module_id."_".$this->name."_".$this->name);                
            } else {
                $this->siteAdapter = $Objects->get("SiteDataAdapter_".$this->name."_".$this->name);                                
            }
        }    
        $result = array();
		$result = @$Objects->query("WebEntity_".$this->module_id,"@parent IS NOT EXISTS AND @siteId=".$this->name,$this->siteAdapter,"");
        if ($result!=0 and count($result)>0) {
            $this->reportError("На этом Web-сайте еще есть разделы. Их необходимо предварительно удалить","remove");
            return 0;
        }
        parent::remove();
    }
    
    function afterRemove() {
        global $Objects;
        if ($this->module_id!="")
            $this->webapp = $Objects->get($this->module_id);
        else
            $this->webapp = $Objects->get(@$_SERVER["MODULE_ID"]);  
        $dir = $this->webapp->sitesBasePath.$this->name;
        $shell = $Objects->get("Shell_shell");
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        $shell->exec_command($app->deleteCommand." -rf ".$dir);
        $shell->exec_command($app->deleteCommand." ".$this->webapp->apacheVirtualHostsDir.$this->name.".conf");
        if ($this->siteAdapter->driver=="pdo_sqlite") {
            $shell->exec_command($app->deleteCommand." ".$this->siteAdapter->path);
        }
    }   
}
?>