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
 *  $Id: postgres.2004071400.php,v 1.12 2011/01/18 08:12:12 alec Exp $
 */

$DB->Execute("
    BEGIN;
    ALTER TABLE stats ADD down bigint;
    ALTER TABLE stats ADD up bigint;
    ALTER TABLE stats ALTER down SET DEFAULT 0;
    ALTER TABLE stats ALTER up SET DEFAULT 0;
    UPDATE stats SET up=upload;
    UPDATE stats SET down=download;
    ALTER TABLE stats DROP upload;
    ALTER TABLE stats DROP download;
    ALTER TABLE stats RENAME up TO upload;
    ALTER TABLE stats RENAME down TO download;
    
    UPDATE dbinfo SET keyvalue = '2004071400' WHERE keytype = 'dbversion';
    COMMIT;
");

?>