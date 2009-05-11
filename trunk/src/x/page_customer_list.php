<?php

require_once('framework.php');
require_once('page_generic_list.php');
require_once('obj_lists.php');

//{display value, database field name}
$order_by_options = array(
		array('Relevance', 'relevance'),
		array('Customer Code', 'icode'),
		array('Company Name', 'company_name'),
		array('City', 'city'),
		array('Province', 'province'),
		array('Country', 'country'),
		array('Created Date', 'created_date'),
		array('Updated Date', 'updated_date'),
		array('In Trash Bin', 'trash_flag')	//specially lists stuff in trash first
	);

$obj_customer_list = new ObjCustomerList();

//make a customer list page from constructing a generic list page with different parameters, and a different list object
$page_customer_list = new PageGenericList(ObjOuterArea::$TAB_CUSTOMERS, 'customer', $order_by_options, $obj_customer_list);
$page_customer_list->run();

?>