<?php
/**
 * Класс, отображающий дерево выбора элемента
 *
 * @author andrey
 */
class SelectItemTree extends Tree {
    function  construct($params) {
        parent::construct($params);
        $this->module_id = $params[0]."_".$params[1];
        $this->name = $params[2];
        $this->handler = "scripts/handlers/SelectTree.js";
    }
    function setTreeItems($site,$parent=1,$parent_parent="0",$parent_open_as="",$parent_class="WebItem") {
        global $Objects;
        $site_root = $Objects->get($parent_class."_".$this->module_id."_".$site."_".$parent);
        $items = $site_root->getChildren();
        $website = $Objects->get("WebSite_".$this->module_id."_".$site);
        $website->connect();
        if ($items==FALSE)
            return 0;
        foreach ($items as $item)
        {
            $parent = $item->getNode()->getParent()->id;
            $result["01_".$item->name]["id"] = $item->class."_".$this->module_id."_".$site."_".$item->id."_".$parent."_asAdminList";
            $result["01_".$item->name]["title"] = $item->title;
            $result["01_".$item->name]["icon"] = $item->icon;
            if ($parent_parent==0)
                $result["01_".$item->name]["parent"] = "";
            if ($item->getNode()->isLeaf())
                $result["01_".$item->name]["loaded"] = "true";
            else
                $result["01_".$item->name]["loaded"] = "false";
        }
        //ksort($result);
        $res = array();
        foreach($result as $value)
        {
            $res[count($res)] = implode("#",$value);
        }
        $this->items_string = implode("|",$res);
    }
    
    function getSiteRootTree($site,$parent=1,$parent_parent="0",$parent_open_as="",$parent_class="WebItem") {

        global $Objects;
        $site_root = $Objects->get($parent_class."_".$this->module_id."_".$site."_".$parent);
        $items = $site_root->getChildren();
        $website = $Objects->get("WebSite_".$this->module_id."_".$site);
        $website->connect();
        if ($items==FALSE)
            return 0;
        foreach ($items as $item)
        {
            $parent = $item->getNode()->getParent()->id;
            $result["01_".$item->name]["id"] = $item->class."_".$this->module_id."_".$site."_".$item->id."_".$parent."_asAdminList";
            $result["01_".$item->name]["title"] = $item->title;
            $result["01_".$item->name]["icon"] = $item->icon;
            if ($parent_parent==0)
                $result["01_".$item->name]["parent"] = "WebSite_".$this->module_id."_".$site;
            else
                $result["01_".$item->name]["parent"] = $parent_class."_".$this->module_id."_".$site."_".$parent."_".$parent_parent."_".$parent_open_as;
            if ($item->getNode()->isLeaf())
                $result["01_".$item->name]["loaded"] = "true";
            else
                $result["01_".$item->name]["loaded"] = "false";
        }
        //ksort($result);
        $res = array();
        foreach($result as $value)
        {
            $res[count($res)] = implode("#",$value);
        }
        echo implode("|",$res);
    }

    function getId() {
        return "SelectItemTree_".$this->module_id."_".$this->name;
    }
}
?>