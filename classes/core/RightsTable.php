<?php
/*
 * Класс, реализующий таблицу прав доступа пользователей или ролей к сущности
 * 
 * Сущность определяется идентификатором entityObject.
 * 
 * Тип таблицы определяется параметром rightsType, который может быть равен либо users,
 * либо roles.
 * 
 *  Отображает таблицу в два столбца: в первом столбце наименование пользователя или роли,
 *  а во втором уровень доступа (1 - чтение, 2 - запись).
 *  
 *  В первом столбце в качестве значений могут быть #all_users - все пользователи и #auth_users,
 *  если тип таблицы "users". Также может быть @all_roles если тип таблицы - "roles".
 *  
 *  В таблицу типа "roles" попадают все пользователи, имя которых начинается с "@", то есть роли.
 *  В таблицу типа "users" соответственно попадают все пользователи, имя которых не начинается с "@". 
 */

class RightsTable extends DataTable { 
	   
    function construct($params) {
        parent::construct($params);
        global $Objects,$tags;
        $this->entityObject = "";
        $this->rightsType = "users";
        $this->handler = "scripts/handlers/core/RightsTable.js";
        $this->res = "";
        $this->clientClass = "RightsTable";
        $this->parentClientClasses = "DataTable~Entity";        
    }

    function getArgs() {
        $result = parent::getArgs();
        global $Objects,$roles;
        if ($this->entityObject!="")
        	$this->entityObject = $Objects->get($this->entityObject);
        if (is_object($this->entityObject) and method_exists($this->entityObject,"getId")) {
        	$this->entityObject->loaded=false;
        	$this->entityObject->load(null,null,true);
        } 
        $tbl = array();
		if ($this->rightsType=="users") {
        	$users_list = array("#all_users","#auth_users");
        	$users_titles = array("Все пользователи","Авторизованные пользователи");
			$users = $Objects->get("ApacheUsers_users");
			$users->load();
			foreach($users->apacheUsers as $value) {
				$users_list[] = $value->name;
				$users_titles[] = $value->name;
			}
			$names_string = " ~".implode("~",$users_list);
			$titles_string = " ~".implode("~",$users_titles);
			foreach ($this->entityObject->rights as $key=>$value) {
				if ($key[0]!="@") {
					$tbl[$key] = $value;
				}
			}
		} else {
			$users_list = array("@all_roles");
			$users_titles = array("Все роли");				
			foreach($roles as $value) {
				$users_list[] = "@".$value["name"];
				$users_titles[] = $value["title"];
			}
			$names_string = " ~".implode("~",$users_list);
			$titles_string = " ~".implode("~",$users_titles);
			foreach ($this->entityObject->rights as $key=>$value) {
				if ($key[0]=="@") {
					$tbl[$key] = $value;
				}
			}
		}
		
        $id = $this->getId();
        $str = "";//$id."tbl=new Array;";
        $str .= $id."tbl.columns = new Array;\n";
        
        $str .= $id."tbl.columns[0] = new Array;\n";
        $str .= $id."tbl.columns[0]['name'] = 'name';\n";
        if ($this->rightsType=="users")
        	$str .= $id."tbl.columns[0]['title'] = 'Пользователь';\n";
        else
        	$str .= $id."tbl.columns[0]['title'] = 'Роль';\n";
        $str .= $id."tbl.columns[0]['class'] = 'cell';\n";
        $str .= $id."tbl.columns[0]['properties'] = '';\n";
        $str .= $id."tbl.columns[0]['control'] = 'list,".$names_string."|".$titles_string."';\n";
        $str .= $id."tbl.columns[0]['control_properties'] = 'deactivated=true,input_class=input1';\n";
        $str .= $id."tbl.columns[0]['must_set'] = true;\n";
        $str .= $id."tbl.columns[0]['unique'] = true;\n";
        $str .= $id."tbl.columns[0]['readonly'] = false;\n";

        $str .= $id."tbl.columns[1] = new Array;\n";
        $str .= $id."tbl.columns[1]['name'] = 'value';\n";
        $str .= $id."tbl.columns[1]['title'] = 'Доступ';\n";
        $str .= $id."tbl.columns[1]['class'] = 'cell';\n";
        $str .= $id."tbl.columns[1]['properties'] = 'width=60%';\n";
        $str .= $id."tbl.columns[1]['control'] = 'list, ~1~2| ~Чтение~Запись';\n";        
        $str .= $id."tbl.columns[1]['control_properties'] = 'deactivated=true,input_class=input1';\n";
        $str .= $id."tbl.columns[1]['must_set'] = true;\n";
        $str .= $id."tbl.columns[1]['unique'] = false;\n";
        $str .= $id."tbl.columns[1]['readonly'] = false;\n";        
        
        $str .= $id."tbl.rows = new Array;\n";
        $str .= $id."tbl.rows[0] = new Array;\n";
        $str .= $id."tbl.rows[0]['class'] = '';\n";
        $str .= $id."tbl.rows[0]['properties'] = '';\n";
        $str .= $id."tbl.rows[0]['cells'] = new Array;\n";
        $str .= $id."tbl.rows[0]['cells'][0] = new Array;\n";
        $str .= $id."tbl.rows[0]['cells'][0]['properties'] = '';\n";
        $str .= $id."tbl.rows[0]['cells'][0]['class'] = 'header';\n";
        if ($this->rightsType=="users")
        	$str .= $id."tbl.rows[0]['cells'][0]['value'] = 'Пользователь';\n";
        else
        	$str .= $id."tbl.rows[0]['cells'][0]['value'] = 'Роль';\n";
        $str .= $id."tbl.rows[0]['cells'][0]['control'] = 'header';\n";
        $str .= $id."tbl.rows[0]['cells'][0]['control_properties'] = 'input_class=input1,deactivated=false';\n";

        $str .= $id."tbl.rows[0]['cells'][1] = new Array;\n";
        $str .= $id."tbl.rows[0]['cells'][1]['properties'] = '';\n";
        $str .= $id."tbl.rows[0]['cells'][1]['value'] = 'Значение';\n";
        $str .= $id."tbl.rows[0]['cells'][1]['control'] = 'header';\n";
        $str .= $id."tbl.rows[0]['cells'][1]['control_properties'] = 'input_class=input1,deactivated=false';\n";
        $str .= $id."tbl.rows[0]['cells'][1]['class'] = 'header';\n";
                

        $str .= $id."tbl.emptyrow = new Array;\n";
        $str .= $id."tbl.emptyrow['class'] = '';\n";
        $str .= $id."tbl.emptyrow['properties'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'] = new Array;\n";
        $str .= $id."tbl.emptyrow['cells'][0] = new Array;\n";
        $str .= $id."tbl.emptyrow['cells'][0]['properties'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][0]['value'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][1] = new Array;\n";
        $str .= $id."tbl.emptyrow['cells'][1]['properties'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][1]['value'] = '';\n";
        $c = 1;
        foreach ($tbl as $key=>$value) {
	        $str .= $id."tbl.rows[$c] = new Array;\n";
	        $str .= $id."tbl.rows[$c]['cells'] = new Array;\n";
	        $str .= $id."tbl.rows[$c]['cells'][0] = new Array;\n";
	        $str .= $id."tbl.rows[$c]['cells'][0]['value'] = '".$key."'\n";
	        $str .= $id."tbl.rows[$c]['cells'][1] = new Array;\n";
	        $str .= $id."tbl.rows[$c]['cells'][1]['value'] = '".$value."';\n";
	        $c++;
        }
        $result["{data}"] = $str;
        return $result;
    } 
    
    function getDataHook($arguments) {
    	$this->setArguments($arguments);
    	$result = $this->getArgs();
    	echo $result["{data}"];
    }
    
    function getHookProc($number) {
    	switch ($number) {
    		case '3': return "getDataHook";
    	}
    	return parent::getHookProc($number);    	 
    }        
}
?>