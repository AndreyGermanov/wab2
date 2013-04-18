<?php
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.view');

class FlippingBookViewBook extends JView {
	function display($tpl = null) {
		global $mainframe;

		$user = &JFactory::getUser();
		$pathway = &$mainframe->getPathway();
		$document = & JFactory::getDocument();
		$model = &$this->getModel(  );

		
		$menus = &JSite::getMenu();
		$menu = $menus->getActive();

		$pparams = &$mainframe->getParams('com_flippingbook');

		$bookId = intval(JRequest::getVar( 'id', 0, 'get', 'int'));

		$options['id']	= $bookId;
		$book = $model->getBook( $options );
		$total_pages = $model->countPages( $options );
		
		$document->setTitle($book->title);

		
		
			
		
		if($menu && $menu->query['view'] != 'book') {
			switch ($menu->query['view'])
			{
				case 'categories':
					$pathway->addItem($book->category, 'index.php?view=category&id='.$book->categoryId);
					$pathway->addItem($book->title, '');
					break;
				case 'category':
					$pathway->addItem($book->title, '');
					break;
			}
		}
		
		
		
		$this->assignRef('book', $book);
		$this->assignRef('params', $pparams);
		$this->assignRef('total_pages', $total_pages);
		
		echo $this->showBook($bookId, 'component');
	}
	
	function showBook ($book_id, $output_type) {
		$db	=& JFactory::getDBO();
		$model = &$this->getModel();
		$model->loadGlobalVars();
		global $mainframe;

		$templ = JRequest::getVar( 'templ', 0, 'get', 'str' );
		if ( ($this->params->get( 'show_page_title' )) && ($output_type == 'component') && ($templ == 0) ) { ;echo '<div class="componentheading'; echo $this->params->get( 'pageclass_sfx' ); ;echo '">
	'; echo $this->params->get( 'page_title' ); ;echo '</div>
'; }
		
		
		$db->setQuery("SELECT * FROM #__flippingbook_books WHERE id=" . intval ( $book_id ) . " ORDER BY ordering");
		$rows = $db->loadObjectList();
		if ( count($rows) == 0 )
			return '<div class="fb_errorMessage">The requested book is not exists.</div>' . "\n";
		if ( $rows[0]->published != 1 )
			return '<div class="fb_errorMessage">The requested book have been unpublished.</div>' . "\n";
		$book_name = $rows[0]->title;
		$emailIcon = $rows[0]->emailIcon;
		$printIcon = $rows[0]->printIcon;
		
		$db->setQuery("SELECT COUNT(*) FROM #__flippingbook_pages WHERE book_id = " . intval ( $book_id ) . " AND published = 1");
		$total_pages = $db->loadResult();

		
		$mainframe->setPageTitle( $book_name );

		
		$output_html = "\n<!-- FlippingBook Gallery Component -->\n";

		
		if ( ($output_type == 'component') && ($rows[0]->show_book_title == 1) && (($emailIcon == 1) || ($printIcon == 1) || ($book_name != '')) ) {
			$output_html .= '<table class="contentpaneopen">' . "\n";
			$output_html .= '<tr>' . "\n";
			if ( $book_name != '' ) {
				$output_html .= '<td class="contentheading" width="100%">' . "\n";
				$output_html .= $book_name;
				$output_html .= '</td>' . "\n";
			}
			if ( JRequest::getVar( 'print', '', 'get', 'int') != 1 ) {
				if ( $printIcon == 1 ) {
					$output_html .= '<td align="right" width="100%" class="buttonheading">' . "\n";
					$output_html .= '<a href="index.php?option=com_flippingbook&amp;view=book&amp;id=' . $book_id . '&amp;print=1" title="' . JText::_( 'Print' ) . '" onclick="window.open(this.href,\'win2\',\'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no\'); return false;"><img src="images/M_images/printButton.png" alt="Print" align="top" border="0" /></a>';
					$output_html .= '</td>' . "\n";
				}
				if ( $emailIcon == 1 ) {
					$output_html .= '<td align="right" width="100%" class="buttonheading">' . "\n";
					$link = JURI::root() . 'index.php?option=com_flippingbook&amp;view=book&amp;id=' . $book_id;
					$url	= 'index.php?option=com_mailto&amp;tmpl=component&amp;link=' . base64_encode( $link );
					$status = 'width=400,height=350,menubar=yes,resizable=yes';
					$text = JHTML::_('image.site', 'emailButton.png', '/images/M_images/', NULL, NULL, JText::_( 'Email' ), 'border="0"');
					$attribs = array();
					$attribs['title']	= JText::_( 'Email' );
					$attribs['onclick'] = "window.open(this.href,'win2','" . $status . "'); return false;";
					$output_html .= JHTML::_( 'link', JRoute::_($url), $text, $attribs );
					$output_html .= '</td>';
				}
			} else {
				$output_html .= '<td>' . "\n";
				$text = JHTML::_('image.site',  'printButton.png', '/images/M_images/', NULL, NULL, JText::_( 'Print' ), JText::_( 'Print' ) );
				$output_html .= '<a title="Print" href="#" onclick="window.print();return false;">' . $text . '</a>';
				$output_html .= '</td>' . "\n";
			}
			$output_html .= '</tr>' . "\n";
			$output_html .= '</table>' . "\n";
		}
		
		echo $output_html;
		$this->assignRef( 'book_id', $book_id );
		
		parent::display();
	}
}
?>