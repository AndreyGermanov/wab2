<?php
/**
 * Класс определяет таблицу с данными, таблицу, в которой в качестве значений
 * ячеек выступают элементы InputControl
 *
 * @author andrey
 */
class SpamBlacklistTable extends SpamWhitelistTable{

    public $rules_array = array();

    function construct($params) {
        parent::construct($params);
        global $Objects;
        $mailapp = $Objects->get("MailApplication_".$this->module_id);
        $this->spamlistFile = $mailapp->spamBlacklistFile;
        $this->spamGetRulesCommand = str_replace("{file}",$this->spamlistFile,$mailapp->spamGetRulesCommand);
        $this->loaded = false;
        $this->clientClass = "SpamBlacklistTable";
        $this->parentClientClasses = "SpamWhitelistTable~DataTable~Entity";        
    }
}
?>