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
 *  $Id: promotionset.php,v 1.1 2011/03/02 10:31:05 alec Exp $
 */

$id = isset($_GET['id']) ? $_GET['id'] : 0;

if($id)
{
	$DB->Execute('UPDATE promotions SET disabled = ? WHERE id = ?',
	    array(!empty($_GET['access']) ? 0 : 1, $id));
}

header('Location: ?'.$SESSION->get('backto'));

?>