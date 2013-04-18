<?php
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.model');

class FlippingBookModelBook extends JModel {

	function getBook( &$options ) {
		$db =& JFactory::getDBO();
		$id = @$options['id'];
		$query = 'SELECT c.*, ' .
				' CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(\':\', c.id, c.alias) ELSE c.id END as slug '.
				' FROM #__flippingbook_books AS c' .
				' WHERE c.id = '. intval ( $id );
		$result = $this->_getList( $query );
		
		$query = 'SELECT c.*, ' .
			' CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(\':\', c.id, c.alias) ELSE c.id END as catslug '.
			' FROM #__flippingbook_categories AS c' .
			' WHERE c.id = '. intval ( $result[0]->category_id );
		$category_result = $this->_getList( $query );
		$result[0]->category = $category_result[0]->title;
		$result[0]->categoryId = $category_result[0]->id;	
		return $result[0];
	}
	
	function countPages ( &$options ) {
		$db =& JFactory::getDBO();
		$id = @$options['id'];
		$db->setQuery( "SELECT COUNT(*) FROM #__flippingbook_pages WHERE book_id = " . intval ( $id ) . " AND published = 1" );
		$total_pages = $db->loadResult();
		return $total_pages;
	}
	
	function loadGlobalVars () {
		
		if ( defined( "FB_categoryListTitle" ) ) return; 

		$db	=& JFactory::getDBO();
		$query = "SELECT name, value FROM #__flippingbook_config";
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		foreach ( $rows as $row ) {
			eval ( "DEFINE('FB_" . $row->name . "', '" . $row->value . "');" );
		}
	}
}
?>