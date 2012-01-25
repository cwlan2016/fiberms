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
 *  $Id: mysql.2009091000.php,v 1.4 2011/01/18 08:12:11 alec Exp $
 */

$DB->Execute("DROP VIEW nas");

$DB->Execute("ALTER TABLE netdevices CHANGE description description text NOT NULL DEFAULT ''");

$DB->Execute("CREATE VIEW nas AS 
        SELECT n.id, inet_ntoa(n.ipaddr) AS nasname, d.shortname, d.nastype AS type,
	d.clients AS ports, d.secret, d.community, d.description 
        FROM nodes n 
        JOIN netdevices d ON (n.netdev = d.id) 
        WHERE n.nas = 1");

$DB->Execute("UPDATE dbinfo SET keyvalue = ? WHERE keytype = ?", array('2009091000', 'dbversion'));

?>