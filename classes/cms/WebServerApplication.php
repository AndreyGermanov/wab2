<?php
/* 
 * Класс управляет Web-сервером. Web-сервер представляет из себя коллекцию Web-сайтов,
 * называемую sites, а также набор других параметров.
 *
 * Класс управляет Web-сервером Apache и сайтами внутри него.
 *
 * Функция init инициализирует основные параметры Web-приложения, это в основном
 * каталоги:
 *
 * apacheVirtualHostsDir - каталог с определениями сайтов (каждый сайт это виртуальный хост)
 * apacheConfigDir - путь к каталогу конфигурации Apache
 * sitesBasePath - Путь к каталогу, в котором размещаются каталоги сайтов
 *
 * Функция __get это автоматический загрузчик значений полей. В частности здесь он
 * используется для получения значения динамического поля sites - списка сайтов
 *
 * load() - загружает информацию о сайтах из их конфигурационных файлов
 * contains() - проверяет, существует ли сайт с указанным именем приложения
 * containsDomain() - проверяет, существует ли сайт с указанным доменным именем
 * remove() - удаляет указанный сайт
 */
class WebServerApplication extends WABEntity {
    function construct($params) {
        global $webitem_classes;
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        $this->template = "templates/cms/WebServerApplication.html";
        $this->handler = "scripts/handlers/cms/WebServerApplication.js";
        $this->object_id = @$params[0];
        $this->css=$app->skinPath."styles/MailApplication.css";
        $this->icon=$app->skinPath."images/Window/header-lva.gif";
        $webitem_classes[0] = "WebItem";
        $webitem_classes[1] = "TestWebItem";
        $webitem_classes[2] = "GuestbookWebItem";

        $this->tree_init_string = '$object->icon = "'.$app->skinPath.'images/Tree/collectormx.png";$object->title="Mystix Spiderman";$object->setTreeItems();';
        $this->init();
        
        $this->clientClass = "WebServerApplication";
        $this->parentClientClasses = "Entity";        
    }

    function init() {
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        $this->module = $app->getModuleByClass($this->getId());
        foreach ($this->module as $key=>$value)
        	$this->fields[$key] = $value;
        $this->apacheVirtualHostsDir = $this->sitesConfigDir;
        $this->sitesBasePath = $this->sitesDir;
        $this->apacheConfigDir = $this->serverConfigDir;
        $this->loaded = false;
    }

    function __get($name) {
        global $Objects;
        switch ($name) {
            case "sites":
                return $Objects->query("WebSite");
            default:
                if (isset($this->fields[$name]))
                    return $this->fields[$name];
                else
                    return "";
        }
    }

    function getId() {
        return "WebServerApplication_".$this->object_id;
    }

    function load() {
        global $Objects;
        $dir = $this->apacheVirtualHostsDir;
        $handle = opendir($dir);
        while ($file=readdir($handle)) {
            if (!is_dir($file)) {
                $file = explode(".",$file);
                array_pop($file);
                $file = implode(".",$file);
                $load = true;
                $site = $Objects->get("WebSite_".$this->getId()."_".$file);
                if ($Objects->contains("WebSite_".$this->getId()."_".$file)) {
                    if ($site->loaded)
                        $load = false;
                }
                if ($load)
                    $site->load();
            }
        }
        $this->loaded = true;
    }

    function contains($name) {
        if (!$this->loaded)
            $this->load();
        global $Objects;        
        return $Objects->contains("WebSite_".$this->getId()."_".$name);
    }

    function containsDomain($domain_name) {
        if (!$this->loaded)
            $this->load();
        global $Objects;
        $res = $Objects->query("WebSite",array("domain_name"=>$domain_name));
        if (count($res)>=0)
            return true;
        else
            return false;
    }

    function remove($name) {
        if (!$this->contains($name)) {
            $this->reportError("Сайт ".$name." не существует","remove");
            return 0;
        }
        global $Objects;

        $site = $Objects->get("WebSite_".$this->getId()."_".$name);
        $site->load();
        shell_exec("rm -rf ".$site->path);
        shell_exec("rm ".$this->apacheVirtualHostsDir.$name.".conf");
    }
}
?>