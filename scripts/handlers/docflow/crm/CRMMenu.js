//include scripts/handlers/interface/Menu.js

entity.helpGuideId = '{helpGuideId}';
var prevScrollY = 0;
var firstTop = 0;
var currentMenu = null;
var currentTitle = "";

   function onload(event) {
	   var pos = getElementPosition('{object_id}_menu_table');
       firstTop = pos.top;
    }
    if ($O("CRMMenu_"+entity.module_id+"_top","")!=null) {
        $O("CRMMenu_"+entity.module_id+"_top","").referencesMenu_onClick = function(event) {
            var pos = getElementPosition(eventTarget(event).getAttribute("item_id"));
            var leftt = pos.left;
            var topp = pos.top+pos.height;
            currentMenu=$O("CRMMenu_"+entity.module_id+"_top","").showSubMenu(leftt,topp,"DocFlowMenu_"+this.module_id+"_ReferencesMenu",event);
            currentTitle="DocFlowMenu_"+entity.module_id+"_ReferencesMenu";
        };
        $O("CRMMenu_"+entity.module_id+"_top","").documentsMenu_onClick = function(event) {
            var pos = getElementPosition(eventTarget(event).getAttribute("item_id"));
            var leftt = pos.left;
            var topp = pos.top+pos.height;
            currentMenu=$O("CRMMenu_"+entity.module_id+"_top","").showSubMenu(leftt,topp,"DocFlowMenu_"+this.module_id+"_DocumentsMenu",event);
            currentTitle="DocFlowMenu_"+entity.module_id+"_DocumentsMenu";
        };
        $O("CRMMenu_"+entity.module_id+"_top","").reportsMenu_onClick = function(event) {
            var pos = getElementPosition(eventTarget(event).getAttribute("item_id"));
            var leftt = pos.left;
            var topp = pos.top+pos.height;
            currentMenu=$O("CRMMenu_"+entity.module_id+"_top","").showSubMenu(leftt,topp,"DocFlowMenu_"+this.module_id+"_ReportsMenu",event);
            currentTitle="DocFlowMenu_"+entity.module_id+"_ReportsMenu";
        };
        $O("CRMMenu_"+entity.module_id+"_top","").settingsMenu_onClick = function(event) {
            var pos = getElementPosition(eventTarget(event).getAttribute("item_id"));
            var leftt = pos.left;
            var topp = pos.top+pos.height;
            currentMenu=$O("CRMMenu_"+entity.module_id+"_top","").showSubMenu(leftt,topp,"DocFlowMenu_"+this.module_id+"_SettingsMenu",event);
            currentTitle="DocFlowMenu_"+entity.module_id+"_SettingsMenu";
        };
        $O("CRMMenu_"+entity.module_id+"_top","").helpMenu_onClick = function(event) {
            var pos = getElementPosition(eventTarget(event).getAttribute("item_id"));
            var leftt = pos.left;
            var topp = pos.top+pos.height;
            currentMenu=$O("CRMMenu_"+entity.module_id+"_top","").showSubMenu(leftt,topp,"DocFlowMenu_"+this.module_id+"_HelpMenu",event);
            currentTitle="DocFlowMenu_"+entity.module_id+"_HelpMenu";
        };
    }    
    if ($O("DocFlowMenu_"+entity.module_id+"_ReferencesMenu","")!=null) {
        $O("DocFlowMenu_"+entity.module_id+"_ReferencesMenu","").banksReferenceMenu_onClick = function(event) {
                var elem_id = "ReferenceBanks_"+entity.module_id+"_List";
                var window_elem_id = "Window_Window"+elem_id.replace(/_/g,'');
                var params = new Object;
                params["hook"] = "3";
                params["object_text"] = "Банки";
                getWindowManager().show_window(window_elem_id,elem_id,params,null,null,null,true);                            
        };
        $O("DocFlowMenu_"+entity.module_id+"_ReferencesMenu","").contragentsReferenceMenu_onClick = function(event) {
            var elem_id = "ReferenceContragents_"+entity.module_id+"_List";
            var window_elem_id = "Window_Window"+elem_id.replace(/_/g,'');
            var params = new Object;
            params["hook"] = "3";
            params["object_text"] = "Контрагенты";
            getWindowManager().show_window(window_elem_id,elem_id,params,null,null,null,true);                            
        };
        $O("DocFlowMenu_"+entity.module_id+"_ReferencesMenu","").bankAccountsReferenceMenu_onClick = function(event) {
            var elem_id = "ReferenceBankAccounts_"+entity.module_id+"_List";
            var window_elem_id = "Window_Window"+elem_id.replace(/_/g,'');
            var params = new Object;
            params["hook"] = "3";
            params["object_text"] = "Банковские счета";
            getWindowManager().show_window(window_elem_id,elem_id,params,null,null,null,true);                            
        };
        $O("DocFlowMenu_"+entity.module_id+"_ReferencesMenu","").emailAddressesReferenceMenu_onClick = function(event) {
            var elem_id = "ReferenceEmailAddresses_"+entity.module_id+"_List";
            var window_elem_id = "Window_Window"+elem_id.replace(/_/g,'');
            var params = new Object;
            params["hook"] = "3";
            params["object_text"] = "Адреса Email";
            getWindowManager().show_window(window_elem_id,elem_id,params,null,null,null,true);                            
        };
        $O("DocFlowMenu_"+entity.module_id+"_ReferencesMenu","").filesReferenceMenu_onClick = function(event) {
            var elem_id = "ReferenceFiles_"+entity.module_id+"_List";
            var window_elem_id = "Window_Window"+elem_id.replace(/_/g,'');
            var params = new Object;
            params["hook"] = "3";
            params["object_text"] = "Файлы";
            getWindowManager().show_window(window_elem_id,elem_id,params,null,null,null,true);                            
        };
        $O("DocFlowMenu_"+entity.module_id+"_ReferencesMenu","").notesReferenceMenu_onClick = function(event) {
            var elem_id = "ReferenceNotes_"+entity.module_id+"_List";
            var window_elem_id = "Window_Window"+elem_id.replace(/_/g,'');
            var params = new Object;
            params["hook"] = "3";
            params["object_text"] = "Заметки";
            getWindowManager().show_window(window_elem_id,elem_id,params,null,null,null,true);                            
        };
        $O("DocFlowMenu_"+entity.module_id+"_ReferencesMenu","").departmentsReferenceMenu_onClick = function(event) {
            var elem_id = "ReferenceDepartments_"+entity.module_id+"_List";
            var window_elem_id = "Window_Window"+elem_id.replace(/_/g,'');
            var params = new Object;
            params["hook"] = "3";
            params["object_text"] = "Подразделения";
            getWindowManager().show_window(window_elem_id,elem_id,params,null,null,null,true);                            
        };
        $O("DocFlowMenu_"+entity.module_id+"_ReferencesMenu","").placesReferenceMenu_onClick = function(event) {
            var elem_id = "ReferencePlaces_"+entity.module_id+"_List";
            var window_elem_id = "Window_Window"+elem_id.replace(/_/g,'');
            var params = new Object;
            params["hook"] = "3";
            params["object_text"] = "Места";
            getWindowManager().show_window(window_elem_id,elem_id,params,null,null,null,true);                            
        };
        $O("DocFlowMenu_"+entity.module_id+"_ReferencesMenu","").appointmentsReferenceMenu_onClick = function(event) {
            var elem_id = "ReferenceAppointments_"+entity.module_id+"_List";
            var window_elem_id = "Window_Window"+elem_id.replace(/_/g,'');
            var params = new Object;
            params["hook"] = "3";
            params["object_text"] = "Должности";
            getWindowManager().show_window(window_elem_id,elem_id,params,null,null,null,true);                            
        };
        $O("DocFlowMenu_"+entity.module_id+"_ReferencesMenu","").emailAccountsReferenceMenu_onClick = function(event) {
            var elem_id = "ReferenceEmailAccounts_"+entity.module_id+"_List";
            var window_elem_id = "Window_Window"+elem_id.replace(/_/g,'');
            var params = new Object;
            params["hook"] = "3";
            params["object_text"] = "Учетные записи Email";
            getWindowManager().show_window(window_elem_id,elem_id,params,null,null,null,true);                            
        };
        $O("DocFlowMenu_"+entity.module_id+"_ReferencesMenu","").usersReferenceMenu_onClick = function(event) {
            var elem_id = "ReferenceUsers_"+entity.module_id+"_List";
            var window_elem_id = "Window_Window"+elem_id.replace(/_/g,'');
            var params = new Object;
            params["hook"] = "3";
            params["object_text"] = "Пользователи";
            getWindowManager().show_window(window_elem_id,elem_id,params,null,null,null,true);                            
        };
        $O("DocFlowMenu_"+entity.module_id+"_ReferencesMenu","").idDocumentTypesReferenceMenu_onClick = function(event) {
            var elem_id = "ReferenceIdDocumentTypes_"+entity.module_id+"_List";
            var window_elem_id = "Window_Window"+elem_id.replace(/_/g,'');
            var params = new Object;
            params["hook"] = "3";
            params["object_text"] = "Документы удостоверения личности";
            getWindowManager().show_window(window_elem_id,elem_id,params,null,null,null,true);                            
        };
        $O("DocFlowMenu_"+entity.module_id+"_ReferencesMenu","").firmsReferenceMenu_onClick = function(event) {
            var elem_id = "ReferenceFirms_"+entity.module_id+"_List";
            var window_elem_id = "Window_Window"+elem_id.replace(/_/g,'');
            var params = new Object;
            params["hook"] = "3";
            params["object_text"] = "Организации";
            getWindowManager().show_window(window_elem_id,elem_id,params,null,null,null,true);                            
        };
        $O("DocFlowMenu_"+entity.module_id+"_ReferencesMenu","").contactsReferenceMenu_onClick = function(event) {
            var elem_id = "ReferenceContacts_"+entity.module_id+"_List";
            var window_elem_id = "Window_Window"+elem_id.replace(/_/g,'');
            var params = new Object;
            params["hook"] = "3";
            params["object_text"] = "Контактные лица";
            getWindowManager().show_window(window_elem_id,elem_id,params,null,null,null,true);                            
        };
        $O("DocFlowMenu_"+entity.module_id+"_ReferencesMenu","").contragentRequestTypesReferenceMenu_onClick = function(event) {
            var elem_id = "ReferenceContragentRequestTypes_"+entity.module_id+"_List";
            var window_elem_id = "Window_Window"+elem_id.replace(/_/g,'');
            var params = new Object;
            params["hook"] = "3";
            params["object_text"] = "Типы обращений контрагентов";
            getWindowManager().show_window(window_elem_id,elem_id,params,null,null,null,true);                            
        };
        $O("DocFlowMenu_"+entity.module_id+"_ReferencesMenu","").orderConditionsReferenceMenu_onClick = function(event) {
            var elem_id = "ReferenceOrderConditions_"+entity.module_id+"_List";
            var window_elem_id = "Window_Window"+elem_id.replace(/_/g,'');
            var params = new Object;
            params["hook"] = "3";
            params["object_text"] = "Состояния заказа";
            getWindowManager().show_window(window_elem_id,elem_id,params,null,null,null,true);                            
        };
        $O("DocFlowMenu_"+entity.module_id+"_ReferencesMenu","").contractTemplatesReferenceMenu_onClick = function(event) {
            var elem_id = "ReferenceContractTemplates_"+entity.module_id+"_List";
            var window_elem_id = "Window_Window"+elem_id.replace(/_/g,'');
            var params = new Object;
            params["hook"] = "3";
            params["object_text"] = "Шаблоны договоров";
            getWindowManager().show_window(window_elem_id,elem_id,params,null,null,null,true);                            
        };
        $O("DocFlowMenu_"+entity.module_id+"_ReferencesMenu","").requestFormsReferenceMenu_onClick = function(event) {
            var elem_id = "ReferenceRequestForms_"+entity.module_id+"_List";
            var window_elem_id = "Window_Window"+elem_id.replace(/_/g,'');
            var params = new Object;
            params["hook"] = "3";
            params["object_text"] = "Формы обращения";
            getWindowManager().show_window(window_elem_id,elem_id,params,null,null,null,true);                            
        };
        $O("DocFlowMenu_"+entity.module_id+"_ReferencesMenu","").projectConditionsReferenceMenu_onClick = function(event) {
            var elem_id = "ReferenceProjectConditions_"+entity.module_id+"_List";
            var window_elem_id = "Window_Window"+elem_id.replace(/_/g,'');
            var params = new Object;
            params["hook"] = "3";
            params["object_text"] = "Состояния проекта";
            getWindowManager().show_window(window_elem_id,elem_id,params,null,null,null,true);                            
        };
        $O("DocFlowMenu_"+entity.module_id+"_ReferencesMenu","").taskConditionsReferenceMenu_onClick = function(event) {
            var elem_id = "ReferenceTaskConditions_"+entity.module_id+"_List";
            var window_elem_id = "Window_Window"+elem_id.replace(/_/g,'');
            var params = new Object;
            params["hook"] = "3";
            params["object_text"] = "Состояния задачи";
            getWindowManager().show_window(window_elem_id,elem_id,params,null,null,null,true);                            
        };
        $O("DocFlowMenu_"+entity.module_id+"_ReferencesMenu","").projectsReferenceMenu_onClick = function(event) {
            var elem_id = "ReferenceProjects_"+entity.module_id+"_List";
            var window_elem_id = "Window_Window"+elem_id.replace(/_/g,'');
            var params = new Object;
            params["hook"] = "3";
            params["object_text"] = "Проекты";
            getWindowManager().show_window(window_elem_id,elem_id,params,null,null,null,true);                            
        };
        $O("DocFlowMenu_"+entity.module_id+"_ReferencesMenu","").contragentKindsReferenceMenu_onClick = function(event) {
            var elem_id = "ReferenceContragentKinds_"+entity.module_id+"_List";
            var window_elem_id = "Window_Window"+elem_id.replace(/_/g,'');
            var params = new Object;
            params["hook"] = "3";
            params["object_text"] = "Виды контрагентов";
            getWindowManager().show_window(window_elem_id,elem_id,params,null,null,null,true);                            
        };
        $O("DocFlowMenu_"+entity.module_id+"_ReferencesMenu","").productKindsReferenceMenu_onClick = function(event) {
            var elem_id = "ReferenceProductKinds_"+entity.module_id+"_List";
            var window_elem_id = "Window_Window"+elem_id.replace(/_/g,'');
            var params = new Object;
            params["hook"] = "3";
            params["object_text"] = "Виды номенклатуры";
            getWindowManager().show_window(window_elem_id,elem_id,params,null,null,null,true);                            
        };
        $O("DocFlowMenu_"+entity.module_id+"_ReferencesMenu","").productsReferenceMenu_onClick = function(event) {
            var elem_id = "ReferenceProducts_"+entity.module_id+"_List";
            var window_elem_id = "Window_Window"+elem_id.replace(/_/g,'');
            var params = new Object;
            params["hook"] = "3";
            params["object_text"] = "Номенклатура";
            getWindowManager().show_window(window_elem_id,elem_id,params,null,null,null,true);                            
        };
        $O("DocFlowMenu_"+entity.module_id+"_ReferencesMenu","").dimensionsReferenceMenu_onClick = function(event) {
            var elem_id = "ReferenceDimensions_"+entity.module_id+"_List";
            var window_elem_id = "Window_Window"+elem_id.replace(/_/g,'');
            var params = new Object;
            params["hook"] = "3";
            params["object_text"] = "Единицы измерения";
            getWindowManager().show_window(window_elem_id,elem_id,params,null,null,null,true);                            
        };
    }
    if ($O("DocFlowMenu_"+entity.module_id+"_DocumentsMenu","")!=null) {
        $O("DocFlowMenu_"+entity.module_id+"_DocumentsMenu","").contragentRequestDocumentsMenu_onClick = function(event) {
            var elem_id = "DocumentContragentRequest_"+entity.module_id+"_List";
            var window_elem_id = "Window_Window"+elem_id.replace(/_/g,'');
            var params = new Object;
            params["hook"] = "3";
            params["object_text"] = "Обращения контрагентов";
            getWindowManager().show_window(window_elem_id,elem_id,params,null,null,null,true);                            
        };
        $O("DocFlowMenu_"+entity.module_id+"_DocumentsMenu","").orderDocumentsMenu_onClick = function(event) {
            var elem_id = "DocumentOrder_"+entity.module_id+"_List";
            var window_elem_id = "Window_Window"+elem_id.replace(/_/g,'');
            var params = new Object;
            params["hook"] = "3";
            params["object_text"] = "Заказы";
            getWindowManager().show_window(window_elem_id,elem_id,params,null,null,null,true);                            
        };
        $O("DocFlowMenu_"+entity.module_id+"_DocumentsMenu","").changeOrderConditionDocumentsMenu_onClick = function(event) {
            var elem_id = "DocumentChangeOrderCondition_"+entity.module_id+"_List";
            var window_elem_id = "Window_Window"+elem_id.replace(/_/g,'');
            var params = new Object;
            params["hook"] = "3";
            params["object_text"] = "Изменение состояния заказа";
            getWindowManager().show_window(window_elem_id,elem_id,params,null,null,null,true);                            
        };
        $O("DocFlowMenu_"+entity.module_id+"_DocumentsMenu","").changeProjectConditionDocumentsMenu_onClick = function(event) {
            var elem_id = "DocumentChangeProjectCondition_"+entity.module_id+"_List";
            var window_elem_id = "Window_Window"+elem_id.replace(/_/g,'');
            var params = new Object;
            params["hook"] = "3";
            params["object_text"] = "Изменение состояния проекта";
            getWindowManager().show_window(window_elem_id,elem_id,params,null,null,null,true);                            
        };
        $O("DocFlowMenu_"+entity.module_id+"_DocumentsMenu","").changeTaskConditionDocumentsMenu_onClick = function(event) {
            var elem_id = "DocumentChangeTaskCondition_"+entity.module_id+"_List";
            var window_elem_id = "Window_Window"+elem_id.replace(/_/g,'');
            var params = new Object;
            params["hook"] = "3";
            params["object_text"] = "Изменение состояния задачи";
            getWindowManager().show_window(window_elem_id,elem_id,params,null,null,null,true);                            
        };
        $O("DocFlowMenu_"+entity.module_id+"_DocumentsMenu","").invoiceDocumentsMenu_onClick = function(event) {
            var elem_id = "DocumentInvoice_"+entity.module_id+"_List";
            var window_elem_id = "Window_Window"+elem_id.replace(/_/g,'');
            var params = new Object;
            params["hook"] = "3";
            params["object_text"] = "Счета на оплату";
            getWindowManager().show_window(window_elem_id,elem_id,params,null,null,null,true);                            
        };
        $O("DocFlowMenu_"+entity.module_id+"_DocumentsMenu","").contractDocumentsMenu_onClick = function(event) {
            var elem_id = "DocumentContract_"+entity.module_id+"_List";
            var window_elem_id = "Window_Window"+elem_id.replace(/_/g,'');
            var params = new Object;
            params["hook"] = "3";
            params["object_text"] = "Договоры";
            getWindowManager().show_window(window_elem_id,elem_id,params,null,null,null,true);                            
        };
        $O("DocFlowMenu_"+entity.module_id+"_DocumentsMenu","").workReportDocumentsMenu_onClick = function(event) {
            var elem_id = "DocumentWorkReport_"+entity.module_id+"_List";
            var window_elem_id = "Window_Window"+elem_id.replace(/_/g,'');
            var params = new Object;
            params["hook"] = "3";
            params["object_text"] = "Отчеты о работе";
            getWindowManager().show_window(window_elem_id,elem_id,params,null,null,null,true);                            
        }; 
        $O("DocFlowMenu_"+entity.module_id+"_DocumentsMenu","").taskDocumentsMenu_onClick = function(event) {
            var elem_id = "DocumentTask_"+entity.module_id+"_List";
            var window_elem_id = "Window_Window"+elem_id.replace(/_/g,'');
            var params = new Object;
            params["hook"] = "3";
            params["object_text"] = "Задачи";
            getWindowManager().show_window(window_elem_id,elem_id,params,null,null,null,true);                            
        };
        $O("DocFlowMenu_"+entity.module_id+"_DocumentsMenu","").quickSaleDocumentsMenu_onClick = function(event) {
            var elem_id = "DocumentQuickSale_"+entity.module_id+"_List";
            var window_elem_id = "Window_Window"+elem_id.replace(/_/g,'');
            var params = new Object;
            params["hook"] = "3";
            params["object_text"] = "Продажи";
            getWindowManager().show_window(window_elem_id,elem_id,params,null,null,null,true);                            
        };                
    }
    
    if ($O("DocFlowMenu_"+entity.module_id+"_ReportsMenu","")!=null) {
        $O("DocFlowMenu_"+entity.module_id+"_ReportsMenu","").globalSearchMenu_onClick = function(event) {
            var elem_id = "GlobalSearchTable_"+entity.module_id+"_tbl";
            var window_elem_id = "Window_Window"+elem_id.replace(/_/g,'');
            var params = new Object;
            params["hook"] = "setParams";
            params["object_text"] = "Глобальный поиск";
            getWindowManager().show_window(window_elem_id,elem_id,params,null,null,null,true);                            
        };
        $O("DocFlowMenu_"+entity.module_id+"_ReportsMenu","").registryReportsMenu_onClick = function(event) {
            var params = new Object;
            params["hook"] = "setParams";
            params["object_text"] = "Отчеты по регистрам";
			getWindowManager().show_window("Window_RegistryReports"+this.module_id.replace(/_/g,"")+"reps","RegistryReports_"+this.module_id+"_reps",params,null,null,null,true);
        };        
        $O("DocFlowMenu_"+entity.module_id+"_ReportsMenu","").registryDiscountCardsReportsMenu_onClick = function(event) {
            var params = new Object;
            params["hook"] = "7";
            params["object_text"] = "Дисконтные карты";
            params["defaultReport"] = "Остатки";
			getWindowManager().show_window("Window_RegistryDiscountCards"+this.module_id.replace(/_/g,"")+"reps","RegistryDiscountCards_"+this.module_id+"_reps",params,null,null,null,true);
        };        
    }
    
    if ($O("DocFlowMenu_"+entity.module_id+"_SettingsMenu","")!=null) {
        $O("DocFlowMenu_"+entity.module_id+"_SettingsMenu","").apacheUsersMenu_onClick = function(event) {
            var elem_id = "ApacheUsers_"+entity.module_id+"_List";
            var window_elem_id = "Window_Window"+elem_id.replace(/_/g,'');
            var params = new Object;
            params["hook"] = "setParams";
            params["object_text"] = "Учетные записи пользователей";
            getWindowManager().show_window(window_elem_id,elem_id,params,null,null,null,true);                            
        };
        $O("DocFlowMenu_"+entity.module_id+"_SettingsMenu","").crmTablesMenu_onClick = function(event) {
            var elem_id = "EntityGroupsTable_"+entity.module_id+"_List";
            var window_elem_id = "Window_Window"+elem_id.replace(/_/g,'');
            var params = new Object;
            params["hook"] = "3";
            params["object_text"] = "Мои дела";
            getWindowManager().show_window(window_elem_id,elem_id,params,null,null,null,true);                            
        };
        $O("DocFlowMenu_"+entity.module_id+"_SettingsMenu","").deleteMarkedObjectsMenu_onClick = function(event) {
            var elem_id = "DeleteMarkedObjectsWindow_"+entity.module_id+"_List";
            var window_elem_id = "Window_Window"+elem_id.replace(/_/g,'');
            var params = new Object;
            params["hook"] = "setParams";
            params["object_text"] = "Удаление помеченных объектов";
            getWindowManager().show_window(window_elem_id,elem_id,params,null,null,null,true);                            
        };
        $O("DocFlowMenu_"+entity.module_id+"_SettingsMenu","").rolesMenu_onClick = function(event) {
            var elem_id = "MetadataRole_"+entity.module_id+"_List";
            var window_elem_id = "Window_Window"+elem_id.replace(/_/g,'');
            var params = new Object;
            params["hook"] = "5";
            params["object_text"] = "Роли пользователей";
            getWindowManager().show_window(window_elem_id,elem_id,params,null,null,null,true);                            
        };
        $O("DocFlowMenu_"+entity.module_id+"_SettingsMenu","").metadataMenu_onClick = function(event) {
            var elem_id = "MetadataGroupsTree_"+entity.module_id+"_List";
            var window_elem_id = "Window_Window"+elem_id.replace(/_/g,'');
            var params = new Object;
            params["hook"] = "3";
            params["object_text"] = "Метаданные";
            getWindowManager().show_window(window_elem_id,elem_id,params,null,null,null,true);                            
        };                
        $O("DocFlowMenu_"+entity.module_id+"_SettingsMenu","").codesMenu_onClick = function(event) {
            var elem_id = "MetadataObjectCode_"+entity.module_id+"_List";
            var window_elem_id = "Window_Window"+elem_id.replace(/_/g,'');
            var params = new Object;
            params["hook"] = "7";
            params["object_text"] = "Алгоритмы";
            getWindowManager().show_window(window_elem_id,elem_id,params,null,null,null,true);                            
        };        
        $O("DocFlowMenu_"+entity.module_id+"_SettingsMenu","").firmSettingsMenu_onClick = function(event) {
            var elem_id = "BasicCompanyInfo_"+entity.module_id+"_1";
            var window_elem_id = "Window_Window"+elem_id.replace(/_/g,'');
            var params = new Object;
            params["hook"] = "setParams";
            params["object_text"] = "Основные сведения о компании";
            getWindowManager().show_window(window_elem_id,elem_id,params,null,null,null);                            
        };        
    }
    if ($O("DocFlowMenu_"+entity.module_id+"_HelpMenu","")!=null) {
        $O("DocFlowMenu_"+entity.module_id+"_HelpMenu","").referenceMenu_onClick = function(event) {
			var params = new Object;
	        getWindowManager().show_window("Window_HTMLBook"+this.module_id.replace(/_/g,"")+"guide1","HTMLBook_"+this.module_id+"_"+entity.helpGuideId+"_1",params,this.opener_item.getAttribute("object"),this.opener_item.id);
        };
        $O("DocFlowMenu_"+entity.module_id+"_HelpMenu","").aboutMenu_onClick = function(event) {
			var params = new Object;
	        getWindowManager().show_window("Window_HTMLBook"+this.module_id.replace(/_/g,"")+"guide1","HTMLBook_"+this.module_id+"_crm_1",params,this.opener_item.getAttribute("object"),this.opener_item.id);
        };
        $O("DocFlowMenu_"+entity.module_id+"_HelpMenu","").sendRequestMenu_onClick = function(event) {
			var params = new Object;
	        getWindowManager().show_window("Window_UserRequest"+this.module_id.replace(/_/g,"")+"request","UserRequest_"+this.module_id+"_request",params,this.opener_item.getAttribute("object"),this.opener_item.id,null,true);
        };
    }