<?php

/*
 * LMS version 1.11.13 Dira
 *
 *  (C) Copyright 2001-2011 LMS Developers
 *
 *  Please, see the doc/AUTHORS for more information about authors!
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License Version 2 as
 *  published by the Free Software Foundation.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307,
 *  USA.
 *
 *  $Id: customerdel.php,v 1.11 2011/01/18 08:12:21 alec Exp $
 */

$layout['pagetitle'] = trans('Customer Remove: $0',sprintf("%04d",$_GET['id']));
$SMARTY->assign('customerid',$_GET['id']);

if (!$LMS->CustomerExists($_GET['id']))
{
	$body = '<P>'.trans('Incorrect Customer ID.').'</P>';
}else{

	if($_GET['is_sure']!=1)
	{
		$body = '<P>'.trans('Do you want to remove $0 customer?',$LMS->GetCustomerName($_GET['id'])).'</P>'; 
		$body .= '<P>'.trans('All customer data and computers bound to this customer will be lost!').'</P>';
		$body .= '<P><A HREF="?m=customerdel&id='.$_GET['id'].'&is_sure=1">'.trans('Yes, I do.').'</A></P>';
	}else{
		header("Location: ?".$SESSION->get('backto'));
		$body = '<P>'.trans('Customer $0 has been removed.',$LMS->GetCustomerName($_GET['id'])).'</P>';
		$LMS->DeleteCustomer($_GET['id']);
	}
}

$SMARTY->assign('body',$body);
$SMARTY->display('dialog.html');

?>