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
 *  $Id: nodewarn.php,v 1.32 2011/03/04 10:47:57 alec Exp $
 */

$setwarnings = isset($_POST['setwarnings']) ? $_POST['setwarnings'] : array();

if(isset($setwarnings['mnodeid']))
{
	$message = isset($setwarnings['message']) ? $setwarnings['message'] : '';
	$warnon  = isset($setwarnings['warnon']) ? $setwarnings['warnon'] : FALSE;
	$warnoff = isset($setwarnings['warnoff']) ? $setwarnings['warnoff'] : FALSE;
    $nodes   = array();

	foreach($setwarnings['mnodeid'] as $value)
	{
		if ($warnon) {
			if ($LMS->NodeSetWarn($value, TRUE))
			    $nodes[] = $value;
	    }
		else if ($warnoff) {
			if ($LMS->NodeSetWarn($value, FALSE))
			    $nodes[] = $value;
        }
		if($message)
			$DB->Execute('UPDATE customers SET message=? WHERE id=?', array($message,$LMS->GetNodeOwner($value)));
	}

    if (!empty($nodes)) {
        $data = array('nodes' => $nodes);
        $LMS->ExecHook('node_warn_after', $data);
    }

	$SESSION->save('warnmessage', $message);
	$SESSION->save('warnon', $warnon);
	$SESSION->save('warnoff', $warnoff);

	$SESSION->redirect('?'.$SESSION->get('backto'));
}

$warning = isset($_GET['warning']) ? 1 : 0;

if(!empty($_POST['marks']))
{
    $nodes = array();
    foreach($_POST['marks'] as $id) {
        $LMS->NodeSetWarn($id, $warning);
    }

    if (!empty($nodes)) {
        $data = array('nodes' => $nodes, 'warning' => $warning);
        $LMS->ExecHook('node_warn_after', $data);
    }

	$SESSION->redirect('?'.$SESSION->get('backto'));
}

$backid = isset($_GET['ownerid']) ? $_GET['ownerid'] : 0;

if($backid && $LMS->CustomerExists($backid))
{
	$res = $LMS->NodeSetWarnU($backid, $warning);

    if ($res) {
        $data = array('ownerid' => $backid, 'warning' => $warning);
        $LMS->ExecHook('node_warn_after', $data);
    }

	$redir = $SESSION->get('backto');
	if($SESSION->get('lastmodule')=='customersearch')
		$redir .= '&search=1';

	$SESSION->redirect('?'.$redir.'#'.$backid);
}

$backid = isset($_GET['id']) ? $_GET['id'] : 0;

if($backid && $LMS->NodeExists($backid))
{
    $res = $LMS->NodeSwitchWarn($backid);

    if ($res) {
        $data = array('nodeid' => $backid);
        $LMS->ExecHook('node_warn_after', $data);
    }

	if(!empty($_GET['shortlist'])) {
	    header('Location: ?m=nodelistshort&id='.$LMS->GetNodeOwner($backid));
		die;
	}
	else {
		$SESSION->redirect('?'.$SESSION->get('backto').'#'.$backid);
	}
}

$SESSION->save('backto', $_SERVER['QUERY_STRING']);

$layout['pagetitle'] = trans('Notices');

$nodelist = $LMS->GetNodeList();

unset($nodelist['total']);
unset($nodelist['order']);
unset($nodelist['direction']);
unset($nodelist['totalon']);
unset($nodelist['totaloff']);

$SMARTY->assign('warnmessage', $SESSION->get('warnmessage'));
$SMARTY->assign('warnon', $SESSION->get('warnon'));
$SMARTY->assign('warnoff', $SESSION->get('warnoff'));
$SMARTY->assign('nodelist',$nodelist);
$SMARTY->display('nodewarnings.html');

?>