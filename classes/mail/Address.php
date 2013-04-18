<?php

/**
 * Класс управляет отдельным адресом в адресной книге. Адрес включает в себя
 * непосредственно адрес электронной почты $name и таблицу полей, которая хранится в
 * файле, Application->addrbookRuleFilesPath.$name.".data" . Файл имеет формат:
 *
 * Имя-поля: Значение
 *
 * Информация о всех адресах хранится в файле Application->addrbookFile в следующем
 * формате:
 *
 * To: Адрес Файл-полей-и-их-значений
 *
 * Объект хранит поля в свойстве dataFields, который является хэшем. В качестве ключа
 * выступает имя поля, а в качестве значения - значение поля. Методы addField и removeField
 * позволяют интеллектуально добавлять и удалять поля из этого массива.
 *
 * Метод setFields позволяет установить значения всех полей из строки с разделителями в виде:
 *
 * Поле~Значение|Поле~Значение.
 *
 * Метод initFields() - инициализирует массив dataFields для вновь созданного адреса.
 * Для этого он загржает список полей по умолчанию из файла Application->addrbookDefaultFieldsFile.
 *
 * Метод load() загружает данные об адресе из файла Application->addrbookFile, получает из него
 * имя файла со значениями полей для данного адреса и загружает поля из этого файла. При этом поля,
 * загруженные из файла по умолчанию затираются.
 *
 * Метод save() сохраняет данные об адреса в файл Application->addrbookFile, а также данные о полях
 * в файл Application->addrbookRuleFilePath.$name.".".$data, при необходимости создавая его.
 * 
 *
 * @author andrey
 */
class Address extends WABEntity {

    public $dataFields = array();

    function construct($params) {
        $this->module_id = $params[0]."_".$params[1];
        array_shift($params);
        array_shift($params);
        global $Objects;
        $app = $Objects->get("MailApplication_".$this->module_id);
        $app = $app->app;
        $this->fields["name"] = implode("_",$params);
        $this->loaded = false;
        $this->template = "templates/mail/Address.html";
        $this->css = $app->skinPath."styles/Mailbox.css";
        $this->skinPath = $app->skinPath;
        $this->handler = "scripts/handlers/mail/Mailbox.js";
        $this->icon = $app->skinPath."images/Tree/address.gif";
        $this->clientClass = "Address";
        $this->parentClientClasses = "Entity";        
        $this->initFields();
        $this->classTitle = "Адрес электронной почты";
        $this->classListTitle = "Адреса электронной почты";
    }

    function initFields() {
        global $Objects;
        $app = $Objects->get("MailApplication_".$this->module_id);
  //      echo $app->addrbookDefaultFieldsFile;
        $strings = file($app->remotePath.$app->addrbookDefaultFieldsFile);
 //       echo print_r($strings);
        for ($counter=0;$counter<count($strings);$counter++)
            $this->dataFields[trim(str_replace("\n","",$strings[$counter]))] = "";
    }

    function setFields($fields) {
        $this->dataFields = array();
        $arr = explode("~",$fields);
        for ($counter=0;$counter<count($arr);$counter++) {
            $parts = explode("|",$arr[$counter]);
            $this->dataFields[trim($parts[0])] = trim($parts[1]);
        }
    }

    function getFields() {
        $result = array();;
        foreach ($this->dataFields as $key=>$value) {
            $result[count($result)] = $key."|".$value;
        }
        $result = implode("~",$result);
        return $result;
    }

    function load() {
        global $Objects;
        $app = $Objects->get("MailApplication_".$this->module_id);
        $strings = @file($app->remotePath.$app->addrbookFile);
        for ($counter=0;$counter<count($strings);$counter++) {
            $parts = explode(" ",$strings[$counter]);
            if (isset($parts[1]))
                $cur_name = trim($parts[1]);
            if (isset($parts[2]))
                $cur_file = trim($parts[2]);            
            if (@$cur_name == $this->name)
            {
                    $this->file = @$cur_file;
                    $this->loaded = true;
                    $this->loaded_name = $this->name;
                    $strings = @file($app->remotePath.$this->file);
                    foreach($this->dataFields as $key=>$value)
                        unset($this->dataFields[$key]);

                    for ($counter1=0;$counter1<count($strings);$counter1++) {
                        $parts = explode(":",$strings[$counter1]);
                        if (!isset($parts[0]))
                            continue;
                        if (isset($parts[1]))
                            $cur_value = trim(str_replace("\n","",$parts[1]));
                        else
                            $cur_value = "";
                        $this->dataFields[trim(str_replace("\n","",$parts[0]))] = $cur_value;
                    }
                    break;
            }
        }
    }

    function save($arguments=null) {

        global $Objects;
        
		if (isset($arguments["name"])) {
			$this->load();
			$this->name = $arguments["name"];
		}
		if (isset($arguments["cells"]))
			$this->setFields($arguments["cells"]);
			
        if ($this->name == "") {
            $this->reportError("Укажите адрес","save");
            return 0;
        }

        if ($this->name != $this->loaded_name) {
            $addressBook = $Objects->get("AddressBook_".$this->module_id);
            if ($addressBook->contains($this->name)) {
                $this->reportError("Адрес ".$this->name." уже существует !","save");
                return 0;
            }
        }

        $app = $Objects->get("MailApplication_".$this->module_id);
        $strings = file($app->remotePath.$app->addrbookFile);
        $fp = fopen($app->remotePath.$app->addrbookFile,"w");
        $found = false;
        for ($counter=0;$counter<count($strings);$counter++) {
            $string = $strings[$counter];
            $parts = explode(" ",$string);
            if (trim($parts[1])==$this->loaded_name) {
                fwrite($fp,"To: ".$this->name." ".$app->addrbookRuleFilesPath.$this->name.".data\n");
                $found = true;
            }
            else
                fwrite($fp,$string);
        }
        if (!$found)
            fwrite($fp,"To: ".$this->name." ".$app->addrbookRuleFilesPath.$this->name.".data\n");
        fclose($fp);
        if (file_exists($app->remotePath.$app->addrbookRuleFilesPath.$this->loaded_name.".data"))
            unlink($app->remotePath.$app->addrbookRuleFilesPath.$this->loaded_name.".data");
        
        $fp = fopen($app->remotePath.$app->addrbookRuleFilesPath.$this->name.".data","w");

        foreach($this->dataFields as $key=>$value) {
            fwrite($fp,$key.":".$value."\n");
        }
        fclose($fp);
        $app = $Objects->get("Application");
        if (!$app->initiated)
        	$app->initModules();
        if ($this->loaded_name=="")
        	$app->raiseRemoteEvent("ADDRBOOK_ADDRESS_ADDED","object_id=".$this->getId());
        else
        	$app->raiseRemoteEvent("ADDRBOOK_ADDRESS_CHANGED","object_id=".$this->getId());
        $this->loaded_name = $this->name;
        $this->loaded = true;
        $shell = $Objects->get("Shell_Helix");
        $shell->exec_command($app->remoteSSHCommand." ".$app->restartMailScannerCommand);
    }

    function getPresentation() {
        return $this->name;
    }

    function getId() {
        return "Address_".$this->module_id."_".$this->name;
    }
    
    function getHookProc($number) {
		switch ($number) {
			case '3': return "save";
		}
		return parent::getHookProc($number);
	}
}
?>