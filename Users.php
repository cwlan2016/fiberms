<?php

require_once("auth.php");
require_once("smarty.php");
require "backend/Users.php";

function print_m($msg) {
    print "<table id='contable'>
    <tr>
        <td>
            $msg
        </td>
    </tr>
</table>";

}

if ($_SESSION['class'] != 1) {
    $smarty->assign("message", "Недостаточно прав!<br /><a href='".$_SERVER['HTTP_REFERER']."'>Назад</a>" );
    $smarty->display( 'message.tpl' );
    die();
}

if ( $_SERVER[ "REQUEST_METHOD" ] == 'POST' )
{
    if ( $_POST[ 'mode' ] == 1 )
    {
        $userId = $_POST[ 'userid' ];
        if ( $userId == 0 )
        {
            $res = Users_SELECT( 'id LIMIT 1', '' );
            $userId = $res[ 'rows' ][ 0 ][ 'id' ];
        }
        $wr[ 'id' ] = $userId;
        $res = Users_SELECT( '', $wr );
        $rows = $res[ 'rows' ];

        $smarty->assign( "id", $rows[ 0 ][ 'id' ] );
        $smarty->assign( "login", $rows[ 0 ][ 'username' ] );
        $smarty->assign( "password", '' );
        $class = $rows[ 0 ][ 'class' ];
        $res = Users_SELECT( '', '' );
        $rows = $res[ 'rows' ];
        $i = -1;
        while ( ++$i < $res[ 'count' ] )
        {
            $comboBox_Users_Values[ ] = $rows[ $i ][ 'id' ];
            $comboBox_Users_Text[ ] = $rows[ $i ][ 'username' ];
        }
        $smarty->assign( "combobox_users_values", $comboBox_Users_Values );
        $smarty->assign( "combobox_users_text", $comboBox_Users_Text );
        $smarty->assign( "combobox_users_selected", $userId );
        $smarty->assign( "combobox_usergroup_values", array( "1", "2" ) );
        $smarty->assign( "combobox_usergroup_text", array( "Админ", "ReadOnly" ) );
        $smarty->assign( "combobox_usergroup_selected", $class );
        $smarty->display( 'Users_content.tpl' );
    }
    elseif ( $_POST[ 'mode' ] == 2 )
    {
        $id = $_POST[ 'userid' ];
        $login = $_POST[ 'login' ];
        $password = $_POST[ 'password' ];
        $group = $_POST[ 'group' ];
        if ( $_POST[ 'rb' ] == 'true' )
        {
            $wr[ 'id' ] = $id;
            $upd[ 'username' ] = $login;
            $upd[ 'class' ] = $group;
            if ( $password != '' )
            {
                $upd[ 'password' ] = md5( $password );
            }
            Users_UPDATE( $upd, $wr );
            print_m("Пользователь изменен!<br />
				<a href=\"Users.php\">Назад</a>" );
        }
        else
        {
            $wr[ 'username' ] = $login;
            $res = Users_SELECT( '', $wr );
            if ( $res[ 'count' ] > 0 )
            {
                print_m("Пользователь с такими логином существует!<br />
				      <a href=\"Users.php\">Назад</a>" );
            }
            $ins[ 'username' ] = $login;
            $ins[ 'password' ] = md5( $password );
            $ins[ 'class' ] = $group;
            Users_INSERT( $ins );
            print_m("Пользователь добавлен!<br />
			<a href=\"Users.php\">Назад</a>" );
        }
    }
}
else
{
    $smarty->assign( "id", '' );
    $smarty->assign( "login", '' );
    $smarty->assign( "password", '' );
    $smarty->assign( "combobox_usergroup_values", '' );
    $smarty->assign( "combobox_usergroup_text", '' );
    $smarty->assign( "combobox_usergroup_selected", '' );
    $smarty->assign( "combobox_users_selected", '' );
    $smarty->assign( "combobox_users_values", '' );
    $smarty->assign( "combobox_users_text", '' );
    $smarty->display( 'Users.tpl' );
}
?>