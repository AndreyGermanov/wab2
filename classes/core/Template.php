<?php
class Template extends WABEntity {

    public $events = array();
    public $args = array();
    
    function __construct($file) {
        $this->template_file = $file;
        $event_types[0]="onActivate";
        $event_types[1]="onAfterPrint";
        $event_types[2]="onBeforePrint";
        $event_types[3]="onAfterUpdate";
        $event_types[4]="onBeforeUpdate";
        $event_types[5]="onErrorUpdate";
        $event_types[6]="onAbort";
        $event_types[7]="onBeforeDeactivate";
        $event_types[8]="onDeactivate";
        $event_types[9]="onBeforeCopy";
        $event_types[10]="onBeforeCut";
        $event_types[11]="onBeforeEditFocus";
        $event_types[12]="onBeforePaste";
        $event_types[13]="onBeforeUnload";
        $event_types[14]="onBlur";
        $event_types[15]="onBounce";
        $event_types[16]="onChange";
        $event_types[17]="onClick";
        $event_types[18]="onControlSelect";
        $event_types[19]="onCopy";
        $event_types[20]="onCut";
        $event_types[21]="onDblClick";
        $event_types[22]="onDrag";
        $event_types[23]="onDragEnter";
        $event_types[24]="onDragLeave";
        $event_types[25]="onDragOver";
        $event_types[26]="onDragStart";
        $event_types[27]="onDrop";
        $event_types[28]="onFilterChange";
        $event_types[29]="onDragDrop";
        $event_types[30]="onError";
        $event_types[31]="onFilterChange";
        $event_types[32]="onFinish";
        $event_types[33]="onFocus";
        $event_types[34]="onHelp";
        $event_types[35]="onKeyDown";
        $event_types[36]="onKeyPress";
        $event_types[37]="onKeyUp";
        $event_types[38]="onLoad";
        $event_types[39]="OnLoseCapture";
        $event_types[40]="onMouseDown";
        $event_types[41]="onMouseEnter";
        $event_types[42]="onMouseLeave";
        $event_types[43]="onMouseMove";
        $event_types[44]="onMouseOut";
        $event_types[45]="onMouseOver";
        $event_types[46]="onMouseUp";
        $event_types[47]="onMove";
        $event_types[48]="onPaste";
        $event_types[49]="onPropertyChange";
        $event_types[50]="onReadyStateChange";
        $event_types[51]="onReset";
        $event_types[52]="onResize";
        $event_types[53]="onResizeEnd";
        $event_types[54]="onResizeStart";
        $event_types[55]="onScroll";
        $event_types[56]="onSelectStart";
        $event_types[57]="onSelect";
        $event_types[58]="onSelectionChange";
        $event_types[59]="onStart";
        $event_types[60]="onStop";
        $event_types[61]="onSubmit";
        $event_types[62]="onUnload";
        $event_types[62]="onContextMenu";

        for ($counter=0;$counter<count($event_types);$counter++)
        {   
            $this->events[$event_types[$counter]] = false;
        }
    }

    function getArgsString()
    {
        $result="";
        $result.="presentation=".$this->object->presentation."\n";
        $arr = $this->object->getArgs();
        foreach ($arr as $key=>$value)
        {
            $result.= str_replace("{","",str_replace("}","",$key))."=".$value."\n";
        }
        return $result;
    }

    /**
     *
     * Функция строит вывод текущего объекта по текущему шаблону.
     *
     * @global <массив> $Objects - глобальный кэш объектов
     * @param <логическое> $out - выводить для Ajax или нет
     * @return <строка>
     *
     */
    function parse($out=true) {
        global $Objects,$modules;
        $result["html"] = "";
        $result["css"] = "";
        
        $result["javascript"] = "<script>\n";
        $result["args"] ="";        
        if ($this->object!="") {
        	$object_id = $this->object->getId();
        	if (count($this->object->role)==0)
        		$this->object->setRole();
        	
        		if ($this->object->getRoleValue(@$this->object->role["canRead"])=="false") {
        			$this->reportError("Не достаточно прав доступа!");
        			return 0;
        		};
        			
                if ($this->object->object_text!="")
                        $object_text = $this->object->object_text;
                else {
                    $object_text = $this->object->presentation;

                    if ($this->object->readOnly=="true") {
                            $object_text = $object_text." (только для чтения)";
                    }
                }
                if ($this->object->icon!="")
                        $object_icon = $this->object->icon;
                else {
                    $object_icon = "";
                    $this->object->icon = "images/spacer.gif";
                }
        }
        else
        {
            $object_id = "";
            $object_text = "";
        }
        if ($this->instance!="")
        {
            $instance_id = "_".$this->instance;
        }
        else
            $instance_id = "";
        $object_id = $object_id;
        $js_object_id = str_replace(".","_",str_replace("@","_",$object_id));
        $this->args = array_merge($this->args,array("{object_id}" => $object_id));
        $this->args = array_merge($this->args,array("{instance_id}" => $this->instance));
        $this->args = array_merge($this->args,array("{full_object_id}" => $object_id.$instance_id));
        if ($object_icon!="")
            $this->args = array_merge($this->args,array("{icon}" => $object_icon));

        $this->args = array_merge($this->args,array("{object_text}" => $object_text));
        $this->args = array_merge($this->args,array("{object_class}" => get_class($this->object)));
        $parse_style = false;
        // Обработка стилевой спецификации
        $rels = "";
        if ($this->css!="")
        {
            $arr = file($this->css);
            if (trim($arr[0])=="/* linked */") {
                $rels .= "<link rel='stylesheet' href='".$this->css."' type='text/css' media='all'/>";
                $parse_style = false;
            }
            else {
                $parse_style = true;
                $lines = $this->applyIncludeLines(file($this->css));
                foreach ($lines as $line)
                {
                    foreach ($this->args as $key=>$value)
                        if (!is_array($value) and !is_object($value))
                            $line=str_replace($key,$value,$line);
                    $line = preg_replace("/^\.#(.*) /",".".$js_object_id.$instance_id."_$1",$line);
                    $line = preg_replace("/^#(.*) /","#".$js_object_id.$instance_id."_$1",$line);
                    $line = str_replace("#".$object_id."_{","#".$js_object_id."{",$line);
                    $result["css"] .= $line;
                }
            }            
        }        
        // обработка шаблона разметки                
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        
        // Если файл, указанный в качестве шаблона существует, открываем его
        if (file_exists($app->root_path."/".$this->template_file)) {
        	$fp = fopen($app->root_path."/".$this->template_file,"r");
        	$result["html"].= "";
        	$strings = file_get_contents($app->root_path."/".$this->template_file);
        	$str = file($app->root_path."/".$this->template_file);
        } else {
        	// если файл не существует, значит это не файл, а процедура, формирующая шаблон,
        	// в этом случае получаем текст шаблона из нее
        	$tpl = $this->template_file;
        	$strings = $this->object->$tpl();
        	$str = explode("\n",$strings);        	
        };
      	if (isset($str[0]) and trim($str[0])=="<!-- unnamed -->") {
          	$this->object->unnamed = true;
          	array_shift($str);
           	$strings = implode("\n",$str);
       	}
       	//$strings = str_replace("xyxyxy","\n",$strings);
        if (!$this->object->unnamed)
            $result["html"].= "<div id='".str_replace("@","_",str_replace(".","_",$object_id.$instance_id))."' object='".$object_id."' instance='".$this->instance."'>";
        foreach ($this->args as $key=>$value)
        {
            if (!is_array($value) and !is_object($value))
            {
                if ($key[0]=="{") {
                	if ($key[1]!="|")
                    	$strings=str_replace($key,htmlspecialchars($value,ENT_QUOTES),$strings);
                	else {
                		$strings=str_replace($key,$value,$strings);
                	}
                }
            }
        } 
        
        preg_match_all("/(\{\:.*\:\})/",$strings,$matches,PREG_SET_ORDER);            
        while(preg_match("/(\{\:.*\:\})/",$strings,$match))
        {        
            $str = $match[1];
            $params = explode(",",str_replace("{:include ","",str_ireplace(":}","",$str)));
            $object = $params[0];
            $object = $Objects->get($object);
            $object->asAdminTemplate = false;
            if (count($params)>1)
                $instance = $params[1];
            if (count($params)>2) {
                if ($params[2]!="") {
                	$params[2] = html_entity_decode($params[2],ENT_QUOTES);                	
                    eval($params[2]);      
                }
            }
            if (count($params)>3) {
                if ($params[3]!="") {
                    $object->template = $params[3];
                    $object->reset_template = true;
                }
            }
            if (count($params)>4) {
                if ($params[4]!="") {
                    $object->handler = $params[4];
                    $object->reset_handler = true;
                }
            }
            if (count($params)>5) {
                if ($params[5]!="") {
                    $object->css = $params[5];
                    $object->reset_css = true;
                }
            }
            $static = false;
            if (count($params)>6) {
                if ($params[6]=="static")
                    $static = true;
                else
                    $static = false;
            }
            $object->inner = true;
            if ($object->module_id=="")
                $object->module_id = @$_SERVER["MODULE_ID"];
            $app = $Objects->get("Application");
            if (!$app->initiated)
                $app->initModules();            
			if (!$object->loaded)
				$object->load();
			if (count($params)>3) {
				if ($params[3]!="") {
					$object->template = $params[3];
					$object->reset_template = true;
				}
			}
			if (count($params)>4) {
				if ($params[4]!="") {
					$object->handler = $params[4];
					$object->reset_handler = true;
				}
			}
			if (count($params)>5) {
				if ($params[5]!="") {
					$object->css = $params[5];
					$object->reset_css = true;
				}
			}        
            if ($static and $object->siteId!="" and !$object->asAdminTemplate) {
                $object->setTemplates();
                
                if (!file_exists($app->variablesPath."cache/".$object->siteId."/".$object->getId()."/".str_replace("/","~",$object->template))) {
                    $shell = $Objects->get("Shell_shell");
                    $shell->exec_command($app->makeDirCommand." -p ".$app->variablesPath."cache/".$object->siteId."/".$object->getId());
                    $shell->exec_command($app->chownCommand." -R ".$app->apacheServerUser." ".$app->variablesPath."cache/".$object->siteId."/".$object->getId());                    
                    $inner_result = $object->show($instance,false);
                    file_put_contents($app->variablesPath."cache/".$object->siteId."/".$object->getId()."/".str_replace("/","~",$object->template), $inner_result["html"]);
                    file_put_contents($app->variablesPath."cache/".$object->siteId."/".$object->getId()."/".str_replace("/","~",$object->css), $inner_result["css"]);
                    file_put_contents($app->variablesPath."cache/".$object->siteId."/".$object->getId()."/".str_replace("/","~",$object->handler), $inner_result["javascript"]);                    
                }                
                $inner_result = array();
                $inner_result["html"] = file_get_contents($app->variablesPath."cache/".$object->siteId."/".$object->getId()."/".str_replace("/","~",$object->template));
                $inner_result["css"] = file_get_contents($app->variablesPath."cache/".$object->siteId."/".$object->getId()."/".str_replace("/","~",$object->css));
                $inner_result["javascript"] = file_get_contents($app->variablesPath."cache/".$object->siteId."/".$object->getId()."/".str_replace("/","~",$object->handler));
                $inner_result["args"] = "";
                $strings = str_ireplace($str,$inner_result["html"],$strings);
                $result["javascript"].=str_replace("<script>","",str_replace("</script>","",$inner_result["javascript"]));
                $result["css"].=str_replace("<style>","",str_replace("</style>","",$inner_result["css"]));
                $result["args"].=$inner_result["args"];
            } else {
            	$inner_result = $object->show(@$instance,false);
            	$strings = str_ireplace($str,$inner_result["html"],$strings);
                $result["javascript"].=str_replace("<script>","",str_replace("</script>","",$inner_result["javascript"]));
                $result["css"].=str_replace("<style>","",str_replace("</style>","",$inner_result["css"]));
                $result["args"].=$inner_result["args"];
            }            
        }
        preg_match_all("/\[\[(.+)\]\]/",$strings,$matches,PREG_SET_ORDER);                    
        foreach ($matches as $match) {
            $str = $match[0];
            $phrase = l10n($match[1]);
            $strings = str_ireplace($str,$phrase,$strings);
        }
        $result["html"].= $strings;
        if (!$this->object->unnamed)
            $result["html"].= "</div>";

        if ($this->object->clientClassLoaded!=true) {
            $this->object->clientClassLoaded = true;
        }
        if ($this->handler!="" and trim(file_get_contents($app->root_path."/".$this->handler))!="")
            $result["javascript"] .= "object_id='$object_id';\n instance_id = '$this->instance'; \n";
        
        //if ($this->object->template_class=="")
            $js_class = get_class($this->object);
        //else {
            //$js_class = basename($this->object->template_class);
            //$js_class = array_shift(explode(".",$js_class));
        //}
        if ($result["javascript"]!="<script>\n")
            $result["javascript"] .= "if (typeof(".$js_class.") != 'undefined') {\n object_id='".$this->object->getId()."';\nobj = new ".$js_class."(object_id,instance_id);objects.add(obj);\n};\n";
        $catched_events = array();
        $default_events = array();
        if ($result["javascript"]!="<script>\n") {
            if ($this->handler!="")
            {
                $lines = $this->applyIncludeLines(file($app->root_path."/".$this->handler));
                foreach ($lines as $line)
                {
                    foreach ($this->args as $key=>$value)
                    {
                        if (!is_array($value) and !is_object($value))
                            $line=str_replace($key,$value,$line);
                    }
                    $result["javascript"] .=$line;
                }            
            }
        }
        // Обрабатываем коллекции элементов раздела, если есть
        error_reporting(E_ERROR | E_NOTICE);
        $arr = explode("_",$object_id);
        if ($arr[0]!="ItemTemplate") {
            $result["html"] = $this->fillItemCollections($result["html"],$result["css"]);
        }
        
        foreach ($this->args as $key=>$value)
        {
            if (!is_array($value) and !is_object($value))
            {
                if ($key[0]=="[")
                    $result["html"]=str_replace($key,$value,$result["html"]);
            }
        }
        if ($result["javascript"]!="<script>\n") {
            $result["javascript"] .="\n";
            $result["javascript"] .= "</script>\n";
        }
        if ($result["javascript"]=="<script>\n")
            $result["javascript"] = "";
        if ($parse_style)
            $result["css"] = "<style>\n".str_replace("<style>","",str_replace("</style>","",$result["css"]))."</style>".$rels;
        else
        	$result["css"] .= $rels;
        // Если запрос пришел от объекта Ajax.Request, то выводим результат в виде объекта JSON
        if (isset($_POST["ajax"]) and $this->object->inner!=true)
        {
            $result["args"] = "";//$this->getArgsString();
            echo json_encode($result);
            $end_parse_time = time();
            return json_encode($result);
        }
        else
        {
            // Иначе просто выводим по порядку стилевую спецификацию, html-разметку и текст обработчика
            // событий на JavaScript
            $result["args"] = "";
            if ($out)
            {
                print($result["css"]."\n");
                print($result["html"]."\n");
                if ($result["javascript"]!="")
                    print($result["javascript"]."\n");
            }
            $end_show = microtime(1);
            return $result;
        }
    }

    function applyIncludeLines($strings) {
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        $result_strings = array();
        foreach($strings as $line) {
            if (preg_match("/\/\/include (.*)$/",$line,$matches,PREG_OFFSET_CAPTURE)) {                
                $result_strings = array_merge($result_strings,$this->applyIncludeLines(file(trim($app->root_path."/".$matches[1][0]))));
            } else
                $result_strings[count($result_strings)] = $line;
        }
        return $result_strings;
    }

    /**
     *  Функция преобразует таблицу в список элементов WebItem. Принимает на
     * вход шаблон ($table), в котором находится разметка каждой строки и столбца
     * Каждая ячейка содержит один элемент.
     *
     * Формирование списка производится по правилам, которые формируют атрибуты
     * тэга:
     *
     * base_item - родительский элемент, дочерние элементы которого попадают в список
     * rows - количество строк списка (или * если бесконечно)
     * cols - количество столбцов списка (или * если бесконечно)
     *
     * Если элементов больше чем cols*rows, то выводятся не все элементы.
     *
     * Если внутри таблицы есть тэг <table>, имеющий атрибут type="pagepanel",
     * то, этот тэг будет использоваться как шаблон для формирования панели страниц,
     * которая позволяет организовать постраничный вывод списка
     *
     * @global <объект> $Objects - кэш объектов
     * @param <узел HTML> $table - таблица-шаблон
     *
     */
    function parseTable(&$table) {
        $rows = $table->getElementsByTagName("tbody")->item(0)->childNodes;
        $body = $table->getElementsByTagName("tbody")->item(0);
        $rows_array = array();
        $cols_array = array();
        foreach ($rows as $row) {
            if ($row->nodeName=="tr" and $row->getAttribute("type")!="pagepanel") {
                $rows_array[count($rows_array)] = $row;
                $cols_array[count($rows_array)-1] = array();
                $cols = $row->childNodes;
                foreach($cols as $col) {
                    if ($col->nodeName=="td")
                        $cols_array[count($rows_array)-1][count($cols_array)-1] = $col;
                }
                
            }
            else {
                if ($row->nodeName=="tr" and $row->getAttribute("type")=="pagepanel") {
                    $pagepanel = $row->cloneNode(true);
                    $body->removeChild($row);
                }                
            }
        }
        $cols = $table->getAttribute("cols");
        $rows = $table->getAttribute("rows");
        $only_public = $table->getAttribute("only_public");
        $current_col = 1;
        $current_row = 1;
        $pages=1;
        global $Objects;
        $base_item = $Objects->get($table->getAttribute("base_item"));
        $childs = $base_item->getItems(0,0,"",$only_public);
        $items_count = count($childs);
        if (!isset($current_page))
            $current_page = 1;
        if (isset($_GET[$this->object->id."_current_page"]))
            $current_page = $_GET[$this->object->id."_current_page"];
        if ($rows!="*") {
            $page_size = $cols*$rows;
            if ($items_count>$page_size) {
                $pages = ceil($items_count/$page_size);
            }
        }
        if (isset($pagepanel)) 
            $childs = $base_item->getItems(($current_page-1)*$page_size,$page_size);
        else
            $childs = $base_item->getItems();
        $tpl_col = 0;
        $tpl_row = 0;
        $counter = 0;
        $row = $rows_array[$tpl_row]->cloneNode(true);
        $row->nodeValue = "";
        foreach ($childs as $child) {
            if ($only_public!=null)
                if (!$child->is_public)
                    continue;
            if ($counter>$items_count)
                break;
            if ($tpl_col>=count($cols_array[$tpl_row]))
                $tpl_col = 0;
            if ($current_col>$cols) {
                $current_col = 1;
                $current_row = $current_row+1;
                $tpl_row = $tpl_row + 1;
                if ($tpl_row>=count($rows_array))
                    $tpl_row = 0;
                $body->appendChild($row);
                $row = $rows_array[$tpl_row]->cloneNode(true);
                $row->nodeValue = "";
            }
            if ($rows!="*")
                if ($current_row>$rows)
                    break;
            if (isset($cols_array[$tpl_row][$tpl_col])) {
                $col = $cols_array[$tpl_row][$tpl_col]->cloneNode(true);
                $args = $child->getChildArgs();               
                $this->parseNode($col,$args);
                $row->appendChild($col);
                $tpl_col = $tpl_col+1;
                $current_col = $current_col+1;
            }
        }
        $body->appendChild($row);
        foreach($rows_array as $row)
            $body->removeChild($row);
        if ($pages>1) {
            if (isset($pagepanel)) {
                $page_col = $pagepanel->getElementsByTagName("td")->item(0);
                $page_tpl = $page_col->getElementsByTagName("page")->item(0);
                $selected_page_tpl = $page_col->getElementsByTagName("selected_page")->item(0);
                for ($counter=1;$counter<=$pages;$counter++) {
                    if ($counter==$current_page)
                        $page = $selected_page_tpl->cloneNode(true);
                    else
                        $page = $page_tpl->cloneNode(true);
                    $this->parseNode($page,array("#page" => $counter));
                    $link = $table->ownerDocument->createElement('a');
                    $link->setAttribute("href","?i=".$this->object->base_id."&".$this->object->id."_current_page=".$counter);
                    $link->appendChild($page);
                    $page_tpl->parentNode->insertBefore($link,$page_tpl);
                }
                $page_tpl->parentNode->removeChild($page_tpl);
                $selected_page_tpl->parentNode->removeChild($selected_page_tpl);
                $body->appendChild($pagepanel);
            }
        }
        $table->setAttribute("type","");
        $counter++;
    }

    function parseEntityTable(&$table,$args="") {
        global $Objects,$defaultCacheDataAdapter;
        $id = $table->getAttribute("id");
        $rows_count = $table->getAttribute("rows");
        $cols_count = $table->getAttribute("cols");
        $object = $table->getAttribute("object");
        $item = $table->getAttribute("item");
        $condition = $table->getAttribute("condition");
        $sort = $table->getAttribute("sort");
        $useAdapter = $table->getAttribute("useadapter");
        $className = $table->getAttribute("className");
        $query = $table->getAttribute("query");
        $adapterId = $table->getAttribute("adapterId");
        
        if ($useAdapter=="true") {
            if ($adapterId!=null)
                $adapter = $Objects->get($adapterId);
            else
                $adapter = $Objects->get($object)->adapter;
        }
        else
            $adapter = "";
        if ($Objects->get($object)->module_id!="")
            $module_id = "_".$Objects->get($object)->module_id;
        else
            $module_id = "";
        $from = $table->getAttribute("from");        
        if ($from==null)
            $from = 0;
        if (isset($_GET[$id."_from"]))
            $from = $_GET[$id."_from"];
        if (isset($_GET[$id."_sort"]))
            $sort = $_GET[$id."_sort"];
        if (isset($_POST[$id."_from"]))
            $from = $_POST[$id."_from"];
        if (isset($_POST[$id."_sort"]))
            $sort = $_POST[$id."_sort"];
        $table->setAttribute("type","");
        if ($condition==null)
            $condition = "";
        $items = array();
        if ($className==null)
            $className = get_class($Objects->get($object));
        if ($item==null) {
            if ($useAdapter=="true") {
                if ($from!=null)
                    $limit = $from.",".($rows_count*$cols_count);
                else
                    $limit = "";
                if ($query==null) {
                    $childs = $Objects->query("*".$className."*".$module_id,$condition,$adapter,$sort,&$limit);
                    $count_items = $limit;
                }
                else { 
                    if ($rows_count!="*" and $cols_count!="*")
                        $childs = PDODataAdapter::makeQuery($query." LIMIT ".$from.",".$rows_count*$cols_count,$adapter);
                    else {
                        $childs = PDODataAdapter::makeQuery($query,$adapter);
                    }

                    $arr = PDODataAdapter::makeQuery($query,$adapter,"",true);
                    $count_items = $arr["count"];
                }
            }
            else 
                $childs = $Objects->get($object)->getItems($condition,$sort,$from,$adapter);   
        } else {
            $obj = $Objects->get($object);
            if (!$obj->loaded) {
                $obj->load();
            }
            $items = explode(",",$item);
            if (count($items)==1 and $item!="*" and $item!="+") {
                if (!isset($obj->fields[$item])) {
					$table->parentNode->removeChild($table);
					return 0;
                }
                if (is_array($obj->fields[$item])) {
                    $condition = str_replace("@",'@$elem->',$condition);
                    $childs = array();
                    $c=0;
                    echo print_r($obj->fields[$item]);
                    foreach ($obj->fields[$item] as $key=>$elem) {
                        if ($condition!="") {
                            if (eval('return '.$condition.";")) {
                                if ($c<$from) {
                                    $c++;
                                    continue;
                                }
                                $childs[count($childs)] = $elem;
                            }
                        } else {
                            $childs[count($childs)] = $elem;
                        }
                    }
                    $sort_parts = explode(" ",$sort);
                    $sort_field = $sort_parts[0];
                    if (isset($sort_parts[1]))
                        $sort_direction = strtoupper($sort_parts[1]);
                    else
                        $sort_direction = "ASC";
                    $res = $childs;
                    $childs = array();
                    foreach ($res as $entry) {
                            $childs[@strtoupper($entry->fields[$sort_field])." ".count($childs)] = $entry;
                    }
                    if ($sort_direction=="DESC")
                        krsort($childs);
                    else
                        ksort($childs);
                    $res = $childs;
                    $result = array();
                    foreach($res as $entry)
                        $result[count($result)] = $entry;
                    $childs = $result;
                }
            } else {
                if ($items[0]=="*") {
                    $childs = array_flip(array_keys($obj->fields));
                    array_shift($items);
                    foreach ($items as $key=>$value) {
                        if (isset($childs[$value]))
                           unset($childs[$value]);
                    }
                    $childs = array_flip($childs);
                } else if ($items[0]== "+") {
                    $childs = array_flip(array_keys($obj->getPersistedArray()));
                    array_shift($items);
                    foreach ($items as $key=>$value) {
                        if (isset($childs[$value]))
                           unset($childs[$value]);
                    }
                    $childs = array_flip($childs);
                }
                else {
                    $childs = $items;
                }
            }
        }

        if ($rows_count == null or $rows_count == 0)
            $rows_count = count($childs);
        if ($cols_count == null)
            $cols_count = 1;
        $tbody = $table->getElementsByTagName("tbody")->item(0);
        $rows = $tbody->getElementsByTagName("tr");
        $tpl_rows = array();
        $c=0;
        while ($rows->item($c)!=null) {
            $row = $rows->item($c);
            if ($row->parentNode!=$tbody) {
                $c++;
                continue;
            }
            if ($row->getAttribute("type")=="pagepanel") {
                $pagepanel_row = $row;
            }
            else if ($row->getAttribute("type")!="header")
                $tpl_rows[count($tpl_rows)] = $row;
            if ($row->getAttribute("type")!="header")
                $row->parentNode->removeChild($row);
            else
                $c++;
        }
        $tpl_row_number = 0;
        $tpl_col_number = 0;
        $row_number = 0;
        $c = 0;
        while ($c<count($childs)) {
            $row = $tpl_rows[$tpl_row_number]->cloneNode(true);
            $cols = $row->getElementsByTagName("td");
            $cols_array = array();
            while ($cols->item(0) != null) {
                $col = $cols->item(0);
                $cols_array[count($cols_array)] = $col;
                $col->parentNode->removeChild($col);
            }
            $tpl_col_number = 0;
            for ($c1=0;$c1<$cols_count;$c1++) {
                $col = $cols_array[$tpl_col_number]->cloneNode(true);
                if ($col->hasChildNodes()) {
                    if (is_object(current($childs)))
                        $this->parseNode($col,current($childs)->getChildArgs());
                    else
                         if (is_array(current($childs))) {  
                            $arr = $this->getArrayAsArgs(current($childs));
                            $this->parseNode($col,$arr);                            
                         }
                    else {
                        if (count($items)>1 or $item=="*" or $item="+") {
                            $args = array();
                            $object_args = $this->object->getArgs();
                            foreach ($object_args as $key=>$value) {
                                if (strpos($key,current($childs))!==FALSE) {
                                    $key_arr = explode("_",$key);
                                    $key_last = array_pop($key_arr);
                                    if (count($key_arr)==0) {
                                        $key_last = str_replace(current($childs),"field",$key_last);
                                        $args[$key_last] = $value;
                                    } else {
                                        $key_first = implode("_",$key_arr);
                                        $key_first = str_replace(current($childs),"field",$key_first);
                                        $args[$key_first."_".$key_last] = $value;
                                    }
                                }
                            }
                            $this->parseNode($col,$args);
                        } else
                            $this->parseNode($col,array("{".$item."_value}"=>current($childs)));
                    }
                }
                $c++;
                next($childs);
                $row->setAttribute("content","true");
                $row->appendChild($col);
                $tpl_col_number++;
                if (!isset($cols_array[$tpl_col_number]))
                    $tpl_col_number = 0;
                if ($c>=count($childs))
                    break;
            }
            $tbody->appendChild($row);

            $tpl_row_number++;
            if (!isset($tpl_rows[$tpl_row_number]))
                $tpl_row_number = 0;
            $row_number++;
            if ($row_number>=$rows_count)
                break;
        }
        if (isset($pagepanel_row)) {
            $pagepanel_td = $pagepanel_row->getElementsByTagName("td")->item(0);
            if ($item==null) {
            } else {
                $obj = $Objects->get($object);
                if (is_array($obj->fields[$item])) {
                    $count_items = count($obj->fields[$item]);
                }
            }
            $items_per_page = $rows_count*$cols_count;
            $pages = ceil($count_items/$items_per_page);
            if ($pages<=1)
                return 0;            
            $spans = $pagepanel_td->getElementsByTagName("span");
            for ($c=0;$c<$spans->length;$c++) {
                if ($spans->item($c)->getAttribute("type")=="page") {
                    $page_span = $spans->item($c);
                    $page_span_parent = $spans->item($c)->parentNode;
                }
                if ($spans->item($c)->getAttribute("type")=="selected_page") {
                    $selected_page_span = $spans->item($c);
                    $page_span_parent = $spans->item($c)->parentNode;
                }
            }
            $args = array();
            if (isset($obj))
                $args = $obj->getArgs();
            $query_array = explode("&",$_SERVER["QUERY_STRING"]);
            $query_hash = array();
            $current_from = @$_GET[$id."_from"];
            if (!isset($current_from))
                $current_from = 0;
            if (count($query_array)>0) {
                foreach($query_array as $elem) {
                    $elem_parts = explode("=",$elem);
                    @$query_hash[$elem_parts[0]] = $elem_parts[1];
                }
            }
            for ($c=1;$c<=$pages;$c++) {
                $args["#text"] = $c;
                $query_hash[$id."_from"] = $items_per_page*($c-1);
                $qa = array();
                if (isset($query_hash)) {
                foreach($query_hash as $key=>$value) {
                    $qa[count($qa)] = $key."=".$value;
                }
                    $args["#link"] = "?".implode("&",$qa);
                }
                else
                    $args["#link"] = "?".$id."_from=".$items_per_page*($c-1);
                if ($current_from == $items_per_page*($c-1)) {
                    $page_span1 = $selected_page_span->cloneNode(true);
                }
                else
                    $page_span1 = $page_span->cloneNode(true);
                $this->parseNode($page_span1,$args);
                $page_span_parent->appendChild($page_span1);
            }
            if ($page_span_parent != $pagepanel_td)
                $pagepanel_td->appendChild($page_span_parent);
            $page_span->parentNode->removeChild($page_span);
            $selected_page_span->parentNode->removeChild($selected_page_span);

            $tbody->appendChild($pagepanel_row);
        }
    }

    /**
     *
     * Функция обрабатывает сформированный шаблон, все таблицы,
     * имеющие атрибут type="WebItemCollection". Эти таблицы являются списками
     * элементов WebItem.
     *
     * @global <объект> $Objects - глобальная кэш объектов
     * @param <строка> $string - сформированный шаблон
     * @param <строка> $css - стилевая спецификация
     * @return <строка>
     *
     */
    function fillItemCollections($string,$css) {

        $doc = new DOMDocument();
        $result = $doc->loadXML('<?xml version="1.0" encoding="UTF-8"?>'."\n".str_replace('<?xml version="1.0" encoding="UTF-8"?>','',$string));

        $tables = $doc->getElementsByTagName("table");
        $found = false;
        foreach ($tables as $table) {
            if ($table->getAttribute("type")=="WebItemCollection") {                
                $found = true;
                $this->parseTable($table);
            }                
            if ($table->getAttribute("type")=="Collection") {
                $found = true;
                $this->parseEntityTable($table);
            }
        }
        $images = $doc->getElementsByTagName("img");
        foreach ($images as $image) {
            if (!$image->getAttribute("src"))
                $image->setAttribute("src","/content/images/spacer.gif");
        }
        if ($found) {
            return htmlspecialchars_decode($doc->saveXML(),LIBXML_NOEMPTYTAG);
        }
        else
            return $string;
    }

    /**
     *
     * Функция, выполняющая подстановку аргументов $args в содержимое указанного
     * узла $node, а также в его дочерние узлы, для каждого из которых рекурсивно
     * вызывается эта же функция
     *
     * @param <узел-HTML> $node - узел
     * @param <массив> $args - список заменяемых значений в формате ключ=значение
     * @return <строка>
     *
     */
    function parseNode(&$node,$args) {        
        if ($node->attributes!=null) {
            $to_remove = array();
            foreach($node->attributes as $name=>$value) {                
                if ($name=="checked" and $value->value=="{checked}" and @$args["{checked}"]=="") {
                    $to_remove[count($to_remove)] = $name;
                }
                if ($name=="checked" and $value->value=="{checked_child}" and @$args["{checked_child}"]=="") {
                    $to_remove[count($to_remove)] = $name;
                }
                if ($name=="checked" and $value->value=="{checked_parent}" and @$args["{checked_parent}"]=="") {
                    $to_remove[count($to_remove)] = $name;
                }
                if ($node->nodeName=="img" and $name=="src" and $value->value=="")
                    $value->value = "/content/images/spacer.gif";
                $node->setAttribute($name,strtr($value->value,$args));
            }
            foreach ($to_remove as $attr) {
                $node->removeAttribute($attr);
            }
            if ($node->getAttribute("type")=="WebItemCollection") {
                    $this->parseTable($node,$args);
            }
            if ($node->nodeName == "table" and $node->getAttribute("type")=="Collection") {
                    $this->parseEntityTable($node,"nested");
            }
        }
        if ($node->hasChildNodes()) {
            $childs = $node->childNodes;
            foreach($childs as  $child) {
                $this->parseNode($child,$args);
            }
        }
        else {
            $node->nodeValue = strtr($node->nodeValue,$args);
        }
    }
    
    function getArrayAsArgs($array) {
        $result = array();
        foreach ($array as $key=>$value) {
            $result["{".$key."_child}"] = $value;
        }
        return $result;
    }    
}
?>