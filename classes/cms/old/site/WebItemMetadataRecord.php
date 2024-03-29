<?php
/**
 * Этот класс управляет записью метаданных метаданных поля элемента Web-сайта.
 * Метаданные поля это свойства этого поля. Метаданные всех полей хранятся в
 * таблице metadata. Для поля определены следующие свойства:
 *
 * id - уникальный идентификатор поля
 * name - внутреннее системное имя поля
 * title - заголовок поля
 * type - тип поля:
 *        int - число
 *        boolean - логическое значение (правда/ложь)
 *        string - строка
 *        date - дата и время
 *        image - изображение
 *        file - файл
 *        images - коллекция изображений
 *        files - коллекция файлов
 *        item - элемент
 *        clob - многострочный текст
 *        blob - двоичные данные
 * default_value - значение по умолчанию
 * 
 * is_group - является ли данная строка группой
 * must_unique - должно ли быть значение этого поля уникальным. Может принимать
 *             значения: 0, 1, 2 и 3.
 *             0 - нет
 *             1 - должно быть уникально в пределах всех элементов
 *             2 - должно быть уникально в пределах всех соседей данного элемента
 *             3 - должно быть уникально в пределах всех соседей и всех подчиненных
 *                 им элементов
 * must_set - должно ли значение быть установлено (true/false)
 * check_regexp - регулярное выражение, проверяющее правильность введенного значения
 *                для данного поля
 * min - минимальное значение
 * max - максимальное значение
 * accuracy - точность (количество знаков после запятой)
 * where_query - часть текста запроса, отбирающего элементы для выбора элемента,
 *               который может быть установлен в поле типа item
 * author - создатель этой записи (пользователь, который вошел в Web-интерфейс)
 * tag - произвольные данные, связанные с этим полем
 * item_id - идентификатор элемента WebItem, которому принадлежит эта запись
 *
 * Записи метаданных могут быть иерархическими. Для этого в таблицу добавляются
 * свойства lfg, rgt и level. Level определяет уровень вложенности. Поле is_group
 * определяет, выполняет ли данная запись исключительно функцию группировки. Если
 * флаг is_group=true, то данная запись не содержит никаких данных кроме title и
 * name.
 *
 * Для всех полей, независимо от типа могут быть установлены свойства must_unique
 * и must_set, означающие соответственно, должно ли значение быть уникальным и
 * должно ли оно обязательно быть установленным. Также может быть установлено
 * свойство check_regexp, определяющее регулярное выражение, которое будет использоваться
 * для проверки правильности заполения данного поля.
 *
 * В зависимости от типа, к значениям поля могут применяться и другие ограничения.
 * Если тип равен string, то свойство min определяет минимальное количество символов,
 * а свойство max - максимальное.
 * Для чисел это соответственно минимальное и максимальное значения.
 * Для дат это соответственно минимальная и максимальная дата.
 *
 * Свойство accuracy это количество знаков после запятой, которые разрешено указывать
 * для данного числа.
 *
 * Для файлов и изображений применяется единственное условие: они должны реально
 * существовать.
 *
 *
 */
class WebItemMetadataRecord extends Doctrine_Record {

    function setTableDefinition() {
        parent::setTableDefinition();
        $this->setTableName("metadata");
        $this->hasColumn("name",'string',100);
        $this->hasColumn("title",'string',100);
        $this->hasColumn("type",'string',20);
        $this->hasColumn("default_value",'string',255);
        $this->hasColumn("is_group",'boolean');
        $this->hasColumn("must_unique",'integer');
        $this->hasColumn("must_set",'boolean');
        $this->hasColumn("check_regexp",'string',255);
        $this->hasColumn("min",'integer');
        $this->hasColumn("max",'integer');
        $this->hasColumn("accuracy",'integer');
        $this->hasColumn("where_query",'string',255);
        $this->hasColumn("author",'string',50);
        $this->hasColumn("tag",'string',255);
        $this->hasColumn("item_id",'integer');
    }

    public function setUp() {

        $this->hasOne("WebItemRecord as item",array(
            'local' => "item_id",
            'foreign' => "id",
            'onDelete' => "CASCADE"
            )
        );

        $this->hasMany("WebItemDataRecord as data",array(
            'local' => "id",
            'foreign' => "metadata_id",
            'cascade' => array("delete")
            )
        );

        $this->actAs('Searchable', array('fields'=>array('title','name')));
        $this->actAs('NestedSet');
        $this->actAs('Timestampable');
    }
    
}
?>