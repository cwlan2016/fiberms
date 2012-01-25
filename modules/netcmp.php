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
 *  $Id: netcmp.php,v 1.39 2011/01/18 08:12:23 alec Exp $
 */

if(!$LMS->NetworkExists($_GET['id']))
{
	$SESSION->redirect('?m=netlist');
}

$network['name'] = $LMS->GetNetworkName($_GET['id']);

if($_GET['is_sure'])
{
	$LMS->NetworkCompress($_GET['id']);
	$SESSION->redirect('?m='.$SESSION->get('lastmodule').'&id='.$_GET['id']);
}else{
	$layout['pagetitle'] = trans('Readdressing Network $0', strtoupper($network['name']));
	$SMARTY->display('header.html');
	echo '<H1>'.trans('Readdressing network $0', strtoupper($network['name'])).'</H1>';
	echo '<P>'.trans('Are you sure, you want to reorder that network?').'</P>';
	echo '<A href="?m=netcmp&id='.$_GET['id'].'&is_sure=1">'.trans('Yes, I am sure.').'</A>';
	$SMARTY->display('footer.html');
}

?>