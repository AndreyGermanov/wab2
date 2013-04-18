<?php
/**
 * Класс, реализующий отображение дерева сущностей с произвольным корнем.
 *
 * Отображает все сущности, соответствующие классам, указанным в className и
 * условию, указанному в condition. Сущности сортируются в соответствии с
 * условием sortOrder.
 *
 * Запрос выполняется с помощью адаптера данных, который определен в поле adapter.
 *
 * Иерархия строится на базе поля "parent" сущности, при условии что свойство
 * hierarchy установлено в true.
 *
 * В случае если любая сущность в дереве содержит в себе дочерние сущности,
 * то есть после загрузки его поле hasChildren=true, то для этой сущности
 * отображается значок, путь к которому указан в свойства groupEntityImage, а
 * также появляется символ + для раскрытия этой сущности. Иначе отображается
 * значок entityImage и символа + нет.
 *
 * При открытии узла дерева сущности, выполняется запрос, в который передается
 * parent.name="имя_сущности_родителя", а также условие childCondition.
 * Также в запрос передаются имена классов сущностей в поле childClassName,
 * которое по умолчанию равно className.
 *
 * В качестве текста элемента дерева используется свойство, указанное в свойстве
 * titleField.
 *
 * В родительском классе Tree предусмотрены обработчики событий onObjectClick,
 * onContextMenu, onMouseOver, onMouseOut, которые позволяют описать реакцию
 * на взаимодействие с определенным элементом сущности. Это поведение могут
 * переопределять потомки.
 *
 * По умолчанию происходит следующее:
 *
 * При наведении курсора мыши на сущность и при "отведении" курсора выполняются
 * обработчики onMouseOver и onMouseOut, которые будут подсвечивать активный
 * элемент или убирать подсветку (менять класс элемента на tree_item_hover и
 * tree_item).
 *
 * При нажатии мышью на элемент будет выполняться действие в зависимости от
 * значения свойства editorType:
 *
 * window - отображать сущность во всплывающем окне размером windowWidth,
 * windowHeight с заголовком windowTitle
 *
 * div - будет отображать окно сущности в DIV-е с именем divName. DIV будет
 * удаляться если свойство destroyDiv=true.
 *
 * WABWindow - отображать сущность в окне панели управления размера windowWidth,
 * windowHeight. В заголовке будет отображаться заголовок сущности и картинка
 * сущности.
 *
 * entityDataTable - отображать дочерние сущности в указанной таблице
 * tableId. В этом случае к тексту запроса entityDataTable (condition)
 * добавляется (@parent.@name='имя-сущности') и className устанавливается в
 * childClassName. Затем вызывается rebuild таблицы EntityDataTable. Работает
 * только для сущностей, у которых есть дочерние сущности.
 *
 * none - ничего не будет происходить.
 *
 * В любом случае, при нажатии на сущности будет генерироваться событие
 * NODE_CLICKED, в которое будет передаваться имя дерева в параметре object_id
 * и имя сущности в параметре entity_id. На это событие смогут реагировать
 * любые заинтересованные объекты.
 *
 * В случае нажатия на правую кнопку мыши, всплывает контекстное меню с указанным
 * contextMenuId, если он есть.
 *
 * Дерево реагирует на событие ENTITY_CHANGED в которое передается тип изменения:
 * (add,change,delete), список изменившихся сущностей (entities). Для каждой
 * сущности определяется, может ли она быть в списке сущностей данного дерева
 * и если может, то событие трансформируется в серию событий NODE_CHANGED для
 * каждой сущности, которая может быть в этом дереве.
 *
 *
 * @author andrey
 */
class EntityTree extends Tree {

    function construct($params) {
    	 
        parent::construct($params);
        
        global $defaultCacheDataAdapter,$Objects;
        $this->className="";
        $this->condition = "";
        
        $this->sortOrder = "sortOrder ASC integers";
        $this->parent_object_id="";
        $this->adapter = $defaultCacheDataAdapter;

        $this->groupEntityImage = $this->skinPath."images/Tree/folder.gif";
        $this->entityImage = $this->skinPath."images/Tree/item.gif";

        $this->childClassName = $this->className;
        $this->treeClassName = "EntityTree";
        $this->childCondition = "";
        $this->titleField = "title";
        $this->additionalFields = "";
        $this->hierarchy = "";
        $this->result_object_id = "";
        $this->hide_root_context_menu = "false";

        $this->editorType = "none";

        $this->contextMenuClass = "WABEntityContextMenu";
        $this->rootContextMenuClass = "WABEntityRootContextMenu";

        $this->handler = "scripts/handlers/core/EntityTree.js";
        
        $this->parent_object_id = "";

        $this->windowWidth = 400;
        $this->windowHeight = 500;
		
        $this->windowTitle = "";

        $this->divName = "";
        $this->destroyDiv = false;
		$this->selectGroup = "1";
        $this->tableId = "";
        $this->forEntitySelect = false;
        $this->entityId = "";
        $this->entityParentStr = "";
        $this->windowTitle = "";
        $this->clientClass = "EntityTree";
        $this->parentClientClasses = "Tree~Entity";
        $app = $Objects->get("Application");
        if (!$app->initiated)
        	$app->initModules();
        $this->skinPath = $app->skinPath;  
    }

    function setTreeItems($className="",$condition="",$parent="",$module_id="") {
        global $Objects;
        if ($module_id=="")
            $module_id=$this->module_id;
        $this->condition = str_replace("xoxo","#",$this->condition);
        $this->condition = str_replace("yoyo","@",$this->condition);
        $this->condition = str_replace("zozo","=",$this->condition);
        if ($this->adapterId!="")
            $this->adapter = $Objects->get($this->adapterId);
        if (isset($this->adapter) and !$this->adapter->isPDO) {
            if ($this->condition=="" and $this->hierarchy!="false")
                $this->condition = "#IntegerField@parent=-1";
        } else {
            if ($this->condition=="" and $this->hierarchy!="false")
                $this->condition = "@parent IS NOT EXISTS";
        }
        
        if ($this->entityId!="" and $this->entityId!=-1) {
            $ent = $Objects->get($this->entityId);
            if (!$ent->loaded)
                $ent->load();
            $parent_str = array();
            $ent1 = $ent->parent;
            if ($ent1!=null and is_object($ent1) and method_exists($ent1,"getPresentation"))
                if (!$ent1->loaded)
                    $ent1->load();
            if ($ent1!=null and is_object($ent1) and method_exists($ent1,"getPresentation")) {
                $parent_str[] = $ent1->getId();
                while ($ent1!=null) {
                    $ent1 = $ent1->parent;                    
                    if ($ent1!=null and is_object($ent1) and method_exists($ent1,"getPresentation")) {
                        if (!$ent1->loaded)
                            $ent1->load();
                        $parent_str[] = $ent1->getId();
                    } else
                        break;
                }
            }
            $this->entityParentStr = implode(",",array_reverse($parent_str));
        }
        if ($className == "")
            $className = $this->className;
        if ($className=="") {
        	global $objGroups;
        	$result = array();
        	foreach ($objGroups as $key=>$value) {
        		$result[$key]["id"] = "ObjGroupsTree_".$this->module_id."_".$key;
        		$result[$key]["title"] = $value["title"];
        		$result[$key]["icon"] = $this->skinPath.$value["icon"];
        		$result[$key]["parent"] = "";
        		$result[$key]["loaded"] = "treeClassName=ObjGroupsTree#objGroup=".$key."#entityImage=".$this->skinPath.$value["icon"]."#loaded=false#hook=setParams#parent_object_id=".$this->getId()."#result_object_id=".$this->result_object_id."#editorType=".$this->editorType."#forEntitySelect=".$this->forEntitySelect."#selectGroup=".$this->selectGroup."#condition=".str_replace("=","zozo",$this->condition)."#tableId=".$this->tableId;
        		$result[$key]["subtree"] = "true";        		
        	}
        	$this->title = "Объекты";
        	$this->icon = $this->skinPath."images/docflow/objects.png";
        } else {
	        $cls = $Objects->get($this->className."_".$this->module_id."_List");
	        if (is_object($cls) and method_exists($cls,"setRole") and count($cls->role)==0) {
	        	$cls->setRole();
	        }
	        if (is_object($cls) and method_exists($cls,"setRole"))
	        	$this->additionalCondition .= @$cls->getRoleValue(@$cls->role["listFilter"]);
	        if ($condition== "") {
	            if ($parent=="") {
	                $this->condition = preg_replace("/^\#EntityField@parent=.+$/", "", $this->condition);
	                $this->condition = preg_replace("/\#EntityField@parent=.+ /", "", $this->condition);
	                $this->condition = preg_replace("/^\#IntegerField@parent=.+$/", "", $this->condition);
	                $this->condition = preg_replace("/\#IntegerField@parent=.+ /", "", $this->condition);
	                if ($this->hierarchy!="false") {
	                    if (!$this->adapter->isPDO) {
	                        if ($this->condition=="")
	                            $this->condition = "#IntegerField@parent=-1";
	                        else
	                            $this->condition = $this->condition." AND #IntegerField@parent=-1";
	                    } else {
	                        if ($this->condition=="")
	                            $this->condition = "@parent IS NOT EXISTS";
	                        else
	                         $this->condition = $this->condition." AND @parent IS NOT EXISTS";                        
	                    }
	                }
	                $condition = $this->condition;
	            }
	            else {
	            	if ($this->childCondition=="")
	            		$this->childCondition = $this->condition;
	                $condition = $this->childCondition;
	                $condition = str_replace("AND @parent IS NOT EXISTS","",$condition);
	                $condition = str_replace("@parent IS NOT EXISTS","",$condition);
	                $condition = preg_replace("/ AND \@parent.\@name\=[0-9]+/","",$condition);
	                $condition = preg_replace("/AND \@parent.\@name\=[0-9]+/","",$condition);
	                $condition = preg_replace("/\@parent.\@name\=[0-9]+/","",$condition);
	                
	                
	                $parent_arr = explode("_",$parent);
	                if (!$this->adapter->isPDO) {
	                    $name = array_pop($parent_arr);
	                    $condition .= "#EntityField@parent=".$name;
	                }
	                else {
	                    $parent_arr = explode("_",$parent);
	                    $name = array_pop($parent_arr);
	                    if ($condition=="")
	                    	$condition = "@parent.@name=".$name."";
	                    else
	                    	$condition .= " AND @parent.@name=".$name."";
	                }                
	                if ($parent!="-1" and $parent!="") {                   
	                    $par = $Objects->get($parent);
	                    if (!$par->loaded)
	                        $par->load();
	                    if ($par->sortFields!="")
	                        $sortOrder = $par->sortFields;
	                }
	            }
	        } else {
	        	if ($parent!="") {
	                if (!$this->adapter->isPDO) {
	                    $parent_arr = explode("_",$parent);                
	                    $name = array_pop($parent_arr);
	                    $condition = str_replace("#IntegerField@parent=-1 AND ","",$condition);
	                    $condition .= " AND #EntityField@parent=".$name;                    
	                } else {
	                    $parent_arr = explode("_",$parent);                
	                    $name = array_pop($parent_arr);                    
	                    $condition = str_replace("@parent IS NOT EXISTS","",$condition);
	                    $condition .= " AND @parent.@name=".$name."";
	                }
	                if ($parent!="-1" and $parent!="") {
	                    $par = $Objects->get($parent);
	                    if (!$par->loaded)
	                        $par->load();
	                    if ($par->sortFields!="")
	                        $sortOrder = $par->sortFields;
	                }
	            }
	        }
	        if (!$this->adapter->isPDO) {
	            if ($condition=="#Integer@parent=-1 AND #IntegerField@parent=-1")
	                $condition = "#IntegerField@parent=-1";
	        } else {
	            if ($condition=="@parent IS NOT EXISTS AND @parent IS NOT EXISTS")
	                $condition = "@parent IS NOT EXISTS";
	            
	        }
	        if (!isset($sortOrder))
	            $sortOrder = $this->sortOrder;
	        $sortOrder = str_replace("~",",",$sortOrder);
	        $items = array();
	        //try {
	        if ($this->additionalFields!="")
	            $this->additionalFields = "~".$this->additionalFields;
	        $condition .= " ".$this->additionalCondition;
	        
	        //echo $className."_".$module_id,"simple|"."name integers~".$this->titleField." strings".$this->additionalFields."~groupEntityImage strings~entityImage strings|".$condition;
	        if ($module_id!="")
	            $items = $Objects->query($className."_".$module_id,"simple|"."name integers~".$this->titleField." strings".$this->additionalFields."~groupEntityImage strings~entityImage strings|".$condition,$this->adapter,$sortOrder);
	        else
	            $items = $Objects->query($className,"simple|"."name integers~".$this->titleField." strings~groupEntityImage strings".$this->additionalFields."~entityImage strings|".$condition,$this->adapter,$sortOrder);
	        if ($this->entityId!="" and $this->entityId!=-1) {
	            $currentEntity = $Objects->get($this->entityId);
	            if (!$currentEntity->loaded)
	                $currentEntity->load();
	            $entityParentArr = explode(",",$this->entityParentStr);
	        }
	        
	        $result = array();
	        foreach ($items as $item) {            
	            $result[$item->getId()]["id"] = $item->getId();
	            $result[$item->getId()]["title"] = str_replace("#","xyxxyx",$item->fields[$this->titleField]);
//	            if ($result[$item->getId()]["title"]=="")
	            	$result[$item->getId()]["title"] = $item->getPresentation(false);
	            if ($item->childrenCount()) {
	                $result[$item->getId()]["icon"] = $this->groupEntityImage;
	                $result[$item->getId()]["parent"] = $parent;
	                if (isset($currentEntity)) {
	                    if (array_search($item->getId(), $entityParentArr)!==FALSE) {
	                        $result[$item->getId()]["loaded"] = "true";
	                    } else {
	                        if (isset($currentEntity->parent)) {
	                            if ($item->getId()==$currentEntity->parent->getId()) {
	                                $result[$item->getId()]["loaded"] = "true";
	                            }
	                            else {
	                                $result[$item->getId()]["loaded"] = "false";
	                            }
	                        } else {
	                            $result[$item->getId()]["loaded"] = "false";                        
	                        }
	                    }
	                }
	                else
	                    $result[$item->getId()]["loaded"] = "false";
	            }            
	            else {
	                $result[$item->getId()]["icon"] = $this->entityImage;
	                $result[$item->getId()]["parent"] = $parent;
	                $result[$item->getId()]["loaded"] = "true";
	            }
	        }
	        if ($this->entityParentStr!="" and $parent=="" and count($items)>0) {
	            $res_str = array();
	            $entityParentArr = explode(",",$this->entityParentStr);
	            foreach ($entityParentArr as $item_id) {                
	                $res_str[] =  $this->setTreeItems('','',$item_id);
	            }
	            $res_str = implode("|",$res_str);
	        }
        }       
        $res = array();        
        foreach($result as $value)
        {
            $res[count($res)] = implode("~",$value);
        }
        if (isset($res_str)) {           
        	$this->items_string = implode("|",$res)."|".$res_str;
        	return implode("|",$res)."|".$res_str;
        }
        else {
        	$this->items_string = implode("|",$res);
            return implode("|",$res);
        }
    }

    function getArgs() {
    	$result = parent::getArgs();

        global $Objects;

        if ($this->destroyDiv)
            $result["{destroyDivStr}"] = "true";
        else
            $result["{destroyDivStr}"] = "false";

        if ($this->forEntitySelect)
            $result["{forEntitySelectStr}"] = "true";
        else
            $result["{forEntitySelectStr}"] = "false";
        $result["{className}"] = $this->className;
        return $result;
    }

    function getId() {
        if ($this->module_id!="")
            return get_class($this)."_".$this->module_id."_".$this->name;
        else
            return get_class($this)."_".$this->name;
    }
    
    function getHookProc($number) {
		switch($number) {
			case '3': return "setTreeItemsHook";
			case '4': return "setTreeItemsHookShow";
		}
		return parent::getHookProc($number);
    }
    
    function setTreeItemsHook($arguments) {
    	$this->setArguments($arguments);
    	echo $this->setTreeItems('','',@$arguments["elem_id"]);
    }

    function setTreeItemsHookShow($arguments) {
    	$this->setArguments($arguments);
    	$this->items_string = $this->setTreeItems('','',@$arguments["elem_id"]);
    	$this->show();
    }    
}
?>