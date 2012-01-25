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
 *  $Id: postgres.2005101700.php,v 1.11 2011/01/18 08:12:14 alec Exp $
 */

$DB->BeginTrans();

//$lastupgrade = $DB->GetOne("SELECT keyvalue FROM dbinfo where keytype='dbversion'");

// we have 2005092900 (1.7.3) database - it was wrong upgrade
// so we need to fix something
if($lastupgrade == '2005092900')
{
	$DB->Execute("ALTER TABLE cash ADD COLUMN type smallint");
	$DB->Execute("UPDATE cash SET type = 0");
	$DB->Execute("ALTER TABLE cash ALTER type SET NOT NULL");
	$DB->Execute("ALTER TABLE cash ALTER type SET DEFAULT 0");
	// set type for network operations
	$DB->Execute("UPDATE cash SET type = 1 WHERE customerid = 0");
}
else
{
	$DB->Execute("UPDATE cash SET value = -value WHERE type = 2 OR type = 4");
	$DB->Execute("UPDATE cash SET customerid = 0 WHERE type = 1 OR type = 2");
	$DB->Execute("UPDATE cash SET type = 1 WHERE type < 4");
	$DB->Execute("UPDATE cash SET type = 0 WHERE type != 1"); // "type!=1" <-> "type=4"
}

// "type" values after change: 
// 1 - payments e.g. cash payments
// 0 - liabilities e.g. invoices

$DB->Execute("UPDATE dbinfo SET keyvalue = ? WHERE keytype = ?",array('2005101700', 'dbversion'));

$DB->CommitTrans();

?>
