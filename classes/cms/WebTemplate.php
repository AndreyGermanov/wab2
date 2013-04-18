<?php
/**
 * Класс для хранения шаблонов дизайна страниц Web-сайта
 * 
 * Шаблон дизайна это совокупность из четырех файлов, которые участвуют в 
 * отображении сущности на клиенте методом WABEntity->parseTemplate:
 * 
 * * Шаблон разметки (template)
 * * Стилевая спецификация (css)
 * * Обработчик (handler)
 * * Класс (class)
 * 
 * Данный класс представляет из себя сущность, которая хранит пути к этим файлам:
 * 
 * template_file, css_file, handler_file и class_file.
 * 
 * Шаблон также позволяет редактировть эти файлы, для этого предусмотрены 
 * соответствующие поля типа text:
 * 
 * template_text, css_text, handler_text и class_text.
 * 
 * У template_file атрибут control_type="timyMCE", у остальных - editArea.
 * 
 * Он хранит это в четырех строковых полях, которые в шаблоне отображаются как
 * элемент управления InputControl file.
 * 
 * Пути к этим файлам находятся соответственно на четырех закладках: "Разметка",
 * "Оформление", "Обработчик" и "Класс". На каждой закладке находится по два
 * поля: _file и _text. При изменении значения в поле _file, этот файл
 * автоматически загружается в поле "_text". Для выполнения этой процедуры 
 * выполняется запрос к серверу: (if(file_exists($file)) echo file_get_contents($file);
 * Результирующая строка заполняет соответствующее поле.
 * 
 * При сохранении проверяется заполненность всех полей (они должны быть заполнены).
 * В базу сохраняются пути к файлам. Сами файлы тоже сохраняются методом file_put_contents.
 * Если файла нет, он автоматически создается.
 *
 *
 * @author andrey
 */
class WebTemplate extends WABEntity {
    
    function construct($params) {                
        parent::construct($params);
        global $Objects;
        if ($this->module_id=="")
            $this->adapter = $Objects->get("PDODataAdpater_".get_class($this)."_".$this->name);
        else
            $this->adapter = $Objects->get("PDODataAdapter_".$this->module_id."_".get_class($this)."_".$this->name);
        $this->adapter->dbEntity = "";        
        if ($this->module_id!="")
            $webapp = $Objects->get($this->module_id);
        else
            $webapp = $Objects->get($_SERVER["MODULE_ID"]);       
        $this->adapter->driver = "pdo_sqlite";
        $this->adapter->path = $webapp->templatesDB;

        $this->template = "templates/cms/WebTemplate.html";
        $this->handler = "scripts/handlers/cms/WebTemplate.js";

        $this->tabs_string = "template|Разметка|".$this->skinPath."images/spacer.gif;";
        $this->tabs_string.= "css|Оформление|".$this->skinPath."images/spacer.gif;";
        $this->tabs_string.= "handler|Обработчик|".$this->skinPath."images/spacer.gif;";
        $this->tabs_string.= "class|Класс|".$this->skinPath."images/spacer.gif";
        $this->active_tab = "template";
        
        $this->title = "";
        $this->template_file = "";
        $this->css_file = "";
        $this->handler_file = "";
        $this->class_file = "";
        $this->template_text = "";
        $this->css_text = "";
        $this->handler_text = "";
        $this->class_text = "";
        $this->persistedFields = $this->explodePersistedFields(array());
        $this->persistedFields["template_file"]["params"]["rootPath"] = $_SERVER["DOCUMENT_ROOT"]."/templates";
        $this->persistedFields["template_file"]["params"]["root_dir"] = $_SERVER["DOCUMENT_ROOT"];
        $this->persistedFields["css_file"]["params"]["rootPath"] = $_SERVER["DOCUMENT_ROOT"]."/styles";
        $this->persistedFields["css_file"]["params"]["root_dir"] = $_SERVER["DOCUMENT_ROOT"];
        $this->persistedFields["handler_file"]["params"]["rootPath"] = $_SERVER["DOCUMENT_ROOT"]."/scripts/handlers";
        $this->persistedFields["handler_file"]["params"]["root_dir"] = $_SERVER["DOCUMENT_ROOT"];
        $this->persistedFields["class_file"]["params"]["rootPath"] = $_SERVER["DOCUMENT_ROOT"]."/scripts/classes";
        $this->persistedFields["class_file"]["params"]["root_dir"] = $_SERVER["DOCUMENT_ROOT"];
        
        $this->fieldList = 'title Наименование';
        $this->presentationField = "title";
        
        $this->clientClass = "WebTemplate";
        $this->parentClientClasses = "Entity";        
    }

    function getArgs() {
        $this->icon = $this->skinPath."images/Tree/templates.gif";
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        if (file_exists($app->root_path."/".$this->tempale_file))
            $this->template_text = @file_get_contents($app->root_path."/".$this->template_file);
        $result = parent::getArgs();
        return $result;
    }
    
    function getPresentation() {
        return parent::getPresentation();
    }
    
    function load() {
        parent::load();        
    } 
    
    function checkData() {
        
        if ($this->title == "") {
            $this->reportError("Укажите название шаблона !");
            return false;
        }
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        file_put_contents($app->root_path."/".$this->template_file,$this->template_text);
        file_put_contents($app->root_path."/".$this->css_file,$this->css_text);
        file_put_contents($app->root_path."/".$this->handler_file,$this->handler_text);
        file_put_contents($app->root_path."/".$this->class_file,$this->class_text);
        return parent::checkData();
    }
    
    function getHookProc($number) {
    	switch ($number) {
    		case '3': return "addHook";
    		case "4": return "getFileContents";
    	}
    	return parent::getHookProc($number);
    }
    
    function addHook($arguments) {
    	global $Objects;
    	$object->parent=$Objects->get($arguments["item"]);
    	$object->parent->load();
    	$object->persistedFields=$object->parent->persistedFields;    	
    }
    
    function getFileContents($arguments) {
    	echo file_get_contents(@$arguments["file"]);
    }
}
?>