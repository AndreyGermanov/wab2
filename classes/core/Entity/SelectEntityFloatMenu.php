<?php
class SelectEntityFloatMenu extends WABEntity{
	function construct($params) {
		parent::construct($params);
		$this->template = "renderForm";
		$this->clientClass = "SelectEntityFloatMenu";
		$this->parentClientClasses = "ContextMenu~Entity";
		$this->handler = "scripts/handlers/core/SelectEntityFloatMenu.js";
		$this->count = "5";
	}
	
	function renderForm() {
		$blocks = getPrintBlocks(file_get_contents("templates/core/SelectEntityFloatMenu.html"));
		$out = $blocks["header"];
		if ($this->showAdvanced)
			$out .= $blocks["button"];
		$arr = explode("|",$this->resultFields);
		$fields = explode("-",$arr[0]);
		$controls = explode("-",$arr[1]);
		if (trim($this->value)!="")		
			$query = "SELECT entities FROM fields WHERE @".$this->searchField." LIKE '".trim($this->value)."%' AND @classname='".$this->entityClass."'";
		else
			$query = "SELECT entities FROM fields WHERE @classname='".$this->entityClass."'";
		global $Objects;
		$app = $Objects->get("DocFlowApplication_".$this->module_id."_docs");
		$adapter = $app->getAdapter($this);
		$result = PDODataAdapter::makeQuery($query,$adapter,$this->module_id);
		$ents = array();
		if (count($result)>0) {
			foreach ($result as $entity) {
				$arr = array();
				if ($entity->isGroup)
					continue;
				for ($i=0;$i<count($fields);$i++) {
					if ($fields[$i]=="entityId")
						$arr[$this->resultObject."_".$controls[$i]] = $entity->getId();
					else {
						if (stripos($fields[$i],".")===FALSE)
							$val = $entity->fields[$fields[$i]];
						else {
							$parts = explode(".",$fields[$i]);
							$entity->fields[$parts[0]]->load();
							$val = $entity->fields[$parts[0]]->fields[$parts[1]];
						}							
						$arr[$this->resultObject."_".$controls[$i]] = $val;
					}
				}
				
				$args = array("{presentation}" => $entity->presentation, "{displayValue}" => $entity->fields[$this->displayField], "{fieldValues}" => json_encode($arr));
				if (!isset($ents[$entity->fields[$this->displayField]]))
					$out .= strtr($blocks["row"],$args);		
				$ents[$entity->fields[$this->displayField]] = $entity->fields[$this->displayField];						
			}
		}
		$out .= $blocks["footer"];
		return $out;
	}

	function getArgs() {
		$result = parent::getArgs();
		$result["{className}"] = $this->className;
		return $result;
	}
	
	function getHookProc($number) {
		switch ($number) {
			case '5': return "getEntityFields";
		}
		return parent::getHookProc($number);
	}
	
	function getEntityFields($arguments) {
		$this->setArguments($arguments);
		global $Objects;
		$entity = $Objects->get($this->item);
		if (!$entity->loaded)
			$entity->load();
		$arr = explode("|",$this->resultFields);
		$fields = explode("-",$arr[0]);
		$controls = explode("-",$arr[1]);
		for ($i=0;$i<count($fields);$i++) {
			if ($fields[$i]=="entityId")
				$arr[$this->resultObject."_".$controls[$i]] = $entity->getId();
			else {
				if (stripos($fields[$i],".")===FALSE)
					$val = $entity->fields[$fields[$i]];
				else {
					$parts = explode(".",$fields[$i]);
					$entity->fields[$parts[0]]->load();
					$val = $entity->fields[$parts[0]]->fields[$parts[1]];
				}							
				$arr[$this->resultObject."_".$controls[$i]] = $val;
			}
		}
		echo json_encode($arr);
	}
}
?>