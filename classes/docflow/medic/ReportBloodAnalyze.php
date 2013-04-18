<?php
/**
 * Класс, реализующий отчет
 *
 * @author andrey
 */
class ReportBloodAnalyze extends Report {
    public $persistedFields = array(); 
    function construct($params) {
        parent::construct($params);
        $this->template="templates/docflow/medic/ReportBloodAnalyze.html";      
        $this->periodStart = time()."000";
        $this->periodEnd = time()."000";
        $this->patient = "";
        $this->def = "";
        $this->width = 800;
        $this->overrided = "width,height";
        global $Objects;
        $app = $Objects->get("Application");
        $this->skinPath = $app->skinPath;
        $this->icon = $this->skinPath."images/Tree/report.png";
        $this->tabset_id = "WebItemTabset_".$this->module_id."_".$this->name."Report";
        $this->tabs_string = "table|Таблица|".$app->skinPath."images/spacer.gif;";
        $this->tabs_string .= "diagram|Диаграмма|".$app->skinPath."images/spacer.gif";
        $this->active_tab = "table";        
        $this->clientClass = "ReportBloodAnalyze";
        $this->parentClientClasses = "Report~Entity";
        $this->unchangeable = "true";     
        $this->classTitle = "Анализ показателя крови";
        $this->classListTitle = "Анализ показателя крови";
    }
    
    function getPresentation() {
		if ($this->noPresent)
			return "";
    	return "Анализ изменения показателей крови";
    }
        
    function afterInit() {
        return true;
    }
        
    function printReport($formName="") {
		global $Objects;
		$this->persistedFields["periodStart"] = array("type" => "date", "params" => array("type" => "date"));
		$this->persistedFields["periodEnd"] = array("type" => "date", "params" => array("type" => "date"));
		$result = parent::printReport($formName);
		if ($this->def!="" and $this->patient!="") {
			$reg = $Objects->get("RegistryBloodDefinitions_".$this->module_id."_reg");
			$this->periodStart = getBeginOfDay($this->periodStart/1000)."000";
			$this->periodEnd = getEndOfDay($this->periodEnd/1000)."000";				
			$res = $reg->getRecords($this->periodStart,$this->periodEnd,"analyzeDefValue,regDate,document","@patient='".str_replace($this->module_id."_","",$this->patient)."' AND @analyzeDef='".str_replace($this->module_id."_","",$this->def)."'","regDate ASC");
			$def = $Objects->get($this->def);
			if (!$def->loaded)
				$def->load();
			$this->def = $def;
			if (!$def->dimension->loaded)
				$def->dimension->load();
			$dim = $def->dimension;
			$pat = $Objects->get($this->patient);
			$pat->load(null,null,true);
			if ($pat->gender==0) {
				$maxValue = $def->mMaxV;
				$minValue = $def->mMinV;
			} else {
				$maxValue = $def->wmMaxV;
				$minValue = $def->wmMinV;
			}
			$this->patient = $pat;
			if ($this->reportType=="diagram") {
				$blocks = getPrintBlocks(file_get_contents("templates/docflow/medic/printForms/ReportBloodAnalyze.html"));
				$xml_data  = "<chart><chart_type><string>line</string><string>line</string><string>line</string></chart_type>";
				$xml_data .= "<chart_pref point_shape='none'></chart_pref><chart_pref point_shape='none'></chart_pref><chart_pref point_shape='none'></chart_pref>";
				$xml_data .= "<chart_transition type='slide_right' delay='1' duration='2' order='series'/>";
				$xml_data .= "<chart_data>";
				$row1 = "<row><null/>";
				$row2 = "<row><string>".str_replace("%","",$def->code)."</string>";
				if ($minValue>0)
					$row3 = "<row><string>Min.</string>";
				if ($maxValue>0)
					$row4 = "<row><string>Max.</string>";
				//$exp2 = "<axis_category_label>";
				foreach ($res as $record) {
					$row1 .= "<string></string>";//.date("d.m.Y",substr($record["regDate"],0,strlen($record["regDate"])-3))."</string>";
					$row2 .= "<number tooltip='".$record["analyzeDefValue"]." ".$dim->title." (".date("d.m.Y",substr($record["regDate"],0,strlen($record["regDate"])-3)).")'>".$record["analyzeDefValue"]."</number>";
					if ($minValue>0)
						$row3 .= "<number>".$minValue."</number>";
					if ($maxValue>0)
						$row4 .= "<number>".$maxValue."</number>";
	//				$exp2 .= "<string></string>";
				}
				$row1.= "</row>";
				$row2.= "</row>";
				if ($minValue>0)
					$row3.= "</row>";
				if ($maxValue>0)
					$row4.= "</row>";
	//			$exp2.= "</axis_category_label>";
				$rows = "";
				if ($minValue>0)
					$rows .= $row3;
				if ($maxValue>0)
					$rows .= $row4;
				$xml_data .= $row1.$rows.$row2."</chart_data></chart>";
				$result .=strtr($blocks["diagram"],array("{xml_data}"=>$xml_data));
			} else {
				$blocks = getPrintBlocks(file_get_contents("templates/docflow/medic/printForms/ReportBloodAnalyzeTable.html"));
				$this->normas = $minValue."-".$maxValue;
				$rs = $this->getArgs();
				$this->def->load();
				$this->def->dimension->load();
				$this->patient->load();
				$rs["{defTitle}"] = $this->def->title;
				$rs["{defDimensionTitle}"] = $this->def->dimension->title;
				$rs["{patientTitle}"] = $this->patient->title;
				$result .= strtr($blocks["header"],$rs);
				$i = 0;
				$result .= $blocks["rowstart"];
				foreach ($res as $record) {
					if ($i>9) {
						$result .= $blocks["rowend"].$blocks["rowstart"];
						$i=0;
						continue;
					} else
						$i++;
					
					$result .= strtr($blocks["cell"], array("{date}" => date("d.m.Y",substr($record["regDate"],0,strlen($record["regDate"])-3)), "{value}" => $record["analyzeDefValue"],"{document}" => $record["document"]->getId(), "{windowDocument}" => "Window_".str_replace("_","",$record["document"]->getId())));
				}
			}
		}
		return $result;
	}
	
	function getPrintForms() {
		return array("Анализ изменения показателей крови");
	}    
}
?>