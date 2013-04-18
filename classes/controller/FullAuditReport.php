<?php
/**
 * Класс отвечает за ведение и формирование отчета о доступе к файловым 
 * ресурсам. 
 * 
 * Данные о доступе к файловым ресурсам хранятся в базе данных MySQL, на которую
 * указывает адаптер. Адаптер берет реквизиты доступа к базе данных
 * конфигурационного файла пользователя панели управления. В разделе модуля 
 * MystixController находится подраздел <FileServer>, а в нем параметры:
 * 
 * AuditDBHost
 * AuditDBPort
 * AuditDBName
 * AuditDBUser
 * AuditDBPassword
 * 
 * На основании этих данных адаптер подключается к базе и читает и записывает
 * в нее сущности типа AuditRecord. 
 * 
 * Каждая сущность содержит следующие параметры:
 * 
 * eventDate - время события
 * eventIP - IP-адрес компьютера, который совершил событие
 * eventType - тип события (как по модулю Samba full_audit):
 *  1 - connect (соединение)
 *  2 - disconnect (отсоединение)
 *  3 - mkdir (создание каталога)
 *  4 - rmdir (удаление каталога)
 *  5 - open (открытие файла)
 *  6 - close (закрытие файла)
 *  7 - read (чтение файла)
 *  8 - write (запись файла)
 *  9 - sendfile (отправка файла)
 *  10 - rename (перемещение файла)
 *  11 - unlink (удаление файла)
 *  12 - ftruncate (обнуление файла)
 *  13 - lock (блокировка файла)
 * eventFilePath - путь к файлу или каталогу
 * newFilePath - путь к новому файлу или каталогу, если он был перемещен
 * 
 * 
 * Функция getData() получает данные из файла журнала, который ведет Samba и 
 * на базе каждой строки создает сущность AuditRecord, заполняет ее и сохраняет
 * в базе. Файл журнала берется из параметра конфигурационного файла FileServer->
 * SambaAuditLogFile (по умолчанию /var/log/samba/log.audit) и обрабатывает
 * каждую строку в соответствии со следующим шаблоном:
 * 
 * (стандартный префикс журнала:<eventIP>|<eventRootPath>|<eventDate>|<eventType>||<eventPath>|<eventNewPath>
 * 
 * и заполняет поля сущности следующим образом:
 * 
 * eventDate = <eventDate>
 * eventIP = <eventIP>(подставляет число вместо реального IP-адреса)
 * eventFilePath = <eventRootPath>."/".<eventPath>
 * eventNewFilePath = <eventRootPath>."/".<eventNewPath>
 * eventType = <eventType>(подставляет код вместо реального имени события)
 * 
 * Когда процедура getData() запускается, она создает резервную копию файла audit.log (файл audit.log.bak),
 * затем считывает все строки файла в массив strings, затем обнуляет файл audit.log и начинает обработку
 * массива, создавая сущности и сохраняя их в базе. При проходе по строкам, процедура не записывает дублирующиеся
 * строки.
 * 
 * Также, перед тем как записывать данные в базу, процедура удаляет устаревшие данные из базы. Данные считаются 
 * устаревшими, если были созданы раньше чем количество дней, определенных в параметре конфигурационного файла
 * FileServer->auditPeriod.
 *   
 * 
 * @author andrey
 */
class FullAuditReport extends WABEntity {
    
    public $eventTypes = array();
    public $eventTitles = array();
    public $eventCodes = array();
    
    function construct($params) {
        parent::construct($params);
        
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();

        $this->icon = $app->skinPath."images/Tree/eventviewer.png";
        $this->skinPath = $app->skinPath;

        $this->template = "templates/controller/FullAuditReport.html";
        $this->css = $app->skinPath."styles/Mailbox.css";
        $this->handler = "scripts/handlers/controller/FullAuditReport.js";

        $this->tabs_string = "log|Журнал|".$app->skinPath."images/spacer.gif;";
        $this->tabs_string.= "settings|Настройка|".$app->skinPath."images/spacer.gif";

        $this->tabset_id = "WebItemTabset_".$this->module_id."_SmbAudit";
        $this->active_tab = "log";
        $this->width = "680";
        $this->height = "400";
        $this->overrided = "width,height";
        
        $this->adapter = $Objects->get("FullAuditDataAdapter_".$this->module_id."_SMB");
        $this->adapterId = $this->adapter->getId();
        $this->sortFields = "eventDate DESC";
        $this->fieldList = "eventDate Дата~eventIP IP-адрес~eventType Событие~eventFilePath Файл/Каталог~eventFileNewPath Новое место";
        $this->smbAuditDBHost = $this->adapter->host;
        $this->smbAuditDBPort = $this->adapter->port;
        $this->smbAuditDBName = $this->adapter->dbname;
        $this->smbAuditDBUser = $this->adapter->user;
        $this->smbAuditDBPassword = $this->adapter->password; 
        
        $this->fileServer = $Objects->get("FileServer_".$this->module_id."_Shares");
        $this->smbAuditPeriod = $this->fileServer->smbAuditPeriod;
        
        $this->eventCodes["pwrite"]      = 2;
        $this->eventCodes["ftruncate"]  = 3;
        $this->eventCodes["disconnect"] = 4;
        $this->eventCodes["sendfile"]   = 6;
        $this->eventCodes["rename"]     = 7;
        $this->eventCodes["connect"]    = 8;
        $this->eventCodes["pread"]       = 9;
        $this->eventCodes["mkdir"]      = 10;
        $this->eventCodes["rmdir"]      = 11;
        $this->eventCodes["unlink"]     = 12;
        $this->eventCodes["lock"]       = 13;
        
        $this->eventTypes[2]  = "pwrite";
        $this->eventTypes[3]  = "ftruncate";
        $this->eventTypes[4]  = "disconnect";
        $this->eventTypes[6]  = "sendfile";
        $this->eventTypes[7]  = "rename";
        $this->eventTypes[8]  = "connect";
        $this->eventTypes[9]  = "pread";
        $this->eventTypes[10] = "mkdir";
        $this->eventTypes[11] = "rmdir";
        $this->eventTypes[12] = "unlink";
        $this->eventTypes[13] = "lock";
        
        $this->eventTitles[2]  = "Записал изменения в файле";
        $this->eventTitles[3]  = "Обнулил файл";
        $this->eventTitles[4]  = "Отключился";
        $this->eventTitles[6]  = "Отправил файл";
        $this->eventTitles[7]  = "Переместил файл";
        $this->eventTitles[8]  = "Подключился";
        $this->eventTitles[9]  = "Прочитал файл";
        $this->eventTitles[10] = "Создал папку";
        $this->eventTitles[11] = "Удалил папку";
        $this->eventTitles[12] = "Удалил файл";
        $this->eventTitles[13] = "Установил блокировку на файл";
        
        $this->eventDateStart = time()."000";
        $this->eventDateEnd = time()."000";
        $this->eventIP = "";
        $this->eventFilePath = "";
        $this->eventFileNewPath = "";
        $this->eventType = 0;
        
        $this->clientClass = "FullAuditReport";
        $this->parentClientClasses = "Entity";        
    }
    
    function load() {
        
    }
    
    function save($arguments) {    	
        global $Objects;
        
        if (isset($arguments)) {
        	$this->load();
        	$this->setArguments($arguments);
        }
        
        if ($this->smbAuditDBHost=="") {
            $this->reportError("Не указан сервер БД!","save");
            return 0;
        }
        if ($this->smbAuditDBPort=="") {
            $this->reportError("Не указан порт сервера БД!","save");
            return 0;
        }
        if ($this->smbAuditDBName=="") {
            $this->reportError("Не указано имя БД!","save");
            return 0;
        }
        if ($this->smbAuditDBUser=="") {
            $this->reportError("Не указано имя пользователя БД!","save");
            return 0;
        }
        
        $config = new DOMDocument();
        $gapp = $Objects->get("Application");
        if (!$gapp->initiated)
            $gapp->initModules();
        $module = $gapp->getModuleByClass($this->module_id);
        $module["smbAuditDBHost"] = $this->smbAuditDBHost;
        $module["smbAuditDBPort"] = $this->smbAuditDBPort;
        $module["smbAuditDBName"] = $this->smbAuditDBName;
        $module["smbAuditDBUser"] = $this->smbAuditDBUser;
		$module["smbAuditDBPassword"] = $this->smbAuditDBPassword;
		$module["smbAuditPeriod"] = $this->smbAuditPeriod;
		$GLOBALS["modules"][$module["name"]] = $module;
		$str = "<?php\n".getMetadataString(getMetadataInFile($module["file"]))."\n?>";
		file_put_contents($module["file"],$str);
	}
    
    function getPresentation() {
        return "Журнал событий файловой системы";
    }
    
    function getData() {
        global $Objects;
        $t = time();
        $t2 = $t-$this->fileServer->smbAuditPeriod*24*60*60;        
        if (!$this->adapter->connected)
            $this->adapter->connect();
        if (!$this->adapter->connected)
    	    return 0;
        @$this->adapter->dbh->exec("CREATE TABLE eventlog (eventDate INT, eventType INT, eventIP INT, eventFilePath VARCHAR(255), eventFileNewPath VARCHAR(255))");	        
        $stmt = $this->adapter->dbh->prepare("DELETE FROM eventlog WHERE eventDate<=:eventDate");
        $stmt->bindParam(":eventDate",$t2);
        $stmt->execute();
        $app = $Objects->get($this->module_id);
        $prev_event = "";        
        $prev_file = "";
        $strings = file($app->remotePath.$this->fileServer->smbAuditLogFile);        
        $result_strings = array();
        $fp = fopen($app->remotePath.$this->fileServer->smbAuditLogFile,"w");
        fwrite($fp,"");
        fclose($fp);
        foreach ($strings as $line) {
            $arr = explode("]:",$line);
            array_shift($arr);
            $line = implode("]:",$arr);
            $line = trim($line);
            $arr = explode("|",$line);
            if (ip2int(trim($arr[0]))=="")
                continue;
            if (!isset($this->eventCodes[trim($arr[4])]))
                continue;
            if (array_search($line, $result_strings)!==FALSE)
                continue;
            else
                $result_strings[] = $line;
            
            $eventType = $this->eventCodes[trim($arr[4])];
            $eventIP = ip2int(trim($arr[0]));
            if (trim($arr[4])=="connect" or trim($arr[4])=="disconnect")
                $eventFilePath = trim($arr[2]);
            else {
                if (trim($arr[4])=="open") {
                    if (strpos(trim($arr[7]),"/")!==0)
                        $eventFilePath = trim($arr[2])."/".trim($arr[7]);
                    else
                        $eventFilePath = trim($arr[7]);
                }
                else {
                    if (strpos(trim($arr[6]),"/")!==0)
                        $eventFilePath = trim($arr[2])."/".trim($arr[6]);
                    else
                        $eventFilePath = trim($arr[6]);
                }
            }
            if (trim($arr[4])=="open") {
                if (isset($arr[8])) {
                    if (strpos($arr[8],"/")!==0)
                            $eventFileNewPath = trim($arr[2])."/".trim($arr[8]);
                    else
                        $eventFileNewPath = trim($arr[8]);
                }
            } else {
                if (isset($arr[7])) {
                    if (strpos($arr[7],"/")!==0)
                        $eventFileNewPath = trim($arr[2])."/".trim($arr[7]);
                    else
                        $eventFileNewPath = trim($arr[7]);
                }
            }
            if ($eventType==$prev_event and $eventFilePath==$prev_file)
                continue;
            else {
                $prev_event = $eventType;
                $prev_file = $eventFilePath;
            }
            $date_arr = explode(" ",trim($arr[3]));
            $time_arr = $date_arr[1];
            $date_arr = $date_arr[0];
            $date_arr = explode("/",$date_arr);
            $time_arr = explode(":",$time_arr);
            
            $eventDate = mktime($time_arr[0],$time_arr[1],$time_arr[2],$date_arr[1],$date_arr[2],$date_arr[0]);//str_replace("\r","",str_replace("/","-",trim($arr[3])));
            $stmt=$this->adapter->dbh->prepare("INSERT INTO eventlog (eventDate,eventType,eventIP,eventFilePath,eventFileNewPath) VALUES(:eventDate,:eventType,:eventIP,:eventFilePath,:eventFileNewPath)");
            $stmt->bindParam(":eventDate",$eventDate);
            $stmt->bindParam(":eventType",$eventType);
            $stmt->bindParam(":eventIP",$eventIP);
            $stmt->bindParam(":eventFilePath",$eventFilePath);
            $stmt->bindParam(":eventFileNewPath",$eventFileNewPath);
            $stmt->execute();
            $eventFileNewPath = "";
        }
        $this->adapter->dbh->exec("OPTIMIZE TABLE eventlog");	        
    }
    
    function getHookProc($number) {
    	switch ($number) {
    		case '3': return "save";
    	}
    	return parent::getHookProc($number);
    }
}
?>