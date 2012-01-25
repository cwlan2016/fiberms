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
 *  $Id: tariffdel.php,v 1.9 2011/03/02 10:31:05 alec Exp $
 */

$id = intval($_GET['id']);

if($id && $_GET['is_sure']=="1" && $LMS->TariffExists($id))
{
	if(!$DB->GetOne('SELECT 1 FROM assignments WHERE tariffid = ? LIMIT 1', array($id)))
		$LMS->TariffDelete($id);
}

$SESSION->redirect('?m=tarifflist');

?>