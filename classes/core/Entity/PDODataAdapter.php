<?php
/*
 * Класс интерфейса к хранилищу данных, который работает через PDO.
 * Является потомком класса DataAdapter и в целом реализует все его возможности
 * через PDO.
 * 
 */
$classmatch="";$classname="";
$not_exist_fields = array();
$not_exist_ops = array();
class PDODataAdapter extends DataAdapter {
        
    public $values = array();
    public $tables = array("texts","fields");
    
    function construct($params) {
        if (count($params)>2)
            $this->module_id = array_shift($params)."_".array_shift($params);
        else
            $this->module_id = "";
        $this->name = implode("_",$params);
        $this->old_name = $this->name;     
        $this->driver = "pdo_sqlite";
        $this->isPDO = true;
        $this->options = array(PDO::ATTR_PERSISTENT => true);
        $this->clientClass = "PDODataAdapter";
        $this->parentClientClasses = "DataAdapter~Entity";        
    }
    
    function connect() {
        $dsn = "";
        if ($this->driver=="pdo_sqlite") {
            if (!$this->memory)
                $dsn = "sqlite:".$this->path;
            else
                $dsn = "sqlite::memory:";
        } else if ($this->driver=="pdo_firebird") {
            $dsn = "dbname=".$this->path;
            if ($this->charset!="")
                $dsn .= ";".$this->charset;                
            if ($this->role!="") {
                $dsn .= ";".$this->role;
            } 
        } else if ($this->driver=="pdo_informix") {
            $dsn = "informix:DSN=".$this->dsn;
        } else if ($this->driver=="pdo_odbc") {
            if ($this->odbc_source!="")
                $dsn = "odbc:".$this->odbc_source;
            else {
                $dsn .= "driver:".$this->driver_string.";dbq=".$this->path.";uid".$this->user;
            }                
        } else {
            $drv = str_replace("pdo_","",$this->driver);    
            if ($this->driver!="pdo_oci" and $this->host!="")
                $dsn  = $drv.":host=".$this->host;
            else
                $dsn = $drv.":host=localhost";
            if ($this->port!="")
                $dsn .= ";port=".$this->port;
            if ($this->driver=="pdo_ibm") {
                $dsn .= ";database=".$this->dbname;
                $dsn = str_replace("host=","hostname=",$dsn);
            }
            else
                $dsn .= ";dbname=".$this->dbname;
            if ($this->driver=="pdo_mysql") {
                if ($this->unix_socket!="")
                    $dsn .= ";unix_socket=".$this->unix_socket;            
            }
            if ($this->charset!="")
                $dsn .= ";charset=".$this->charset;            
        }        
        try {
            $dbh = new PDO($dsn,$this->user,$this->password,$this->options);
        } catch (Exception $e) {
    	    $this->connected = false;
    	    return 0;
        }
        
        if ($this->driver=="pdo_mysql") {
            $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, TRUE);
        }
        if ($this->driver == "pdo_sqlite") {
            $concat2 = "e1.classname || '_' || e1.id";
            $incr = "AUTOINCREMENT";
        } else {
            $concat2 = "CONCAT(e1.classname,'_',e1.id)";
            $incr = "AUTO_INCREMENT";
        }
        try {
	        $dbh->exec("SET NAMES 'utf8' COLLATE 'utf8_unicode_ci'");
	        $dbh->exec("CREATE TABLE dbEntity (id BIGINT NOT NULL PRIMARY KEY, classname VARCHAR(255))");
	        $dbh->exec("CREATE TABLE fields (id BIGINT NOT NULL PRIMARY KEY $incr NOT NULL,type VARCHAR(100), name VARCHAR(100), value LONGTEXT, value2 LONGTEXT,entityId INT, classname VARCHAR(100),arrayItemId INT)");
	        $dbh->exec("CREATE TABLE links (id BIGINT NOT NULL PRIMARY KEY $incr NOT NULL, class1 VARCHAR(255), entity1 INT, class2 VARCHAR(255), entity2 INT)");
	        $dbh->exec("CREATE TABLE rights (object VARCHAR(100), entity VARCHAR(255), rule INT)");
	        
	        $dbh->exec("CREATE INDEX classname ON fields (classname)");        
	        $dbh->exec("CREATE INDEX name ON fields (name)");
	        $dbh->exec("CREATE INDEX value ON fields (value(256))");
	        if ($this->driver!="pdo_sqlite")
	        	$dbh->exec("CREATE FULLTEXT INDEX value2 ON fields (value2)");
	        else
	        	$dbh->exec("CREATE INDEX value2 ON fields (value2)");
	        $dbh->exec("CREATE INDEX type ON fields (type)");
	        $dbh->exec("CREATE INDEX entityId ON fields (entityId)");
	        $dbh->exec("CREATE INDEX classname ON fields (classname)");
	        $dbh->exec("CREATE INDEX arrayItemId ON fields (arrayItemId)");
	        
	        $dbh->exec("CREATE INDEX class1 ON links (class1)");
	        $dbh->exec("CREATE INDEX entity1 ON links (entity1)");        
	        $dbh->exec("CREATE INDEX class2 ON links (class2)");
	        $dbh->exec("CREATE INDEX entity2 ON links (entity2)");        
	        
	        $dbh->exec("CREATE INDEX object on rights (object)");
	        $dbh->exec("CREATE INDEX entity on rights (entity)");
	        $dbh->exec("CREATE INDEX rule on rights (rule)");
        } catch (Exception $e) {};
        $this->dbh = $dbh;
        $this->connected = true;
        return $dbh;
    }
    
    function getMaxId($table) {
        if (!$this->connected)
            $this->connect();
        $stmt = $this->dbh->prepare("SELECT MAX(CAST(id AS UNSIGNED)) as maxid FROM ".$table);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result["maxid"]+1;        
    }
    
    function getMaxValue($table,$field,$condition) {
        if (!$this->connected)
            $this->connect();
        if ($condition!="") {
            if (stripos($condition,"@classname")!==FALSE) {
                $matches=array();
                preg_match("/@classname\='(.*)'/",$condition,$matches);
                $classname = $matches[1];
                $condition=preg_replace("/@classname\='(.*)'/","",$condition);
                if (trim($condition)!="")
                    $condition = " JOIN fields AS f1 ON ".$this->parseCondition($condition,false,"",$this)." WHERE f.classname='".$classname."'";
                else
                    $condition = "WHERE f.classname='".$classname."'";
            } else
                $condition = " JOIN fields AS f1 ON ".$this->parseCondition($condition,false,"",$this)."";
            
        }
        $sql = "SELECT MAX(CAST(so.value AS UNSIGNED)) as maxid FROM ".$table." f JOIN fields AS so ON (f.entityId=so.entityId AND so.name='".$field."') ".$condition."";        
        $sql = str_replace(" AND ("," (",$sql);
        $sql = str_replace(") AND ",") ",$sql);
        $stmt = $this->dbh->prepare($sql,array(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true));
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$result["maxid"]+1;                
    }
    
    function getAuthor($entity) {
   		$class = get_class($entity);
    	$id = array_pop(explode("_",$entity->getId()));
    	$sql = "SELECT value FROM fields WHERE name='user' AND classname='".$class."' AND entityId=".$id;
        $stmt = $this->dbh->prepare($sql,array(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true));
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result and count($result)==1)
        	return $result["value"];
	}
        
    function getFieldType($field_name,$field_value,$is_array=false) {
    	if ($field_name=="tags")
    		return "text";
    	if (isset($this->entity->tagsList[$field_name]))
    		return "text";
        if ($this->entity->persistedFields!="" and !$is_array) {
        	if (!is_array($this->entity->persistedFields)) {
            	$arr = $this->entity->getPersistedArray();
            	$arr1 = explode("|",$arr[$field_name]);
            	$type = $arr1[0];
        	} else {
        		$persistedFields = $this->entity->explodePersistedFields($this->entity->persistedFields);
        		$type = $persistedFields[$field_name]["type"];
        	}
            switch ($type) {
                case "integer":
                    return "integer";                
                case "string":
                    return "string";
                case "boolean":
                    return "boolean";
                case "decimal":
                    return "decimal";
                case "text":
                    return "text";
                case "entity":
                    return "entity";
                case "array":
                    return "array";                
            }
        } else {
            $type = gettype($field_value);
            switch ($type) {
                case "integer":
                    return "integer";
                case "string":
                    if (strlen($field_value)>255)
                        return "text";
                    else
                     return "string";
                case "boolean":
                    return "boolean";
                case "double":
                    return "decimal";
                case "array":
                    return "array";
                case "object":
                    if (method_exists($field_value,"getPresentation"))
                        return "entity";                
            }
        }
        return null;
    }
    
    static function getDbValue($value,$is_array=false) {
        if (is_float($value) or is_numeric($value))
            return $value;
        else
            return "'".$value."'";
    }
    
    
    function getFieldTable($field_name,$field_value,$is_array=false) {
        $type = $this->getFieldType($field_name,$field_value,$is_array);
        switch ($type) {
            case "integer":
                return "fields";                
            case "string":
                return "fields";
            case "boolean":
                return "fields";
            case "decimal":
                return "fields";
            case "text":
                return "fields";
            case "entity":
                return "fields";
            case "array":
                return "fields";                
        }
        return "fields";
    }
    
    function save() {
        global $Objects;
        if (is_object($this->entity)) {   
            if (!$this->connected)
                $this->connect();
			
            $entity_id = $this->entity->getId();
            if ($this->entity->module_id!="")
                $entity_id = str_replace($this->entity->module_id."_","",$entity_id);
            if ($this->entity->siteId!="")
                $entity_id = str_replace($this->entity->siteId."_","",$entity_id);
            $entity_arr = explode("_",$entity_id);
            $classname = array_shift($entity_arr);
            $id = array_pop($entity_arr);
            if (!$this->entity->loaded and !$this->entity->tryloaded)
                $this->entity->load();
            $persistedFields = $this->entity->getPersistedArray();
            if (isset($persistedFields["tags"])) {
				$persistedFields["tags"]["type"] = "text";
				$this->entity->persistedFields["tags"] = array("type" => "text");
            }
            if ($id=="") {
                $id = $this->getMaxId("dbEntity");
            }
            $stmt = $this->dbh->prepare("DELETE FROM dbEntity WHERE id=:id",array(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true));
            $stmt->bindParam(":id",$id);
            $stmt->execute();

            $stmt = $this->dbh->prepare("DELETE FROM fields WHERE entityId=:id",array(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true));                
            $stmt->bindParam(":id",$id);
            $stmt->execute();
            
            if ($this->driver!="pdo_sqlite") {
	            $stmt = $this->dbh->prepare("OPTIMIZE TABLE dbEntity",array(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true));
	            $stmt->execute();
	
	            $stmt = $this->dbh->prepare("OPTIMIZE TABLE fields",array(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true));
	            $stmt->execute();
            }
            
            $sql = "INSERT INTO dbEntity (id,classname) VALUES(".$id.",'".$classname."')";
            
            if ($this->driver!="pdo_sqlite")
            	$stmt = $this->dbh->prepare($sql,array(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true));
            else         
            	$stmt = $this->dbh->prepare($sql);
            $stmt->execute();
            $efields = $this->entity->fields;
            if (isset($persistedFields["persistedFields"]))
            	$efields["persistedFields"] = $this->entity->persistedFields;
            foreach ($efields as $key=>$value) {
                if (trim($key)==="") 
                    continue;
                if (!is_object($value) and !is_array($value))
                    if ($value===null or trim($value)==="")
                        continue;
                $key=trim($key);
                if ($this->entity->persistedFields!="" and !isset($persistedFields[$key]) and !isset($this->entity->tagsList[$key]))
                    continue;
                if ($key=="persistedFields") {
                	if (is_array($value)) {
                		$value = serialize($value);
                	}
                	else {
                		$value = str_replace("#","\n",$value);
                	}
                }
                $tbl = $this->getFieldTable($key,$value);
                $type= $this->getFieldType($key,$value);
                switch ($type) {                	
                    case "entity":
                        if (!is_object($value))
                            $value = $Objects->get($value);
                        $value->load();
                        if (!$value->loaded)
                            continue;
                        $value_id = $value->getId();
                        if ($this->entity->module_id!="")
                            $value_id = str_replace($this->entity->module_id."_","",$value_id);
                        if ($value_id=="")
                            $value_id = $value->save(true);
                        else {
                            if ($value->cascadeSave)
                                $value->save(true);
                        }
                        $value_arr = explode("_",$value_id);
                        $value_id = array_shift($value_arr)."_".array_pop($value_arr);
                        if ($this->driver!="pdo_sqlite")
                        	$stmt = $this->dbh->prepare("INSERT INTO ".$tbl." (name,type,classname,value,entityId) VALUES(:name,:type,:classname,:value,:entityId)",array(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true));
                        else                        
                        	$stmt = $this->dbh->prepare("INSERT INTO ".$tbl." (name,type,classname,value,entityId) VALUES(:name,:type,:classname,:value,:entityId)");
                        $stmt->bindParam(":name",$key);
                        $stmt->bindParam(":type",$type);
                        $stmt->bindParam(":classname",get_class($this->entity));
                        $stmt->bindParam(":value",$value_id);
                        $stmt->bindParam(":entityId",$id);
                        $stmt->execute();                            
                        break;
                    case "array":
                        $this->saveArray($id,$key,$value);
                        break;                            
                    default:
                    	if ($this->driver!="pdo_sqlite")              	
                       		$stmt = $this->dbh->prepare("INSERT INTO ".$tbl." (name,type,classname,value,value2,entityId) VALUES(:name,:type,:classname,:value,:value2,:entityId)",array(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true));
                    	else
                    		$stmt = $this->dbh->prepare("INSERT INTO ".$tbl." (name,type,classname,value,value2,entityId) VALUES(:name,:type,:classname,:value,:value2,:entityId)");
                        $stmt->bindParam(":name",$key);
                        $stmt->bindParam(":type",$type);
                        $stmt->bindParam(":classname",get_class($this->entity));
                        $stmt->bindParam(":value",$value);
                       	$stmt->bindParam(":value2",strip_tags($value));
                        $stmt->bindParam(":entityId",$id);
                        $stmt->execute();
                }
            }             
            $this->entity->entityId = $id;
            $this->setRights();
            return $id;
        }
    }
    
    function saveArray($entityId,$field,$value,$arrayItemId=null) {
        $id=$this->getMaxId("fields");
        if ($arrayItemId!=null) {
            $stmt = $this->dbh->prepare("INSERT INTO fields (id,name,type,classname,entityId,arrayItemId) VALUES(:id,:name,:type,:classname,:entityId,:arrayItemId)",array(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true));
            $stmt->bindValue(":arrayItemId",$arrayItemId);
        } else
            $stmt = $this->dbh->prepare("INSERT INTO fields (id,name,type,classname,entityId) VALUES(:id,:name,:type,:classname,:entityId)",array(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true));
        $stmt->bindValue(":id",$id);
        $stmt->bindValue(":name",$field);
        $stmt->bindValue(":type","array");
        $stmt->bindValue(":classname",get_class($this->entity));
        $stmt->bindValue(":entityId",$entityId);
        $stmt->execute();
        foreach($value as $array_index=>$array_value) {
            $tbl = $this->getFieldTable($array_index,$array_value,true);
            $type = $this->getFieldType($array_index,$array_value,true);
            switch ($tbl) {
                case "entity":
                    $value_id = $array_value->getId();
                    if ($this->entity->module_id!="")
                        $value_id = str_replace($this->entity->module_id."_","",$value_id);
                    if ($value_id=="")
                        $value_id = $array_value->save();
                    else
                        $array_value->save();
                    $stmt = $this->dbh->prepare("INSERT INTO ".$tbl." (name,value,type,classname,entityId,arrayItemId) VALUES(:name,:value,:type,:classname,:entityId,:arrayItemId)",array(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true));
                    $stmt->bindParam(":name",$array_index);
                    $stmt->bindParam(":value",$value_id);
                    $stmt->bindValue(":type",$type);
                    $stmt->bindValue(":classname",get_class($this->entity));
                    $stmt->bindParam(":entityId",$entityId);
                    $stmt->bindParam(":arrayItemId",$id);
                    $stmt->execute();                            
                    break;
                case "array":
                    $this->saveArray($entityId,$array_index,$array_value,$id);
                    break;
                default:
                   	$stmt = $this->dbh->prepare("INSERT INTO ".$tbl." (name,value,value2,type,classname,entityId,arrayitemId) VALUES(:name, :value, :value2, :type,:classname,:entityId, :arrayItemId)",array(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true));
                	$stmt->bindParam(":name",$array_index);
                    $stmt->bindParam(":value",$array_value);
                   	$stmt->bindParam(":value2",strip_tags($array_value));
                    $stmt->bindValue(":type",$this->getFieldType($array_index,$array_value,true));
                    $stmt->bindValue(":classname",get_class($this->entity));
                    $stmt->bindParam(":entityId",$entityId);
                    $stmt->bindParam(":arrayItemId",$id);
                    $stmt->execute();                            
                    break;
            }   
        }
    }
    
    function load($result="") {
        global $Objects;
        if (is_object($this->entity)) {        	 
            $entity_id = $this->entity->getId();
            if ($this->entity->module_id!="")
                $entity_id = str_replace($this->entity->module_id."_","",$entity_id);
            $entity_arr = explode("_",$entity_id);
            $classname = array_shift($entity_arr);
            $id = array_pop($entity_arr);
            if ($id=="")
                return false;

            if ($result=="") {
                if (!$this->connected)
                    $this->connect();
                if (!$this->connected)
					return 0;
                $stmt = $this->dbh->prepare("SELECT * FROM fields f WHERE f.entityId=:id");        
                if (!$stmt)
                    return false;
                $stmt->bindParam(":id",$id);
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);                    
            }            
            if (count($result)==0 or !$result)
                return false;
            foreach($result as $value) {                
                $loaded_value = "";
                if ($value["arrayItemId"]==null and $value["entityId"]==$id) {
                    if (@$value["type"]=="array") {
                        $this->entity->fields[$value["name"]] = $this->loadArray($value,$result,$loaded_value);
                    } else {
                        if (@$value["type"]=="entity") {
                            if ($this->entity->module_id!="") {
                                $arr = explode("_",$value["value"]);
                                $class_name = array_shift($arr);
                                $val = $class_name."_".$this->entity->module_id."_".implode("_",$arr);
                                $Objects->get($val)->noPresent = true;
                                $this->entity->fields[$value["name"]] = $Objects->get($val);
                            } else
                            $this->entity->fields[$value["name"]] = $Objects->get($value["value"]);                        
                        } else {
                        	$this->entity->fields[$value["name"]] = $value["value"];
                        	if ($value["name"]=="persistedFields") {
                        		if (is_array(@unserialize($value["value"])))
                        			$this->entity->fields[$value["name"]] = unserialize($value["value"]); 
                        	}
                        }
                    }
                }
            }   
            $this->getRights(); 
            return true;
        }
        return false;
    }
    
    function loadArray($value,$result,&$loaded_value) {
        $result_array = array();
        foreach($result as $array_value) {            
            if ($array_value["arrayItemId"]==$value["id"]) {
                if ($array_value["type"]=="array") {
                    $result_array[$array_value["name"]] = $this->loadArray($array_value,$result,$lv);
                } else {
                    if ($array_value["type"]=="entity") {
                        if ($this->entity->module_id!="") {
                            $arr = explode("_",$array_value["value"]);
                            $class_name = array_shift($arr);
                            $val = $class_name."_".$this->entity->module_id."_".implode("_",$arr);
                            $result_array[$array_value["name"]] = $Objects->get($val);
                        } else
                            $result_array[$array_value["name"]] = $Objects->get($array_value["value"]);                        
                    }
                    else {
                        $result_array[$array_value["name"]] = $array_value["value"];
                    }
                }
            }            
        }   
        return $result_array;        
    }
    
    function getEntityLinks($name="") { 
        global $Objects;
        $results = array();
        $id = $this->entity->getId();
        if ($this->entity->module_id!="")
            $id = str_replace($this->module_id."_","",$id);
        if (!$this->connected)
            $this->connect();
        if ($name=="") {
            $stmt = $this->dbh->prepare("SELECT * FROM fields WHERE `type`=:type AND value=:id");
        }
        else {
            $stmt = $this->dbh->prepare("SELECT * FROM fields WHERE `type`=:type AND value=:id AND name=:name");
            $stmt->bindParam(":name",$name);
        }
        $type_entity="entity";
        $stmt->bindParam(":id",$id);
        $stmt->bindParam(":type",$type_entity);
        $stmt->execute();
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($res)>0) {
	        foreach($res as $rs) {
        	    if ($this->module_id!="")
                    $id = $rs["classname"]."_".$this->module_id."_".$rs["entityId"];
                else
                 $id = $rs["classname"]."_".$rs["entityId"];
                $obj = $Objects->get($id);
                $obj->loaded = false;
                $obj->load("","",true);
                $result = array();
                $result["entity"] = $obj;
                $result["field"] = $rs["name"];
    	        $results[] = $result;
	        }
        }
        return $results;
    }
    
    function getBlockingObjects() {
    	global $Objects;
    	$entities = $this->getEntityLinks();
    	$blocked = array();
    	foreach ($entities as $entity) {
    		$ent = $entity["entity"];
    		if (method_exists($ent,"getRecords"))
    			continue;
    		if (!$ent->deleted) {
    			$blocked[] = $ent;
    		}
    		else {
    			$blocked = mergeArrays($blocked,$ent->getBlockingObjects());
    		}
    	}
    	return $blocked;    	
    }
    
    function remove() {
        if (is_object($this->entity))
            $id = $this->entity->getId();
        else
            return 0;
        if ($this->entity->module_id!="")
            $id = str_replace($this->module_id."_","",$id);
        $id = explode("_",$id);
        array_shift($id);
        $id = array_pop($id);
        if (!$this->connected)
            $this->connect();
        $results = $this->getEntityLinks();
        if (count($results)>0)
            return $results;
        $this->dbh->beginTransaction();
        $stmt = $this->dbh->prepare("DELETE FROM dbEntity WHERE id=:id");
        $stmt->bindParam(":id",$id);
        $stmt->execute();
        //foreach($this->tables as $tbl) {
            $stmt = $this->dbh->prepare("DELETE FROM fields WHERE entityId=:id");
            $stmt->bindParam(":id",$id);
            $stmt->execute();            
        //}
        $stmt = $this->dbh->prepare("DELETE FROM links WHERE entity1=:id OR entity2=:id");
        $stmt->bindParam(":id",$id);
        $stmt->execute();
        $stmt = $this->dbh->prepare("DELETE FROM rights WHERE entity=:entityId");
        $stmt->bindParam(":entityId",str_replace($this->module_id."_","",$this->entity->getId()));
        $stmt->execute();
        
        if ($this->driver!="pdo_sqlite") {        
	        $stmt = $this->dbh->prepare("OPTIMIZE TABLE dbEntity");
	        $stmt->execute();            
	        $stmt = $this->dbh->prepare("OPTIMIZE TABLE fields");
	        $stmt->execute();            
	        $stmt = $this->dbh->prepare("OPTIMIZE TABLE links");
	        $stmt->execute();
        }            
        $this->dbh->commit();
        return 0;        
    }
    
    function hasChildren() {
        global $Objects;
        $results = array();
        $id = get_class($this->entity)."_".array_pop(explode("_",$this->entity->getId()));
        if ($this->entity->module_id!="")
            $id = str_replace($this->module_id."_","",$id);
        if (!$this->connected)
            $this->connect();
        if (!$this->connected)
			return 0;
        $stmt = $this->dbh->prepare("SELECT * FROM fields WHERE type='entity' AND name='parent' AND value=:id");
        $stmt->bindParam(":id",$id);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return count($results);
    }
    
    function getChildren() {
    	global $Objects;
    	$results = array();
    	$id = get_class($this->entity)."_".array_pop(explode("_",$this->entity->getId()));
    	 
    	if ($this->entity->module_id!="")
    		$id = str_replace($this->module_id."_","",$id);
    	if (!$this->connected)
    		$this->connect();
    	if (!$this->connected)
    		return array();
    	$stmt = $this->dbh->prepare("SELECT DISTINCT classname,entityId FROM fields WHERE type='entity' AND name='parent' AND value=:id");
    	$stmt->bindParam(":id",$id);
    	$stmt->execute();
    	$r = $stmt->fetchAll(PDO::FETCH_ASSOC);
    	foreach ($r as $res) {
    		$results[] = $Objects->get($res["classname"]."_".$this->entity->module_id."_".$res["entityId"]);
    	}
    	return $results;
    }
    
    static function makeQuery($query,$adapter,$module_id="",$count=false) {        
        global $Objects,$classmatch,$not_exist_fields,$not_exist_ops,$classname;
        $reg_count = 0;
        $not_exist_fields = array();
        $not_exist_ops = array();
        $classmatch="";
        $where_clause="";$orderby_clause="";$limit_clause="";
        $matches = array();
        $query = trim($query);
        $query = trimSpaces($query);        
        $etype = "";
        $evalue = "";
        $type = "";
        $query = str_replace("->",".",$query);
        $query = str_replace("==","=",$query);    
        $query = str_replace("xyxyxy","'",$query);
        
        if (stripos($query,"WHERE")!==false)            
            $where_clause = " (WHERE) (.*)";
        else
            $where_clause = "";
        if (stripos($query,"ORDER BY")!==false)
            $orderby_clause = " (ORDER BY) (.*)";
        else
            $orderby_clause = "";
        if (stripos($query,"LIMIT")!==false)
            $limit_clause = " (LIMIT) (.*)";
            
        $reg = "/SELECT (.*) FROM .*".$where_clause.$orderby_clause.$limit_clause."/";
        $condition="";$sort_order="";$limit="";
        if (preg_match($reg,$query,$matches)) {
            $fields = trim($matches[1]);
            if (isset($matches[2]))
                if ($matches[2]=="WHERE")
                    $condition = trim($matches[3]);
                else
                    if ($matches[2]=="ORDER BY") {
                        $sort_order = trim($matches[3]);                
                    }
                    else
                    if ($matches[2]=="LIMIT") {
                        $limit = trim($matches[3]);                
                    }
            if (isset($matches[4]))
                if ($matches[4]=="ORDER BY")
                    $sort_order = trim($matches[5]);                
                else
                    if ($matches[4]=="LIMIT")
                        $limit = trim($matches[5]);                
            if (isset($matches[6]))
                if ($matches[6]=="LIMIT")
                    $limit = trim($matches[7]);
            if ($condition!="")
                $condition = PDODataAdapter::parseCondition($condition,false,"",$adapter);
            if ($classmatch!="") {
                $condition = str_replace(" AND )",")",$condition);
                $condition = str_replace(" OR )",")",$condition);
            }
            $cond_arr = explode(" ",trim($condition));
            $last = array_pop($cond_arr);
            if (trim($last)=="OR" OR trim($last)=="AND")
                $condition=implode(" ",$cond_arr)." ";
            if ($limit!="")
                $limit = " LIMIT ".$limit;
            if ($fields=="count") {
                $limit = "";
            }
            $sort_order_array = explode(",",$sort_order);
            $sort_field_list_array = array();
            $order_by_field_list_array = array();
            $sort_order_joins_array = array();            
            $i=1;            
            if ($classname!="") {
                if ($module_id!="")
                    $ob = $Objects->get($classname."_".$module_id."_preview1");
                else
                    $ob = $Objects->get($classname."_preview1");                
                $persistedArray = $ob->getPersistedArray();
            }
            
            if ($sort_order!="") {
                foreach($sort_order_array as $sort_field) {
                    $sort_field_parts = explode(" ",$sort_field);
                    if (isset($ob) and isset($persistedArray[trim($sort_field_parts[0])])) {
                        $ar = explode("|",$persistedArray[trim($sort_field_parts[0])]);
                        $type = $ar[0];
                        if ($type=="integer" or $type=="decimal") {
                        	if (@$sort_field_parts[1]=="DESC")
                            	$filler = "0";
                        	else
                         	$filler = "9999999999";
                            if ($adapter->driver!="pdo_sqlite")
                                $sort_field_list_array[] = "IF(o".$i.".value IS NULL,$filler,CAST(o".$i.".value AS UNSIGNED)) AS of".$i;                            
                            else
                            $sort_field_list_array[] = "IFNULL(CAST(o".$i.".value AS UNSIGNED),$filler) AS of".$i;                            
                        }
                        else {
                            if (@$sort_field_parts[1]=="DESC")
                                $filler = "NULL";
                            else
                            $filler = "'яяяяяяя'";
                            if ($adapter->driver!="pdo_sqlite")
                                $sort_field_list_array[] = "IF(o".$i.".value IS NULL,$filler,o".$i.".value) AS of".$i;
                            else
                            $sort_field_list_array[] = "IFNULL(o".$i.".value,$filler) AS of".$i;
                        }
                    } else {
                        if (@$sort_field_parts[1]=="DESC")
                            $filler = "NULL";
                        else
                         $filler = "'яяяяяяя'";
                        if ($adapter->driver!="pdo_sqlite")
                            $sort_field_list_array[] = "IF(o".$i.".value IS NULL,$filler,o".$i.".value) AS of".$i;
                        else
                         $sort_field_list_array[] = "IFNULL(o".$i.".value IS NULL,$filler) AS of".$i;
                    }
                    $order_by_field_list_array[] = str_replace($sort_field_parts[0],"of".$i,$sort_field);
                    $sort_order_joins_array[] = PDODataAdapter::getFieldJoin($sort_field_parts[0],"o".$i,$adapter);
                    $i++;
                }
            }
            $order_by = implode(",",$order_by_field_list_array);                        
            if ($order_by!="")
                $order_by = " ORDER BY ".$order_by;
            
            $sort_fields = implode(",",$sort_field_list_array);
            if ($sort_fields!="")
                $sort_fields = ",".$sort_fields;
            $sort_joins = implode(" ",$sort_order_joins_array);
            if ($sort_joins!="")
                $sort_joins = " ".$sort_joins;
            $field_joins="";
            $field_string_array = array();
            $field_string="";
            $field_joins_array = array();
            $join_fields = array();
            if ($count)
                $fields="count";
            
            if ($fields=="entities" or $fields=="count") {
                $field_string = "DISTINCT f.entityId AS entityId";
                if ($order_by!="")
                    "entityId ASC,".$order_by;
            } 
            if ($fields=="count") {
                $sort_joins = "";
                $sort_fields = "";
                $order_by = "";
            }
            else {
                $fields_array = explode(",",$fields);
                $field_list = array();
                $i=1;
                $as = array();
                foreach($fields_array as $field) {
                    $field_parts = explode(" ",$field);
                    if (stripos($field_parts[0], ".")!==FALSE) {
                        $field_joins_array[] = PDODataAdapter::getFieldJoin($field_parts[0],"f".$i,$adapter);
                        $join_fields[] = "f".$i;
                        $fld = array_shift(explode(".",$field_parts[0]));
                        $i++;
                    } else
                        $fld = $field_parts[0];
                    if (isset($field_parts[1])) {
                    	if (trim($field_parts[2])=="AS") {
                    		$field_parts[2] = trim($field_parts[3]);
                    	}
                        $as[$fld] = trim($field_parts[2]);
                    } else
                        $as[$fld] = $fld;
                    if ($fld!="") {
                        $field_string_array[] = "'".trim($fld)."'";
                        $field_list[] = trim($fld);
                    }
                }
                $field_joins = " ".implode(" ",$field_joins_array);
            }
            $i=0;$i1=0;
            $evalue = "";
            if (count($join_fields)>0) {
                for ($i=0;$i<count($join_fields);$i++) {
                    $f = $join_fields[$i];
                    $evalue .= "IF(".$f.".value IS NULL,";
                    $etype .= "IF(".$f.".value IS NULL,";
                    $i1++;
                }
                $evalue.="e.value,".$join_fields[$i1-1].".value)";
                $etype.="e.type,'string')";
                for ($i=count($join_fields)-2;$i>=0;$i--) {
                    $f = $join_fields[$i];                    
                    $evalue.=",".$f.".value)";
                    $etype.=",'string')";
                }
                $evalue .=" AS value";
                $type .=" AS type";
            } else {
                $evalue = "e.value AS value";
                $etype = "e.type AS type";
            }
            $not_exists_fields_array = array();
            $not_exists_join_array = array();
            $not_exists_where_array = array();
            $not_exists_fields = "";
            $not_exists_where = "";
            $not_exists_joins = "";            
            if (count($not_exist_fields)>0) {
                for ($i=0;$i<count($not_exist_fields);$i++) {
                    $field = $not_exist_fields[$i];
                    $not_exists_fields_array[] = "ne".$i.".value AS fne".$i;
                    if ($not_exist_ops[$i]=="")
                        $not_exist_ops[$i] = " AND ";
                    $not_exists_where_array[] = $not_exist_ops[$i]." ne".$i.".value IS NULL";
                    $not_exists_join_array[] = PDODataAdapter::getFieldJoin($field,"ne".$i,$adapter);
                }
            }
            if (count($not_exists_fields_array)>0)
                $not_exists_fields = ",".implode(",",$not_exists_fields_array);
            $not_exists_where = implode(" ",$not_exists_where_array);
            if ($classmatch=="") {
                $arr = explode(" ",$not_exists_where);
                array_shift($arr);
                if ($not_exists_where!="")
                    $not_exists_where = " WHERE ".implode("_",$arr);
            } else
                $not_exists_where = " ".$not_exists_where;
            $not_exists_joins = " ".implode(" ",$not_exists_join_array);

            if (count($field_string_array)>0 and trim($fields)!="entities") {
                if (trim($classmatch.$not_exists_where)=="")
                    $fields_where = " WHERE f.name IN (".implode(",",$field_string_array).")";
                else
                    $fields_where = " AND f.name IN (".implode(",",$field_string_array).")";
                $efields_where = " AND e.name IN (".implode(",",$field_string_array).")";
            } else {
                $fields_where = "";
            }
            if (!$adapter->connected)
                $adapter->connect();
            if ($limit!="") {
                $query = "SELECT DISTINCT f.entityId as eid".$sort_fields.$not_exists_fields." FROM fields f ".$condition;          
                $query = preg_replace("/ FROM fields f \(f\.entityId=(.*)\.entityId /U"," FROM fields f JOIN fields AS \\1 ON (f.entityId=\\1.entityId ",$query);                
                $query = preg_replace("/ FROM fields f AND \(f\.entityId=(.*)\.entityId /U"," FROM fields f JOIN fields AS \\1 ON (f.entityId=\\1.entityId ",$query);                
                                                
                $q = $query;
                $q1=$query.$sort_joins.$not_exists_joins.$classmatch.$not_exists_where.$fields_where." ".$order_by.$limit;
				for ($i=0;$i<10;$i++)
					$q1 = str_replace("  "," ",$q1);
				$q1 = str_replace("AND LEFT","LEFT",$q1);
				$stmt=$adapter->dbh->prepare($q1);
                $stmt->execute();
                $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $r = array();
                foreach ($res as $item)
                    $r[] = $item["eid"];
                $entList = implode(",",$r);
            } else {
                $query = "SELECT DISTINCT f.entityId as eid FROM fields f ".$condition;                           
                $query = preg_replace("/ FROM fields f \(f\.entityId=(.*)\.entityId /U"," FROM fields f JOIN fields AS \\1 ON (f.entityId=\\1.entityId ",$query);
                $q = $query;
            }
            if ($fields!="count") {
                if (isset($entList)) {
                    if ($entList!="") {
                    	if ($fields!="entities")
                            $query = "SELECT e.entityId AS entityId, e.id AS id, e.name AS name, $etype, $evalue, e.classname AS classname,e.arrayItemId as arrayItemId".$sort_fields.$not_exists_fields." FROM fields AS e".str_replace("f.","e.",$field_joins).str_replace("f.","e.",$sort_joins).str_replace("f.","e.",$not_exists_joins)." WHERE e.entityId IN (".$entList.")".@$efields_where;
                        else
                        $query = "SELECT e.entityId AS entityId, e.id AS id, e.name AS name, $etype, $evalue, e.classname AS classname,e.arrayItemId as arrayItemId".$sort_fields.$not_exists_fields." FROM fields AS e".str_replace("f.","e.",$field_joins).str_replace("f.","e.",$sort_joins).str_replace("f.","e.",$not_exists_joins)." WHERE e.entityId IN (".$entList.")";
                    }
                    else
                     return array();
                }
                else {
                    if ($fields!="entities" and $fields!="delete")                    
                        $query = "SELECT e.entityId AS entityId, e.id AS id, e.name AS name, $etype, $evalue, e.classname AS classname,e.arrayItemId as arrayItemId".$sort_fields.$not_exists_fields." FROM fields AS e".str_replace("f.","e.",$field_joins).str_replace("f.","e.",$sort_joins).str_replace("f.","e.",$not_exists_joins)." WHERE e.entityId IN ({query})".@$efields_where;            
                    else if ($fields=="entities")
                        $query = "SELECT e.entityId AS entityId, e.id AS id, e.name AS name, $etype, $evalue, e.classname AS classname,e.arrayItemId as arrayItemId".$sort_fields.$not_exists_fields." FROM fields AS e".str_replace("f.","e.",$field_joins).str_replace("f.","e.",$sort_joins).str_replace("f.","e.",$not_exists_joins)." WHERE e.entityId IN ({query})";       
                    else if ($fields=="delete")
						$query = "DELETE FROM fields AS e WHERE e.entityId IN ({query})";
				}
            }
            else
                $query = "SELECT DISTINCT e.entityId as count".$sort_fields.$not_exists_fields." FROM fields AS e".str_replace("f.","e.",$sort_joins).str_replace("f.","e.",$not_exists_joins)." WHERE e.entityId IN (".$query.$classmatch.$not_exists_where.")".$order_by;
            $query=str_replace("AND AND","AND",$query);
            $query=str_replace("OR OR","OR",$query);   
            if ($fields!="count" and $fields!="delete") {
                $rquery = $query.$order_by;
                $rquery = str_replace("{query}",$q.$classmatch.$not_exists_where,$rquery);
            }
            else if ($fields!="delete")
                $rquery = $query.$order_by;
            else {
				$rquery = $query;								
                $rquery = str_replace("{query}",$q.$classmatch.$not_exists_where,$rquery);
			}
			for ($i=0;$i<10;$i++)
				$rquery = str_replace("  "," ",$rquery);
			$rquery = str_replace("AND LEFT","LEFT",$rquery);
			//echo $rquery;
			if (!$adapter->connected)
				$adapter->connect();
			if (!$adapter->connected)
				return array();
			
            $stmt = $adapter->dbh->prepare($rquery);
            $stmt->execute();
            if ($fields!="delete") {
				$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
				$arr = array();
				$res = array();
				if ($fields=="count")
					return array("count" => count($result));
				else {
					foreach ($result as $item) {
						if (!isset($arr[$item["entityId"]])) {
							if ($module_id=="")
								$arr[$item["entityId"]] = $Objects->get($item["classname"]."_".$item["entityId"]);
							else
								$arr[$item["entityId"]] = $Objects->get($item["classname"]."_".$module_id."_".$item["entityId"]);
							$arr[$item["entityId"]]->name = $item["entityId"];
							$arr[$item["entityId"]]->entityId = $item["entityId"];
							$arr[$item["entityId"]]->adapter = clone $adapter;
							$arr[$item["entityId"]]->adapter->entity = $arr[$item["entityId"]];
							$arr[$item["entityId"]]->adapter->load($result);							
							if ($fields!="entities") {
								$it = array();
								foreach ($field_list as $fld_name) {
									$it[@$as[@$fld_name]] = @$arr[@$item["entityId"]]->fields[@$fld_name];
								}
								$it['classname'] = $item['classname'];
								$it['entityId'] = $item['entityId'];
								$res[] = $it;
							}                    
						}
					}					
					if ($fields=="entities")
						return $arr;
					else {
						return $res;
					}
				}
			}
        }
        return 0;
    }
    
    static function getConditionRegs() {
        $regs = array();
        $regs[] = "/@(\S+?)( IS NOT )(NULL)/";
        $regs[] = "/@(\S+?)( IS )(NULL)/";
        $regs[] = "/@(\S+?)( NOT LIKE )('.+')/U";
        $regs[] = "/@(\S+?)( LIKE )('.+')/U";
        $regs[] = "/@(\S+?)( NOT BETWEEN )(\S+ AND \S+)/";
        $regs[] = "/@(\S+?)( BETWEEN )(\S+ AND \S+)/";
        $regs[] = "/@(\S+?)( NOT IN )(\S+)/";
        $regs[] = "/@(\S+?)( IN )(\S+)/";
        $regs[] = "/@(\S+?)(\!\=)(\'.+\')/U";
        $regs[] = "/@(\S+?)(\!\=)(\S+)/";        
        $regs[] = "/@(\S+?)(\>\=)(\'.+\')/U";
        $regs[] = "/@(\S+?)(\>\=)(\S+)/";        
        $regs[] = "/@(\S+?)(\<\=)(\'.+\')/U";
        $regs[] = "/@(\S+?)(\<\=)(\S+)/";
        $regs[] = "/@(\S+?)([=\<\>\!]{1,2})(\'.+\')/U";
        $regs[] = "/@(\S+?)([=\<\>\!]{1,2})(\S+)/";
        $regs[] = "/( AND )@(\S+?)( IS NOT EXISTS)/";
        $regs[] = "/( OR )@(\S+?)( IS NOT EXISTS)/";
        $regs[] = "/()@(\S+?)( IS NOT EXISTS)/";
        return $regs;
	}
	
    static function parseCondition($condition,$is_entity=false,$table="",$adapter="") {
        global $classmatch,$not_exist_fields,$not_exist_ops,$classname;
        $regs = PDODataAdapter::getConditionRegs();
        $tbl=$table;
 
        $reg_count=0;
        $conds = array();
        foreach ($regs as $reg) {
            $matches = array();
            while (preg_match($reg,$condition,$matches)) {
                $reg_count++;
                if ($tbl=="")
                    $table = "f".$reg_count;              
                if ($matches[1]=="classname") {
					if ($classmatch=="") {
						$classmatch=" WHERE f.".$matches[1].$matches[2].$matches[3];
						$classname = str_replace("'","",str_replace("*","",str_replace("%","",$matches[3])));
						$condition = preg_replace($reg,"",$condition,1);
					} else {
						$classmatch.=" AND f.".$matches[1].$matches[2].$matches[3];
						$classname = str_replace("'","",str_replace("*","",str_replace("%","",$matches[3])));
						$condition = preg_replace($reg,"",$condition,1);
					}
                } else
                if ($matches[3]==" IS NOT EXISTS") {
                    $not_exist_fields[] = $matches[2];
                    $not_exist_ops[] = $matches[1];
                    $condition = preg_replace($reg,"",$condition,1);
                } else if ($matches[1]=="entityId") {
					if ($classmatch == "") {
						$classmatch=" WHERE f.".$matches[1].$matches[2].$matches[3];
						$condition = preg_replace($reg,"",$condition,1);
					} else {
						$classmatch.=" AND f.".$matches[1].$matches[2].$matches[3];
						$condition = preg_replace($reg,"",$condition,1);
					}
				}
                else  {
                    $result = PDODataAdapter::replaceIt($matches,$is_entity,$table,$adapter);
                    $condition = preg_replace($reg,$result,$condition,1);
                }
                $condition = str_replace("'NULL'","NULL",$condition);                
            }
        }
        $condition = preg_replace("/\) AND \(f\.entityId=(.*)\.entityId /U",") JOIN fields AS \\1 ON (f.entityId=\\1.entityId ",$condition);
        $condition = preg_replace("/\) OR \(f\.entityId=(.*)\.entityId /U",") LEFT JOIN fields AS \\1 ON (f.entityId=\\1.entityId ",$condition);
        return trimSpaces($condition);
    }
    
    function replaceIt($params,$is_entity=false,$table,$adapter="") {
        $name = $params[1];
        $operation = $params[2];
        $value = $params[3];
        if (trim($value==""))
            return "";
        if (stripos($name,".")!==FALSE) {
            $name_array = explode(".",$name);
            $name = array_shift($name_array);
            if (stripos($name,"[")!==FALSE) {
                $matches = array();
                $result = array();
                while (preg_match("/.*\[(.*)\]/U",$name,$matches)) {                    
                    $result[] = str_replace('"','',str_replace("'","",$matches[1]));
                    $name = preg_replace("/\[.*\]/U","",$name,1);                    
                }            
                $result = array_reverse($result);
                $nm = $result[0];
                $i=1;
                $ending="";
                $tble=$table;
                foreach ($result as $item) {
                    if ($item!="")
                        $ending.=" AND ".$tble.".arrayItemId IN (SELECT id FROM fields b".$tble."_".$i." WHERE b".$tble."_".$i.".name='".$item."'";
                    else
                        $ending.=" AND ".$tble.".arrayItemId IN (SELECT id FROM fields b".$tble."_".$i." WHERE";
                    $tble = "b".$tble."_".$i;
                    $i++;
                }
                $ending .= " ".$tble.".arrayItemId IN (SELECT id FROM fields b".$tble."_".$i." WHERE b".$tble."_".$i.".name='".$name."')";
                for ($o=0;$o<$i;$o++);
                    $ending .= ")";
                $is_array=true;
            } else
                $is_array=false;
            if ($adapter->driver!="pdo_sqlite")
                $value = "(SELECT CONCAT(a".$table.".classname,'_',a".$table.".entityId) FROM fields a".$table." WHERE ".implode(".",$name_array).$operation.$value."";
            else
              $value = "(SELECT a".$table.".classname || '_' || a".$table.".entityId FROM fields a".$table." WHERE ".implode(".",$name_array).$operation.$value."";
            if (!$is_entity) {
                if (!$is_array)
                    $result = "(f.entityId=".$table.".entityId AND ".$table.".name='".$name."' AND ".$table.".value IN ".$value." AND ".$table.".arrayItemId IS NULL))"; 
                else
                 $result = "(f.entityId=".$table.".entityId AND ".$table.".name='".$nm."' AND ".$table.".value IN ".$value."".$ending.")"; 
            }
            else {
                if (!$is_array)                
                    $result = "(".$table.".name='".$name."' AND ".$table.".value IN ".$value." AND ".$table.".arrayItemId IS NULL))"; 
                else
                 $result = "(".$table.".name='".$name."' AND ".$table.".value IN ".$value."".$ending.")"; 
            }
            $result = PDODataAdapter::parseCondition($result,true,"a".$table,$adapter);
        }
        else {         
            if (stripos($name,"[")!==FALSE) {
                $matches = array();
                $result = array();
                while (preg_match("/.*\[(.*)\]/U",$name,$matches)) {                    
                    $result[] = str_replace('"','',str_replace("'","",$matches[1]));
                    $name = preg_replace("/\[.*\]/U","",$name,1);                    
                }            
                $result = array_reverse($result);
                $nm = array_shift($result);
                $i=1;
                $ending="";
                $tble=$table;
                foreach ($result as $item) {
                    if ($item!="")
                        $ending.=" AND ".$tble.".arrayItemId IN (SELECT id FROM fields b".$tble."_".$i." WHERE b".$tble."_".$i.".name='".$item."'";
                    else
                        $ending.=" AND ".$tble.".arrayItemId IN (SELECT id FROM fields b".$tble."_".$i." WHERE";
                    $tble = "b".$tble."_".$i;
                    $i++;
                }
                $ending .= " ".$tble.".arrayItemId IN (SELECT id FROM fields b".$tble."_".$i." WHERE b".$tble."_".$i.".name='".$name."')";
                for ($o=0;$o<$i;$o++);
                    $ending .= ")";
                $is_array=true;
            } else
                $is_array=false;
            if (!$is_entity)
                if (!$is_array) {
					if (is_numeric(str_replace("'","",$value)))
						$valuename = "CAST(".$table.".value AS UNSIGNED)";
					else
						$valuename = $table.".value";
                    $result = "(f.entityId=".$table.".entityId AND ".$table.".name='".$name."' AND ".$valuename.$operation.$value." AND ".$table.".arrayItemId IS NULL)";
				}
                else {
					if (is_numeric(str_replace("'","",$value)))
						$valuename = "CAST(".$table.".value AS UNSIGNED)";
					else
						$valuename = $table.".value";
                    $result = "(f.entityId=".$table.".entityId AND ".$table.".name='".$nm."' AND ".$valuename.$operation.$value.$ending.")";
				}
            else {
				if (is_numeric(str_replace("'","",$value)))
					$valuename = "CAST(".$table.".value AS UNSIGNED)";
				else
					$valuename = $table.".value";				
                if (!$is_array) 
                    $result = "(".$table.".name='".$name."' AND ".$valuename.$operation.$value." AND ".$table.".arrayItemId IS NULL)";
                else
                    $result = "(".$table.".name='".$nm."' AND ".$valuename.$operation.$value.$ending.")";
            }
        }
        return $result;
    }  
    
    static function getFieldJoin($field,$table,$adapter="") {
        $field_array = explode(".",$field);
        if (count($field_array)>1) {
            if ($adapter!="" and $adapter->driver!="pdo_sqlite")
                return "LEFT JOIN fields AS ".$table." ON (f.value=CONCAT(".$table.".classname,'_',".$table.".entityId) AND ".$table.".name='".$field_array[1]."' AND f.name='".$field_array[0]."')";
            else
              return "LEFT JOIN fields AS ".$table." ON (f.value=".$table.".classname || '_' || ".$table.".entityId AND ".$table.".name='".$field_array[1]."' AND f.name='".$field_array[0]."')";
            //return "LEFT JOIN fields AS ".$table." ON (".$table.".entityId=f.entityId AND ".$table.".name='".$field_array[0]."' AND ".$table.".arrayItemId IS NULL)";
            $field_array = array_reverse($field_array);
            $result_field = array_shift($field_array);
            $result = "LEFT JOIN fields AS ".$table." ON (".$table.".name='".$result_field."'";
            $i=0;
            $pred_table = $table;
            foreach ($field_array as $new_field) {
                if ($adapter->driver!="pdo_sqlite")
                    $result .= " AND ".$table.".entityId=(SELECT d".$table.".entityId FROM fields AS d".$table." WHERE d".$table.".arrayItemId is NULL AND CONCAT(d".$table.".classname,'_',d".$table.".entityId)=(SELECT dd".$table.".value FROM fields AS dd".$table." WHERE dd".$table.".arrayItemId IS NULL AND dd".$table.".name='".$new_field."'";
                else
                    $result .= " AND ".$table.".entityId=(SELECT d".$table.".entityId FROM fields AS d".$table." WHERE d".$table.".arrayItemId is NULL AND d".$table.".classname || '_' || d".$table.".entityId=(SELECT dd".$table.".value FROM fields AS dd".$table." WHERE dd".$table.".arrayItemId IS NULL AND dd".$table.".name='".$new_field."'";
                $pred_table = $table;
                $table = "dd".$table;
                $i++;
            }
            $i=$i*2;
            for ($ii=0;$ii<$i;$ii++)
                $result .= ")";
            return $result.")";
        } else {
            return "LEFT JOIN fields AS ".$table." ON (".$table.".entityId=f.entityId AND ".$table.".name='".$field."')";
        }
    }
    
    function getId() {
        if ($this->module_id!="")
            return get_class($this)."_".$this->module_id."_".$this->name;
        else
            return get_class($this)."_".$this->name;
    }
    
    function setLinks($links) {
    	if ($this->entity->name=="")
    		return 0;
    	if (!$this->connected) 		
    		$this->connect();
    	if (!is_array($links))
    		$links = array($links);
    	if (!$this->connected)
    		$this->connect();
    	if (!$this->connected)
    		return 0;
    	foreach ($links as $link) {
    		if (is_object($link))
    			$link = $link->getId();
    		$parts = explode("_",$link);
    		$class = trim(array_shift($parts));
    		$id = trim(array_pop($parts));
    		if ($id==$this->name)
    			continue;
    		if ($id=="" or $class=="")
    			continue;
    		$query = "SELECT * FROM links WHERE (entity1=:current AND entity2=:id) OR (entity1=:id AND entity2=:current)";
  			$stmt = $this->dbh->prepare($query);
    		$name = $this->entity->name;
  			$stmt->bindParam(":id",$id);
  			$stmt->bindParam(":current",$name);
  			$stmt->execute();  		    		
    		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    		if (count($result)>0)
    			continue;   
    		$query = "INSERT INTO links (entity1,entity2,class1,class2) VALUES(:id1,:id2,:class1,:class2)";
    		$stmt = $this->dbh->prepare($query);
    		$stmt->bindParam(":id1",$name);
    		$stmt->bindParam(":id2",$id);
    		$stmt->bindParam(":class1",get_class($this->entity));
    		$stmt->bindParam(":class2",$class);
    		$stmt->execute();     		
    	}
    }
    
    function getLinks($classes="",$load=false) {
    	if ($this->entity->name=="")
    		return 0;
    	if (!is_array($classes))
    		$classes = array($classes);
    	$conditionArray = array();    	
    	foreach ($classes as $class) {
    		if (strpos($class,"*")!==FALSE or strpos($class,"%")!==FALSE) {
    			$class = str_replace("*","%",$class);
    			$conditionArray[] = "class1 LIKE '".$class."'";    			
    		} else {
    			$conditionArray[] = "class1='".$class."'";    			
    		}
    	}
    	if (count($conditionArray)>0) {
    		$condition1 = "entity1=".$this->entity->name." AND (".str_replace("class1","class2",implode(" OR ",$conditionArray)).")";
    		$condition2 = "entity2=".$this->entity->name." AND (".implode(" OR ",$conditionArray).")";
    	} else {
    		$condition1 = "entity1=".$this->entity->name;
    		$condition2 = "entity2=".$this->entity->name;    		
    	}
    	$query = "SELECT class2 as classname,entity2 as entityId FROM links WHERE ".$condition1." UNION 
    			  SELECT class1 as classname,entity1 as entityId FROM links WHERE ".$condition2;
    	
		if (!$this->connected)
			$this->connect();

		if (!$this->connected)
			return 0;
    	$stmt = @$this->dbh->prepare($query);
   		$arr = array();
    	if ($stmt) {
	    	$stmt->execute();
	   		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
	   		global $Objects;
	   		foreach ($result as $item) {
	   			if ($load)
	   				$arr[] = $item["entityId"];
	   			else
	   				$arr[$item["entityId"]] = $Objects->get($item["classname"]."_".$this->entity->module_id."_".$item["entityId"]);
	   		}
    	}   		
   		if (!$load)
   			return $arr;
   		else
   			return PDODataAdapter::makeQuery("SELECT entities FROM fields WHERE entityId IN (".implode(",",$arr).")");
    }
    
    function getLinkClasses() {
    	$query = "SELECT DISTINCT(tbl.classname) as classname FROM (SELECT class2 as classname FROM links WHERE entity1=".$this->entity->name." UNION
    			  SELECT class1 as classname FROM links WHERE entity2=".$this->entity->name.") as tbl";
    	if (!$this->connected)
    		$this->connect();
    	if (!$this->connected)
    		return 0;
    	$stmt = @$this->dbh->prepare($query);
    	$result = array();    	    
    	if ($stmt) {
    		$stmt->execute();
	   		$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
    		foreach ($res as $item) {
    			$result[$item["classname"]] = $item["classname"];
    		}
    	}
    	return $result;
    }
    
    function removeLinks($links) {
    	if ($this->entity->name=="")
    		return 0;
    	if (!is_array($links))
    		$links = array($links);
    	$arr = array();
    	foreach ($links as $link) {
    		if (is_object($link) and method_exists($link,"getId"))
    			$link = $link->getId();
    		$arr[] = array_pop(explode("_",$link));
    	}
    	if (count($arr)>0) {
    		if (!$this->connected)
    			$this->connect();
    		if (!$this->connected)
    			return 0;    		
    		$query = "DELETE FROM links WHERE (entity1 IN (".implode(",",$arr).") AND entity2=".$this->name.") OR (entity2 IN (".implode(",",$arr).") AND entity1=".$this->name.")";
    		if (!$this->connected)
    			$this->connect();
    		$stmt = $this->dbh->prepare($query);
    		$stmt->execute();
    		$stmt = $this->dbh->prepare("OPTIMIZE TABLE links");
    		$stmt->execute();
    	}
    }
    
    function getRights() {
    	if ($this->entity->name=="") {
    		if (is_object($this->entity))
    			$this->entity->rights = array("#all_users" => "2");
    		return 0;
    	} else {
    		if (!$this->connected)
    			$this->connect();
    		
    		if (!$this->connected)
    			return 0;
    		$entityId = str_replace($this->module_id."_","",$this->entity->getId());
    		$query = "SELECT * FROM rights WHERE entity='".$entityId."'";
    		$stmt = @$this->dbh->prepare($query);
   	    	if ($stmt) {
	    		$stmt->execute();
		   		$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
		   		if (count($res)>0)
		   			$this->entity->rights = array();
    			foreach ($res as $item) {
    				$this->entity->rights[$item["object"]] = $item["rule"];
    			}
   	    	}
    	}
    }
    
    function removeRights() {
    	if ($this->entity->name=="") {
    		 return 0;
    	}
    	if (!$this->connected)
    		$this->connect();
    	
    	if (!$this->connected)
    		return 0;
    	$entityId = str_replace($this->module_id."_","",$this->entity->getId());
    	$query = "DELETE FROM rights WHERE entity='".$entityId."'";
    	$stmt = $this->dbh->prepare($query);
    	$stmt->execute();
    	if ($this->driver!="pdo_sqlite") {
    		$stmt = $this->dbh->prepare("OPTIMIZE TABLE rights");
    		$stmt->execute();
    	}    	     	
    }
    
    function setRights() {
    	if ($this->entity->name=="") {
    		return 0;
    	}
    	if (!$this->connected)
    		$this->connect();
    	if (!$this->connected)
    		return 0;
    	
    	 $this->removeRights();
    	 $entityId = str_replace($this->module_id."_","",$this->entity->getId());
    	 foreach ($this->entity->rights as $key=>$value) {
    	 	$query = "INSERT INTO rights (object,entity,rule) VALUES('".$key."','".$entityId."',".$value.")";
    	 	$stmt = $this->dbh->prepare($query);
    	 	$stmt->execute();    	 	 
    	 }
    }
    
    function getClassTagNames() {
    	global $Objects;
    	$results = array();
    	$class = get_class($this->entity);
    	if (!$this->connected)
    		$this->connect();
    	if (!$this->connected)
    		return array();
    	$stmt = $this->dbh->prepare("SELECT DISTINCT value FROM fields WHERE name='tags' AND classname=:class");
    	$stmt->bindParam(":class",$class);
    	$stmt->execute();
    	$r = $stmt->fetchAll(PDO::FETCH_ASSOC);
    	foreach ($r as $res) {
    		$tags = explode("~",$res["value"]);
    		foreach ($tags as $tag)
    			$results[$tag] = $tag;
    	}
    	return $results;
    }    
    
    function getClassFieldValues($tag) {
    	global $Objects;
    	$results = array();
    	$class = get_class($this->entity);
    	if (!$this->connected)
    		$this->connect();
    	if (!$this->connected)
    		return array();
    	$stmt = $this->dbh->prepare("SELECT DISTINCT value FROM fields WHERE name=:tag AND classname=:class");
    	$stmt->bindParam(":class",$class);
    	$stmt->bindParam(":tag",$tag);
    	$stmt->execute();
    	$r = $stmt->fetchAll(PDO::FETCH_ASSOC);
    	foreach ($r as $res) {
    		$tags = explode("~",$res["value"]);
    		foreach ($tags as $tag_value)
    			$results[$tag_value] = $tag_value;
    	}
    	return $results;    	 
    }
}
?>