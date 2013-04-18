<?php
defined('_JEXEC') or die('Restricted access');

$current_itemid = intval( @$Itemid );
if (!$current_itemid) $current_itemid = '';
else $current_itemid = '&amp;Itemid=' . $current_itemid;

$output_html = '<ul class="flippingbook_book-name">';
foreach ($this->categories_list as $row) {
	$FB_link = 'href="' . JRoute::_("index.php?option=com_flippingbook&amp;view=category&amp;id=" . $row->slug . $current_itemid ) . '"';
	$output_html .= '<li><a ' . $FB_link . '>' . $row->title . '</a></li>';
}
$output_html .= '</ul>';
echo $output_html;
?>