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
 *  $Id: nodegroupedit.php,v 1.9 2011/01/18 08:12:24 alec Exp $
 */

if( !($id = $DB->GetOne('SELECT id FROM nodegroups WHERE id = ?', array(intval($_GET['id'])))))
{
	$SESSION->redirect('?m=nodegrouplist');
}

if(isset($_POST['nodeassignments']))
{
	$oper = $_POST['oper'];
	$nodeassignments = $_POST['nodeassignments'];
	if (isset($nodeassignments['membersnetid']) && $oper=='2')
	{
		$SESSION->redirect('?'.preg_replace('/&membersnetid=[0-9]+/', '', $SESSION->get('backto')).'&membersnetid='.$nodeassignments['membersnetid']);
	}
	if (isset($nodeassignments['othersnetid']) && $oper=='3')
	{
		$SESSION->redirect('?'.preg_replace('/&othersnetid=[0-9]+/', '', $SESSION->get('backto')).'&othersnetid='.$nodeassignments['othersnetid']);
	}
}

$membersnetid = isset($_GET['membersnetid']) ? $_GET['membersnetid'] : 0;
$othersnetid =  isset($_GET['othersnetid']) ? $_GET['othersnetid'] : 0;

$nodegroup = $LMS->GetNodeGroup($id, $membersnetid);
$nodes = $LMS->GetNodesWithoutGroup($id, $othersnetid);

$layout['pagetitle'] = trans('Group Edit: $0', $nodegroup['name']);

if(isset($_POST['nodegroup']))
{
	$nodegroupedit = $_POST['nodegroup'];

	foreach($nodegroupedit as $key => $value)
		$nodegroupedit[$key] = trim($value);

	$nodegroupedit['id'] = $_GET['id'];
	
	if($nodegroupedit['name'] == '')
		$error['name'] = trans('Group name required!');
	elseif(strlen($nodegroupedit['name']) > 32)
		$error['name'] = trans('Group name is too long!');
	elseif(!preg_match('/^[._a-z0-9-]+$/i', $nodegroupedit['name']))
		$error['name'] = trans('Invalid chars in group name!');
	elseif( $id != $nodegroupedit['id'])
		$error['name'] = trans('Group with name $0 already exists!',$nodegroupedit['name']);

	if(!$error)
	{
		$LMS->DB->Execute('UPDATE nodegroups SET name=?, description=?
				WHERE id=?',
				array($nodegroupedit['name'],
					$nodegroupedit['description'],
					$nodegroupedit['id']
				));

		$SESSION->redirect('?m=nodegroupinfo&id='.$id);
	}

	$nodegroup['description'] = $nodegroupedit['description'];
	$nodegroup['name'] = $nodegroupedit['name'];
	$SMARTY->assign('error', $error);
}

$SESSION->save('backto', $_SERVER['QUERY_STRING']);

$SMARTY->assign('nodegroup',$nodegroup);
$SMARTY->assign('nodes', $nodes);
$SMARTY->assign('nodescount', sizeof($nodes));
$SMARTY->assign('networks', $LMS->GetNetworks());
$SMARTY->assign('nodegroups', $LMS->GetNodeGroupNames());
$SMARTY->assign('membersnetid', isset($membersnetid) ? $membersnetid : 0);
$SMARTY->assign('othersnetid', isset($othersnetid) ? $othersnetid : 0);

$SMARTY->display('nodegroupedit.html');

?>
