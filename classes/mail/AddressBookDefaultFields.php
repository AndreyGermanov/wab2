<?php
	class AddressBookDefaultFields extends WABEntity {
		
		public $addrbookFields = array();
		
		function construct($params) {
			global $Objects;
			$this->module_id = array_shift($params)."_".array_shift($params);
			$this->rnd = array_shift($params);
			$this->name = implode("_",$params);
			$this->old_name = $this->name;
			$this->title = "";
			$this->old_title = "";
			$this->file = "/opt/WAB2/metadata/modules/mail/addressbooks.php";
			$this->old_file = "";
			$this->addrbookFields = $GLOBALS["addressBookDefaultFields"]["first"]["params"];
			$this->template = "renderForm";
			$this->fieldName = "first";
			$this->metadataArray = "addressBookDefaultFields";
			$this->app = $Objects->get("Application");
			if (!$this->app->initiated)
				$this->app->initModules();
			$this->skinPath = $this->app->skinPath;
			$this->icon = $this->skinPath."images/Tree/addrbook.gif";
			$this->css = $this->skinPath."styles/Mailbox.css";
			$this->handler = "scripts/handlers/mail/AddressBookDefaultFields.js";
			$this->width = "450";
			$this->height = "550";
			$this->overrided = "width,height";
			$this->fieldParamsTable = "MetadataFieldParamsTable_".$this->module_id."_".$this->name."Table";
        	$this->clientClass = "AddressBookDefaultFields";
        	$this->parentClasses = "Entity";
			$this->loaded = false;
		}
		
		function renderForm() {
			$blocks = getPrintBlocks(file_get_contents("templates/mail/AddressBookDefaultFields.html"));
			$out = $blocks["header"];
			return $out;
		}
		
		function getId() {
			if ($this->module_id!="")
				return get_class($this)."_".$this->module_id."_".$this->rnd."_".$this->name;
			else
				return get_class($this)."_".$this->rnd."_".$this->name;
		}
		
		function getPresentation() {
			return "Общие поля";
		}
		
		function load() {
			return true;
		}
		
		function checkData() {
						
			return 1;
		}
		
		function save($arguments) {
			if (!$this->loaded)
				$this->load();
			$this->setArguments($arguments);
			$this->addrbookFields = @$arguments["fields"];
			$this->addrbookFields = (array)$this->addrbookFields;
				
			if ($this->checkData()) {
				if ($this->addrbookFields!="" and is_array($this->addrbookFields)) {
					$this->addrbookFields = array_unique($this->addrbookFields);
				}
				$GLOBALS["addressBookDefaultFields"][$this->name] = array();
				$GLOBALS["addressBookDefaultFields"][$this->name]["name"] = $this->name;
				$GLOBALS["addressBookDefaultFields"][$this->name]["collection"] = "addressBookDefaultFields";				
				$GLOBALS["addressBookDefaultFields"][$this->name]["params"] = $this->addrbookFields;
				$GLOBALS["addressBookDefaultFields"][$this->name]["file"] = $this->file;
				$str = "<?php\n".getMetadataString(getMetadataInFile($this->file))."\n?>";
				file_put_contents($this->file,$str);
				global $Objects;
				$app = $Objects->get("Application");
				if (!$app->initiated)
					$app->initModules();
				$app->raiseRemoteEvent("ADDRBOOK_DEFAULT_FIELDS_CHANGED");
			}				
		}
		
		function getFields() {
			$result = array();
			foreach($this->addrbookFields as $key=>$value) {
				$result["{".$key."}"] = $value;
			}
			return $result;
		}
		
		function getHookProc($number) {
			switch($number) {
				case '3': return "save";
			}
			return parent::getHookProc($number);
		}		
	}
?>