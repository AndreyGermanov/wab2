<?php
$reg_count = 0;
$process_array = false;
$array_name = "";

class CacheDataAdapter extends DataAdapter {    
    public $classes,$field_types,$type_classes,$type_arrays;
    
    function construct($params) {

        if (count($params)>2)
            $this->module_id = array_shift($params)."_".array_shift($params);
        else
            $this->module_id = "";
        $this->name = implode("_",$params);
        $this->old_name = $this->name;

        $this->driver = "pdo_sqlite";
        //$this->memory = true;
        $this->user = "";
        $this->password = "";
        $this->path = "cachedb/cachedb.sqlite";
        $this->classes = array();
        $this->classes[count($this->classes)] = "DbEntity";
        $this->classes[count($this->classes)] = "StringField";
        $this->classes[count($this->classes)] = "IntegerField";
        $this->classes[count($this->classes)] = "BooleanField";
        $this->classes[count($this->classes)] = "EntityField";
        $this->classes[count($this->classes)] = "ArrayField";
        $this->classes[count($this->classes)] = "DecimalField";
        $this->classes[count($this->classes)] = "TextField";

        $this->field_types = array();
        $this->field_types[count($this->field_types)] = "strings";
        $this->field_types[count($this->field_types)] = "integers";
        $this->field_types[count($this->field_types)] = "booleans";
        $this->field_types[count($this->field_types)] = "entities";
        $this->field_types[count($this->field_types)] = "arrays";
        $this->field_types[count($this->field_types)] = "decimals";
        $this->field_types[count($this->field_types)] = "texts";

        $this->type_classes = array();
        $this->type_classes["strings"] = "StringField";
        $this->type_classes["integers"] = "IntegerField";
        $this->type_classes["booleans"] = "BooleanField";
        $this->type_classes["entities"] = "EntityField";
        $this->type_classes["arrays"] = "ArrayField";
        $this->type_classes["decimals"] = "DecimalField";
        $this->type_classes["texts"] = "TextField";

        $this->type_arrays["string"] = "strings";
        $this->type_arrays["integer"] = "integers";
        $this->type_arrays["boolean"] = "booleans";
        $this->type_arrays["decimal"] = "decimals";
        $this->type_arrays["text"] = "texts";
        $this->type_arrays["entity"] = "entities";
        $this->type_arrays["array"] = "arrays";

        $this->entities_path = "classes/dbClasses/Entities";
        $this->proxies_path = "classes/dbClasses/Proxies";
        $this->loaded = false;

        $this->query = "SELECT e,s,i,b,et,d,t,a,ast,ain,ab,ad,at,ent{sortFields} FROM DBEntity e LEFT JOIN e.strings s WITH s.arrayItem IS NULL INDEX BY s.name LEFT JOIN e.integers i WITH i.arrayItem IS NULL INDEX BY i.name".
                       " LEFT JOIN e.booleans b WITH b.arrayItem IS NULL INDEX BY b.name LEFT JOIN e.entities et WITH et.arrayItem IS NULL INDEX BY et.name LEFT JOIN e.decimals d WITH d.arrayItem IS NULL INDEX BY d.name".
                       " LEFT JOIN e.texts t WITH t.arrayItem IS NULL INDEX BY t.name LEFT JOIN e.arrays a INDEX BY a.name LEFT JOIN a.strings ast WITH ast.arrayItem IS NOT NULL INDEX BY ast.name".
                       " LEFT JOIN a.integers ain WITH ain.arrayItem IS NOT NULL INDEX BY ain.name LEFT JOIN a.booleans ab WITH ab.arrayItem IS NOT NULL INDEX BY ab.name LEFT JOIN a.decimals ad WITH ad.arrayItem IS NOT NULL INDEX BY ad.name".
                       " LEFT JOIN a.texts at WITH at.arrayItem IS NOT NULL INDEX BY at.name LEFT JOIN a.entities ent WITH ent.arrayItem IS NOT NULL INDEX BY ent.name {sortJoins} WHERE ";

        $this->sortFields="";
        $this->sortJoins="";
        $this->sortDirections = "";
        $this->clientClass = "CacheDataAdapter";
        $this->parentClientClasses = "DataAdapter~Entity";        
    }

    function loadArray($ownerField) {
        global $Objects;
        $result = array();
        foreach ($this->field_types as $ft) {
            foreach ($ownerField->getFieldValue($ft) as $str) {
                if ($ft!="arrays") {
                        if ($str->getArrayItem()==null)
                                continue;
                        $value = $str->getValue();
                    if (gettype($value)=="object") {
                        $dbe = $value;
                        $value = $Objects->get($value->getUid());
                        $value->load($this->em,$dbe);
                    }
                    if ($ft!="strings")
                        $result[$str->getName()] = $value;
                    else
                        $result[$str->getName()] = htmlentities($value,ENT_QUOTES,"UTF-8");
                } else
                    $result[$str->getName()] = $this->loadArray($str);
            }
        }
        return $result;
    }

    function load($em="",$dbEntity="") {
        return 0;
        global $Objects;
        $dbEntity="";
        if ($dbEntity=="") {
            if ($em=="") {
                $em = $this->connect();
            }
            $this->em = $em;
            $query = strtr($this->query,array("{sortFields}"=>$this->sortFields,
                                              "{sortJoins}"=>$this->sortJoins,
                                              "{sortDirections}"=>$this->sortDirections));
            $q = $em->createQuery($query." e.uid = :uid");
            if ($this->entity->module_id!="")
                $q->setParameter('uid',str_replace($this->entity->module_id."_","",$this->entity->getId()));
            else
                $q->setParameter('uid',$this->entity->getId());
            $q->useResultCache(true);
            try {
                $this->dbEntity = $q->getSingleResult();
            } catch (Exception $e) {

            }
        }
        else
            $this->dbEntity = $dbEntity;
        if ($this->dbEntity==null)
            $this->dbEntity = "";
        if ($this->dbEntity!="") {
            if ($this->entity->persistedFields!="")
            foreach ($this->field_types as $ft) {
                foreach ($this->dbEntity->getFieldValue($ft) as $str) {
                    if ($ft!="arrays") {
                            $value = $str->getValue();
                        if (gettype($value)=="object" and !method_exists($value, "getPresentation")) {
                            $dbe = $value;
                            if ($this->entity->module_id!="") {
                                $uid_arr = explode("_",$value->getUid());
                                $className = array_shift($uid_arr);
                                $uid = $className."_".$this->entity->module_id."_".implode("_",$uid_arr);
                                $value = $Objects->get($uid);
                            } else
                                $value = $Objects->get($value->getUid());
                            $value->load($em,$dbe);
                        }   
                        if ($ft=="strings")
                            $this->entity->fields[$str->getName()] = htmlentities($value,ENT_QUOTES,'UTF-8');
                        else
                            $this->entity->fields[$str->getName()] = $value;
                    } else {
                        $this->entity->fields[$str->getName()] = $this->loadArray($str);
                    }
                }
            }

            $this->oldPersistedFields = $this->persistedFields;
            $this->loaded = true;
        }
        return $this->loaded;        
    }

    function saveArray($em,$dbEntity,$ownerField,$name,$array_value) {
            $arr = $ownerField->getFieldValue("arrays")->get($name);
            if ($arr == null) {
                $arr = new ArrayField();
            }
            $arr->setName($name);
            $arr->setDbEntity($dbEntity);
            if ($dbEntity!=$ownerField)
                $arr->setArrayItem($ownerField);
            
            $fields = array();
            foreach ($this->field_types as $ft) {
                $fields[$ft] = array();
            }

            foreach ($array_value as $key=>$value) {
                $key = (string)$key;
//                $value = htmlentities($value,ENT_QUOTES);
                switch (gettype($value)) {
                    case "string":
                        if (strlen($value)<=255)
                            $fields["strings"][$key] = html_entity_decode($value,ENT_QUOTES,"UTF-8");
                        else
                            $fields["texts"][$key] = $value;
                        break;
                    case "integer":
                        $fields["integers"][$key] = $value;
                        break;
                    case "boolean":
                        $fields["booleans"][$key] = $value;
                        break;
                    case "double":
                        $fields["decimals"][$key] = $value;
                        break;
                    case "object":
                        if ($key!="adapter" && method_exists($value,"getId")) {
                            $value->load();
                            if (!$value->loaded)
                                $value->save();
                            $fields["entities"][$key] = $value->adapter->dbEntity;                            
                        }
                        break;
                    case "array":
                        $fields["arrays"][$key] = $this->saveArray($em,$dbEntity,$arr,$key,$value);
                        break;
                }
            }

            foreach ($fields as $type_key=>$type_value) {
                foreach ($type_value as $key=>$value) {
                    $key = (string)$key;
                    if ($key=="old_name" or $key=="adapter" or $key=="loaded" or $key=="oldPersistedFields") {
                        continue;
                    }
                    $str = $arr->getFieldValue($type_key)->get($key);
                    if ($str == null) {
                        $cls = $this->type_classes[$type_key];
                        $str = new $cls();
                    } 
                    $str->setName($key);

                    if ($type_key=="entities") {
                        if ($em->getUnitOfWork()->getEntityState($value)==\Doctrine\ORM\UnitOfWork::STATE_DETACHED) {
                            $value = $em->merge($value);
                        }
                    }
                    $str->setDbEntity($dbEntity);
                    $str->setArrayItem($arr);
                    if ($type_key!="arrays") {
                        $str->setValue($value);
                        $arr->getFieldValue($type_key)->set($key,$str);
                    } else {
                        $arr->getFieldValue($type_key)->set($key,$value);
                    }
                }
            }

            foreach ($fields as $type_key=>$type_value) {
                $persisted_fields = $arr->getFieldValue($type_key)->getKeys();
                foreach($persisted_fields as $key) {
                    if (!isset($type_value[$key])) {
                        $em->remove($arr->getFieldValue($type_key)->get($key));
                        $arr->getFieldValue($type_key)->remove($key);
                    }
                }
            }
            //$em->persist($arr);
            return $arr;
    }

    function save() {
        global $Objects,$dataAdapterClass;
        $em = $this->connect();
        $this->em = $em;
        if ($em instanceof \Doctrine\ORM\EntityManager) {
            if ($this->dbEntity=="") {
                $query = strtr($this->query,array("{sortFields}"=>$this->sortFields,
                                                  "{sortJoins}"=>$this->sortJoins,
                                                  "{sortDirections}"=>$this->sortDirections));
                $q = $em->createQuery($query." WHERE e.id = :id");
                $q->setParameter('id',$this->entity->name);               
                    
                $q->useResultCache(true);
                try {
                    $this->dbEntity = $q->getSingleResult();
                } catch (Exception $e) {

                }
            }
            if ($this->dbEntity==null)
                $this->dbEntity = new DbEntity();
            if ($this->entity->old_name != $this->entity->name and $this->entity->name!="") {
                $Objects->remove($this->entity->getId());
                $Objects->remove($dataAdapterClass."_".$this->entity->getId());
                $this->entity->old_name = $this->entity->name;
                $Objects->set($this->entity->getId(),$this->entity);
                $Objects->set($dataAdapterClass."_".$this->entity->getId(),$this);
            }
            $this->dbEntity->setClassname(get_class($this->entity));
            if ($this->entity->module_id!="")
                $this->dbEntity->setUid(str_replace($this->entity->module_id."_","",$this->entity->getId()));
            else
                $this->dbEntity->setUid($this->entity->getId());
            if ($this->entity->persistedFields!="") {
                $persistedArray = $this->entity->getPersistedArray();
                $oldPersistedArray = $this->entity->getOldPersistedArray();
//                if (isset($persistedArray["parent"])) {
//                    if (!isset($this->entity->fields["parent"])) {
//                        $this->entity->fields["parent"] = -1;
//                    }
//                }
            }
            foreach ($this->entity->fields as $key=>$value) {
                if (isset($persistedArray)) {
                    if (!isset($persistedArray[$key]) && $key!="module_id" && $key!="siteId") {
                        if (isset($oldPersistedArray[$key])) {
                            $this->entity->fields[$oldPersistedArray[$key]] = $this->entity->fields[$key];
                        }
                        unset($this->entity->fields[$key]);
                        continue;
                    }
                }
                if ($value == "")
                    unset($this->entity->fields[$key]);
                if (gettype($value)=="array") {
                    if (count($value)==0)
                        unset($this->entity->fields[$key]);
                }
            }

            $fields = array();
            foreach ($this->field_types as $ft) {
                $fields[$ft] = array();
            }
            foreach ($this->entity->fields as $key=>$value) {
//                $value = htmlentities($value,ENT_QUOTES);
                if ($key=="module_id" || $key=="siteId")
                    continue;
                if (isset($persistedArray)) {
                    $arr = explode("|",$persistedArray[$key]);
                    if (isset($arr[0]) and $arr[0]!="") {
                        $value_type = gettype($value);
                        if ($value_type=="object")
                            $value_type = "entity";
                        //echo @$key."-".@$value."-".@$value_type."-".@$arr[0]."<br>";
                        if ($value_type != $arr[0]) {
                            if ($value_type == "array")
                                $value = "";
                            else if ($value_type == "entity" and method_exists($value, "getPresentation")) {
                                if ($arr[0]=="array")
                                    $value = array();
                                if ($arr[0]=="string")
                                    $value = $value->getId();
                                else
                                    $value = "";
                            } else if ($arr[0]=="entity" and $value_type != "array") {                                
                                if (gettype($value)=="string" and $value!=-1) {
                                    $value = $Objects->get($value);
                                    if (!$value->loaded and method_exists($value,"getPresentation"))
                                        $value->load();
                                }
                            }
                            else {
                                if ($arr[0]!="text") {
                                    if ($arr[0]=="decimal")
                                        settype($value,"double");
                                    else if ($arr[0]=="file" or $arr[0]=="path")
                                        settype($value,"string");
                                    else
                                        settype($value,$arr[0]);
                                }
                            }                            
                        }
                        if ($key=="parent" and ($value_type=="integer" or $value=="-1")) {
                            $fields["integers"]["parent"] = -1;
                        } else {
                            if ($arr[0]=="entity") {
                                if ($key!="adapter" && method_exists($value,"getPresentation")) {
                                    if ($value->adapter->dbEntity=="") {
                                        $value->save();
                                    }
                                    $fields["entities"][$key] = $value->adapter->dbEntity;
                                }
                            } else {
                                if ($arr[0]=="string")
                                    $fields[$this->type_arrays[$arr[0]]][$key] = html_entity_decode($value,ENT_QUOTES,"UTF-8");                               
                                else
                                    $fields[$this->type_arrays[$arr[0]]][$key] = $value;                               
                            }
                        }
                        
                        if ($arr[0]=="array") {
                            $fields["arrays"][$key] = $this->saveArray($em,$this->dbEntity,$this->dbEntity,$key,$value);
                        }
                        continue;
                    }
                }
                switch (gettype($value)) {
                    case "string":
                        if (strlen($value)<=255) {
                            $fields["strings"][$key] = html_entity_decode($value,ENT_QUOTES,"UTF-8");
                        }
                        else {
                            $fields["texts"][$key] = $value;
                        }
                        break;
                    case "integer":
                        $fields["integers"][$key] = $value;
                        break;
                    case "boolean":
                        $fields["booleans"][$key] = $value;
                        break;
                    case "double":
                        $fields["decimals"][$key] = $value;
                        break;
                    case "object":
                        if ($key!="adapter" && method_exists($value,"getId")) {
                            if ($value->adapter->dbEntity=="") {
                                $value->save();
                            }
                            $fields["entities"][$key] = $value->adapter->dbEntity;
                        }
                        break;
                    case "array":
                        $fields["arrays"][$key] = $this->saveArray($em,$this->dbEntity,$this->dbEntity,$key,$value);
                        break;
                }
            }
            foreach ($fields as $type_key=>$type_value) {
                foreach ($type_value as $key=>$value) {
                    if ($key=="old_name" or $key=="adapter" or $key=="loaded" or $key=="oldPersistedFields")
                        continue;
                    $str = $this->dbEntity->getFieldValue($type_key)->get($key);
                    if ($str == null) {
                        $cls = $this->type_classes[$type_key];
                        $str = new $cls();
                    } 
                    $str->setName($key);
                    if ($type_key=="entities") {
                        if ($em->getUnitOfWork()->getEntityState($value)==\Doctrine\ORM\UnitOfWork::STATE_DETACHED)
                            $value = $em->merge($value);
                    }
                    $str->setDbEntity($this->dbEntity);
                    if ($type_key!="arrays") {
                        if ($str->getArrayItem()!=null) {
                                continue;
                        }
                        $str->setValue($value);
                        $this->dbEntity->getFieldValue($type_key)->set($key,$str);
                    } else
                        $this->dbEntity->getFieldValue($type_key)->set($key,$value);
                }
            }

            foreach ($fields as $type_key=>$type_value) {                
                $persisted_fields = $this->dbEntity->getFieldValue($type_key)->getKeys();
                foreach($persisted_fields as $key) {
                    if (!isset($type_value[$key])) {
                        $em->remove($this->dbEntity->getFieldValue($type_key)->get($key));
                        $this->dbEntity->getFieldValue($type_key)->remove($key);
                    }
                }
            }
            $em->persist($this->dbEntity);
            $em->flush();
            $this->entity->loaded = true;
            if ($this->entity->name == "") {                
                $this->entity->name = $this->dbEntity->getId();
                $Objects->remove($this->entity->getId());
                $Objects->remove(get_class($this)."_".$this->entity->getId());
                $this->entity->old_name = $this->entity->name;
                $Objects->set($this->entity->getId(),$this->entity);
                $Objects->set(get_class($this)."_".$this->entity->getId(),$this);
                $this->entity->adapter = $this;
                if ($this->entity->module_id!="")
                    $this->dbEntity->setUid(str_replace($this->entity->module_id."_","",$this->entity->getId()));
                else
                    $this->dbEntity->setUid($this->entity->getId());
                $str = new IntegerField();
                $str->setName("name");
                $str->setDbEntity($this->dbEntity);
                $str->setValue($this->entity->name);
                $this->dbEntity->getFieldValue("integers")->set("name",$str);
                $em->persist($this->dbEntity);
                $em->flush();                              
                echo $this->entity->name;
            }
            $this->entity->old_name = $this->entity->name;
            return $this->loaded;
        }
    }
    
    function getEntityLinks($em=null,$name = "") {
        $results = array();
        if ($em==null)
            $em = $this->connect();
        $id = $this->entity->getId();
        if ($this->entity->module_id!="")
            $id = str_replace($this->entity->module_id."_","",$id);
        $this->dbEntity = $em->getRepository("DbEntity")->findOneByUid($id);
        if ($this->dbEntity!=null) {
            if ($name=="")
                $query = "SELECT f FROM EntityField f WHERE f.value=:entity";
            else
                $query = "SELECT f FROM EntityField f WHERE f.value=:entity AND f.name=:name";
            $q = $em->createQuery($query);
            $q->setParameter("entity",$this->dbEntity);
            if ($name != "")
                $q->setParameter("name",$name);
                
            $res = array();
            $res = $q->getResult();
            if (count($res)>0) {
                global $Objects;
                foreach ($res as $ent) {
                    $entity = $Objects->get($ent->getDbEntity()->getUid());
                    $entity->load();
                    $rs = array();
                    $rs["entity"] = $entity;
                    $rs["field"] = $ent->getName();
                    $results[] = $rs;
                }
            }
        }            
        return $results;        
    }

    function remove() {
        $em = $this->connect();
        if ($em instanceof \Doctrine\ORM\EntityManager) {
            $id = $this->entity->getId();
            if ($this->module_id!="")
                $id = str_replace($this->module_id."_","",$id);
            $this->dbEntity = $em->getRepository("DbEntity")->findOneByUid($id);
            if ($this->dbEntity!=null) {
                $results = $this->getEntityLinks();
                if (count($results)>0)
                    return $results;
                $em->remove($this->dbEntity);
                $em->flush();
            }
        }
        return 0;
    }

    function dqlQuery($condition) {
        $em = $this->connect();
        $this->em = $em;        
        $query = strtr($this->query,array("{sortFields}"=>$this->sortFields,
                                          "{sortJoins}"=>$this->sortJoins,
                                          "{sortDirections}"=>$this->sortDirections));
//       echo $query." ".$condition;
        $query = @$em->createQuery($query." ".$condition);
////        //echo $query->getSQL();
       $query->useResultCache(true);
       //echo $query->getResult();
       return @$query->getResult();
    }
    
    function getMaxValue($field,$classname,$condition) {
        $this->sortFields = ",i1";
        $condition = $this->makeQuery($condition); 
        if ($condition!="") {
            $condition .= " AND (i1.name='$field' AND i1.value!=0)";
            if ($classname!="")
                $condition = $condition." AND e.classname LIKE '".str_replace("*","%",$classname)."'";
        } else {
            if ($classname!="")
                $condition = "e.classname LIKE '".str_replace("*","%",$classname)."'";
        }
        $this->sortJoins = " LEFT JOIN e.integers i1 ";
//        echo $condition;
        $results = $this->dqlQuery($condition); 
//        echo count($results);
//        echo "ya";
        $max = 0;
        foreach ($results as $result) {
            foreach($result->getIntegers() as $str) {
//                echo $str->getName()."<br>";
                if ($str->getName() == $field) {
                    if ($str->getValue()>$max)
                        $max = $str->getValue();
                }
            }
        }
        return $max;
    }
    
    function getTypeSymbol($value,$is_array=false) {
        if ($value=="")
            return "empty";
        if ($value[0]=="(")
            $value = str_replace("(","",str_replace(")","",$value));
        if ($value[0]=="'") {
            if (!$is_array)
                return "s";
            else
                return "ast";
        }
        if (strtoupper(substr($value,0,4))=="TRUE" or strtoupper(substr($value,0,4))=="FALSE") {
            if (!$is_array)
                return "b";
            else
                return "ab";
        }
        if (stripos($value,".")!=0) {
            if (!$is_array)
                return "d";
            else
                return "ad";
        }
        if (!$is_array)
            return "i";
        else
            return "ain";
    }

    function getTypeClass($value,$is_array=false) {
        $type = $this->getTypeSymbol($value,$is_array);
        switch ($type) {
            case "s":
                return "StringField";
                break;
            case "b":
                return "BooleanField";
                break;
            case "d":
                return "DecimalField";
            case "i":
                return "IntegerField";
                break;
            case "ast":
                return "StringField";
                break;
            case "ab":
                return "BooleanField";
                break;
            case "ad":
                return "DecimalField";
            case "ain":
                return "IntegerField";
                break;
        }
    }

    function makeQuery($params,$new=true) {
        global $reg_count;
        $params = str_replace("->",".",$params);
        $params = str_replace("==","=",$params);
        $regs = array();
        $regs[] = "/@(\S+?)( IS NOT )(NULL)/";
        $regs[] = "/@(\S+?)( IS )(NULL)/";
        $regs[] = "/@(\S+?)( NOT LIKE )(\S+)/";
        $regs[] = "/@(\S+?)( LIKE )('\S+')/";
        $regs[] = "/@(\S+?)( NOT BETWEEN )(\S+ AND \S+)/";
        $regs[] = "/@(\S+?)( BETWEEN )(\S+ AND \S+)/";
        $regs[] = "/@(\S+?)( NOT IN )(\S+)/";
        $regs[] = "/@(\S+?)( IN )(\S+)/";
        $regs[] = "/@(\S+?)([=\<\>\!]{1,2})(\S*)/";
        foreach ($regs as $reg) {
            $matches = array();
            while (preg_match($reg,$params,$matches)) {
                $result = $this->replaceIt($matches,$new);
                $reg_count++;
                $params = preg_replace($reg,$result,$params,1);
            }
        }
        return $params;
    }

    function replaceIt($args,$new=false) {
        global $reg_count,$process_array,$array_name;
        $name = $args[1];
        $operation = $args[2];
        $value = $args[3];
    
        if (stripos($name,".")==0) {
            if (stripos($name,"[")!=0)
                $is_array = true;
            else
                $is_array = false;

            if (stripos($name,"#")!=0) {
                    if (!$is_array)
                        $type = "t";
                    else
                        $type = "at";
            }
            else
                $type = $this->getTypeSymbol($value,$is_array);
            if ($reg_count>0 and !$new) {
    
                if (!$is_array)
                    $type = "et".$reg_count;
            }
            $name = str_replace("#","",$name);
            if ($is_array) {
                $name_array = explode("[",$name);
                $array_name = array_shift($name_array);
                for ($c=0;$c<count($name_array);$c++) {
                    $name_array[$c] = str_replace("]","",str_replace("'","",$name_array[$c]));
                }
                if (count($name_array)==1) {
                    if ($name_array[0]!="")
                        return "(".$type.".name='".$name_array[0]."' AND ".$type.".value".$operation."".$value." AND ".$type.".arrayItem IN (SELECT a_1".$reg_count." FROM ArrayField a_1".$reg_count." WHERE a_1".$reg_count.".name='".$array_name."'))";
                    else
                        return "(".$type.".value".$operation."".$value." AND ".$type.".arrayItem IN (SELECT a_1".$reg_count." FROM ArrayField a_1".$reg_count." WHERE a_1".$reg_count.".name='".$array_name."'))";
                }
                else {
                    $reg_count++;
                    $res = "";
                    $name_array = array_reverse($name_array);
                    for ($c=0;$c<count($name_array)-1;$c++) {
                        $rc1 = $reg_count+$c;
                        $rc2 = $rc1+1;
                        if ($name_array[$c+1]!="")
                            $res .= "a_1".($rc1).".name='".$name_array[$c+1]."' AND a_1".($rc1).".arrayItem IN (SELECT a_1".($rc2)." FROM ArrayField a_1".($rc2)." WHERE ";
                        else
                            $res .= "a_1".($rc1).".arrayItem IN (SELECT a_1".($rc2)." FROM ArrayField a_1".($rc2)." WHERE ";
                    }
                    $res .= "a_1".($reg_count+$c).".name='".$array_name."'";
                    for ($c=0;$c<count($name_array);$c++)
                        $res .= ")";

                    if ($name_array[0]!="")
                        return "(".$type.".name='".$name_array[0]."' AND ".$type.".value".$operation."".$value." AND ".$type.".arrayItem IN (SELECT a_1".$reg_count." FROM ArrayField a_1".$reg_count." WHERE ".$res.")";
                    else
                        return "(".$type.".value".$operation."".$value." AND ".$type.".arrayItem IN (SELECT a_1".$reg_count." FROM ArrayField a_1".$reg_count." WHERE ".$res.")";
                }
            } else
                if ($type!="empty" or $name=="name")
                    return "(".$type.".name='".$name."' AND ".$type.".value".$operation."".$value.")";
                else {
                        return "NOT EXISTS (SELECT ee FROM DbEntity ee JOIN ee.entities eee WITH eee.name='".$name."' AND eee.dbEntity=e)";
                }
        } else {
            $name_arr = explode(".",$name);
            $current_name = array_shift($name_arr);
            if (stripos($current_name,"[")!=0)
                $is_array = true;
            else
                $is_array = false;
            if ($process_array!=false)
                $is_array = $process_array;
            if (count($name_arr)>1)
                    $fld = "EntityField";
            else
                $fld = $this->getTypeClass($value);
            if (stripos($name,"#")!=0) {
                $fld = "TextField";
                $name = str_replace("#","",$name);
            }
            if ($reg_count==0)
                $re_count="";
            else
                $re_count = $reg_count;
            if ($reg_count-1<0)
                $re_count1 = "";
            else
                $re_count1 = $reg_count-1;
            if ($is_array and $new)
                $fld1 = "ent";
            else
                $fld1 = "et";
            $reg_count++;
            if ($new)
                $re_count = "";
            if ($is_array) {
                $narr = explode("[",$current_name);
                $array_name = array_shift($narr);
                for ($c=0;$c<count($narr);$c++) {
                    $narr[$c] = strtr($narr[$c],array("]"=>"","'"=>""));
                }
                if (count($narr)==1) {
                    if ($narr[0]!="")
                        $value = "(".$fld1.$re_count.".name='".$narr[0]."' AND ".$fld1.$re_count.".value IN (SELECT dbe$reg_count FROM ".$fld." et".$reg_count." LEFT JOIN et".$reg_count.".dbEntity dbe$reg_count WHERE ".implode(".",$name_arr).$operation.$value.") AND ".$fld1.($re_count1).".arrayItem IN (SELECT aa_".$reg_count." FROM ArrayField aa_".$reg_count." WHERE aa_".$reg_count.".name='".$array_name."') )";
                    else
                        $value = "(".$fld1.$re_count.".value IN (SELECT dbe$reg_count FROM ".$fld." et".$reg_count." LEFT JOIN et".$reg_count.".dbEntity dbe$reg_count WHERE ".implode(".",$name_arr).$operation.$value.") AND ".$fld1.($re_count).".arrayItem IN (SELECT aa_".$reg_count." FROM ArrayField aa_".$reg_count." WHERE aa_".$reg_count.".name='".$array_name."'))";
                } else {
                    $res = "";
                    $narr = array_reverse($narr);
                    for ($c=0;$c<count($narr)-1;$c++) {
                        $rc1 = $reg_count+$c;
                        $rc2 = $rc1+1;
                        if ($narr[$c+1]!="")
                            $res .= "aa_1".($rc1).".name='".$narr[$c+1]."' AND aa_1".($rc1).".arrayItem IN (SELECT aa_1".($rc2)." FROM ArrayField aa_1".($rc2)." WHERE ";
                        else
                            $res .= "aa_1".($rc1).".arrayItem IN (SELECT aa_1".($rc2)." FROM ArrayField aa_1".($rc2)." WHERE ";
                    }
                    $res .= "aa_1".($reg_count+$c).".name='".$array_name."'";
                    for ($c=0;$c<count($narr);$c++)
                        $res .= ")";
                    if ($narr[0]!="")
                        $value = "(".$fld1.$re_count.".name='".$narr[0]."' AND ".$fld1.$re_count.".value IN (SELECT dbe$reg_count FROM ".$fld." et".$reg_count." LEFT JOIN et".$reg_count.".dbEntity dbe$reg_count WHERE ".implode(".",$name_arr).$operation.$value.") AND ".$fld1.($re_count1).".arrayItem IN (SELECT aa_1".($reg_count)." FROM ArrayField aa_1".($reg_count)." WHERE $res )";
                    else
                        $value = "(".$fld1.$re_count.".value IN (SELECT dbe$reg_count FROM ".$fld." et".$reg_count." LEFT JOIN et".$reg_count.".dbEntity dbe$reg_count WHERE ".implode(".",$name_arr).$operation.$value.") AND ".$fld1.($re_count1).".arrayItem IN (SELECT aa_".$reg_count." FROM ArrayField aa_".$reg_count." WHERE $res )";
                }
            } else {
                $value = "(et".$re_count.".name='".$current_name."' AND et".$re_count.".value IN (SELECT dbe$reg_count FROM ".$fld." et".$reg_count." LEFT JOIN et".$reg_count.".dbEntity dbe$reg_count WHERE ".implode(".",$name_arr).$operation.$value."))";
            }
            if ($is_array!=false)
                $process_array = $is_array;
            return $this->makeQuery($value,false);
        }
    }    
    
    function hasChildren() {
        if ($this->entity->module_id!="")
            $query = "SELECT e,et,ev FROM DBEntity e JOIN e.entities et WITH (et.name='parent') JOIN et.value ev WHERE ev.uid='".str_replace($this->entity->module_id."_","",$this->entity->getId())."'";
        else
            $query = "SELECT e,et,ev FROM DBEntity e JOIN e.entities et WITH (et.name='parent') JOIN et.value ev WHERE ev.uid='".$this->entity->getId()."'";
        $q = $this->query;
        $this->query = $query;
        $result = $this->dqlQuery("");
        $this->query = $q;
        if (count($result)>0) {
            return true;
        }
        else {
            return false;
        }
        
    }
}
?>