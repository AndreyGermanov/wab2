<?php
/* 
 * Класс представляет из себя запись элемента сайта в таблице базы данных.
 * Через этот класс происходит связь между WAB и базой данных с помощью Doctrine.
 *
 * Запись имеет следующие поля:
 *
 * id - уникальный идентификатор элемента
 * name - уникальное системное имя элемента
 * title - произвольный текстовый заголовок элемента
 * icon - иконка для элемента, появляющаяся в Web-интерфейсе
 * item_icon - иконка для дочерних элементов, появляющаяся в Web-интерфейсе
 * class - класс PHP, который отвечает за управление элементом. Класс является потомком
 *         стандартного класса WebItem.
 * template - шаблон по умолчанию для данного элемента и шаблон по умолчанию для его потомков, если
 *            потомок создается командой "Новый раздел".
 * item_template - шаблон по умолчанию для потомков-элементов, т.е. если элемент был создан
 *                 командой "Новый элемент".
 * admin_template - шаблон по умолчанию для этого элемента и его потомков, появляющийся в интерфейсе
 *                  администрирования, если для создания потомка используется команда "Новый раздел"
 * admin_item_template - шаблон по умолчанию для потомков данного элемента, появляющийся в интерфейсе
 *                       администрирования, если для создания потомка используется команда
 *                       "Новый элемент".
 * admin_list_template - шаблон по умолчанию для элемента и потомков данного элемента для отображения
 *                       списка дочерних элементов в интерфейсе администрирования.
 * tag - поле для произвольных данных об элементе
 * author - пользователь, который создал раздел (это пользователь, от имени которого произведен
 *          вход в режим администрирования.
 * is_public - опубликован ли раздел на сайте
 * sort_order - порядок сортировки
 * display_fields - отображаемые поля в списке дочерних элементов
 *
 * Как уже наверное стало понятно, элементы могут быть вложены друг в друга и вложенность их
 * безгранична. Для создания этой вложенности используется модель NestedSet из Doctrine.
 * Для работы этой модели, Doctrine добавляет поля lft, rgt и level. По полю level можно узнать
 * уровень вложенности каждого элемента.
 *
 * Технически все элементы равносильны, однако для конечного пользователя часть элементов может
 * быть представлено в виде разделов, а часть в виде элементов. Элемент это обычно элемент, у
 * которого нет дочерних.
 *
 * Каждый элемент может иметь набор полей, каждое поле может иметь определенный тип. Описание полей,
 * которые может иметь элемент находится в связанной таблице metadata, запись которой управляется
 * классом WebItemMetadataRecord.
 *
 * Значения полей хранятся в таблицах data_int, data_string, data_date, data_clob и data_blob,
 * в зависимости от типа данных. Управляются эти таблицы соответственно классами WebItemDataRecord,
 * WebItemIntDataRecord, WebItemBooleanDataRecord,WebItemStringDataRecord, WebItemDateDataRecord,
 * WebItemСlobDataRecord и WebItemBlobDataRecord.
 * 
 */

/**
 * Description of WebItemRecord
 *
 * @author andrey
 */
class WebItemRecord extends Doctrine_Record {

    function setTableDefinition() {
        parent::setTableDefinition();
        $this->setTableName("items");
        $this->hasColumn("name",'string',100);
        $this->hasColumn("title",'string',255);
        $this->hasColumn("icon",'string',100);
        $this->hasColumn("item_icon",'string',100);
        $this->hasColumn("class",'string',30);
        $this->hasColumn("item_class",'string',30);
        $this->hasColumn("template",'integer');
        $this->hasColumn("item_template",'integer');
        $this->hasColumn("admin_template",'integer');
        $this->hasColumn("admin_item_template",'integer');
        $this->hasColumn("admin_list_template",'integer');
        $this->hasColumn("author",'string',50);
        $this->hasColumn("is_public",'boolean');
        $this->hasColumn("sort_order",'string',100);
        $this->hasColumn("display_fields",'string',255);
        $this->hasColumn("tag",'string',255);
    }

    public function setUp() {

        $this->hasMany("WebItemMetadataRecord as metadata",array(
            'local' => "id",
            'foreign' => "item_id",
            'cascade' => array("delete")
            )
        );

        $this->hasMany("WebItemIntegerDataRecord as data_integer",array(
            'local' => "id",
            'foreign' => "item_id",
            'cascade' => array("delete")
            )
        );

        $this->hasMany("WebItemStringDataRecord as data_string",array(
            'local' => "id",
            'foreign' => "item_id",
            'cascade' => array("delete")
            )
        );

        $this->hasMany("WebItemBooleanDataRecord as data_boolean",array(
            'local' => "id",
            'foreign' => "item_id",
            'cascade' => array("delete")
            )
        );

        $this->hasMany("WebItemClobDataRecord as data_clob",array(
            'local' => "id",
            'foreign' => "item_id",
            'cascade' => array("delete")
            )
        );

        $this->hasMany("WebItemBlobDataRecord as data_blob",array(
            'local' => "id",
            'foreign' => "item_id",
            'cascade' => array("delete")
            )
        );

        $this->hasMany("WebItemDateDataRecord as data_date",array(
            'local' => "id",
            'foreign' => "item_id",
            'cascade' => array("delete")
            )
        );

        $this->hasMany("WebItemImageDataRecord as data_image",array(
            'local' => "id",
            'foreign' => "item_id",
            'cascade' => array("delete")
            )
        );

        $this->hasMany("WebItemImagesDataRecord as data_images",array(
            'local' => "id",
            'foreign' => "item_id",
            'cascade' => array("delete")
            )
        );

        $this->hasMany("WebItemFileDataRecord as data_file",array(
            'local' => "id",
            'foreign' => "item_id",
            'cascade' => array("delete")
            )
        );

        $this->hasMany("WebItemFilesDataRecord as data_files",array(
            'local' => "id",
            'foreign' => "item_id",
            'cascade' => array("delete")
            )
        );

        $this->hasMany("WebItemItemDataRecord as data_item",array(
            'local' => "id",
            'foreign' => "item_id",
            'cascade' => array("delete")
            )
        );

        $this->actAs('Searchable', array('fields'=>array('title')));
        $this->actAs('NestedSet');
        $this->actAs('Timestampable');
    }
}
?>
