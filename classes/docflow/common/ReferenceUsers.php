<?php
// Справочник пользователей (сотрудников)
class ReferenceUsers extends Reference {
	
	function construct($params) {
		parent::construct($params);
		$this->clientClass = "ReferenceEmailAccounts";
		$this->hierarchy = true;
		$this->parentClientClasses = "Reference~Entity";
		$this->classListTitle = "Пользователи";
		$this->classTitle = "Пользователь";		
		$this->template = "renderForm";
        $this->fieldList = "title Фамилия~firstName Имя~secondName Отчество";
        $this->additionalFields = "name~isGroup~parent~deleted";
        $this->condition = "@parent IS NOT EXISTS";
        $this->printFieldList = $this->fieldList;
        $this->allFieldList = $this->fieldList;
        $this->width = "700";
        $this->height = "600";     
        $this->overrided = "width,height";
        $this->displayHasAdminAccess = "none";
        $this->displayUserEmailAddress = "none";
        $this->conditionFields = "title~Фамилия~string|firstName~Имя~string|secondName~Отчество~string";        
        $this->sortOrder = "title ASC";
        $this->tabs_string  = "main|Основное|".$this->skinPath."images/spacer.gif";
        $this->tabs_string .= ";personal|Личное|".$this->skinPath."images/spacer.gif";
        $this->tabs_string .= ";work|Работа|".$this->skinPath."images/spacer.gif";
        $this->tabs_string .= ";contacts|Контакты|".$this->skinPath."images/spacer.gif";
        if ($this->name!="")
        	$this->tabs_string .= ";emails|Email|".$this->skinPath."images/spacer.gif";
        $this->tabset_id = "WebItemTabset_".$this->module_id."_ReferenceUsers".$this->name;
        $this->handler = "scripts/handlers/docflow/common/ReferenceUsers.js";
        $this->tabsetName = $this->tabset_id;
        $this->active_tab = "main";
        $this->adapterId = $this->adapter->getId();
        $this->skinPath = $this->app->skinPath;
        $this->icon = $this->app->skinPath."images/Tree/user.png";
		global $Objects;
        $apacheUsers = $Objects->get("ApacheUsers_".$this->module_id."_Users");
        if (!$apacheUsers->loaded)
        	$apacheUsers->load();
        $arr = array();
        $arr[" "] = "";
        foreach($apacheUsers->apacheUsers as $value) {
        	$arr[$value->name] = $value->name;
        }
        
        $this->persistedFields["account"] = array("params" => array("type" => "list,".implode("~",$arr)."|".implode("~",$arr)));
                
		$this->setRole();
		$this->accountControl = "simpleAccount";
		if (isset($this->role["accountControl"])) {
			$this->accountControl = $this->getRoleValue($this->role["accountControl"]);
		}
	}
	
	function renderForm() {
		$blocks = getPrintBlocks(file_get_contents("templates/docflow/common/ReferenceUsers.html"));
		$common = getPrintBlocks(file_get_contents("templates/docflow/common/CommonFields.html"));
		$out = str_replace("{hiddenFields}",$common["hiddenFields"],$blocks["header"]);
		$out .= $blocks[$this->accountControl];
		$out .= $blocks["rest"];
		if ($this->name!="") {
			$out .= $blocks["notNew"];
			$out .= $common["tabs"];
			$out .= $common["profileEditTab"];
		}
		$out .= $blocks["footer"];		
		return $out;
	}
	
	function getPresentation($full=true) {
		if ($this->noPresent)
			return "";
		$this->loaded = false;
		$this->load();
		if ($this->fullName=="")
			return "";
		if (!$full)
			return $this->fullName;
		if ($this->classTitle != "")
			return $this->classTitle.":".$this->fullName;
		else
			return $this->fullName;
	}	
		
	function checkData() {
		
		global $Objects,$roles;
		
		if (!$this->isGroup) {
			if ($this->title=="") {
				$this->reportError("Укажите Фамилию","save1");
				return 0;
			}
			if ($this->firstName=="") {
				$this->reportError("Укажите Имя","save2");
				return 0;
			}
			if ($this->secondName=="") {
				$this->reportError("Укажите отчество","save3");
				return 0;
			}
			if ($this->accountControl=="advancedAccount") {
				if (!$this->loaded)
					$this->load();
				
				if ($this->account=="") {
					$this->reportError("Укажите логин","save5");
					return 0;						
				}				
				if ($this->accountPassword=="") {
					$this->reportError("Укажите пароль","save8");
					return 0;
				}
				if ($this->accountPassword!=$this->accountPassword2) {
					$this->reportError("Пароли не совпадают","save4");
					return 0;
				}			
				if ($this->name!="")
					$query = "SELECT entities FROM fields WHERE @account='".$this->account."' AND @name!=".$this->name." AND @classname='".get_class($this)."'";
				else
					$query = "SELECT entities FROM fields WHERE @account=".$this->account." AND @classname='".get_class($this)."'";
				$result = PDODataAdapter::makeQuery($query, $this->adapter,$this->module_id);
				if (count($result)>0) {
					$this->reportError("Пользователь с таким логином уже зарегистрирован!","save6");
					return 0;
				}
				if ($this->oldAccount!="") {
					$user = $Objects->get("ApacheUser_".$this->module_id."_".$this->oldAccount);
				}
				else {				
					$user = $Objects->get("ApacheUser_".$this->module_id."_".$this->account);
				}
				$user->load();
				$user->hasAdminAccess = $this->hasAdminAccess;
				$user->name = $this->account;
				$user->password = $this->accountPassword;
				$args = array();
				$args["roles"] = array("base","sales",$this->accountRole);
				$user->save($args);
				if ($this->oldAccount!=$this->account and $this->webServerApplication!="") {
					$siteDataAdapter = $Objects->get("SiteDataAdapter_".$this->webServerApplication."_".$this->webSite."_site");					
					$siteDataAdapter->connect();
					if ($siteDataAdapter->connected) {
						$query = "UPDATE rights SET object='".$this->account."' WHERE object='".$this->oldAccount."'";
						$stmt = $siteDataAdapter->dbh->prepare($query);
						$stmt->execute();
					}					
				}				
				$shell = $Objects->get("Shell_shell");
				$shell->exec_command($this->app->apacheRestartCommand);								
			}	
			$this->fullName = $this->title." ".$this->firstName." ".$this->secondName;
		} else {
			if ($this->title=="") {
				$this->reportError("Укажите наименование","save7");
				return 0;
			}				
		}
		return parent::checkData();			
	}
	
	function afterLoad() {
		$this->oldAccount = $this->account;
		if ($this->account!="") {
			global $Objects;
			$user = $Objects->get("ApacheUser_".$this->module_id."_".$this->account);
			if (!$user->loaded)
				$user->load();
			$this->hasAdminAccess = $user->hasAdminAccess;
		} else
			$this->hasAdminAccess = 0;
	}
	
	function afterRemove() {
		if ($this->accountControl=="advancedAccount") {
			global $Objects;
			$user = $Objects->get("ApacheUser_".$this->module_id."_".$this->account);
			$user->remove();
			
			if ($this->webServerApplication!="") {
				$siteDataAdapter = $Objects->get("SiteDataAdapter_".$this->webServerApplication."_".$this->webSite."_site");
				$siteDataAdapter->connect();				
				if ($siteDataAdapter->connected) {
					$query = "DELETE FROM rights WHERE object='".$this->account."'";
					$stmt = $siteDataAdapter->dbh->prepare($query);
					$stmt->execute();
				}
			}
		}
	}
	
	function getArgs() {		        
		if ($this->window_id=="")
			$this->window_id = @$_GET["window_id"];
		
		global $roles,$Objects;
		$roles_list = array();		
		foreach ($roles as $key=>$value)
			if ($value["visible"]!="false")
				$roles_list[$key] = $value["title"];
		$this->accountRolesList = implode("~",array_keys($roles_list))."|".implode("~",$roles_list);
		$this->entityImages = json_encode(array());
		
		if ($this->accountControl=="advancedAccount") {
			$user = $Objects->get("ApacheUser_".$this->module_id."_".$this->account);
			if (!$user->loaded)
				$user->load();
			if ($user->loaded) {
				$this->accountPassword = $user->password;
				$this->accountPassword2 = $user->password;
			}				
		}
		
		$this->emailfieldList = "email Адрес Email~title Описание";
		$this->emailitemsPerPage = 10;
		$this->emailsortOrder = "email ASC";
		$this->emailsortField = $this->emailsortOrder;
		$this->emailconditon = "@parent IS NOT EXISTS";
		
		$this->entityImages = json_encode(array());
		if (is_object($this->defaultEmail)) {
			$this->entityImages = json_encode(array($this->defaultEmail->getId() => $this->skinPath."images/Buttons/RegisteredDocumentEntityImage.png"));
		}
		$this->emailsCode = '$object->topLinkObject="'.$this->getId().'";
		$object->window_id="'.$this->window_id.'";
		$object->parent_object_id="'.$this->getId().'";
		$object->className="ReferenceEmailAccounts";
		$object->defaultClassName="ReferenceEmailAccounts";
		$object->fieldList="'.$this->emailfieldList.'";
		$object->itemsPerPage='.$this->emailitemsPerPage.';
		$object->currentPage=1;
		$object->hierarchy=0;
		$object->additionalFields="name~deleted";
		$object->adapterId="'.$this->adapter->getId().'";
		$object->autoload="false";
		$object->sortOrder="'.$this->emailSortOrder.'";';
		
		$this->emailsCode = cleanText($this->emailsCode);
		$this->emailsTableId = "DocFlowReferenceTable_".$this->module_id."_".$this->name."emails";				
						
		return parent::getArgs();
	}
}
?>