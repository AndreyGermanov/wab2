<?php
/*
 * Класс, реализующий таблицу активных пользователей FTP.
 *
 * Он представляет из себя таблицу со следующими колонками:
 *
 * "Имя пользователя", "Имя хоста", "Операция", "Продолжительность", "Подробности"
 *
 * Таблица формируется методом getArgs(), который вызывает метод FTPHost::getActiveConnections().
 * 
 * Этот метод возвращает список активных соединений в виде массива, каждый элемент которого 
 * это соединение, которое содержит поля: "user", "host", "operation", "time" и "description".
 * 
 * На основании этой таблицы заполняется список пользователей usersList и одновременно в таблицу
 * отбираются записи для текущего активного пользователя. 
 *
 * (C) 2012 ООО "ЛВА". Все права защищены
 *
 * @andrey 11.07.2012 23:49:00
 *
 */

class FTPActiveUsersTable extends DataTable {
    
    function construct($params) {
        parent::construct($params);
        global $Objects;
        $this->usersList = "";
		$this->ftpHostName = "";
		$this->currentUser = "";
		$this->handler = "scripts/handlers/ftp/FTPActiveUsersTable.js";
		$this->shell = $Objects->get("Shell_shell");
		$this->clientClass = "FTPActiveUsersTable";
		$this->parentClientClasses = "DataTable~Entity";		
    }

    function getArgs() {
        $result = parent::getArgs();
        global $Objects;
		$ftpHost = $Objects->get("FTPHost_".$this->module_id."_".$this->ftpHostName);
		$connections = $ftpHost->getActiveConnections();
		$id = $this->getId();
        $str = "";
        $str .= $id."tbl.columns = new Array;\n";
        $str .= $id."tbl.columns[0] = new Array;\n";
        $str .= $id."tbl.columns[0]['name'] = 'entityId';\n";
        $str .= $id."tbl.columns[0]['title'] = 'Entity Id';\n";
        $str .= $id."tbl.columns[0]['class'] = 'cell';\n";
        $str .= $id."tbl.columns[0]['control'] = 'hidden';\n";
        $str .= $id."tbl.columns[0]['control_properties'] = 'input_class=input1';\n";
        $str .= $id."tbl.columns[0]['must_set'] = false;\n";
        $str .= $id."tbl.columns[0]['unique'] = false;\n";
        $str .= $id."tbl.columns[0]['readonly'] = false;\n";
        
        $str .= $id."tbl.columns[1] = new Array;\n";
        $str .= $id."tbl.columns[1]['name'] = 'host';\n";
        $str .= $id."tbl.columns[1]['title'] = 'Имя хоста';\n";
        $str .= $id."tbl.columns[1]['class'] = 'cell';\n";
        $str .= $id."tbl.columns[1]['control'] = 'plaintext';\n";
        $str .= $id."tbl.columns[1]['control_properties'] = 'input_class=input1';\n";
        $str .= $id."tbl.columns[1]['must_set'] = false;\n";
        $str .= $id."tbl.columns[1]['unique'] = false;\n";
        $str .= $id."tbl.columns[1]['readonly'] = false;\n";

        $str .= $id."tbl.columns[2] = new Array;\n";
        $str .= $id."tbl.columns[2]['name'] = 'operation';\n";
        $str .= $id."tbl.columns[2]['title'] = 'Операция';\n";
        $str .= $id."tbl.columns[2]['class'] = 'cell';\n";
        $str .= $id."tbl.columns[2]['control'] = 'plaintext';\n";
        $str .= $id."tbl.columns[2]['control_properties'] = 'deactivated=true,input_class=input1';\n";
        $str .= $id."tbl.columns[2]['must_set'] = false;\n";
        $str .= $id."tbl.columns[2]['unique'] = false;\n";
        $str .= $id."tbl.columns[2]['readonly'] = false;\n";

        $str .= $id."tbl.columns[3] = new Array;\n";
        $str .= $id."tbl.columns[3]['name'] = 'time';\n";
        $str .= $id."tbl.columns[3]['title'] = 'Продолжительность';\n";
        $str .= $id."tbl.columns[3]['class'] = 'cell';\n";
        $str .= $id."tbl.columns[3]['control'] = 'plaintext';\n";
        $str .= $id."tbl.columns[3]['control_properties'] = 'deactivated=true,input_class=input1';\n";
        $str .= $id."tbl.columns[3]['must_set'] = false;\n";
        $str .= $id."tbl.columns[3]['unique'] = false;\n";
        $str .= $id."tbl.columns[3]['readonly'] = false;\n";
        
        $str .= $id."tbl.columns[4] = new Array;\n";
        $str .= $id."tbl.columns[4]['name'] = 'description';\n";
        $str .= $id."tbl.columns[4]['title'] = 'Подробности';\n";
        $str .= $id."tbl.columns[4]['class'] = 'cell';\n";
        $str .= $id."tbl.columns[4]['properties'] = 'width=100%';\n";
        $str .= $id."tbl.columns[4]['control'] = 'plaintext';\n";
        $str .= $id."tbl.columns[4]['control_properties'] = 'deactivated=true,input_class=input1';\n";
        $str .= $id."tbl.columns[4]['must_set'] = false;\n";
        $str .= $id."tbl.columns[4]['unique'] = false;\n";
        $str .= $id."tbl.columns[4]['readonly'] = false;\n";
        
        $str .= $id."tbl.rows = new Array;\n";
        $str .= $id."tbl.rows[0] = new Array;\n";
        $str .= $id."tbl.rows[0]['class'] = '';\n";
        $str .= $id."tbl.rows[0]['properties'] = '';\n";
        $str .= $id."tbl.rows[0]['cells'] = new Array;\n";

        $str .= $id."tbl.rows[0]['cells'][0] = new Array;\n";
        $str .= $id."tbl.rows[0]['cells'][0]['properties'] = '';\n";
        $str .= $id."tbl.rows[0]['cells'][0]['value'] = 'header';\n";
        $str .= $id."tbl.rows[0]['cells'][0]['control'] = 'plaintext';\n";
        $str .= $id."tbl.rows[0]['cells'][0]['class'] = 'hidden';\n";
        $str .= $id."tbl.rows[0]['cells'][0]['control_properties'] = 'input_class=input1,deactivated=false';\n";

        $str .= $id."tbl.rows[0]['cells'][1] = new Array;\n";
        $str .= $id."tbl.rows[0]['cells'][1]['properties'] = '';\n";
        $str .= $id."tbl.rows[0]['cells'][1]['value'] = 'Имя Хоста';\n";
        $str .= $id."tbl.rows[0]['cells'][1]['control'] = 'header';\n";
        $str .= $id."tbl.rows[0]['cells'][1]['class'] = 'header';\n";
        $str .= $id."tbl.rows[0]['cells'][1]['control_properties'] = 'input_class=input1,deactivated=false';\n";

        $str .= $id."tbl.rows[0]['cells'][2] = new Array;\n";
        $str .= $id."tbl.rows[0]['cells'][2]['properties'] = '';\n";
        $str .= $id."tbl.rows[0]['cells'][2]['value'] = 'Операция';\n";
        $str .= $id."tbl.rows[0]['cells'][2]['control'] = 'header';\n";
        $str .= $id."tbl.rows[0]['cells'][2]['class'] = 'header';\n";
        $str .= $id."tbl.rows[0]['cells'][2]['control_properties'] = 'input_class=input1,deactivated=false';\n";

        $str .= $id."tbl.rows[0]['cells'][3] = new Array;\n";
        $str .= $id."tbl.rows[0]['cells'][3]['properties'] = '';\n";
        $str .= $id."tbl.rows[0]['cells'][3]['value'] = 'Продолжительность';\n";
        $str .= $id."tbl.rows[0]['cells'][3]['control'] = 'header';\n";
        $str .= $id."tbl.rows[0]['cells'][3]['class'] = 'header';\n";
        $str .= $id."tbl.rows[0]['cells'][3]['control_properties'] = 'input_class=input1,deactivated=false';\n";

        $str .= $id."tbl.rows[0]['cells'][4] = new Array;\n";
        $str .= $id."tbl.rows[0]['cells'][4]['properties'] = '';\n";
        $str .= $id."tbl.rows[0]['cells'][4]['value'] = 'Подробности';\n";
        $str .= $id."tbl.rows[0]['cells'][4]['control'] = 'header';\n";
        $str .= $id."tbl.rows[0]['cells'][4]['class'] = 'header';\n";
        $str .= $id."tbl.rows[0]['cells'][4]['control_properties'] = 'input_class=input1,deactivated=false';\n";        

        $str .= $id."tbl.emptyrow = new Array;\n";
        $str .= $id."tbl.emptyrow['class'] = '';\n";
        $str .= $id."tbl.emptyrow['properties'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'] = new Array;\n";
        $str .= $id."tbl.emptyrow['cells'][0] = new Array;\n";
        $str .= $id."tbl.emptyrow['cells'][0]['properties'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][0]['control'] = 'hidden';\n";
        $str .= $id."tbl.emptyrow['cells'][0]['value'] = '0';\n";
        $str .= $id."tbl.emptyrow['cells'][1] = new Array;\n";
        $str .= $id."tbl.emptyrow['cells'][1]['properties'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][1]['control'] = 'hidden';\n";
        $str .= $id."tbl.emptyrow['cells'][1]['value'] = '0';\n";
        $str .= $id."tbl.emptyrow['cells'][2] = new Array;\n";
        $str .= $id."tbl.emptyrow['cells'][2]['properties'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][2]['control'] = 'plaintext';\n";
        $str .= $id."tbl.emptyrow['cells'][2]['value'] = '0';\n";
        $str .= $id."tbl.emptyrow['cells'][3] = new Array;\n";
        $str .= $id."tbl.emptyrow['cells'][3]['properties'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][3]['value'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][4] = new Array;\n";
        $str .= $id."tbl.emptyrow['cells'][4]['properties'] = '';\n";
        $str .= $id."tbl.emptyrow['cells'][4]['control'] = 'plaintext';\n";
        $str .= $id."tbl.emptyrow['cells'][4]['value'] = '';\n";
		$userArray = array();
        if (count($connections)>0) {
			if ($this->condition!="")
				$this->currentUser = $this->condition;
			$c=1;
            foreach ($connections as $value) {
				if (!isset($userArray[@$value["user"]]))
					$userArray[@$value["user"]] = @$value["user"];
				if (@$value["user"]!=$this->currentUser)
					continue;
                $str .= $id."tbl.rows[$c] = new Array;\n";
                $str .= $id."tbl.rows[$c]['class'] = '';\n";
                $str .= $id."tbl.rows[$c]['properties'] = '';\n";
                $str .= $id."tbl.rows[$c]['cells'] = new Array;\n";

                $str .= $id."tbl.rows[$c]['cells'][0] = new Array;\n";
                $str .= $id."tbl.rows[$c]['cells'][0]['class'] = 'hidden';\n";
                $str .= $id."tbl.rows[$c]['cells'][0]['value'] = '".@$value["user"]."';\n";
                
                $str .= $id."tbl.rows[$c]['cells'][1] = new Array;\n";
                $str .= $id."tbl.rows[$c]['cells'][1]['class'] = 'cell';\n";
                $str .= $id."tbl.rows[$c]['cells'][1]['value'] = '".@$value["host"]."';\n";

                $str .= $id."tbl.rows[$c]['cells'][2] = new Array;\n";
                $str .= $id."tbl.rows[$c]['cells'][2]['properties'] = '';\n";
                $str .= $id."tbl.rows[$c]['cells'][2]['class'] = 'cell';\n";
                $str .= $id."tbl.rows[$c]['cells'][2]['value'] = '".@$value["operation"]."';\n";
                    
                $str .= $id."tbl.rows[$c]['cells'][3] = new Array;\n";
                $str .= $id."tbl.rows[$c]['cells'][3]['properties'] = '';\n";
                $str .= $id."tbl.rows[$c]['cells'][3]['class'] = 'cell';\n";
                $str .= $id."tbl.rows[$c]['cells'][3]['value'] = '".@$value["time"]."';\n";
                    
                $str .= $id."tbl.rows[$c]['cells'][4] = new Array;\n";
                $str .= $id."tbl.rows[$c]['cells'][4]['properties'] = '';\n";
                $str .= $id."tbl.rows[$c]['cells'][4]['class'] = 'cell';\n";
                $str .= $id."tbl.rows[$c]['cells'][4]['value'] = '".@$value["description"]."';\n";
				$c++;
            }
        }
        $this->userList = " ~".implode("~",$userArray)."| ~".implode("~",$userArray);
        $str .= $id."tbl.userList='".$this->userList."';\n";
        $str .= $id."tbl.currentUser='".$this->currentUser."';\n";            
        
        $result["{userList}"] = $this->userList;
        $result["{data}"] = $str;
        return $result;
    }   
    
    function getHookProc($number) {
		switch ($number) {
			case '3': return "rebuild";
			case '4': return "kickUser";
		}
		return parent::getHookProc($number);
	}
	
	function rebuild($arguments) {
		$this->currentUser = $arguments["currentUser"];
		$this->ftpHostName = $arguments["ftpHostName"];
		$result = $this->getArgs();
		echo $result["{data}"];
	}
	
	function kickUser($arguments) {
		$this->user = trim($arguments["user"]);
		if ($this->user!="") {
			global $Objects;
			$ftpServer = $Objects->get("FTPServer_".$this->module_id."_ftp");
			$capp = $Objects->get($this->module_id);
			if ($capp->remoteSSHCommand=="")
				$this->shell->exec_command($capp->remoteSSHCommand." ".str_replace("{command}","kick user ".$this->user,$ftpServer->ftpdctlCommand));
			else
				shell_exec($capp->remoteSSHCommand." ".str_replace("{command}","kick user ".$this->user,$ftpServer->ftpdctlCommand));
		}
	}
}
?>