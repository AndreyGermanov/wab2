<?php
// Рабочее место врача
class DoctorWorkplace extends WABEntity {
	
	function construct($params) {
		parent::construct($params);
		$this->clientClass = "DoctorWorkplace";
		$this->hierarchy = true;
		$this->parentClientClasses = "Entity";
		$this->template = "renderForm";
        $this->overrided = "width,height";
        global $Objects;
        $this->app = $Objects->get("Application");
        $this->skinPath = $this->app->skinPath;
        $this->classTitle = "Рабочее место врача";
        $this->classListTitle = "Рабочее место врача";
	}
	
	function renderForm() {
		$blocks = getPrintBlocks(file_get_contents("templates/docflow/medic/DoctorWorkplace.html"));
		$out = $blocks["header"];
		return $out;
	}
		
	function getArgs() {		        						
		return parent::getArgs();
	}
	
	function getHookProc($number) {
		switch ($number) {
			case "3": return "getPatientData";			
		}
		return parent::getHookProc($number);	
	}
	
	function getPatientData($arguments) {
		if (isset($arguments["patient"])) {
			global $Objects;
			$obj = $Objects->get($arguments["patient"]);
			if (is_object($obj) && method_exists($obj,"getId")) {
				$obj->load();
				if ($obj->loaded) {
					$arr = array();
					$arr["fio"] = $obj->title;
					$arr["birthDate"] = date("d.m.Y",$obj->birthDate/1000);
					$arr["photo"] = $obj->photo;
					$obj->city->load();
					if ($obj->city->loaded)
						$arr["city"] = $obj->city->title;
					$arr["address"] = $obj->address;
					$arr["diagnozeDate"] = date("d.m.Y",$obj->diagnozeDate/1000);
					$arr["diagnozeYear"] = date("Y",$obj->diagnozeDate/1000)." г.";
					$reg = $Objects->get("RegistryBloodDefinitions_".$this->module_id."_defs");
					$values = $reg->getLastValues("analyzeDefValue,regDate,document","@analyzeDef.@code='BCR-ABL' AND @patient='".str_replace($this->module_id."_","",$obj->getId())."'");
					$arr["pcrResult"] = "<b><span style='color:#660000'>".$values["analyzeDefValue"]."%</span></b> ".date("d.m.Y",$values["regDate"]/1000);
					$arr["pcrDocument"] = $values["document"]->getId();
					$values = $reg->getLastValues("analyzeDefValue,regDate,document","@analyzeDef.@code='Ph+' AND @patient='".str_replace($this->module_id."_","",$obj->getId())."'");
					$arr["phResult"] = "<b><span style='color:#660000'>".$values["analyzeDefValue"]."%</span></b> ".date("d.m.Y",$values["regDate"]/1000);				
					$arr["phDocument"] = $values["document"]->getId();
					$date1 = date_create(date("d.m.Y",$obj->birthDate/1000));
					$date2 = date_create();
					$interval = date_diff($date2,$date1);
					$arr["age"] = $interval->format("%y")." лет";
					echo json_encode($arr);						
				}
			}
		}
	}
}
?>