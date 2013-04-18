<?php
/**
 * Класс реализует сервер WebDAV. 
 * 
 * Сервер WebDAV управляет файлом /etc/apache2/conf.d/davfolders.conf.
 * Этот файл хранит настройки домашних папок пользователей, которые
 * доступны через WebDAV.
 * 
 * Работа сервера WebDAV зависит от работы сервера FTP (класс FTP-сервер).
 * Сервер WebDAV просто реализует доступ к структуре папок, доступной
 * по FTP через WebDAV.
 * 
 * Метод getFolders() получает все общие папки WebDAV в виде массива,
 * в котором ключ это имя пользователя, а значение это домашняя папка
 * FTP данного пользователя.
 * 
 * Метод setFolders() записывает список общих папок WebDAV из массива
 * в файл /etc/apache2/conf.d/davfolders.conf, используя для каждой папки
 * шаблон из файла templates/controller/davfolders.conf
 *   
 * @author andrey 20.06.2012
 */
class DAVServer extends WABEntity {
    
    function construct($params) {
        $this->module_id = $params[0]."_".$params[1];
        $this->name = $params[2];
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        $this->app = $app;
        $this->Shell = $Objects->get("Shell_shell");

        $this->skinPath = $app->skinPath;
                
        global $Objects;
        $app = $Objects->get($this->module_id);
        $this->app = $app;
        
        foreach ($app->module as $key=>$value)
        	$this->fields[$key] = $value;        
        
        $capp = $Objects->get($this->module_id);
        $this->capp = $capp;

        $this->clientClass = "DAVServer";
        $this->parentClientClasses = "Entity";        
    }

    function getArgs() {
        $result = parent::getArgs();
        return $result;        
    }
   
    function getId() {
        return "DavServer_".$this->module_id."_".$this->name;
    }
    
    function getPresentation() {
        return l10n("Dav-сервер");
    }   

    /**
     * Записывает переданный массив папок в
     * файл /etc/apache2/conf.d/davfolders.conf.
     * 
     * @param array $folders массив, ключ которого это имя пользователя, а значение это путь к его домашней папке.
     */
    function setFolders($folders) {
		global $Objects;
		$tpl = file_get_contents($this->davFoldersTemplateFile);
		$fileServer = $Objects->get("FileServer_".$this->module_id."_Shares");
		if (!$fileServer->loaded)
			$fileServer->load();
		$result = "";
		foreach ($folders as $key=>$value) {
			if ($key=="")
				continue;
			$args = array("{name}" => $key, "{ftpHome}" => $value, "{ldap_base}" => $fileServer->ldap_base);
			$result .= strtr($tpl,$args)."\n";
		}
		file_put_contents($this->capp->remotePath.$this->davFoldersConfigFile,$result);
		$this->restart();
	}
	
	/**
	 * Считывает файл /etc/conf.d/davfolders и возвращает из него домашние папки DAV каждого пользователя в виде массива
	 * @return array : ключ - имя пользователя, значение - путь к домашней папке Интернет этого пользователя
	 */
	function getFolders() {
		global $Objects;
		$folders = array();
		$strings = file($this->capp->remotePath.$this->davFoldersConfigFile);
		foreach ($strings as $line) {
			$matches = array();
			if (preg_match("/alias.url \= \(\/(\S+) \=\> (.*)/",$line,$matches)) {
				if (trim($matches[1])!="")
					$folders[trim($matches[1])] = trim($matches[2]);
			}
		}
		return $folders;
	}
	
	/**
	 * Перезапускает Web-сервер
	 *  
	 */
	function restart() {
		global $Objects;
		$app = $Objects->get("Application");
		if (!$app->initiated)
			$app->initModules();
		$shell = $Objects->get("Shell_shell");
		$shell->exec_command($app->apacheRestartCommand);
	}

}
?>