<?php
class SelectTagFloatMenu extends WABEntity{
	function construct($params) {
		parent::construct($params);
		$this->template = "renderForm";
		$this->clientClass = "SelectTagFloatMenu";
		$this->parentClientClasses = "ContextMenu~Entity";
		$this->count = "5";
	}
	
	function renderForm() {
		global $Objects;
		$blocks = getPrintBlocks(file_get_contents("templates/core/SelectEntityFloatMenu.html"));
		$out = $blocks["header"];

		$query = "SELECT value FROM fields WHERE name='tags' AND classname='".$this->entityClass."'";
		$app = $Objects->get("DocFlowApplication_".$this->module_id."_docs");
		$adapter = $app->getAdapter($this);
		$adapter->connect();
		$stmt = $adapter->dbh->prepare($query);
		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$tags = array();
		if (count($result)>0) {
			foreach ($result as $value) {
				$arr = explode("~",$value["value"]);
				foreach ($arr as $v)
					$tags[$v] = $v;
			}
		}
		foreach ($tags as $tag) {			
			$args = array("{presentation}" => $tag, "{displayValue}" => $tag, "{fieldValues}" => json_encode(array($this->resultObject."_".$this->resultFields => $tag)));
			$out .= strtr($blocks["row"],$args);		
		}
		$out .= $blocks["footer"];
		return $out;
	}

	function getArgs() {
		$result = parent::getArgs();
		$result["{className}"] = $this->className;
		return $result;
	}
}
?>