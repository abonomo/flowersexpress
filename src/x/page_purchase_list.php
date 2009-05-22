<?php

require_once('framework.php');
require_once('page_generic_list.php');
require_once('obj_lists.php');

//{display value, database field name}
$order_by_options = array(
		array('Relevance', 'relevance'),
		array('Purchase Code', 'icode'),
		array('Supplier ID', 'supplier_id'),
		array('Shipper ID', 'shipper_id'),
		array('Shipment Details', 'shipment_details'),
		array('In Warehouse', 'in_warehouse'),
		array('Delivery Date', 'delivery_date'),
		array('Price', 'price'),
		array('Created Date', 'created_date'),
		array('Updated Date', 'updated_date'),
		array('In Trash Bin', 'trash_flag')	//specially lists stuff in trash first
	);

$obj_purchase_list = new ObjPurchaseList();

//make a purchase list page from constructing a generic list page with different parameters, and a different list object
$page_purchase_list = new PageGenericList(ObjOuterArea::$TAB_PURCHASES, 'purchase', $order_by_options, $obj_purchase_list);
$page_purchase_list->run();

?>