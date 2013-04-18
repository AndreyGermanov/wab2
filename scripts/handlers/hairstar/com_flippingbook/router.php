<?php
function FlippingBookBuildRoute( &$query ) {

	$segments = array();
	
	static $items;
	$itemid		= null;
	
	if (!$items) {
		$component	= &JComponentHelper::getComponent('com_flippingbook');
		$menu		= &JSite::getMenu();
		$items		= $menu->getItems('componentid', $component->id);
	}
	
	
	if (is_array($items)) {
		
		if (!isset($query['view']) && !isset($query['id']) && !isset($query['catid']) && isset($query['Itemid'])) {
			$itemid = (int) $query['Itemid'];
		}
	}

	
	if (!$itemid) {
		if ( isset($query['view']) ) {
			$segments[] = $query['view'];
			unset( $query['view'] );
		};
		if ( isset($query['id']) ) {
			$segments[] = $query['id'];
			unset( $query['id'] );
		};
		if (isset( $query['catid'] )) {
			$segments[] = $query['catid'];
			unset( $query['catid'] );
		};
	} else {
		$query['Itemid'] = $itemid;

		
		unset($query['view']);
		unset($query['id']);
		unset($query['catid']);
	}
		return $segments;
}

function FlippingBookParseRoute($segments) {

	$vars = array();

	$count = count( $segments );

	switch( $segments[0] ) {
		case 'book':
			$vars['view'] = 'book'; 
			$id = explode( ':', $segments[1] );
			$vars['id'] = (int) $id[0];
			$vars['catid'] = explode( ':', $segments[$count-1] );
		break;
		case 'category': 
			$id = explode( ':', $segments[$count-1] ); 
			$vars['id'] = (int) $id[0]; 
			$vars['view'] = 'category'; 
		break;
	}
	
	return $vars;
}
?>