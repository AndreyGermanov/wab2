<?php
/**
 * Класс, реализующий регистр движений и остатков товаров на складах. Остатки товаров учитываются
 * в разрезе складов, мест хранения и партий. Партия это документ, по которому товар поступил.
 * 
 * Товар учитывается по сумме и по количеству.
 * 
 * Реквизиты регистра:
 * 
 * product - Товар
 * department - Подразделение (склад)
 * place - Место хранения
 * partya - партия
 * сount - количество
 * summa - сумма
 * 
 * @author andrey
 */
class RegistryProducts extends Registry {
    function construct($params) {
		parent::construct($params);		
        $this->classTitle = "Остатки и движения товаров";
        $this->classListTitle = "Остатки и движения товаров";
        $this->recvs = "product,department,place,partya,count,summa";
        $this->clientClass = "RegistryProducts";
        $this->parentClientClasses = "Registry~Entity";        
        $this->printProfiles["Основной"] = array (
        		"settings" => array (
        			"settingsClass" => "RegistrySettingsDialog"
        		),
        		"Основной" => array (
	       			"printFields" => array (
	   					"regDate" => array (
							"size" => "10%"
	   					),
	   					"document" => array (
							"size" => "23%"
	   					),
	   					"product" => array (
							"size" => "24%"
	   					),        							
	   					"count" => array (
							"size" => "20%"
	   					),
	   					"summa" => array (
							"size" => "20%"
	   					)
	       			),
	        		"totals" => array (
	        			"bgColor" => "#DDDDDD",
	        			"fontFace" => "Arial",
	        			"fontSize" => "15",
	    				"fontWeight" => "bold",
						"fontColor" => "#000000"
	   				)        				
      			)        		
        );             
    }
}
?>