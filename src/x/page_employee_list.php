<?php

require_once('framework.php');
require_once('page_generic_list.php');
require_once('obj_lists.php');

//{display value, database field name}
$order_by_options = array(
		array('Relevance', 'relevance'),
		array('Employee Code', 'icode'),
		array('First Name', 'first_name'),
		array('Last Name', 'last_name'),
		array('Title', 'title'),
		array('Department Name', 'dept_name'),
		array('Office Location', 'office_location'),
		array('Created Date', 'created_date'),
		array('Updated Date', 'updated_date'),
		array('In Trash Bin', 'trash_flag')	//specially lists stuff in trash first
	);

$obj_employee_list = new ObjEmployeeList();

//make a employee list page from constructing a generic list page with different parameters, and a different list object
$page_employee_list = new PageGenericList(ObjOuterArea::$TAB_EMPLOYEES, 'employee', $order_by_options, $obj_employee_list);
$page_employee_list->run();

?>