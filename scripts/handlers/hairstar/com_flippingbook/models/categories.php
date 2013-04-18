<?php
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.model');

class FlippingBookModelCategories extends JModel {
	
	function getCategories( ) {
		$db =& JFactory::getDBO();
		$query = 'SELECT c.*, ' .
				' CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(\':\', c.id, c.alias) ELSE c.id END as slug '.
				' FROM #__flippingbook_categories AS c' .
				' WHERE c.published = 1' .
				' ORDER BY c.ordering';
		$result = $this->_getList( $query );
		return @$result;
	}
	
	function loadGlobalVars () {
		
		if (defined("_FB_ITEMID")) return; 
		DEFINE("_FB_ITEMID", JRequest::getVar( 'Itemid', '', 'get', 'int'));
		$db	=& JFactory::getDBO();
		$query = "SELECT name, value FROM #__flippingbook_config";
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		foreach ( $rows as $row ) {
			$row->value = urldecode ( $row->value );
			eval ('DEFINE("FB_' . $row->name . '", "' . $row->value . '");');
		}
	}
}
?>