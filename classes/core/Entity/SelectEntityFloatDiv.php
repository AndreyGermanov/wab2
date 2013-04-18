<?php
/**
 * Класс, отображающий всплывающее меню для выбора сущности. Во всплывающем
 * меню находится innerFrame, в котором отображается дерево сущностей, доступных
 * для выбора. Дерево это объект класса EntityTree, которое строится с помощью
 * параметров className и condition. Свойство editorType этого дерева равно 'none',
 * то есть просто генерирует событие NODE_CLICKED, которое перехватывает этот
 * объект и заполняет значение элемента управления, который открыл это всплывающее
 * меню.
 * 
 * Также в entityTree передается параметр entityId, который определяет сущность,
 * которая выбрана в данный момент. Это значение берется из свойства value элемента
 * управления, который открывает это всплывающее меню. Дерево автоматически
 * разворачивается и делает активным элемент c этим id.
 *
 * @author andrey
 */
class SelectEntityFloatDiv extends ContextMenu{
    
    function construct($params) {
        parent::construct($params);
        
        $this->entityId = "";
        $this->className = "";
        $this->condition = "";
        $this->childCondition = "";
        $this->parent_object_id = "";
        $this->editorType = "";
        $this->divName = "";
        $this->windowWidth = "";
        $this->windowHeight = "";
        $this->windowTitle = "";
        $this->destroyDiv = "";
        $this->selectGroup = "1";
        $this->additionalFields="";
        $this->handler = "scripts/handlers/core/SelectEntityFloatDiv.js";    
        $this->clientClass = "SelectEntityFloatDiv";
        $this->parentClientClasses = "ContextMenu~Entity";        
    }
    
    function getArgs() {
        $result = parent::getArgs();
        $result["{className}"] = $this->className;
        return $result;
    }
}
?>