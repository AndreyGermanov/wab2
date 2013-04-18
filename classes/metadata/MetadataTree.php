<?php
	class MetadataTree extends Tree {
		
		function construct($params) {
			
			parent::construct($params);
			
			global $Objects;
			$app = $Objects->get("Application");
			if (!$app->initiated)
				$app->initModules();			
			$this->app = $app;
			$this->skinPath = $app->skinPath;
			
			// Массив метаданных, дерево по которым строится (fields, models, codes)
			$this->metadataArray = "fields";

			// Массив групп метаданных, дерево по которым строится (groups,modelGroups,codesGroups)
			$this->metadataGroupArray = "";
			
			// Класс метаданных, дерево по которым строится (MetadataObjectField, MetadataObjectModel, MetadataObjectCode)
			$this->metadataClass = "";
			
			// класс групп метаданных, дерево по которым строится (MetadataGroup, MetaDataModelGroup, MetadataCodeGroup)
			$this->metadataGroupClass = "";
			
			// Отображать ли в дереве группы метаданных
			$this->showGroups = "1";
			
			// Отображать ли в дереве метаданные
			$this->showItems = "1";
			
			// Дерево создано для выбора элемента в поле ввода
			$this->forSelect = "0";
			
			// Поле, в которое будет выбираться значение
			$this->opener_item = "";
			
			// Можно ли выбирать группы
			$this->groupSelect = "0";
			
			// Можно ли выбирать элементы
			$this->itemSelect = "1";
			
			// Изображение корня дерева
			$this->icon = "";
			
			// Изображение группы
			$this->groupImage = "";
			
			// Изображение элемента
			$this->itemImage = "";
			
			// Класс контекстного меню корня дерева
			$this->rootContextMenuClass = "MetadataRootContextMenu";
			
			// Класс контекстного меню группы или элемента
			$this->contextMenuClass = "MetadataContextMenu";
			
			// Выбранный элемент, который должен быть выделен
			$this->selectedItem = "";
			
			$this->handler = "scripts/handlers/metadata/MetadataTree.js";
			$this->loaded = false;
			$this->items_string = "";
		}
		
		/*
		 * Функция получает имя массива метаданных для текущего
		 * типа метаданных
		 * 
		 * @return string
		 */
		function getGroupArray() {
			switch($this->metadataArray) {
				case "fields":
					return "groups";
				case "models":
					return "modelGroups";
				case "codes":
					return "codeGroups";
			}
		}
		
		function getMetadataClass() {
			switch($this->metadataArray) {
				case "fields":
					return "MetadataObjectField";
				case "models":
					return "MetadataObjectModel";
				case "codes":
					return "MetadataObjectCode";
				case "panels":
					return "MetadataPanel";
				case "interfaces":
					return "MetadataInterface";
				case "modules":
					return "ModuleConfig";
				case "roles":
					return "MetadataRole";
				case "tags":
					return "MetadataObjectTag";
				case "addressbooks":
					return "LDAPAddressBook";							
			}				
		}
		
		/** 
		 * Функция получает имя класса метаданных для
		 * текущего типа метаданных
		 * 
		 * @return string
		 */
		function getGroupClass() {
			switch ($this->metadataArray) {
				case "fields":
					return "MetadataGroup";
				case "models":
					return "MetadataModelGroup";
				case "codes":
					return "MetadataCodeGroup";
			}
		}
		
		function getRootImage() {
			switch ($this->metadataArray) {
				case "fields":
					return $this->skinPath."images/Tree/fields.png";
				case "models":
					return $this->skinPath."images/Tree/models.png";
				case "codes":
					return $this->skinPath."images/Tree/algo4.png";
				case "panels":
					return $this->skinPath."images/Tree/panels.png";
				case "interfaces":
					return $this->skinPath."images/Tree/interfaces.png";
				case "modules":
					return $this->skinPath."images/Tree/modules.png";
				case "roles":
					return $this->skinPath."images/Tree/role.gif";
				case "tags":
					return $this->skinPath."images/Tree/tag.png";
				case "addressbooks":
					return $this->skinPath."images/Tree/addrbook.gif";
			}				
		}
		
		function getItemImage() {
			switch ($this->metadataArray) {
				case "fields":
					return $this->skinPath."images/Tree/field.png";
				case "models":
					return $this->skinPath."images/Tree/model.png";
				case "codes":
					return $this->skinPath."images/Tree/algo2.png";
				case "panels":
					return $this->skinPath."images/Tree/panel.png";
				case "interfaces":
					return $this->skinPath."images/Tree/interface.png";
				case "modules":
					return $this->skinPath."images/Tree/module.png";
				case "roles":
					return $this->skinPath."images/Tree/role.gif";
				case "tags":
						return $this->skinPath."images/Tree/tag.png";							
				case "addressbooks":
					return $this->skinPath."images/Tree/addrbook.gif";
			}				
		}

		function getGroupImage() {
			switch ($this->metadataArray) {
				case "fields":
					return $this->skinPath."images/Tree/metagroup.png";
				case "models":
					return $this->skinPath."images/Tree/metagroup.png";
				case "codes":
					return $this->skinPath."images/Tree/metagroup.png";
				case "panels":
					return $this->skinPath."images/Tree/metagroup.png";
				case "interfaces":
					return $this->skinPath."images/Tree/metagroup.png";
				case "modules":
					return $this->skinPath."images/Tree/metagroup.png";
				case "roles":
					return $this->skinPath."images/Tree/metagroup.png";
				case "roles":
					return $this->skinPath."images/Tree/tag.png";							
				case "addressbooks":
					return $this->skinPath."images/Tree/metagroup.png";
			}
		}
		
		function getTitle() {
			switch ($this->metadataArray) {
				case "fields":
					return "Поля";
				case "models":
					return "Модели";
				case "codes":
					return "Алгоритмы";
				case "panels":
					return "Панели";
				case "interfaces":
					return "Интерфейсы";
				case "modules":
					return "Модули";
				case "roles":
					return "Роли";
					case "tags":
						return "Тэги";							
				case "addressbooks":
					return "Адресные книги LDAP";
				default:
					return "Поля";
			}				
		}
		
		function load() {
			// Если не указаны тип и класс групп, получаем их 
			// автоматически		
			$this->metadataClass = $this->getMetadataClass();			
			$this->metadataGroupArray = $this->getGroupArray();
			$this->metadataGroupClass = $this->getGroupClass();
			$this->icon = $this->getRootImage();
			$this->itemImage = $this->getItemImage();
			$this->groupImage = $this->getGroupImage();
			$this->title = $this->getTitle();			
		}
		
		function show() {
			if ($this->loaded) {
				$this->setTreeItems();
			}
			parent::show();					
		}
		
		/**
		 * Функция получения массива элементов дерева для отображения.
		 * 
		 * В качестве параметра принимает имя родительской группы.
		 * Если параметр не указан, отображает список корневых групп
		 * и элементов
		 * 
		 * @param string $parent
		 */
		function setTreeItems($parent="",$rnd="") {			
				$this->load();			
			// Если родитель не указана
			if ($parent=="") {
				$grp = @$GLOBALS[$this->metadataGroupArray];
				$itm = $GLOBALS[$this->metadataArray];
				// Получаем списки корневых групп и элементов
				$groups = getTopGroups($grp);
				$items = getTopItems($itm,$grp);
			} else {
				// Иначе получаем список отображаемых групп из поля groups
				// переданного родителя
				$grp = @$GLOBALS[$this->metadataGroupArray][$parent]["groups"];
				$groups = array();
				if (is_array($grp))
					foreach($grp as $value)
						$groups[$value] = @$GLOBALS[$this->metadataGroupArray][$value];
				
				// Список полей получаем из поля fields переданного родителя
				$itm = @$GLOBALS[$this->metadataGroupArray][$parent]["fields"];
				$items = array();
				if (is_array($itm))
					foreach($itm as $value)
						$items[$value] = $GLOBALS[$this->metadataArray][$value];				
			}
			// Получаем список групп и элементов для отображения
			$result = array();
			if ($parent=="")
				$parentGroup = "";
			else
				$parentGroup = $this->metadataGroupClass."_".$this->module_id."_".$rnd."_".$parent;

			// Если нужно отображать группы, заполняем список групп			
			if ($this->showGroups) {
				foreach ($groups as $key=>$value) {
					$id = $this->metadataGroupClass."_".$this->module_id."_".rand()."_".$key;
					$result[$id]["id"] = $id;
					$result[$id]["title"] = $key."#".@$value["title"];
					$result[$id]["icon"] = $this->groupImage;
					$result[$id]["parent"] = $parentGroup;
					$result[$id]["loaded"] = "false";
					$result[$id]["subtree"] = "false";
				} 										
			}
			
			// Если нужно отображать элементы, заполняем список элементов
			if ($this->showItems) {
				foreach ($items as $key=>$value) {
					$id = $this->metadataClass."_".$this->module_id."_".rand()."_".$key;
					$result[$id]["id"] = $id;
					if ($this->metadataArray=="fields")
						$result[$id]["title"] = $key."#".@$value["params"]["title"];
					else if ($this->metadataArray=="tags" or $this->metadataArray=="tagGroups")
						$result[$id]["title"] = $key."#".$key;
					else {
						$result[$id]["title"] = $key."#".@$value["metaTitle"];
					}
					$result[$id]["icon"] = $this->itemImage;
					$result[$id]["parent"] = $parentGroup;
					$result[$id]["loaded"] = "true";
					$result[$id]["subtree"] = "false";
				}				
			}
			
			// Сортируем получившийся массив по возрастанию ключа
			ksort($result);		
			
			// Записываем массив в виде строки, которая используется для построения дерева
			$res = array();
			foreach($result as $value)
			{
				$res[count($res)] = implode("~",$value);
			}
			$this->items_string = implode("|",$res);			
		}
		
		function getHookProc($number) {
			switch ($number) {
				case '3': return "setTreeItemsHook";
			}
			return parent::getHookProc($number);
		}
		
		function setTreeItemsHook($arguments) {
			$this->setArguments($arguments);
			$this->setTreeItems(@$arguments["parent"],@$arguments["rnd"]);
			echo $this->items_string;
		}
				
		function getArgs() {
			$this->load();
			return parent::getArgs();
		}
	}
?>