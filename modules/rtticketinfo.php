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
 *  $Id: rtticketinfo.php,v 1.5 2011/01/18 08:12:25 alec Exp $
 */

$id = $_GET['id'];

if(! $LMS->TicketExists($id))
{
	$SESSION->redirect('?m=rtqueuelist');
}

$rights = $LMS->GetUserRightsRT($AUTH->id, 0, $id);

if(!$rights)
{
	$SMARTY->display('noaccess.html');
	$SESSION->close();
	die;
}

$ticket = $DB->GetRow('SELECT t.id, t.cause, t.creatorid, c.name AS creator, 
		    t.createtime, t.resolvetime
		    FROM rttickets t
		    LEFT JOIN users c ON (t.creatorid = c.id)
		    WHERE t.id = ?', array($id));

$ticket['message'] = $DB->GetOne('SELECT body FROM rtmessages
		    WHERE ticketid = ?
		    ORDER BY createtime DESC LIMIT 1', 
		    array($id));

$ticket['uptime'] = uptimef($ticket['resolvetime'] ? $ticket['resolvetime'] - $ticket['createtime'] : time() - $ticket['createtime']);

$SMARTY->assign('ticket', $ticket);

$SMARTY->display('rtticketinfoshort.html');

?>
