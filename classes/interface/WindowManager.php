<?php
/**
 * Класс оконного менеджера
 *
 * Отвечает за отображение панели задач и кнопок на панели задач, связанных с
 * открытыми окнами. Управляет созданием и уничтожением окон.
 * @author andrey
 */
class WindowManager extends WABEntity{
    function construct($params) {
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        $this->skinPath = $app->skinPath;
        $this->wabBackgroundColor = $app->wabBackgroundColor;
        $this->left = 1;
        $this->top = 1;
        $this->width = "99%";
        $this->height = "60";
        $this->template = "templates/interface/WindowManager.html";
        $this->css = $app->skinPath."styles/WindowManager.css";
        $this->handler = "scripts/handlers/interface/WindowManager.js";
        $this->taskbar_width="100%";
        $this->taskbar_height="30";
        $this->showMainMenu = 0;
        $this->mainMenuName = "";
        $this->showControlPanel = 0;     
        $this->appUser = @$app->User;   
        $this->showMainMenu = @$app->user->config["interface"]["showMainMenu"];
        $this->mainMenuName = @$app->user->config["interface"]["mainMenuName"];
        $this->showControlPanel = @$app->user->config["interface"]["showControlPanel"];
        $this->customObjectName = @$app->user->config["interface"]["customObjectName"];
        $this->showInfoPanel = @$app->user->config["interface"]["showInfoPanel"];
        $this->defaultModuleName = @$app->defaultModuleName;
        if ($this->showMainMenu=="")
        	$this->showMainMenu = "0";
        if ($this->showControlPanel=="")
        	$this->showControlPanel = "0";
        if ($this->showInfoPanel=="")
        	$this->showInfoPanel = "0";
        $this->clientClass = "WindowManager";
        $this->parentClientClasses = "Entity";
        $this->autorun = array();
        if (file_exists("/var/WAB2/users/".$app->User."/settings/autorun"))
        	$this->autorun = json_decode(file_get_contents("/var/WAB2/users/".$app->User."/settings/autorun"));
        $this->autorunStr = json_encode($this->autorun);
        $this->path = "";
        $this->fileId = "";
    }

    function initTaskbar() {   
        parent::parseTemplate($this->template, $this->handler, $this->css);
    }    
}
?>