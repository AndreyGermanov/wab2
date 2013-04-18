<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view' );

class FlippingBookViewCategory extends JView {
	function display($tpl = null) {
		global $mainframe;

		$user = &JFactory::getUser();
		$pathway = &$mainframe->getPathway();
		$document = & JFactory::getDocument();
		$model = &$this->getModel();

		
		$menus = &JSite::getMenu();
		$menu = $menus->getActive();

		$pparams = &$mainframe->getParams( 'com_flippingbook' );

		$categoryId = intval( JRequest::getVar( 'id', 0, 'get', 'int' ) );

		$options['id']	= $categoryId;
		$category = $model->getCategory( $options );

		$document->setTitle( $category->title );
		
		
		if (isset( $menu ) && isset($menu->query['view']) && $menu->query['view'] != 'category')
			$pathway->addItem( $category->title );

		$this->assignRef( 'category', $category );
		$this->assignRef( 'params', $pparams );

		$this->Show_Category( $categoryId, 'component', $category );
	}
	
	function Show_Category ( $category_id, $output_type, $category ) {
		$model = &$this->getModel();
		$model->loadGlobalVars();
		global $mainframe;
		
		if ( ( $this->params->get( 'show_page_title' ) ) && ( $output_type == 'component' ) ) { ;echo '			<div class="componentheading'; echo $this->params->get( 'pageclass_sfx' ); ;echo '">
					'; echo $this->params->get( 'page_title' ); ;echo '			</div>
		'; }

		
		if ( !$category->id ) {
			return '<div style="color:#dd0000; background-color:#f5f5f5; padding:3px;"><strong>FlippingBook:</strong>The requested category was not found.</div>' . "\n";
		}
		$emailIcon = $category->emailIcon;
		$printIcon = $category->printIcon;
		$categoryTitle = $category->title;
		$show_title = $category->show_title;

		
		if ( $output_type == 'component' ) {
			$mainframe->setPageTitle( $categoryTitle );
		}

		
		$document=& JFactory::getDocument();
		$css_tag='<link rel="stylesheet" href="' . JURI::base( true ) . '/components/com_flippingbook/css/' . FB_theme . '" type="text/css" />';
		$document->addCustomTag( $css_tag );

		
		$output_html =  "\n<!-- FlippingBook Gallery Component -->\n";

		
		if ( ( $output_type == 'component' ) && ( $show_title == 1 ) && ( ( $emailIcon == 1 ) || ( $printIcon == 1 ) || ( $categoryTitle != '' ) ) ) {
			$output_html .= '<table class="contentpaneopen">' . "\n";
			$output_html .= '<tr>' . "\n";
			if ( $categoryTitle != '' ) {
				$output_html .= '<td class="contentheading" width="100%">' . "\n";
				$output_html .= $categoryTitle;
				$output_html .= '</td>' . "\n";
			}
			if ( JRequest::getVar( 'print', '', 'get', 'int') != 1 ) {
				if ( $printIcon == 1 ) {
					$output_html .= '<td align="right" width="100%" class="buttonheading">' . "\n";
					$output_html .= '<a href="index.php?option=com_flippingbook&amp;view=category&amp;id=' . $category_id . '&amp;print=1" title="' . JText::_( 'Print' ) . '" onclick="window.open(this.href,\'win2\',\'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no\'); return false;"><img src="images/M_images/printButton.png" alt="Print" align="top" border="0" /></a>';
					$output_html .= '</td>' . "\n";
				}
				if ( $emailIcon == 1 ) {
					$output_html .= '<td align="right" width="100%" class="buttonheading">' . "\n";
					$link = JURI::root() . 'index.php?option=com_flippingbook&amp;view=category&amp;id=' . $category_id;
					$url	= 'index.php?option=com_mailto&amp;tmpl=component&amp;link=' . base64_encode( $link );
					$status = 'width=400,height=350,menubar=yes,resizable=yes';
					$text = JHTML::_('image.site', 'emailButton.png', '/images/M_images/', NULL, NULL, JText::_( 'Email' ), JText::_( 'Email' ));
					$attribs = array();
					$attribs['title']	= JText::_( 'Email' );
					$attribs['onclick'] = "window.open(this.href,'win2','" . $status . "'); return false;";
					$output_html .= JHTML::_('link', JRoute::_( $url ), $text, $attribs);
					$output_html .= '</td>' . "\n";
				}
			} else {
				$output_html .= '<td>' . "\n";
				$text = JHTML::_( 'image.site',  'printButton.png', '/images/M_images/', NULL, NULL, JText::_( 'Print' ), JText::_( 'Print' ) );
				$output_html .= '<a href="#" onclick="window.print();return false;">' . $text . '</a>' . "\n";
				$output_html .= '</td>' . "\n";
			}
			$output_html .= '</tr>' . "\n";
			$output_html .= '</table>' . "\n";
		}
		echo $output_html;

		$options['id']	= $category_id;
		$books = $model->getBooks( $options );

		$this->assignRef( 'books', $books );

		parent::display();
	}
}
?>