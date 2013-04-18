<?php    
    $webitem_classes = array();

    function cmp($a,$b) {
        $a = explode("_",$a);
        $b = explode("_",$b);
        $a1 = array_shift($a);
        $b1 = array_shift($b);
        $a = implode("_",$a);
        $b = implode("_",$b);
        if ($a1==$b1) {
            if ($a==$b) return 0;
            if ($a<$b) return -1;
            if ($a>$b) return 1;
        }
        if ($a1>$b1) return 1;
        if ($a1<$b1) return -1;
    }

class Objects extends WABEntity {

    public $objects;
    public $object_names;
    public $created;
    public $scripts_loaded = false;

	function decodeJson($arr) {
		$arr1 = array();
		foreach ($arr as $key=>$value) {
			if (is_array($value)) {
				$arr1[$key] = $this->decodeJson($value);
				continue;
			}
			if (is_array(json_decode($value))) {
				$arr1[$key] = $this->decodeJson(json_decode($value));
			}
			else
				$arr1[$key] = $value;
		}
		$this->clientClass = "Objects";
		$this->parentClientClasses = "Entity";		
		return $arr1;
	}
	
	function getArguments($str) {
		$arr = explode("&",$str);
		foreach ($arr as $item) {
			$parts = explode("=",$item);
			if ($parts[0]=="arguments")
				return urldecode($parts[1]); 
		}
		return "";
	}
    /**
     *
     * Функция добавляет указанный объект $object в глобальный массив объектов,
     * присваивая ему индекс $id
     * 
     * @param <строка> $id Идентификатор объекта
     * @param <объект> $object Объект
     * @return <объект>
     */

    function start($object_id="",$init_string="",$hook="0",$arguments=array()) {
        if (@strpos("~",$object_id)!==FALSE) {
            $arr = explode("~",$object_id);
            $object_id=array_shift($arr);
            $init_string = implode("~",$arr);
        }
        
        if ($object_id!="") {            
            $object = $this->get($object_id);
            $Objects = $this;
            $object_init_string = $init_string;
            if (@$_POST["action"]=="submit") {
                $hook = '3';
            }
            if (@$_POST["action"]=="unsubmit") {
            	$hook = '4';
            }            
            if ($arguments=="")
            	$arguments = $this->getArguments($_SERVER["QUERY_STRING"]);
            $object->arguments = $arguments;
            if ($arguments!="") {
	            if (!file_exists($object->arguments))
	                $arguments = (array)json_decode($arguments);  
	            else {
	                $arguments = unserialize(file_get_contents($object->arguments));
	            }
            }
            $hookProc = @$object->getHookProc($hook);
            if ($hook!="" and $hookProc!="") {
                @$object->$hookProc($arguments);
            }
            else {
                if ($init_string=="" or $init_string=="undefined")
                    $init_string = '$object->show();';
                eval($init_string);     
            }
        }
    }
    
    function set($id, $object) {
        $vars1 = explode("_",$id);
        $obj_name = array_shift($vars1);
        if (!isset($this->objects[$id]))
        {
            /**
             * Модификация массива типов объектов, которые есть в массиве
             * объектов.
             */
            if (!isset($this->object_names[$obj_name]))
            {
                    $this->object_names[$obj_name]="0-0";
                    uksort($this->object_names,"cmp");
                    reset($this->object_names);
                    if (key($this->object_names) != $obj_name)
                    {
                        $found = false;
                        while (key($this->object_names)!=$obj_name)
                        {
                            $value = current($this->object_names);
                            next($this->object_names);
                            if (key($this->object_names)==$obj_name)
                                $found = true;
                        }
                        $vars = explode("-",$value);
                        $low = $vars[1]+1;
                        $high = $vars[1]+1;

                        $this->object_names[$obj_name] = $low."-".$high;
                    }
            }
            else
            {
                $vars1 = explode("-",$this->object_names[$obj_name]);
                $low = $vars1[0];
                $high = $vars1[1]+1;
                $this->object_names[$obj_name] = $low."-".$high;
                uksort($this->object_names,"cmp");
                reset($this->object_names);
                if (key($this->object_names) != $obj_name)
                {
                    while (key($this->object_names)!=$obj_name)
                    {
                        $value = current($this->object_names);
                        next($this->object_names);
                    }
                }
            }

            while (next($this->object_names)) {
                $key = key($this->object_names);
                if (cmp($key,$obj_name)!=1)
                    continue;
                $vars = $this->object_names[$key];
                $vars = explode("-",$vars);
                $low = $vars[0]+1;
                $high = $vars[1]+1;
                $this->object_names[$key] = $low."-".$high;
            }
        }
        $this->objects[$id] = $object;
        $result= $this->objects[$id];
        uksort($this->objects,"cmp");
        return $result;

    }

    function get($id) { 
        if (isset($this->objects[$id])) {
            return $this->objects[$id];
        }
        else
        {        	
            $params = explode("_",$id);
            $class_name = array_shift($params);
            if ($class_name!="") {
                $not_found = false;
                if (!class_exists($class_name)) {
                    @eval("class $class_name extends WABEntity {};");
                    $this->created[$class_name] = true;
                    $not_found = true;
                }
                if (class_exists($class_name)) {
                	$obj = new $class_name();
                }
                if (isset($obj) and $obj!= null) {
                    $obj->construct($params);
                    if ($not_found) {
                        $obj->not_found = true;
                        $obj->clientClassLoaded = false;
                    } else {
                        $obj->not_found = false;
                        if (!isset($this->created[$class_name]))
                            $obj->clientClassLoaded = true;
                    }
                    return $this->set($id,$obj);
                }
            }
        }
    }

    function remove($id) {
        if (isset($this->objects[$id]))
        {
                unset($this->objects[$id]);
                $vars = explode("_",$id);
                if (count($vars)>1)
                    $obj_name = array_shift($vars);
                else
                    $obj_name = $id;
                uksort($this->object_names,"cmp");
                reset($this->object_names);
                while (key($this->object_names)!=$obj_name)
                    next($this->object_names);
                $vars = explode("-",$this->object_names[$obj_name]);
                $low = $vars[0];
                $high = $vars[1]-1;
                if ($high<$low)
                {
                    unset($this->object_names[$obj_name]);
                    $v = current($this->object_names);
                    if (isset($v))
                    {
                        $key = key($this->object_names);
                        $vars = explode("-",current($this->object_names));
                        $key = key($this->object_names);
                        $low = $vars[0]-1;
                        if (isset($vars[1]))
                            $high = $vars[1]-1;
                        $this->object_names[$key] = $low."-".$high;
                    }
                }
                else
                    $this->object_names[$obj_name] = $low."-".$high;
                while (next($this->object_names))
                {
                    $vars = explode("-",current($this->object_names));
                    $key = key($this->object_names);
                    $low = $vars[0]-1;
                    $high = $vars[1]-1;
                    $this->object_names[$key] = $low."-".$high;
                }
        }
    }
    
    function simpleQuery($class_name,$fields,$condition="",$sort="",$adapter="",$limit="",$entityNumber="") {
        $numEn = $entityNumber;
       	//$entityNumber = 0;
    	if (!$adapter->isPDO) {
            $fieldTypes = array();
            $fieldTypes["strings"]  = "StringField";
            $fieldTypes["integers"] = "IntegerField";
            $fieldTypes["booleans"] = "BooleanField";
            $fieldTypes["decimals"] = "DecimalField";
            $fieldTypes["texts"]    = "TextField";
            $fieldTypes["entities"] = "EntityField";
            $fields = explode(",",$fields);
            $field_array = array();
            foreach($fields as $field) {            
                if ($field=="")
                    continue;
                    $field_parts = explode(" ",$field);
                    $field_array[$field_parts[0]] = $field_parts[count($field_parts)-1];            
            }
            $sort = str_replace("~",",",$sort);
            $sort = explode(",",$sort);
            $sort_array = array();
            foreach($sort as $field) {
                $sort_parts = explode(" ",$field);
                $sort_array[$sort_parts[0]] = $sort_parts[1].",".$sort_parts[2];
            }

            $select_field_strings = "e.id,e.uid,";
            $select_fields = array();
            $sort_fields = array();
            $order_fields = array();
            $join_fields = array();
            $sort_join_fields = array();
            foreach ($field_array as $key=>$value) {
                $select_fields[count($select_fields)] = "f".(count($select_fields)+1).".value as ".$key;
                if ($value!="")
                    $join_fields[count($join_fields)] = "LEFT JOIN ".$fieldTypes[$value]." f".(count($join_fields)+1)." ON (e.id=f".(count($join_fields)+1).".dbEntity_id AND f".(count($join_fields)+1).".name='".$key."')";
            }
            foreach ($sort_array as $key=>$value) {
                $sort_fields[count($sort_fields)] = "so".(count($sort_fields)+1).".value as soc".(count($sort_fields)+1);
                $value_parts = explode(",",$value);
                $sortField = $key;
                $sortDirection = $value_parts[0];
                $sortTable = $fieldTypes[$value_parts[1]];
                $sort_join_fields[count($sort_join_fields)] = "LEFT JOIN ".$sortTable." so".(count($sort_join_fields)+1)." ON (e.id=so".(count($sort_join_fields)+1).".dbEntity_id AND so".(count($sort_join_fields)+1).".name='".$key."')";
                $order_fields[count($order_fields)] = "soc".(count($order_fields)+1)." ".$sortDirection;
            }
            $class_array = explode("_",$class_name);
            if (count($class_array)>1) {
                $class_name = array_shift($class_array);
                $module_id=implode("_",$class_array);            
            }
            else
                $module_id = "";
            if (strpos($class_name,"*")!==false)
                $where = "e.classname LIKE '".str_replace("*","%",$class_name)."'";
            else
                $where = "e.classname='".$class_name."'";
            if ($condition != "") {
                $regs = array();
                $regs[] = "/#(\S+?)@(\S+?)( IS NOT )(NULL)/";
                $regs[] = "/#(\S+?)@(\S+?)( IS )(NULL)/";
                $regs[] = "/#(\S+?)@(\S+?)( NOT LIKE )(\S+)/";
                $regs[] = "/#(\S+?)@(\S+?)( LIKE )('\S+')/";
                $regs[] = "/#(\S+?)@(\S+?)( NOT BETWEEN )(\S+ AND \S+)/";
                $regs[] = "/#(\S+?)@(\S+?)( BETWEEN )(\S+ AND \S+)/";
                $regs[] = "/#(\S+?)@(\S+?)( NOT IN )(\S+)/";
                $regs[] = "/#(\S+?)@(\S+?)( IN )(\S+)/";
                $regs[] = "/#(\S+?)@(\S+?)([=\<\>\!]{1,2})(\S*)/";

                $values = array();
                $matches = array();
                $condition = str_replace("AND","JOIN",$condition);
                $condition = str_replace("OR","LEFT JOIN",$condition);
                $counter=1;
                foreach ($regs as $reg) {
                    while (preg_match($reg,$condition,$matches)) {
                        $str = $matches[0];
                        $type = $matches[1];
                        $field = $matches[2];
                        $operator = $matches[3];
                        $value = $matches[4];
                        $values[] = $value;
                        if ($type=="EntityField")
                            $value_field = "value_id";
                        else
                            $value_field = "value";
                        $replacement = $type." co".$counter." ON (co.id=co".$counter.".dbEntity_id AND co".$counter.".name='".$field."' AND co".$counter.".".$value_field."=?)";
                        $condition = str_replace($str,$replacement,$condition);
                        $counter++;
                    }
                }
                $where .= " AND e.id IN (SELECT co.id FROM DbEntity co JOIN ".$condition.")";
            }
            if ($limit!="") {
                $limit = explode(",",$limit);
                $limit_str = " LIMIT ".$limit[1]." OFFSET ".$limit[0];
            } else
                $limit_str = "";

            $query = "SELECT e.id,e.uid,e.classname, ".implode(",",$select_fields).",".implode(",",$sort_fields)." FROM DbEntity e ".implode(" ",$join_fields)." ".implode(" ",$sort_join_fields)." WHERE ".$where." ORDER BY ".implode(",",$order_fields).$limit_str;
            $em = $adapter->connect(true);
            $stmt = @$em->prepare($query);

            if (isset($values))
                foreach ($values as $key=>$value) {            
                    @$stmt->bindValue($key+1,$value);
                }
            @$stmt->execute();

            global $Objects;        
            $result = array();
            $c = 0;
            while ($row=$stmt->fetch()) {
                if ($entityNumber!="") {
                    $c++;
                    if ($row[0]==$numEn) {
                        $entityNumber = $c;
                        return 0;
                    }
                    continue;
                }
                if ($module_id!="") {                
                    $object_id = $row[2]."_".$module_id."_".str_replace($row[2]."_","",$row[1]);
                } else {
                    $object_id = $row[1];
                }            
                $object = $Objects->get($object_id);
                $object->name = $row[0];
                $counter = 3;
                foreach ($field_array as $key=>$value) {
                    $object->fields[$key] = $row[$counter];
                    $counter++;
                }
                $result[count($result)] = $object;
            }
            if ($limit=="") {
                $limit = count($result);
            } else {
                $query = "SELECT COUNT(e.id) FROM DbEntity e WHERE ".$where;
                $stmt = @$em->prepare($query);
                foreach ($values as $key=>$value) {            
                    @$stmt->bindValue($key+1,$value);
                }
                @$stmt->execute();
                if ($row = $stmt->fetch()) {
                    $limit = $row[0];                
                } else
                    $limit = 0;            
            }
			$entityNumber = 0;
            return $result;        
        } else {
            $sort = trim(str_replace("~",",",$sort));
            $sort = explode(",",$sort);
            $sort_array = array();
            if ($sort!="") {
                foreach($sort as $field) {
                    $sort_parts = explode(" ",$field);
                    $sort_array[] = $sort_parts[0]." ".@$sort_parts[1];
                }
            }
            if (count($sort_array)>0)
                $sort = implode(",",$sort_array);    
            else
                $sort = "";
            $fields = explode(",",$fields);
            $field_array = array();
            foreach($fields as $field) {            
                if ($field=="")
                    continue;
                if (stripos($field,"AS")!==FALSE) {
                    $arr = explode("AS",$field);
                    $key = $arr[0];
                    $arr = explode(" ",$arr[1]);
                    $key .= " AS ".$arr[1];
                    $field_array[] = $key;                                
                } else {
                    $field_parts = explode(" ",$field);
                    $field_array[] = $field_parts[0];            
                }
            }
            $fields = implode(",",$field_array);
            $condition = preg_replace("/#(\S+?)@/U","@",$condition);
            $class_array = explode("_",$class_name);
            if (count($class_array)>1) {
                $class_name = array_shift($class_array);
                $module_id=implode("_",$class_array);            
            }
            else
                $module_id = "";
            if ($condition!="")
                $condition .= " AND";
            if (strpos($class_name,"*")!==false)
                $condition .= " @classname LIKE '".str_replace("*","%",$class_name)."'";
            else
                $condition .= " @classname='".trim($class_name)."'";
            $query1 = "SELECT count FROM fields ";
            $query = "SELECT ".trim($fields)." FROM fields ";
            if ($condition!="") {
                $query .= " WHERE ".trim($condition);
                $query1 .= " WHERE ".trim($condition);
            }
            $query = str_replace(" WHERE AND "," WHERE ",$query);
            $query1 = str_replace(" WHERE AND "," WHERE ",$query1);   
            if (trim($sort)!="")
                $query .= " ORDER BY ".trim($sort);
            
            if ($limit!="")
                $query .= " LIMIT ".trim($limit);      

            $res = PDODataAdapter::makeQuery($query,$adapter,$module_id);     
            $c=0;
            global $Objects;
            $result = array();
            foreach ($res as $item) {
                if ($entityNumber!="") {
                    $c++;
                    if (!is_object($item)) {
						if ($item["entityId"]==$numEn) {
							$entityNumber = $c;
							return 0;
						}
					} else {
						if ($item->name==$numEn) {
							$entityNumber = $c;
							return 0;
						}						
					}
                    continue;
                }
                if ($module_id!="") {
					if (!is_object($item))
						$object_id = $item["classname"]."_".$module_id."_".$item["entityId"];
					else
						$object_id = get_class($item)."_".$module_id."_".$item->name;
				}
                else {
                    $object_id = $item["classname"]."_".$item["entityId"];
				}
                $obj = $Objects->get($object_id);
                $obj->module_id = $module_id;
                $obj->adapter = $adapter;
                foreach ($item as $key=>$value) {
                    if ($value!=null)
                        $obj->fields[$key] = $value;
                }
                $result[count($result)] = $obj;                
            }
            $entityNumber = 0;
            if ($limit=="") {
                $limit = count($result);
            } else
            {
                $res = PDODataAdapter::makeQuery($query1,$adapter,$module_id);
                $limit = $res["count"];
            }
            return $result;
        }
    }

    function query($class_name,$params="",$adapter="",$sort="",$limit="",$entityNumber="") {
        global $Objects;
        // Если передан адаптер данных, то запрашиваем объекты из БД
        if ($adapter!="") {
            $params_array = explode("|",$params);
            $simple = false;
            if (count($params_array)>1) {
                $simple = array_shift($params_array);
                if ($simple=="simple") {
                    $fields = array_shift($params_array);
                    $fields = str_replace("~",",",$fields);
                    $params = implode("|",$params_array);                    
                    return @$this->simpleQuery($class_name,$fields,$params,$sort,$adapter,&$limit,&$entityNumber);
                }
            }
            if (!$adapter->isPDO) {
                $em = $adapter->connect();
                global $reg_count;
                $reg_count = 0;
                if ($params=="")
                    $empty = true;
                else
                    $empty = false;
                if ($params=="")
                    $params_empty = true;
                else
                    $params_empty = false;
                if ($class_name!="") {
                    $class_arr = explode("_",$class_name);
                    $class_name = array_shift($class_arr);                
                    if (count($class_arr)>0)
                        $module_id = implode("_",$class_arr);                
                    if (strpos($class_name,"*")==0 and $class_name[0]!="*")
                        $params = $params." AND e.classname='".$class_name."'";
                    else
                        $params = $params." AND e.classname LIKE '".str_replace("*","%",$class_name)."'";
                }            
                if ($params_empty)
                    $params = str_replace("AND ","",$params);
                // если нужно сортировать, сортируем
                $sortDirection = "";
                if ($sort!="") {
                    $sort_array = explode(",",$sort);
                    $sortDirection = " ORDER BY ";
                    $sort_direction_array = array();
                    $sort_joins_array = array();
                    $ec = 0;
                    for ($c=0;$c<count($sort_array);$c++) {
                        $sort_parts = explode(" ",$sort_array[$c]);
                        if (strpos($sort_parts[0],".")!==FALSE) {
                            $ec = $ec+1;
                            $sort_fields_array = explode(".",$sort_parts[0]);
                            $sortField = array_pop($sort_fields_array);
                            $sort_joins_array[] = "et.value e".$ec." WITH (et.name='".$sort_fields_array[0]."')";
                            array_shift($sort_fields_array);
                            $adapter->sortFields .= ",e".$ec;
                            foreach ($sort_fields_array as $fld) {
                                $sort_joins_array[] = "e$ec.entities ee".($ec+1)." WITH (ee".($ec+1).".name='".$fld."') LEFT JOIN ee".($ec+1).".value e".($ec+1);
                                $ec++;
                                $adapter->sortFields .= ",e".$ec;
                                $adapter->sortFields .= ",ee".$ec;
                            }
                            $so = "e".$ec;
                        }
                        else {
                            $so = "e";
                            $sortField = $sort_parts[0];
                        }
                        $adapter->sortFields .= ",so".$c;
                        $sort_direction_array[] = "so".$c.".value ".$sort_parts[1];
                        $sort_joins_array[] = "$so.".$sort_parts[2]." so".$c." WITH (so".$c.".name='".$sortField."')";
                    }
                    $sortDirection .= implode(",",$sort_direction_array);
                    $adapter->sortJoins = " LEFT JOIN ".implode(" LEFT JOIN ",$sort_joins_array);
                }            
                if ($empty)
                    $params = str_replace(" AND","",$params);

                $params .= $sortDirection;
                $params = $adapter->makeQuery($params);
                $result = @$adapter->dqlQuery($params);
                // Если запрос вернул результат, то создаем сущности в глобальном кэше
                // объектов и загружаем их;
                $res = array();
                if ($limit!="") {
                    $limit_parts = explode(",",$limit);
                    $offset = $limit_parts[0];
                    $limit = $limit_parts[1];
                }
                else {
                    $offset = 0;
                    $limit = 0;
                }            
                $c = 0;
                if ($entityNumber=="") {
                    foreach($result as $dbEntity) {
                        if ($limit!=0) {
                            if ($c<$offset) {
                                $c++;
                                continue;
                            }
                            if ($c>$offset+$limit-1) {
                                break;
                            }
                        }
                        if (isset($module_id)) {
                            $uid_arr = explode("_",$dbEntity->getUid());
                            $classname = array_shift($uid_arr);
                            $uid = $classname."_".$module_id."_".implode("_",$uid_arr);                         
                            $obj = $this->get($uid);
                        } else {
                            $obj = $this->get($dbEntity->getUid());
                        }
                        $obj->load("",$dbEntity);
                        $res[] = $obj;
                        $c++;
                    }
                } else {
                    foreach($result as $dbEntity) {
                        $c++;
                        if ($dbEntity->getUid()==$entityNumber) {
                            $entityNumber = $c;
                            return 0;
                        }
                    }
                    return 0;
                }
                $adapter->sortFields = "";
                $adapter->sortJoins = "";
                if ($limit == "")
                    $limit = count($result);
            } else {
                if (!$adapter->connected)
                    $adapter->connect();
                if (!$adapter->connected)
					return 0;
                $class_array = explode("_",$class_name);
                if (count($class_array)>1) {
                    $class_name = array_shift($class_array);
                    $module_id=implode("_",$class_array);            
                }
                else
                    $module_id = "";
                $condition = $params;
                if (strpos($class_name,"*")!==false)
                    $condition .= " AND @classname LIKE '".str_replace("*","%",$class_name)."'";
                else
                    $condition .= " AND @classname='".$class_name."'";
                $query = "SELECT entities FROM fields ";
                if ($condition!="")
                    $query .= " WHERE ".$condition;
                if ($sort!="")
                    $query .= " ORDER BY ".$sort;
                $query1 = $query;
                $res = PDODataAdapter::makeQuery($query,$adapter,$module_id);
                $c=0;
                global $Objects;
                $result = array();
                if ($limit!="") {
                    $limit_parts = explode(",",$limit);
                    $offset = $limit_parts[0];
                    $limit = $limit_parts[1];
                }
                else {
                    $offset = 0;
                    $limit = 0;
                }            
                if ($entityNumber=="") {
                    foreach ($res as $item) {
                        if ($limit!=0) {
                            if ($c<$offset) {
                                $c++;
                                continue;
                            }
                            if ($c>$offset+$limit-1) {
                                break;
                            }
                        }
                        $result[count($result)] = $item;                
                    }
                } else {
                    foreach ($res as $item) {
                        if ($entityNumber!="") {
                            $c++;
                            if ($item->entityId==$entityNumber) {
                                $entityNumber = $c;
                                return 0;
                            }
                            continue;
                        }
                    }                    
                }
                $limit = count($res);
                return $result;
            }
        // если адаптер данных не передан, то данные будем запрашивать из глобального
        // кэша объектов, которые уже загружены в память
        } else {
        	// Если в качестве имени класса передана строка в формате "procedure|ИмяКласса|ИмяПроцедуры|Массив-параметров-в-формате-JSON
        	// то вызываем этот метод. Он возвращает массив сущностей, который нам нужен 
        	$arr = explode("|",$class_name);
        	if ($arr[0]=="procedure") {
        		$objectName = $arr[1];
        		$methodName = $arr[2];
        		$params = @$arr[3];
        		if ($params!="")
        			$params = json_decode($params);
        		$obj = $this->get($objectName);
        		$res = array();
        		if (is_object($obj))
        			$res = $obj->$methodName($params);
        	// иначе выбираем все объекты этого класса из глобального кэша объектов
        	} else {
        		$class_name = array_shift(explode("_",$class_name));
	            uksort($this->objects,"cmp");
	            uksort($this->object_names,"cmp");
	            if (!isset($this->object_names[$class_name]))
	                    return array();
	            $result = array();
	            // сначала выбираем все объекты указанного класса
	            $vars = explode("-",$this->object_names[$class_name]);
	            if ($vars[0]==$vars[1])
	                $res = array_slice($this->objects,$vars[0],1);
	            else {
	                if ($vars[0]!=0)
	                    $res = array_slice($this->objects,$vars[0],$vars[1]-$vars[0]+1);
	                else
	                    $res = array_slice($this->objects,$vars[0],$vars[1]-$vars[0]+1);
	            }
        	}
            // если есть условия запроса
            if ($params!="") {
                // если они переданы в виде массива (устаревший вариант,
                // оставлен для совместимости)
                if (is_array($params)) {
                    // отсеиваем сущности со свойствами, не совпадающими с элементами массива
                    foreach ($res as $key=>$value) {
                            $vars = get_object_vars($value);
                            $is_yes = true;
                            foreach ($params as $key2=>$value2) {
                                if (isset($vars[$key2])) {
                                    if ($vars[$key2]!=$value2) {
                                            $is_yes = false;
                                            break;
                                    }
                                }
                                if (isset($value->fields[$key2])) {
                                    if ($value->fields[$key2]!=$value2) {
                                            $is_yes = false;
                                            break;
                                    }
                                }
                            }
                            if (!$is_yes) {
                                unset($res[$key]);
                            }
                    }
                    // если условия переданы в виде строки
                } else if ($params!="") {
                    // пока лишнее
                    $params = explode("|",$params);
                    $params = $params[0];
                    // Заменяем условие так, чтобы это стало стандартным условным
                    // оператором PHP
                    $params = str_replace("@",'@$obj->',$params);
                    foreach ($res as $key=>$obj) {
                        // исполняем это условие для каждого из найденных объектов
                        // $obj->, и если оно возвращает false, значит объект не
                        // соответствует условию и исключается из выборки
                        if (!eval('return '.$params.";"))
                            unset($res[$key]);
                    }
                }
            }
            // сортируем результаты, если они есть и нужна сортировка            
            if (count($res)>1 and $sort!="") {
                $sort_fields = explode(",",$sort);
                $sort_fields = array_reverse($sort_fields);
                $result = array();
                foreach ($sort_fields as $sort_field) {
                    $sort_field_parts = explode(" ",$sort_field);
                    $c=0;
                    foreach ($res as $value) {
                        if (isset($result[$value->fields[$sort_field_parts[0]]]))
                            $result[$value->fields[$sort_field_parts[0]]."_".$c] = $value;
                        else
                            $result[$value->fields[$sort_field_parts[0]]] = $value;
                        $c++;
                    }
                    if (strtoupper($sort_field_parts[1])=="DESC") {
                        krsort($result);
                    } else {
                        ksort($result);
                    }
                    $res = array();
                    $c = 0;
                    $names = array();
                    foreach ($result as $value) {
                    	if ($entityNumber!="") {
                    		if ($value->getId()==$entityNumber) {
                    			$entityNumber = $c;
                    			return 0;
                    		}
                    	}
                    	if (!isset($names[$value->name])) {
                    		$res[] = $value;
                    		$names[$value->name] = $value->name;
                    		$c++;
                    	} 
                    }
                    $result = array();
                }
            }
            if ($limit!="") {
            	$parts = explode(",",$limit);
            	$page = $parts[0];
            	$count = $parts[1];
            	$limit = count($res);
            	$res = array_slice($res, $page, $count);
            } else
            	$limit = count($res);            
        }       
        if (isset($res))
            return $res;
    }

    function count($class_name) {
        if (isset($this->object_names[$class_name]))
        {
            $vars = explode("-",$this->object_names[$class_name]);
            return $vars[1]-$vars[0]+1;
        }
        else return 0;
    }

    function contains($id) {
        if (isset($this->objects[$id]))
                return true;
        else
            return false;
    }
    
    function removeList($list) {
        if (!is_array($list))
            $list = explode(",",$list);
        $removed_list = array();
        $error_text = "";
        foreach ($list as $entity) {
            $obj = $this->get($entity);
            $obj->load();
            $obj_presentation = $obj->getPresentation();
            $obj_id = $obj->getId();
            ob_start();
            $obj->afterSave(false);
            $obj->remove();            
            $output = ob_get_contents();
            ob_end_clean();
            if ($output=="")
                $removed_list[] = $obj_id;
            else {
                $result = json_decode($output,true);
                if ($result)
                    $error_text .= $obj_presentation.":\n\n".$result["error"]."\n\n";
                else
                    $error_text .=  $obj_presentation.":\n\n".$output."\n\n";                
            }
        }
        if (@$_POST["ajax"]==true)        
            return json_encode(array("removed_objects" => implode("~",$removed_list), "error_text" => $error_text));
        else
            return array("removed_objects" => $removed_list, "error_text" => $error_text);
    }
}
?>