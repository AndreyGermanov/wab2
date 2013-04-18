<?php
/**
 * Класс управляет элементом Web-сайта. Элемент Web-сайта хранит свои данные в трех
 * таблицах: WebItemRecord(items), WebItemMetadataRecord(metadata) и WebItemDataRecord(data)
 * и его потомках.
 *
 * Класс позволяет создавать элементы сайта, изменять их и удалять, однако для создания
 * новых элементов лучше использовать сам WebSite, его метод addItem(), так как элементы
 * сайтов не обязательно должны быть экземплярами класса WebItem, они также могут быть
 * его потомками или вообще экземплярами другого, произвольного класса.
 *
 * Также через этот класс можно получить список дочерних элементов, соседей и всех родителей
 * элемента. Можно отображать элемент по определенному шаблону.
 *
 * Элемент состоит из следующих полей
 *
 * base_id - идентификатор элемента в базе данных
 * id - полный идентификатор элемента, генерируемый функцией getId()
 * name - системное имя элемента
 * title - заголовок элемента
 * author - пользователь-создатель элемента
 * site - Web-приложение, которому принадлежит элемент
 * parent_id - идентификатор родителя элемента
 * parent - ссылка на элемент-родитель данного элемента
 * icon - картинка элемента
 * item_icon - картинка дочерних элементов
 * class - класс, экземпляром которого является элемент
 * item_class - класс, экземпляром которого будут являться потомки
 * template_object - шаблон элемента
 * item_template - шаблон дочерних элементов данного элемента
 * admin_template - шаблон администрирования элемента
 * admin_item_template - шаблон администрирования дочерних элементов
 * admin_list_template - шаблон администрирования списка дочерних элементов
 * tag - произвольные данные, привязанные к элементу
 * record - ссылка на запись в базе данных
 *
 * 
 */
class WebItem extends WABEntity{

    public $data = array();
    public $metadata = array();
    public $site;
    public $parent;
    public $record;
    public $metadata_types = array();
    public $reverse_metadata_types = array();
    public $changed_metadata = array();
    public $data_tables = array();
    public $data_tables1 = array();
    /**
     *
     * @global <объект> $Objects - глобальный кэш объектов
     * @param <массив> $params - список параметров, используемых при конструировании
     * экземпляра
     *
     * Изначально экземпляр создается методом $Objects->get(id) где ID это
     * глобальный идентификатор объекта. Глобальный идентификатор уникален. Он
     * имеет форму: <имя-класса>_<параметры>
     *
     * Метод get() класса $Objects убирает имя класса и берет <параметры>, которые
     * представляют из себя строку в формате параметр_параметр..._параметр. Он разбивает
     * ее методом explode("_",<параметры> и создает объект указанного класса и
     * передает ему этот массив в качестве параметра params.
     *
     * В случае с объектами класса WebItem, уникальный идентификатор может выглядеть так
     *
     * WebItem_<имя сайта>_<идентификатор элемента>_<идентификатор родителя>_<тип элемента>
     *
     * На основании этих данных формируется массив params:
     *
     * params[0] - имя сайта (класс WebSite)
     * params[1] - идентификатор элемента
     * params[2] - идентификатор родительского элемента
     * params[3] - в виде чего создается элемент: в виде раздела или в виде элемента. Если
     *             указано "Item", значит он создается в виде элемента, а иначе в виде раздела
     *             Если он создается в виде элемента, то в качестве шаблонов по умолчанию,
     *             класса и иконки присваиваются соответствующие опции родителя, начинающиеся
     *             на "item", а иначе, соответствующие опции родителя, которые не содержат item.
     */
    function construct($params = "") {
        global $Objects,$webitem_classes;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        $webitem_classes[count($webitem_classes)] = get_class($this);
        $this->metadata_types["integer"] = "Число";
        $this->metadata_types["boolean"] = "Логическое";
        $this->metadata_types["string"] = "Строка";
        $this->metadata_types["clob"] = "Текст";
        $this->metadata_types["date"] = "Дата";
        $this->metadata_types["image"] = "Изображение";
        $this->metadata_types["file"] = "Файл";
        $this->metadata_types["images"] = "Массив изображений";
        $this->metadata_types["files"] = "Массив файлов";
        $this->metadata_types["blob"] = "Двоичный объект";
        $this->metadata_types["item"] = "Элемент Web-сайта";

        $this->reverse_metadata_types["Число"] = "integer";
        $this->reverse_metadata_types["Логическое"] = "boolean";
        $this->reverse_metadata_types["Строка"] = "string";
        $this->reverse_metadata_types["Текст"] = "clob";
        $this->reverse_metadata_types["Дата"] = "date";
        $this->reverse_metadata_types["Изображение"] = "image";
        $this->reverse_metadata_types["Файл"] = "file";
        $this->reverse_metadata_types["Массив изображений"] = "images";
        $this->reverse_metadata_types["Массив файлов"] = "files";
        $this->reverse_metadata_types["Двоичный объект"] = "blob";
        $this->reverse_metadata_types["Элемент Web-сайта"] = "item";

        $this->data_tables["integer"] = "integer";
        $this->data_tables["boolean"] = "boolean";
        $this->data_tables["string"] = "string";
        $this->data_tables["clob"] = "clob";
        $this->data_tables["blob"] = "blob";
        $this->data_tables["date"] = "date";
        $this->data_tables["image"] = "image";
        $this->data_tables["file"] = "file";
        $this->data_tables["images"] = "images";
        $this->data_tables["files"] = "files";
        $this->data_tables["item"] = "item";

        $this->data_tables1["integer"] = "integer";
        $this->data_tables1["boolean"] = "boolean";
        $this->data_tables1["string"] = "string";
        $this->data_tables1["clob"] = "clob";
        $this->data_tables1["blob"] = "blob";
        $this->data_tables1["date"] = "date";
        $this->data_tables1["image"] = "image";
        $this->data_tables1["file"] = "file";
        $this->data_tables1["images"] = "images";
        $this->data_tables1["files"] = "files";
        $this->data_tables1["item"] = "item";
        $this->title = "";
        $this->name = "";
        $this->display_fields = "title~Заголовок~main~string";
        if (count($params)>3 and $params[0]!="") {
            $base = 3;
            $this->site = $Objects->get("WebSite_".$params[0]."_".$params[1]."_".$params[2]);
            $this->module_id = $params[0]."_".$params[1];
            $this->site->name = $params[2];
            $this->site->load();
            $this->site->connect();
        } else {
            if ($params[0]!="") {
            $base = 1; 
            $this->site = $Objects->get("WebSite_".$params[0]);
            $this->module_id = "";
            $this->site->name = $params[0];
            $this->site->load();
            $this->site->connect();
            }
	}
        if (@$params[$base]!="") {
            if (is_numeric($params[$base])) {
                $this->base_id = $params[$base];
            }
            else {
                $q = Doctrine_Query::create()
                    ->from("WebItemRecord r")
                    ->where("r.name = ?",$params[$base]);
                $result = $q->fetchOne();
                if (isset($result->id))
                    $this->base_id = $result->id;
            }
        }
        if (@$params[$base+1]!="") {
            $this->site->connect();
            Doctrine::GetTable('WebItemMetadataRecord')->setAttribute(Doctrine::ATTR_COLL_KEY, 'id');
            Doctrine::GetTable('WebItemDataRecord')->setAttribute(Doctrine::ATTR_COLL_KEY, 'id');
            Doctrine::GetTable('WebItemIntegerDataRecord')->setAttribute(Doctrine::ATTR_COLL_KEY, 'id');
            Doctrine::GetTable('WebItemBooleanDataRecord')->setAttribute(Doctrine::ATTR_COLL_KEY, 'id');
            Doctrine::GetTable('WebItemStringDataRecord')->setAttribute(Doctrine::ATTR_COLL_KEY, 'id');
            Doctrine::GetTable('WebItemDateDataRecord')->setAttribute(Doctrine::ATTR_COLL_KEY, 'id');
            Doctrine::GetTable('WebItemBlobDataRecord')->setAttribute(Doctrine::ATTR_COLL_KEY, 'id');
            Doctrine::GetTable('WebItemClobDataRecord')->setAttribute(Doctrine::ATTR_COLL_KEY, 'id');
            Doctrine::GetTable('WebItemImageDataRecord')->setAttribute(Doctrine::ATTR_COLL_KEY, 'id');
            Doctrine::GetTable('WebItemImagesDataRecord')->setAttribute(Doctrine::ATTR_COLL_KEY, 'id');
            Doctrine::GetTable('WebItemFileDataRecord')->setAttribute(Doctrine::ATTR_COLL_KEY, 'id');
            Doctrine::GetTable('WebItemFilesDataRecord')->setAttribute(Doctrine::ATTR_COLL_KEY, 'id');
            Doctrine::GetTable('WebItemItemDataRecord')->setAttribute(Doctrine::ATTR_COLL_KEY, 'id');
            $record = new WebItemRecord();
            $record = $record->getTable()->find(@$params[$base+1]);

            if (isset($record->id)) {
                $this->parent = $record;
                $this->parent->load();
                $this->parent_id = $this->parent->id;
                $this->loaded_parent_id = $this->parent_id;
            }
            // Загружаем метаданные полей элемента из соответствующей подчиненной таблицы.
            // Doctrine при запросе помещает данные из подчиненных таблиц в массивы,
            // соответствующие указанным отношениям. В данном случае все элементы
            // метаданных собрались в массиве metadata. По умолчанию берутся метаданные
            // родительского элемента
            $this->author = @$_SERVER["PHP_AUTH_USER"];
            $this->metadata = array();
            $this->changed_metadata = array();
            foreach($this->reverse_metadata_types as $value) {
                $datatype = $value;
                $this->data[$datatype] = array();
            }
            foreach ($this->parent->metadata as $element) {
                $this->metadata[$element->name] = $element;
                $this->metadata[$element->name]->id = "";

                $type_name = $this->metadata_types[$this->metadata[$element->name]->type];

                $this->changed_metadata[$element->name]["name"] = $this->metadata[$element->name]->name;
                $this->changed_metadata[$element->name]["title"] = $this->metadata[$element->name]->title;
                $this->changed_metadata[$element->name]["type"] = $type_name;
                $this->changed_metadata[$element->name]["default_value"] = $this->metadata[$element->name]->default_value;
                $this->changed_metadata[$element->name]["is_group"] = $this->metadata[$element->name]->is_group;
                $this->changed_metadata[$element->name]["must_set"] = $this->metadata[$element->name]->must_set;
                $this->changed_metadata[$element->name]["must_unique"] = $this->metadata[$element->name]->must_unique;
                $this->changed_metadata[$element->name]["check_regexp"] = $this->metadata[$element->name]->check_regexp;
                $this->changed_metadata[$element->name]["min"] = $this->metadata[$element->name]->min;
                $this->changed_metadata[$element->name]["max"] = $this->metadata[$element->name]->max;
                $this->changed_metadata[$element->name]["accuracy"] = $this->metadata[$element->name]->accuracy;
                $this->changed_metadata[$element->name]["where_query"] = $this->metadata[$element->name]->where_query;
                $this->changed_metadata[$element->name]["author"] = $this->author;

                $field_name = $element->name;
                $class = "WebItem".ucfirst($this->data_tables[$element->type])."DataRecord";
                $datatype = $this->data_tables[$element->type];
                $this->data[$datatype][$field_name] = new $class();
                $this->data[$datatype][$field_name]->metadata_id = $element->id;
                $this->data[$datatype][$field_name]->id = "";
                $this->data[$datatype][$field_name]->value = $element->default_value;
                $this->data[$datatype][$field_name]->author = @$_SERVER["PHP_AUTH_USER"];
            }
        }
        else {
            $this->site->connect();
            Doctrine::GetTable('WebItemMetadataRecord')->setAttribute(Doctrine::ATTR_COLL_KEY, 'id');
            Doctrine::GetTable('WebItemDataRecord')->setAttribute(Doctrine::ATTR_COLL_KEY, 'id');
            Doctrine::GetTable('WebItemIntegerDataRecord')->setAttribute(Doctrine::ATTR_COLL_KEY, 'id');
            Doctrine::GetTable('WebItemBooleanDataRecord')->setAttribute(Doctrine::ATTR_COLL_KEY, 'id');
            Doctrine::GetTable('WebItemStringDataRecord')->setAttribute(Doctrine::ATTR_COLL_KEY, 'id');
            Doctrine::GetTable('WebItemDateDataRecord')->setAttribute(Doctrine::ATTR_COLL_KEY, 'id');
            Doctrine::GetTable('WebItemBlobDataRecord')->setAttribute(Doctrine::ATTR_COLL_KEY, 'id');
            Doctrine::GetTable('WebItemClobDataRecord')->setAttribute(Doctrine::ATTR_COLL_KEY, 'id');
            Doctrine::GetTable('WebItemImageDataRecord')->setAttribute(Doctrine::ATTR_COLL_KEY, 'id');
            Doctrine::GetTable('WebItemImagesDataRecord')->setAttribute(Doctrine::ATTR_COLL_KEY, 'id');
            Doctrine::GetTable('WebItemFileDataRecord')->setAttribute(Doctrine::ATTR_COLL_KEY, 'id');
            Doctrine::GetTable('WebItemFilesDataRecord')->setAttribute(Doctrine::ATTR_COLL_KEY, 'id');
            Doctrine::GetTable('WebItemItemDataRecord')->setAttribute(Doctrine::ATTR_COLL_KEY, 'id');
            $record = new WebItemRecord();
            $record = $record->getTable()->find(1);
            $this->parent = $record;
            $this->parent_id = $this->parent->id;
            $this->loaded_parent_id = $this->parent_id;
        }
        if (isset($params[$base+2]) and @$params[$base+2]!="")
            $this->openAs = $params[$base+2];

        // Если некоторые значения остались незаполненными, то заполняем их
        // значениями по умолчанию
        if ($this->class=="")
            $this->class = "WebItem";
        if ($this->item_class=="")
            $this->item_class = "WebItem";
        if ($this->icon=="")
            $this->icon = $app->skinPath."images/Tree/folder.gif";
        if ($this->item_icon=="")
            $this->item_icon = $app->skinPath."images/Tree/item.gif";
        
        $this->author = @$_SERVER["PHP_AUTH_USER"];
        $this->tag = "";
        $this->sort_order = "";
        $this->site->connect();
        $this->record = new WebItemRecord();

        $this->is_public = false;
        $this->loaded = false;

        $this->tabset_id = $this->module_id."_".$this->base_id."WebItem";

        $this->width = "730";
        $this->height = "440";
        $this->overrided = "width,height,icon";
        $this->tabs_string = "main|Основное|".$this->skinPath."images/spacer.gif;";
        $this->tabs_string .= "advanced|Дополнительное|".$this->skinPath."images/spacer.gif;";
        $this->tabs_string .= "items|Список элементов|".$this->skinPath."images/spacer.gif;";
        $this->tabs_string .= "system|Системные|".$this->skinPath."images/spacer.gif;";
        $this->tabs_string .= "metadata|Метаданные|".$this->skinPath."images/spacer.gif";
        $this->setTemplate();
    }

    function addByTemplate() {
        $this->setTemplate();
        $this->site->connect();
        $record = new WebItemRecord();
        $record = $record->getTable()->find($this->template_item);
        if (isset($record->id)) {
            $this->loaded_name = $record->name;
            $this->title = $record->title;
            $this->icon = $record->icon;
            $this->item_icon = $record->item_icon;
            $this->class = $record->class;
            $this->item_class = $record->item_class;
            $this->template_object = $record->template;
            $this->item_template = $record->item_template;
            $this->admin_template = $record->admin_template;
            $this->admin_item_template = $record->admin_item_template;
            $this->admin_list_template = $record->admin_list_template;
            $this->tag = $record->tag;
//            $this->is_public = $record->is_public;
            $this->sort_order = $record->sort_order;
            if ($record->display_fields!="")
                $this->display_fields = $record->display_fields;
            $this->metadata = array();
            foreach ($record->metadata as $element) {
                $this->metadata[$element->name] = $element;
                $this->metadata[$element->name]->id = "";
                $field_name = $element->name;
                $this->data[$element->type][$field_name] = "";
            }
            // получаем данные полей элемента из соответствующего массива data
            foreach ($this->data_tables1 as $type) {
                $datatype = "data_".$type;
                foreach ($record->$datatype as $element) {
                    $field_name = $record->metadata[$element->metadata_id]->name;
                    $this->data[$type][$field_name] = $element;
                    $this->data[$type][$field_name]->id = "";
                }
            }
        }
        $this->loaded = true;
    }

    /**
     * Функция на основании полученного шаблона заполняем стандартные поля объекта,
     * отвечающие за отображение объекта
     */
    function setTemplate() {

        if (!$this->loaded) {
            if (@$this->openAs=="asAdminItem") {
                $this->template_object = $this->parent->item_template;
                $this->admin_template = $this->parent->admin_item_template;
                $this->class = $this->parent->item_class;
                $this->icon = $this->parent->item_icon;
            }
            else {
                $this->template_object = $this->parent->template;
                $this->admin_template = $this->parent->admin_template;
                $this->class = $this->parent->class;
                $this->icon = $this->parent->icon;
            }
            $this->item_template = $this->parent->item_template;
            $this->admin_list_template = $this->parent->admin_list_template;
            $this->admin_item_template = $this->parent->admin_item_template;
            if ($this->admin_template == "")
                $this->admin_template = 49;
            if ($this->admin_item_template == "") {
                $this->admin_item_template = 49;
            }
            if ($this->admin_list_template == "") {
                $this->admin_list_template = 50;
            }
        }

//         На основании полученного шаблона заполняем стандартные поля объекта,
//         отвечающие за отображение объекта
        $childs_count = $this->getChildrenCount();
        if ($childs_count>0)
            $this->active_tab = "items";
        else
            $this->active_tab = "main";
        
        if ($this->openAs=="asAdminChapter") {
            $id = $this->admin_template;
        }
        else if ($this->openAs=="asAdminList") {
            $id = $this->admin_list_template;
        }
        else if ($this->openAs=="asAdminItem") {
            $id = $this->admin_item_template;
        }
        else
            $id = $this->template_object;
        global $Objects;
	if ($this->module_id!="")
	        $template = $Objects->get("ItemTemplate_".$this->module_id."_".$id);
	else
	        $template = $Objects->get("ItemTemplate_".$id);
        $template->load();
        if ($this->template_object!="") {
		if ($this->module_id!="")
	            $tpl = $Objects->get("ItemTemplate_".$this->module_id."_".$this->template_object);
		else
		    $tpl = $Objects->get("ItemTemplate_".$this->template_object);
            $tpl->load();
            $this->cssfile = $tpl->cssfile;
        }

        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        
        if ($template->id!="") {
            $this->template = $template->templatefile;
            if (substr($template->cssfile,0,1)=="/" or substr($template->cssfile,0,5)=="skins")
                $this->css = $template->cssfile;
            else
                $this->css = $app->skinPath.$template->cssfile;
            $this->handler = $template->handlerfile;
            $this->template_class = $template->classfile;
        }
    }

    /**
     * Функция загружает данные элемента из БД
     */
    function load() {
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        // Если это новый элемент, то загружать нечего
        if ($this->base_id=="" or $this->base_id<=0)
            return 0;
        // Если он не привязан ни к какому сайту, то тоже нечего загружать
        if ($this->site=="")
            return 0;

        // Подключаемся к базе данных сайта
        $conn = $this->site->connect();
        // Делаем запрос к базе, чтобы получить данные этого элемента
        $item = new WebItemRecord();
        $item = $item->getTable()->find($this->base_id);
        if ($item!=null)
            $item->load();
        // Если данные не были получены, то нет смысла продолжать
        if (!isset($item->id))
            return 0;        

        // Храним полученную запись, чтобы потом не загружать повторно
        $this->record = $item;

        // Заполняем поля объекта результатами запроса
        $this->name = $item->name;
        $this->loaded_name = $item->name;
        $this->title = $item->title;
        $this->author = $item->author;
        $this->created_at = $item->created_at;
        $this->updated_at = $item->updated_at;
        if (substr($item->icon,0,1)=="/")
            $this->icon = $item->icon;
        else
            $this->icon = $app->skinPath.$item->icon;
        if (substr($item->item_icon,0,1)=="/")
            $this->item_icon = $item->item_icon;
        else
            $this->item_icon = $app->skinPath.$item->item_icon;
        $this->class = $item->class;
        $this->item_class = $item->item_class;
        $this->template_object = $item->template;
        $this->item_template = $item->item_template;
        $this->admin_template = $item->admin_template;
        $this->admin_item_template = $item->admin_item_template;
        $this->admin_list_template = $item->admin_list_template;
        $this->tag = $item->tag;
        $this->is_public = $item->is_public;
        $this->sort_order = $item->sort_order;
        if ($item->display_fields!="")
            $this->display_fields = $item->display_fields;

        // Получаем ссылку на родителя
        $this->parent = $item->getNode()->getParent();
        $this->loaded_parent = $this->parent;
        
        // если родитель есть, то также присваиваем его id
        if (isset($this->parent->id))
            $this->parent_id = $this->parent->id;
        else
            $this->parent_id = 1;

        $this->loaded_parent_id = $this->parent_id;
        
        // Загружаем метаданные полей элемента из соответствующей подчиненной таблицы.
        // Doctrine при запросе помещает данные из подчиненных таблиц в массивы,
        // соответствующие указанным отношениям. В данном случае все элементы
        // метаданных собрались в массиве metadata

        $this->metadata = array();
        
        foreach ($item->metadata as $element) {
            $this->metadata[$element->name] = $element;
            $field_name = $element->name;
            if (!isset($this->data_tables[$element->type]))
                    continue;
            $class = "WebItem".ucfirst($this->data_tables[$element->type])."DataRecord";
            $datatype = $this->data_tables[$element->type];
            $this->data[$datatype][$field_name] = new $class();
            $this->data[$datatype][$field_name]->id = "";
            $this->data[$datatype][$field_name]->metadata_id = $element->id;
            $this->data[$datatype][$field_name]->value = $element->default_value;
            $this->data[$datatype][$field_name]->author = @$_SERVER["PHP_AUTH_USER"];
        }

        // получаем данные полей элемента из соответствующего массива data
        foreach($this->data_tables1 as $value) {
            $datatype = $value;
            $item_datatype = "data_".$datatype;
            $item->$item_datatype->loadRelated();
            foreach ($item->$item_datatype as $element) {                
                $field_name = $item->metadata[$element->metadata_id]->name;
                $this->data[$datatype][$field_name] = $element;
            }
        }

        $this->loaded = true;
        $this->changed_metadata = array();
        $this->setTemplate();
    }

    /**
     * Функция сохраняет данные элемента в базу
     */
    function save() {

        // Если заголовок не указан, то выходим
        if ($this->title == "") {
            $this->reportError("Укажите заголовок элемента !","save");
            return 0;
        }

        // Подключаемся к базе данных сайта
        $this->site->connect();

        // Если элемент с таким системным именем уже есть в базе, то выходим
        if ($this->name != $this->loaded_name) {
            $q = Doctrine_Query::create()
                 ->from("WebItemRecord")
                 ->where("name = ?",$this->name)
                 ->useQueryCache()
                 ->useResultCache();
            $result = $q->fetchOne();

            if (isset($result->id)) {
                $this->reportError("Элемент с системным именем '".$this->name."' уже есть в базе !","save");
                return 0;
            }
        }
        // Если изменился идентификатор родителя, то загрузим
        // нового родителя по этому новому идентификатору
        if ($this->parent_id != $this->loaded_parent_id) {
            $parent = new WebItemRecord();
            $parent = $parent->getTable()->find($this->parent_id);
            $this->parent = $parent;
        }

        // Получим все дочерние элементы текущего родителя и всех соседей текущего элемента
        if (isset($this->parent->id)) {
            $siblings = $this->parent->getNode()->getChildren();
            $children = $this->parent->getNode()->getAncestors();
        }
       
        // Проверяем корректность заполнения полей, используя ограничения,
        // установленные в метаданных этих полей
        foreach($this->data_tables1 as $type) {
            $datatype = $type;
            if (!isset($this->data[$datatype]))
                continue;
            foreach($this->data[$datatype] as $key=>$element) {
                if (!isset($element->value))
                    continue;
                if (!isset($this->metadata[$key]))
                        continue;
                $md = $this->metadata[$key];
                $value = $element->value;
                $type = $md->type;
                if (!isset($md->id)) {
                    $this->reportError("Системная ошибка: для поля '".$md->title."' не найдено метаданных !","save");
                    return 0;
                }

                // Если значение должно быть указано
                if ($md->must_set) {
                    if ($value=="") {
                        $this->reportError("Поле '".$md->title."' должно быть заполнено !");
                        return 0;
                    }
                }

                if ($md->min!="" and $md->min!="0") {
                    if ($type=="integer") {
                        if ($value=="")
                            $value = date("");
                        if ($value<$md->min) {
                            $this->reportError("Значение поля '".$md->title."' должно быть больше ".$md->min,"save");
                            return 0;
                        }
                    }
                    if ($type=="string") {
                        if (strlen($value)<$md->min) {
                            $this->reportError("Длина значения поля '".$md->title."' должно быть больше ".$md->min,"save");
                            return 0;
                        }
                    }
                }

                if ($md->max!="" and $md->max!="0" ) {
                    if ($type=="integer") {
                        if ($value>$md->max) {
                            $this->reportError("Значение поля '".$md->title."' должно быть меньше ".$md->max,"save");
                            return 0;
                        }
                    }
                    if ($type=="string") {
                        if (strlen($value)>$md->max) {
                            $this->reportError("Длина значения поля '".$md->title."' должно быть меньше ".$md->max,"save");
                            return 0;
                        }
                    }
                }

                // Если значение должно быть уникально в пределах всех элементов
                if ($md->must_unique==1) {
                    $q = Doctrine_Query::create()
                         ->from("WebItem"+$type+"DataRecord")
                         ->where("item_id != ? and value == ?",array($this->base_id,$this->value));
                    $result = $q->fetchOne();
                    if (isset($result->id)) {
                        $this->reportError("Значение поля '".$md->title."' не уникально !","save");
                        return 0;
                    }
                }

                //Если значение должно быть уникально в среди соседей
                $found = false;
                if ($md->must_unique==2) {
                    foreach ($siblings as $sible) {
                        if ($sible->id == $this->base_id)
                            continue;
                        foreach ($sible->data as $data) {
                            if ($data->metadata_id == $md->id and $data->value == $value) {
                                $found = true;
                                break;
                            }
                        }
                    }
                }
                if ($found) {
                        $this->reportError("Значение поля '".$md->title."' не уникально !","save");
                        return 0;
                }

                //Если значение должно быть уникально в пределах текущего родителя
                $found = false;
                if ($md->must_unique==3) {
                    foreach ($children as $child) {
                        if ($child->id == $this->base_id)
                            continue;
                        foreach ($child->data as $data) {
                            if ($data->metadata_id == $md->id and $data->value == $value) {
                                $found = true;
                                break;
                            }
                        }
                    }
                }

                if ($found) {
                        $this->reportError("Значение поля '".$md->title."' не уникально !","save");
                        return 0;
                }

                // Проверяем значение по регулярному выражению
                if ($md->check_regexp!="")
                    if (preg_match("/".$md->check_regexp."/",$value)==0) {
                        $this->reportError("Значение поля '".$md->title."' указано неверно!","save");
                    }
            }
        }

        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();

        // Все подготовительные работы закончены и теперь можно записывать

        // Если мы обновляем существующий элемент, то сначала его получим
        if ($this->record!="")
            $item = $this->record;
        else
            $item = new WebItemRecord();
        $item->name = $this->name;
        $item->title = $this->title;
        $item->author = $this->author;
        $item->icon = str_replace($app->skinPath,"",$this->icon);
        $item->item_icon = str_replace($app->skinPath,"",$this->item_icon);
        $item->class = $this->class;
        $item->item_class = $this->item_class;
        $item->template = $this->template_object;
        $item->item_template = $this->item_template;
        $item->admin_template = $this->admin_template;
        $item->admin_item_template = $this->admin_item_template;
        $item->admin_list_template = $this->admin_list_template;
        $item->sort_order = $this->sort_order;
        $item->is_public = $this->is_public;
        $item->display_fields = $this->display_fields;
        $item->tag = $this->tag;

        if (isset($this->parent->id)) {
            if ($this->sible_id!="") {
                $sible = $item->getTable()->find($this->sible_id);
                if (!$this->loaded)
                    $item->getNode()->insertAsPrevSiblingOf($sible);
                else
                    $item->getNode()->moveAsPrevSiblingOf($sible);
            }
            else {
                if ($this->parent_id != $this->loaded_parent_id || !$this->loaded) {
                    if (!$this->loaded)
                        $item->getNode()->insertAsLastChildOf($this->parent);
                    else
                        $item->getNode()->moveAsLastChildOf($this->parent);
                }
            }
        }
        $item->save();

        // записываем метаданные
        $root = new WebItemMetadataRecord();
        $root = $root->getTable()->find(1);
        $prev_group = $root;
        $added_metadata = array();
        if (count($this->changed_metadata)>0)
        {
            foreach ($this->changed_metadata as $value) {
                if (isset($this->metadata[$value["name"]]) and isset($value["id"]))
                    $this->metadata[$value["name"]]->id = $value["id"];
                $type_name = $this->reverse_metadata_types[$value["type"]];
                $this->metadata[$value["name"]]->name = $value["name"];
                $this->metadata[$value["name"]]->title = $value["title"];
                $this->metadata[$value["name"]]->type = $type_name;
                $this->metadata[$value["name"]]->default_value = $value["default_value"];
                $this->metadata[$value["name"]]->is_group = $value["is_group"];
                $this->metadata[$value["name"]]->must_set = $value["must_set"];
                $this->metadata[$value["name"]]->must_unique = $value["must_unique"];
                $this->metadata[$value["name"]]->check_regexp = $value["check_regexp"];
                $this->metadata[$value["name"]]->min = $value["min"];
                $this->metadata[$value["name"]]->max = $value["max"];
                $this->metadata[$value["name"]]->accuracy = $value["accuracy"];
                $this->metadata[$value["name"]]->where_query = $value["where_query"];
                $this->metadata[$value["name"]]->author = $this->author;
                $added_metadata[$value["name"]] = $value["name"];
            }

            foreach ($this->metadata as $value) {
                if (!isset($added_metadata[$value->name])) {
                    unset($this->metadata[$value->name]);
                    if (isset($item->metadata[$value->id]))
                        $item->metadata[$value->id]->delete();
                }
                else {
                    if (@$value->id=="" && @$value->name!="") {
                        $md = new WebItemMetadataRecord();
                    }
                    else if (@$value->name!="") {
                        $md = $item->metadata[$value->id];
                        $md->id = $value->id;
                    }
                    if ($value->name!="") {
                        $md->name = $value->name;
                        $md->title = $value->title;
                        $md->type = $value->type;
                        $md->default_value = $value->default_value;
                        $md->is_group = $value->is_group;
                        $md->must_set = $value->must_set;
                        $md->must_unique = $value->must_unique;
                        $md->check_regexp = $value->check_regexp;
                        $md->min = $value->min;
                        $md->max = $value->max;
                        $md->accuracy = $value->accuracy;
                        $md->where_query = $value->where_query;
                        $md->author = $value->author;

                        if (@$value->id=="") {
                            if ($value->is_group) {
                                $prev_group = $item;
                                $md->getNode()->insertAsLastChildOf($root);
                                $md->item_id = $item->id;
                                $md->save();
                            }
                            else {
                              $md->getNode()->insertAsLastChildOf($prev_group);
                              $md->item_id = $item->id;
                              $md->save();
                            }
                            $md->link('item',array($item["id"]));
                        }
                        else {
                            if ($value->is_group) {
                                $md->getNode()->moveAsLastChildOf($root);
                                $prev_group = $item;
                                $md->save();
                            }
                            else {
                                $md->getNode()->moveAsLastChildOf($prev_group);
                                $md->save();
                            }
                        }
                    }
                }
                $value->id = $md->id;
            }
        }

        // записываем данные
        foreach ($this->data_tables1 as $type) {
            $datatype = $type;
            $item_datatype = "data_".$datatype;
            if (!isset($this->data[$datatype]))
                continue;
            foreach ($this->data[$datatype] as $key=>$value) {
                if (!isset($this->metadata[$key]->id))
                   continue;
                if (!isset($item->metadata[$this->metadata[$key]->id]))
                    continue;
                if ($value->id=="") {
                    $class = "WebItem".ucfirst($datatype)."DataRecord";
                    $dt = new $class();
                    
                    $dt->value = str_replace("\'","'",urldecode($value->value));
                    $dt->author = $value->author;
                    $dt->item_id = $item->id;
                    if (!isset($this->metadata[$key]))
                            continue;
                    $dt->metadata_id = $item->metadata[$this->metadata[$key]->id]->id;
                    $dt->save();
                    $dt->link("metadata",array($item->metadata[$this->metadata[$key]->id]["id"]));
                    $dt->link("item",$item["id"]);
                }
                else {
                    $dt = $item->$item_datatype;
                    $dt[$value->id]->id = $value->id;
                    $dt[$value->id]->metadata_id = $value->metadata_id;
                    $dt[$value->id]->value = $value->value;
                }
            }
        }
        
        
        if ($this->name == "") {
            $this->name = "Item".$item->id;
            $item->name = $this->name;
            $item->save();
        }

        $this->base_id = $item->id;
        echo $this->base_id;
        $this->loaded = true;
        $this->loaded_parent = $this->parent;
        $this->loaded_parent_id = $this->parent_id;
        $this->loaded_name = $this->name;
    }

    function setDataField($name,$new_value) {        
        foreach ($this->data_tables1 as $type) {
            $datatype = $type;
            $item_datatype = "data_".$datatype;
            if (!isset($this->data[$datatype])) {               
                continue;
            }
            foreach ($this->data[$datatype] as $key=>$value) {
                if ($key==$name) {
                    $value->value = str_replace("\'","'",urldecode($new_value));
                    break;
                }
            }
        }
    }

    function remove() {
        global $Objects;
        $this->site->connect();
        $this->record->getNode()->delete();
        $Objects->remove($this->class."_".$this->module_id."_".$this->site->name."_".$this->base_id+"_".$this->parent_id);
    }

    function getChildren($all=false,$from=0,$count=0,$sort_order="") {
        $this->site->connect();
        $from_string = "WebItemRecord r";
        if (!$this->loaded)
            $this->load();
        if ($sort_order=="")
            $sort_order = $this->sort_order;
        if ($sort_order!="") {
            $order_arr = explode(" ",$sort_order);
            $order_field = $order_arr[0];
            if (count($order_arr)>1)
                $order_kind = " ".$order_arr[1];
            else
                $order_kind = "";
            $joins_count = 1;
            $joins = array();
            if (isset($this->record->$order_field)) {
                $order_string = $order_field.$order_kind;
            } else {
                foreach($this->data_tables as $datatype) {
                    $found = false;
                    if (!isset($this->data[$datatype]))
                        continue;
                    foreach (@$this->data[$datatype] as $key=>$value) {
                        if ($key==$order_field) {
                            $join1 = "r.data_".$datatype." d1 on r.id=d1.item_id";
                            $join2 = "r.metadata md on d1.metadata_id = md.id";
                            $where = "md.name='".$order_field."'";
                            $order_string = "d1.value".$order_kind;
                            $found= true;
                            break;
                        }
                    }
                    if ($found)
                        break;
                }                
            }
        }
        $q = Doctrine_Query::create();
        $q->useQueryCache();
        $q->useResultCache();
       if (isset($order_string)) {
            if ($count!=0) {
                $q->from($from_string);
                $q->offset($from);
                $q->limit($count);
            } else {
                $q->from($from_string);
            }
            if (isset($join1)) {
                $q->leftJoin($join1);
                $q->leftJoin($join2);
                $q->where($where);
                $q->orderBy($order_string);
            }
            else
                $q->orderBy($order_string);
            $treeObject = Doctrine::getTable("WebItemRecord")->getTree();
            $treeObject->setBaseQuery($q);
        } else {
            if ($count!=0) {
                $q->from($from_string);
                $q->offset($from);
                $q->limit($count);
                $treeObject = Doctrine::getTable("WebItemRecord")->getTree();
                $treeObject->setBaseQuery($q);
            } else {
                $q->from($from_string);
            }
        }
        $treeObject = Doctrine::getTable("WebItemRecord")->getTree();
        $treeObject->setBaseQuery($q);
       // echo $q->getSqlQuery();
        if (!$all) {
            return $this->record->getNode()->getChildren();            
        }
        else {
            return $this->record->getNode()->getDescendants();
        }
    }

    function getChildrenCount($all=false) {
        $this->site->connect();
        if (!$this->loaded)
            $this->load();
        if (!$all) {
            return $this->record->getNode()->getNumberChildren();
        }
        else {
            return $this->record->getNode()->getNumberDescendants();
        }
    }


    function getItems($from=0,$count=0,$sort_order="",$only_public=false) {
        $childs = $this->getChildren(0,$from,$count,$sort_order);
        $result = array();
        global $Objects;
        if ($childs!=FALSE) {
            foreach($childs as $value) {
                if ($this->module_id!="")
                        $obj= $Objects->get($value->class."_".$this->module_id."_".$this->site->name."_".$value->id."_".@$value->getNode()->getParent()->id);
                else
                        $obj = $Objects->get($value->class."_".$this->site->name."_".$value->id."_".@$value->getNode()->getParent()->id);
                if ($only_public) {
                    if (!$obj->loaded)
                       $obj->load();
                    if (!$obj->is_public)
                        continue;
                }
                $result[count($result)] = $obj;
            }
        }
        return $result;
    }

    function getPresentation() {
        if (!$this->loaded)
            $this->load();
        return $this->title;
    }

    function getId() {
        if (!$this->loaded)
           $this->load();
	if ($this->module_id!="")
	        return get_class($this)."_".$this->module_id."_".$this->site->name."_".$this->base_id."_".$this->parent_id;
	else
	        return get_class($this)."_".$this->site->name."_".$this->base_id."_".$this->parent_id;
    }

    function getArgs() {
        if (!$this->loaded) {
            $this->load();
        }
        $result = parent::getArgs();

        $result["{created_at}"] = "";
        $result["{updated_at}"] = "";
        $result["{object_id}"] = $this->id;
        
        $created_at = explode(" ",$this->created_at);
        $date_created_at = implode(".",array_reverse(explode("-",$created_at[0])));
        if (isset($created_at[1]))
            $result["{created_at}"] = $date_created_at." ".$created_at[1];
        $updated_at = explode(" ",$this->updated_at);
        $date_updated_at = implode(".",array_reverse(explode("-",$updated_at[0])));
        if (isset($updated_at[1]))
            $result["{updated_at}"] = $date_updated_at." ".$updated_at[1];

        $this->site->connect();
        if ($this->parent!="") {
            $this->parent->load();
            $result["{parent_title}"] = $this->parent->title;
            $result["{parent_class}"] = $this->parent->class;
        }

        global $Objects;

        if ($this->parent!="")
            if (isset($this->parent->getNode()->getParent()->id))
                $result["{parent_parent_id}"] = $this->parent->getNode()->getParent()->id;
            else
                $result["{parent_parent_id}"] = 1;
        $result["{site}"] = $this->site->name;
        $result["{id}"] = $this->base_id;
        $result["{item_id}"] = $this->id;
        $result["{objectid}"] = $this->getId();
        if ($this->module_id!="")
			$start = "ItemTemplate_".$this->module_id."_";
		else
			$start = "ItemTemplate_";
		
        $template = $Objects->get($start.$this->template_object);
        $template->load();
        $result["{template_title}"] = $template->title;
        $template = $Objects->get($start.$this->admin_template);
        $template->load();
        $result["{admin_template_title}"] = $template->title;
        $template = $Objects->get($start.$this->admin_item_template);
        $template->load();
        $result["{admin_item_template_title}"] = $template->title;
        $template = $Objects->get($start.$this->admin_list_template);
        $template->load();
        $result["{admin_list_template_title}"] = $template->title;
        $template = $Objects->get($start.$this->item_template);
        $template->load();
        $result["{item_template_title}"] = $template->title;
        $result["{metadata_types}"] = implode(",",array_keys($this->reverse_metadata_types));
        $result["{unique_types}"] = "0,1,2,3|Не должно быть уникальным,Уникально,Уникально среди соседей,Уникально в пределах подчинения";
        $result["{metadata_names}"] = array();
        $result["{metadata_titles}"] = array();
        $result["{metadata_ids}"] = array();
        $result["{metadata_values}"] = array();
        $result["{metadata_types}"] = array();
        $result["{metadata_presentations}"] = array();
        
        foreach ($this->metadata as $metadata) {
            $datatype = $metadata->type;
            if ($metadata->name=="")
                    continue;
            if ($metadata->type=="date") {
                    $value = array_shift(explode(" ",$this->data[$datatype][$metadata->name]->value));
                    $value = implode(".",array_reverse(explode("-",$value)));
            }
            else
                $value = @$this->data[$datatype][$metadata->name]->value;
                $result['{$'.$metadata->name.'}'] = $value;
            $result['{$'.$metadata->name.'_type}'] = $metadata->type;
            $result['{$'.$metadata->name.'_id}'] = @$this->data[$datatype][$metadata->name]->id;
            $result['[$'.$metadata->name.']'] = htmlentities(@$this->data[$this->data_tables[$datatype]][$metadata->name]->value,ENT_QUOTES,"UTF-8");
            $result['{metadata_names}'][count($result["{metadata_names}"])] = $metadata->name;
            $result["{metadata_titles}"][count($result["{metadata_titles}"])] = $metadata->title;
            $result['{metadata_ids}'][count($result["{metadata_ids}"])] = $metadata->id;
            $result["{metadata_values}"][count($result["{metadata_values}"])] = str_replace("\n","/#n",htmlentities(@$this->data[$this->data_tables[$datatype]][$metadata->name]->value,ENT_QUOTES,"UTF-8"));
            $result["{metadata_types}"][count($result["{metadata_types}"])] = $metadata->type;
            if ($metadata->type == "item") {
                if (@$this->data[$this->data_tables[$datatype]][$metadata->name]->value!="") {
                    $it_arr = explode("_",$this->data[$this->data_tables[$datatype]][$metadata->name]->value);
                    $it_class = array_shift($it_arr);
                    if ($this->module_id!="")
                        $it = $it_class."_".$this->module_id."_".implode("_",$it_arr);
                    else
                        $it = $it_class."_".implode("_",$it_arr);
                    $it = $Objects->get($it);
                    $result["{metadata_presentations}"][count($result["{metadata_presentations}"])] = $it->presentation;
                }
                else
                    $result["{metadata_presentations}"][count($result["{metadata_presentations}"])] = "";
            }
            else
                $result["{metadata_presentations}"][count($result["{metadata_presentations}"])] = "";
        }

        $result["{metadata_names}"] = implode("'",$result["{metadata_names}"]);
        $result["{metadata_titles}"] = implode("'",$result["{metadata_titles}"]);
        $result["{metadata_ids}"] = implode("'",$result["{metadata_ids}"]);
        $result["{metadata_values}"] = implode("'",$result["{metadata_values}"]);
        $result["{metadata_types}"] = implode("'",$result["{metadata_types}"]);
        $result["{metadata_presentations}"] = implode("'",$result["{metadata_presentations}"]);

        $result["{field_names}"] = array();
        foreach($this->data_tables1 as $value) {
            $datatype = $value;
            if (!isset($this->data[$datatype]))
                continue;
            $result["{field_names}"][count($result["{field_names}"])] = implode(",",array_keys($this->data[$datatype]));
        }
        $result["{field_names}"] = implode(",",$result["{field_names}"]);
        $result["{control_question}"] = rand(1,10)."+".rand(1,10);
        $result["[control_question]"] = $result["{control_question}"];

        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();
        $result["{skin_path}"] = $app->skinPath;
        return $result;
    }

    function postResult($title,$fields) {
        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();

        if (!$this->loaded)
            $this->load();
        if ($this->module_id!="")
			$new_result = $Objects->get($this->class."_".$this->module_id."_".$this->site->name."__".$this->base_id);
		else
			$new_result = $Objects->get($this->class."_".$this->site->name."__".$this->base_id);
        $fields = explode("|",$fields);
        $values = array();
        foreach($fields as $field) {
            $field_name_value = explode("#",$field);
            $new_result->setDataField(strip_tags($field_name_value[0]),strip_tags($field_name_value[1]));
            $values[count($values)] = strip_tags($field_name_value[1]);
        }
        $new_result->title = $title;
        $new_result->icon = $app->skinPath."images/Tree/item.gif";
        $new_result->item_icon = $app->skinPath."images/Tree/item.gif";
        $new_result->is_public = 1;
        $new_result->save();
    }
}
?>
