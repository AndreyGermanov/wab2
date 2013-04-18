<?php
/**
 * 
 * Класс управляет информацией об удаленном почтовом ящике в Интернете.
 * Информация об удаленных почтовых ящиках в Интернете хранится в файле /etc/postfix/fetchmailrc
 * в следующем формате
 * 
 * (poll/skip) <сервер> proto <протокол> port <порт> user <имя-пользователя-сервера> to <локальный адрес> password <пароль> properties <имя-удаленного-ящика>
 * 
 * Благодаря этой информации, почтовый сервер будет раз в минуту выходить на указанный сервер, используя указанный порт и протокол, под указанным именем пользователя
 * и паролем, загружать всю почту и передавать ее на указанный локальный адрес, который может быть либо адресом локального почтового ящика, либо псевдонимом.
 * Адрес псевдонима может ссылаться на другие адреса почтовых ящиков или на любые другие адреса.
 * 
 * Имя ящика (его адрес) находится в свойстве properties и является исключительно вспомогательной информацией, чтобы ящик было просто найти в Интерфейсе.
 * 
 * Почта будет загружаться только с ящиков, определение которых начинается с поля poll. Если строка начинается со слова skip, то с этого ящика почта в автоматическом режиме
 * загружаться не будет. Информация о нем просто хранится в файле. В частности, если не указан локальный адрес в поле "to", то информация о ящике сохраняется только
 * как skip.
 * 
 * Соответственно, информацией можно управлять с помощью следующих переменных:
 * 
 * name - имя почтового ящика
 * server - имя сервера
 * protocol - имя протокола (auto, pop2, pop3, apop, rpop, kpop, sdps, imap, etrn, odmr)
 * port - имя порта (также для удобства создан вспомогаетльный массив protocol_ports, в котором в качестве ключа указывается протокол, а в качестве значения порт, который
 *           по умолчанию подставляется при выборе указанного протокола
 * user - имя пользователя
 * password - пароль
 * owner - локальный адрес
 * active - является ли ящик активным (установлен poll или skip)
 *
 * Дополнительное поле loaded определяет, является ли ящик новым, или информация о нем уже была загружена. Поля loaded_name, loaded_server и loaded_user соответственно
 * указывают, какое имя ящика, имя сервера и имя пользователя были загружены, для целей сравнения при записи.
 *
 * Для управления информацией используются следующие методы:
 *
 * load - загрузка данных о ящике из файла
 * save - запись данных о ящике в файле
 * getArgs - формирование массива подстановок для шаблонов
 * getId - получает уникальный идентификатор объекта
 * getPresentation - получает представление объекта
 *
 * Также для взаимодействия с интерфейсной частью, используются следующие свойства и методы:
 *
 * template - файл шаблона разметки, по которому отображается объект
 * css - файл стилевой спецификации по которой форматируется шаблон разметки
 * handler - файл обработчика на JavaScript, который загружается после шаблона и выполняет действия по инициализации объекта
 *
 * Метод show() отображает шаблон на экрнане. Этот метод наследуется от класса Entity. В классе Entity метод show() вызывает метод parseTemplate(), в который
 * передается шаблон, стилевая спецификация и обработчик. Этот метод создает экземпляр класса Template, в который передается экземпляр объекта, все его данные. Класс
 * Template вызывает метод parse, который выводит объект на экран, используя файлы template, css и handler, а также файл класса JavaScript, созданного для этого объекта.
 * При выводе выполняются подстановки из массива, формируемого функцией getArgs(). После того как шаблон выведен, к нему привязывается класс объекта на JavaScript,
 * который создает экземпляр для указанного объекта и вызывает процедуру onLoadTemplate, которая со своей стороны вызывает функции changeIds() и fillSelects(). Первая
 * подменяет все атрибуты id в шаблоне, подставляя в каждый ID имя объекта, для организации уникальности. Метод fillSelects заполняет все раскрывающиеся списки из атрибута
 * collection, если есть. 
 * 
 */

class RemoteMailbox extends WABEntity {
    /**
     *
     * Конструктор почтового ящика
     *
     * @param <массив> $params - массив передаваемых параметров. В качестве первого элемента указывается имя ящика
     */

    var $protocol_ports = array();

    function  construct($params) {
        $this->module_id = array_shift($params);
        $this->module_id.= "_".array_shift($params);
        // Присваиваем объекту имя
        $this->fields["name"] = implode("_",$params);
        // Присваиваем параметры по умолчанию
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        $this->active = "poll";
        $this->protocol = "pop3";
        $this->port = 110;
        $this->loaded = false;
        $this->owner = "";
        $this->server = "";
        $this->user = "";
        // Присваиваем шаблон представления и обработчик по умолчанию
        $this->template = "templates/mail/RemoteMailbox.html";
        $this->css = $app->skinPath."styles/Mailbox.css";
        $this->handler = "scripts/handlers/mail/Mailbox.js";
        $this->icon = $app->skinPath."images/Tree/RemoteMailbox.gif";
        $this->skinPath = $app->skinPath;
        $this->changed = true;
        $this->width="400";
        $this->height="370";
        $this->overrided = "width,height";
        $this->protocol_ports["auto"] = 110;
        $this->protocol_ports["pop2"] = 0;
        $this->protocol_ports["pop3"] = 110;
        $this->protocol_ports["apop"] = 0;
        $this->protocol_ports["rpop"] = 0;
        $this->protocol_ports["kpop"] = 0;
        $this->protocol_ports["sdps"] = 0;
        $this->protocol_ports["imap"] = 143;
        $this->protocol_ports["etrn"] = 0;
        $this->protocol_ports["odmr"] = 0;
        $this->clientClass = "RemoteMailbox";
        $this->parentClientClasses = "Entity";        
	    $this->classTitle = "Почтовый ящик Интернет";
	    $this->classListTitle = "Почтовые ящики Интернет";
    }

    /**
     *
     *  Функция загружает информацию о почтовом ящике из файла Application->fetchmailFile
     *
     * @global <массив> $Objects - хранилище загруженных объектов
     */
    function load()
    {
        global $Objects;

        if ($this->name=="")
                return 0;
        
        // Получаем ссылку на текущее приложение
        $app = $Objects->get("MailApplication_".$this->module_id);

        // открываем файл удаленных почтовых ящиков и ищем в цикле информацию о ящике с указанным именем
        $strings = file($app->remotePath.$app->fetchmailFile);
        for ($counter=0;$counter<count($strings);$counter++)
        {
            $matches = array();
            $line = $strings[$counter];
            // ищем строку, соответствующую указанному шаблону
            // если строка соответствует шаблону, загружаем данные из этой строки в поля объекта
            if (preg_match("/(poll|skip) (.*)proto (.*)port (.*)user (.*)to (.*)password (.*)properties (".$this->name.")/U",$line,$matches)==1)
            {                
                $this->active = trim($matches[1]);
                $this->server = trim($matches[2]);
                $this->protocol = trim($matches[3]);
                $this->port = trim($matches[4]);
                $this->user = trim($matches[5]);
                $this->owner = trim($matches[6]);
                $this->password = trim($matches[7]);
                
                // устанавливаем признак того что загруженные данные соответствуют сохраненным в файле
                $this->loaded = true;
                $this->loaded_name = $this->name;
                $this->loaded_server = $this->server;
                $this->loaded_user = $this->user;
                $this->loaded_owner = $this->owner;               
                $this->error = "";          
                $this->changed = "false";
                $this->loaded = "true";
                break;
            }
        }
    }

    /**
     *
     * Функция сохраняет информацию о почтовом ящике в файле Application->fetchmailFile
     *
     * @global <массив> $Objects = хранилище загруженных объектов
     * @return 0;
     */
    function save($arguments=null)
    {
        global $Objects;
        if (isset($arguments)) {
        	$this->load();
        	$this->setArguments($arguments);
        }
        // Начинаем операцию только если у почтового ящика были изменены свойства, которые должны храниться в файле, иначе выходим
        // см. функцию __set
        if (!$this->changed)
                return 0;

        // Цикл проверок введенности данных (имя почтового ящика, название сервера, имя пользователя)
        if ($this->name =="") {
                $this->reportError("Укажите название почтового ящика !","save");
                return 0;
        }

        if ($this->name!=$this->loaded_name)
        {
            // Если ящик с таким именем уже есть, то выдаем ошибку
            $remote_mailboxes = $Objects->get("RemoteMailboxes_".$this->module_id);
            if ($remote_mailboxes->contains($this->name))
            {
                if ($Objects->get("RemoteMailbox_".$this->module_id."_".$this->name)->loaded)
                {
                    $this->reportError("Почтовый ящик ".$this->name." уже существует! ","save");
                    return 0;
                }
            }
        }        

        if ($this->server =="")
        {
                $this->reportError("Укажите имя сервера !","save");
                return 0;
        }
        if ($this->user =="")
        {
                $this->reportError("Укажите имя пользователя !","save");
                return 0;
        }

        // Если не указан протокол, по умолчанию используем POP3
        if ($this->protocol=="")
                $this->protocol = "pop3";

        // Если не указан порт, используем порт по умолчанию, который используется для указанного протокола
        if ($this->port=="")
                $this->port = @$protocol_ports[$this->protocol];

        // получаем доступ к коллекции локальных почтовых ящиков
        $mailboxes = $Objects->get("Mailboxes_".$this->module_id);
        $mail_aliases = $Objects->get("MailAliases_".$this->module_id);
        // Если локальный почтовыя ящик не указан, автоматически ставим тип записи в неактивную (skip)       
        if ($this->owner=="" or $this->active=="")
        {
            $this->active = "skip";
        }
        else
        {
            // Если в качестве локального почтового ящика указан адрес, не существующий в коллекции локальных
            //почтовых ящиков, прерываемся
            $owner_parts = explode("@",$this->owner);
            if (count($owner_parts)!=2)
            {
                    $this->reportError("Адрес владельца указан неверно !","save");
                    return 0;
            }
            $mailboxes->load();
            $mail_aliases->load();
            if (!$mailboxes->contains($owner_parts[0],$owner_parts[1]))
            {
                if (!$mail_aliases->contains($owner_parts[0],$owner_parts[1]))
                {
                    $this->reportError("Указанный адрес владельца '$this->owner' не существует !","save");
                    return 0;
                }
            }
        }

        // Формируем строку для записи в файле
        $target_line = $this->active." ".$this->server." proto ".$this->protocol." port ".$this->port." user ".$this->user." to ".$this->owner." password ".$this->password." properties ".$this->name;
        // Получаем доступ к приложению
        $app = $Objects->get("MailApplication_".$this->module_id);

        // открываем файл для записи
        $strings = file($app->remotePath.$app->fetchmailFile);

        // ищем в нем строку, соответствующую сохраняемому почтовому ящику
        $found = false;
        for ($counter=0;$counter<count($strings);$counter++)
        {
            if ($this->loaded_name=="")
                break;
            $matches = array();
            preg_match("/(poll|skip) (.*)proto (.*)port (.*)user (.*)to (.*)password (.*)properties (".$this->loaded_name.")/U",$strings[$counter],$matches);
            // Если соответствие найдено, подменяем эту строку на сформированную ранее
            if (count($matches)>1)
            {
                $found = true;
                $strings[$counter] = $target_line."\n";
                break;
            }
        }
        // Если строка не найдена, добавляем новую строку в конец файла
        if (!$found)
            $strings[count($strings)] = $target_line."\n";

        // Перезаписываем обновленный файл на диск
        $fp = fopen($app->remotePath.$app->fetchmailFile,"w");
        for ($counter=0;$counter<count($strings);$counter++)
            fwrite($fp,$strings[$counter]);
        fclose($fp);

        // Присваиваем признак того что загруженные данные соответствуют сохраненным
        $this->loaded = true;
        
        $app = $Objects->get("Application");
        if (!$app->initiated)
        	$app->initModules();
        if ($this->loaded_name=="")
        	$app->raiseRemoteEvent("REMOTEMAILBOX_ADDED","object_id=".$this->getId());
        else
        	$app->raiseRemoteEvent("REMOTEMAILBOX_DELETED","object_id=".$this->getId());
        $this->loaded_name = $this->name;
        $this->loaded_user = $this->user;
        $this->loaded_server = $this->server;
        $this->loaded_owner = $this->owner;
        $this->changed = false;
    }

    /**
     *
     * Функция формирует массив аргументов для подстановки в шаблоны
     * 
     * @return <массив>
     */
    function getArgs()
    {
        $result = parent::getArgs();
        $result["{protocols}"] = implode(",",array_keys($this->protocol_ports))."|".implode(",",array_keys($this->protocol_ports));
        $result["{protocol_ports}"] = implode(",",array_keys($this->protocol_ports))."|".implode(",",array_values($this->protocol_ports));
        if ($this->active == "poll")
            $result["{active_checked}"] = "checked";
        else
            $result["{active_checked}"] = "";
        return $result;
    }

    /**
     * Функция получает уникальный идентификатор объекта
     */
    function getId()
    {
        return get_class($this)."_".$this->module_id."_".$this->name;
    }

    /**
     * Функция получает строковое представление объекта
     * @return <строка>
     */
    function getPresentation()
    {
        return $this->name;
    }

    /**
     *
     *  Функция обработчик установки значений свойств для объекта
     *
     * @global <массив> $Objects хранилище загруженных объектов
     * @param <строка> $name имя свойства
     * @param <type> $value значение свойства
     * @return <ничего>
     */
    function __set($name,$value)
    {
        global $Objects;

        // Если предыдущие проверки не сбойнули и свойство изменилось
        if (!isset($this->fields[$name]))
                $this->fields[$name] = "";

        if ("$this->fields[$name]"!=$value)
        {
            // то устанавливаем свойство
            $this->fields[$name]=$value;
            // А если это свойство, которое должно записываться в файл,
            if ($name == "protocol" || $name =="port" || $name == "active" || $name == "password" || $name=="name" ||
                $name == "server" || $name =="user" || $name == "owner" )
            {
                // То устанавливаем признак того, что данные о почтовом ящике были изменены
                $this->fields["changed"] = true;
            }
        }
    }
    
    function getHookProc($number) {
    	switch ($number) {
    		case '3': return "save";
    	}
    	return parent::getHookProc($number);
    }
}
?>