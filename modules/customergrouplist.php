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
 *  $Id: customergrouplist.php,v 1.11 2011/01/18 08:12:21 alec Exp $
 */

$layout['pagetitle'] = trans('Customer Groups List');

$customergrouplist = $LMS->CustomergroupGetList();

$listdata['total'] = $customergrouplist['total'];
$listdata['totalcount'] = $customergrouplist['totalcount'];

unset($customergrouplist['total']);
unset($customergrouplist['totalcount']);

$SMARTY->assign('customergrouplist', $customergrouplist);
$SMARTY->assign('listdata', $listdata);
$SMARTY->display('customergrouplist.html');

?>
