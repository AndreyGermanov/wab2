<?php
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.controller');

class FlippingBookController extends JController {
	function display() {
		if ( ! JRequest::getCmd( 'view' ) ) {
			JRequest::setVar('view', 'categories' );
		}

		if (JRequest::getCmd('view') == 'category') {
			$model =& $this->getModel('category');
		}
		
		if (JRequest::getCmd('view') == 'book') {
			$model =& $this->getModel('book');
		}
		
		parent::display();
	}
}
?>