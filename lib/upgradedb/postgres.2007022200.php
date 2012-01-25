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
 *  $Id: postgres.2007022200.php,v 1.6 2011/01/18 08:12:14 alec Exp $
 */

$DB->BeginTrans();

$DB->Execute("
    CREATE INDEX rtmessages_ticketid_idx ON rtmessages (ticketid);

    CREATE SEQUENCE \"rtnotes_id_seq\";
    CREATE TABLE rtnotes (
	id integer default nextval('rtnotes_id_seq'::text) NOT NULL,
	ticketid integer      DEFAULT 0 NOT NULL,
        userid integer        DEFAULT 0 NOT NULL,
	body text             DEFAULT '' NOT NULL,
	createtime integer    DEFAULT 0 NOT NULL,
	PRIMARY KEY (id)
    );
			  
    CREATE INDEX rtnotes_ticketid_idx ON rtnotes (ticketid);
");

$DB->Execute("UPDATE dbinfo SET keyvalue = ? WHERE keytype = ?", array('2007022200', 'dbversion'));

$DB->CommitTrans();

?>
