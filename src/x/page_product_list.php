<?php

require_once('framework.php');
require_once('page_generic_list.php');
require_once('obj_lists.php');

//{display value, database field name}
$order_by_options = array(
		array('Relevance', 'relevance'),
		array('Product Code', 'icode'),
		array('Product Name', 'name'),
		array('Type', 'type'),
		array('Subtype 1', 'subtype1'),
		array('Subtype 2', 'subtype2'),
		array('Typical Lifespan', 'typical_lifespan_days'),
		array('Typical Price per Unit', 'typical_min_price_per_unit'),
		array('Created Date', 'created_date'),
		array('Updated Date', 'updated_date'),
		array('In Trash Bin', 'trash_flag')	//specially lists stuff in trash first
	);

$obj_product_list = new ObjProductList();

//make a product list page from constructing a generic list page with different parameters, and a different list object
$page_product_list = new PageGenericList(ObjOuterArea::$TAB_PRODUCTS, 'product', $order_by_options, $obj_product_list);
$page_product_list->run();

?>