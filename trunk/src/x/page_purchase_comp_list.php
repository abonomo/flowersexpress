<?php

require_once('framework.php');
require_once('page_generic_list.php');
require_once('obj_comp_lists.php');

//{display value, database field name}
$order_by_options = array(
		array('Relevance', 'relevance'),
		array('Availibility', 'quantity_left'),
		array('Expiration Date', 'obj_table.expiration_date'),
		array('Expected Date', 'purchases.delivery_date'),	//CHANGE
		array('In Warehouse', 'purchases.in_warehouse'),
		array('Min Price/Unit', 'obj_table.min_price_per_unit'),
		array('Product Name', 'products.name'),
		array('Supplier Name', 'suppliers.company_name'),		
	);

$obj_purchase_comp_list = new ObjPurchaseCompList();

//make a customer list page from constructing a generic list page with different parameters, and a different list object
$page_purchase_comp_list = new PageGenericList(ObjOuterArea::$TAB_PURCHASES, 'purchase_comp', $order_by_options, $obj_purchase_comp_list, 'product', false, 'Purchase Inventory');
$page_purchase_comp_list->run();

?>