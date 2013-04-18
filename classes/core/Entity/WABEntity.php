<?php
class WABEntity {
	
    public $fields = array();
    public $models = array();
    public $loaded_fields = array();
    public $adapter;
    public $persistedFields = array();
    public $role = array();
    public $settings = array();
    public $userSettings = array();
    public $fieldAccess = array();
    public $fieldDefaults = array();
    public $links = array();
    public $additionalLinks = array();
    public $createObjectList = array();
    public $tagsList = array();
    public $listProfiles = array();
    public $rights = array();
    
    function __set($name,$value) {
        global $Objects;
        if ($name=="name") {
            if ($this->old_name!="" and $name!=$this->old_name)
                $Objects->remove($this->getId());
        }
        $this->fields[$name] = $value;
        if ($name=="name") {
            if ($this->old_name!="" and $name!=$this->old_name)
                $Objects->set($this->getId(),$this);
        }
    }

    function reportError($message,$method="") {
        if (@$_POST["ajax"]==true or @$_POST["ajax"]=="true")
        {
            if ($method!="")
                echo json_encode(array("error" => $message." - ".$method));
            else
                echo json_encode(array("error" => $message));
        }
        else {           
            if ($method!="")
                echo str_replace("\n","<br>",$message)." - ".$method."<br>";
            else
                echo str_replace("\n","<br>",$message)."<br>";
        }
    }

    function getId() {
        $result = get_class($this);
        if ($this->module_id!="")
            $result .= "_".@$this->module_id;
        if ($this->name_set or $this->name!="")
            $result .= "_".trim(str_replace("\n","",@$this->old_name));            
        return $result;
    }

    function getPresentation() {
        if (isset($this->fields[$this->presentationField]))
            return $this->fields[$this->presentationField];
        else
            return get_class($this);
    }

    function __get($name) {
        if ($name == "presentation")
            return $this->getPresentation();
        if ($name == "id")
            return $this->getId();
		if ($name=="entityTableClass")
			return $this->getEntityTableClass();
        	$name_array = explode("_",$name);
        	$pres = array_pop($name_array);
        	if ($pres=="pres") {        		        	
        		$name = implode($name_array);
	        	if (isset($this->fields[$name]) and is_object($this->fields[$name]) and method_exists($this->fields[$name],"getPresentation"))
   					return $this->fields[$name]->getPresentation();
        	}
	        if (isset($this->fields[$name])) {
    	    	return $this->fields[$name];            
        }
        if ($name=="canDelete") {
        	if (count($this->getBlockingObjects())==0)
        		return true;
        	else
        		return false;
        }
        if ($name=="canDeletePresentation") {
        	if (count($this->getBlockingObjects())==0)
        		return "да";
        	else
        		return "нет";
        }
        else
            return "";
    }
    
    function getRole($user="") {
    	global $Objects;
        $app = $Objects->get("Application");        
        if (!$app->initiated)
        	$app->initModules();
    	if ($user=="")
    		$user = $app->user;
    	else {
    		$user = $Objects->get("ApacheUser_".$this->module_id."_".$user);
    		$user->load();
    	}
    	$user_role = array();
    	if (isset($user->config["roles"])) {
    		foreach ($user->config["roles"] as $role) {
	    		$classesArray = array_reverse(explode("~",$this->parentClientClasses));
	    		foreach($classesArray as $value) {
	    			if ($value=="Entity")
	    				$value = "WABEntity";
	    			if (isset($role[$value]))
	    				$user_role = mergeArrays($user_role,$role[$value]);
	    		}
	    		if (isset($role[get_class($this)]))
	    			$user_role = mergeArrays($user_role,$role[get_class($this)]);
	    		$obj_arr = explode("_",$this->getId());
	    		array_shift($obj_arr);array_shift($obj_arr);array_shift($obj_arr);
	    		$objid = get_class($this)."_".implode("_",$obj_arr);
	    		if (isset($role[$objid]))
	    			$user_role = mergeArrays($user_role,$role[$objid]);	    			
    		}
    	}
    	return $user_role;
    }

    function getSettings() {
    	global $Objects;
    	$app = $Objects->get("Application");
    	if (!$app->initiated)
    		$app->initModules();
    	$classesArray = array_reverse(explode("~",$this->parentClientClasses));
    	$settings = array();
    	$module = $app->getModuleByClass($this->module_id);
    	if (!is_array($module))
    		return 0;    	
    	foreach($classesArray as $value) {
    		if ($value=="Entity")
				$value = "WABEntity";
    		if (is_array($module)) {
    		if (isset($module["settings"]) and isset($module["settings"][$value]))
    			$settings = mergeArrays($settings,$module["settings"][$value]);
    		}
    	}
    	if (isset($module["settings"][get_class($this)]))
    		$settings = mergeArrays($settings,$module["settings"][get_class($this)]);
    	$obj_arr = explode("_",$this->getId());
    	array_shift($obj_arr);array_shift($obj_arr);array_shift($obj_arr);
    	$objid = get_class($this)."_".implode("_",$obj_arr);
    	if (isset($module["settings"][$objid]))
			$settings = mergeArrays($settings,$module["settings"][$objid]);
    	return $settings;
    }
    
    function getUserSettings() {
    	global $Objects,$userSettings;
    	$app = $Objects->get("Application");
    	if (!$app->initiated)
    		$app->initModules();
    	$classesArray = array_reverse(explode("~",$this->parentClientClasses));
    	
    	$settings = array();
    	foreach($classesArray as $value) {
    		if ($value=="Entity")
    			$value = "WABEntity";
   			if (isset($userSettings[$value]))
   				$settings = mergeArrays($settings,$userSettings[$value]);
    	}
    	if (isset($userSettings[get_class($this)]))
    		$settings = mergeArrays($settings,$userSettings[get_class($this)]);
    	$obj_arr = explode("_",$this->getId());
    	array_shift($obj_arr);array_shift($obj_arr);array_shift($obj_arr);
    	$objid = get_class($this)."_".implode("_",$obj_arr);
    	if (isset($userSettings[$objid]))
    		$settings = mergeArrays($settings,$userSettings[$objid]);
    	return $settings;
    }
    
    
    function setRole() {
    	$this->role = $this->getRole();
    }
    
    function setSettings() {
    	$this->settings = $this->getSettings();
    }

    function setUserSettings() {
    	$this->userSettings = $this->getUserSettings();
    }
    
    function getRoleValue($value,$params=array()) {
    	$matches = array();
    	if (isset($value) and !is_array($value) and preg_match("/\[(.*)\]/U",$value,$matches)) {    		
    		global $Objects;    		    		
    		$algo = $Objects->get("MetadataObjectCode_".$this->module_id."_110_".$matches[1]);
    		$params["object_id"] = $this->getId();
    		$value = str_replace("[".$matches[1]."]",$algo->exec($params),$value);
    	}     	
    	return $value;
    }
    
    function processEvent($event,$params,$sender,$receiver="") {
    	global $Objects;
    	$app = $Objects->get("Application");
    	$this->noPresent = false;
    	if (!$app->initiated)
    		$app->initModules();
    	if ($receiver=="")
    		$receiver = $app->User;
    	if ($receiver!="none")
   			$user_role = $this->getRole($receiver);
    	else {
    		$args = array("<presentation>" => $this->getPresentation(), "<object_id>" => $this->getId(),"<user>" => $sender);
    		$message = strtr(@$GLOBALS["events"][$event]["comment"],$args);
    		return $message;
    	}
   		$args = array("<presentation>" => $this->getPresentation(), "<object_id>" => $this->getId(),"<user>" => $sender);
   		if (isset($GLOBALS["events"][$event]))
   			$message = strtr(@$GLOBALS["events"][$event]["comment"],$args);
   		else
   			$message = "";
   		if (isset($user_role["events"][$event]))
   			$ev = $user_role["events"][$event];
   		else if (isset($user_role["events"]["*"]))
   			$ev = $user_role["events"]["*"];
   		else {
   			if ($message!="")
   				return $message;
   			else
   				return true;
   		}
   		if (@$ev["infoPanel"]=="false")
   			return 0;
   		if ($message!="" and (@$ev["mail"]=="true" or (@$ev["mail"]=="justMy" and $this->author==$receiver))) {
   			$recv = $Objects->get("ApacheUser_".$this->module_id."_".$receiver);
   			$sendr = $Objects->get("ApacheUser_".$this->module_id."_".$sender);
   			
   		 	$user = $recv->getLinkedDocflowObjects("","ReferenceUsers");
   		 	if (is_array($user) and count($user)>0) {
   		 		$user = $user[0];
   		 		if (is_object($user)) {
   		 			if (!$user->loaded)
   		 				$user->load();
   		 			if (!$user->defaultEmail->loaded)
   		 				$user->defaultEmail->load();
   		 			$senderUser = $sendr->getLinkedDocflowObjects("","ReferenceUsers");
   		 			if (is_array($senderUser) and count($senderUser)>0) {
   		 				$senderUser = $senderUser[0];
   		 				if ($senderUser->loaded)
   		 					$senderUser->load();
   		 				$senderMessage = "(".$senderUser->fullName.")";
   		 			} else
   		 				$senderMessage = "";
   		 			$email = $user->defaultEmail->email;
   		 			$headers = "From: informer\n";
   		 			
   		 			$headers.= "MIME-Version: 1.0\n";
   		 			$headers.= "Content-type: text/html; charset=utf-8\n";   		 			
   		 			$to = $email;
   		 			$subject = "Сообщение LVA Business Server";
   		 			mail($to,$subject,$sender.$senderMessage."-".$message,$headers);
   		 		} 
   		 	}
   		}
   		if (@$ev["infoPanel"]=="true" or (@$ev["infoPanel"]=="justMy" and $this->author==$receiver)) {
   			if ($message!="")
   				return $message;
   			else
   				return true;
   		}
    }
    
    function getAuthor() {
    	global $Objects;
    	$app = $Objects->get("Application");
    	if (!$app->initiated)
    		$app->initModules();
    	$this->user = $app->User;
    	return $this->user;
    }
    
    function setRoleArgs() {    	    	 
    	global $Objects;
    	if (count($this->role)==0)
    		$this->setRole();
    	
    	if ($this->getRoleValue(@$this->role["canEdit"])=="false") {
    		$this->readonly = "true";
    	}
    	$this->roleStr = "";
    	
    	if (is_array($this->role) and count($this->role)>0) {
    		$role = array();
    		foreach ($this->role as $key=>$value) {
    			$role[$key] = $this->getRoleValue($value);
    		}
    		$this->roleStr = json_encode(str_replace("'","``",$role));
    	}
    	
    	if (!$this->loaded) {
    		if (isset($this->role["fields"]) and is_array($this->role["fields"]) and count($this->role["fields"])>0) {
    			foreach ($this->role["fields"] as $key=>$value) {
    				$this->fields[$key] = $value;
    			}
    		}
    	}
    	 
    	$this->linksStr = json_encode(getObjectsIndexes($this->additionalLinks));
    	$this->createObjectsCount = count($this->createObjectList);
    	$fieldDefaults = array();
    	if (isset($this->role["fieldDefaults"]))
    		$fieldDefaults = $this->role["fieldDefaults"];
    	$fieldDefaults = mergeArrays($fieldDefaults,$this->fieldDefaults);
    	$this->fieldDefaultsStr = json_encode(str_replace("'","``",$fieldDefaults));
    	
    	$fieldAccess = array();
    	if (isset($this->role["fieldAccess"]))
    		$fieldAccess = $this->role["fieldAccess"];
    	$fieldAccess = mergeArrays($fieldAccess,$this->fieldAccess);
    	$this->fieldAccessStr = json_encode(str_replace("'","``",$fieldAccess));
    	
    	$persistedFields = $this->explodePersistedFields($this->persistedFields);
    	if (isset($fieldDefaults) and count($fieldDefaults)>0 and is_array($fieldDefaults) and $this->name=="") {
    		foreach($fieldDefaults as $key=>$value) {
    			if (!isset($persistedFields[$key]))
    				continue;
    			if (!isset($this->fields[$key]) or $this->fields[$key]=="") {
    				$roleValue = $this->getRoleValue($value,$persistedFields[$key]);
    				$this->fields[$key] = $roleValue;
    				if (isset($persistedFields[$key])) {
    					if ($persistedFields[$key]["type"]=="entity") {
    						$arr = explode("_",$roleValue);
    						$class = array_shift($arr);
    						$name = implode("_",$arr);
    						$this->fields[$key] = $Objects->get($class."_".$this->module_id."_".$name);
    					}
    				}
    			}
    		}
    	}    
    }

    function getArgs($full=false) {
        global $webitem_classes,$Objects;
        $this->roleStr = "";
        $this->setRoleArgs();
        if ($this->asAdminTemplate)
            $this->asAdminTemplateStr = "true";
        else
            $this->asAdminTemplateStr = "false";
        $result["{classname}"] = get_class($this);
        $result["{parent_object_id}"] = "";
        $result["{object_id}"] = $this->getId();
        $result["{icon}"] = "images/spacer.gif";
        $result["{session_name}"] = session_name();
        $result["{session_id}"] = session_id();
        
        if ($this->helpGuideId!="")
        	$this->helpButtonDisplay = "";
        else
        	$this->helpButtonDisplay = "none";
        	
        if ($this->module_id=="")
            $result["{objectid}"] = str_replace("_","",$this->getId());
        else
            $result["{objectid}"] = $this->module_id."_".str_replace("_","",str_replace($this->module_id."_","",$this->getId()));
        $result["{presentation}"] = $this->getPresentation();
        if ($this->static)
        	$this->staticStr = "true";
        else
			$this->staticStr = "false";
        foreach ($this->fields as $key=>$value) {
            if (is_scalar($value) or $value=="") {
                if (gettype($value)=="boolean") {
                    if ($value)
						$value = "true";
                    else
						$value = "false";
                }
                $result["{".$key."}"] = $value;
                $result["{|".$key."}"] = $value;
                $result["%7B".$key."%7D"] = $value;
                $result["{".$key."_first}"] = mb_substr($value, 0,1,"UTF8");
                $result["{".$key."_without_first}"] = mb_substr($value,1,mb_strlen($value)-1,"UTF8");
            }  else {
                if (is_object($value)) {
                    if (!method_exists($value,"getId"))
                        continue;
                    $result["{".$key."}"] = $value->getId();
                    if ($full) {
	                    $r = $value->getArgs();
	                    foreach ($r as $key1=>$value1) {
	                        $key1 = str_replace("{","",str_replace("}","",$key1));
	                        $result["{".$key.".".$key1."}"] = $value1;
	                    }
                    }
                 } 
              
              if ($full and is_array($value) and $key!="persistedFields") {
                    for ($c=0;$c<count($value);$c++) {
                        if (is_object(current($value))) {
                            $result["{".$key."[$c]}"] = current($value)->getId();
                            $r = current($value)->getArgs();
                            foreach ($r as $key1=>$value1) {
                                $key1 = str_replace("{","",str_replace("}","",$key1));
                                $result["{".$key."[$c].".$key1."}"] = $value1;
                            }
                        } else {
                            $result["{".$key."[$c]}"] = current($value);
                        }
                        next($value);
                    }
                }
            } 
        }
        if (is_array($webitem_classes))
            $result["{webitem_classes}"] = implode(",",$webitem_classes);
        else
            $result["{webitem_classes}"] = "";
        if ($this->persistedFields!="") {
        	if (!is_array($this->persistedFields))        	
            	$persistedArray = $this->getPersistedArray();
        	else {
        		$persistedArray = $this->explodePersistedFields($this->persistedFields);
        	}
        	foreach($persistedArray as $key=>$value) {
        		$attrs_array = array();
            	if (!is_array($this->persistedFields))
                	$value_parts = explode("|",$value);
            	else {
            		if (@$value["params"]!="")
            			$value_parts = array($value["type"],$value["params"]);
            		else
            			$value_parts = array($value["type"]);
            	}
                if (isset($value_parts[1]) and $value_parts[1]!="") {
                	if (!is_array($this->persistedFields))
                    	$attrs_array = $this->getClientInputControlAttrsArray($value_parts[1]);
                	else
                		$attrs_array = $value["params"];
                    if (!isset($attrs_array["type"])) {
                        $attrs_array["type"] = $value_parts[0];
                        $type = $value_parts[0];
                    } else
                        $type = $attrs_array["type"];
                } else if (@$value_parts[0]!="" and isset($value_parts[0])) {
                    $attrs_array["type"] = $value_parts[0];
                    $type = $value_parts[0];
                } else {
                    if (isset($this->fields[$key])) {
                        $type = gettype($this->fields[$key]);
                        if ($type=="object")
                            $type = "entity";
                        if ($type=="double")
                            $type = "decimal";
                    } else {
                        $type = "string";
                        $this->fields[$key] = '';
                    }
                    $attrs_array["type"] = $type;
                }
                if (!isset($this->fields[$key]))
                    $this->fields[$key] = '';
                $result["{".$key."_properties"."}"] = json_encode($attrs_array);
                $result["{".$key."_title}"] = $key;
                $result["{".$key."_name}"] = $key;
                if (isset($attrs_array)) {
                    foreach ($attrs_array as $key1=>$value1)
                        $result["{".$key."_".$key1."}"] = $attrs_array[$key1];
                }
                if (!isset($this->fields[$key]))
                    $this->fields[$key] = "";
                $result["{".$key."_type}"] = $type;
                if (is_object($this->fields[$key])) {
                    if (method_exists($this->fields[$key], "getId")) {
                        $result["{".$key."_fieldvalue}"] = $this->fields[$key]->getId();
                        $this->fields[$key]->loaded = true;
                        $result["{".$key."_presentation}"] = $this->fields[$key]->getPresentation();
                    }
                } else {
                        $result["{".$key."_fieldvalue}"] = $this->fields[$key];
                }
                if ($type=="date") { 
                    if ($this->fields[$key]>9999999999) {
                        $result["{".$key."_full_presentation}"] = @date("d.m.Y H:i:s",$this->fields[$key]/1000);
                        $result["{".$key."_presentation}"] = @date("d.m.Y",$this->fields[$key]/1000);
                    } else {
                        settype($this->fields[$key],"integer");
                        $result["{".$key."_full_presentation}"] = @date("d.m.Y H:i:s",$this->fields[$key]);
                        $result["{".$key."_presentation}"] = @date("d.m.Y",$this->fields[$key]);                        
                    }
                }
                    
            }
            if ($this->name == "")
                $result["{pname}"] = 0;
            else
                $result["{pname}"] = $this->name;            
        }
        
        if (!isset($result["{opener_item}"]))
        	$result["{opener_item}"] = $this->opener_item;
        
        if (!isset($result["{window_id}"]))
        	$result["{window_id}"] = @$_GET["window_id"];        
        
        $result["{className}"] = get_class($this);
        $persistedFields = $this->persistedFields;
        if (isset($persistedFields) and !is_array($persistedFields)) {
        	$persistedFields = explode("\n",$this->persistedFields);
	        $result["{persistedFieldsSafe}"] = implode("#",$persistedFields);
        	$childPersistedFields = explode("\n",$this->childPersistedFields);
        	$result["{childPersistedFieldsSafe}"] = implode("#",$childPersistedFields);
        } else {        	
        	$result["{persistedFieldsSafe}"] = $this->getSafePersistedFields($this->persistedFields);
        	$result["{childPersistedFieldsSafe}"] = $this->getSafePersistedFields($this->childPersistedFields);        	 
        }
        if ($this->hasChildren)
            $result["{hasChildrenStr}"] = "true";
        else
            $result["{hasChildrenStr}"] = "false";
        foreach ($result as $key=>$value)
            $result[str_replace("{","[",str_replace("}","]",$key))] = $value;
        return $result;
    }
    
    function getClientInputControlAttrsArray($str) {
        $arr = explode("~",$str);
        $result = array();
        foreach($arr as $elem) {
            $elem_parts = explode("=",$elem);
            $result[trim($elem_parts[0])] = trim(@$elem_parts[1]);
        }
        return $result;
    }
    
    function getClientInputControlStr($arr) {
        $result = array();
        foreach ($arr as $key=>$value)
            $result[] = $key."=".$value;
        return implode("~",$result);
    }
    
    function getClientInputControlJSON($arr) {
    	return json_encode($arr);
    }
    
    /**
     * @param массив $persistedFields
     * @return string
     * 
     * Возвращает массив переданных хранимых полей
     * в виде строки, в которой каждое поле отделено
     * друг от друга символом возврата каретки
     */
    function getPersistedFieldsString($persistedFields="") {
    	$result = array();
    	$persistedFIelds = $this->explodePersistedFields($persistedFields);
    	if (!is_array($persistedFields))
    		return "";
    	foreach ($persistedFields as $key=>$value) {
    		if (isset($value["params"]) and isset($value["type"])) {
    			$result[] = $key."|".$value["type"]."|".$this->getClientInputControlStr($value["params"]);
    		}
    	}
    	return implode("\n",$result);
    }
    
    /**
     * @param массив $persistedFields
     * @return string
     * 
     * Возвращает массив переданных хранимых полей
     * в виде строки, в которой каждое поле отделено
     * друг от друга символом #, что безопасно для включения
     * такой строки в HTML-код
     */
    function getSafePersistedFields($persistedFields) {
    	if (is_array($persistedFields))
    		return implode("#",explode("\n",$this->getPersistedFieldsString($persistedFields)));
    	else {
    		return str_replace("\n","#",$persistedFields);    		
    	}
    }    
    
    /**
     * @param массив $persistedFields
     * 
     * Функция добавляет в массив хранимых полей
     * все поля, которые определены в наборе полей
     * fieldSet данной сущности (если определены),
     * а также для каждого поля добавляет все
     * параметры, унаследованные от базового поля,
     * наименование которого указывается в свойстве
     * поля "base"
     * 
     * Все базовые поля и их параметры хранятся
     * в глобальном массиве $fields, а все базовые
     * наборы полей хранятся в глоабльном массиве $fieldSets.
     * 
     * Элемент массива $fieldSets имеет форму:
     * <имя-набора-полей> => <описание-поля-в-наборе>
     * описание-поля-в-наборе:
     * <имя-поля> => <название-базового-поля>
     * 
     * Названия базовых полей должны быть ключами массива $fields
     */
    function explodePersistedFields($persistedFields) {
    	$result = array();
    	global $fields,$models;
    	foreach($this->models as $model) {
	    	if (isset($models[$model])) {
	    		$fieldSet = $models[$model]; 		
	    		foreach($fieldSet as $key=>$value) {
	    			if ($key=="file" or $key=="groups" or $key=="metaTitle" or $key=="collection")
	    				continue;
	    			if (!isset($persistedFields[$key])) {
	    				if (isset($fields[$value])) {
	    					$persistedFields[$key] = array();
	    					if (!isset($fields[$value]["base"]))
	    						$persistedFields[$key]["base"] = $value;
	    					else
	    						$persistedFields[$key]["base"] = $fields[$value]["base"];
	    					if (!isset($fields[$value]["params"]))
	    						$persistedFields[$key]["params"] = array();
	    					else
	    						$persistedFields[$key]["params"] = $fields[$value]["params"];
	    					$persistedFields[$key]["type"] = @$fields[$value]["type"];
	    				}
	    			} else {
	    				if (is_array($persistedFields) and isset($persistedFields[$key]) and isset($fields[$value]) and !isset($persistedFields[$key]["type"])) {	    				
    						$persistedFields[$key]["type"] = @$fields[$value]["type"];
	    				}	    				 
	    			}
	    		}
	    	}
    	}
    	if (!is_array($persistedFields))
    		return $result; 
    	foreach ($persistedFields as $key => $value) {
    		$result[$key] = $this->explodeField($value); 
    	}
    	return $result;
    }
    
    function explodeField($field) {
    	global $fields;
    	$result = array();
    	if (isset($field["base"]) and isset($fields[$field["base"]]))
    		$result = $this->explodeField($fields[$field["base"]]);
    	if (isset($field["type"]))
    		$result["type"] = $field["type"];
    	if (isset($field["file"]))
    		$result["file"] = $field["file"];
    	if (isset($field["params"]))
    		foreach ($field["params"] as $key1=>$value1)
    			$result["params"][$key1] = $value1;
    	return $result;
    }

    function getPersistedArray() {
      	$result = array();
    	if (!is_array($this->persistedFields)) {
        	$arr = explode("\n",$this->persistedFields);
        	foreach ($arr as $str) {
            	$parts = explode("|",$str);
            	$name = array_shift($parts);
            	$value = implode("|",$parts);
            	$result[$name] = $value;
        	}
    	} else {
    		$persistedFields = $this->explodePersistedFields($this->persistedFields);    		
    		foreach ($persistedFields as $key => $value) {
    			if (!isset($value["type"]))
    				if (isset($value["params"]["type"]))
    					$value["type"] = $value["params"]["type"];    			
    			$result[$key] = @$value["type"]."|".$this->getClientInputControlStr($value["params"]);
    		}
    	}
        return $result;
    }
    
    function getTableClassHook() {
    	echo $this->entityDataTableClass;
    }

    function getOldPersistedArray() {
    	if (!is_array($this->persistedFields)) {
	        $arr = explode("\n",$this->persistedFields);
    	    $result = array();
	        foreach ($arr as $str) {
    	        $parts = explode("|",$str);
        	    if (isset($parts[3]))
            	    $result[$parts[3]] = $parts[0];
        	}
    	} else {
    		$persistedFields = $this->explodePersistedFields($this->persistedFields);    		
    		foreach ($persistedFields as $key=>$value) {
    			if (isset($value["oldName"]))
    				$result["oldName"] = $key;
    		}
    	}
        return $result;
    }

    function getChildArgs() {
        $args = $this->getArgs();
        $result = array();
        foreach ($args as $key=>$value) {
            $key = str_replace("{","",str_replace("}","",$key));
            $result["{".$key."_child}"] = $value;
            $result["%7B".$key."_child%7D"] = $value;
        }
        $result = $result + $this->getParentArgs();
        return $result;
    }

    function getParentArgs() {
        global $Objects;
        if (isset($this->parent) and ($this->parent!="")){
			if ($this->module_id!="")
				$o = $Objects->get(@$this->parent->class."_".$this->module_id."_".$this->site->name."_".$this->parent->id);
			else
				$o = $Objects->get(@$this->parent->class."_".$this->site->name."_".$this->parent->id);
            if (!$o->loaded)
                $o->load();
            $args = $o->getArgs();
        }
        $result = array();
        if (isset($args)) {
            foreach ($args as $key => $value) {
                $key = str_replace("{","",str_replace("}","",$key));
                $result["{".$key."_parent}"] = $value;
            }
        }
        return $result;
    }

    function parseTemplate($template_file,$handler_file="",$css="",$instance="",$out=true) {
    	$args  = $this->getArgs();
    	$tpl = new Template($template_file);
        $tpl->args = $args;
        $tpl->object = $this;
        $tpl->class = @$this->class;
        $tpl->handler = $handler_file;
        if ($this->instance!="")
            $tpl->instance = $this->instance;
        $tpl->css = $css;        
        return $tpl->parse($out);
    }

    function __destruct() {
        unset($fields);
    }

    function show($instance="",$out=true) {
		if (is_array($instance)) {
			$this->setArguments($instance);
			if (isset($instance["instance"]))
				$instance = $instance["instance"];
		}				
        return $this->parseTemplate($this->template, $this->handler, $this->css,$instance,$out);
    }

    function construct($params) {
    	global $models;
        if (count($params)>2)
            $this->module_id = array_shift($params)."_".array_shift($params);
        else
            $this->module_id = "";
        if (count($params)>0)
            $this->name_set = true;
        $this->name = implode("_",$params);
        $this->old_name = $this->name;
        $this->clientObjectId = $this->getId();
        global $Objects,$dataAdapterClass;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        $this->skinPath = $app->skinPath;
        $this->old_name = $this->name;
        $this->template = "templates/core/WABEntity.html";
        $this->css = $app->skinPath."styles/Mailbox.css";
        $this->handler = "scripts/handlers/core/WABEntity.js";
        $this->loaded = false;
        if (is_object($this->adapter))
        	$this->adapter->entity = $this;
        $this->window_id = "";
        $this->opener_item = "";
        $this->opener_object = "";
        $this->childClass = get_class($this);
        $this->width = "720";
        $this->height = "500";
        $this->serverName = $_SERVER["SERVER_NAME"];
        $this->overrided = "width,height,persistedFields";
        $this->asAdminTemplate = false;
		$this->appUser = $app->User;
		$this->user = $this->appUser;
		$this->helpGuideId = "";
		if ($this->name=="")
			$this->createDisplayStyle = 'none';
		else
			$this->createDisplayStyle = "";
		
		$this->readonly = "false";
		                       
		if (isset($models[get_class($this)]))
			$this->models[] = get_class($this);
		
        if ($this->module_id!="") {
            $this->tabset_id = "WebItemTabset_".$this->module_id."_".str_replace("_","",$this->getId())."Tabset";
            $this->tabsetName = $this->tabset_id;
        }        
        else {
            $this->tabset_id = "WebItemTabset_".str_replace("_","",$this->getId())."Tabset";
            $this->tabsetName = str_replace("_","",$this->getId())."Tabset";
        }
        
        $this->tabs_string = "fieldValues|Данные|".$this->skinPath."images/spacer.gif;";
        $this->tabs_string.= "persistedFieldsTable|Структура|".$this->skinPath."images/spacer.gif";
        if ($this->name!="") {
            $this->tabs_string.= ";childrenTable|Элементы|".$this->skinPath."images/spacer.gif";
            $this->active_tab = "childrenTable";
        } else 
          $this->active_tab = "fieldValues";

        $this->loaded = false;        
        $this->tagsTable = "";
        $this->entityDataTableClass = "EntityDataTable";
        $this->clientClass = "Entity";
        $this->parentClientClasses = "";
        $this->profileClass = "EntityProfile";
        $this->classTitle = "Сущность";
        $this->classListTitle = "Сущности";
        $this->classType = "Объект";
        $this->icon = $this->skinPath."images/Tree/module.png";
        $this->receiveBarCodes = "0";
        $this->rights = array("#all_users" => "2");
        
        $rolesList = array();
        foreach ($GLOBALS["roles"] as $key=>$value) {
        	$rolesList[] = $value["title"];
        } 
        $this->rolesString = implode("~",array_keys($GLOBALS["roles"]))."|".implode("~",$rolesList);
        $this->setSettings();
        $this->setUserSettings();
    }     

    function getItems($condition="",$sort="",$from=0,$adapter="") {
        global $Objects;
        $objs = $Objects->query(get_class($this),$condition,$adapter,$sort);
        $result = array();
        $c=0;
        foreach ($objs as $obj) {
            if ($obj->name!="") {
                if ($c<$from) {
                    $c++;
                    continue;
                }
                $result[count($result)] = $obj;
            }
        }
        return $result;
    }
    
    function childrenCount() {
        if ($this->isGroup)
            return true;
        if ($this->adapter!="") {
            if (!$this->adapter->isPDO) {
                $query = "SELECT COUNT(e.id) FROM EntityField e WHERE e.value_id=".$this->name;
                $em = $this->adapter->connect(true);
                if (method_exists($em,"prepare")) {
                    $stmt = $em->prepare($query);
                    $stmt->execute();
                    if ($row = $stmt->fetch())
                        return $row[0];
                }
            } else {
                $name = array_pop(explode("_",$this->name));
                $query = "SELECT count(id) as child_count FROM fields WHERE type='entity' AND name='parent' AND value='".get_class($this)."_".$name."'";
                if (!$this->adapter->connected)
                    $this->adapter->connect();
                if ($this->adapter->connected) {
                	$stmt = $this->adapter->dbh->prepare($query);
                	$stmt->execute();
                	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                	return $result[0]["child_count"];
                }                
            }
        }
        return 0;
    }
    
    function load($em="",$dbEntity="",$force=false) { 
		$object = $this;
		global $Objects;
		if (count($this->role)==0)
			$this->setRole();
		if (isset($this->role["active_tab"]))
			$this->active_tab = $this->role["active_tab"];
		if (isset($this->role["fields"]) and is_array($this->role["fields"]) and count($this->role["fields"])>0) {
			foreach ($this->role["fields"] as $key=>$value) {
				$this->fields[$key] = $value;
			}
		}
		$this->setUserSettings();
		if (isset($this->userSettings["fields"]) and is_array($this->userSettings["fields"]) and count($this->userSettings["fields"])>0) {
			foreach ($this->userSettings["fields"] as $key=>$value) {
				$this->fields[$key] = $value;
			}
		}
				
		$app = $Objects->get("Application");
		if (!$app->initiated)
			$app->initModules();
		
        if (file_exists("/var/WAB2/users/".$app->User."/settings/".$this->getId())) {
			eval(file_get_contents("/var/WAB2/users/".$app->User."/settings/".$this->getId()));
		}
		if ($this->defaultListProfile != "") {
			if (isset($this->listProfiles[$this->defaultListProfile])) {
				foreach ($this->listProfiles[$this->defaultListProfile] as $key=>$value)
					$this->fields[$key] = $value;
			}
		}
        if ($this->ownerObject!="") {
        	$this->ownerObject = $Objects->get($this->ownerObject);
        }
        if (is_object($this->ownerObject) and method_exists($this->ownerObject,"getId")) {
        	if ($this->topLinkObject=="")
        		$this->topLinkObject = $this->ownerObject;
        }
        $ownerObject = $this->ownerObject;
		if ($this->topLinkObject!="" and !is_object($this->topLinkObject))
           	$this->topLinkObject = $Objects->get($this->topLinkObject);
        if (is_object($this->topLinkObject) and $this->topLinkObject->name!="")
          	$this->additionalLinks[$this->topLinkObject->name] = $this->topLinkObject->getId();
        if (is_object($this->ownerObject) and $this->ownerObject->name!="")
        	$this->additionalLinks[$this->ownerObject->name] = $this->ownerObject->getId();        	 
        $this->links = $this->additionalLinks;
        if (!$this->loaded or $force) {                
        	if ($this->adapter!= "" and $this->adapter!=null and is_object($this->adapter)) {				
            	$this->adapter->entity = $this;
            	if ($this->name!="") {
            		$this->loaded = $this->adapter->load($em,$dbEntity);
            		if ($this->loaded and !is_array(@$this->adapter->entity->fields["persistedFields"]) and isset($this->adapter->entity->fields["persistedFields"]) and trim($this->adapter->entity->fields["persistedFields"])!="")
            			$this->persistedFields = @$this->adapter->entity->fields["persistedFields"];
                    $links = $this->getLinks();
                    $this->links = mergeArrays($this->links,$links);
                }
        	    $this->ownerObject = $ownerObject;
                if (!$this->loaded and is_object($this->ownerObject)) {
              		$this->ownerObject->load();
                  	$this->createByOwnerObject();                    	
                }
        	}   
            $this->hasChildren = false;
            global $skipChildrenCheck;
            if ($this->persistedFields=="")
            	$this->persistedFields = $this->explodePersistedFields($this->persistedFields);
            $persistedArray = $this->getPersistedArray();
            foreach ($persistedArray as $key=>$value)
                if (!isset($this->fields[$key]))
                    $this->fields[$key] = "";
            if (isset($persistedArray["isGroup"]))
	            $this->hasChildren = $this->isGroup;
            else {
            	if (isset($persistedArray["parent"])) {                
                	if ($skipChildrenCheck!=true) {                    
                    	$this->hasChildren = $this->adapter->hasChildren();                    
                	}
            	}
            }
        }
        if ($this->icon=="") {
            $this->icon = $this->treeIcon;
        } 
        if ($this->old_id=="")
        	$this->old_id = $this->getId();
        if ($this->isGroup) {
        	$this->template = "templates/docflow/core/ReferenceGroup.html";
        	$this->width = "350";
        	$this->height = "150";        
        }
        $this->setRoleArgs();        
        $this->afterLoad();
    }

    function checkData() {
        return true;
    }

    function changeKeyBrackets($arr,$l,$r) {
        $result = array();
        foreach ($arr as $key=>$value) {
            $result[str_replace("{",$l,str_replace("}",$r,$key))] = $value;
        }
        return $result;
    }
    
    function beforeSave() {
    	
    }
    
    function afterLoad() {
    	
    }
    
    function save($not_echo=false) {
        global $Objects;
        $this->beforeSave();
        $app = $Objects->get("Application");
        
        if (!$app->initiated)
        	$app->initModules();
        
        if (count($this->role)==0)
        	$this->setRole();
          $error = false;
         if ($this->getRoleValue($this->role["canEdit"])=="false") {
         	$this->reportError("Недостаточно прав доступа для записи '".$this->getPresentation()."'","save");
         	$error = true;
         };
        $old_id = $this->getId();
        if ($error) {
         	if ((@$_POST["action"]=="save" || @$_POST["action"]=="submit") and isset($_POST["ajax"])) {
         		if ($not_echo!=true) {
         			$echoed=true;
         			echo "<script>window.parent.objects.objects['".$old_id."'].afterSave();</script>";         			
         		}
         	}
         	return 0;
        }
        $arr = array();
        if (!$this->loaded)
            $this->load();
        if ($this->module_id!="")
            $id = str_replace($this->module_id."_","",$this->getId());
        foreach ($_POST as $key=>$value) {
        	if ($key==$id."_links" or $key==$this->getId()."_links") {    		
        		$this->links = $_POST[$key];
        	}
            if (preg_match("/^".$this->getId()."/",$key)) {               
                $arr[str_replace($this->getId()."_","",$key)] = $value;
            }
            if (preg_match("/^".$id."/",$key)) {               
                $arr[str_replace($id."_","",$key)] = $value;
            }
        }   
        $this->tagsList = array();
        if (isset($arr["tagsTable"])) {
        	$tagRows = explode("|",$arr["tagsTable"]);
        	foreach ($tagRows as $tagRow) {
        		$tagParts = explode("~",$tagRow);
        		$this->tagsList[$tagParts[0]] = $tagParts[0];
        		$this->fields[$tagParts[0]] = $tagParts[1];
        		unset($arr[$tagParts[0]]);
        	}
        	$this->tags = implode("~",$this->tagsList);
        } else { 
        	$tags = explode("~",$this->tags);
        	foreach ($tags as $tg)
        		$this->tagsList[$tg] = $tg;
        }
        if (isset($arr["usersRightsTable"]) or isset($arr["rolesRightsTable"]))
        	$this->rights = array();
        if (isset($arr["usersRightsTable"])) {
        	$rightsRows = explode("|",$arr["usersRightsTable"]);
        	foreach ($rightsRows as $rightRow) {
        		$rightParts = explode("~",$rightRow);
        		$this->rights[$rightParts[0]] = $rightParts[1];
        	}        	
        }
        if (isset($arr["rolesRightsTable"])) {
        	$rightsRows = explode("|",$arr["rolesRightsTable"]);
        	foreach ($rightsRows as $rightRow) {
        		$rightParts = explode("~",$rightRow);
        		$this->rights[$rightParts[0]] = $rightParts[1];
        	}        	
        }
        foreach ($arr as $key=>$value) {
        	if ($key=="tags")
        		continue;
        	if ($key=="tagsTable")
        		continue;
        	if ($key=="usersRightsTable")
        		continue;
        	if ($key=="rolesRightsTable")
        		continue;
        	if ($key=="persistedFields" and !is_array($value)) {
                if (str_replace("#","\n",$value)==$this->defaultPersistedFields) {
                    unset($this->fields["persistedFields"]);
                    continue;
                }
            }
            if ($key=="childPersistedFields") {
                if (str_replace("#","\n",$value)==$this->defaultChildPersistedFields) {
                    unset($this->fields["childPersistedFields"]);
                    continue;
                }
            }                                                
            $fAccess = array();
            $canWrite = true;
           	if (isset($this->role["fieldAccess"]))
           		$fAccess = $this->role["fieldAccess"];
           	$fAccess = mergeArrays($fAccess,$this->fieldAccess);
            
           	if (isset($fAccess[$key]))
            	$fieldAccess = $fAccess[$key];
            else
            	if (isset($fAccess["*"]))
            		$fieldAccess = $fAccess["*"];           	

            if (isset($fieldAccess) and ($canWrite or isset($this->tagsList[$key]))) {
            	$this->fields[$key] = $value;
            }
            unset($fieldAccess);
        }
        if (isset($this->fields["persistedFields"]) and !is_array($this->persistedFields))
            $this->persistedFields = str_replace("#","\n",$this->fields["persistedFields"]);
        if (isset($this->fields["childPersistedFields"]) and !is_array($this->childPersistedFields))
            $this->childPersistedFields = str_replace("#","\n",$this->fields["childPersistedFields"]);
        $echoed=false;
        if ($this->class != "" and $this->class != get_class($this)) {
            $clsarr = explode("_",$this->getId());
            array_shift($clsarr);
            $obj = $Objects->get($this->class."_".implode("_",$clsarr));
            foreach ($this->fields as $key=>$value) {
                if ($key=="persistedFields" and !is_array($value)) {
                    if (str_replace("#","\n",$value)==$this->defaultPersistedFields) {
                        unset($this->fields["persistedFields"]);
                        continue;
                    }
                }
                if ($key=="childPersistedFields") {
                    if (str_replace("#","\n",$value)==$this->defaultChildPersistedFields) {
                        unset($this->fields["childPersistedFields"]);
                        continue;
                    }
                }            
	            if (isset($fAccess[$key]))
	            	$fieldAccess = $fAccess[$key];
	            else
	            	if (isset($fAccess["*"]))
	            		$fieldAccess = $fAccess["*"];
	           	
	            if (isset($fieldAccess) and $fieldAccess!="write" and $fieldAccess!="onlyMy")
					$canWrite = false;
	
	            if ($canWrite or $key=="tags" or isset($this->tagsList[$key])) {	            	
	                $obj->fields[$key] = $value;
	            }
	            unset($fieldAccess);
            }
            if (is_object($obj->adapter))
                $obj->adapter->entity = $obj;
            $Objects->remove($this->getId());
            $id = $obj->save($not_echo);
            if ((@$_POST["action"]=="save" || @$_POST["action"]=="submit") and isset($_POST["ajax"])) {
                if ($not_echo!=true) {
                    $echoed=true;
                    echo "<script>window.parent.objects.objects['".$old_id."'].afterSave();</script>";
                }
            }
            return 0;
        } 
        if ($this->checkData()) {
            if ($this->adapter!= "") {
                $this->adapter->entity = $this;
                if (!is_array($this->links))
                	$this->links = json_decode($this->links);
                if (is_object($this->links))
                	$this->links = (array)$this->links;
                $links = $this->links;

                $this->loaded = $this->adapter->save();

                $to_save = false;
                if ($this->name=="") {
                	$this->name = $this->loaded;
                	$to_save = true;
                }
                if ($to_save)
                	$this->adapter->save();
                if (is_array($links)) {
                	$this->setLinks(getObjectsIndexes($links));
                	$this->links = $links;
                }
                if (@$_POST["action"]=="save") {
                    if ($not_echo!=true) {
                        if (!$echoed) {
                            echo $this->loaded;
                        }
                    }
                }
            }
            $this->afterSave();
        }
        if ($not_echo!=true and isset($_POST["ajax"])) {
            echo "<script>if (window.parent.objects.objects['".$old_id."']!=null) window.parent.objects.objects['".$old_id."'].afterSave();</script>";
        }                
        return $this->loaded;            
    }
    
    function afterSave($out=true) {
        return true;
    }
    
    function afterRemove() {
        return true;
    }

    function getGUID() {
        if ($this->adapter!="")
            if (!$this->adapter->isPDO) {
                if ($this->adapter->dbEntity!="") {
                    return $this->adapter->dbEntity->getId();
                }
            } else {
                $id = str_replace(get_class($this)."_","",$this->getId());
                if ($this->module_id!="")
                    $id = str_replace($this->module_id."_","",$id);
                return $id;                
            }
    }

    function unsetField($name) {
        unset($this->fields[$name]);
    }
    
    function getEntityLinks($name = "") {
        if (!$this->adapter->isPDO)
            return $this->adapter->getEntityLinks(null,$name);
        else
            return $this->adapter->getEntityLinks($name);
    }
    
    function getBlockingObjects() {
   		return $this->adapter->getBlockingObjects();
    }    

    function remove($recursive=false,$recursive_field="parent") {
        global $Objects;
        if (!$this->loaded)
            $this->load();
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        $used_by = $this->usedBy();
        if (is_object($used_by) and $used_by->name!=$app->User) {
            $this->reportError("Этот объект используется пользователем {$used_by->name}. Его удалить нельзя.","remove");
            return 0;
        }
        if (!$this->canDelete)
        	return 0;        
        if ($this->adapter!="") {
            $this->adapter->entity=$this;
            $results = $this->adapter->remove();
            if (is_array($results) and count($results)>0) {
                $str = "";
                foreach($results as $res) {
                    $str .= $res["entity"]->getPresentation().", поле ".$res["field"]."\n";
                }
                $this->reportError("Этот объект нельзя удалить, так как он используется другими объектами :\n".$str,"remove");
                return 0;                
            }
        }
        $users = $Objects->get("ApacheUsers_".$this->module_id."_users");
        $users->load();
        foreach($users->apacheUsers as $user) {
        	if (file_exists("/etc/WAB2/config/".$user->name."_settings.php")) {
        		include "/etc/WAB2/config/".$user->name."_settings.php";
        		if (isset($userSettings) and isset($userSettings[$this->getId()])) {
        			unset($userSettings[$this->getId()]);
	        		file_put_contents("/etc/WAB2/config/".$user->name."_settings.php","<?php\n\$userSettings =\n".getArrayCode($userSettings).";\n?>");
        		}
        	}
        }        
        $this->afterRemove();        
        $Objects->remove($this->getId());
        return 0;
    }
    
    function getRights($user="") {
    	global $Objects;
    	$app = $Objects->get("Application");
    	if (!$app->initiated)
    		$app->initModules();
    	if ($user=="")
    		$user = $app->User;
    	if (!$this->loaded)
    		$this->load();
    	if (isset($this->rights[$user])) {
    		return $this->rights[$user];
    	}
    	if ($user!="default" and isset($this->rights["#auth_users"])) {
    		return $this->rights["#auth_users"];
    	}
    	if (isset($this->rights["#all_users"]))
    		return $this->rights["#all_users"];
		
    	$found = false;
    	foreach ($app->user->config["roles"] as $key=>$value) {
    		if (isset($this->rights["@".$key])) {
    			if ($this->rights["@".$key]=="2")
    				return $this->rights["@".$key];
    			else
    				$found = $this->rights["@".$key];
    		}    			
    	}
    	if ($found)
    		return $found;
    	
    	$rule = false;
    	if ($this->parent!="") {
    		if (!is_object($this->parent))
    			$this->parent = $Objects->get($this->parent);
    		if (!$this->parent->loaded)
    			$this->parent->load();
    		$parent = $this->parent;  
			while ($parent!=null) {
				$obj = $parent;
				if (isset($obj->rights[$user])) {
					return $obj->rights[$user];
				}
				if ($user!="default" and isset($obj->rights["#auth_users"])) {
					return $obj->rights["#auth_users"];
				}
				if (isset($obj->rights["#all_users"]))
					return $obj->rights["#all_users"];
				
				$found = false;
				if (isset($app->users->config["roles"]))
					foreach ($app->users->config["roles"] as $key=>$value) {
						if (isset($obj->rights["@".$key])) {
							if ($obj->rights["@".$key]=="2")
								return $obj->rights["@".$key];
							else
								$found = $obj->rights["@".$key];
						}
					}
				if ($found)
					return $found;
				if ($obj->parent!="") {
					if (!is_object($obj->parent))
						$obj->parent = $Objects->get($obj->parent);
					if (!$obj->parent->loaded)
						$obj->parent->load();
					$parent = $obj->parent;			
				} else 
					$parent = null;	
			}    		
    	}
    	return 0;
    }   
    
    function copyFrom($entityId) {
        global $Objects;
        if (is_array($entityId)) {
        	if (isset($entityId["item"])) {
				$entityId = @$entityId["item"];
        	} else
        		$entityId = "";
        }
        if ($entityId=="")
        	return 0;
        $this->asAdminTemplate = true;
        $entity = $Objects->get($entityId);
        if (is_object($entity)) {
	        $entity->load();
	        if ($entity!=null) {
	            $this->persistedFields = $entity->persistedFields;
	            $parr = $this->getPersistedArray($this->persistedFields);
	            foreach ($parr as $key=>$value) {                
	                if ($key!="name" && $key!="sortOrder" && $key!="old_name" && $key!="old_title" && $key!="old_id" && $key!="sysname") {
	                    $this->fields[$key] = @$entity->fields[$key];
	                }
	            }
	            $this->tagsTableEntity = $entityId;
	        }
        }
        $this->afterCopyFrom();
    }
    
    function afterCopyFrom() {
    	
    }
    
    function usedBy() {
        global $Objects;
        $users = $Objects->get("ApacheUsers");
        if (!$users->loaded)
            $users->load();
        foreach ($users->apacheUsers as $user) {
            $objs = $user->getOpenedObjects();
            if (isset($objs[$this->getId()])) 
                    return $user;
        }
        return 0;
    }
    
    function afterInit($parent_id) {
        global $Objects;       
        if (is_object($parent_id)) {
        	$this->setArguments($parent_id);
        	$parent_id = (array)$parent_id;
        }         
        if (is_array($parent_id)) {
        	$parent_id = $parent_id["item"];
        }
        if (isset($parent_id)) {
        	$object = $this;
        	if ($this->getId()!=$parent_id) {
        		$object->parent=$Objects->get($parent_id);        	        	
        		if (is_object($object->parent)) {       		        
	        		$object->parent->load();
	        		if ($object->parent->childPersistedFields!="") {
    	    			$object->persistedFields=$object->parent->childPersistedFields;
	        		}
        		}
        	}
        }
    }
    
    function getEntityImage() {
		if ($this->deleted)
			return  'utils/docflow/entimage.php?path='.$this->icon.'&style=deleted';
		if ($this->registered)
			return  'utils/docflow/entimage.php?path='.$this->icon.'&style=registered';
   		return $this->icon;
	}
	
	function getEntityGroupImage() {
		if ($this->deleted)
			return  'utils/docflow/entimage.php?path='.$this->skinPath."images/Buttons/entityGroupImage.png".'&style=deleted';
		return $this->skinPath."images/Buttons/entityGroupImage.png";
	}
	
	function getHookProc($number) {
		switch ($number) {
			case "1": return "showEntityInWindow";
			case 'vchanged': return "vchanged";
			case 'get_id': return 'get_id';
			case 'admTpl': return 'admTpl';
			case 'childClass': return 'childClassHook';
			case 'afterInit': return 'afterInit';
			case "2": return "copyFrom";
			case "copyFrom": return "copyFrom";
			case 'move': return "moveHook";
			case "removelist": return "removeListHook";
			case "show": return "showHook";
			case "setParams": return "setParamsHook";
			case "getPresentation": return "getPresentationHook";
			case 'getParent': return "getParentHook";
			case "remove": return "removeHook";
			case "save": return "saveHook";
			case "getFields": return "getFieldsHook";
			case "setLinks": return "setLinksHook";
			case "getProfileClass": return "getProfileClassHook";
			case "getTableClass": return "getTableClassHook";
			case "getListString": return "getListStringHook";
		}
		return 0;
	}
	
	function getListStringHook($arguments) {
		global $Objects;
		$adapter = $Objects->get("DocFlowDataAdapter_".$this->module_id."_".get_class($this));
		$res = PDODataAdapter::makeQuery("SELECT entities FROM fields WHERE @name!=0 AND @classname='".get_class($this)."'", $adapter,$this->module_id);
		$arr = array();
		foreach ($res as $str) {
			$arr[$str->getId()] = $str->getPresentation(false);
		}
		if (count($arr)>0) {
			echo implode("~",array_keys($arr))."|".implode("~",array_values($arr));		
		}
	}
	
	function getProfileClassHook() {
		echo $this->profileClass;
	}
	
	function getFieldsHook($arguments) {
		$arr = (array)$arguments["fields"];
		if (!$this->loaded)
			$this->load();
		$result = array();
		foreach($arr as $value) {
			$result[$value] = @$this->fields[$value];
		}
		echo json_encode($result);
	}
	
	function saveHook($arguments) {
		@$this->save();
	}
	
	function removeHook($arguments) {
		$this->load();
		$this->afterSave(false);
		$this->remove();
	}
	
	function getParentHook($arguments) {
		$object = $this;
		$object->load();
		if (method_exists($object->parent,'getId')) 
			echo $object->parent->getId();
		else echo $object->parent;		
	}
	
	function getPresentationHook($arguments) {
		$this->setArguments($arguments);
		global $Objects;
		if ($this->adapterId!="") {
			$this->adapter = $Objects->get($this->adapterId);
			$this->adapter->entity = $this;
		}
		$this->load();
		echo html_entity_decode($this->getPresentation(false),ENT_QUOTES,'UTF-8');
	}
	
	function setParamsHook($arguments) {
		$this->setArguments($arguments);
	}
	
	function showHook($arguments) {
		$this->load();
		$this->setArguments($arguments);
		$this->show();
	}
	
	function removeListHook($arguments) {
		global $Objects;
		echo $Objects->removeList($arguments["deleted_entities"]);
	}

	function vchanged($arguments) {
		$this->load();
		$this->old_name=$this->name;
		$this->old_parent=$this->parent;
		$this->isPublic=$arguments["value"];
		$this->save();
	}

	function get_id($arguments) {
		$this->load();
		if (method_exists($this->parent,'getId'))
			echo $this->parent->getId();
	}

	function admTpl($arguments) {
		$this->asAdminTemplate = true;
	}

	function childClassHook($arguments) {
		$this->load();
		echo $this->childClass;
	}

	function moveHook($arguments) {
		$object = $this;
        $object->load();
        $object->old_parent=$object->parent;
        $sortOrderCurrent = $object->sortOrder;
        global $Objects;
        $object2=$Objects->get($arguments["sible_target"]);
        $object2->load();
        $object2->old_parent=$object2->parent;        
        $sortOrderSible = $object2->sortOrder;
        $object->sortOrder = $sortOrderSible;
        $object2->sortOrder = $sortOrderCurrent;
        $object->save();
        $object2->save();$object->afterSave(false);
        if (method_exists($object->parent,'getId'))
			echo $object->parent->getId();
	}

	function showEntityInWindow($arguments) {
		$object = $this;
        $arguments = (array)$arguments;  
	    $this->setArguments($arguments);
		$object->parent_object_id=@$arguments["object_id"];
		if (isset($arguments["arguments"])) {
			if (!is_object($arguments["arguments"])) {
				if (!file_exists($arguments["arguments"])) {
					$arguments["arguments"] = json_decode($arguments["arguments"]);
                    $arguments["arguments"] = (array)$arguments["arguments"];
                } else
					$arguments["arguments"] = unserialize(file_get_contents($arguments["arguments"]));
			}
			$this->setArguments($arguments["arguments"]);
			if (!$this->loaded) $this->load();
			if (isset($arguments["arguments"]['hook'])) {								
				$hook = $object->getHookProc(@$arguments["arguments"]['hook']);
				if ($hook!="")
					$object->$hook($arguments["arguments"]);
			}
		} else {
			if (!$this->loaded) $this->load();				
		}
		$object->readOnly=@$arguments["readOnly"];
		if (!$this->loaded) $this->load();
		$object->show();
	}
	
	function setArguments($arguments,$onlyNew=false) {
		foreach ($arguments as $key => $value) {
			if (!$onlyNew or !isset($this->fields[$key])) {
				if (!is_array($value) and !is_object($value))
					$this->fields[trim($key)] = trim($value);
				else if (is_object($value)) {
					$this->$key = (array)$value;
				}
				else
					$this->$key = $value;
			}
		}
	}
	
	function getLinks($classes=array()) {		
		if (is_object($this->adapter->entity) and method_exists($this->adapter, "getLinks")) {
			return $this->adapter->getLinks($classes);
		}
		else
			return 0;
	}

	function getLinkClasses() {
		if (is_object($this->adapter->entity) and method_exists($this->adapter, "getLinkClasses")) {
			return $this->adapter->getLinkClasses();
		}
		else
			return 0;
	}
	
	function setLinks($links=array()) {
		if ($this->adapter!="") {
			$this->adapter->setLinks($links);
		}
	}
	
	function removeLinks($links=array()) {
		if ($this->adapter!="")
			@$this->adapter->removeLinks($links);
	}
	
	function setLinksHook($arguments) {
		if (!$this->loaded)
			$this->load();
		$this->setLinks((array)$arguments["links"]);
		global $Objects;
		foreach ($arguments["links"] as $value) {
			$obj = $Objects->get($value);
			$icon = $obj->icon;
		}		
		echo $icon;
	}

	function getClassTagNames() {
		if ($this->adapter!="" and is_object($this->adapter->entity) and method_exists($this->adapter, "getClassTagNames")) {
			return $this->adapter->getClassTagNames();
		}
		else
			return 0;
	}	

	function getClassFieldValues($tag) {
		if ($this->adapter!="" and is_object($this->adapter->entity) and method_exists($this->adapter, "getClassFieldValues")) {
			return $this->adapter->getClassFieldValues($tag);
		}
		else
			return 0;
	}
	
	function getCreateObjectList() {
		return $this->createObjectList;
	}
	
	function createByOwnerObject() {
		return 0;
	}
	
	function getEntityTableClass() {
		$arr = explode("~",$this->parentClientClasses);
		if (in_array("Reference",$arr))
			return "DocFlowReferenceTable";
		if (in_array("Document",$arr))
			return "DocFlowDocumentTable";
		return "EntityDataTable";
	}
	
	function getBarCode($params) {
		global $Objects,$bcobjects;
		$app = $Objects->get("Application");
		$this->getRole();
		if (!$app->initiated)
			$app->initModules();
		if (@$_GET["a"]=="1") {
			$obj = $Objects->get($bcobjects[@$_GET["o"]]."_".$this->module_id."_".@$_GET["i"]);
			if (is_object($obj)) {				
				$app->raiseRemoteEvent("SHOW_WINDOW","object_id=".$obj->getId(),$app->User,$app->User);
			}
		} else {
			$this->name = @$_GET["i"];
			$arr = explode("_",$this->getId());
			array_shift($arr);
			$this->construct($arr);
			$this->loaded = false;			
			$this->load();
			if ($this->scanCodeGenEvent)
				$app->raiseRemoteEvent("SCAN_CODE","object_class=".get_class($this).",i=".@$_GET["i"],$app->User,$app->User);
			if ($this->scanCodeShowForm) {
				$this->show();
			}
		}		
	}	
}
?>