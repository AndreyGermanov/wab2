<?php
/**
 * Класс главного меню подсистемы CRM
 *
 * @author andrey
 */
class CRMMenu extends Menu {    
    function construct($params) {
        global $Objects;
        parent::construct($params);
        $this->horizontal = "true";
        $this->height = "20";        
        $this->table_properties = "cellpadding=5";
        $this->properties = "class=".$this->getId()."_menu^style=display:";
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        $this->skinPath = $app->skinPath;
        $this->helpGuideId = "crm";        
        $this->css = $app->skinPath."styles/DocFlowMenu.css";
        $this->template = "templates/docflow/crm/CRMMenu.html";
        $this->handler = "scripts/handlers/docflow/crm/CRMMenu.js";
        $this->clientClass = "CRMMenu";        
        $this->parentClientClasses = "DocFlowApplication~DocFlowMenu~Menu~Entity";        
    }
    
    function getArgs() {
    	
    	$this->setRole();
    	
    	$referenceItems = array
    	(
   			"banksReferenceMenu" => "banksReferenceMenu~Банки~".$this->skinPath."images/docflow/bank.png",
    		"contragentsReferenceMenu" => "contragentsReferenceMenu~Контрагенты~".$this->skinPath."images/docflow/contragent.png",
    		"bankAccountsReferenceMenu" => "bankAccountsReferenceMenu~Банковские счета~".$this->skinPath."images/docflow/bankaccount.png",
    		"emailAddressesReferenceMenu" => "emailAddressesReferenceMenu~Адреса Email~".$this->skinPath."images/docflow/emailaddr.png",
    		"filesReferenceMenu" => "filesReferenceMenu~Файлы~".$this->skinPath."images/docflow/file.png",
    		"departmentsReferenceMenu" => "departmentsReferenceMenu~Подразделения~".$this->skinPath."images/docflow/department.png",
    		"placesReferenceMenu" => "placesReferenceMenu~Места~".$this->skinPath."images/Tree/objectgroup.png",
    		"appointmentsReferenceMenu" => "appointmentsReferenceMenu~Должности~".$this->skinPath."images/Tree/addrbook.gif",
    		"emailAccountsReferenceMenu" => "emailAccountsReferenceMenu~Учетные записи Email~".$this->skinPath."images/Window/mail.gif",
    		"usersReferenceMenu" => "usersReferenceMenu~Пользователи~".$this->skinPath."images/Tree/user.png",
    		"idDocumentTypesReferenceMenu" => "idDocumentTypesReferenceMenu~Документы удостоверения личности~".$this->skinPath."images/Tree/user.png",
    		"firmsReferenceMenu" => "firmsReferenceMenu~Организации~".$this->skinPath."images/docflow/firm.png",
    		"contactsReferenceMenu" => "contactsReferenceMenu~Контактные лица~".$this->skinPath."images/docflow/contragent.png",
    		"projectsReferenceMenu" => "projectsReferenceMenu~Проекты~".$this->skinPath."images/docflow/project.png",
    		"orderConditionsReferenceMenu" => "orderConditionsReferenceMenu~Состояния заказа~".$this->skinPath."images/Tree/addrbook.gif",
    		"projectConditionsReferenceMenu" => "projectConditionsReferenceMenu~Состояния проекта~".$this->skinPath."images/Tree/addrbook.gif",
    		"taskConditionsReferenceMenu" => "taskConditionsReferenceMenu~Состояния задачи~".$this->skinPath."images/Tree/addrbook.gif",
    		"contractTemplatesReferenceMenu" => "contractTemplatesReferenceMenu~Шаблоны договоров~".$this->skinPath."images/docflow/template.png",
    		"requestFormsReferenceMenu" => "requestFormsReferenceMenu~Формы обращения~".$this->skinPath."images/Tree/addrbook.gif",
    		"contragentKindsReferenceMenu" => "contragentKindsReferenceMenu~Виды контрагентов~".$this->skinPath."images/Tree/addrbook.gif",
    		"productKindsReferenceMenu" => "productKindsReferenceMenu~Виды номенклатуры~".$this->skinPath."images/Tree/addrbook.gif",
    		"productsReferenceMenu" => "productsReferenceMenu~Номенклатура~".$this->skinPath."images/Tree/objectgroup.png",
    		"dimensionsReferenceMenu" => "dimensionsReferenceMenu~Единицы измерения~".$this->skinPath."images/Tree/addrbook.gif",
    		"contragentRequestTypesReferenceMenu" => "contragentRequestTypesReferenceMenu~Типы обращений контрагентов~".$this->skinPath."images/Tree/addrbook.gif"
    	);
    	
    	$refItems = array();
    	if (isset($this->role["referenceItems"])) {
    		foreach ($this->role["referenceItems"] as $value)
    			$refItems[$value] = @$referenceItems[$value];
    	} else
    		$refItems = $referenceItems;
    	
    	$documentItems = array
    	(
    		"contragentRequestDocumentsMenu" => "contragentRequestDocumentsMenu~Обращения контрагентов~".$this->skinPath."images/Tree/document.png",
    		"orderDocumentsMenu" => "orderDocumentsMenu~Заказы~".$this->skinPath."images/Tree/document.png",
    		"invoiceDocumentsMenu" => "invoiceDocumentsMenu~Счета~".$this->skinPath."images/Tree/document.png",
    		"quickSaleDocumentsMenu" => "quickSaleDocumentsMenu~Продажи~".$this->skinPath."images/Tree/document.png",
    		"contractDocumentsMenu" => "contractDocumentsMenu~Договоры~".$this->skinPath."images/Tree/document.png",
    		"taskDocumentsMenu" => "taskDocumentsMenu~Задачи~".$this->skinPath."images/Tree/document.png",
    		"workReportDocumentsMenu" => "workReportDocumentsMenu~Отчеты о работе~".$this->skinPath."images/Tree/document.png",
    		"changeOrderConditionDocumentsMenu" => "changeOrderConditionDocumentsMenu~Изменение состояния заказа~".$this->skinPath."images/Tree/document.png",
    		"changeTaskConditionDocumentsMenu" => "changeTaskConditionDocumentsMenu~Изменение состояния задачи~".$this->skinPath."images/Tree/document.png",
    		"changeProjectConditionDocumentsMenu" => "changeProjectConditionDocumentsMenu~Изменение состояния проекта~".$this->skinPath."images/Tree/document.png"
    	);

    	$docItems = array();
    	if (isset($this->role["documentItems"])) {
    		foreach ($this->role["documentItems"] as $value)
    			$docItems[$value] = @$documentItems[$value];
    	} else
    		$docItems = $documentItems;
    	 
    	$reportItems = array 
    	(
    		"globalSearchMenu" => "globalSearchMenu~Глобальный поиск~".$this->skinPath."images/Buttons/gfind.png",
    		"registryReportsMenu" => "registryReportsMenu~Отчеты по регистрам~".$this->skinPath."images/Tree/report.png",
    		"registryDiscountCardsReportsMenu" => "registryDiscountCardsReportsMenu~Дисконтные карты~".$this->skinPath."images/Tree/report.png"
    	);

    	$repItems = array();
    	if (isset($this->role["reportItems"])) {
    		foreach ($this->role["reportItems"] as $value)
    			$repItems[$value] = @$reportItems[$value];
    	} else
    		$repItems = $reportItems;
    	 
    	$settingsItems = array
    	(
    			"apacheUsersMenu" => "apacheUsersMenu~Учетные записи пользователей~".$this->skinPath."images/Tree/users.gif",
    			"metadataMenu" => "metadataMenu~Метаданные~".$this->skinPath."images/Tree/metadata.png",
    			"rolesMenu" => "rolesMenu~Роли пользователей~".$this->skinPath."images/Tree/role.gif",
    			"crmTablesMenu" => "crmTablesMenu~Рабочее место пользователя~".$this->skinPath."images/docflow/contragent.png",
    			"deleteMarkedObjectsMenu" => "deleteMarkedObjectsMenu~Удаление помеченных объектов~".$this->skinPath."images/Tree/delmail.png",
    			"codesMenu" => "codesMenu~Алгоритмы~".$this->skinPath."images/Tree/algo2.png",
    			"firmSettingsMenu" => "firmSettingsMenu~Основные сведения о компании~".$this->skinPath."images/docflow/firm.png"
    	);

    	$setItems = array();
    	if (isset($this->role["settingsItems"])) {
    		foreach ($this->role["settingsItems"] as $value)
    			$setItems[$value] = @$settingsItems[$value];
    	} else
    		$setItems = $settingsItems;
    	 
    	$helpItems = array
    	(
			"referenceMenu" => "referenceMenu~Руководство пользователя~".$this->skinPath."images/Tree/docs.png",
    		"sendRequestMenu" => "sendRequestMenu~Отправить запрос в службу поддержки~".$this->skinPath."images/Tree/mail.gif"    	
    	);

    	$hlpItems = array();
    	if (isset($this->role["helpItems"])) {
    		foreach ($this->role["helpItems"] as $value)
    			$hlpItems[$value] = @$helpItems[$value];
    	} else
    		$hlpItems = $helpItems;
    	     
    	$this->referencesMenuCode  = cleanText('$object->data="'.implode("|",$refItems).'";');
    	$this->documentsMenuCode  = cleanText('$object->data="'.implode("|",$docItems).'";');
    	$this->reportsMenuCode  = cleanText('$object->data="'.implode("|",$repItems).'";');
    	$this->settingsMenuCode  = cleanText('$object->data="'.implode("|",$setItems).'";');
    	$this->helpMenuCode  = cleanText('$object->data="'.implode("|",$hlpItems).'";');
    	 
		$mainMenu = array (
			"referencesMenu" => "referencesMenu~Справочники&nbsp;~~~",
			"documentsMenu" => "documentsMenu~Документы&nbsp;~~~", 				
			"reportsMenu" => "reportsMenu~Отчеты&nbsp;~~~",
			"settingsMenu" => "settingsMenu~Система&nbsp;~~~",
			"helpMenu" => "helpMenu~Помощь&nbsp;~~~"
		);  
		$menu = array();  	
		if (isset($this->role["menu"])) {
			foreach ($this->role["menu"] as $value)
				$menu[$value] = @$mainMenu[$value];
		} else
			$menu = $mainMenu;
    	
        $result = parent::getArgs();
        $data = implode("|",$menu);
                
        $result["{data}"] = $data;
        return $result;
    }      
}
?>