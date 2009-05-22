<?php

require_once('framework.php');
require_once('page_generic_list.php');
require_once('obj_lists.php');

//{display value, database field name}
$order_by_options = array(
		array('Relevance', 'relevance'),
		array('Sales Order Code', 'icode'),
		array('Customer', 'customer_id'),
		array('Shipper', 'shipper_id'),
		array('Shipment Details', 'shipment_details'),
		array('Special', 'special'),
		array('Order Date', 'order_date'),
		array('Delivery Date', 'delivery_date'),
		array('Price', 'price'),
		array('Created Date', 'created_date'),
		array('Updated Date', 'updated_date'),
		array('In Trash Bin', 'trash_flag')	//specially lists stuff in trash first
	);

$obj_sales_order_list = new ObjSalesOrderList();

//make a sales_order list page from constructing a generic list page with different parameters, and a different list object
$page_sales_order_list = new PageGenericList(ObjOuterArea::$TAB_SALES_ORDERS, 'sales_order', $order_by_options, $obj_sales_order_list);
$page_sales_order_list->run();

?>