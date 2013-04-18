<?
class MetadataGroupsTree extends Tree {

    public $userConfig;

    function  construct($params) {
        parent::construct($params);
        $this->module_id = @$params[0]."_".@$params[1];
        $this->object_id = @$params[2];
        $this->name = @$params[2];
        $this->loaded = true;
        $this->clientClass = "MetadataGroupsTree";
        $this->parentClientClasses = "Tree~Entity";        
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        $this->skinPath = $app->skinPath;
        $this->app = $app;
        $this->icon = $this->skinPath."images/Tree/metadata.png";
        $this->width="300";
        $this->height = "500";
        $this->overrided = "width,height";
        $this->title="Метаданные";        
    }

    function setTreeItems() {
    	global $Objects;
    	
		$result = array();
		
        $result["20_09_ControlPanelMetadata"]["id"] = "MetadataTree_".$this->module_id."_MetadataFields";
        $result["20_09_ControlPanelMetadata"]["title"] = l10n("Поля");
        $result["20_09_ControlPanelMetadata"]["icon"] = $this->skinPath."images/Tree/fields.png";
        $result["20_09_ControlPanelMetadata"]["parent"] = "";
        $result["20_09_ControlPanelMetadata"]["loaded"] = "metadataArray=fields";
        $result["20_09_ControlPanelMetadata"]["subtree"] = "true";
         
        $result["20_10_ControlPanelMetadata"]["id"] = "MetadataTree_".$this->module_id."_MetadataModels";
        $result["20_10_ControlPanelMetadata"]["title"] = l10n("Модели");
        $result["20_10_ControlPanelMetadata"]["icon"] = $this->skinPath."images/Tree/models.png";
        $result["20_10_ControlPanelMetadata"]["parent"] = "";
        $result["20_10_ControlPanelMetadata"]["loaded"] = "metadataArray=models";
        $result["20_10_ControlPanelMetadata"]["subtree"] = "true";
         
        $result["20_11_ControlPanelMetadata"]["id"] = "MetadataTree_".$this->module_id."_MetadataCodes";
        $result["20_11_ControlPanelMetadata"]["title"] = l10n("Алгоритмы");
        $result["20_11_ControlPanelMetadata"]["icon"] = $this->skinPath."images/Tree/algo4.png";
        $result["20_11_ControlPanelMetadata"]["parent"] = "";
        $result["20_11_ControlPanelMetadata"]["loaded"] = "metadataArray=codes";
        $result["20_11_ControlPanelMetadata"]["subtree"] = "true";
        
        $result["20_12_ControlPanelAdmins"]["id"] = "Modules_".$this->module_id;
        $result["20_12_ControlPanelAdmins"]["title"] = l10n("Модули");
        $result["20_12_ControlPanelAdmins"]["icon"] = $this->skinPath."images/Tree/modules.png";
        $result["20_12_ControlPanelAdmins"]["parent"] = "";
        $result["20_12_ControlPanelAdmins"]["loaded"] = "false";
        
        $result["20_13_ControlPanelMetadata"]["id"] = "MetadataTree_".$this->module_id."_MetadataPanels";
        $result["20_13_ControlPanelMetadata"]["title"] = l10n("Панели");
        $result["20_13_ControlPanelMetadata"]["icon"] = $this->skinPath."images/Tree/fields.png";
        $result["20_13_ControlPanelMetadata"]["parent"] = "";
        $result["20_13_ControlPanelMetadata"]["loaded"] = "metadataArray=panels#show_groups=0";
        $result["20_13_ControlPanelMetadata"]["subtree"] = "true";
         
        $result["20_14_ControlPanelMetadata"]["id"] = "MetadataTree_".$this->module_id."_MetadataInterfaces";
        $result["20_14_ControlPanelMetadata"]["title"] = l10n("Интерфейсы");
        $result["20_14_ControlPanelMetadata"]["icon"] = $this->skinPath."images/Tree/interfaces.png";
        $result["20_14_ControlPanelMetadata"]["parent"] = "";
        $result["20_14_ControlPanelMetadata"]["loaded"] = "metadataArray=interfaces#show_groups=0";
        $result["20_14_ControlPanelMetadata"]["subtree"] = "true";
         
        $result["20_15_ControlPanelMetadata"]["id"] = "MetadataTree_".$this->module_id."_MetadataRoles";
        $result["20_15_ControlPanelMetadata"]["title"] = l10n("Роли");
        $result["20_15_ControlPanelMetadata"]["icon"] = $this->skinPath."images/Tree/role.gif";
        $result["20_15_ControlPanelMetadata"]["parent"] = "";
        $result["20_15_ControlPanelMetadata"]["loaded"] = "metadataArray=roles#show_groups=0";
        $result["20_15_ControlPanelMetadata"]["subtree"] = "true";
        
        $result["20_16_ControlPanelMetadata"]["id"] = "MetadataTree_".$this->module_id."_MetadataTags";
        $result["20_16_ControlPanelMetadata"]["title"] = l10n("Тэги");
        $result["20_16_ControlPanelMetadata"]["icon"] = $this->skinPath."images/Tree/tag.png";
        $result["20_16_ControlPanelMetadata"]["parent"] = "";
        $result["20_16_ControlPanelMetadata"]["loaded"] = "metadataArray=tags#show_groups=0";
        $result["20_16_ControlPanelMetadata"]["subtree"] = "true";
        
        ksort($result);
        $res = array();
        foreach($result as $value)
        {
            $res[count($res)] = implode("~",$value);
        }
        $this->items_string = implode("|",$res);
    }
    
    function getHookProc($number) {
    	switch ($number) {
    		case '3': return "setTreeItemsHook";
    	}
    	return parent::getHookProc($number);
    }

    function setTreeItemsHook($arguments) {
    	$this->setArguments($arguments);
    	$this->setTreeItems();
    }
}
?>