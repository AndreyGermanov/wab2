<?php
defined('_JEXEC') or die('Restricted access');

	
	$book_id = intval(JRequest::getVar( 'id', 0, 'get', 'int'));
	if ( isset($book_id_for_module) ) $book_id = $book_id_for_module;

	
	$db	=& JFactory::getDBO();
	$db->setQuery("SELECT * FROM #__flippingbook_books WHERE id=" . intval ( $book_id ));
	$bookRow = $db->loadObjectList();
	$bookParams = $bookRow[0];

	if (count($bookParams) == 0)
		echo '<div class="fb_errorMessage">FlippingBook: The requested book was not found.</div>' . "\n";
	else { 
	
		
		
		
		
		$db->setQuery("UPDATE #__flippingbook_books SET hits=(hits+1) WHERE id=" . intval ( $book_id ) );
		$db->query();

		
		
		$document=& JFactory::getDocument();
		$headerTad='<link rel="stylesheet" href="' . JURI::base(true) . '/components/com_flippingbook/css/' . FB_theme . '" type="text/css" />';
		$document->addCustomTag($headerTad); 

		
		
		$headerTad='<script type="text/javascript" src="' . JURI::base(true) . '/components/com_flippingbook/js/swfobject.js"></script>';
		$document->addCustomTag($headerTad); 
		$headerTad='<script type="text/javascript" src="' . JURI::base(true) . '/components/com_flippingbook/js/flippingbook.js"></script>';
		$document->addCustomTag($headerTad);
		$headerTad='<script type="text/javascript" src="' . JURI::base(true) . '/components/com_flippingbook/js/jquery-1.3.1.min.js"></script>';
		$document->addCustomTag($headerTad);
		if ($bookParams->zooming_method == 1) {
			$headerTad='<script type="text/javascript" src="' . JURI::base(true) . '/components/com_flippingbook/js/ajax-zoom.js"></script>';
			$document->addCustomTag($headerTad); 
			$headerTad='<link rel="stylesheet" href="' . JURI::base(true) . '/components/com_flippingbook/js/ajax-zoom.css" type="text/css" />';
			$document->addCustomTag($headerTad);
		}

		
		$unique_suffix = rand();
		$output_html = '<div id="fbContainer_' . $unique_suffix . '">' . "\n";
		$output_html .= '	<a id="altmsg" class="altlink" href="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash" target="_blank">Download Adobe Flash Player.</a>' . "\n";
		$output_html .= '</div>' . "\n"; 
		
		
		
		$output_html .= '<script language="JavaScript" type="text/javascript">' . "\n";
		$output_html .= 'flippingBook' . $unique_suffix . ' = new FlippingBook();' . "\n"; 
		$db->setQuery("SELECT * FROM #__flippingbook_pages WHERE book_id = " . intval ( $book_id ) . " AND published = 1 ORDER BY ordering");
		$rows = $db->loadObjectList();
		$total_pages = count($rows);
		 
		
		$output_html .= 'flippingBook' . $unique_suffix . '.pages = [' . "\n";
		if (($bookParams->direction == "RTL") && ($total_pages%2 == 1) && ($total_pages > 0))
			$output_html .= '"' . JURI::base(true) . '/images/flippingbook/blank.png|",' . "\n";
		for ($i = 0; $i < $total_pages; $i++ ) {
			if ($bookParams->direction == "RTL")
				$output_html .= '"' . JURI::base(true) . "/images/flippingbook/" . $rows[$total_pages - $i - 1]->file;
			else
				$output_html .= '"' . JURI::base(true) . "/images/flippingbook/" . $rows[$i]->file;
			if ( $i != ($total_pages-1) ) 
				$output_html .= '|",' . "\n";
			else 
				$output_html .= '"' . "\n";
		}
		$output_html .= '];' . "\n\n";
		
		
		
		$output_html .= 'flippingBook' . $unique_suffix . '.enlargedImages = [' . "\n";
		if (($bookParams->direction == "RTL") && ($total_pages%2 == 1) && ($total_pages > 0))
			$output_html .= '"' . JURI::base(true) . '/images/flippingbook/blank.png|",' . "\n";
		for ( $i = 0; $i < $total_pages; $i++ ) { 
			if ( $rows[$i]->zoom_url == "" ) 
				$rows[$i]->zoom_url = $rows[$i]->file; 
			if ($bookParams->direction == "RTL") 
				$output_html .= '"' . JURI::base(true) . "/images/flippingbook/" . $rows[$total_pages - $i - 1]->zoom_url;
			else
				$output_html .= '"' . JURI::base(true) . "/images/flippingbook/" . $rows[$i]->zoom_url;
			if ( $i != ($total_pages-1) )  
				$output_html .= '|",' . "\n";
			else  
				$output_html .= '"' . "\n";
		}
		$output_html .= '];' . "\n\n";
		
		
		$output_html .= 'flippingBook' . $unique_suffix . '.pageLinks = [' . "\n";
		if (($bookParams->direction == "RTL") && ($total_pages%2 == 1) && ($total_pages > 0))
			$output_html .= '"|",' . "\n";
		for ( $i = 0; $i < $total_pages; $i++ ) {
			$rows[$i]->link_url = str_replace ('&', '&', $rows[$i]->link_url );	
			
			if ($bookParams->direction == "RTL")
				$output_html .= '"' . $rows[$total_pages - $i - 1]->link_url;
			else
				$output_html .= '"' . $rows[$i]->link_url;
			if ( $i != ($total_pages-1) ) 
				$output_html .= '|",' . "\n";
			else 
				$output_html .= '"' . "\n";
		}
		$output_html .= '];' . "\n\n";
		
		
		if ($bookParams->zooming_method == 1) { 
			$output_html .= 'flippingBook' . $unique_suffix . '.swfHeight = [' . "\n";
			if (($bookParams->direction == "RTL") && ($total_pages%2 == 1) && ($total_pages > 0))
				$output_html .= '"|",' . "\n"; 
			for ( $i = 0; $i < $total_pages; $i++ ) {
				if ($bookParams->direction == "RTL") 
					$output_html .= '"' . $rows[$total_pages - $i - 1]->zoom_height;
				else
					$output_html .= '"' . $rows[$i]->zoom_height;
				if ( $i != ($total_pages-1) ) 
					$output_html .= '|",' . "\n";
				else 
					$output_html .= '"' . "\n";
			}
			$output_html .= '];' . "\n\n";
			$output_html .= 'flippingBook' . $unique_suffix . '.swfWidth = [' . "\n";
			for ( $i = 0; $i < $total_pages; $i++ ) {
				if ($bookParams->direction == "RTL")
					$output_html .= '"' . $rows[$total_pages - $i - 1]->zoom_width;
				else
					$output_html .= '"' . $rows[$i]->zoom_width;
				if ( $i != ($total_pages-1) ) 
					$output_html .= '|",' . "\n";
				else 
					$output_html .= '"' . "\n";
			}
			$output_html .= '];' . "\n\n";
		}

		
		$output_html .= 'flippingBook' . $unique_suffix . '.settings.uniqueSuffix = "' . $unique_suffix . '";' . "\n";
		$output_html .= 'flippingBook' . $unique_suffix . '.stageWidth = "' . $bookParams->flash_width . '";' . "\n";
		$output_html .= 'flippingBook' . $unique_suffix . '.stageHeight = "' . $bookParams->flash_height . '";' . "\n";
		$output_html .= 'flippingBook' . $unique_suffix . '.settings.direction = "' . $bookParams->direction . '";' . "\n";
		$output_html .= 'flippingBook' . $unique_suffix . '.settings.bookWidth = "' . ($bookParams->book_width * 2 + $bookParams->frame_width * 4) . '";' . "\n";
		$output_html .= 'flippingBook' . $unique_suffix . '.settings.bookHeight = "' . ($bookParams->book_height + $bookParams->frame_width * 2) . '";' . "\n";
			$firstPageNumber = intval(JRequest::getVar( 'page', '', 'get', 'int'));
			if ($firstPageNumber == '') $firstPageNumber = $bookParams->first_page;
			if ($firstPageNumber == 0) $firstPageNumber = 1;
			if ($bookParams->direction == "RTL") { 
				$firstPageNumber = $total_pages - $firstPageNumber + 1;
				if ($total_pages%2 == 1) 
					$firstPageNumber++;
			}
		$output_html .= 'flippingBook' . $unique_suffix . '.settings.firstPageNumber = "' . $firstPageNumber . '";' . "\n";
		if ( $bookParams->navigation_bar != "" )
			$output_html .= 'flippingBook' . $unique_suffix . '.settings.navigationBar = "' . JURI::base(true) . '/components/com_flippingbook/navigationbars/' . $bookParams->navigation_bar .'";' . "\n";
		$output_html .= 'flippingBook' . $unique_suffix . '.settings.navigationBarPlacement = "' . $bookParams->navigation_bar_placement . '";' . "\n";
		$output_html .= 'flippingBook' . $unique_suffix . '.settings.pageBackgroundColor = 0x' .$bookParams->page_background_color . ';' . "\n";
		$output_html .= 'flippingBook' . $unique_suffix . '.settings.backgroundColor = "' . $bookParams->background_color . '";' . "\n";
		$output_html .= 'flippingBook' . $unique_suffix .'.settings.backgroundImage = "' . JURI::base(true) . '/images/flippingbook/' . $bookParams->background_image . '";' . "\n";
		$output_html .= 'flippingBook' . $unique_suffix . '.settings.backgroundImagePlacement = "' . $bookParams->background_image_placement . '";' . "\n";
		$output_html .= 'flippingBook' . $unique_suffix . '.settings.staticShadowsType = "' . $bookParams->static_shadows_type . '";' . "\n";
		$output_html .= 'flippingBook' . $unique_suffix . '.settings.staticShadowsDepth = "' . $bookParams->static_shadows_depth . '";' . "\n";
		$output_html .= 'flippingBook' . $unique_suffix . '.settings.autoFlipSize = "' . $bookParams->auto_flip_size . '";' . "\n";
		$center_book = $bookParams->center_book == 1 ? "true" : "false";
		$output_html .= 'flippingBook' . $unique_suffix . '.settings.centerBook = ' . $center_book . ';' ."\n";
		$scale_content = $bookParams->scale_content == 1 ? "true" : "false";
		$output_html .= 'flippingBook' . $unique_suffix . '.settings.scaleContent = ' . $scale_content . ';' . "\n";
		$always_opened = $bookParams->always_opened == 1 ? "true" : "false";
		$output_html .= 'flippingBook' . $unique_suffix . '.settings.alwaysOpened = ' . $always_opened . ';' . "\n";
		$output_html .= 'flippingBook' . $unique_suffix . '.settings.flipCornerStyle = "' . $bookParams->flip_corner_style .'";' . "\n";
		$hardcover = $bookParams->hardcover == 1 ? "true" : "false";
		$output_html .= 'flippingBook' . $unique_suffix . '.settings.hardcover = ' . $hardcover . ';' . "\n";
		$output_html .= 'flippingBook' . $unique_suffix . '.settings.downloadURL = "' . $bookParams->download_url . '";' . "\n";
		$output_html .= 'flippingBook' . $unique_suffix . '.settings.downloadTitle = "' . $bookParams->download_title . '";' . "\n";
		$output_html .= 'flippingBook' . $unique_suffix . '.settings.downloadSize = "' . $bookParams->download_size . '";' . "\n";
		$allow_pages_unload = $bookParams->allow_pages_unload == 1 ? "true" : "false";
		$output_html .= 'flippingBook' . $unique_suffix . '.settings.allowPagesUnload = ' . $allow_pages_unload . ';' ."\n";
		$fullscreen_enabled = $bookParams->fullscreen_enabled == 1 ? "true" : "false";
		$output_html .= 'flippingBook' . $unique_suffix . '.settings.fullscreenEnabled = ' . $fullscreen_enabled . ';' ."\n";
		$zoom_enabled = $bookParams->zoom_enabled == 1 ? "true" : "false";
		$output_html .= 'flippingBook' . $unique_suffix . '.settings.zoomEnabled = ' . $zoom_enabled . ';' . "\n";
		$output_html .= 'flippingBook' . $unique_suffix . '.settings.zoomImageWidth = "' . $bookParams->zoom_image_width. '";' . "\n";
		$output_html .= 'flippingBook' . $unique_suffix . '.settings.zoomImageHeight = "' . $bookParams->zoom_image_height . '";' . "\n";
		$output_html .= 'flippingBook' . $unique_suffix . '.settings.zoomUIColor = 0x' . $bookParams->zoom_ui_color . ';'. "\n";
		$slideshow_button = $bookParams->slideshow_button == 1 ? "true" : "false";
		$output_html .= 'flippingBook' . $unique_suffix . '.settings.slideshowButton = ' . $slideshow_button .';' . "\n";
		$slideshow_auto_play = $bookParams->slideshow_auto_play == 1 ? "true" : "false";
		$output_html .= 'flippingBook' . $unique_suffix . '.settings.slideshowAutoPlay = ' . $slideshow_auto_play .';' . "\n";
		$output_html .= 'flippingBook' . $unique_suffix .'.settings.slideshowDisplayDuration = "' . $bookParams->slideshow_display_duration . '";' . "\n";
		$go_to_page_field = $bookParams->go_to_page_field == 1 ? "true" : "false";
		$output_html .= 'flippingBook' . $unique_suffix . '.settings.goToPageField = ' . $go_to_page_field . ';' . "\n";
		$first_last_buttons = $bookParams->first_last_buttons == 1 ? "true" : "false";
		$output_html .= 'flippingBook' . $unique_suffix . '.settings.firstLastButtons = '. $first_last_buttons . ';' . "\n";
		$print_enabled = $bookParams->print_enabled == 1 ? "true" : "false";
		$output_html .= 'flippingBook' . $unique_suffix . '.settings.printEnabled = ' . $print_enabled . ';' . "\n";
		$zooming_method = $bookParams->zooming_method == 1 ? '"ajax"' : '"flash"';
		$output_html .= 'flippingBook' . $unique_suffix . '.settings.zoomingMethod = ' . $zooming_method . ';' . "\n";
		$sound_control_button = $bookParams->sound_control_button == 1 ? "true" : "false";
		$output_html .= 'flippingBook' . $unique_suffix . '.settings.soundControlButton = ' . $sound_control_button . ';' . "\n";
		$transparent_pages = $bookParams->transparent_pages == 1 ? "true" : "false";
		$output_html .= 'flippingBook' . $unique_suffix . '.settings.showUnderlyingPages = ' . $transparent_pages . ';' . "\n";
		$output_html .= 'flippingBook' . $unique_suffix . '.settings.fullscreenHint = "' . urlencode ( $bookParams->fullscreen_hint ) . '";' . "\n";
		$show_zoom_hint = $bookParams->show_zoom_hint == 1 ? "true" : "false";
		$output_html .= 'flippingBook' . $unique_suffix . '.settings.zoomHintEnabled = "' . $show_zoom_hint . '";' . "\n";

		
		FB_zoomOnClick == 1 ? $zoomOnClick = "true" : $zoomOnClick = "false";
		$output_html .= 'flippingBook' . $unique_suffix .'.settings.zoomOnClick = ' . $zoomOnClick . ';' . "\n";
		$output_html .= 'flippingBook' . $unique_suffix .'.settings.moveSpeed = "' . FB_moveSpeed . '";' . "\n";
		$output_html .= 'flippingBook' . $unique_suffix .'.settings.closeSpeed = "' . FB_closeSpeed . '";' . "\n";
		$output_html .= 'flippingBook' . $unique_suffix .'.settings.gotoSpeed = "' . FB_gotoSpeed . '";' . "\n";
		$output_html .= 'flippingBook' . $unique_suffix .'.settings.rigidPageSpeed = "' . FB_rigidPageSpeed . '";' . "\n";
		$output_html .= 'flippingBook' . $unique_suffix .'.settings.zoomHint = "' . FB_zoomHint . '";' . "\n";
		$output_html .= 'flippingBook' . $unique_suffix .'.settings.printTitle = "' . FB_printTitle . '";' . "\n";
		$output_html .= 'flippingBook' . $unique_suffix .'.settings.downloadComplete = "' . FB_downloadComplete . '";' . "\n";
		FB_dropShadowEnabled == 1 ? $dropShadowEnabled = "true" : $dropShadowEnabled = "false";
		$output_html .= 'flippingBook' . $unique_suffix . '.settings.dropShadowEnabled = ' . $dropShadowEnabled . ';' . "\n";
		if ( FB_flipSound != "")
			$output_html .= 'flippingBook' . $unique_suffix . '.settings.flipSound = "' . JURI::base(true) . '/components/com_flippingbook/sounds/' . FB_flipSound . '";' . "\n";
		if ( FB_hardcoverSound != "")
			$output_html .= 'flippingBook' . $unique_suffix . '.settings.hardcoverSound = "' . JURI::base(true) . '/components/com_flippingbook/sounds/' . FB_hardcoverSound . '";' . "\n";
		$output_html .= 'flippingBook' . $unique_suffix . '.settings.preloaderType = "' . FB_preloaderType . '";' . "\n";
		$output_html .= 'flippingBook' . $unique_suffix . '.settings.loader = true;' . "\n";
		$output_html .= 'flippingBook' . $unique_suffix . '.settings.frameColor = 0x' . $bookParams->frame_color . ';' . "\n";
		$output_html .= 'flippingBook' . $unique_suffix . '.settings.frameWidth = "' . intval ( $bookParams->frame_width ) . '";' . "\n";
		$output_html .= 'flippingBook' . $unique_suffix . '.containerId = "fbContainer_' . $unique_suffix . '";' . "\n";

		
		$output_html .= 'flippingBook' . $unique_suffix . '.create("' . JURI::base(true) . '/components/com_flippingbook/flippingbook.swf");' . "\n";
		$output_html .= 'jQuery.noConflict();' . "\n";
		
		
		if ($bookParams->zooming_method == 1) {
			$output_html .= 'jQuery(document).ready(function() {' . "\n";
			$output_html .= '	zoom_init("' . JURI::root() . '", flippingBook' . $unique_suffix . ');' . "\n";
			$output_html .= '});' . "\n";
		}
		
		$output_html .= '</script>' . "\n";

		
		if ( $bookParams->show_pages_description == 1 ) {
			if ($bookParams->direction == "RTL") {
				$output_html .= '<div id="fb_pageDescription_'. $unique_suffix . '" class="fb_pageDescription"><div id="fb_rightPageDescription_' . $unique_suffix . '" class="fb_rightPageDescription"></div>' . "\n";
				$output_html .= '<div id="fb_leftPageDescription_' . $unique_suffix . '" class="fb_leftPageDescription"></div></div>' . "\n";
			} else {
				$output_html .= '<div id="fb_pageDescription_'. $unique_suffix . '" class="fb_pageDescription"><div id="fb_leftPageDescription_' . $unique_suffix . '" class="fb_leftPageDescription"></div>' . "\n";
				$output_html .= '<div id="fb_rightPageDescription_'. $unique_suffix .'" class="fb_rightPageDescription"></div></div>' . "\n";
			}
		}

		
		if ( $bookParams->show_book_description == 1 ) {
			$output_html .= '<div id="fb_bookDescription' . $unique_suffix . '" class="fb_bookDescription">' . $bookParams->description . "</div>\n\n";
		}

		
		if ( $bookParams->show_pages_description == 1 ) { 
			$page_decriptions = '';
			$db->setQuery("SELECT * FROM #__flippingbook_pages WHERE book_id = " . intval ( $book_id ) . " AND published = 1 ORDER BY ordering");
			$rows = $db->loadObjectList();
			$i = 1;
			foreach ( $rows as $row ) {
				if ($bookParams->direction == "RTL")
					$page_decriptions .= '<div id="fb_page_'. $unique_suffix . '_' . ($total_pages-$i+1) . '">' . $row->description . "</div>\n";
				else
					$page_decriptions .= '<div id="fb_page_'. $unique_suffix . '_' . ($i) . '">' . $row->description . "</div>\n";
				$i++;
			}
			$output_html .= '<div id="fb_hidden_' .$unique_suffix . '" style="position: absolute; visibility: hidden; display: none;">' . "\n" . $page_decriptions . "</div>\n";
		}
		
		$output_html .= '';

		if ( empty($call_from_plugin) )
			echo $output_html;
	}

?>