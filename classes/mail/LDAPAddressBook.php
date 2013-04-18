<?php
	class LDAPAddressBook extends WABEntity {
		
		public $groups = array();
		public $modelFields = array();
		
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
			$this->group = "";
			$this->fullGroup = "";
			$this->ldapHost = "";
			$this->ldapPort	= "";
			$this->ldapBindDN = "";
			$this->ldapBindPassword = "";
			$this->ldapBaseDN = "";
			$this->addrbookFields = $GLOBALS["addressbookFieldsTemplate"];
			$this->template = "renderForm";
			$this->fieldName = "0";
			$this->metadataArray = "addressbookFieldsTemplate";
			$this->app = $Objects->get("Application");
			if (!$this->app->initiated)
				$this->app->initModules();
			$this->skinPath = $this->app->skinPath;
			$this->icon = $this->skinPath."images/Tree/addrbook.gif";
			$this->css = $this->skinPath."styles/Mailbox.css";
			$this->handler = "scripts/handlers/mail/LDAPAddressBook.js";
			$this->width = "450";
			$this->height = "550";
			$this->overrided = "width,height";
			$this->fieldParamsTable = "MetadataFieldParamsTable_".$this->module_id."_".$this->name."Table";
			$this->tabset_id = "WebItemTabset_".$this->module_id."_".$this->name."ModelPanel";
			$this->tabs_string = "main|Подключение|".$this->app->skinPath."images/spacer.gif;";
			$this->tabs_string .= "params|Параметры|".$this->app->skinPath."images/spacer.gif";
			$this->active_tab = "main";				
        	$this->clientClass = "LDAPAddressBook";
        	$this->parentClasses = "Entity";
			$this->loaded = false;
	        $this->classTitle = "Адресная книга LDAP";
	        $this->classListTitle = "Адресные книги LDAP";
		}
		
		function renderForm() {
			$blocks = getPrintBlocks(file_get_contents("templates/mail/LDAPAddressBook.html"));
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
			return $this->name;
		}
		
		function load() {
			global $addressbooks;
			$name = $this->name;
			if (isset($addressbooks[$name])) {
				$this->title = @$addressbooks[$name]["title"];
				$this->old_title = $this->title;
				$this->fieldName = $name;
				$this->metadataArray = "addressbooks";
				$this->ldapHost = @$addressbooks[$name]["ldapHost"];
				$this->ldapPort = @$addressbooks[$name]["ldapPort"];
				$this->ldapBindDN = @$addressbooks[$name]["ldapBindDN"];
				$this->ldapBindPassword = @$addressbooks[$name]["ldapBindPassword"];
				$this->ldapBaseDN = @$addressbooks[$name]["ldapBaseDN"];
				$this->addrbookFields = @$addressbooks[$name]["params"];
				$this->loaded = true;
			}			
		}
		
		function checkData() {
			
			global $addressbooks;				
			if ($this->name=="") {
				$this->reportError("Укажите 'системное имя'",'save');
				return 0;
			}
			if ($this->name!=$this->old_name) {
				if (isset($addressbooks[$this->name])) {					
					$this->reportError("Поле с таким именем уже существует",'save');
					return 0;
				}
			}
				
			if ($this->title=="") {
				$this->reportError("Укажите 'описание'","save");
				return 0;
			}

			if ($this->ldapHost=="") {
				$this->reportError("Укажите 'Имя сервера'","save");
				return 0;
			}
			
			if ($this->ldapPort=="") {
				$this->reportError("Укажите 'Порт сервера'","save");
				return 0;
			}

			if ($this->ldapBindDN=="") {
				$this->reportError("Укажите 'Имя пользователя'","save");
				return 0;
			}

			if ($this->ldapBindPassword=="") {
				$this->reportError("Укажите 'Пароль'","save");
				return 0;
			}
				
			if ($this->ldapBaseDN=="") {
				$this->reportError("Укажите 'Корневой узел дерева'","save");
				return 0;
			}
			
			return 1;
		}
		
		function save($arguments) {
			global $Objects;
			if (!$this->loaded)
				$this->load();
			$this->setArguments($arguments);
			$this->addrbookFields = @$arguments["fieldsTbl"];
			$this->addrbookFields = (array)$this->addrbookFields;

			
			if ($this->checkData()) {
				
				if (!$this->loaded)
					$this->load();
				if ($this->ldapPort=="636")
					$this->ldapProto = "ldaps";
				else
					$this->ldapProto = "ldap";
				$ds = ldap_connect($this->ldapProto."://".$this->ldapHost);
				if (ldap_error($ds)!="Success") {
					$this->reportError("Невозможно подключиться к указанному серверу. Проверьте имя сервера и порт.");
					return 0;
				}
					
				ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
				$r = ldap_bind($ds,$this->ldapBindDN,$this->ldapBindPassword);
				if (ldap_error($ds)!="Success") {
					$this->reportError("Невозможно подключиться к указанному серверу. Проверьте имя и пароль.");
					return 0;
				}
					
				$res = @ldap_list($ds, $this->ldapBaseDN,"(objectClass=*)");
				if ($res == FALSE) {
					$arr = explode(",",$this->ldapBaseDN);
					$parts = explode("=",$arr[0]);
					$entry = array();
					$entry[trim($parts[0])] = trim($parts[1]);
					$entry["objectClass"][0] = "organizationalUnit";
					ldap_add($ds,$this->ldapBaseDN,$entry);
				}
				
				if ($this->addrbookFields!="" and is_array($this->addrbookFields)) {
					$this->addrbookFields = array_unique($this->addrbookFields);
				}
				if ($this->name!=$this->old_name and $this->old_name!="") {
					unset($GLOBALS["addressbooks"][$this->old_name]);
				}
				$GLOBALS["addressbooks"][$this->name] = array();
				$GLOBALS["addressbooks"][$this->name]["name"] = $this->name;
				$GLOBALS["addressbooks"][$this->name]["collection"] = "addressbooks";				
				$GLOBALS["addressbooks"][$this->name]["params"] = $this->addrbookFields;
				$GLOBALS["addressbooks"][$this->name]["title"] = $this->title;
				$GLOBALS["addressbooks"][$this->name]["file"] = $this->file;
				$GLOBALS["addressbooks"][$this->name]["ldapHost"] = $this->ldapHost;
				$GLOBALS["addressbooks"][$this->name]["ldapPort"] = $this->ldapPort;
				$GLOBALS["addressbooks"][$this->name]["ldapBindDN"] = $this->ldapBindDN;
				$GLOBALS["addressbooks"][$this->name]["ldapBindPassword"] = $this->ldapBindPassword;
				$GLOBALS["addressbooks"][$this->name]["ldapBaseDN"] = $this->ldapBaseDN;
				$str = "<?php\n".getMetadataString(getMetadataInFile($this->file))."\n?>";
				file_put_contents($this->file,$str);
				$app = $Objects->get("Application");
				if (!$app->initiated)
					$app->initModules();
				if ($this->old_name=="")
					$app->raiseRemoteEvent("LDAPADDRBOOK_ADDED","object_id=".$this->getId());
				else
					$app->raiseRemoteEvent("LDAPADDRBOOK_CHANGED","object_id=".$this->getId());
			}				
		}
		
		function remove($arguments) {
			$this->setArguments($arguments);
			if (isset($GLOBALS["addressbooks"][$this->name])) {
				$file = $this->file;
				global $Objects;
				$app = $Objects->get("Application");
				if (!$app->initiated)
					$app->initModules();
				$app->raiseRemoteEvent("LDAPADDRBOOK_DELETED","object_id=".$this->getId());
				unset($GLOBALS["addressbooks"][$this->name]);
				$str = "<?php\n".getMetadataString(getMetadataInFile($file))."\n";
				file_put_contents($file,$str);				
			}
		}

		function getFields($mail) {
			if (!$this->loaded)
				$this->load();
			
			if ($this->ldapPort=="636")
				$this->ldapProto = "ldaps";
			else
				$this->ldapProto = "ldap";
			$ds = ldap_connect($this->ldapProto."://".$this->ldapHost);

			if (ldap_error($ds)!="Success") {
				return 0;
			}
				
			ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
			$r = ldap_bind($ds,$this->ldapBindDN,$this->ldapBindPassword);
			if (ldap_error($ds)!="Success") {
				return 0;
			}
				
			$res = ldap_list($ds, $this->ldapBaseDN, "(mail=".$mail.")");
			if ($res == FALSE)
				return 0;
			$entries = ldap_get_entries($ds,$res);
			if ($entries["count"]<1)
				return 0;
			$entry = $entries[0];
			$result = array();
			foreach ($this->addrbookFields as $key=>$value) {
				if (isset($entry[strtolower($key)])) {
					$result["{".$key."}"] = $entry[strtolower($key)][0];
					$result["{".$value."}"] = $entry[strtolower($key)][0];
				}
			}
			return $result;
		}
		
		function printList($arguments) {
			$this->setArguments($arguments);
			$this->addrbookFields = @$arguments["fields"];
			$this->addrbookFields = (array)$this->addrbookFields;
			$blocks = getPrintBlocks(file_get_contents("templates/mail/LDAPAddressesList.html"));
			$out = $blocks["header"];

			if ($this->ldapPort=="636")
				$this->ldapProto = "ldaps";
			else
				$this->ldapProto = "ldap";
			
			$ds = ldap_connect($this->ldapProto."://".$this->ldapHost);
			
			if (ldap_error($ds)!="Success") {
				$this->reportError("Невозможно подключиться к указанному серверу. Проверьте имя сервера и порт.");
				return 0;
			}
				
			ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
			$r = ldap_bind($ds,$this->ldapBindDN,$this->ldapBindPassword);
			if (ldap_error($ds)!="Success") {
				$this->reportError("Невозможно подключиться к указанному серверу. Проверьте имя и пароль.");
				return 0;
			}
				
			$res = @ldap_list($ds, $this->ldapBaseDN, "(objectClass=*)");
			if ($res == FALSE) {
				$this->reportError("Невозможно подключиться к указанному серверу. Проверьте корневой узел дерева.");
				return 0;
			}
				
			$entries = ldap_get_entries($ds,$res);
			
			foreach ($entries as $entry) {
				if (!is_array($entry))
					continue;
				$out .= $blocks["headerRow"];
				foreach ($entry as $key=>$value) {
					if (is_numeric($key) or @$value=="")
						continue;					
					if (is_array($value))
						$value = $value[0];
					if (strlen($value)>200)
						$value = "{Многострочный текст}";
					$args = array("{name}" => $key, "{title}" => @$this->addrbookFields[$key], "{value}" => @$value);
					$out .= strtr($blocks["row"],$args);
				}
				$out .= $blocks["rowFooter"];
			}
			$out .= $blocks["footer"];
			file_put_contents("tmp/addrbook.html",$out);
		}
		
		function getHookProc($number) {
			switch($number) {
				case '3': return "save";
				case '4': return "remove";
				case '5': return "printList";
			}
			return parent::getHookProc($number);
		}		
	}
?>