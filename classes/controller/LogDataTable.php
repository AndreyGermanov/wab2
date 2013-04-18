<?php
/**
 * Данный класс предназначен для отображения списка сущностей в виде таблицы.
 * Он выполняет запрос к указанному хранилищу adapter в котором он ищет сущности
 * указанных классов, которые передаются в формате сlassName, применяя при этом
 * условие condition. К этому условию также может добавляться параметры LIMIT,
 * которые вычисляются исходя из переменных currentPage и itemsPerPage. Параметр
 * sortOrder задает порядок сортировки.
 *
 * В колонках таблицы отображаются поля сущности, которые указываются в переменной
 * fieldList. Также добавляются две дополнительные колонки в начало: "GroupCheckbox"
 * с флажком, для того чтобы можно было выделять несколько строк для работы с ними
 * как с группой и entityImage - картинка-кнопка типа hidden, позволяющая разворачивать
 * сущность в случае если у нее есть дочерние элементы. Картинка, отображаемая
 * на кнопке определяется одной из двух иконок: entityIcon - если у сущности нет
 * потомков и groupIcon если у сущности есть потомки. Также вместо entityImage
 * может браться картинка из поля самой сущности. Поле определеяется параметром
 * entityImageField.
 *
 * Наличие потомков у сущности определяется значением поля сущности,
 * на которое указывает свойство parentField. Это должно быть поле типа Entity,
 * которая ссылается на родительскую сущность. Для каждой сущности определяется,
 * есть ли в базе данных сущности, у которых поле parentField указывает на нее
 * и если есть, то считается что у сущности есть потомки и в таблице для нее
 * отображается groupIcon. Будет ли вообще работать режим иерархичности определяется
 * параметром hierarchy. Если он установлен в true, то разворачивание по иерархии
 * будет работать, иначе нет.
 *
 * Также есть еще одна дополнительная колонка entityName, в которой находится имя
 * текущей сущности.
 *
 * Если указан параметр sortField и в нем указано поле, то по умолчанию сортировка
 * производится по этому полю и только в этом случае появляются кнопки "вверх"/
 * "вниз" над таблицей, что позволяет менять порядок сортировки полей вручную
 * на уровне базы данных, меняя значение поля sortField.
 *
 * @author andrey
 */
class LogDataTable extends DataTable {

    function construct($params) {
        parent::construct($params);
        global $defaultCacheDataAdapter;
        $this->adapter = $defaultCacheDataAdapter;
        $this->persistedFields = "";
        $this->className = "";
        if (!$this->adapter->isPDO)
            $this->condition = "@parent=-1";
        else
            $this->condition = "@parent IS NOT EXISTS";
        $this->sortOrder = "";
        $this->fieldList = "";
        $this->hierarchy = false;

        $this->itemsPerPage = 0;
        $this->currentPage = 1;
        $this->entityId = "";
		$this->autoload = "true";
		$this->entityImagesStr = "";
		$this->additionalLinksStr = "";
		$this->topLinkObject = "";
		
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        $this->skinPath = $app->skinPath;

        $this->entityImage = $this->skinPath."images/Buttons/EntityImage.png";
        $this->groupImage = $this->skinPath."images/Buttons/entityGroupImage.png";


        $this->defaultClassName = "";
        $this->entityImageField = "";

        $this->parentField = "parent";
        $this->sortField = "sortOrder";

        $this->editorType = "WABWindow";

        $this->contextMenuId = "";

        $this->parent_object_id = "";

        $this->windowWidth = 400;
        $this->windowHeight = 500;
        $this->template = "templates/controller/LogDataTable.html";
		$this->entityCount = "0";
        $this->windowTitle = "";
        $this->additionalFields = "";
        $this->divName = "";
        $this->destroyDiv = false;
		$this->showHierarchy = false;
        $this->tableId = "";
        $this->forEntitySelect = false;        
        $this->handler = "scripts/handlers/core/EntityDataTable.js";
        
        $this->clientClass = "LogDataTable";
        $this->parentClientClasses = "DataTable~Entity";        
    }

    function getArgs() {
        global $Objects;
        $this->condition = str_replace('"',"'",$this->condition);
        $subnets = $Objects->get("DhcpSubnets_".$this->module_id."_subnets");
        $subnets->load();
        foreach ($subnets->subnets as $sub) {
            $sub->loadHosts();
        }
        if ($this->adapterId!="")
            $this->adapter = $Objects->get($this->adapterId);     
            $this->loaded=false;
        if ($this->loaded!=true) {
            if ($this->sortOrder=="")
                if (!$this->adapter->isPDO)
                    $this->sortOrder = $this->sortField." ASC integers";
                else
                    $this->sortOrder = $this->sortField." ASC";
            $this->condition = str_replace("xoxo","#",$this->condition);
            $fldList = $this->fieldList;
            $this->fieldList = str_replace("~",",",$this->fieldList);            
            $this->sortOrder = str_replace("~",",",$this->sortOrder);
            $adapter = $Objects->get("FullAuditDataAdapter_".$this->module_id."_adapter");
            $count = 0;
            if (!$adapter->connected)
                $adapter->connect();            
            if ($this->condition!="")
                $this->condition = "WHERE ".$this->condition;
            if ($this->sortOrder!="" and stripos($this->sortOrder,"ORDER BY")===FALSE)
                $this->sortOrder = "ORDER BY ".$this->sortOrder;
            if ($adapter->connected) {
                $stmt = $adapter->dbh->prepare(str_replace("null","","SELECT count(eventDate) as cnt FROM eventlog ".$this->condition." ".$this->sortOrder));
	        $stmt->execute();
    		$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        	$count = $res[0]["cnt"];  
    	    }
            if ($this->itemsPerPage!=0) {
                $limit = (($this->itemsPerPage)*($this->currentPage-1)).",".$this->itemsPerPage;                
            } else
                $limit = "";
            if ($limit!="")
                $limit = "LIMIT ".$limit;
            if ($adapter->connected) {
                $stmt = $adapter->dbh->prepare(str_replace("null","","SELECT * FROM eventlog ".$this->condition." ".$this->sortOrder." ".$limit));
	        $stmt->execute();
    		$entities = $stmt->fetchAll();
    	    }
            $this->entityCount = $count;
            if ($this->itemsPerPage>0)
                $this->numPages = ceil($this->entityCount/$this->itemsPerPage);
            else
                $this->numPages = 0;

            $this->adapterId = $this->adapter->getId();
            $result = parent::getArgs();
            $id = $this->getId();
            
            $str = "";
            $str .= $this->getId()."Rows = new Array;\n";
            $str .= $this->getId()."Rows[0] = new Array;\n";
            $str .= $id."tbl.columns = new Array;\n";
            $str .= $this->getId()."Rows[0]['class'] = '';\n";
            $str .= $this->getId()."Rows[0]['properties'] = 'valign=top';\n";
            $str .= $this->getId()."Rows[0]['cells'] = new Array;\n";
            
            $str .= $id."tbl.columns[0] = new Array;\n";
            $str .= $id."tbl.columns[0]['name'] = 'entityName';\n";
            $str .= $id."tbl.columns[0]['title'] = '';\n";
            $str .= $id."tbl.columns[0]['class'] = 'hidden';\n";
            $str .= $id."tbl.columns[0]['properties'] = 'width=1';\n";
            $str .= $id."tbl.columns[0]['control'] = 'plaintext';\n";
            $str .= $id."tbl.columns[0]['control_properties'] = '';\n";
            $str .= $id."tbl.columns[0]['must_set'] = false;\n";
            $str .= $id."tbl.columns[0]['unique'] = false;\n";
            $str .= $id."tbl.columns[0]['readonly'] = false;\n";

            $str .= $this->getId()."Rows[0]['cells'][0] = new Array;\n";
            $str .= $this->getId()."Rows[0]['cells'][0]['properties'] = 'width=1%';\n";
            $str .= $this->getId()."Rows[0]['cells'][0]['class'] = 'hidden';\n";
            $str .= $this->getId()."Rows[0]['cells'][0]['value'] = 'header';\n";
            $str .= $this->getId()."Rows[0]['cells'][0]['control'] = 'plaintext';\n";
            $str .= $this->getId()."Rows[0]['cells'][0]['control_properties'] = '';\n";

            $str .= $id."tbl.columns[1] = new Array;\n";
            $str .= $id."tbl.columns[1]['name'] = 'entityImage';\n";
            $str .= $id."tbl.columns[1]['title'] = '';\n";
            $str .= $id."tbl.columns[1]['class'] = 'cell';\n";
            $str .= $id."tbl.columns[1]['properties'] = 'width=1%';\n";
            $str .= $id."tbl.columns[1]['control'] = 'hidden';\n";
            $str .= $id."tbl.columns[1]['control_properties'] = 'showValue=false';\n";
            $str .= $id."tbl.columns[1]['must_set'] = false;\n";
            $str .= $id."tbl.columns[1]['unique'] = false;\n";
            $str .= $id."tbl.columns[1]['readonly'] = false;\n";

            $str .= $this->getId()."Rows[0]['cells'][1] = new Array;\n";
            $str .= $this->getId()."Rows[0]['cells'][1]['properties'] = 'width=1%';\n";
            $str .= $this->getId()."Rows[0]['cells'][1]['class'] = 'header';\n";
            $str .= $this->getId()."Rows[0]['cells'][1]['value'] = '';\n";
            $str .= $this->getId()."Rows[0]['cells'][1]['control'] = 'plaintext';\n";
            $str .= $this->getId()."Rows[0]['cells'][1]['control_properties'] = '';\n";
            $c = 2;
            $fieldList = explode(",",$this->fieldList);
            foreach($fieldList as $field) {
                if (stripos($field,"AS")!==FALSE) {
                    $arr = explode("AS",$field);
                    $name=$arr[0];
                    $arr = explode(" ",$arr[1]);
                    array_shift($arr);    
                    array_shift($arr);
                    $title = trim(implode(" ",$arr));
                } else {
                    $field_parts = explode(" ",$field);
                    $name = array_shift($field_parts);
                    if (count($field_parts)>0)
                        $title = implode(" ",$field_parts);
                    else
                        $title = $name;
                }

                $str .= $id."tbl.columns[$c] = new Array;\n";
                $str .= $id."tbl.columns[$c]['name'] = '".trim($name)."';\n";
                $str .= $id."tbl.columns[$c]['class'] = 'cell';\n";
                $str .= $id."tbl.columns[$c]['properties'] = '';\n";
                $str .= $id."tbl.columns[$c]['control'] = 'plaintext';\n";
                $str .= $id."tbl.columns[$c]['control_properties'] = '';\n";
                $str .= $id."tbl.columns[$c]['must_set'] = false;\n";
                $str .= $id."tbl.columns[$c]['unique'] = false;\n";
                $str .= $id."tbl.columns[$c]['readonly'] = false;\n";

                $str .= $this->getId()."Rows[0]['cells'][$c] = new Array;\n";
                $str .= $this->getId()."Rows[0]['cells'][$c]['properties'] = '';\n";
                $str .= $this->getId()."Rows[0]['cells'][$c]['class'] = 'header';\n";
                $str .= $this->getId()."Rows[0]['cells'][$c]['value'] = '".html_entity_decode($title,ENT_QUOTES,'UTF-8')."';\n";
                $str .= $this->getId()."Rows[0]['cells'][$c]['control'] = 'header';\n";
                //$str .= $this->getId()."Rows[0]['cells'][$c]['control_properties'] = 'fieldType=$type';\n";
                $c++;
            }
            $rw=1;
            if (isset($entities) and is_array($entities))
            foreach ($entities as $entity) {
            	$str .= $this->getId()."Rows[$rw] = new Array;\n";
                $str .= $this->getId()."Rows[$rw]['class'] = '';\n";
                $str .= $this->getId()."Rows[$rw]['properties'] = 'valign=top';\n";
                $str .= $this->getId()."Rows[$rw]['cells'] = new Array;\n";
                $str .= $this->getId()."Rows[$rw]['cells'][0] = new Array;\n";
                $str .= $this->getId()."Rows[$rw]['cells'][0]['properties'] = 'width=1%';\n";
                $str .= $this->getId()."Rows[$rw]['cells'][0]['class'] = 'hidden';\n";
                $str .= $this->getId()."Rows[$rw]['cells'][0]['value'] = '".$rw."';\n";//AuditRecord_".$entity["eventType"].$entity["eventDate"]."';\n";//AuditRecord_".$this->module_id."_".$rw."';\n";
                $str .= $this->getId()."Rows[$rw]['cells'][0]['control'] = 'plaintext';\n";
                $str .= $this->getId()."Rows[$rw]['cells'][0]['control_properties'] = '';\n";
                $entityImage = $this->skinPath."images/Buttons/EntityImage.png";
                $str .= $this->getId()."Rows[$rw]['cells'][1] = new Array;\n";
                $str .= $this->getId()."Rows[$rw]['cells'][1]['properties'] = 'width=1%';\n";
                $str .= $this->getId()."Rows[$rw]['cells'][1]['class'] = 'cell';\n";
                $str .= $this->getId()."Rows[$rw]['cells'][1]['value'] = 'false';\n";
                $str .= $this->getId()."Rows[$rw]['cells'][1]['control'] = 'hidden';\n";
                $str .= $this->getId()."Rows[$rw]['cells'][1]['control_properties'] = 'fieldList=".str_replace(",","~",$fldList).",buttonImage=".$entityImage.",actionButton=true';\n";
                $c=2;
                
                foreach ($fieldList as $field) {
                    $field = str_replace(".","->",$field);
                    $field_parts = explode(" ",$field);
                    $title = "";
                    if ($field_parts[0]!="") {
                        if ($field_parts[0]=="eventDate") {
                            if ($entity[$field_parts[0]]>9999999999)
                                $title = date("d.m.Y H:i:s",$entity[$field_parts[0]]/1000);
                            else
                                $title = date("d.m.Y H:i:s",$entity[$field_parts[0]]);
                        }
                        else {
                            $report = $Objects->get("FullAuditReport_".$this->module_id."_report");
                            $title = $entity[$field_parts[0]];
                            if (@$field_parts[0]=="eventIP") {                                    
                                $title = int2ip($title);
                                $rr = $Objects->query("DhcpHost","@fixed_address=='".$title."'");
                                if (count($rr)==1)
                                    $title .= " (".array_shift($rr)->name.")";
                            }
                            if (@$field_parts[0]=="eventType") {
                                $title = $report->eventTitles[$title];
                            }                                                                                                    
                        }
                    }
                    $title = str_replace("'","\'",$title);
                    $str .= $this->getId()."Rows[$rw]['cells'][$c] = new Array;\n";
                    $str .= $this->getId()."Rows[$rw]['cells'][$c]['properties'] = '';\n";
                    $str .= $this->getId()."Rows[$rw]['cells'][$c]['class'] = 'cell';\n";
                    $str .= $this->getId()."Rows[$rw]['cells'][$c]['value'] = '$title';\n";                    
                    if ($field_parts[count($field_parts)-1]!="booleans") {
                        $str .= $this->getId()."Rows[$rw]['cells'][$c]['control'] = 'static';\n";                    
                        $str .= $this->getId()."Rows[$rw]['cells'][$c]['control_properties'] = '';\n";
                    } else {
                        $str .= $this->getId()."Rows[$rw]['cells'][$c]['control'] = 'boolean';\n";                    
                        $str .= $this->getId()."Rows[$rw]['cells'][$c]['control_properties'] = 'control_type=checkbox';\n";                        
                    }
                    $c++;
                }
                $rw++;
            }
            if ($this->entityCount=="")
            	$this->entityCount = "0";
            if ($this->numPages=="")
            	$this->numPages = "0";
            $str .= $this->getId()."EntityCount = ".$this->entityCount.";";
            $str .= $this->getId()."NumPages = ".$this->numPages.";";
            $this->data = $str;
            $this->loaded = true;
        }
        $result["{data}"] = $this->data;
        $result["{className}"] = $this->className;
        if ($this->destroyDiv)
            $result["{destroyDivStr}"] = "true";
        else
            $result["{destroyDivStr}"] = "false";

        if ($this->forEntitySelect)
            $result["{forEntitySelectStr}"] = "true";
        else
            $result["{forEntitySelectStr}"] = "false";    
        return $result;
    }
}
?>