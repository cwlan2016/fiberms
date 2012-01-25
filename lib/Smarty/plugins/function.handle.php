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
 *  $Id: function.handle.php,v 1.7 2011/01/18 08:12:06 alec Exp $
 */

/*
    Returns registered template plugin(s) output.
    Example of use: 
    
    {handle name="nodeinfobox-end"}
*/
function smarty_function_handle($args, &$SMARTY)
{
	global $PLUGINS;  // or maybe $SMARTY->_tpl_vars['PLUGINS'] assigned by ref.
	
	$result = '';
	if(isset($PLUGINS[$args['name']]))
		foreach($PLUGINS[$args['name']] as $plugin)
			$result .= $SMARTY->fetch($plugin);

	return $result;
}

?>