<?php
defined('_JEXEC') or die('Restricted access');

$current_itemid = intval( @$Itemid );
if (!$current_itemid) $current_itemid = '';
else $current_itemid = '&Itemid=' . $current_itemid;

@$output_html .= '<ul class="flippingbook_book-name">';
foreach ($this->books as $row) {
	switch ($row->open_book_in) {
		case 1:
		default:
			$FB_link = 'href="' . JRoute::_("index.php?option=com_flippingbook&amp;view=book&amp;id=" . $row->slug . "&amp;catid=" . $this->category->catslug ) . '"';
		break;
		case 3:
			$FB_link = 'href="#" onclick="javascript: window.open(' . "'index.php?option=com_flippingbook&amp;view=book&amp;id=" . $row->slug . "&amp;catid=" . $this->category->catslug . "&amp;tmpl=component'" . ", '', 'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,width=" . $row->new_window_width . ",height=" . $row->new_window_height . "'); return false\"";
		break;
	}
	$output_html .= '<li><a ' . $FB_link . '>' . $row->title . '</a></li>';
}
$output_html .= '</ul>';
echo $output_html;
?>