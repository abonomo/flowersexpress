<?php

include('framework.php');

class ObjOuterArea
{
	//pass one of these into echo_top to highlight that tab as currently active
	public static $TAB_NONE = -1;
	public static $TAB_HOME = 0;
	public static $TAB_SALES_ORDERS = 1;
	public static $TAB_PURCHASES = 2;
	public static $TAB_CUSTOMERS = 3;
	public static $TAB_PRODUCTS = 4;
	public static $TAB_SUPPLIERS = 5;
	public static $TAB_SHIPPERS = 6;
	public static $TAB_REPORTS = 7;
	public static $TAB_EMPLOYEES = 8;
	public static $TAB_ADMIN = 9;
	

	public static function echo_top($active_tab)
	{
		//here there will be contextualized tabs based on the user auth level
		echo('
		<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
		<html>
		  <head>
			<title>Flowers Express - Login</title>
			<link href="style.css" rel="stylesheet">
			<link href="favicon.ico" rel="shortcut icon">
			<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
			<script language="JavaScript" type="text/JavaScript">
			<!-- Hide script from older browsers
			function MM_openBrWindow(theURL,winName,features) { //v2.0
			  newwindow=window.open(theURL,winName,features);
			if (window.focus) {newwindow.focus()}
			  return false;
			}
			// End hiding script -->
			</script>
			<script language="Javascript" type="text/JavaScript">
			<!--
			var weekday = new Array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");
			var month = new Array("January","February","March","April","May","June","July","August","September","October","November","December");
			var today = new Date()
			var dateline = weekday[today.getDay()] + ",&nbsp;" + today.getDate() + ".&nbsp;" + month[today.getMonth()] + "&nbsp;" + today.getFullYear()
			//-->
			</script>
		  </head>

		<body>
		<div align="center">&nbsp;

		  <table class="content_area" cellspacing="0" cellpadding="0">
		  <tbody>

		<!-- top row of page: header -->
			<tr>
			  <td height="120" valign="top" align="right" colspan="6" style="background-image: url(\'../img/top_logo.jpg\'); background-position: 50% 50%; background-repeat: no-repeat;">
		');
		
		//if logged in, echo a logout button
		if(LoginManager::meets_auth_level(LoginManager::$AUTH_LOGIN))
		{
			echo('<a href="op_logout.php">Logout</a>');
		}		
		
		echo('
			  </td>
			</tr>

		<!-- middle "row" of page: all the words -->
			<tr>

		<!-- left column -->
			  <td valign="top" class="left" rowspan="2" height="957">

		<!-- left menu links -->
				<table width="120" cellspacing="0" cellpadding="0" id="navigation">
				  <tbody>
					<tr>

		<!-- leading space between banner and menu -->
					  <td bgcolor="#FFFFFF">&nbsp;</td></tr><tr>
					  <td><a class="' . (($active_tab == self::$TAB_HOME) ? 'active_menu' : 'menu') . '" href="page_home.php">Home</a></tr><tr>
					  <td><a class="' . (($active_tab == self::$TAB_SALES_ORDERS) ? 'active_menu' : 'menu') . '" href="page_sales_order_menu.php">Sales Order</a></td></tr><tr>
					  <td><a class="' . (($active_tab == self::$TAB_PURCHASES) ? 'active_menu' : 'menu') . '" href="page_purchase_menu.php">Purchase</a></td></tr><tr>
					  <td><a class="' . (($active_tab == self::$TAB_CUSTOMERS) ? 'active_menu' : 'menu') . '" href="page_customer_menu.php">Customer</a></td></tr><tr>
					  <td><a class="' . (($active_tab == self::$TAB_PRODUCTS) ? 'active_menu' : 'menu') . '" href="page_product_menu.php">Product</a></td></tr><tr>					  
					  <td><a class="' . (($active_tab == self::$TAB_SUPPLIERS) ? 'active_menu' : 'menu') . '" href="page_supplier_menu.php">Supplier</a></td></tr><tr>
					  <td><a class="' . (($active_tab == self::$TAB_SHIPPERS) ? 'active_menu' : 'menu') . '" href="page_shipper_menu.php">Shipper</a></td></tr><tr>					  
					  <td><a class="' . (($active_tab == self::$TAB_REPORTS) ? 'active_menu' : 'menu') . '" href="page_report_menu.php">Report</a></td></tr><tr>			  
					  <td><a class="' . (($active_tab == self::$TAB_EMPLOYEES) ? 'active_menu' : 'menu') . '" href="page_employee_menu.php">Employee</a></td></tr><tr>
		');
		
		//** ADMIN ONLY - view admin tab
		if(LoginManager::meets_auth_level(LoginManager::$AUTH_ADMIN))
		{
			echo('
					  <td><a class="' . (($active_tab == self::$TAB_ADMIN) ? 'active_menu' : 'menu') . '" href="page_admin_menu.php">Admin</a></td></tr><tr>
			');
		}		
		
		echo('
		
					</tr>

				  </tbody>
				</table>

		<!-- end left menu links -->
			  </td>

		<!-- 4 spaces separating left menu and middle -->

			  <td valign="top" >&nbsp;&nbsp;&nbsp;&nbsp; </td>

		<!-- inner area -->

			 <td valign="top">

		<!-- date today -->
				<p style="text-align: right">&nbsp;

				  <b><script language="Javascript" type="text/JavaScript">
					  document.write(dateline)
				  </script></b>

				</p>

		<!-- keep middle and top spaced correctly, insert empty very-wide table -->
				<table cellpadding="0" cellspacing="0"><tr><td width="1000"></td></tr></table>	
		');

		//inner area comes next
	}
	
	public static function echo_bottom()
	{
		echo('
					  </td>
					</tr>

		<!-- end of all Announcements -->

				  </tbody>
				</table>

		<!-- end of Announcements table -->

			  </td>

		<!-- end of middle column -->

		<!-- 4 spaces separating right column and right edge-->
			  <td valign="top" rowspan="2">&nbsp;&nbsp;&nbsp;&nbsp;</td>

			</tr>
		<!-- end of right"row": text -->

		<!-- "3rd" row of page: link to top -->

			  <td valign="top" colspan="3">
				<p style="text-align: center">

		<a href="index.html">top</a>

				</p><br>
			  </td>

		<!-- end of 3rd row of page: link -->

		<!-- bottom row: footer -->

			<tr>
			  <td valign="top" class="footer" colspan="6">
				<p style="text-align: center">

		Copyright &#169; 2009 Someone

				</p>
			  </td>
			</tr>
		<!-- end of bottom row, footer -->

		  </tbody>
		  </table>

		<!-- end page disclaimer -->
				<p style="text-align: center">

				  <font size="0">
		<br><i>Submitted for CS 130 Software Engineering, Spring \'09, UCLA</i>
				  </font>

				</p>

		<!-- end page -->
		</div>
		</body>
		</html>
		');	
	}
}

?>