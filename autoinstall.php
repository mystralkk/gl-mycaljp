<?php
// Reminder: always indent with 4 spaces (no tabs).
// +---------------------------------------------------------------------------+
// | Site Calendar - Mycaljp Plugin for Geeklog                                |
// +---------------------------------------------------------------------------+
// | plugins/mycaljp/autoinstall.php                                           |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2000-2009 by the following authors:                         |
// | Geeklog Author:        Tony Bibbs - tony AT tonybibbs DOT com             |
// | mycal Block Author:    Blaine Lang - geeklog AT langfamily DOT ca         |
// | mycaljp Plugin Author: dengen - taharaxp AT gmail DOT com                 |
// | Original PHP Calendar by Scott Richardson - srichardson@scanonline.com    |
// +---------------------------------------------------------------------------+
// |                                                                           |
// | This program is free software; you can redistribute it and/or             |
// | modify it under the terms of the GNU General Public License               |
// | as published by the Free Software Foundation; either version 2            |
// | of the License, or (at your option) any later version.                    |
// |                                                                           |
// | This program is distributed in the hope that it will be useful,           |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of            |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the             |
// | GNU General Public License for more details.                              |
// |                                                                           |
// | You should have received a copy of the GNU General Public License         |
// | along with this program; if not, write to the Free Software Foundation,   |
// | Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.           |
// |                                                                           |
// +---------------------------------------------------------------------------+

function plugin_autoinstall_mycaljp($pi_name)
{
    $pi_name         = 'mycaljp';
    $pi_display_name = 'Mycaljp';
    $pi_admin        = $pi_display_name . ' Admin';

    $info = array(
        'pi_name'         => $pi_name,
        'pi_display_name' => $pi_display_name,
        'pi_version'      => '2.1.2',
        'pi_gl_version'   => '1.6.0',
        'pi_homepage'     => 'http://www.trybase.com/~dengen/log/'
    );

    $groups = array(
        $pi_admin => 'Has full access to ' . $pi_display_name . ' features'
    );

    $features = array(
        $pi_name . '.admin'  => 'Mycaljp Admin'
    );

    $mappings = array(
        $pi_name . '.admin'  => array($pi_admin)
    );

    $tables = array();

    $inst_parms = array(
        'info'      => $info,
        'groups'    => $groups,
        'features'  => $features,
        'mappings'  => $mappings,
        'tables'    => $tables
    );

    return $inst_parms;
}

function plugin_load_configuration_mycaljp($pi_name)
{
    global $_CONF;

    $base_path = $_CONF['path'] . 'plugins/' . $pi_name . '/';

    require_once $_CONF['path_system'] . 'classes/config.class.php';
    require_once $base_path . 'install_defaults.php';

    return plugin_initconfig_mycaljp();
}

function plugin_postinstall_mycaljp($pi_name)
{
    global $_TABLES;

    // Add the "Site Calendar" block
//  $inst_parms = plugin_autoinstall_mycaljp($pi_name);
//  $pi_admin = key($inst_parms['groups']);
//  $admin_group_id = DB_getItem($_TABLES['groups'], 'grp_id', "grp_name = '{$pi_admin}'");
    $blockadmin_id  = DB_getItem($_TABLES['groups'], 'grp_id', "grp_name = 'Block Admin'");

    $result = DB_query( "SELECT COUNT(*) AS c FROM " . $_TABLES['blocks'] . " WHERE phpblockfn = 'phpblock_mycaljp2'" );
    $A = DB_fetchArray ($result);
    if ( $A["c"] < 1 ) {
        DB_query( "INSERT INTO " . $_TABLES['blocks'] 
        . " ( is_enabled, name, type, title, tid, blockorder, onleft, phpblockfn, owner_id, group_id ) "
        . "VALUES ( 1, 'mycaljp', 'phpblock', 'Site Calendar', 'all', 1, 1, 'phpblock_mycaljp2', 2, $blockadmin_id )", 1 );
        if ( DB_error() ) {
            COM_errorLog( 'failed insert blocks table', 1 );
            return false;
        }
    }

    return true;
}

function plugin_compatible_with_this_version_mycaljp($pi_name)
{
    if (function_exists('COM_printUpcomingEvents')) {
        // if this function exists, then someone's trying to install the
        // plugin on Geeklog 1.4.0 or older - sorry, but that won't work
        return false;
    }   

    if (!function_exists('MBYTE_strpos')) {
        // the plugin requires the multi-byte functions
        return false; 
    }   

    if (!function_exists('COM_createLink')) {
        return false;
    }

    if (!function_exists('SEC_createToken')) {
        return false;
    }

    if (!function_exists('COM_showMessageText')) {
        return false;
    }

    return true;
}

?>
