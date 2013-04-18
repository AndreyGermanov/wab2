globalTopWindow.taskbar_loaded = true;
windowManager = globalTopWindow.getWindowManager(); 
if (windowManager.showInfoPanel) {
    var params = new Object;
    params["moveable"] = "0";
    params["resizeable"] = "0";
    params["resizeable_top"] = "1";
    params["resizeable_bottom"] = "0";
    params["resizeable_left"] = "0";
    params["resizeable_right"] = "0";
    params["dock_top"] = "0";
    params["dock_left"] = "1";
    params["dock_right"] = "1";
    params["dock_bottom"] = "1";
    params["height"] = window.innerHeight-windowManager.height-1;                
    params["has_close"] = "0";
    params["has_minimize"] = "1";
    params["has_maximize"] = "0";
    params["left"] = "1";
    params["top"] = "1";
    params["width"] = "99%";
    params["hook"] = "setParams";
    params["module_id"] = '{module_id}';
    windowManager.show_window('Window_InfoPanelNew','InfoPanel',params,null,null,null,true);
}            
windowManager.resize_window();