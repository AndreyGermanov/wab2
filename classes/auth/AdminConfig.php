<?php
/**
 * Класс предназначен для работы с конфигурационным файлом пользователя.
 * Конфигурационный файл пользователя имеет формат XML и может назначаться
 * каждому пользователю при создании. Если конфигурационный файл для пользователя
 * не указан, используется конфигурационный файл по умолчанию $app->root_path."/config/default.conf".
 *
 * Объект Application обращается к этому классу чтобы считать информацию о модулях,
 * доступных данному пользователю. Описания модулей находятся в контейнере Modules.
 * Для каждого модуля предназначен соответствующий контейнер Module. В качестве
 * атрибутов указывается все, что необходимо для создания закладки в Панели управления.
 *
 * name - название модуля
 * title -название, отображаемое на вкладке
 * image - изображение, отображаемое на вкладке
 * class - имя класса, ответственного за инициализацию модуля и отрисовку содержимого вкладки
 * 
 * Вкладка, которая отображается по умолчанию указывается в атрибуте name тэга DefaultModule.
 *
 * Каждый модуль в своих классах обращается к этому классу и читает настройки пользователя,
 * относящиеся конкретно к его модулю. Они находятся в соответствующем контейнере Modules.
 * Например для WebServerApplication указывается, где находятся конфигурационные файлы
 * сайтов и какие сайты данный пользователь имеет право видеть в дереве панели управления.
 * 
 * @author andrey
 */
class AdminConfig extends WABEntity {

    public $modules = array();
    public $defaultModules = array();
    
    function construct($params) {
        global $Objects;

        $app = $Objects->get("Application");
        $shell = $Objects->get("Shell_shell");
        $this->configFile = new DOMDocument();
        $user = $Objects->get("ApacheUser___".@$params[0]);
        $this->user = $user;

        if (!$this->user->loaded)
            $this->user->load();
        
		$this->config = $this->user->config;
		$this->modules = $this->user->modules;		
		
		$this->defaultModule = @$this->config["appconfig"]["defaultModule"];
		$this->networkSettingsStyle = @$this->config["appconfig"]["networkSettingsStyle"];
		$this->redHatNetworkSettingsFile = @$this->config["appconfig"]["redHatNetworkSettingsFile"];
		$this->debianNetworkSettingsFile = @$this->config["appconfig"]["debianNetworkSettingsFile"];
		$this->debianNetworkSettingsTemplateFile = @$this->config["appconfig"]["debianNetworkSettingsTemplateFile"];
		$this->debianNetworkRestartCommand = @$this->config["appconfig"]["debianNetworkRestartCommand"];
		$this->redHatNetworkRestartCommand = @$this->config["appconfig"]["redHatNetworkRestartCommand"];
				
        $this->clientClass = "AdminConfig";
        $this->parentClientClasses = "Entity";
        
    }

    function load() {        
    }
}
?>