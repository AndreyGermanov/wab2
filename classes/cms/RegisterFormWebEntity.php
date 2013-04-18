<?php
class RegisterFormWebEntity extends InputWebEntity {
	
	function construct($params) {
		parent::construct($params);
		$this->models[] = "InputWebEntity";
		$this->email1 = "";
		$this->email2 = "";
		$this->password1 = "";
		$this->password2 = "";
		$this->login = "";		
	}
	
	function submitForm() {
		
		if ((!$this->loaded))			
			$this->load();
		
		$this->submitFormFields.="~login~password1~password2~email1~email2";
		if (!is_array($this->childPersistedFields)) {
			$this->childPersistedFields .="\nlogin|string|type=string\n";
			$this->childPersistedFields .="password1|string|type=string\n";
			$this->childPersistedFields .="password2|string|type=string\n";
			$this->childPersistedFields .="email1|string|type=string\n";
			$this->childPersistedFields .="email2|string|type=string";
		} else {
			$this->childPersistedFields["login"] = array("type"=>"string","params" => array("type"=>"string"));
			$this->childPersistedFields["password1"] = array("type"=>"string","params" => array("type"=>"string","password"=>"true"));
			$this->childPersistedFields["password2"] = array("type"=>"string","params" => array("type"=>"string","password"=>"true"));
			$this->childPersistedFields["email1"] = array("type"=>"string","params" => array("type"=>"string"));
			$this->childPersistedFields["email2"] = array("type"=>"string","params" => array("type"=>"string"));				
		}
		$this->submitForm->childPersistedFields = $this->childPersistedFields;
		$this->getData();
		ob_start();
		$error = false;
		if (trim($this->email1)=="") {
			$this->reportError("Email не указан!");
			$error=true;			
		}
		if (!$error and trim($this->email1) != trim($this->email2)) {
			$this->reportError("Введенные E-Mail-адреса не совпадают!");
			$error=true;			
		}
		if (!$error and trim($this->login)=="") {
			$this->reportError("Логин не указан!");
			$error=true;			
		}
		if (!$error and $this->password1=="") {
			$this->reportError("Пароль не указан!");
			$error=true;			
		}
		if (!$error and $this->password1!=$this->password2) {
			$this->reportError("Пароли не совпадают!");
			$error=true;			
		}
		
		global $Objects;
		
		$user = $Objects->get("ApacheUser_".$this->module_id."_".$this->login);
		$user->load();
		if (!$error and $user->password) {
			$this->reportError("Пользователь с указанным логином уже зарегистрирован");
			$error=true;			
		}
		
		parent::submitForm();		
	}
}