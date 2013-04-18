<?php
/**
 *	Класс реализует виртуальный хост FTP
 *
 *  FTP-сервер содержит как минимум один хост, который называется Default. Это хост FTP по умолчанию. Изначально он привязан к
 *  порту 21. 
 *
 *  Также FTP-сервер может содержать любое количество виртуальных хостов. Структура их конфигурации идентична хосту Default. Они должны быть привязаны
 *  к портам с другими номерами.
 *
 *  Конфигурации виртуальных хостов находятся в файле /etc/proftpd/virtuals.conf. Конфигурация каждого хоста находится между
 *  тэгами <VirtualHost> </VirtualHost>.
 *
 *  Данный класс управляет FTP-хостами обоих типов одинаково. Если имя объекта равно Default, то класс работает с конфигурацией
 *  по умолчанию, а если имя объекта другое, он ищет соответствующий раздел этого хоста в файле /etc/proftpd/virtuals.conf. Поиск
 *  производится по имени сервера, которое указывается в параметре ServerName внутри виртуального хоста.
 *
 *  Для избежания колизий, имена виртуальных хостов (и хоста по умолчанию) пользователь не имеет возможности редактировать.
 *  Имена виртуальных хостов генерируются автоматически в формате FTPHost_<порядковый-номер>.
 *
 *  Каждый хост имеет следующий набор параметров, которые в большинстве случаев соответствуют директивам конфигурации
 *  сервера ProFTPd:
 *
 *  name - имя хоста (директива ServerName), должно быть уникально
 *
 *  ipAddresses - IP-адреса и имена хостов, по которым данный хост доступен, указанные через пробел
 *
 *  port - порт хоста (директива Port), должно быть уникально
 *
 *  userBase - база данных, из которой берется информация о пользователях, имеющих право подключаться к серверу. Варианты:
 *             'pam' - системные пользователи Unix (используется по умолчанию в файловом сервере)
 *             'virtual' - виртуальные пользователи. Информация о таких пользователях хранится в текстовом файле с именем <имя-хоста>_users.conf.
 *             'ldap' - пользователи, которые берутся из каталога LDAP
 *             'sql' - пользователи, которые берутся из базы данных SQL-сервера
 *             В зависимости от установленного типа, в конфигурационный файл хоста заносятся различные директивы, предназначенные для настройки данного
 *             типа аутентификации пользователя.
 *             (Все варианты кроме 'pam' в версии платформы 1.1.05 не работают и просто заведены на будущее. Для варианта 'pam' в конфигурационном файле
 *              статически прописываются следующие директивы:
                 AuthPAM on
                 AuthPAMConfig proftpd
               )
 *
 *
 * userAccess - тип ограничения доступа пользователей. allowAll - разрешено всем, denyAll - запрещено всем, allowList - разрешено только пользователям,
 *              указанным в свойстве userList
 *
 * userList - список имен пользователей, которым разрешен доступ к данному хосту (через символ ~). Если userAccess установлено значение 'allowAll', доступ разрешен
 *            всем пользователям. Для обработки этого параметра в конфигурационном файле, в подразделе данного хоста создается следующий подраздел
 *
 *            	<Limit LOGIN>
 *                  Order Allow,Deny
 *					<права-доступа>
 *					DenyAll
 *				</Limit>
 *
 *            Раздел такого вида создается только если используется база системных пользователей 'pam', так как системные пользователи уже содаржат
 *            информацию о правах на чтение и запись, поэтому контролируется только возможность подключения к серверу (LOGIN). Для других типов
 *            аутентификации данный раздел может быть изменен.
 *
 *            <права доступа> зависит от того, что реально содержится в свойстве userAccess. Если оно содержит denyAll, значит доступ запрещен всем и здесь
 *            будет директива 'DenyAll' и Order Deny,Allow. Если значение userAccess равно 'allowAll', значит доступ разрешен всем и здесь установлено 'AllowAll'
 *			  и Order Allow,Deny. Если в userAccess указано allowList, значит доступ разрешен только пользователям, указанным в этом списке и больше никому. В этом
 *            случае здесь будет список директив 'AllowUser <имя-пользователя>' по одной на каждой строке и в конце будет добавлена директива 'DenyAll',
 *            означающая в данном случае что всем остальным доступ запрещен. Также остается директива Order Allow,Deny, которая сначала разрешает доступ перечисленным
 * 			  в директивах AllowUser пользователям, а затем запрещает доступ всем остальным, так как остается директива DenyAll.
 *
 * anonymousAccess - разрешен ли анонимный доступ к этому хосту от имени пользователя Anonymous. Если данная опция имеет значение true, в конфигурационном
 *                   файле, в разделе этого виртуального хоста создается подраздел <Anonymous ~ftp>, которая выглядит следующим образом:
 *
 *					<Anonymous ~/ftp>
 *						User <anonymousUser>
 *						Group <основная группа anonymousUser>
 *						UserAlias anonymous <anonymousUser>
 *						MaxClients <maxAnonymousClients>
 *						DirFakeUser on <anonymousUser>
 *						DirFakeGroup on <основная группа anonymousUser>
 *					</Anonymous>
 *
 *					Соответственно, если включен анонимный доступ, должны быть указаны параметры, для этой директивы, описанные ниже
 *
 * anonymousUser - пользователь, от имени которого клиенты будут анонимно подключаться к серверу
 *
 * maxAnonymousClients - максимальное количество одновременных анонимных подключений к серверу.
 *
 * maxClients - максимальное количество одновременных подключений к этому хосту
 *
 * maxClientsPerHost - максимальное количество одновременных подключений к этому хосту с одного хоста
 *
 * maxClientsPerUser - максимальное количество одновременных подключений к этому хосту от имени одного пользователя
 *
 * maxHostsPerUser - максимальное количество различных хостов, с которых один пользователь может подключиться одновременно
 *
 * maxConnectionsPerHost - максимальное количество неавторизованных подключений с одного хоста
 *
 * maxLoginAttempts - максимальное количество неудачных попыток подключения с одного хоста
 *
 * maxStoreFileSize - максимальный размер файла, который можно закачать на сервер
 *
 * maxRetrieveFileSize - максимальный размер файла, который можно скачать с сервера
 *
 * rLimitMemory - максимальное количество памяти, которое может занимать один процесс
 *
 * rLimitCPU - максимальное время процессора, которое может занимать один процесс
 *
 * rLimitOpenFiles - максимальное количество открытых файлов, которые может занимать один процесс
 *
 * timeoutIdle - максимальное время в секундах, которое клиентам будет разрешено оставаться подключенными при отсутствии активности
 *
 * timeoutLogin - максимальное время, которое дается пользователю на аутентификацию
 *
 * timeoutSession - максимальное время соединения с сервером
 *
 * welcomeMsg - текст сообщения, которое появляется при входе пользователя. Находится в файле /etc/proftpd/<имя-хоста>_welcome.txt
 *
 * serverTransferRate - ширина канала сервера, то есть, максимальная скорость передачи данных по FTP, в рамках которой FTP-сервер будет работать.
 *                      Этот параметр указывается в килобитах в секунду и настраивается не с помощью конфигурационного файла proftpd. Это
 * 						настраивается на уровне операционной системы, с помощью механизма Trafic Shaping. Создается скрипт /root/ftpshaping.sh,
 *						в котором находятся команды tc. Эти команды устанавливают максимальную пропускную способность канала для пакетов,
 *						исходящих с порта, на котором находится данный FTP-сервер, а также помещает эти пакеты в очередь sfq, то есть равномерно
 *						распределяет все пакеты всех сеансов. Иначе приоритет получит первый, кто подключится, а все остальные будут использовать
 *						оставшуюся скорость.
 *
 * uploadTransferRate - ограничение скорости закачки файлов на сервер по умолчанию для всех пользователей. Указывается в килобитах-в-секунду.
 *                      Генерирует директиву TransferRate STOR,APPE <скорость>
 *
 * downloadTransferRate - ограничение скорости скачивания файлов с сервера по умолчанию для всех пользователей. Указывается в килобитах в секунду.
 *                        Генерирует директиву TransferRate RETR <скорость>
 *
 * userTransferRates - массив скоростей скачивания для каждого пользователя в виде строки в формате:
 *                     <имя-пользователя>~<скорость-закачки>~<скорость-скачивания>|<имя-пользователя>~<скорость-закачки>~<скорость-скачивания> ...
 *                     Этот параметр передается в таблицу типа FTPUserTransferRates, которая создает на базе нее массив и заполняет таблицу. Затем,
 *                     при сохранении изменений свойств хоста, класс таблицы делает обратную операцию - на основании заполненной таблицы генерирует
 *                     массив скоростей для пользователей и из этого массива генерирует строку. На базе этой строки процедура сохранения изменений
 *                     создает в конфигурационном файле в разделе данного хоста набор директив TransferRate, по две для каждого пользователя:
 *
 *                     TransferRate STOR,APPE <скорость> user <имя-пользователя>
 *                     TransferRate RETR <скорость> user <имя-пользователя>
 *
 * Для чтения и записи всех этих настроек используются процедуры load() и save(). Так как информация о всех виртуальных хостах находится в одном файле,
 * фактически происходит полное чтение этого файла, затем изменение параметров определенного хоста и полная перезапись файла. Поэтому чтение и запись
 * происходит на общем уровне, в классе FTPServer, который хранится в свойстве ftpServer данного класса. В нем есть соответствующие методы getHosts() и
 * setHosts(). Первый метод считывает конфигурационный файл /etc/proftpd/virtuals.conf и возвращает список хостов с их параметрами в виде
 * многомерного массива. Второй метод принимает массив хостов с их параметрами и сохраняет его в конфигурационный файл /etc/proftpd/virtuals.conf.
 *
 * Соответственно, метод load() данного класса вызывает метод FTPServer::getHosts() и выбирает из полученного массива хостов нужный элемент (по свойству
 * name). Затем, после внесения изменений в настройки, метод save() получает список хостов с помощью метода FTPServer::getHosts(), получает из этого
 * массива элемент, соответствующий данному хосту, вносит изменения в его параметры и затем передает весь массив хостов, с внесенными модификациями
 * в метод FTPServer::setHosts(). Этот метод сохраняет данные в файл /etc/proftpd/virtuals.conf и перезапускает FTP-сервер с помощью FTPServer::restart().
 *
 * Также методы FTPServer::getHosts() и FTPServer::setHosts() должен вызывать объект пользователя (класс User) при изменении имени пользователя или
 * при удалении пользователя, чтобы изменить или убрать имя этого пользователя из списка userList или из списка userTransferRates. Также, если
 * переименовывается пользователь, указанный в качестве анонимного, должно происходить изменение свойства anonymousUser. При попытке удаления пользователя,
 * который указан в качестве анонимного хотя бы для одного хоста, должно выдаваться сообщение о невозможности удалить этого пользователя до внесения
 * изменений в настройку FTP-хостов, в которых он фигурирует.
 *
 * Для сохранения параметров в конфигурационный файл, класс FTPServer использует файл шаблона templates/ftp/ftphost.conf, в котором находится
 * шаблон директивы <VirtualHost> для виртуального хоста. Также, если указывается свойство serverTransferRate, то также обрабатывается файл
 * /root/ftpshaping.sh, который состоит из наборов директив ограничения скорости для каждого FTP-хоста (по порту). Шаблон набора директив для каждого
 * хоста находится в templates/ftp/ftpshaping.sh.
 *
 * Также данный класс позволяет получать информацию об активных соединениях пользователей с данным хостом. Для получения списка всех пользователей и
 * операций которые они в данный момент выполняют, используется функция getActiveConnections(). Она возвращает данные в виде массива. Этот массив затем
 * преобразовывается в строку вида:
 *
 * <имя-пользователя>~<продолжительность операции>~<операция>~<имя-хоста-с-которого-подключился>-<содержание-операции>|...
 *
 * <операция> может быть: idle - неактивен, STOR - закачивает файл на сервер, RETR - скачивает файл с сервера. В зависимости от типа операции, содержание
 * операции выглядит по разному. Если idle, то в нем находится путь к папке, в которой пользователь находится в данный момент, если STOR, то выглядит оно так:
 *
 * <имя-файла> ==> <в какую папку закачивает> (скорость: <скорость-закачивания> Kbps, <процент-выполнения>).
 *
 * Если RETR:
 *
 * <папка из которой скачивает>/<имя-файла> (скорость: <скорость-скачивания> Kbps, <процент-выполнения>).
 *
 * Для получения этой информации, функция getActiveConnections() использует следующую команду:
 *
 * ftpwho -v -o oneline -S <имя-хоста-FTP> | fmt -usw 1000 | cut -d -f2-
 *
 * которая прописана в конфигурационном файле FTP-сервера под именем GetFTPConnectionsCommand.
 *
 * Эта функция преобразует вывод этой команды в описанный выше массив, руководствуясь следующими правилами:
 *
 * Если встретилась строка, в которой фигурирует слово " idle ", то для получения нужных параметров используется следующее регулярное выражение:
 *
 * /(S+) [(.*)] S+ idle client\: S+ [\:\:ffff\:(S+)] server\: .* protocol\: ftp location\: (S+)/
 *
 * В первых скобках (S+) находится имя пользователя, далее в скобках (.*) находится продолжительность соединения, затем в скобках (S+) находится адрес
 * хоста клиента, затем в следующих скобках папка, в которой пользователь находится в данный момент.
 *
 * Если встетилась строка, в которой фигурирует строка " STOR ", то для получения параметров используется следующее регулярное выражение
 *
 * /(S+) [(.*)] \((S+)\) STOR (.*) KB/s\: (S+) client\: S+ [\:\:ffff\:(S+)] server\: .* protocol\: ftp location\: (S+)/
 * 
 * В первых скобках (S+) находится имя пользователя, далее в скобках (.*) находится продолжительность соединения, затем в скобках (S+) находится процент
 * выполнения операции, затем в скобках (.*) находится имя файла, который закачивается на сервер, далее в следующих скобках находится скорость закачивания и затем
 * адрес хоста клиента, затем в следующих скобках папка, в которую пользователь этот файл закачивает.
 *
 * Для строки, в которой встретилась строка " RETR " используется такое же регулярное выражение, как и для STOR, только оно несколько иначе интерпретируется.
 *
 * Полученные данные записываются в массив, затем этот массив преобразуеся в строку указанного ранее вида и передается в таблицу типа FTPConnectionsTable,
 * которая показывает список активных соединений в Панели управления. Таблица позволяет обновлять список соединений,
 *
 * Также, в интерфейсе фигурирует список активных пользователей и есть функция disconnectUser(), позволяющая отключить любого активного пользователя
 * от сервера в любой момент.
 *
 * @andrey 08.07.2012 9:14
*/

class FTPHost extends WABEntity {

	function construct($params) {
		$this->module_id = array_shift($params)."_".array_shift($params);
		$this->name = implode("_",$params);
		$this->port = "21";
		$this->ipAddresses = "localhost";
		$this->userBase = "pam";
		$this->userAccess = "allowAll";
		$this->userList = "";
		$this->anonymousAccess = false;
		$this->anonymousUser = "";
		$this->maxAnonymousClients = "";
		$this->maxAnonymousClientsPerHost = "";
		$this->maxAnonymousStoreFileSize = "";
		$this->maxAnonymousRetrieveFileSize = "";
		$this->anonymousTimeoutSession = "";
		$this->anonymousTimeoutIdle = "";
		$this->anonymousTimeoutLogin = "";
		$this->maxClients = "";
		$this->maxClientsPerHost = "";
		$this->maxClientsPerUser = "";
		$this->maxHostsPerUser = "";
		$this->maxConnectionsPerHost = "";
		$this->maxLoginAttempts = "";
		$this->maxStoreFileSize = "";
		$this->maxRetrieveFileSize = "";
		$this->timeoutSession = "";
		$this->timeoutIdle = "";
		$this->timeoutLogin = "";
		$this->uploadTransferRate = "";
		$this->downloadTransferRate = "";
		$this->userTransferRates = "";
		$this->serverTransferRate = "";
		$this->welcomeMsg = "Welcome to FTP!";
		$this->loaded = false;
		$this->template = "templates/ftp/FTPHost.html";
		$this->handler = "scripts/handlers/ftp/FTPHost.js";

        $this->width = "750";
        $this->height = "520";
        $this->overrided = "width,height";

        $this->tabs_string = "main|Основное|".$this->skinPath."images/spacer.gif;";
        $this->tabs_string.= "connect_options|Ограничения соединений|".$this->skinPath."images/spacer.gif;";
        $this->tabs_string.= "limits|Ограничения времени и объема|".$this->skinPath."images/spacer.gif;";
        $this->tabs_string.= "userRights|Права доступа|".$this->skinPath."images/spacer.gif;";
        $this->tabs_string.= "anonymous|Анонимный FTP|".$this->skinPath."images/spacer.gif;";
        $this->tabs_string.= "monitor|Монитор|".$this->skinPath."images/spacer.gif";

		$this->active_tab = "main";

        $this->tabset_id = "WebItemTabset_".$this->module_id."_".$this->subnet_name.$this->name."FTPHost";

		global $Objects;
		$this->app = $Objects->get("Application");
		if (!$this->app->initiated)
			$this->app->initModules();
		$this->capp = $Objects->get($this->module_id);
		$this->shell = $Objects->get("Shell_shell");
		$this->skinPath = $this->app->skinPath;
		$this->icon = $this->skinPath."images/Tree/ftp.png";
		$this->css = $this->skinPath."styles/Mailbox.css";
		$this->handler = "scripts/handlers/ftp/FTPHost.js";
		$this->usersTable = "";
		$this->clientClass = "FTPHost";
		$this->parentClientClasses = "Entity";		
        $this->classTitle = "Хост FTP";
        $this->classListTitle = "Хосты FTP";
	}

	function getHostData() {
		return $this->fields;
	}

	function load() {
		global $Objects;
		$ftpServer = $Objects->get("FTPServer_".$this->module_id."_ftp");
		$hosts = $ftpServer->getHosts();
		// Если хост есть в конфигурационном файле, загружаем его
		if (isset($hosts[$this->name])) {
			foreach ($hosts[$this->name] as $key=>$value)
				$this->fields[$key] = $value;
			$this->old_name = $this->name;
			$this->loaded = true;
		} else if ($this->name==""){
			// иначе определяем следующий порядковый номер хоста и формирууем его имя
			// в формате FTPHost_<порядковый-номер>
			$ftpServer = $Objects->get("FTPServer_".$this->module_id."_ftp");
			$hosts = $ftpServer->getHosts();
			$max=0;
			foreach($hosts as $key => $value) {
				if ($key!="Default") {
					$num = array_pop(explode("_",$key));
					if ($num>$max)
						$max = $num;
				}
			}
			$max = $max+1;
			$this->name = "FTPHost_".$max;
		}
	}

	function checkData() {
		if ($this->ipAddresses=="") {
			$this->reportError("Не заполнено поле 'IP-адреса и имена хостов'!");
			return 0;
		}
		if ($this->port=="") {
			$this->reportError("Не заполнено поле 'Порт'!");
			return 0;
		}
		if ($this->anonymousAccess and $this->anonymousUser=="") {
			$this->reportError("Не указан анонимный пользователь");
			return 0;
		}
		$usersArray = array();
		$usersTransferRates = array();
		if (trim($this->usersTable)!="") {
			$arr =explode("|",$this->usersTable);
			$c=1;
			foreach ($arr as $value) {
				$parts = explode("~",$value);
				if (trim($parts[1])=="") {
					$this->reportError("В строке ".$c." таблицы пользователей не заполнено значение колонки 'Пользователь'");
					return 0;
				}
				if (trim($parts[0])=="1") {
					$usersArray[trim($parts[1])] = trim($parts[1]);
				}
				if (trim($parts[2])!="" or trim($parts[3])!="") {
					if (isset($usersTransferRates[trim($parts[1])])) {
						$this->reportError("В таблице пользователей один и тот же пользователь '".trim($parts[1])."' указан несколько раз");
						return 0;
					}
					$usersTransferRates[trim($parts[1])] = trim($parts[1])."~".trim($parts[2])."~".trim($parts[3]);
				}
			}
		}
		$this->userList = implode("~",$usersArray);
		$this->userTransferRates = implode("|",$usersTransferRates);
		return true;
	}

	function getHookProc($number) {
		switch ($number) {
			case '3':
				return "saveHook";		
		}
		return parent::getHookProc($number);
	}

	function saveHook($arguments) {

		$this->loaded = true;

		// загружаем все значения и переданного массива в поля объекта
		foreach ($arguments as $key=>$value) {
			$this->fields[trim($key)] = trim($value);
		}

		// и сохраняем их
		$this->save();
	}

	function save() {
		global $Objects;

		// Загружаем объект, если еще не загружен
		if (!$this->loaded)
			$this->load();

		// Проверяем данные на корректность
		if (!$this->checkData())
			return 0;

		// Получаем массив виртуальных хостов с FTP-сервера
		$ftpServer = $Objects->get("FTPServer_".$this->module_id."_ftp");
		$hosts = $ftpServer->getHosts();
	
		$hosts[$this->name] = $this->getHostData();
		$ftpServer->setHosts($hosts);
		$this->app->raiseEvent("FTPSERVER_CHANGED");
		$this->loaded = true;
	}

	function getPresentation() {
		if ($this->name=="Default")
			return "FTP-сервер";
	}
	
	function getActiveConnections() {
		global $Objects;
		$ftpServer = $Objects->get("FTPServer_".$this->module_id."_ftp");
		if ($this->capp->remoteSSHCommand=="")
			$lines = explode("\n",$this->shell->exec_command(strtr($ftpServer->fTPWhoCommand,array("{hostname}" => $this->name))));
		else
			$lines= explode("\n",shell_exec(strtr($ftpServer->fTPWhoCommand,array("{hostname}" => $this->name))));
		$arr = array();
		$c=0;
		foreach ($lines as $line) {
			$line=trim($line);
			$matches = array();
			if (preg_match("/(\S+) \[(.*)\] \S+ idle client\: \S+ \[\:\:ffff\:(\S+)\] server\: .* protocol\: ftp location\: (\S+)/",$line,$matches)) {
				$arr[$c]["user"] = trim($matches[1]);
				$arr[$c]["time"] = trim($matches[2]);
				$arr[$c]["operation"] = "простаивает";
				$arr[$c]["host"] = trim($matches[3]);
				$arr[$c]["description"] = str_replace("//","/",trim($matches[4]));
				$c++;
				continue;
			}
			if (preg_match("/(\S+) \[(.*)\] \((.*)\) STOR (.*) KB\/s\: (\S+) client\: \S+ \[\:\:ffff\:(\S+)\] server\: .* protocol\: ftp location\: (.*)/",$line,$matches)) {
				$arr[$c]["user"] = trim(@$matches[1]);
				$arr[$c]["time"] = trim(@$matches[2]);
				$arr[$c]["operation"] = "передает";
				$arr[$c]["host"] = trim(@$matches[6]);
				$arr[$c]["description"] = str_replace("//","/",trim(@$matches[4])." ==> ".trim(@$matches[7])." (скорость: ".trim(@$matches[5])." Кбит/с,процент выполнения: ".trim(@$matches[3]).")");
				$c++;
				continue;
			}
			if (preg_match("/(\S+) \[(.*)\] \((.*)\) RETR (.*) KB\/s\: (\S+) client\: \S+ \[\:\:ffff\:(\S+)\] server\: .* protocol\: ftp location\: (.*)/",$line,$matches)) {
				$arr[$c]["user"] = trim(@$matches[1]);
				$arr[$c]["time"] = trim(@$matches[2]);
				$arr[$c]["operation"] = "принимает";
				$arr[$c]["host"] = trim(@$matches[6]);
				$arr[$c]["description"] = str_replace("//","/",trim(@$matches[7])."/".trim(@$matches[4])." (скорость: ".trim(@$matches[5])." Кбит/с,процент выполнения: ".trim(@$matches[3]).")");
				$c++;
				continue;
			}
			
		}		
		return $arr;
	}

	function getArgs() {
		global $Objects;
		$fileServer = $Objects->get("FileServer_".$this->module_id."_Shares");
		$fileServer->loadUsers(false);
		$arr = array();
		foreach ($fileServer->users as $user) {
			$arr[] = $user->name;
		}
		$this->usersList = " ~".implode("~",$arr)."| ~".implode("~",$arr);
		$usersArray = explode("~",$this->userList);
		$arr = array();
		foreach($usersArray as $user) {
			$user = trim($user);
			if ($user=="")
				continue;
			$arr[$user]["enabled"] = "1";
			$arr[$user]["name"] = $user;
			$arr[$user]["uploadRate"] = "";
			$arr[$user]["downloadRate"] = "";
		}
		$usersArray = explode("|",$this->userTransferRates);
		foreach($usersArray as $rate) {
			$parts = explode("~",$rate);
			$parts[0] = trim($parts[0]);
			if ($parts[0]=="")
				continue;
			$arr[$parts[0]]["name"] = $parts[0];
			$arr[$parts[0]]["uploadRate"] = @$parts[1];
			$arr[$parts[0]]["downloadRate"] = @$parts[2];
		}
		$result = array();
		foreach ($arr as $value) {
			$result[] = @$value["enabled"]."~".$value["name"]."~".$value["uploadRate"]."~".$value["downloadRate"];
		}
		$this->usersTable = implode("|",$result);
		return parent::getArgs();
	}
}
?>