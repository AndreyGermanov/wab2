<?
class SelectTemplateTree extends Tree {

    function  construct($params) {
        parent::construct($params);
        $this->module_id = $params[0]."_".$params[1];
        $this->object_id = $params[2];
        $this->handler = "scripts/handlers/SelectTree.js";
    }

    function setTreeItems($parent_id="0")
    {        
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        $templates = $Objects->get("ItemTemplates_".$this->module_id);
        if ($parent_id!="0") {
            $templates->connect();
            $tpl = $Objects->get("ItemTemplate_".$this->module_id."_".$parent_id);
            $tpl->load();
            $parent_parent_id = $tpl->parent_id;
        }
        $templates_array = $templates->load($parent_id);
        foreach ($templates_array as $template) {
            $result["01_".$template["title"]]["id"] = "ItemTemplate_".$this->module_id."_".$template["id"]."_".$template["parent_id"];
            $result["01_".$template["title"]]["title"] = $template["title"];
            $result["01_".$template["title"]]["icon"] = $app->skinPath."images/Tree/templates.gif";
            $result["01_".$template["title"]]["parent"] = "";
            $arr = $templates->load($template["id"]);
            if ($arr!=0)
                $result["01_".$template["title"]]["loaded"] = "false";
            else
                $result["01_".$template["title"]]["loaded"] = "true";
        }
        ksort($result);
        $res = array();
        foreach($result as $value)
        {
            $res[count($res)] = implode("#",$value);
        }
        $this->items_string = implode("|",$res);        
    }

    function getTemplatesTree($parent_id="0") {
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        $templates = $Objects->get("ItemTemplates_".$this->module_id);
        if ($parent_id!="0") {
            $templates->connect();
            $tpl = $Objects->get("ItemTemplate_".$this->module_id."_".$parent_id);
            $tpl->load();
            $parent_parent_id = $tpl->parent_id;
        }
        $templates_array = $templates->load($parent_id);
        foreach ($templates_array as $template) {
            $result["01_".$template["title"]]["id"] = "ItemTemplate_".$this->module_id."_".$template["id"]."_".$template["parent_id"];
            $result["01_".$template["title"]]["title"] = $template["title"];
            $result["01_".$template["title"]]["icon"] = $app->skinPath."images/Tree/templates.gif";
            if ($parent_id=="0")
                $result["01_".$template["title"]]["parent"] = "Templates";
            else
                $result["01_".$template["title"]]["parent"] = "ItemTemplate_".$this->module_id."_".$parent_id."_".$parent_parent_id;
            $arr = $templates->load($template["id"]);
            if ($arr!=0)
                $result["01_".$template["title"]]["loaded"] = "false";
            else
                $result["01_".$template["title"]]["loaded"] = "true";
        }
        ksort($result);
        $res = array();
        foreach($result as $value)
        {
            $res[count($res)] = implode("#",$value);
        }
        echo implode("|",$res);
    }
}
?>