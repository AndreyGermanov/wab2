//include scripts/handlers/interface/Menu.js

var prevScrollY = 0;
var firstTop = 0;
var currentMenu = null;
var currentTitle = "";

   function onload(event) {
	   var pos = getElementPosition('{object_id}_menu_table');
       firstTop = pos.top;
    }
    if ($O("MedicMenu_"+entity.module_id+"_top","")!=null) {
        $O("MedicMenu_"+entity.module_id+"_top","").referencesMenu_onClick = function(event) {
            var pos = getElementPosition(eventTarget(event).getAttribute("item_id"));
            var leftt = pos.left;
            var topp = pos.top+pos.height;
            currentMenu=$O("MedicMenu_"+entity.module_id+"_top","").showSubMenu(leftt,topp,"DocFlowMenu_"+this.module_id+"_ReferencesMenu",event);
            currentTitle="DocFlowMenu_"+entity.module_id+"_ReferencesMenu";
        };
        $O("MedicMenu_"+entity.module_id+"_top","").documentsMenu_onClick = function(event) {
            var pos = getElementPosition(eventTarget(event).getAttribute("item_id"));
            var leftt = pos.left;
            var topp = pos.top+pos.height;
            currentMenu=$O("MedicMenu_"+entity.module_id+"_top","").showSubMenu(leftt,topp,"DocFlowMenu_"+this.module_id+"_DocumentsMenu",event);
            currentTitle="DocFlowMenu_"+entity.module_id+"_DocumentsMenu";
        };       
        $O("MedicMenu_"+entity.module_id+"_top","").reportsMenu_onClick = function(event) {
            var pos = getElementPosition(eventTarget(event).getAttribute("item_id"));
            var leftt = pos.left;
            var topp = pos.top+pos.height;
            currentMenu=$O("MedicMenu_"+entity.module_id+"_top","").showSubMenu(leftt,topp,"DocFlowMenu_"+this.module_id+"_ReportsMenu",event);
            currentTitle="DocFlowMenu_"+entity.module_id+"_ReportsMenu";
        };       
        $O("MedicMenu_"+entity.module_id+"_top","").settingsMenu_onClick = function(event) {
            var pos = getElementPosition(eventTarget(event).getAttribute("item_id"));
            var leftt = pos.left;
            var topp = pos.top+pos.height;
            currentMenu=$O("MedicMenu_"+entity.module_id+"_top","").showSubMenu(leftt,topp,"DocFlowMenu_"+this.module_id+"_SettingsMenu",event);
            currentTitle="DocFlowMenu_"+entity.module_id+"_SettingsMenu";
        };
        $O("MedicMenu_"+entity.module_id+"_top","").helpMenu_onClick = function(event) {
            var pos = getElementPosition(eventTarget(event).getAttribute("item_id"));
            var leftt = pos.left;
            var topp = pos.top+pos.height;
            currentMenu=$O("MedicMenu_"+entity.module_id+"_top","").showSubMenu(leftt,topp,"DocFlowMenu_"+this.module_id+"_HelpMenu",event);
            currentTitle="DocFlowMenu_"+entity.module_id+"_HelpMenu";
        };        
    }
    
    if ($O("DocFlowMenu_"+entity.module_id+"_ReferencesMenu","")!=null) {
        $O("DocFlowMenu_"+entity.module_id+"_ReferencesMenu","").dimensionsMenu_onClick = function(event) {
                var elem_id = "ReferenceDimensions_"+entity.module_id+"_List";
                var window_elem_id = "Window_Window"+elem_id.replace(/_/g,'');
                var params = new Object;
                params["hook"] = "3";
                params["object_text"] = "Единицы измерения";
                getWindowManager().show_window(window_elem_id,elem_id,params,null,null,null,true);                            
        };
        $O("DocFlowMenu_"+entity.module_id+"_ReferencesMenu","").clientsReferenceMenu_onClick = function(event) {
                var elem_id = "ReferencePatients_"+entity.module_id+"_List";
                var window_elem_id = "Window_Window"+elem_id.replace(/_/g,'');
                var params = new Object;
                params["hook"] = '3';
                params["object_text"] = "Пациенты";
                getWindowManager().show_window(window_elem_id,elem_id,params,null,null,null,true);                            
        };
        $O("DocFlowMenu_"+entity.module_id+"_ReferencesMenu","").bloodAnalyzeDefinitionsMenu_onClick = function(event) {
                var elem_id = "BloodDefinitionsReference_"+entity.module_id+"_List";
                var window_elem_id = "Window_Window"+elem_id.replace(/_/g,'');
                var params = new Object;
                params["hook"] = "4";
                params["object_text"] = "Показатели анализа крови";
                getWindowManager().show_window(window_elem_id,elem_id,params,null,null,null,true);                            
        };
        $O("DocFlowMenu_"+entity.module_id+"_ReferencesMenu","").bloodAnalyzeTypesMenu_onClick = function(event) {
                var elem_id = "BloodAnalyzeTypesReference_"+entity.module_id+"_List";
                var window_elem_id = "Window_Window"+elem_id.replace(/_/g,'');
                var params = new Object;
                params["hook"] = "3";
                params["object_text"] = "Типы анализа крови";
                getWindowManager().show_window(window_elem_id,elem_id,params,null,null,null,true);                            
        };
        $O("DocFlowMenu_"+entity.module_id+"_ReferencesMenu","").bloodAnalyzeResultsMenu_onClick = function(event) {
                var elem_id = "BloodAnalyzeResultsReference_"+entity.module_id+"_List";
                var window_elem_id = "Window_Window"+elem_id.replace(/_/g,'');
                var params = new Object;
                params["hook"] = "3";
                params["object_text"] = "Результаты анализа крови";
                getWindowManager().show_window(window_elem_id,elem_id,params,null,null,null,true);                            
        };
        $O("DocFlowMenu_"+entity.module_id+"_ReferencesMenu","").citiesMenu_onClick = function(event) {
            var elem_id = "ReferenceCities_"+entity.module_id+"_List";
            var window_elem_id = "Window_Window"+elem_id.replace(/_/g,'');
            var params = new Object;
            params["hook"] = "3";
            params["object_text"] = "Города";
            getWindowManager().show_window(window_elem_id,elem_id,params,null,null,null,true);                            
    };
        $O("DocFlowMenu_"+entity.module_id+"_SettingsMenu","").apacheUsersMenu_onClick = function(event) {
            var elem_id = "ApacheUsers_"+entity.module_id+"_List";
            var window_elem_id = "Window_Window"+elem_id.replace(/_/g,'');
            var params = new Object;
            params["hook"] = "setParams";
            params["object_text"] = "Учетные записи пользователей";
            getWindowManager().show_window(window_elem_id,elem_id,params,null,null,null,true);                            
    };
        $O("DocFlowMenu_"+entity.module_id+"_DocumentsMenu","").bloodAnalyzeDocumentMenu_onClick = function(event) {
                var elem_id = "DocumentBloodAnalyze_"+entity.module_id+"_List";
                var window_elem_id = "Window_Window"+elem_id.replace(/_/g,'');
                var params = new Object;
                params["hook"] = "3";
                params["object_text"] = "Список документов Анализ крови";
                getWindowManager().show_window(window_elem_id,elem_id,params,null,null,null,true);                            
        };
        $O("DocFlowMenu_"+entity.module_id+"_HelpMenu","").referenceMenu_onClick = function(event) {
			getWindowManager().show_window("Window_HTMLBook"+this.module_id.replace(/_/g,"")+"guide1","HTMLBook_"+this.module_id+"_medic_1",null,null,null);
        };
        $O("DocFlowMenu_"+entity.module_id+"_ReportsMenu","").bloodAnalyzeDefinitionMenu_onClick = function(event) {
			getWindowManager().show_window("Window_ReportBloodAnalyze"+this.module_id.replace(/_/g,"")+"rep","ReportBloodAnalyze_"+this.module_id+"_rep",null,null,null,null,true);
        };
        $O("DocFlowMenu_"+entity.module_id+"_ReportsMenu","").registryReportsMenu_onClick = function(event) {
            var params = new Object;
            params["hook"] = "setParams";
            params["object_text"] = "Отчеты по регистрам";
			getWindowManager().show_window("Window_RegistryReports"+this.module_id.replace(/_/g,"")+"reps","RegistryReports_"+this.module_id+"_reps",params,null,null,null,true);
        };
        $O("DocFlowMenu_"+entity.module_id+"_HelpMenu","").aboutMenu_onClick = function(event) {
			var params = new Object;
			params["hook"] = "setParams";
			params["has_maximize"] = "0";
			params["has_minimize"] = "0";
			params["resizeable"] = "0";
			getWindowManager().show_window("Window_AboutWindow"+this.module_id.replace(/_/g,"")+"about","AboutWindow_"+this.module_id+"_about",params,null,null,null,true);
        };
        $O("DocFlowMenu_"+entity.module_id+"_HelpMenu","").sendRequestMenu_onClick = function(event) {
			var params = new Object;
	        getWindowManager().show_window("Window_UserRequest"+this.module_id.replace(/_/g,"")+"request","UserRequest_"+this.module_id+"_request",params,this.opener_item.getAttribute("object"),this.opener_item.id,null,true);
        };       
    }