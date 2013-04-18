<?php
defined('_JEXEC') or die('Restricted access');

$output_html = '<table width="100%" class="fb_book_list_table">' . "\n";
$current_itemid = intval( @$Itemid );
if ( !$current_itemid ) 
	$current_itemid = '';
else 
	$current_itemid = '&amp;Itemid=' . $current_itemid;

If ( @$columns_in_list == '' ) 
	$columns_in_list = FB_columns;
$current_column = 1;
$current_book = 1;

foreach ( $this->categories_list as $row ) {
	if ( $current_column == 1 )
		$output_html .= '<tr>' . "\n";
	$cell_width = 100;
	if ( $columns_in_list > 1 )
		$cell_width = floor( 100 / $columns_in_list );
	if ( ( $current_book == count( $this->categories_list ) ) && ( $current_column != $columns_in_list ) )
		$colspan = $columns_in_list - $current_column + 1;
	if ( @$colspan > 1 )
		$output_html .= '<td valign="middle" align="center" class="flippingbook_book_list_item" colspan="' . $colspan . '">' . "\n";
	else
		$output_html .= '<td valign="middle" align="center" width="' . $cell_width . '%" class="flippingbook_book_list_item">' . "\n";
	
	$FB_link = 'href="' . JRoute::_("index.php?option=com_flippingbook&amp;view=category&amp;id=" . $row->slug . $current_itemid ) . '"';

	if ( @$row->preview_image != '' )
		$output_html .= '<a ' . $FB_link . ' align="center" title="' . $row->title . '"><img src="images/flippingbook/' . $row->preview_image . '" border="0" alt="' . $row->title . '" /></a>';
	else 
		$output_html .= '<a ' . $FB_link . ' align="center">No image</a>';
	$output_html .= '</td>' . "\n";
	if ( ( $current_column == $columns_in_list ) || ( $current_book == count( $this->categories_list ) ) ) { 
		$output_html .= '</tr>' . "\n";
		$current_column = 1;
	} else
		$current_column++;
	$current_book++;
}
$output_html .= '</table>' . "\n";
echo $output_html;
?>