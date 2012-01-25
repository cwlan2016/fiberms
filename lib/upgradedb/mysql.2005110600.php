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
 *  $Id: mysql.2005110600.php,v 1.9 2011/01/18 08:12:09 alec Exp $
 */

$DB->Execute("ALTER TABLE assignments CHANGE at at int(11) NOT NULL DEFAULT '0'");
$DB->Execute("ALTER TABLE assignments ADD liabilityid int(11) NOT NULL DEFAULT '0'");

$DB->Execute("CREATE TABLE liabilities (
	id int(11)		NOT NULL auto_increment,
	value decimal(9,2) 	NOT NULL DEFAULT '0',
	name text		NOT NULL DEFAULT '',
	taxid int(11)		NOT NULL DEFAULT '0',
	prodid varchar(255)	NOT NULL DEFAULT '',
	PRIMARY KEY (id)
	) TYPE=MyISAM	
");

$DB->Execute("UPDATE dbinfo SET keyvalue = ? WHERE keytype = ?",array('2005110600', 'dbversion'));

?>
