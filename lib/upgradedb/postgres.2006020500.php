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
 *  $Id: postgres.2006020500.php,v 1.7 2011/01/18 08:12:14 alec Exp $
 */

$DB->BeginTrans();

$DB->Execute("
    ALTER TABLE invoicecontents ADD COLUMN discount numeric(4,2);
    UPDATE invoicecontents SET discount = 0;
    ALTER TABLE invoicecontents ALTER discount SET NOT NULL;
    ALTER TABLE invoicecontents ALTER discount SET DEFAULT 0;
");    

$DB->Execute("UPDATE dbinfo SET keyvalue = ? WHERE keytype = ?",array('2006020500', 'dbversion'));

$DB->CommitTrans();

?>
