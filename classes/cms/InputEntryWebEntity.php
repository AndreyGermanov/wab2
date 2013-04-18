<?php

/**
 * Класс представляет сущность, в которую записываются результаты ввода данных
 * пользователя в форму ввода. Особенностью этого класса является модифицированный
 * механизм проверки введенных данных. Все ошибки, которые выявляются при проверке
 * не выводятся с помощью метода reportError(), а сохраняются в переменную $this->errors,
 * которая затем может использоваться в шаблоне для их отображения. Поэтому метод
 * checkData() совершенно другой в данном классе.
 * 
 * Также, учтена проверка на captcha, если она есть в форме.
 *
 * @author andrey
 */
class InputEntryWebEntity extends WebEntity {
    
    function construct($params) {
        parent::construct($params);
        $this->tabs_string= "fieldValues|Дополнительно|".$this->skinPath."images/spacer.gif";
        $this->active_tab = "fieldValues";
        $this->clientClass = "InputEntryWebEntity";
        $this->parentClientClasses = "WebEntity~Entity";        
    }
    
    function checkData() {        
        $persisted = $this->getPersistedArray();        
        $this->createdTime = time();        
        foreach ($this->fields as $key=>$value) {
            if (isset($persisted[$key])) {
                $parts = explode("|",$persisted[$key]);
                $type = $parts[0];
                $attrs = $this->getClientInputControlAttrsArray($parts[1]);                
                if (isset($attrs["must_set"]) and $attrs["must_set"]=="true" and $value=="") {
                    $this->reportError("Поле '".@$attrs['title']."' не заполнено.");       
                    return false;
                }
                if ($type=="integer" and $value!="" and !is_numeric($value)) {
                    $this->reportError("Поле '".@$attrs['title']."' заполнено не верно.");                                     
                    return false;
                }
                if ($type=="string" and $value!="" and !is_string($value)) {
                    $this->reportError("Поле '".@$attrs['title']."' заполнено не верно.");                                     
                    return false;
                }        
                if (!is_object($value))
                    $value = strip_tags(htmlentities($value,ENT_QUOTES,"UTF-8"));                    
            }
        }
        return true;
    }        
}
?>