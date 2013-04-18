<?
/**
 *  Класс, обрабатывающий загрузку файлов на сервер
 *  с использованием компонента SWFUpload
 *
 * (C) 2012 ООО "ЛВА"
 * Все права защищеы
 *
 * @andrey 03.07.2012 23:44
 */

class FileUpload extends WABEntity {
  
  function construct($params) {
	$this->module_id = array_shift($params)."_".array_shift($params);
	$this->name = implode("_",$params);

	$this->buttonId = "";
	$this->buttonURL = "";
	$this->buttonWidth = "";
	$this->buttonHeight = "";
	$this->buttonText = "<span style='width:100%;height:100%' title='Загрузить файл'>&nbsp;</span>";
	$this->handler = "scripts/handlers/interface/FileManager/FileUpload.js";
	$this->clientClass = "FileUpload";
	$this->parentClientClasses = "Entity";	
  }

	function getId() {
		return get_class($this)."_".$this->module_id."_".$this->name;
	}

	function getHookProc($number) {
		switch ($number) {
			case '3': return "uploadFile";
		}
		return parent::getHookProc($number);
	}

	function uploadFile($arguments) {
		if (isset($arguments["ftpUser"])) {
			global $Objects,$ftpUser;		
			$app = $Objects->get("Application");
			if (!$app->initiated)
				$app->initModules();	
			//$ftpUser = $GLOBALS["ftpUser"];//$Objects->get("User_ControllerApplication_Network_".$arguments["ftpUser"]);
//			if (!$ftpUser->loaded)
//				$ftpUser->load();
			$fp = fopen("/tmp/file2","w");
			fwrite($fp,$ftpUser->name);
			fclose($fp);
			$conn_id = ftp_connect("localhost");
			if (!ftp_login($conn_id,$ftpUser->name,$ftpUser->ftpPassword))
				return 0;
			if (!ftp_chdir($conn_id,$arguments["path"])) {
				return 0;
			}
			if (!ftp_mkdir($conn_id,"xoxzoz")) {
				return 0;
			}
			ftp_rmdir($conn_id,'xoxzoz');
			$arguments["path"] = str_replace("//","/",$ftpUser->ftpHome."/".$arguments["path"]);
		}	
  		if (is_uploaded_file($_FILES['Filedata']['tmp_name'])) {
			move_uploaded_file($_FILES['Filedata']['tmp_name'],$arguments["path"]."/".$_FILES['Filedata']['name']);
			$app->raiseRemoteEvent("FM_UPLOAD","message=Пользователь загрузил на сервер файл `".$arguments["path"]."/".$_FILES['Filedata']['name']."`");
		}
	}
}