<?php
/**
 * Класс окна "О программе" медицинской подсистемы
 *
 * @author andrey
 */
class AboutWindow extends WABEntity {
    
    function construct($params) {
        global $Objects;
        parent::construct($params);
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        $this->skinPath = $app->skinPath;        
        $this->template = "templates/docflow/medic/AboutWindow.html";
        $this->icon = "{skinPath}images/Tree/info.png";
        $this->width=480;
        $this->height=310;
        $this->overrided = "width,height";
        $this->platformName = "<b><font color='#007700'>ЛВА Конструктор Web-приложений 2</font><br/>версия 1.1.04</b>";
        $this->configName = "<b><font color='#770000'>Учет анализов в медицине</font><br/> версия 0.1</b>";
        $this->clientClass = "AboutWindow";
        $this->parentClientClasses = "Entity";        
    }
    
    function getArgs() {
        $result = parent::getArgs();
        $data = "referencesMenu~Справочники&nbsp;~~~|documentsMenu~Документы&nbsp;~~~|reportsMenu~Отчеты&nbsp;~~~|helpMenu~Помощь&nbsp;~~~";
        $result["{data}"] = $data;
        return $result;
    }
    
    function getPresentation() {
		return "О программе";
	}
}

?>
