<?php
/*
 * Класс предназначен для управления шаблоном элемента.
 * Элемент это некая совокупность полей данных, а шаблон определяет, как
 * эти данные будут отображаться в Web-браузере.
 *
 * Шаблон состоит из четырех файлов:
 * template - файл с HTML-разметкой
 * css - стилевая спецификация, в которой описывается как оформляется разметка
 * handler - обработчик на JavaScript, который выполняется после того как шаблон
 * загружен и отображен
 * class - класс-обертка над шаблоном, в который оборачивается шаблон и управляется
 * с помощью методов этого класса. Он включает в частности обработчики событий
 * элементов шаблона
 *
 * Этот класс хранит шаблон в таблице SQLite, в виде набора полей
 * id - идентификатор записи
 * parent_id - идентификатор родительской записи
 * title - имя шаблона
 * template - имя файла с HTML-разметкой
 * css - имя файла со стилевой спецификацией
 * handler - имя файла с обработчиком шаблона
 * class - имя файла с классом-оберткой шаблона
 *
 * Таблица хранится в файле templates/templates.db.
 *
 * Таблица шаблонов может быть иерархической. Каждый элемент может принадлежать
 * другому элементу, элементу с идентификатором parent_id.
 *
 * При создании нового элемента, поля template, css, handler и class в качестве
 * значений по умолчанию принимают значения этих полей предка.
 *
 * Для работы с классом используются методы load() и save(), названия которых
 * говорят за себя.
 *
 * Метод remove() удаляет текущую запись из базы и из кэша объектов
 *
 * Метод getChildren() возвращает массив всех дочерних элементов данного шаблона
 * 
 * Также используются методы getId() и getPresentation() для получения системного
 * идентификатора объекта и для его представления
 *
 */

class ItemTemplate extends WABEntity {

    function construct($params="") {
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
            //echo print_r($params);
            if (count($params)>2) {
			$this->module_id = $params[0]."_".$params[1];
			$this->fields["id"] = $params[2];
                        $this->base_id = $params[2];
			$base = 3;
		} else {
			$this->module_id = "";
			$this->fields["id"] = $params[0];
			$base = 1;
		}
        if (isset($params[$base]))
            $this->parent_id = $params[$base];
        else
            $this->parent_id = 0;
        $this->template = "templates/ItemTemplate.html";
        $this->css = $app->skinPath."styles/Mailbox.css";
        $this->handler = "scripts/handlers/ItemTemplate.js";
        $this->icon = $app->skinPath."images/Tree/templates.gif";
        $this->skinPath = $app->skinPath;
        $this->loaded = false;

        $this->title = "";
        $this->templatefile = "";
        $this->cssfile = "";
        $this->handlerfile = "";
        $this->classfile = "";
        $this->templatetext = "";
        $this->csstext = "";
        $this->handlertext = "";
        $this->classtext = "";
        $this->active_tab = "file";
        $this->tabset_id = "ItemTemplateTabset_".$this->module_id."_".$this->base_id."_up";

        $this->width = "730";
        $this->height = "440";
        $this->overrided = "width,height";
    }

    function load() {
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        if ($this->module_id!="")
			$collection = $Objects->get("ItemTemplates_".$this->module_id);
		else
			$collection = $Objects->get("ItemTemplates");
        $collection->connect();
        $record = new ItemTemplateRecord();
        if ($this->fields["id"]==0 or $this->fields["id"]=="") {
            return 0;
        }
        $record = $record->getTable()->find($this->fields["id"]);
        $arr = $record->toArray();
        if (is_array($arr)) {
            $this->id = $record->id;
            $this->parent_id = $record->parent_id;
            $this->title = $record->title;
            $this->loaded_title = $record->title;
            $this->templatefile = $record->template_file;
            if (substr($record->css_file,0,1)=="/")
                $this->cssfile = $record->css_file;
            else
                $this->cssfile = $app->skinPath.$record->css_file;
            $this->handlerfile = $record->handler_file;
            $this->classfile = $record->class_file;
            $this->loaded_templatefile = $record->template_file;
            $this->loaded_cssfile = $record->css_file;
            $this->loaded_handlerfile = $record->handler_file;
            $this->loaded_classfile = $record->class_file;

            if (file_exists($this->templatefile)) {
                $this->templatetext = str_replace('/#n','\n',file_get_contents($this->templatefile));
            }
            else
                $this->templatetext = "";

            if (file_exists($this->cssfile)) {
                $this->csstext = str_replace('/#n','\n',file_get_contents($this->cssfile));
            }
            else
                $this->csstext = "";

            if (file_exists($this->handlerfile)) {
                $this->handlertext = str_replace('/#n','\n',file_get_contents($this->handlerfile));
            }
            else
                $this->handlertext = "";

            if (file_exists($this->classfile)) {
                $this->classtext = str_replace('/#n','\n',file_get_contents($this->classfile));
            }
            else
                $this->classtext = "";

            $this->loaded_templatetext = $this->templatetext;
            $this->loaded_csstext = $this->csstext;
            $this->loaded_handlertext = $this->handlertext;
            $this->loaded_classtext = $this->classtext;
            $this->loaded = true;
        }
    }

    function save() {
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        if ($this->module_id!="")
			$collection = $Objects->get("ItemTemplates_".$this->module_id);
		else
			$collection = $Objects->get("ItemTemplates");
        $collection->connect();
        $record = new ItemTemplateRecord();
        if ($collection->contains_id($this->fields["id"]))
            $record = $record->getTable()->find($this->fields["id"]);        
        if ($this->title != $this->loaded_title)
            if ($collection->contains_title($this->fields["title"])) {
                $this->reportError("Шаблон с названием '".$this->title." уже существует !","save");
            return 0;
        }
        if ($this->fields["id"]!="")
            $record->id = $this->fields["id"];
        $record->parent_id = $this->parent_id;
        $record->title = $this->title;
        $record->template_file = $this->templatefile;
        $record->css_file = str_replace($app->skinPath,"",$this->cssfile);
        $record->handler_file = $this->handlerfile;
        $record->class_file = $this->classfile;
        $record->save();
        $this->fields["id"] = $record->id;
        $this->csstext = str_replace("\'","'",urldecode($this->csstext));
        $this->handlertext = str_replace("\'","'",urldecode($this->handlertext));
        $this->classtext = str_replace("\'","'",urldecode($this->classtext));
        $this->templatetext = str_replace("\'","'",urldecode($this->templatetext));

        shell_exec("mkdir -p ".dirname($this->templatefile));
        shell_exec("mkdir -p ".dirname($this->cssfile));
        shell_exec("mkdir -p ".dirname($this->handlerfile));
        shell_exec("mkdir -p ".dirname($this->classfile));

        if ($this->templatefile!="" && $this->templatetext!=$this->loaded_templatetext) {
            $fp = fopen($this->templatefile,"w");
            $strings = explode("\n",$this->templatetext);
            foreach ($strings as $string) {
                fwrite($fp,$string."\n");
            }
            fclose($fp);
        }
        if ($this->cssfile!="" && $this->csstext!=$this->loaded_csstext) {
            $fp = fopen($this->cssfile,"w");
            $strings = explode("\n",$this->csstext);            
            for ($counter=0;$counter<count($strings)-1;$counter++) {
                fwrite($fp,$strings[$counter]."\n");
            }
            fwrite($fp,$strings[count($strings)-1]);
            fclose($fp);
        }
        if ($this->handlerfile!="" && $this->handlertext!=$this->loaded_handlertext) {
            $fp = fopen($this->handlerfile,"w");
            $strings = explode("\n",$this->handlertext);
            for ($counter=0;$counter<count($strings)-1;$counter++) {
                fwrite($fp,$strings[$counter]."\n");
            }
            fwrite($fp,$strings[count($strings)-1]);
            fclose($fp);
        }
        if ($this->classfile!="" && $this->classtext!=$this->loaded_classtext) {
            $fp = fopen($this->classfile,"w");
            $strings = explode("\n",$this->classtext);
            for ($counter=0;$counter<count($strings)-1;$counter++) {
                fwrite($fp,$strings[$counter]."\n");
            }
            fwrite($fp,$strings[count($strings)-1]);
            fclose($fp);
        }

        $this->loaded = true;
        $Objects->remove("ItemTemplate");
        $Objects->set($this->id,$this);
        if (isset($_POST["ajax"]))
        {
            echo $this->base_id;
        }
    }

    function remove() {
        global $Objects;
        if ($this->module_id!="")
			$collection = $Objects->get("ItemTemplates_".$this->module_id);
		else
			$collection = $Objects->get("ItemTemplates");
        $collection->connect();
        $record = new ItemTemplateRecord();
        $record = $record->getTable()->find($this->base_id);
        if ($this->module_id!="") {
			$Objects->remove("ItemTemplate_".$this->module_id."_".$record->id."_".$record->parent_id);
			$Objects->remove("ItemTemplate_".$this->module_id."_".$record->id);
		}
		else {
			$Objects->remove("ItemTemplate_".$record->id."_".$record->parent_id);
			$Objects->remove("ItemTemplate_".$record->id);			
		}
        $record->delete();
    }


    function getId() {
		if ($this->module_id!="")
			return "ItemTemplate_".$this->module_id."_".$this->fields["id"]."_".@$this->fields["parent_id"];
		else
			return "ItemTemplate_".$this->fields["id"]."_".@$this->fields["parent_id"];
    }

    function getPresentation() {
        if (!$this->loaded)
            $this->load();
        return $this->title;
    }

    function __get($name) {
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        if ($name == "base_id") {
            return $this->fields["id"];
        }
        if ($name == "tabs_string") {
            $result = "file|Файлы|".$app->skinPath."images/spacer.gif";
            if ($this->templatefile!="")
                $result .=";markup|Разметка|".$app->skinPath."images/spacer.gif";
            if ($this->cssfile!="")
                $result .=";style|Оформление|".$app->skinPath."images/spacer.gif";
            if ($this->handlerfile!="")
                $result .=";handler|Обработчик|".$app->skinPath."images/spacer.gif";
            if ($this->classfile!="")
                $result .=";class|Класс|".$app->skinPath."images/spacer.gif";
            return $result;
        }
        return parent::__get($name);
    }

    function getChildren() {
        global $Objects;
        if ($this->module_id!="")
			$collection = $Objects->get("ItemTemplates_".$this->module_id);
		else
			$collection = $Objects->get("ItemTemplates");
        return $collection->load($this->base_id);
    }

    function getArgs() {
        $result = parent::getArgs();
        global $Objects;
        if ($this->module_id!="")
			$item = $Objects->get("ItemTemplate_".$this->module_id."_".$this->parent_id);
		else
			$item = $Objects->get("ItemTemplate_".$this->parent_id);
        $item->load();
        if ($item->loaded) {
            $result["{parent_title}"] = $item->title;
            $result["{parent_parent_id}"] = $item->parent_id;
        }
        else {
            $result["{parent_title}"] = "";
            $result["{parent_parent_id}"] = 0;
        }
        $result['{tabs_string}'] = $this->tabs_string;

        $result["[templatetext]"] = $this->templatetext;
        $result["[csstext]"] = $this->csstext;
        $result["[handlertext]"] = $this->handlertext;
        $result["[classtext]"] = $this->classtext;
        return $result;
    }
}
?>
