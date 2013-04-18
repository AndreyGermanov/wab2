<?php
/*
 * Класс реализует меню, элементы которого представляют из себя сущности
 * определенного класса.
 * 
 * Список сущностей определяется запросом, в который передается класс
 * сущностей в свойстве class и условие запроса в свойстве condition.
 * 
 * Для выполнения запроса используется адаптер данных, переданный в свойстве
 * adapter.
 * 
 * Отбирается два поля: текст и изображение. Для этого должны быть определены
 * имена этих полей в свойствах textField и imageField.
 * 
 * Сам запрос выполняется методом load(), который получает элементы меню
 * в виде строки и записывает их в поле data в таком же формате, как и в классе
 * Menu. В качестве id записывается идентификатор сущности, поля textField и
 * imageField записываются соответственно в поля text и image. Поля properties и
 * image_properties записываются соответственно из полей itemProperties и
 * imageProperties.
 * 
 */
class EntityMenu extends Menu {
   
    function construct($params) {        
        parent::construct($params);
        $this->condition = "";
        $this->className = "*Entity*";
        $this->textField = "title";
        $this->imageField = "menuIcon";
        $this->itemProperties = "";
        $this->imageProperties = "";
        $this->adapter = "";        
        $this->data = "";
        $this->sort = "";
        $this->handler = "scripts/handlers/core/EntityMenu.js";
        $this->loaded = false;
        $this->clientClass = "EntityMenu";
        $this->parentClientClasses = "Menu~Entity";        
    }
    
    function load() {
        global $Objects;
        $result = $Objects->simpleQuery($this->class,$this->textField.",".$this->imageField,$this->condition,$this->sort,$this->adapter);
        $arr = array();
        if (count($result)>0) {
            foreach($result as $val) {
                $arr[] = $val->getId()."_menuItem~".$val->fields[$this->textField]."~".$val->fields[$this->imageField]."~".$this->itemProperties."~".$this->imageProperties;
            }
        }
        $this->data = implode("|",$arr);
        $this->loaded = true;
    }
    
    function getHookProc($number) {
    	switch($number) {
    		case '3': return "getDataHook";
    	}
    	return parent::getHookProc($number);
    }
    
    function getDataHook($arguments) {
    	$this->load();
    	echo $this->data;
    }
}
?>