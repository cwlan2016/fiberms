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
 *  $Id: postgres.2007041200.php,v 1.7 2011/01/18 08:12:15 alec Exp $
 */

$DB->BeginTrans();

$DB->Execute("ALTER TABLE cashreglog ADD snapshot numeric(9,2)");

$list = $DB->GetAll('SELECT id, regid, time FROM cashreglog');

if($list) foreach($list as $row)
{    
	$val = $DB->GetOne('SELECT SUM(value) FROM receiptcontents
	                LEFT JOIN documents ON (docid = documents.id)
			WHERE cdate <= ? AND regid = ?',
			array($row['time'], $row['regid']));

	$DB->Execute('UPDATE cashreglog SET snapshot = ? WHERE id = ?',  
			array(str_replace(',','.',floatval($val)), $row['id']));
}

$DB->Execute("ALTER TABLE cashreglog ALTER snapshot SET NOT NULL");
$DB->Execute("ALTER TABLE cashreglog ALTER snapshot SET DEFAULT 0");

$DB->Execute("UPDATE dbinfo SET keyvalue = ? WHERE keytype = ?",array('2007041200', 'dbversion'));

$DB->CommitTrans();

?>
