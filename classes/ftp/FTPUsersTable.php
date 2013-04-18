<?php
/*
 * Класс, реализующий таблицу в окне свойств FTP-сервера на закладке "Права доступа".
 *
 * Он представляет из себя таблицу со следующими колонками:
 *
 * "Разрешен","Имя пользователя","Скорость передачи","Скорость приема".
 *
 * Таблица формируется методом getArgs() с помощью переменной usersList, которая содержит
 * данные таблицы в виде строки в формате:
 *
 * <разрешен>~<имя-пользователя>~<скорость-передачи>~<скорость приема>.
 *
 * В таком же виде данные возвращаются клиенту с помощью метода getSingleValue().
 *
 * (C) 2012 ООО "ЛВА". Все права защищены
 *
 * @andrey 11.07.2012 09:45:00
 *
 */

class FTPUsersTable extends DataTable {
    
    function construct($params) {
        parent::construct($params);
        global $Objects;
        $this->usersList = "";
        $this->clientClass = "FTPUsersTable";
        $this->parentClientClasses = "DataTable~Entity";        
    }

    function getArgs() {
        $result = parent::getArgs();
        global $Objects;
		$fileServer = $Objects->get("FileServer_".$this->module_id."_Shares");
		$fileServer->loadUsers(false);
		$arr = array();
		foreach($fileServer->users as $user) {
			$arr[] = $user->name;
		}
		$usersList = " ~".implode("~",$arr)."| ~".implode("~",$arr);
		$id = $this->getId();
        $str = "";
        $str .= $id."tbl.columns = new Array;\n";
        $str .= $id."tbl.columns[0] = new Array;\n";
        $str .= $id."tbl.columns[0]['name'] = 'enabled';\n";
        $str .= $id."tbl.columns[0]['title'] = 'Разрешено';\n";
        $str .= $id."tbl.columns[0]['class'] = 'cell';\n";
        $str .= $id."tbl.columns[0]['properties'] = 'width=100%';\n";
        $str .= $id."tbl.columns[0]['control'] = 'boolean';\n";
        $str .= $id."tbl.columns[0]['control_properties'] = 'input_class=input1,control_type=checkbox';\n";
        $str .= $id."tbl.columns[0]['must_set'] = false;\n";
        $str .= $id."tbl.columns[0]['unique'] = false;\n";
        $str .= $id."tbl.columns[0]['readonly'] = false;\n";

        $str .= $id."tbl.columns[1] = new Array;\n";
        $str .= $id."tbl.columns[1]['name'] = 'user';\n";
        $str .= $id."tbl.columns[1]['title'] = 'Пользователь';\n";
        $str .= $id."tbl.columns[1]['class'] = 'cell';\n";
        $str .= $id."tbl.columns[1]['properties'] = 'width=100%';\n";
        $str .= $id."tbl.columns[1]['control'] = 'list,".$usersList."';\n";
        $str .= $id."tbl.columns[1]['control_properties'] = 'deactivated=true,input_class=input1';\n";
        $str .= $id."tbl.columns[1]['must_set'] = true;\n";
        $str .= $id."tbl.columns[1]['unique'] = false;\n";
        $str .= $id."tbl.columns[1]['readonly'] = false;\n";

        $str .= $id."tbl.columns[2] = new Array;\n";
        $str .= $id."tbl.columns[2]['name'] = 'uploadRate';\n";
        $str .= $id."tbl.columns[2]['title'] = 'Скорость передачи (Кбит/с)';\n";
        $str .= $id."tbl.columns[2]['class'] = 'cell';\n";
        $str .= $id."tbl.columns[2]['properties'] = 'width=100%';\n";
        $str .= $id."tbl.columns[2]['control'] = 'integer';\n";
        $str .= $id."tbl.columns[2]['control_properties'] = 'deactivated=true,input_class=input1';\n";
        $str .= $id."tbl.columns[2]['must_set'] = false;\n";
        $str .= $id."tbl.columns[2]['unique'] = false;\n";
        $str .= $id."tbl.columns[2]['readonly'] = false;\n";
        
        $str .= $id."tbl.columns[3] = new Array;\n";
        $str .= $id."tbl.columns[3]['name'] = 'downloadRate';\n";
        $str .= $id."tbl.columns[3]['title'] = 'Скорость приема (Кбит/с)';\n";
        $str .= $id."tbl.columns[3]['class'] = 'cell';\n";
        $str .= $id."tbl.columns[3]['properties'] = 'width=100%';\n";
        $str .= $id."tbl.columns[3]['control'] = 'integer';\n";
        $str .= $id."tbl.columns[3]['control_properties'] = 'deactivated=true,input_class=input1';\n";
        $str .= $id."tbl.columns[3]['must_set'] = false;\n";
        $str .= $id."tbl.columns[3]['unique'] = false;\n";
        $str .= $id."tbl.columns[3]['readonly'] = false;\n";
        
        $str .= $id."tbl.rows = new Array;\n";
        $str .= $id."tbl.rows[0] = new Array;\n";
        $str .= $id."tbl.rows[0]['class'] = '';\n";
        $str .= $id."tbl.rows[0]['properties'] = '';\n";
        $str .= $id."tbl.rows[0]['cells'] = new Array;\n";
        $str .= $id."tbl.rows[0]['cells'][0] = new Array;\n";
        $str .= $id."tbl.rows[0]['cells'][0]['properties'] = '';\n";
        $str .= $id."tbl.rows[0]['cells'][0]['value'] = 'Разрешен';\n";
        $str .= $id."tbl.rows[0]['cells'][0]['control'] = 'header';\n";
        $str .= $id."tbl.rows[0]['cells'][0]['class'] = 'header';\n";
        $str .= $id."tbl.rows[0]['cells'][0]['control_properties'] = 'input_class=input1,deactivated=false';\n";

        $str .= $id."tbl.rows[0]['cells'][1] = new Array;\n";
        $str .= $id."tbl.rows[0]['cells'][1]['properties'] = '';\n";
        $str .= $id."tbl.rows[0]['cells'][1]['value'] = 'Пользователь';\n";
        $str .= $id."tbl.rows[0]['cells'][1]['control'] = 'header';\n";
        $str .= $id."tbl.rows[0]['cells'][1]['class'] = 'header';\n";
        $str .= $id."tbl.rows[0]['cells'][1]['control_properties'] = 'input_class=input1,deactivated=false';\n";

        $str .= $id."tbl.rows[0]['cells'][2] = new Array;\n";
        $str .= $id."tbl.rows[0]['cells'][2]['properties'] = '';\n";
        $str .= $id."tbl.rows[0]['cells'][2]['value'] = 'Скорость передачи (Кбит/c)';\n";
        $str .= $id."tbl.rows[0]['cells'][2]['control'] = 'header';\n";
        $str .= $id."tbl.rows[0]['cells'][2]['class'] = 'header';\n";
        $str .= $id."tbl.rows[0]['cells'][2]['control_properties'] = 'input_class=input1,deactivated=false';\n";

        $str .= $id."tbl.rows[0]['cells'][3] = new Array;\n";
        $str .= $id."tbl.rows[0]['cells'][3]['properties'] = '';\n";
        $str .= $id."tbl.rows[0]['cells'][3]['value'] = 'Скорость приема (Кбит/с)';\n";
        $str .= $id."tbl.rows[0]['cells'][3]['control'] = 'header';\n";
        $str .= $id."tbl.rows[0]['cells'][3]['class'] = 'header';\n";
        $str .= $id."tbl.rows[0]['cells'][3]['control_properties'] = 'input_class=input1,deactivated=false';\n";        

        $str .= $id."tbl.emptyrow = new Array;\n";
        $str .= $id."tbl.emptyrow['class'] = '';\n";
        $str .= $id."tbl.emptyrow['properties'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'] = new Array;\n";
        $str .= $id."tbl.emptyrow['cells'][0] = new Array;\n";
        $str .= $id."tbl.emptyrow['cells'][0]['properties'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][0]['control'] = 'boolean';\n";
        $str .= $id."tbl.emptyrow['cells'][0]['value'] = '0';\n";
        $str .= $id."tbl.emptyrow['cells'][1] = new Array;\n";
        $str .= $id."tbl.emptyrow['cells'][1]['properties'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][1]['value'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][2] = new Array;\n";
        $str .= $id."tbl.emptyrow['cells'][2]['properties'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][2]['control'] = 'integer';\n";
        $str .= $id."tbl.emptyrow['cells'][2]['value'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][3] = new Array;\n";
        $str .= $id."tbl.emptyrow['cells'][3]['properties'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][3]['control'] = 'integer';\n";
        $str .= $id."tbl.emptyrow['cells'][3]['value'] = '';\n";

        if ($this->usersList != "") {
            $res = explode("|",$this->usersList);
            $c=1;
            $arr = array();
            $arr2 = array();
            foreach ($res as $item) {
				$value = explode("~",$item);
                $str .= $id."tbl.rows[$c] = new Array;\n";
                $str .= $id."tbl.rows[$c]['class'] = '';\n";
                $str .= $id."tbl.rows[$c]['properties'] = '';\n";
                $str .= $id."tbl.rows[$c]['cells'] = new Array;\n";
                $str .= $id."tbl.rows[$c]['cells'][0] = new Array;\n";
                $str .= $id."tbl.rows[$c]['cells'][0]['control'] = 'boolean';\n";
                $str .= $id."tbl.rows[$c]['cells'][0]['class'] = 'cell';\n";
                $str .= $id."tbl.rows[$c]['cells'][0]['value'] = '".@$value[0]."';\n";

                $str .= $id."tbl.rows[$c]['cells'][1] = new Array;\n";
                $str .= $id."tbl.rows[$c]['cells'][1]['properties'] = '';\n";
                $str .= $id."tbl.rows[$c]['cells'][1]['class'] = 'cell';\n";
                $str .= $id."tbl.rows[$c]['cells'][1]['value'] = '".@$value[1]."';\n";
                    
                $str .= $id."tbl.rows[$c]['cells'][2] = new Array;\n";
                $str .= $id."tbl.rows[$c]['cells'][2]['properties'] = '';\n";
                $str .= $id."tbl.rows[$c]['cells'][2]['control'] = 'integer';\n";
                $str .= $id."tbl.rows[$c]['cells'][2]['class'] = 'cell';\n";
                $str .= $id."tbl.rows[$c]['cells'][2]['value'] = '".@$value[2]."';\n";
                    
                $str .= $id."tbl.rows[$c]['cells'][3] = new Array;\n";
                $str .= $id."tbl.rows[$c]['cells'][3]['properties'] = '';\n";
                $str .= $id."tbl.rows[$c]['cells'][3]['control'] = 'integer';\n";
                $str .= $id."tbl.rows[$c]['cells'][3]['class'] = 'cell';\n";
                $str .= $id."tbl.rows[$c]['cells'][3]['value'] = '".@$value[3]."';\n";
				$c++;
            }
        }
        $result["{data}"] = $str;
        return $result;
    }   
}
?>