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
 *  $Id: mysql.2010050600.php,v 1.3 2011/01/18 08:12:11 alec Exp $
 */

$DB->BeginTrans();

$DB->Execute("
	CREATE TABLE macs (
		id		int(11)		NOT NULL auto_increment,
		mac		varchar(17)	DEFAULT '' NOT NULL,
		nodeid		int(11)		NOT NULL,
		PRIMARY KEY (id),
		FOREIGN KEY (nodeid) REFERENCES nodes (id) ON DELETE CASCADE ON UPDATE CASCADE,
		UNIQUE KEY mac (mac, nodeid)
	) ENGINE=InnoDB
");

$DB->Execute("INSERT INTO macs (mac, nodeid) SELECT mac, id FROM nodes");

$DB->Execute("ALTER TABLE nodes DROP mac");

$DB->Execute("UPDATE dbinfo SET keyvalue = ? WHERE keytype = ?", array('2010050600', 'dbversion'));

$DB->CommitTrans();

?>
