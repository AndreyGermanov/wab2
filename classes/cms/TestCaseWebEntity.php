<?php
/**
 * Класс реализует работу с тестом
 *
 * 
 * Тест представляет из себя набор вопросов и ответов и вариантов ответа на них.
 * Вопрос представляет из себя сущность класса TestQuestionWebEntity. Вопросы
 * должны быть дочерними элементами сущности, указанной в параметре TestQuestions. 
 * Каждый вопрос содержит варианты ответа на него и механизм расчета количества 
 * баллов, которые дает каждый выбранный вариант ответа. 
 * 
 * Тест может иметь результаты. Возможные варианты результатов представлены в
 * виде таблицы:
 * 
 * <Диапазон баллов> - <Результат> - <Текстовое описание результата>
 * .
 * .
 * .
 * 
 * Тест содержит механизм формирования количества вопросов, которые будут 
 * задаваться. Возможны варианты:
 * 
 * 1. Все вопросы
 * 2. Указанное количество произвольных вопросов
 * 
 * Также тест должен принимать параметры о сдающем: его имя и другие данные,
 * список полей, которые относятся к этому указываются в поле studentData в 
 * формате:
 * 
 * Поле~Поле~Поле
 * 
 * Информация о результатах теста по каждому человеку записываются в виде сущностей
 * класса TestResultWebEntity, которые должны быть внутри раздела, который указывается
 * в поле TestResults.
 * 
 * Этот класс должен получать результаты ответа на каждый вопрос в виде массива
 * или многострочного текста:
 * 
 * ID-вопроса|ID-ответа~Id-ответа или string~<строка-ответа>|<Количество баллов>
 * 
 * затем на основании таблицы возможных результатов вычислять результат для данного
 * сдающего и на основании всех этих данных создавать элемент TestResultWebEntity
 * и передавать в него все данные:
 * 
 * - поля информации о сдающем (studentData)
 * - Полные результаты сдачи (в виде поля Text)
 * - Вычисленный результат (в поле String)
 *
 * После прохождения теста и записи результата, этот элемент TestResultWebEntity
 * должен отображаться по шаблону, который в нем указан (берется из родительского
 * контейнера).
 * 
 * 
 * @author andrey
 */
class TestCaseWebEntity extends WebEntity {	
	function construct($params) {
		parent::construct($params);
		$this->clientClass = "TestCaseWebEntity";
		$this->parentClientClasses = "WebEntity~Entity";		
	}
}
?>