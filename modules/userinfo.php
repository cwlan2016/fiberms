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
 *  $Id: userinfo.php,v 1.58 2011/03/10 11:36:39 alec Exp $
 */

$userinfo = $LMS->GetUserInfo($_GET['id']);

if (!$userinfo || $userinfo['deleted'])
{
	$SESSION->redirect('?m=userlist');
}

$rights = $LMS->GetUserRights($_GET['id']);
foreach($rights as $right)
	if($access['table'][$right]['name'])
		$accesslist[] = $access['table'][$right]['name'];

$ntype = array();
if ($userinfo['ntype'] & MSG_MAIL)
    $ntype[] = trans('email');
if ($userinfo['ntype'] & MSG_SMS)
    $ntype[] = trans('sms');
$userinfo['ntype'] = implode(', ', $ntype);

$layout['pagetitle'] = trans('User Info: $0', $userinfo['login']);

$SESSION->save('backto', $_SERVER['QUERY_STRING']);

$SMARTY->assign('userinfo', $userinfo);
$SMARTY->assign('accesslist', $accesslist);
$SMARTY->assign('excludedgroups', $DB->GetAll('SELECT g.id, g.name FROM customergroups g, excludedgroups 
					    WHERE customergroupid = g.id AND userid = ?
					    ORDER BY name', array($userinfo['id'])));

$SMARTY->display('userinfo.html');

?>
