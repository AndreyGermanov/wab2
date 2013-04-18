<?php
/**
 * Класс реализует интерфейс дерева объектов с неограниченной вложенностью и динамической загрузкой элементов
 *
 * @author andrey
 */
class Tree extends WABEntity {

    public $onLeftClick="";

    // Стандартный конструктор из массива параметров
    function construct($params) {
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        $this->skinPath = $app->skinPath;
        if (count($params)>2)
            $this->module_id = array_shift($params)."_".array_shift($params);
        else
            $this->module_id = "";
        $this->name = @$params[0];
        $this->target_object = "";
        $this->title = @$params[1];
        $this->template = "templates/interface/Tree.html";
        $this->handler = "scripts/handlers/interface/Tree.js";
        $this->css = $app->skinPath."styles/Tree.css";
        $this->entityId = "";
        $this->loadedStr = "false";
        $this->loaded = true;
        $this->clientClass = "Tree";
        $this->parentClientClasses = "Entity";        
    }

    function addTreeNode($parent,$id,$title,$display="",$expandable='false',$to_frame=false) {
        if ($this->onLeftClick!="")
                $title = "<a onclick=\'".strtr($this->onLeftClick,array("{id}" => $id))."\'>".$title."</a>";
        if ($to_frame==false)
            echo "<script>addTreeNode('".$this->getId()."','".$parent."','".$id."','".$title."','".$display."',".str_replace('','',$expandable).");</script>\n";
        else
            echo "<script>window.parent.addTreeNode('".$this->getId()."','".$parent."','".$id."','".$title."','".$display."',".str_replace('','',$expandable).");</script>\n";
    }

    function moveTreeNode($id,$parent="",$before="") {
         echo "<script>moveTreeNode('".$this->getId()."','".$id."','".$parent."','".$before."');</script>\n";
    }

    function deleteTreeNode($id) {
         echo "<script>deleteTreeNode('".$this->getId()."','".$id."');</script>\n";
    }

    function deleteAllNodes($id='',$to_frame=false) {
        if (!$to_frame)
            echo "<script>deleteAllNodes('".$this->getId()."','".$id."');</script>\n";
        else
            echo "<script>window.parent.deleteAllNodes('".$this->getId()."','".$id."');</script>\n";
    }

    function getId() {
        if ($this->module_id!="")
            return get_class($this)."_".$this->module_id."_".$this->name;
        else
            return get_class($this)."_".$this->name;
    }
    
    function getArgs() {
        $result = parent::getArgs();
        if (is_string($this->loaded))
            $result["{loadedStr}"] = $this->loaded;
        else {
            if ($this->loaded)
                $result["{loadedStr}"] = "true";
            else
                $result["{loadedStr}"] = "false";
        }
        if ($result["{loadedStr}"]=="")
                $result["{loadedStr}"]="true";
        return $result;
    }
}
?>