<?php
/*
 * Класс управляет коллекцией шаблонов, каждый из которых представляет из себя
 * запись типа ItemTemplate, или элемент массива, если коллекция элементов
 * запрашивается в виде массива.
 *
 * Шаблоны хранятся в базе данных SQLite, которая находится в файле templates.db.
 * В классе создан метод connect(), который организует подключение к этой базе
 * данных.
 *
 * Для всех операций со списком элементов, используются методы класса-предка
 * (ItemCollection).
 */

class ItemTemplates extends ItemCollection {

    function construct($params) {
		if (count($params)>=2) {
			$this->object_id = @$params[2];
			$this->module_id = $params[0]."_".$params[1];
		} else {
			$this->object_id = @$params[0];
			$this->module_id = "";
		}
        $this->itemClass = "ItemTemplate";
    }

    function connect() {
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();

        if ($this->connection=="") {
            $manager = Doctrine_Manager::getInstance();
            $manager->setAttribute(Doctrine::ATTR_MODEL_LOADING,Doctrine::MODEL_LOADING_CONSERVATIVE);
            $this->connection = Doctrine_Manager::connection("sqlite:".$app->root_path."/templates/templates.db",'templates');
            
            $this->connection->createDatabase();
            Doctrine::createTablesFromModels('classes/template');           
        }
        $manager = Doctrine_Manager::getInstance();
        $manager->setCurrentConnection($this->connection->getName());
        return $this->connection;
    }

    function disconnect() {
        if ($this->connection!="") {
            $manager = Doctrine_Manager::getInstance();
            $manager->closeConnection($this->connection);
        }
    }

}
?>
