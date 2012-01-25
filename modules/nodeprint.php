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
 *  $Id: nodeprint.php,v 1.14 2011/01/18 08:12:24 alec Exp $
 */

$type = isset($_GET['type']) ? $_GET['type'] : '';

switch($type)
{
	case 'nodelist': /***********************************************/
		switch($_POST['filter'])
		{
			case 0:
				$layout['pagetitle'] = trans('Nodes List');
				$nodelist = $LMS->GetNodeList($_POST['order'].','.$_POST['direction'], NULL, NULL, $_POST['network'], NULL, $_POST['customergroup']);
			break;
			case 1:
				$layout['pagetitle'] = trans('List of Connected Nodes');
				$nodelist = $LMS->GetNodeList($_POST['order'].','.$_POST['direction'], NULL, NULL, $_POST['network'], 1, $_POST['customergroup']);
			break;
			case 2:
				$layout['pagetitle'] = trans('List of Disconnected Nodes');
				$nodelist = $LMS->GetNodeList($_POST['order'].','.$_POST['direction'], NULL, NULL,  $_POST['network'], 2, $_POST['customergroup']);
			break;
			case 3:
				$layout['pagetitle'] = trans('Nodes List for Customers In Debt');

				$order=$_POST['order'].','.$_POST['direction'];
				if($order=='')
					$order='name,asc';

				list($order,$direction) = explode(',',$order);

				($direction=='desc') ? $direction = 'desc' : $direction = 'asc';

				switch($order)
				{
					case 'name':
						$sqlord = ' ORDER BY vnodes.name';
					break;
					case 'id':
						$sqlord = ' ORDER BY id';
					break;
					case 'mac':
						$sqlord = ' ORDER BY mac';
					break;
				    	case 'ip':
						$sqlord = ' ORDER BY ipaddr';
					break;
					case 'ownerid':
						$sqlord = ' ORDER BY ownerid';
					break;
				    	case 'owner':
						$sqlord = ' ORDER BY owner';
					break;
				}

				if($_POST['network'])
					$net = $LMS->GetNetworkParams($_POST['network']);
				
				$group = $_POST['customergroup'];

				$nodelist = $DB->GetAll('SELECT vnodes.id AS id, inet_ntoa(ipaddr) AS ip, mac, 
					    vnodes.name AS name, vnodes.info AS info, 
					    COALESCE(SUM(value), 0.00)/(CASE COUNT(DISTINCT vnodes.id) WHEN 0 THEN 1 ELSE COUNT(DISTINCT vnodes.id) END) AS balance, '
					    .$DB->Concat('UPPER(lastname)',"' '",'customers.name').' AS owner
					    FROM vnodes 
					    LEFT JOIN customers ON (ownerid = customers.id)
					    LEFT JOIN cash ON (cash.customerid = customers.id) 
					    WHERE 1=1 '
					    .($net ? ' AND ((ipaddr > '.$net['address'].' AND ipaddr < '.$net['broadcast'].') OR (ipaddr_pub > '.$net['address'].' AND ipaddr_pub < '.$net['broadcast'].'))' : '')
					    .($group ? ' AND EXISTS (SELECT 1 FROM customerassignments WHERE customerid = ownerid)' : '')
					    .' GROUP BY vnodes.id, ipaddr, mac, vnodes.name, vnodes.info, customers.lastname, customers.name
					    HAVING SUM(value) < 0'
					    .($sqlord != '' ? $sqlord.' '.$direction : ''));
				
				$SMARTY->assign('nodelist', $nodelist);
				$SMARTY->display('printindebtnodelist.html');
				$SESSION->close();
				die;
			break;
		}	

		unset($nodelist['total']);
		unset($nodelist['order']);
		unset($nodelist['direction']);
		unset($nodelist['totalon']);
		unset($nodelist['totaloff']);
		
		$SMARTY->assign('nodelist', $nodelist);
		$SMARTY->display('printnodelist.html');
	break;

	default:
		$layout['pagetitle'] = trans('Reports');
		
		$SMARTY->assign('customergroups', $LMS->CustomergroupGetAll());
		$SMARTY->assign('networks', $LMS->GetNetworks());
		$SMARTY->assign('printmenu', 'node');
		$SMARTY->display('printindex.html');
	break;
}

?>
