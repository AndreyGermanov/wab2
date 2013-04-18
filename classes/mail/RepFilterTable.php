<?php
/**
 * Класс определяет таблицу с данными, таблицу, в которой в качестве значений
 * ячеек выступают элементы InputControl
 *
 * @author andrey
 */
class RepFilterTable extends DataTable{

    public $rules_array = array();

    function construct($params) {
        parent::construct($params);
        global $Objects;
        $mailapp = $Objects->get("MailApplication_".$this->module_id);
        $this->repFilterTable = $mailapp->repFilterTable;
        $this->loaded = false;
        $this->clientClass = "RepFilterTable";
        $this->parentClientClasses = "DataTable~Entity";        
    }

    function load() {
        global $Objects;
        $mailapp = $Objects->get("MailApplication_".$this->module_id);
        $this->rules_array = array();
        if (file_exists($mailapp->remotePath.$this->repFilterTable)) {
            $strings = file($mailapp->remotePath.$this->repFilterTable);
            foreach($strings as $line) {
                $parts = explode(" ",$line);
                if (trim($parts[0])=="")
                    continue;
                $this->rules_array[count($this->rules_array)] = trim($parts[0])." ".trim(@$parts[1]);
            }
        }
        $this->loaded = true;
    }

    function getArgs() {
        $result = parent::getArgs();
        if (!$this->loaded)
            $this->load();
        $id = $this->getId();
        $str = "";
        $str .= $id."tbl.columns = new Array;\n";
        $str .= $id."tbl.columns[0] = new Array;\n";
        $str .= $id."tbl.columns[0]['name'] = 'ip';\n";
        $str .= $id."tbl.columns[0]['title'] = 'IP-адрес';\n";
        $str .= $id."tbl.columns[0]['class'] = 'cell';\n";
        $str .= $id."tbl.columns[0]['properties'] = 'width=50%';\n";
        $str .= $id."tbl.columns[0]['control'] = 'string,From:~To:|Отправитель~Получатель,null';\n";
        $str .= $id."tbl.columns[0]['control_properties'] = 'deactivated=true,input_class=input,deactivate_class=deactivated,regs=^[0-9\.]*$';\n";
        $str .= $id."tbl.columns[0]['must_set'] = true;\n";
        $str .= $id."tbl.columns[0]['unique'] = false;\n";
        $str .= $id."tbl.columns[0]['readonly'] = false;\n";

        $str .= $id."tbl.columns[1] = new Array;\n";
        $str .= $id."tbl.columns[1]['name'] = 'entry'\n";
        $str .= $id."tbl.columns[1]['title'] = 'Репутация';\n";
        $str .= $id."tbl.columns[1]['class'] = 'cell';\n";
        $str .= $id."tbl.columns[1]['properties'] = 'width=50%';\n";
        $str .= $id."tbl.columns[1]['control'] = 'list,OK~REJECT~HOLD|Хорошая~Плохая~Нейтральная';\n";
        $str .= $id."tbl.columns[1]['control_properties'] = 'deactivated=true,input_class=input,deactivate_class=deactivated';\n";
        $str .= $id."tbl.columns[1]['must_set'] = false;\n";
        $str .= $id."tbl.columns[1]['unique'] = true;\n";
        $str .= $id."tbl.columns[1]['readonly'] = false;\n";

        $str .= $id."tbl.rows = new Array;\n";
        $str .= $id."tbl.rows[0] = new Array;\n";
        $str .= $id."tbl.rows[0]['class'] = '';\n";
        $str .= $id."tbl.rows[0]['properties'] = '';\n";
        $str .= $id."tbl.rows[0]['cells'] = new Array;\n";
        $str .= $id."tbl.rows[0]['cells'][0] = new Array;\n";
        $str .= $id."tbl.rows[0]['cells'][0]['properties'] = '';\n";
        $str .= $id."tbl.rows[0]['cells'][0]['class'] = 'header';\n";
        $str .= $id."tbl.rows[0]['cells'][0]['value'] = 'IP-адрес';\n";
        $str .= $id."tbl.rows[0]['cells'][0]['control'] = 'plaintext';\n";
        $str .= $id."tbl.rows[0]['cells'][0]['control_properties'] = '';\n";
        $str .= $id."tbl.rows[0]['cells'][1] = new Array;\n";
        $str .= $id."tbl.rows[0]['cells'][1]['properties'] = '';\n";
        $str .= $id."tbl.rows[0]['cells'][1]['class'] = 'header';\n";
        $str .= $id."tbl.rows[0]['cells'][1]['value'] = 'Репутация';\n";
        $str .= $id."tbl.rows[0]['cells'][1]['control'] = 'plaintext';\n";
        $str .= $id."tbl.rows[0]['cells'][1]['control_properties'] = '';\n";


        $str .= $id."tbl.emptyrow = new Array;\n";
        $str .= $id."tbl.emptyrow['class'] = '';\n";
        $str .= $id."tbl.emptyrow['properties'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'] = new Array;\n";
        $str .= $id."tbl.emptyrow['cells'][0] = new Array;\n";
        $str .= $id."tbl.emptyrow['cells'][0]['properties'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][0]['control'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][0]['control_properties'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][0]['value'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][1] = new Array;\n";
        $str .= $id."tbl.emptyrow['cells'][1]['properties'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][1]['control'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][1]['control_properties'] = '';\n";

        $i = 1;
        foreach($this->rules_array as $value1) {
            $parts = explode(" ",$value1);
            $key = $parts[0];
            $value = $parts[1];
            $str .= $id."tbl.rows[".$i."] = new Array;\n";
            $str .= $id."tbl.rows[".$i."]['class'] = '';\n";
            $str .= $id."tbl.rows[".$i."]['properties'] = '';\n";
            $str .= $id."tbl.rows[".$i."]['cells'] = new Array;\n";
            $str .= $id."tbl.rows[".$i."]['cells'][0] = new Array;\n";
            $str .= $id."tbl.rows[".$i."]['cells'][0]['properties'] = '';\n";
            $str .= $id."tbl.rows[".$i."]['cells'][0]['class'] = 'cell';\n";
            $str .= $id."tbl.rows[".$i."]['cells'][0]['value'] = '".$key."';\n";
//            $str .= "tbl.rows[".$i."]['cells'][0]['control'] = 'list,From:~To:|Отправитель~Получатель';\n";
            $str .= $id."tbl.rows[".$i."]['cells'][0]['control_properties'] = '';\n";
            $str .= $id."tbl.rows[".$i."]['cells'][1] = new Array;\n";
            $str .= $id."tbl.rows[".$i."]['cells'][1]['properties'] = '';\n";
            $str .= $id."tbl.rows[".$i."]['cells'][1]['class'] = 'cell';\n";
            $str .= $id."tbl.rows[".$i."]['cells'][1]['value'] = '".$value."';\n";
//            $str .= "tbl.rows[".$i."]['cells'][1]['control'] = 'string';\n";
            $str .= $id."tbl.rows[".$i."]['cells'][1]['control_properties'] = '';\n";
            $i++;
        }
        $result["{data}"] = $str;
        return $result;
    }

    function getId() {
        return get_class($this)."_".$this->module_id."_".$this->name;
    }
}
?>