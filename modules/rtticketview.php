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
 *  $Id: rtticketview.php,v 1.43 2011/04/01 10:35:12 alec Exp $
 */

if(! $LMS->TicketExists($_GET['id']))
{
	$SESSION->redirect('?m=rtqueuelist');
}

$rights = $LMS->GetUserRightsRT($AUTH->id, 0, $_GET['id']);

if(!$rights)
{
	$SMARTY->display('noaccess.html');
	$SESSION->close();
	die;
}

$ticket = $LMS->GetTicketContents($_GET['id']);

if($ticket['customerid'] && isset($CONFIG['phpui']['helpdesk_stats']) && chkconfig($CONFIG['phpui']['helpdesk_stats']))
{
	$yearago = mktime(0, 0, 0, date('n'), date('j'), date('Y')-1);
	$stats = $DB->GetAllByKey('SELECT COUNT(*) AS num, cause FROM rttickets 
			    WHERE customerid = ? AND createtime >= ? 
			    GROUP BY cause', 'cause', array($ticket['customerid'], $yearago));

	$SMARTY->assign('stats', $stats);
}

if($ticket['customerid'] && chkconfig($CONFIG['phpui']['helpdesk_customerinfo']))
{
	$customer = $LMS->GetCustomer($ticket['customerid'], true);
        $customer['groups'] = $LMS->CustomergroupGetForCustomer($ticket['customerid']);

	if(!empty($customer['contacts'])) $customer['phone'] = $customer['contacts'][0]['phone'];

	$customernodes = $LMS->GetCustomerNodes($ticket['customerid']);
	$allnodegroups = $LMS->GetNodeGroupNames();

	$SMARTY->assign('customerinfo', $customer);
	$SMARTY->assign('customernodes', $customernodes);
	$SMARTY->assign('allnodegroups', $allnodegroups);
}

$iteration = $LMS->GetQueueContents($ticket['queueid'], $order='createtime,desc', $state=-1);
foreach($iteration as $idx => $element)
{
	if (intval($element['id']) == intval($_GET['id']))
	{
		$next_ticketid = isset($iteration[$idx+1]) ? $iteration[$idx+1]['id'] : 0;
		$prev_ticketid = isset($iteration[$idx-1]) ? $iteration[$idx-1]['id'] : 0;
		break;
	}
}

$ticket['next_ticketid'] = $next_ticketid;
$ticket['prev_ticketid'] = $prev_ticketid;

$layout['pagetitle'] = trans('Ticket Review: $0',sprintf("%06d", $ticket['ticketid']));

$SESSION->save('backto', $_SERVER['QUERY_STRING']);

$SMARTY->assign('ticket', $ticket);
$SMARTY->display('rtticketview.html');

?>
