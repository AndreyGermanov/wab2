<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

@$output_html .= '<table width="100%" class="fb_book_list_table">' . "\n";
$current_itemid = intval( @$Itemid );
if ( !$current_itemid ) $current_itemid = '';
else $current_itemid = '&amp;Itemid=' . $current_itemid;

If ( @$columns_in_list == '' ) $columns_in_list = $this->category->columns;
$current_column = 1;
$current_book = 1;
$totalBooksInCategory = count ( $this->books );

foreach ( $this->books as $row ) {
	if ( $current_column == 1 ) $output_html .= '<tr>' . "\n";
	$cell_width = 100;
	if ( $columns_in_list > 1 ) $cell_width = floor( 100 / $columns_in_list );
	if ( ( $current_book == count( $this->books ) ) && ( $current_column != $columns_in_list ) ) {
		$colspan = $columns_in_list - $current_column + 1;
	}
	if (@$colspan > 1) {
		$output_html .= '<td valign="top" class="flippingbook_book_list_item" colspan="' . $colspan . '">' . "\n";
	} else {
		$output_html .= '<td valign="top" width="' . $cell_width . '%" class="flippingbook_book_list_item">' . "\n";
	}
	switch ($row->open_book_in) {
		case 1:
		default:
			$FB_link = 'href="' . JRoute::_("index.php?option=com_flippingbook&amp;view=book&amp;id=" . $row->slug . "&amp;catid=" . $this->category->catslug ) . '"';
		break;
		case 3:
			$FB_link = 'href="#" onclick="javascript: window.open(' . "'index.php?option=com_flippingbook&amp;view=book&amp;id=" . $row->slug . "&amp;catid=" . $this->category->catslug . "&amp;tmpl=component'" . ", '', 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=" . $row->new_window_width . ",height=" . $row->new_window_height . "'); return false\"";
		break;
	}
	$output_html .= '<a ' . $FB_link . ' class="fb_contentheading"><span class="fb_contentheading">' . $row->title . '</span></a>';
	$output_html .= '<div class="flippingbook_book-description">' . "\n";
	if ( $row->preview_image != '' )
		$output_html .= '<a ' . $FB_link . 'class="flippingbook_book_img" title="' . $row->title . '"><img src="images/flippingbook/' . $row->preview_image . '" border="0" alt="' . $row->title . '" /></a>';
	$output_html .= $row->description;
	$output_html .= '</div>' . "\n";
	$output_html .= '</td>' . "\n";
	if ( ( $current_column == $columns_in_list ) || ( $current_book == $totalBooksInCategory ) ) { 
		$output_html .= '</tr>' . "\n";
		$current_column = 1;
	} else
		$current_column++;
	$current_book++;
}
$output_html .= '</table>' . "\n";
echo $output_html;
?>