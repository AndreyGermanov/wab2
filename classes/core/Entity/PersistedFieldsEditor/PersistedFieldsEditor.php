<?php
/**
 * Класс представляет из себя редактор свойств поля сущности.
 * Он вызывается в основном при нажатии на кнопку элемента InputControl типа
 * 'hidden'. Ему передается ссылка на этот InputControl из которого он получает
 * его текущее значение и в зависимости от этого значения заполняет поля.
 *
 * В зависимости от типа сущности, указанного в колонке диалог отображает разные
 * поля.
 *
 * После заполнения и нажатия кнопки OK, из всех заполненных данных генерируется
 * в виде строки значение поля и передается в элемент управления, дальше генерируется
 * событие CONTROL_VALUE_CHANGED,  в результате которого элемент обновляется
 * и любые заинтересованные слушатели могут отреагировать по своему на изменение
 * значения этого свойства.
 *
 * @author andrey
 */
class PersistedFieldsEditor extends WABEntity {

    function construct($params) {
        if (count($params)>2) {
            $this->module_id = array_shift($params)."_".array_shift($params);
        }
        $this->name = implode("_",$params);

        $this->control = "";
        $this->template = "templates/PersistedFieldsEditor/PersistedFieldsEditor.html";

        global $Objects;
        $app = $Objects->get("Application");
        if (!$app->initiated)
            $app->initModules();

        $this->skinPath = $app->skinPath;
        $this->css = $this->skinPath."styles/Mailbox.css";
        $this->handler = "scripts/handlers/PersistedFieldsEditor/PersistedFieldsEditor.js";
        $this->width = 400;
        $this->height = 400;
        $this->overrided = "width,height";
        $this->parent_object_id = "";
        $this->control_id = "";
        $this->editorType = "";
        $this->value = "";
        $this->entity_id = "";
        $this->fieldName = "";
        $this->icon = $this->skinPath."images/Tree/file.png";
    }

    function getId() {
        if ($this->module_id!="")
                return get_class()."_".$this->module_id."_".$this->name;
        else
                return get_class()."_".$this->name;
    }

    function getPresentation() {
        global $Objects;
        $title = @$Objects->get($this->entity_id)->presentation;
        return "Настройка свойств поля ".$this->fieldName." объекта ".$title;
    }
}
?>
