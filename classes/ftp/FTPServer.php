<?php
/**
 * Класс реализует FTP-сервер. Он хранит настройки FTP-сервера и
 * позволяет с ними работать.
 *
 * FTP-сервер реализован на базе сервера proftpd. Концепция работы файлового
 * сервера заключается в следующем: для каждого пользователя, которому явно не
 * запрещен доступ к FTP-серверу (если он не указан в файле /etc/ftpusers), 
 * создается папка ftp в домашней папке, которая является его корневой FTP-папкой, 
 * в которую он попадает при подключении по FTP. 
 * 
 * В окне свойств пользователя, на закладке "Права доступа" указывается путь
 * к домашней папке FTP для данного пользователя. Он указывает на любую папку
 * в файловой системе. Это не обязательно должна быть папка ~/ftp. Если папка
 * не указана, то используется ~/ftp, а если указана другая папка, то она
 * монтируется поверх ~/ftp с помощью команды mount --rbind. Список команд
 * mount --rbind, которые монтируют указанные папки в качестве домашних папок
 * пользователей FTP находятся в файле /root/ftphomes.sh. Каждая строка имеет
 * формат:
 * 
 * mount --rbind <имя-папки> /<домашняя папка пользователя>/ftp
 * 
 * Данный класс предоставляет сервисы по чтению и записи этого файла и для 
 * предоставления информации о содержимом этого файла различным классам.
 * 
 * Функция getHomes() возвращает этот список в виде хэш-таблицы, в которой
 * в качестве ключей используются домашние папки пользователей, а в качестве
 * значений FTP-домашние папки пользователей.
 * 
 * Если пользователь изменяет FTP-домашнюю папку, процедура записи свойств
 * пользователя модифицирует соответствующее значение этого массива и передает
 * модифицированный массив функции FTPServer::setHomes(), которая сохраняет
 * его. Аналогичное происходит если пользователь меняет имя своей домашней папки.
 * 
 * Существует возможность предоставлять доступ пользователей не только к своей
 * домашней папке, но и к любым другим общим папкам на сервере. У любой общей
 * папке н сервере есть свойство FTPFolder. Если оно установлено в "1", считается
 * что эта папка доступна по FTP. Это можно установить в файловом менеджере,
 * открыв окно свойств папки, перейдя на закладку "Права доступа", затем на
 * закладку "Через Интернет" и включив флажок "Опубликовать по FTP".
 * 
 * Если папка сделана общей по FTP, к ней автоматически получают доступ по FTP все
 * пользователи, которые имеют права доступа к этой папке на уровне файловой
 * системы. Происходит это следующим образом: если пользователь имеет права доступа
 * на чтение или на запись к папке, которая опубликована по FTP, эта папка
 * монтируется в его домашнюю FTP-папку командой mount --rbind. Все такие команды
 * mount --rbind для общих FTP-папок записываются в файл /root/ftpfolders.sh. Список
 * формируется автоматически командой getFtpFolders() и сохраняется в этот файл
 * командой setFTPFolders(). Этот файл полностью перегенерируется каждый раз
 * при изменении домашней папки пользователя, домашней FTP-папки пользователя,
 * при изменении прав доступа пользователей к FTP-папке, при публикации новой
 * FTP-папки, при снятии FTP-публикации с папки, при изменении названия или
 * пути к общей папке.
 * 
 * Формат строки следующий
 * 
 * mount --rbind /<путь-к-общей-папке> /<домашняя папка пользователя>/ftp/<имя-общей-папки>
 *
 * Список пользователей, которым запрещен доступ к FTP-серверу находится в
 * файле /etc/ftpusers. Этот список в виде массива можно получить функцией
 * getDeniedUsers(), модифицировать и сохранить обратно функцией setDeniedUsers().
 * 
 * FTP-сервер представляет из себя набор виртуальных хостов, которые описаны в файле
 * /etc/proftpd/virtuals.conf в директивах <VirtualHost>. Как минимум, каждый FTP-сервер
 * содержит единственный виртуальный хост, привязанный к порту 21 и считается виртуальным
 * хостом по умолчанию.
 *
 * В данном классе содержится метод getHosts(), позволяющий получить список виртуальных хостов,
 * которые находятся в этом файле, а также метод setHosts(), который записывает список хостов
 * в этот файл. Список хостов и их параметров передается этому методу в виде массива.
 *
 * Управление виртуальными хостами выполняется в классе FTPHost, который получает информацию
 * о своем хосте из массива, возвращаемого методом getHosts(), может модифицировать настройки
 * своего хоста и передавать его обратно в составе массива, передаваемого методу setHosts(),
 * сохраняющего параметры хостов в файл /etc/proftpd/virtuals.conf.
 *
 * Также, на уровне каждого виртуального хоста определяется максимальная пропускная способность
 * порта, к которому подключен данный виртуальный хост. Это делается на уровне операционной
 * системы, с помощью утилиты tc (Traffic Shaping). Команды tc для каждого виртуального хоста
 * находятся в файле /root/ftpshaping.sh. Этот файл также используется в функциях getHosts() и
 * setHosts() для получения и записи пропускной способности порта. Максимальная скорость указывается
 * в килобитах в секунду.
 * 
 * @author andrey 20.06.2012
 */
class FTPServer extends WABEntity {
    
    public $denyUsers = array();
    public $ftpHomes = array();

    function construct($params) {
        $this->module_id = $params[0]."_".$params[1];
        $this->name = $params[2];
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        $this->app = $app;
        $this->Shell = $Objects->get("Shell_shell");
        $this->template = "templates/ftp/FTPServer.html";
        $this->css = $app->skinPath."styles/Mailbox.css";
        $this->handler = "scripts/handlers/ftp/FTPServer.js";

        $this->icon = $app->skinPath."images/Tree/ftpserver.png";
        $this->skinPath = $app->skinPath;
                
        global $Objects;
        $app = $Objects->get($this->module_id);
        $this->app = $app;
                
        $capp = $Objects->get($this->module_id);
        $this->capp = $capp;
        
        foreach($this->capp->module as $key=>$value)
        	$this->fields[$key] = $value;
        
        $this->width = "500";
        $this->height = "500";
        $this->overrided = "width,height";
        $this->clientClass = "FTPServer";
        $this->parentClientClasses = "Entity";        
        $this->classTitle = "FTP-сервер";
        $this->classListTitle = "FTP-сервер";
    }

    function getArgs() {
        $result = parent::getArgs();
        return $result;        
    }

    function getId() {
        return "FTPServer_".$this->module_id."_".$this->name;
    }
    
    function getPresentation() {
        return l10n("FTP-сервер");
    }
    
    function getDenyUsers() {
        $arr = file($this->app->remotePath.$this->fTPUsersFile);
        $result = array();
        foreach ($arr as $value) {
            $result[trim($value)] = trim($value);
        }
        $this->denyUsers = $result;
        return $result;
    }
    
    function setDenyUsers($denyUsers = "") {
        if ($denyUsers=="")
            $denyUsers = $this->denyUsers;
        if (count($denyUsers)==0)
            return 0;
        file_put_contents($this->app->remotePath.$this->fTPUsersFile,implode("\n",$denyUsers));
    }
    
    function getHomes() {
        $arr = file($this->app->remotePath.$this->fTPUserHomesMountsFile);
        $result = array();
        foreach ($arr as $value) {
            $matches = array();
            if (preg_match("~^mount --rbind '(/.*)' '(/.*)/ftp'~",$value,$matches)) {
                $result[trim($matches[2])] = trim($matches[1]);
            }
        }
        $this->ftpHomes = $result;
        return $result;
    }
    
    function getBindMounts() {
        $result = array();
        $strings = explode("\n",$this->Shell->exec_command($this->capp->remoteSSHCommand." mount"));
        foreach ($strings as $line) {
            $arr = explode(" ",$line);
            array_pop($arr);array_pop($arr);array_pop($arr);
            $line = trim(implode(" ",$arr));
            $result[$line] = $line;
        }
        return $result;
    }
    
    function setHomes($ftpHomes = "NIL") {        
        if ($ftpHomes=="NIL")
            $ftpHomes = $this->ftpHomes;
        $folders = $this->getFolders();
        $this->unsetFolders($folders);
        $fp = fopen($this->capp->remotePath."/tmp/tmpftphomes.sh","w");
        foreach ($this->ftpHomes as $key=>$value) {
            fwrite($fp,"umount -l '".$key."/ftp'\n");
        }
        fclose($fp);
        if ($this->capp->remoteSSHCommand=="")
            $this->Shell->exec_command("sh /tmp/tmpftphomes.sh");
        else
            shell_exec($this->capp->remoteSSHCommand." sh /tmp/tmpftphomes.sh");
        $fp = fopen($this->app->remotePath.$this->fTPUserHomesMountsFile,"w");
        $mounts = $this->getBindMounts();
        foreach ($ftpHomes as $key => $value) {
            if (!isset($mounts[$value." on /data".$key."/ftp"]))
                fwrite($fp,str_replace("//","/","mount --rbind '".$value."' '".$key."/ftp'\n"));
        }
        fclose($fp);        
        if ($this->capp->remoteSSHCommand=="")
            $this->Shell->exec_command("sh ".$this->fTPUserHomesMountsFile);
        else
            shell_exec($this->capp->remoteSSHCommand." sh ".$this->fTPUserHomesMountsFile);
        $fp = fopen($this->app->remotePath.$this->fTPUserHomesMountsFile,"w");
        foreach ($ftpHomes as $key => $value) {
            fwrite($fp,str_replace("//","/","mount --rbind '".$value."' '".$key."/ftp'\n"));
        }
        fclose($fp);        
    }

    function getAcl($user,$type,$text,$is_default=false) {
        if ($is_default)
            $search_string = "default:";
        else
            $search_string = "";
        $search_string .= $type.":";
        $search_string .= $user.":";        
        foreach ($text as $line) {
            if (strpos($line,$search_string)!==FALSE) {
                $res = array_pop(explode(":",$line));
                $result = array();
                if ($res[0]=="r")
                    $result["r"] = "r";
                if ($res[1]=="w")
                    $result["w"] = "w";
                if ($res[2]=="x")
                    $result["x"] = "x";
                return $result;
            }
        }
        return 0;
    }
    
    function getFolders() {
        global $Objects;
        $fileServer = $Objects->get("FileServer_".$this->module_id."_Shares");
        if (!$fileServer->loaded)
            $fileServer->load();
        $fileServer->loadUsers(false);
        $shares = $fileServer->loadShares(true);
        $ftpHomes = $this->getHomes();
        $denyUsers = $this->getDenyUsers();
        $result = array();
        foreach ($fileServer->users as $user) {
            if (!isset($denyUsers[$user->name]))
            foreach ($fileServer->shares as $share) {
                if ($share->ftpFolder==1 and !isset($ftpHomes[$share->path])) {
                    if ($this->app->remoteSSHCommand!="") {
                        $aclsText = explode("\n",shell_exec($this->app->remoteSSHCommand." getfacl -p '".$fileServer->shares_root."/".$share->path."'"));
                    } else {
                        $aclsText = explode("\n",$this->Shell->exec_command("getfacl -p '".$fileServer->shares_root."/".$share->path."'"));                     
                    }
                    if (count($this->getAcl($user->name,"user",$aclsText))>0 and is_array($this->getAcl($user->name,"user",$aclsText))) {
                        $result[$user->home_dir."/ftp/".$share->name."|".$ftpHomes[$user->home_dir]."/".$share->name] = str_replace("//","/","mkdir -p '".$user->home_dir."/ftp/".$share->name."'; mkdir -p '".$ftpHomes[$user->home_dir]."/".$share->name."'; mount --rbind '".$fileServer->shares_root."/".$share->path."' '".$user->home_dir."/ftp/".$share->name."'; mount --rbind '".$fileServer->shares_root."/".$share->path."' '".$ftpHomes[$user->home_dir]."/".$share->name."'");
                    }
                }
            }
        }
        $this->ftpFolders = $result;
        return $result;
    }
    
    function unsetFolders($oldFolders) {
        $fp = fopen($this->capp->remotePath."/tmp/tmpftpfolders.sh","w");
        if ($oldFolders!="") {
            foreach ($oldFolders as $key=>$value) {
            	$keys = explode("|",$key);
                fwrite($fp,"umount -l '".$keys[0]."'\n");
                fwrite($fp,"umount -l '".$keys[1]."'\n");
                fwrite($fp,"rmdir '".$keys[0]."'\n");
                fwrite($fp,"rmdir '".$keys[1]."'\n");
            }
        }
        fclose($fp);
        if ($this->capp->remoteSSHCommand=="")
            $this->Shell->exec_command("sh /tmp/tmpftpfolders.sh");
        else
            shell_exec($this->capp->remoteSSHCommand." sh /tmp/tmpftpfolders.sh");
	}

    function setFolders($ftpFolders = "NULLL",$oldFolders = "") {
        if ($ftpFolders=="NULLL")
            $ftpFolders = $this->ftpFolders;
        $fp = fopen($this->capp->remotePath."/tmp/tmpftpfolders.sh","w");
        if ($oldFolders!="") {
            foreach ($oldFolders as $key=>$value) {
            	$keys = explode("|",$key);
                fwrite($fp,"umount -l '".$keys[0]."'\n");
                fwrite($fp,"rmdir '".$keys[0]."'\n");
                fwrite($fp,"umount -l '".$keys[1]."'\n");
                fwrite($fp,"rmdir '".$keys[1]."'\n");
            }
        }
        fclose($fp);
        if ($this->capp->remoteSSHCommand=="")
            $this->Shell->exec_command("sh /tmp/tmpftpfolders.sh");
        else
            shell_exec($this->capp->remoteSSHCommand." sh /tmp/tmpftpfolders.sh");
        file_put_contents($this->app->remotePath.$this->fTPFoldersMountsFile,implode("\n",$ftpFolders));
        if ($this->capp->remoteSSHCommand=="")
            $this->Shell->exec_command("sh ".$this->fTPFoldersMountsFile);
        else
            shell_exec($this->capp->remoteSSHCommand." sh ".$this->fTPFoldersMountsFile);
    }

	/**
	* Функция записи информации о виртуальных хостах FTP в файлы /etc/proftpd/virtuals.conf
    * и /root/ftpshaping.sh
	* 
	*/
	function setHosts($hosts) {
		global $Objects;
		// Получаем массивы блоков из файлов шаблонов конфигурационных файлов
		$vhostBlocks = getPrintBlocks(file_get_contents($this->fTPVirtualHostsTemplateFile));
		$shapingBlocks = getPrintBlocks(file_get_contents($this->fTPShapingTemplateFile));
		// Пишем в буферы заголовочные части конфигурационных файлов
		$shapingOutput = $shapingBlocks["header"];
		$vhostsOutput = "";
		foreach ($hosts as $key=>$host) {

			// Пишем заголовочную часть виртуального хоста в буфер
			$vhostsOutput .= strtr($vhostBlocks["vhost_header"],array("{ipAddresses}" => $host["ipAddresses"]));

			// Имя виртуального хоста
			$vhostsOutput .= "	ServerName ".$key."\n";

			// Порт виртуального хоста
			$vhostsOutput .= "	Port ".$host["port"]."\n";

			// Тип аутентификации пользователей
			if ($host["userBase"]=="pam")
				$vhostsOutput .= "	AuthPAM on\n	AuthPAMConfig proftpd\n";

			// Права доступа пользователей к хосту
			if ($host["userList"]!="") {
				$userList = "AllowUser ".implode("\n		AllowUser ",explode("~",$host["userList"]))."\n";
			} else
				$userList = "";
			switch ($host["userAccess"]) {
				case "allowAll":
					$limitOrder = "Allow,Deny";
					if ($userList=="")
						$userList .= "AllowAll\n";
					else
						$userList .= "		AllowAll\n";
					break;
				case "denyAll":
					$limitOrder = "Deny,Allow";
					if ($userList=="")
						$userList .= "DenyAll";
					else
						$userList .= "		DenyAll";
					break;
				case "allowList":
					$limitOrder = "Allow,Deny";
					if ($userList!="")
						$userList .= "		DenyAll";
					else
						$userList .= "DenyAll";
					break;
			}
			$vhostsOutput .= strtr($vhostBlocks["limitLogin"],array("{limitOrder}" => $limitOrder, "{userList}" => $userList))."\n";

			// Анонимный сервер
			$anonymousUser = @$host["anonymousUser"];
			if ($anonymousUser!="") {
				$user = $Objects->get("User_".$this->module_id."_".$anonymousUser);
				if (!$user->loaded)
					$user->load();
				$fileServer = $Objects->get("FileServer_".$this->module_id."_Shares");
    		    if (!$fileServer->groups_loaded)
	        	    $fileServer->loadGroups(false);
				foreach ($fileServer->groups as $group) {
					if ($group->gidNumber == $user->gid)
						$anonymousGroup = $group->name;
				}
			} else
				$anonymousGroup = "";
			$anonymousOptions = "";
			if (isset($host["maxAnonymousClients"]) and $host["maxAnonymousClients"]!="")
				$anonymousOptions .= "MaxClients ".$host["maxAnonymousClients"]."\n";
			if (isset($host["maxAnonymousClientsPerHost"]) and $host["maxAnonymousClientsPerHost"]!="")
				$anonymousOptions .= "		MaxClientsPerHost ".$host["maxAnonymousClientsPerHost"]."\n";
			if (isset($host["maxAnonymousStoreFileSize"]) and $host["maxAnonymousStoreFileSize"]!="")
				$anonymousOptions .= "		MaxStoreFileSize ".$host["maxAnonymousStoreFileSize"]." Mb\n";
			if (isset($host["maxAnonymousRetrieveFileSize"]) and $host["maxAnonymousRetrieveFileSize"]!="")
				$anonymousOptions .= "		MaxRetrieveFileSize ".$host["maxAnonymousRetrieveFileSize"]." Mb\n";
			if (isset($host["anonymousTimeoutSession"]) and $host["anonymousTimeoutSession"]!="")
				$anonymousOptions .= "		TimeoutSession ".$host["anonymousTimeoutSession"]."\n";
			if (isset($host["anonymousTimeoutIdle"]) and $host["anonymousTimeoutIdle"]!="")
				$anonymousOptions .= "		TimeoutIdle ".$host["anonymousTimeoutIdle"]."\n";
			if (isset($host["anonymousTimeouLogin"]) and $host["anonymousTimeoutLogin"]!="")
				$anonymousOptions .= "		TimeoutLogin ".$host["anonymousTimeoutLogin"]."\n";
			$anonymousBlock = strtr($vhostBlocks["anonymous"],array("{anonymousUser}" => $anonymousUser, "{anonymousGroup}" => $anonymousGroup, "{anonymousOptions}" => $anonymousOptions));
			if ($host["anonymousAccess"]!=true) {
				$anonymousBlock = "#".str_replace("\n","\n#",$anonymousBlock);
				$anonymousBlock = substr($anonymousBlock,0,-1);
			}
			$vhostsOutput .= $anonymousBlock;

			// Ограничения
			if (isset($host["maxClients"]) and $host["maxClients"]!="")
				$vhostsOutput .= "	MaxClients ".$host["maxClients"]."\n";
			if (isset($host["maxClientsPerHost"]) and $host["maxClientsPerHost"]!="")
				$vhostsOutput .= "	MaxClientsPerHost ".$host["maxClientsPerHost"]."\n";
			if (isset($host["maxClientsPerUser"]) and $host["maxClientsPerUser"]!="")
				$vhostsOutput .= "	MaxClientsPerUser ".$host["maxClientsPerUser"]."\n";
			if (isset($host["maxHostsPerUser"]) and $host["maxHostsPerUser"]!="")
				$vhostsOutput .= "	MaxHostsPerUser ".$host["maxHostsPerUser"]."\n";
			if (isset($host["maxConnectionsPerHost"]) and $host["maxConnectionsPerHost"]!="")
				$vhostsOutput .= "	MaxConnectionsPerHost ".$host["maxConnectionsPerHost"]."\n";
			if (isset($host["maxLoginAttempts"]) and $host["maxLoginAttempts"]!="")
				$vhostsOutput .= "	MaxLoginAttempts ".$host["maxLoginAttempts"]."\n";
			if (isset($host["maxStoreFileSize"]) and $host["maxStoreFileSize"]!="")
				$vhostsOutput .= "	MaxStoreFileSize ".$host["maxStoreFileSize"]." Mb\n";
			if (isset($host["maxRetrieveFileSize"]) and $host["maxRetrieveFileSize"]!="")
				$vhostsOutput .= "	MaxRetrieveFileSize ".$host["maxRetrieveFileSize"]." Mb\n";
			if (isset($host["timeoutSession"]) and $host["timeoutSession"]!="")
				$vhostsOutput .= "	TimeoutSession ".$host["timeoutSession"]."\n";
			if (isset($host["timeoutIdle"]) and $host["timeoutIdle"]!="")
				$vhostsOutput .= "	TimeoutIdle ".$host["timeoutIdle"]."\n";
			if (isset($host["timeoutLogin"]) and $host["timeoutLogin"]!="")
				$vhostsOutput .= "	TimeoutLogin ".$host["timeoutLogin"]."\n";

			// Приветственное сообщение
			file_put_contents(str_replace("//","/",$this->proFTPdConfigPath."/".$key."_welcome.msg"),$host["welcomeMsg"]);
			$vhostsOutput .= "	DisplayLogin ".str_replace("//","/",$this->proFTPdConfigPath."/".$key."_welcome.msg\n");

			// Скорости приема и передачи для пользователей
			if (isset($host["userTransferRates"]) and $host["userTransferRates"]!="") {
				$userTransferRates = explode("|",$host["userTransferRates"]);
				foreach ($userTransferRates as $line) {
					$arr = explode("~",$line);
					$user = $arr[0];
					$uploadRate = @$arr[1];
					$downloadRate = @$arr[2];
					if ($uploadRate!="")
						$vhostsOutput .= "	TransferRate STOR,APPE ".$uploadRate." user ".$user."\n";
					if ($downloadRate!="")
						$vhostsOutput .= "	TransferRate RETR ".$downloadRate." user ".$user."\n";
				}
			}

			// Скорости приема и передачи по умолчанию для сеанса
			if (isset($host["uploadTransferRate"]) and $host["uploadTransferRate"]!="")
				$vhostsOutput .= "	TransferRate STOR,APPE ".$host["uploadTransferRate"]."\n";
			if (isset($host["downloadTransferRate"]) and $host["downloadTransferRate"]!="")
				$vhostsOutput .= "	TransferRate RETR ".$host["downloadTransferRate"]."\n";

			$vhostsOutput .= $vhostBlocks["constants"];
			$vhostsOutput .= $vhostBlocks["vhost_footer"];

			// Пропускная способность канала для данного хоста
			if (isset($host["serverTransferRate"]) and $host["serverTransferRate"]!="") {
				if ($key=="Default")
					$serverNum = "0";
				else
					$serverNum = array_pop(explode("_",$key));
				$port = $host["port"];
				$serverName = $key;
				$rate = $host["serverTransferRate"];
				$shapingOutput .= strtr($shapingBlocks["host"],array("{rate}" => $host["serverTransferRate"], "{port}" => $port, "{serverName}" => $serverName, "{serverNum}" => $serverNum));
			}
		}

		// сохраняем настройки, накопившиеся в буферах в соответствующие конфигурационные файлы
		file_put_contents($this->capp->remotePath.$this->fTPVirtualHostsConfigFile,$vhostsOutput);
		file_put_contents($this->capp->remotePath.$this->fTPShapingConfigFile,$shapingOutput);

		// перезапускаем FTP-сервер
		$this->restart();
		if ($this->capp->remoteSSHCommand!="")
			shell_exec($this->capp->remoteSSHCommand." ".$this->ftpShapingConfigFile);
		else
			$this->Shell->exec_command($this->ftpShapingConfigFile);
	}

	/**
	* Функция для получения информации о виртуальных хостах FTP из файлов /etc/proftpd/virtuals.conf
    * и /root/ftpshaping.sh
	*
	*/
	function getHosts() {
		global $Objects;
		$result = array();
		$host = array();
		// Открываем конфигурационный файл /etc/proftpd/virtuals.conf и получаем его данные в виде массива
		$vhostsLines = file($this->fTPVirtualHostsConfigFile);

		// Открываем конфигурационный файл /root/ftpshaping.sh и получаем его данные в виде массива
		$shapingLines = file($this->fTPShapingConfigFile);

		// Переменная, определяющая, находимся ли мы в блоке <Anonymous ~/ftp>
		$inAnonymousSession = false;

		// Переменная, определяющая, находимся ли мы в блоке <Limit LOGIN>
		$inLimitLogin = false;

		// Массив пользователей, которым разрешен доступ к серверу
		$usersArray = array();

		// Массив пользователей, для которых установлены максимальная скорость скачивания и закачивания
		$userTransferRates = array();

		// Буфер, в котором хранится значение директивы Order секции <Limit>
		$allowDenyBuffer = "";
		// Проходим по строкам конфигурационного файла /etc/proftpd/virtuals.conf
		foreach ($vhostsLines as $line) {
			$matches = array();

			// Если начинается секция виртуального хоста, то завершаем предыдущую и помещаем
			// собранные данные в массив
			if (preg_match("/\<VirtualHost (.*)\>/",trim($line),$matches)) {
				if (count($result)>0) {
					$host["userList"] = implode("~",$usersArray);
					$usersArray = array();
					$usersTransferRatesStrings = array();
					foreach ($userTransferRates as $key => $value) {
						$usersTransferRatesStrings[] = $key."~".$value["upload"]."~".$value["download"];
					}
					$host["userTransferRates"] = implode("|",$usersTransferRatesStrings);
					$result[$host["ServerName"]] = $host;
					$host = array();
				}
				$host["ipAddresses"] = trim($matches[1]);
			}
			// Определяем, находимся ли мы в секции для анонимного FTP-сервера
			if (trim($line)=='#	<Anonymous ~/ftp>' or trim($line)=="<Anonymous ~/ftp>") {
				// Если секция закоментирована
				if (trim($line)=="#	<Anonymous ~/ftp>")
					$host["anonymousAccess"] = false;
				else
					$host["anonymousAccess"] = true;
				$inAnonymousSession = true;
			}
			$line = trim(str_replace("#","",$line));

			if ($line=="</Anonymous>" or $line=="#	</Anonymous>") {
				$inAnonymousSession = false;
			}
			if ($line=="<Limit LOGIN>")
				$inLimitLogin = true;
			if ($line=="<Limit LOGIN>")
				$inLimitLogin = true;

			// Обрабатываем секцию анонимного сервера, учитывая также, что подобные
            // директивы могут встречаться не только в ней, но и просто в секции
			// данного хоста
			if (preg_match("/^User (.*)/",$line,$matches) and $inAnonymousSession)
				$host["anonymousUser"] = $matches[1];
			if (preg_match("/MaxClients (.*)/",$line,$matches))
				if ($inAnonymousSession)
					$host["maxAnonymousClients"] = trim($matches[1]);
				else
					$host["maxClients"] = trim($matches[1]);
			if (preg_match("/MaxClientsPerHost (.*)/",$line,$matches))
				if ($inAnonymousSession)
					$host["maxAnonymousClientsPerHost"] = trim($matches[1]);
				else
					$host["maxClientsPerHost"] = trim($matches[1]);
			if (preg_match("/MaxStoreFileSize (.*) Mb/",$line,$matches))
				if ($inAnonymousSession)
					$host["maxAnonymousStoreFileSize"] = trim($matches[1]);
				else
					$host["maxStoreFileSize"] = trim($matches[1]);
			if (preg_match("/MaxRetrieveFileSize (.*) Mb/",$line,$matches))
				if ($inAnonymousSession)
					$host["maxAnonymousRetrieveFileSize"] = trim($matches[1]);
				else
					$host["maxRetrieveFileSize"] = trim($matches[1]);
			if (preg_match("/TimeoutSession (.*)/",$line,$matches))
				if ($inAnonymousSession)
					$host["anonymousTimeoutSession"] = trim($matches[1]);
				else
					$host["timeoutSession"] = trim($matches[1]);
			if (preg_match("/TimeoutIdle (.*)/",$line,$matches))
				if ($inAnonymousSession)
					$host["anonymousTimeoutIdle"] = trim($matches[1]);
				else
					$host["timeoutIdle"] = trim($matches[1]);

			// Обрабатываем секцию <Limit LOGIN>

			// Определяем режим доступа пользователей (свойство userAccess)
			if (preg_match("/AllowAll/",$line,$matches)) {
				$host["userAccess"] = "allowAll";
			}
			if (preg_match("/DenyAll/",$line,$matches)) {
				if (count($usersArray)==0)
					$host["userAccess"] = "denyAll";
				else
					$host["userAccess"] = "allowList";
			}

			// Отлавливаем пользователей, которым разрешен доступ к серверу
			if (preg_match("/AllowUser (\S+)/",$line,$matches)) {
				$usersArray[trim($matches[1])] = trim($matches[1]);				
			}

			// Выявляем тип аутентификации
			if (preg_match("/AuthPAM on/",$line,$matches)) {
				$host["userBase"] = "pam";
			}

			// Получаем информацию о различных ограничениях
			if (preg_match("/MaxClientsPerUser (\S+)/",$line,$matches)) {
				$host["maxClientsPerUser"] = trim($matches[1]);
			}

			if (preg_match("/MaxHostsPerUser (\S+)/",$line,$matches)) {
				$host["maxHostsPerUser"] = trim($matches[1]);
			}

			if (preg_match("/MaxConnectionsPerHost (\S+)/",$line,$matches)) {
				$host["maxConnectionsPerHost"] = trim($matches[1]);
			}

			if (preg_match("/MaxLoginAttempts (\S+)/",$line,$matches)) {
				$host["maxLoginAttempts"] = trim($matches[1]);
			}

			if (preg_match("/TimeoutLogin (\S+)/",$line,$matches))
				$host["timeoutLogin"] = trim($matches[1]);

			if (preg_match("/DisplayLogin (\S+)/",$line,$matches)) {
				if (file_exists(trim($this->capp->remotePath.$matches[1])))
					$host["welcomeMsg"] = trim(file_get_contents(trim($this->capp->remotePath.$matches[1])));
			}

			if (preg_match("/TransferRate STOR,APPE (\S+)$/",$line,$matches))
					$host["uploadTransferRate"] = trim($matches[1]);

			if (preg_match("/TransferRate RETR (\S+)$/",$line,$matches))
					$host["downloadTransferRate"] = trim($matches[1]);

			if (preg_match("/TransferRate STOR,APPE (\S+) user (\S+)$/",$line,$matches))
					$userTransferRates[trim($matches[2])]["upload"] = trim($matches[1]);

			if (preg_match("/TransferRate RETR (\S+) user (\S+)$/",$line,$matches))
					$userTransferRates[trim($matches[2])]["download"] = trim($matches[1]);

			if (preg_match("/ServerName (.*)/",$line,$matches))
				$host["ServerName"] = trim($matches[1]);

			if (preg_match("/Port (.*)/",$line,$matches))
				$host["port"] = trim($matches[1]);
		}
		foreach ($shapingLines as $line) {
			if (preg_match("/\# virtualHost ".$host["ServerName"]."\,".$host["port"]."\,(.*) \-\-\>/",$line,$matches)) {
				$host["serverTransferRate"] = trim($matches[1]);
				break;
			}
		}
		if (isset($host["ServerName"])) {
			$host["userList"] = implode("~",$usersArray);
			$usersArray = array();
			$usersTransferRatesStrings = array();
			foreach ($userTransferRates as $key => $value) {
				$usersTransferRatesStrings[] = $key."~".@$value["upload"]."~".@$value["download"];
			}
			$host["userTransferRates"] = implode("|",$usersTransferRatesStrings);
			$result[$host["ServerName"]] = $host;
		}
		return $result;
	}

    function restart() {
    	if ($this->app->remoteSSHCommand != "")
        	$this->Shell->exec_command($this->app->remoteSSHCommand." ".$this->proFTPdRestartCommand);
    	else
    		$this->Shell->exec_command("echo '".$this->proFTPdRestartCommand."' | at now");
    }
}
?>