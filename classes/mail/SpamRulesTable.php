<?php
/**
 * Класс определяет таблицу с данными, таблицу, в которой в качестве значений
 * ячеек выступают элементы InputControl
 *
 * @author andrey
 */
class SpamRulesTable extends DataTable{

    public $rules_array = array();

    function construct($params) {
        parent::construct($params);
        global $Objects;
        $mailapp = $Objects->get("MailApplication_".$this->module_id);
        $this->spamlistFile = $mailapp->spamWhitelistFile;
        $this->spamGetRulesCommand = str_replace("{file}",$this->spamlistFile,$mailapp->spamGetRulesCommand);
        $this->rulesFile = "";
        $this->valueType = "";
        $this->loaded = false;
        $this->clientClass = "SpamRulesTable";
        $this->parentClientClasses = "DataTable~Entity";        
    }

    function load() {
        global $Objects;
        $shell = $Objects->get("Shell_shell");
        $mailapp = $Objects->get("MailApplication_".$this->module_id);
        if (file_exists($mailapp->remotePath.$this->rulesFile)) {
            $this->spamGetRulesCommand = str_replace("{file}",$this->rulesFile,$mailapp->spamGetRulesCommand);
            $strings = explode("\n",$shell->exec_command($mailapp->remoteSSHCommand." ".$this->spamGetRulesCommand));
            $this->rules_array = array();
            foreach($strings as $line) {
                $parts = explode(" ",$line);
                if (trim(@$parts[0])=="")
                    continue;
                if (strtoupper(trim(@$parts[0]))!="FROMORTO:"  and strtoupper(trim(@$parts[1]))!="DEFAULT")
                    $this->rules_array[count($this->rules_array)] = trim(@$parts[0])." ".trim(@$parts[1])." ".trim(@$parts[2]);
            }
        }
        $this->loaded = true;        
    }

    function getArgs() {
        $this->valueType = str_replace(".",",",$this->valueType);
        $result = parent::getArgs();
        if (!$this->loaded)
            $this->load();
        $id = $this->getId();
        $str = "";
        $str .= $id."tbl.columns = new Array;\n";
        $str .= $id."tbl.columns[0] = new Array;\n";
        $str .= $id."tbl.columns[0]['name'] = 'objectType';\n";
        $str .= $id."tbl.columns[0]['title'] = 'Тип объекта';\n";
        $str .= $id."tbl.columns[0]['class'] = 'cell';\n";
        $str .= $id."tbl.columns[0]['properties'] = 'width=30%';\n";
        $str .= $id."tbl.columns[0]['control'] = 'list,From:~To:|Отправитель~Получатель,null';\n";
        $str .= $id."tbl.columns[0]['control_properties'] = 'deactivated=true,input_class=input,deactivate_class=deactivated';\n";
        $str .= $id."tbl.columns[0]['must_set'] = true;\n";
        $str .= $id."tbl.columns[0]['unique'] = false;\n";
        $str .= $id."tbl.columns[0]['readonly'] = false;\n";

        $str .= $id."tbl.columns[1] = new Array;\n";
        $str .= $id."tbl.columns[1]['name'] = 'object'\n";
        $str .= $id."tbl.columns[1]['title'] = 'Объект';\n";
        $str .= $id."tbl.columns[1]['class'] = 'cell';\n";
        $str .= $id."tbl.columns[1]['control'] = 'string';\n";
        $str .= $id."tbl.columns[1]['control_properties'] = 'deactivated=true,input_class=input,deactivate_class=deactivated';\n";
        $str .= $id."tbl.columns[1]['must_set'] = true;\n";
        $str .= $id."tbl.columns[1]['unique'] = true;\n";
        $str .= $id."tbl.columns[1]['readonly'] = false;\n";

        $str .= $id."tbl.columns[2] = new Array;\n";
        $str .= $id."tbl.columns[2]['name'] = 'entry'\n";
        $str .= $id."tbl.columns[2]['title'] = 'Значение';\n";
        $str .= $id."tbl.columns[2]['class'] = 'cell';\n";
        $str .= $id."tbl.columns[2]['control'] = '".$this->valueType."';\n";
        $str .= $id."tbl.columns[2]['control_properties'] = 'deactivated=true,input_class=input,deactivate_class=deactivated';\n";
        $str .= $id."tbl.columns[2]['must_set'] = true;\n";
        $str .= $id."tbl.columns[2]['unique'] = false;\n";
        $str .= $id."tbl.columns[2]['readonly'] = false;\n";

        $str .= $id."tbl.rows = new Array;\n";
        $str .= $id."tbl.rows[0] = new Array;\n";
        $str .= $id."tbl.rows[0]['class'] = '';\n";
        $str .= $id."tbl.rows[0]['properties'] = '';\n";
        $str .= $id."tbl.rows[0]['cells'] = new Array;\n";
        $str .= $id."tbl.rows[0]['cells'][0] = new Array;\n";
        $str .= $id."tbl.rows[0]['cells'][0]['properties'] = '';\n";
        $str .= $id."tbl.rows[0]['cells'][0]['class'] = 'header';\n";
        $str .= $id."tbl.rows[0]['cells'][0]['value'] = 'Тип объекта';\n";
        $str .= $id."tbl.rows[0]['cells'][0]['control'] = 'plaintext';\n";
        $str .= $id."tbl.rows[0]['cells'][0]['control_properties'] = '';\n";

        $str .= $id."tbl.rows[0]['cells'][1] = new Array;\n";
        $str .= $id."tbl.rows[0]['cells'][1]['properties'] = '';\n";
        $str .= $id."tbl.rows[0]['cells'][1]['class'] = 'header';\n";
        $str .= $id."tbl.rows[0]['cells'][1]['value'] = 'Объект';\n";
        $str .= $id."tbl.rows[0]['cells'][1]['control'] = 'plaintext';\n";
        $str .= $id."tbl.rows[0]['cells'][1]['control_properties'] = '';\n";

        $str .= $id."tbl.rows[0]['cells'][2] = new Array;\n";
        $str .= $id."tbl.rows[0]['cells'][2]['properties'] = '';\n";
        $str .= $id."tbl.rows[0]['cells'][2]['class'] = 'header';\n";
        $str .= $id."tbl.rows[0]['cells'][2]['value'] = 'Значение';\n";
        $str .= $id."tbl.rows[0]['cells'][2]['control'] = 'plaintext';\n";
        $str .= $id."tbl.rows[0]['cells'][2]['control_properties'] = '';\n";


        $str .= $id."tbl.emptyrow = new Array;\n";
        $str .= $id."tbl.emptyrow['class'] = '';\n";
        $str .= $id."tbl.emptyrow['properties'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'] = new Array;\n";
        $str .= $id."tbl.emptyrow['cells'][0] = new Array;\n";
        $str .= $id."tbl.emptyrow['cells'][0]['properties'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][0]['control'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][0]['control_properties'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][0]['value'] = 'From';\n";
        $str .= $id."tbl.emptyrow['cells'][1] = new Array;\n";
        $str .= $id."tbl.emptyrow['cells'][1]['properties'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][1]['control_properties'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][2] = new Array;\n";
        $str .= $id."tbl.emptyrow['cells'][2]['properties'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][2]['control_properties'] = '';\n";

        $i = 1;
        foreach($this->rules_array as $value1) {
            $parts = explode(" ",$value1);
            $str .= $id."tbl.rows[".$i."] = new Array;\n";
            $str .= $id."tbl.rows[".$i."]['class'] = '';\n";
            $str .= $id."tbl.rows[".$i."]['properties'] = '';\n";
            $str .= $id."tbl.rows[".$i."]['cells'] = new Array;\n";
            $str .= $id."tbl.rows[".$i."]['cells'][0] = new Array;\n";
            $str .= $id."tbl.rows[".$i."]['cells'][0]['properties'] = '';\n";
            $str .= $id."tbl.rows[".$i."]['cells'][0]['class'] = 'cell';\n";
            $str .= $id."tbl.rows[".$i."]['cells'][0]['value'] = '".$parts[0]."';\n";
            $str .= $id."tbl.rows[".$i."]['cells'][0]['control'] = 'list,From:~To:|Отправитель~Получатель';\n";
            $str .= $id."tbl.rows[".$i."]['cells'][0]['control_properties'] = '';\n";
            $str .= $id."tbl.rows[".$i."]['cells'][1] = new Array;\n";
            $str .= $id."tbl.rows[".$i."]['cells'][1]['properties'] = '';\n";
            $str .= $id."tbl.rows[".$i."]['cells'][1]['class'] = 'cell';\n";
            $str .= $id."tbl.rows[".$i."]['cells'][1]['value'] = '".$parts[1]."';\n";
            $str .= $id."tbl.rows[".$i."]['cells'][1]['control'] = 'string';\n";
            $str .= $id."tbl.rows[".$i."]['cells'][1]['control_properties'] = '';\n";
            $str .= $id."tbl.rows[".$i."]['cells'][2] = new Array;\n";
            $str .= $id."tbl.rows[".$i."]['cells'][2]['properties'] = '';\n";
            $str .= $id."tbl.rows[".$i."]['cells'][2]['class'] = 'cell';\n";
            $str .= $id."tbl.rows[".$i."]['cells'][2]['value'] = '".$parts[2]."';\n";
            $str .= $id."tbl.rows[".$i."]['cells'][2]['control'] = '".$this->valueType."';\n";
            $str .= $id."tbl.rows[".$i."]['cells'][2]['control_properties'] = '';\n";
            $i++;
        }
        $result["{data}"] = $str;
        return $result;
    }
}
?>