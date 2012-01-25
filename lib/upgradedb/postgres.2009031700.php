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
 *  $Id: postgres.2009031700.php,v 1.4 2011/01/18 08:12:16 alec Exp $
 */

$DB->BeginTrans();

$DB->Execute("UPDATE uiconfig SET section = 'mail'
	WHERE section = 'phpui' AND var IN ('debug_email', 'smtp_port', 'smtp_host', 'smtp_username', 'smtp_password', 'smtp_auth_type')
");

$DB->Execute('UPDATE dbinfo SET keyvalue = ? WHERE keytype = ?', array('2009031700', 'dbversion'));

$DB->CommitTrans();

?>
