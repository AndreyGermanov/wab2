<?php
/* 
 * Класс управляет списком удаленных почтовых ящиков Интернет, то есть массивом объектов класса RemoteMailbox.
 * Класс позволяет загрузить данные в этот массив (создав соответствующие объекты) из файла Application->fetchmailFile
 * и сохранять данные из массива в этот файл.
 *
 * Массив хранится в свойстве $mailboxes. Понятие "хранится" очень условно. На самом деле все объекты хранятся
 * в глобальном массиве объектов Objects, а это свойство просто отбирает все объекты RemoteMailbox из него.
 * Признак того что массив был загружен из файла хранится в свойстве $loaded.
 *
 * С помощью данного класса можно добавлять почтовые ящики в массив и удалять их из файла.
 *
 * Для управления списком удаленных почтовых ящиков предназначены методы:
 *
 * load - для загрузки данных из файла
 * save - для записи данных в файл
 * contains - для проверки, существует ли ящик с указанным именем в массиве
 * add - создание нового ящика
 * remove - удаление существующего ящика
 * get - получение почтового ящика с указанным именем
 * getByOwner - получение всех почтовых ящиков, принадлежащих указанному адресу
 * getByServer - получение всех почтовых ящиков, принадлежащих указанному серверу
 */
class RemoteMailboxes extends WABEntity {
    /**
     * Конструктор списка объектов
     */
    function construct($params="") {
        $this->module_id = $params[0]."_".$params[1];
        $this->loaded = false;
        $this->clientClass = "RemoteMailboxes";
        $this->parentClientClasses = "Entity";        
    }

    /**
     *
     * Загружает данные о почтовых ящиках из файла Application->fetchmailFile,
     * создавая при этом объекты почтовых ящиков в глобальном хранилище $Objects;
     * Загружаются только еще не загруженные объекты. Объекты, которые уже были
     * загружены и изменены функция не трогает.
     * 
     * @global <массив> $Objects хранилище загруженных объектов
     */
    function load()
    {
        global $Objects;
        $app = $Objects->get("MailApplication_".$this->module_id);
        $strings = file($app->remotePath.$app->fetchmailFile);        
        for ($counter=0;$counter<count($strings);$counter++)
        {
            $matches = array();
            $line = $strings[$counter];
            // ищем строку, соответствующую указанному шаблону
            preg_match("/(poll|skip) (.*)proto (.*)port (.*)user (.*)to (.*)password (.*)properties (.*)\n/U",$line,$matches);
            // если строка соответствует шаблону, загружаем данные из этой строки в поля объекта
            if (count($matches)>1)
            {   
                $load = true;
                if ($Objects->contains("RemoteMailbox_".$this->module_id."_".$matches[8]))
                {
                    $mbox=$Objects->get("RemoteMailbox_".$this->module_id."_".$matches[8]);
                    if ($mbox->loaded)
                            $load = false;
                }
                else
                {
                    $mbox=$Objects->get("RemoteMailbox_".$this->module_id."_".$matches[8]);
                    $load = true;
                }
                if ($load)
                {
                    $mbox->fields["active"] = trim($matches[1]);
                    $mbox->fields["server"] = trim($matches[2]);
                    $mbox->fields["protocol"] = trim($matches[3]);
                    $mbox->fields["port"] = trim($matches[4]);
                    $mbox->fields["user"] = trim($matches[5]);
                    $mbox->fields["owner"] = trim($matches[6]);
                    $mbox->fields["password"] = trim($matches[7]);
                    $mbox->fields["name"] = str_replace("\n","",trim($matches[8]));
                    // устанавливаем признак того что загруженные данные соответствуют сохраненным в файле
                    $mbox->fields["loaded"] = true;
                    $mbox->fields["loaded_name"] = $mbox->name;
                    $mbox->fields["loaded_server"] = $mbox->server;
                    $mbox->fields["loaded_user"] = $mbox->user;
                    $mbox->fields["loaded_owner"] = $mbox->owner;
                    $mbox->fields["changed"] = false;
                }
            }            
        }
        $this->loaded = true;
    }

    /**
     *
     * Функция проверяет, существует ли почтовый ящик с указанным именем
     *
     * @global  $Objects хранилище загруженных объектов
     * @param <строка> $name Имя почтового ящика
     * @return <булево>
     */
    function contains($name)
    {
        global $Objects;
        if (!$this->loaded)
            $this->load();
        if ($this->loaded)
        {
            if ($Objects->contains("RemoteMailbox_".$this->module_id."_".$name))
                return true;
            else
                return false;
        } else return false;
    }

    /**
     *
     * Функция возвращает все почтовые ящики у которых указанное поле имеет указанное значение
     * Функция может возвращать результат в виде массива объектов
     * 
     * @global  $Objects Хранилище загруженных объектов
     * @param <строка> $field_name Имя поля
     * @param <строка> $field_value Значение поля
     * @return <массив или строка>
     */
    function getByField($field_name,$field_value)
    {
        global $Objects;
        if ($this->loaded!=true)
                $this->load();
        return $Objects->query("RemoteMailbox",array($field_name=>$field_value));
    }

    /**
     *
     * Функция получает список почтовых ящиков, принадлежащих указанному хозяину
     *
     * @param <строка> $owner имя локального почтового ящика-владельца
     * @return <массив или строка>
     */
    function getByOwner($owner)
    {
        return $this->getByField("owner",$owner);
    }

    /**
     *
     * Функция получает список почтовых ящиков, находящихся на указанном сервере
     *
     * @param <строка> $server имя сервера
     * @return <type>
     */
    function getByServer($server)
    {
        return $this->getByField("server",$server);
    }

    /**
     *
     * Возвращает почтовый ящик по имени
     *
     * @global <массив> $Objects хранилище загруженных объектов
     * @param <строка> $name имя почтового ящика
     * @return <type> объект
     */
    function get($name)
    {
        global $Objects;
        return $Objects->get("RemoteMailbox_".$this->module_id."_".$name);
    }

    /** Функция-обработчик, возвращающий значения запрашиваемых полей
     *
     * @global <массив> $Objects хранилище загруженных объектов
     * @param <строка> $name имя запрашиваемого поля
     * @return <значение свойства из массива fields>
     */
    function __get($name)
    {
        global $Objects;
        switch ($name)
        {
            // массив почтовых ящиков
            case "mailboxes":
                return $Objects->query("RemoteMailbox");
            default:
                // иначе делаем то же самое, что и предок Entity, т.е. возвращаем значение свойства, если такое свойство есть
                // или пустую строку, если свойства нет
                if (isset($this->fields[$name]))
                    return $this->fields[$name];
                else
                    return "";
        }
    }

    /**
     *
     * Функция сохраняет массив загруженных почтовых ящиков в файл, удаляя все его содержимое перед этим
     *
     * @global <массив> $Objects хранилище загруженных объектов
     */
    function save()
    {
        global $Objects;
        // получаем объект-приложение
        $app = $Objects->get("MailApplication_".$this->module_id);
        // Открываем файл для записи
        $fp = fopen($app->remotePath.$app->fetchmailFile,"w");

        // Проходим по всему массиву почтовых ящиков и записываем каждый в файл
        foreach($this->mailboxes as $mbox)
        {
            fwrite($fp,$mbox->active." ".$mbox->server." proto ".$mbox->protocol." port ".$mbox->port." user ".$mbox->user." to ".$mbox->owner." password ".$mbox->password." properties ".$mbox->name."\n");
        }
        // закрываем файл
        fclose($fp);
    }

   /**
    *
    * Функция создает новый почтовый ящик и добавляет его в массив (но не в файл), при условии что еще такого нет ни в массиве, ни в файле
    *
    * @global <массив> $Objects хранилище загруженных объектов
    * @param <строка> $name имя почтового ящика
    * @return <объект> почтовый ящик
    */
    function add($name)
    {
        global $Object;
        
        $app = $Objects->get("Application");
        if (!$app->initiated)
        	$app->initModules();
        $app->raiseRemoteEvent("REMOTEMAILBOX_DELETED","object_id="."RemoteMailbox_".$this->module_id."_".$name);
        
        // Если такой ящик уже есть, сбоим и выходим
        if ($this->contains($name))
        {
                $this->reportError("Создаваемый почтовый ящик уже существует !","add");
                return 0;
        }
        // иначе добавляем ящик в коллекцию и возвращаем его
        return $Objects->get("RemoteMailbox_".$this->module_id."_".$name);
    }

    /**
     *
     * Функция удаляет почтовый ящик из массива объектов и из файла, при условии что он есть в массиве
     *
     * @global <массив> $Objects хранилище загруженных объектов
     * @param <строка> $name имя почтового ящика
     * @return <ничего>
     */
    function remove($name)
    {
        global $Objects;
        if (is_array($name) or is_object($name)) {
        	$this->load();
        	$name = $name["name"];
        }
        $name = trim($name);
        // Если такого почтового ящика нет, то сообщаем об ошибке и выходим
//        if (!$this->contains($name))
//        {
//            $this->reportError("Указанный почтовый ящик не существует ".$name." ! ","remove");
//            return 0;
//        }
        // получаем почтовый ящик с указанным именем
        $mbox = $Objects->get("RemoteMailbox_".$this->module_id."_".$name);
        // получаем объект приложения
        $app = $Objects->get("MailApplication_".$this->module_id);
        // Получаем из файла все строки
        $strings = file($app->remotePath.$app->fetchmailFile);
        // открываем файл для записи
        $fp = fopen($app->remotePath.$app->fetchmailFile,"w");
        // Проходим по массиву полученных из файла строк
        for ($counter=0;$counter<count($strings);$counter++)
        {
            $matches = array();
            $line = $strings[$counter];
            // Смотрим, соответствует ли строка нашему удаляемому почтовому ящику
            preg_match("/(poll|skip) (.*)proto (.*)port (.*)user (.*)to (.*)password (.*)properties (".$mbox->name.")/U",$line,$matches);
            // если да, то пропускаем ее, иначе записываем строку в файл
            if (count($matches)<=0)
                fwrite($fp,$line);            
        }
        // в итоге получается что мы записали в файл все строки кроме этой.
        // Закрываем файл
        fclose($fp);
        // Удаляем объект почтового ящика из хранилища объектов.
        $mbox = null;
        $Objects->remove("RemoteMailbox_".$this->module_id."_".$name);
    }
    
    function getHookProc($number) {
    	switch ($number) {
    		case '3': return "remove";
    	}
    	return parent::getHookPRoc($number);
    }
}
?>