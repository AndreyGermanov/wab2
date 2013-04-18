<?php
/**
 *  Интерфейсный класс, отображающий окно, содержит свойства, связанные с внешним видом окна
 *
 *  left - координата левой границы
 *  right - координата правой границы
 *  width - ширина
 *  height - высота
 *  max_left - максимальная правая позиция
 *  min_left - минимальная левая позиция
 *  max_bottom - максимальная нижняя позиция
 *  min_top - минимальная верхняя позиция
 *  resizeable - меняет ли размер
 *  moveable - можно ли перемещать окно
 *  has_minimize - есть ли кнопка сворачивания окна
 *  has_maximize - есть ли кнопка разворачивания окна
 *  has_close - есть ли кнопка закрытия окна
 *  taskbar_button - отображается ли в панели задач оконного менеджера
 *  icon - иконка окна (14x14)
 *  display_object - объект, который отображается в окне
 *  display_object_template - шаблон HTML-разметки, по которому отображается объект
 *  display_object_css - стили оформления объекта
 *  object_handler - обработчик объекта внутри окна
 *
 * @author andrey
 */
class Window extends WABEntity {
	public $winOptions = array();
    function construct($params="") {
        if (isset($params[0]))
            $this->object_id = implode("_",$params);

        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();

        $this->skinPath = $app->skinPath;
        $this->left = 0;
        $this->top = 0;
        $this->width = 350;
        $this->height = 240;
        $this->resizeable = true;
        $this->resizeable_top = true;
        $this->resizeable_bottom = true;
        $this->resizeable_left = true;
        $this->resizeable_right = true;
        $this->dock_left = false;
        $this->dock_right = false;
        $this->doc_top = false;
        $this->dock_bottom = false;
        $this->moveable = true;
        $this->has_minimize= true;
        $this->has_maximize = true;
        $this->has_close = true;
        $this->taskbar_button = true;
        $this->min_left = 1;
        $this->max_right = 0;
        $this->min_top = 1;
        $this->max_bottom = 0;
        $this->headless = 0;
        $this->template = "templates/interface/window.html";
        $this->handler = "scripts/handlers/interface/windowHandler.js";
        $this->css = $app->skinPath."styles/window.css";
        $this->has_border = true;
        $this->overrided = "";
        $this->objectHook = "";
        $this->winOptions[] = "object_text";
        $this->winOptions[] = "resizeable";
        $this->winOptions[] = "resizeable_top";
        $this->winOptions[] = "resizeable_bottom";
        $this->winOptions[] = "resizeable_left";
        $this->winOptions[] = "resizeable_right";
        $this->winOptions[] = "dock_top";
        $this->winOptions[] = "dock_bottom";
        $this->winOptions[] = "dock_left";
        $this->winOptions[] = "dock_right";
        $this->winOptions[] = "moveable";
        $this->winOptions[] = "has_minimize";
        $this->winOptions[] = "has_maximize";
        $this->winOptions[] = "has_border";
        $this->winOptions[] = "has_close";
        $this->winOptions[] = "left";
        $this->winOptions[] = "top";
        $this->winOptions[] = "width";
        $this->winOptions[] = "height";
        $this->winOptions[] = "min_left";
        $this->winOptions[] = "min_top";
        $this->winOptions[] = "max_right";
        $this->winOptions[] = "max_bottom";
        $this->winOptions[] = "headless";
        $this->clientClass = "Window";        
        $this->parentClientClasses = "Entity";        
    }

    function __set($name,$value) {
        switch ($name)
        {
            case "php_object_id":
                $this->fields[$name] = $value;
                break;
            default:
                $this->fields[$name] = $value;
        }
    }

    function getId() {
        return $this->object_id;
    }

    function getPresentation() {
        global $Objects;
        return $Objects->get($this->php_object_id)->getPresentation();
    }

    function getArgs() {
        global $Objects;
        $result = array();
        $obj = $Objects->get($this->php_object_id);
        if (!$obj->loaded)        
			$obj->load();
       	$objargs = $obj->getArgs();
		// Если передана процедура-перехватчик объекта, который будет в окне,
		// вызываем здесь ее. Иногда в этом перехватчике можно перекрыть
		// Некоторые параметры самого окна.
        if ($this->objectHook!="") {
			$hook = @$obj->getHookProc($this->objectHook);
			if ($hook!=0)
				@$obj->$hook(array());
		}
		$result = array_merge($result,$obj->getArgs());
		$app = $Objects->get("Application");
		if (!$app->initiated)
			$app->initModules();
        if ($this->init_string=="")
            $result["{init_string}"]="";
        $overrided_arr = explode(",",$Objects->get($this->php_object_id)->overrided); 
        $overrided_arr[] = "icon";
        if (file_exists("/var/WAB2/users/".$app->User."/settings/".$this->getId())) {
        	$object = $this;
        	eval(file_get_contents("/var/WAB2/users/".$app->User."/settings/".$this->getId()));
        	$this->overrided .= "top,left,width,height";
        } else {
	        if (file_exists("/var/WAB2/users/".$app->User."/settings/"."Window".$this->getId())) {
	        	$object = $this;
	        	eval(file_get_contents("/var/WAB2/users/".$app->User."/settings/"."Window".$this->getId()));
	        	$this->overrided .= "top,left,width,height";
	        }	        
        }    
        if ($this->overrided!="")
            $window_overrided_arr = explode(",",$this->overrided);
        else
            $window_overrided_arr = array();
        $this->readOnly = "false";
        
        // Определяем, открыт ли уже этот объект другим пользователем
        $obj = $Objects->get($this->php_object_id);
        if (count($obj->role)==0)
        	$obj->setRole();
        if ($obj->getRoleValue($obj->role["canEdit"])=="false") {
        	$this->readOnly = "true";
        }
        if ($this->readOnly=="false") {
	        if ($this->php_object_id != "Application" and $this->php_object_id != "Taskbar_Main" and !$this->ignoreChanging and !$obj->unchangeable and array_pop(explode("_",$this->php_object_id))!="") {
	            $found = false;
	            $usr = $obj->usedBy();
	            if (is_object($usr) and $usr->name!=$app->User)
	                $found = true;
	            // если не открыт, прописываем, что теперь он открыт текущим пользователем
	            if (!$found) {
	                $fp = fopen($app->variablesPath."users/".$app->User."/windows/".$this->php_object_id,"w");
	                fwrite($fp," ");
	                fclose($fp);
	           // иначе открываем его в режиме "только-для-чтения"
	           } else {
	                $this->readOnly = "true";
	            }
	        }
        }
        if ($this->php_object_id!="") {
        	$app->raiseRemoteEvent("ENTITY_OPENED","object_id=".$this->php_object_id);
        	$fp = fopen("/var/WAB2/users/".$app->User."/active_window","w");
        	fwrite($fp,$this->php_object_id);
        	fclose($fp);
        }
        foreach ($this->fields as $key=>$value)
        {
            if (in_array($key,$window_overrided_arr)===TRUE or in_array($key,$overrided_arr)===FALSE) {
                $result["{".$key."}"] = $value;
            }
        };
        if ($this->ignoreChanging)
            $result["{ignoreChangingStr}"] = "true";
        else
            $result["{ignoreChangingStr}"] = "false";
		if ($result["{headless}"])
			$result["{headlessDisplay}"] = "none";
		else
			$result["{headlessDisplay}"] = "";
        return $result;
    }
    
    function getHookProc($number) {
		switch ($number) {
			case '2': return "initWindow";
			case '3': return "saveSettings";
			case '4': return "removeSettings";
			case '5': return "addAutorun";
			case '6': return "removeAutorun";
		}
		parent::getHookProc($number);
	}
	
	function initWindow($arguments) {
	    global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
    	    $app->initModules();
        $this->php_object_id=@$arguments["php_object_id"];
        $clname = array_shift(explode("_",$this->php_object_id));
        $this->opener_object = @$arguments["opener_object"];
        $this->opener_item = @$arguments["opener_item"];
        $this->opener_instance = @$arguments["opener_instance"];
        $this->ignoreChanging = @$arguments["ignoreChanging"];

        if (isset($arguments["params"])) {
          	if (!is_object($arguments["params"]) && !is_array($arguments["params"]))
           		$arguments["params"] = json_decode($arguments["params"]);            	
            $arguments["params"] = (array)$arguments["params"];
            if (!isset($arguments["params"]["hook"])) {
        	    $this->objectHook = "";
                $object = $this;
                if (isset($arguments["params"]) and is_array($arguments["params"])) {
            	    eval(@implode(";",$arguments["params"]).";");
                }
            } else {
            	$arguments["params"]["readonly"] = $this->readOnly;
	            $this->objectHook = $arguments["params"]["hook"];
                @mkdir("/var/WAB2/users/".$app->User."/arguments/",0777,true);
                file_put_contents("/var/WAB2/users/".$app->User."/arguments/".$this->getId(), serialize($arguments["params"]));
              	$this->objectArguments = "/var/WAB2/users/".$app->User."/arguments/".$this->getId();
            }
            foreach($this->winOptions as $opt) {
    	       	if (isset($arguments["params"][$opt]))
					$this->fields[$opt] = $arguments["params"][$opt];
            }	            
        }
        $this->show();
	}
		
	function saveSettings($arguments=null) {
		global $Objects;
		if (isset($arguments))
			$this->setArguments($arguments);
		$app = $Objects->get("Application");
		if (!$app->initiated)
			$app->initModules();
		mkdir("/var/WAB2/users/".$app->User."/settings",0777,true);
		$str  = '$object->left='.$arguments["left"].';';
		$str .= '$object->top='.$arguments["top"].';';
		$str .= '$object->width='.$arguments["width"].';';
		$str .= '$object->height='.$arguments["height"].';';			
		file_put_contents("/var/WAB2/users/".$app->User."/settings/".$this->getId(),$str);
	}

	function removeSettings($arguments=null) {
		global $Objects;
		if (isset($arguments))
			$this->setArguments($arguments);
		$app = $Objects->get("Application");
		if (!$app->initiated)
			$app->initModules();
		unlink("/var/WAB2/users/".$app->User."/settings/".$this->getId());
	}

	function addAutorun($arguments=null) {
		global $Objects;
		if (isset($arguments))
			$this->setArguments($arguments);
		$app = $Objects->get("Application");
		if (!$app->initiated)
			$app->initModules();
		mkdir("/var/WAB2/users/".$app->User."/settings",0777,true);
		$autorun = array();
		if (file_exists("/var/WAB2/users/".$app->User."/settings/autorun")) {
			$autorun = (array)json_decode(trim(file_get_contents("/var/WAB2/users/".$app->User."/settings/autorun")));
		}
		$autorun[$this->php_object_id] = $this->object_text;
		file_put_contents("/var/WAB2/users/".$app->User."/settings/autorun",json_encode($autorun));
	}
	
	function removeAutorun($arguments=null) {
		global $Objects;
		if (isset($arguments))
			$this->setArguments($arguments);
		$app = $Objects->get("Application");
		if (!$app->initiated)
			$app->initModules();
		mkdir("/var/WAB2/users/".$app->User."/settings",0777,true);
		$autorun = array();
		if (file_exists("/var/WAB2/users/".$app->User."/settings/autorun")) {
			$autorun = (array)json_decode(file_get_contents("/var/WAB2/users/".$app->User."/settings/autorun"));
			unset($autorun[$this->php_object_id]);
			file_put_contents("/var/WAB2/users/".$app->User."/settings/autorun",json_encode($autorun));
		}
	}
	
}
?>