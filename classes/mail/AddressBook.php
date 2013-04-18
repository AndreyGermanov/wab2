<?php
/**
 * Данный класс управляет адресной книгой.
 *
 * Адресная книга расположена в файле Application->addrbookFile, имеющий формат:
 *
 * To: адрес путь-к-файлу-полей
 *
 * Для  каждой такой строки создается объект Address.
 *
 * Коллекция хранится в массиве addresses. На самом деле этого массива не существует,
 * он является динамическим и формируется динамически с помощью $Objects->query("Address");
 *
 * С помощью метода loadAddresses() адреса загружаются из файла. С помощью метода
 * saveAddresses() адреса сохраняются в файл. Метод contains() позволяет выяснить,
 * существует ли адрес с указанным именем в файле.
 *
 * Функции addAddress() и removeAddress() соответственно добавляют и удаляют адрес из списка
 *
 * Также этот класс управляет списком полей по умолчанию. Именно эти поля с пустыми
 * значениями автоматически появляются при создании нового адреса.
 *
 * Этот список хранится в файле Application->addrbookDefaultFieldsFile.
 *
 * Метод loadDefaults() загружает этот список. Метод saveDefaults() сохраняет его в файл.
 *
 * Сами поля по умолчанию хранятся в массиве defaultFields. Метод getDefaultFields() позволяет получить
 * их список в виде строки с разделителями. Метод setDefaultFields() позволяет
 * установить список полей по умолчанию из строки с разделителями.
 *
 * @author andrey
 */
class AddressBook extends WABEntity {

    public $defaultFields = array();
    
    function construct($params="") {
        $this->module_id = @$params[0]."_".@$params[1];
        if (isset($params[2]))
            $this->object_id = $params[2];
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        $this->loaded = false;
        $this->template = "templates/mail/AddressBook.html";
        $this->css = $app->skinPath."styles/Mailbox.css";
        $this->handler = "scripts/handlers/mail/Mailbox.js";
        $this->icon = $app->skinPath."images/Tree/addrbook.gif";
        $this->skinPath = $app->skinPath;
        $this->clientClass = "AddressBook";
        $this->parentClientClasses = "Entity";        
        $this->classTitle = "Адресная книга";
        $this->classListTitle = "Адресная книга";
    }

    function load()
    {
        $this->loadAddresses();
    }

    function loadAddresses() {
        global $Objects;
        $app = $Objects->get("MailApplication_".$this->module_id);

        $strings = file($app->remotePath.$app->addrbookFile);
        for ($counter=0;$counter<count($strings);$counter++) {
            $parts = explode(" ",$strings[$counter]);
            if (!isset($parts[1]))
                continue;
            $addr=$Objects->get("Address_".$this->module_id."_".$parts[1]);
            $load = true;
            if ($addr->loaded)
                $load = false;
            if ($load) {
                if (!isset($parts[2]))
                    continue;
                $addr->name = trim($parts[1]);
                $addr->file = trim(str_replace("\n","",$parts[2]));
                $strings1 = file($app->remotePath.$addr->file);
                $addr->dataFields = array();
                for ($counter1=0;$counter1<count($strings1);$counter1++) {
                    $field_parts = explode(":",@$strings1[$counter1]);
                    if (!isset($field_parts[0]))
                        continue;
                    $addr->dataFields[trim($field_parts[0])] = trim(str_replace("\n","",@$field_parts[1]));
                }

                $addr->loaded = true;
                $addr->loaded_name = $addr->name;
            }
        }
        $this->loaded = true;
    }

    function contains($address) {
        if (!$this->loaded)
                $this->load();
        global $Objects;
        
        $result = $Objects->contains("Address_".$this->module_id."_".$address);
        if ($result) {
            $addr = $Objects->get("Address_".$this->module_id."_".$address);
            if (!$addr->loaded)
                    return false;
        }
        return $result;
    }

    function saveAddresses() {

        global $Objects;
        $app = $Objects->get("MailApplication_".$this->module_id);
        $fp = fopen($app->remotePath.$app->addrbookFile,"w");
        foreach ($this->addresses as $addr) {
            if (!$addr->loaded)
                    $addr->load();
            fwrite($fp,"To: ".$addr->name." ".$app->addrbookRuleFilesPath.$addr->name.".data\n");
            $fp1 = fopen($app->remotePath.$app->addrbookRuleFilesPath.$addr->name.".data","w");
            foreach($addr->dataFields as $key=>$value) {
                fwrite($fp1,trim($key).": ".trim(str_replace("\n","",$value))."\n");
            }
            fclose($fp1);
        }
        fclose($fp);
        $this->loaded = true;
        $shell = $Objects->get("Shell_Helix");
        $shell->exec_command($app->remoteSSHCommand." /etc/init.d/MailScanner reload >/dev/null 2>/dev/null");
    }

    function addAddress($addr) {
        if ($this->contains($addr)) {
            $this->reportError("Указанный адрес уже существует","addAddress");
            return 0;
        }
        global $Objects;
        return $Objects->get("Address_".$this->module_id."_".$addr);
    }

    function removeAddress($addr) {
		if (is_array($addr)) {
			$this->load();
			$addr = $addr["address"];
		}
        if (!$this->contains($addr)) {
            $this->reportError("Указанного адреса не существует","removeAddress");
            return 0;
        }
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
        	$app->initModules();
        $app->raiseRemoteEvent("ADDRBOOK_ADDRESS_DELETED","object_id="."Address_".$this->module_id."_".$addr);
        $Objects->remove("Address_".$this->module_id."_".$addr);
        $app = $Objects->get("MailApplication_".$this->module_id);
        unlink($app->remotePath.$app->addrbookRuleFilesPath.$addr.".data");
        $this->saveAddresses();
    }

    function loadDefaults() {
        global $Objects;
        $app = $Objects->get("MailApplication_".$this->module_id);
        $this->defaultFields = array();
        $strings = file($app->remotePath.$app->addrbookDefaultFieldsFile);
        foreach ($strings as $str) {
            $this->defaultFields[trim(str_replace("\n","",$str))] = trim(str_replace("\n","",$str));
        }
    }

    function saveDefaults() {
        global $Objects;
        $app = $Objects->get("MailApplication_".$this->module_id);
        $fp = fopen($app->remotePath.$app->addrbookDefaultFieldsFile,"w");
        foreach ($this->defaultFields as $fld) {
            fwrite($fp,trim($fld)."\n");
        }        
        fclose($fp);
        $app = $Objects->get("Application");
        if (!$app->initiated)
        	$app->initModules();
        $app->raiseRemoteEvent("ADDRBOOK_CHANGED");
    }

    function setDefaultFields($fields) {
        $arr = explode("|",$fields);
        foreach($arr as $value) {
            $this->defaultFields[trim($value)]=$value;
        }
    }

    function getDefaultFields() {
        return implode("|",$this->defaultFields);
    }

    function getPresentation() {
        return "Адресная книга";
    }

    function __get($name) {
        switch ($name) {
            case "addresses":
                global $Objects;
                return $Objects->query("Address");
            default:
                if (isset($this->fields[$name])) 
                        return $this->fields[$name];
                else
                    return "";
        }
    }
    
    function getHookProc($number) {
		switch ($number) {
			case '3': return "setDefaults";
			case '4': return "removeAddress";
		}
		return parent::getHookProc($number);
	}
	
	function setDefaults($arguments) {
		$this->setDefaultFields($arguments["fields"]);
		$this->saveDefaults();
	}
}
?>