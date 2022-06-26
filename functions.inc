<?php
// Reminder: always indent with 4 spaces (no tabs).
// +---------------------------------------------------------------------------+
// | Site Calendar - Mycaljp Plugin for Geeklog                                |
// +---------------------------------------------------------------------------+
// | plugins/mycaljp/functions.inc                                             |
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

if (strpos(strtolower($_SERVER['PHP_SELF']), 'functions.inc') !== false) {
    die ('This file can not be used on its own.');
}

if (!defined('GL_VERSION_15')) {
    define('GL_VERSION_15', (version_compare(VERSION, '1.5') >= 0));
}

if (!defined(CSRF_TOKEN)) {
    define('CSRF_TOKEN', 'token');
}

/**
* Language file Include
*/
$langfile = $_CONF['path'] . 'plugins/mycaljp/language/'
          . $_CONF['language'] . '.php';

if ( file_exists( $langfile ) ) {
    include_once $langfile;
} else {
    include_once $_CONF['path'] . 'plugins/mycaljp/language/english.php';
}

/*
* Include mycaljp config file
*/
//require_once $_CONF['path'] . 'plugins/mycaljp/config.php';
//$_MYCALJP2_CONF = array();

/**
* Check and see if we need to load the plugin configuration.
*/
/*
require_once $_CONF['path_system'] . 'classes/config.class.php';
$plg_config = config::get_instance();
$temp = $plg_config->get_config('mycaljp');
if (is_array($temp)) {
    $_MYCALJP2_CONF = array_merge($_MYCALJP2_CONF, $temp);
}
*/

$_MYCALJP2_CONF = MYCALJP_loadConfig();


// +---------------------------------------------------------------------------+
// | Geeklog Plugin API Implementations                                        |
// +---------------------------------------------------------------------------+


/**
* This will put an option for the plugin in the command and control block on moderation.php
*
* Add the plugin name, icon and link to the command and control block in moderation.php
*
* @return   array   Array containing (plugin name, admin url, url of plugin icon)
*
*/

function plugin_cclabel_mycaljp()
{
    global $_CONF;

    $pi_name   = ucfirst( 'mycaljp' );
    $admin_url = '';
    $icon_url  = $_CONF['site_url'] . '/mycaljp/images/mycaljp.png';

    return array ( $pi_name, $admin_url, $icon_url );
}

/**
* Returns the administrative option for this plugin
*
* Adds the plugin to the Admin menu
*
* @return   array   Array containing (plugin name, plugin admin url, # of items in plugin or '')
*
*/
/*
function plugin_getadminoption_mycaljp()
{
    global $_CONF, $_TABLES;

    if ( SEC_hasRights( 'mycaljp.admin' ) ) {
        $pi_name   = ucfirst( 'mycaljp' );
        $admin_url = $_CONF['site_admin_url'] . '/plugins/mycaljp/index.php';
        $num_items = '';
        return array ( $pi_name, $admin_url, $num_items );
    }
}
*/

/**
 * Returns the version for this plugin
 *
 * @return string VersionNo
 */

function plugin_chkVersion_mycaljp()
{
//    global $_MYCALJP2_CONF;
//    return $_MYCALJP2_CONF['pi_version'];

    global $_CONF;

    require_once $_CONF['path'] . 'plugins/mycaljp/autoinstall.php';

    $inst_parms = plugin_autoinstall_mycaljp('mycaljp');

    return $inst_parms['info']['pi_version'];
}

/**
* Upgrade the plugin
*
* @return   boolean true (= success)
*
*/
/*
function plugin_upgrade_mycaljp()
{
    global $_CONF, $_TABLES, $_MYCALJP2_CONF;

    $result = DB_query( "SELECT pi_version FROM {$_TABLES['plugins']} WHERE (pi_name = 'mycaljp')" );
    $A = DB_fetchArray( $result );
    $pi_version = $A['pi_version'];     // Version info stored in database
    
    // Update plugin info
    
    $result = DB_query( "UPDATE {$_TABLES['plugins']} SET pi_version = \"{$_MYCALJP2_CONF['pi_version']}\" WHERE (pi_name = 'mycaljp')" );
    COM_errorLog( ucfirst( "mycaljp" ) . " plugin was successfully updated to version {$_MYCALJP2_CONF['pi_version']}." );
    
    return true;
}
*/
function plugin_upgrade_mycaljp()
{
    global $_CONF, $_TABLES, $_MYCALJP2_DEFAULT;

    $installed_version = DB_getItem($_TABLES['plugins'], 'pi_version',
                                    "pi_name = 'mycaljp'");
    $code_version = plugin_chkVersion_mycaljp();
    if ($installed_version == $code_version) {
        // nothing to do
        return true;
    }

    require_once $_CONF['path'] . 'plugins/mycaljp/autoinstall.php';

    if (! plugin_compatible_with_this_version_mycaljp('mycaljp')) {
        return 3002;
    }

    if (version_compare($installed_version, '2.1.0') < 0) {
        require_once $_CONF['path'] . 'plugins/mycaljp/install_defaults.php';
        plugin_initconfig_mycaljp();
    }

    $inst_parms = plugin_autoinstall_mycaljp('mycaljp');
    $pi_gl_version = $inst_parms['info']['pi_gl_version'];

    DB_query("UPDATE {$_TABLES['plugins']} SET pi_version = '$code_version', "
           . "pi_gl_version = '$pi_gl_version' WHERE pi_name = 'mycaljp'");

    return true;
}


/**
* Automatic uninstall function for plugins
*
* @return   array
*
* This code is automatically uninstalling the plugin.
* It passes an array to the core code function that removes
* tables, groups, features and php blocks from the tables.
* Additionally, this code can perform special actions that cannot be
* foreseen by the core code (interactions with other plugins for example)
*
*/
function plugin_autouninstall_mycaljp ()
{
    global $_CONF;

    $out = array (
        'tables'     => array(),
        'groups'     => array('Mycaljp Admin'),
        'features'   => array('mycaljp.admin'),
        'php_blocks' => array('phpblock_mycaljp2'),
        'vars'       => array()
    );

    $fname = $_CONF['path_data'] . 'mycaljp_conf.php';
    if ( file_exists( $fname ) ) {
        @unlink( $fname );
    }

    return $out;
}

/**
* Removes the datastructures for this plugin from the Geeklog database.
* This routine will get called from the Plugin install program if user select De-Install or if Delete is used in the Plugin Editor.
* The Plugin Installer will also call this routine upon and install error to remove anything it has created.
* The Plugin installer will pass the optional parameter which will then double check that plugin has first been disabled. 
* 
* For this plugin, this routine will also remove the Block definition.
* 
* Returns True if all Plugin related data is removed without error
*
* @param    string   $installCheck     Default is blank but if set, check if plugin is disabled first
* 
* @return   boolean True if successful false otherwise
*
*/  
/*
function plugin_uninstall_mycaljp ( $installCheck = '' )
{
    global $_TABLES, $LANG_MYCALJP, $_CONF;

    $pi_name  = 'mycaljp';
    $pi_admin = 'Mycaljp Admin';

    // $FEATURES and $TABLES have to be changed accodrding to your plugin
    $FEATURES = array ( 'mycaljp.admin' );
    $TABLES   = array ();
    
    // Check and see if plugin is still enabled - if so display warning and exit
    if ( $installCheck != '' && DB_getItem ( $_TABLES['plugins'], 'pi_enabled', 'pi_name = "' . $pi_name . '"' ) ) {
        COM_errorLog ( "Plugin is installed and enabled. Disable first if you want to de-install it", 1 );
        $display .= COM_startBlock ( $LANG_MYCALJP['warning'] );
        $display .= $LANG_MYCALJP['enabled'];
        $display .= COM_endBlock();
        echo $display;
        return false;
        exit;
    }
        
    // Ok to proceed and delete plugin

    // Drop mycaljp tables
    foreach ( $TABLES as $table ) {
        $t = $_TABLES["$table"];
        COM_errorLog ('Dropping $table table', 1);
        DB_query ( "DROP TABLE $t", 1 );
        COM_errorLog ('...success', 1);
    }

    // Remove Security for this plugin
    $grp_id = DB_getItem ($_TABLES['groups'], 'grp_id',"grp_name = '$pi_admin'");
    if (!empty ($grp_id)) {
        // Remove Mycaljp Admin group from all other groups
        COM_errorLog ('Attempting to remove $pi_admin group from all groups' , 1);
        DB_query ("DELETE FROM {$_TABLES['group_assignments']} WHERE ug_main_grp_id = $grp_id");
        COM_errorLog ('...success', 1);

        // Remove the Mycaljp Admin group
        COM_errorLog('Attempting to remove the $pi_admin Group', 1);
        DB_query ("DELETE FROM {$_TABLES['groups']} WHERE grp_id = $grp_id");
        COM_errorLog('...success', 1);
    }

    // Remove the block
    DB_query( "DELETE FROM {$_TABLES['blocks']} WHERE phpblockfn = 'phpblock_mycaljp2'", 1 );
    
    // Remove related features
    foreach ($FEATURES as $f) {
        $feat_id = DB_getItem ($_TABLES['features'], 'ft_id', "ft_name = '$f'");
        if (!empty ($feat_id)) {
            COM_errorLog ("Attempting to remove $f rights from all groups", 1);
            DB_query ("DELETE FROM {$_TABLES['access']} WHERE acc_ft_id = $feat_id");
            COM_errorLog ('...success', 1);

            COM_errorLog ("Attempting to remove the $f feature", 1);
            DB_query ("DELETE FROM {$_TABLES['features']} WHERE ft_id = $feat_id");
            COM_errorLog ('...success', 1);
        }
    }
    
    $fname = $_CONF['path_data'] . 'mycaljp_conf.php';
    if ( file_exists( $fname ) ) {
        @unlink( $fname );
    }
    
    if (GL_VERSION_15) {
        // Remove config table data for this plugin
        COM_errorLog ("Attempting to remove config table records for group_name: $pi_name", 1);
        DB_query ("DELETE FROM {$_TABLES['conf_values']} WHERE group_name = '$pi_name'");
        COM_errorLog ('...success', 1);
    }

    // Unregister the plugin with Geeklog
    // Always attempt to remove these entries or lib-common.php would still
    // try and read our functions.inc file ...
    COM_errorLog ('Attempting to unregister the $pi_name plugin from Geeklog', 1);
    DB_query ("DELETE FROM {$_TABLES['plugins']} WHERE pi_name = '$pi_name'");
    COM_errorLog ('...success',1);

    COM_errorLog ('Finished uninstalling the $pi_name plugin.', 1);
    
    return true;
}
*/

/**
* Callback function when configuration was changed
*
* @param    string  $group      'mycaljp'
* @param    array   $changes    array of strings: config var key
* @return   void
* @see      PLG_configChange
*/

function plugin_configchange_mycaljp($group, $changes = array())
{
    global $_MYCALJP2_CONF;

    if ($group != 'mycaljp') return;
    if (!is_array($changes)) return;
    if (count($changes) == 0) return;

    /**
    * At this time, changes in configuration is not reflected in
    * $_MYCALJP2_CONF yet.  So we have to reload them from DB.
    */
    $_MYCALJP2_CONF = MYCALJP_loadConfig();
    foreach ($changes as $name) MYCALJP_checkChange($name);
}


/**
* Loads config infor with config.class.php
*
* @return   mixed   Mycaljp config array or FALSE
*/
function MYCALJP_loadConfig()
{
    global $_CONF;

    require_once $_CONF['path_system'] . 'classes/config.class.php';
    $config = config::get_instance();
    return $config->get_config('mycaljp');
}

/**
* Check if changed types, priorities, or frequencies are valid
*
* @param  string    $name   either 'supported_contents' or 'enabled_contents'
* @return void
*/
function MYCALJP_checkChange($name)
{
    global $_CONF, $_PLUGINS, $_TABLES, $_MYCALJP2_CONF;

    $valids = array();

    require_once $_CONF['path'] . 'plugins/mycaljp/install_defaults.php';

    // Check and add a valid value to $valids
    switch ($name) {
        case 'supported_contents':
            foreach ($_MYCALJP2_CONF[$name] as $value) {
                if (!in_array($value, $_MYCALJP2_DEFAULT[$name])) continue;
                $valids[] = $value;
            }
            break;

        case 'enabled_contents':
            foreach ($_MYCALJP2_CONF['supported_contents'] as $value) {
                $valids[$value] = 0;
                if (array_key_exists($value, $_MYCALJP2_CONF[$name])) {
                    $valids[$value] = $_MYCALJP2_CONF[$name][$value];
                    if ($valids[$value] !== 1) $valids[$value] = 0;
                }
            }
        
            break;
    }

    require_once $_CONF['path_system'] . 'classes/config.class.php';
    $config = config::get_instance();
    if (!empty($valids)) {
        $config->set($name, $valids, 'mycaljp');
    }
    
    switch ($name) {
        case 'supported_contents':
            $enabled_contents = array();
            foreach ($_MYCALJP2_CONF[$name] as $value) {
                $enabled_contents[$value] = 1;
                if (array_key_exists($value, $_MYCALJP2_CONF['enabled_contents'])) {
                    $enabled_contents[$value] = $_MYCALJP2_CONF['enabled_contents'][$value];
                }
            }
            $config->set('enabled_contents', $enabled_contents, 'mycaljp');
            break;
    }

}





// +-------------------------------------------------------------------------+
// | Main Program                                                            |
// +-------------------------------------------------------------------------+

if ( empty($_MYCALJP2_CONF['template']) ) {
    $_MYCALJP2_CONF['template'] = 'default';
}

// テンプレートパスおよびCSSのURLを設定する
if ($_MYCALJP2_CONF['use_theme'] && is_dir($_CONF['path_layout'] . 'mycaljp')) {
    $_MYCALJP2_CONF['path_layout'] = $_CONF['path_layout'] . 'mycaljp';
    $_MYCALJP2_CONF['css_url'] = $_CONF['layout_url'] . '/mycaljp';
} else {
    $_MYCALJP2_CONF['path_layout'] = $_CONF['path_html'] . 'mycaljp/templates/' . $_MYCALJP2_CONF['template'];
    $_MYCALJP2_CONF['css_url'] = $_CONF['site_url'] . '/mycaljp/templates/' . $_MYCALJP2_CONF['template'];
}

// テンプレートのパスを設定する
// slidecalendarのために残す。
if ( ! function_exists('mycaljp_setlayoutpath') )
{
    function mycaljp_setlayoutpath(){}
}

function MYCALJP_setLayoutPath_old() // for slidecalendar
{
    global $_CONF, $_MYCALJP2_CONF;
    
    if ( is_dir( $_CONF['path_layout'] . 'mycaljp' ) ) {
      $_MYCALJP2_CONF['path_layout'] = $_CONF['path_layout'] . 'mycaljp';
      $_MYCALJP2_CONF['css_url'] = $_CONF['layout_url'] . '/mycaljp';
    } else {
      $_MYCALJP2_CONF['path_layout'] = $_CONF['path_html'] . 'mycaljp/templates/' . $_MYCALJP2_CONF['template'];
      $_MYCALJP2_CONF['css_url'] = $_CONF['site_url'] . '/mycaljp/templates/' . $_MYCALJP2_CONF['template'];
    }
}


// もし、テンプレートパス内に config.php があればインクルードする
@include($_MYCALJP2_CONF['path_layout'] . '/config.php');


function MYCALJP_getTemplates()
{
    global $_CONF, $_MYCALJP2_CONF;

    $templates = array();
    $index = 0;
    $path_mycaljp = $_CONF['path_html'] . 'mycaljp/templates/';
    $fd = opendir( $path_mycaljp );
    while( ( $dir = @readdir( $fd ) ) == true ) {
        if( is_dir( $path_mycaljp . $dir) && $dir <> '.' && $dir <> '..' && $dir <> 'default' && substr( $dir, 0 , 1 ) <> '.' ) {
            clearstatcache();
            $templates[$index] = $dir;
            $index++;
        }
    }
    sort($templates);
    $templates = array_merge( array('default'), $templates );

    return $templates;
}


/**
* Get header code for inclusion
*
* @return   string
*
*/
function plugin_getheadercode_mycaljp()
{
    global $_CONF, $_MYCALJP2_CONF;

    return '
<!-- Include CSS & Script for Site Calendar Mycaljp -->
<link rel="stylesheet" type="text/css" href="' . $_MYCALJP2_CONF['css_url'] . '/mycaljp.css"' . XHTML . '>
<script type="text/javascript" src="' . $_CONF['site_url'] . '/mycaljp/xmlhttp.js"></script>
';
}

define ( "_MYCAL_DAY_", 86400 ); // 60 * 60 * 24
define ( "_STORY_"  , 1 ); 
define ( "_ACTIVE_" , 2 ); 

function MYCALJP_showCalendar( $caltype = 1 )
{
    global $_TABLES, $_CONF, $_PLUGINS, $_USER, $_MYCALJP2_CONF, $LANG_MYCALJP;

    if ( isset( $_USER['uid'] ) AND ( $_USER['uid'] > 1 ) ) {
        $uid = $_USER['uid'];
    } else {
        $uid = 1;
    }

    setlocale(LC_TIME, $_CONF['locale']);

    $tmpmonth = $_GET['moon'];
    if( isset( $GLOBALS['WorkDate'] ) )
        $WorkDate = $GLOBALS['WorkDate'];
    else if( !isset($tmpmonth) ) 
        $WorkDate = date("Y-m-d");
    else {
        $WorkDate = $tmpmonth;
        $GLOBALS['WorkDate'] = $WorkDate;
    }
    
    // Determine the first and last day to display
    $base               = strtotime($WorkDate) + 60 * 60 * 12;
    $firstdayofmonth    = $base - ((date("d", $base) - 1) * _MYCAL_DAY_);
    $daysinmonth        = date("t", $firstdayofmonth);
    $lastdayofmonth     = $firstdayofmonth + (($daysinmonth - 1) * _MYCAL_DAY_);
    $firstdaytodisplay  = $firstdayofmonth - (strftime ('%w', $firstdayofmonth) * _MYCAL_DAY_);
    $lastdaytodisplay   = $lastdayofmonth + ((6 - strftime ('%w', $lastdayofmonth)) * _MYCAL_DAY_);
    $totaldays          = ((($lastdaytodisplay - $firstdaytodisplay) / _MYCAL_DAY_) + 1);
//  $totalweeks         = $totaldays / 7;
    $totalweeks         = 6; // 固定してみる
    
    $firststr = date("Y-m-d", $firstdaytodisplay);
    $laststr  = date("Y-m-d", $lastdaytodisplay);
    $ds = explode("-", $firststr);
    $de = explode("-", $laststr);
    $startdate = mktime( 0, 0, 0,$ds[1],$ds[2],$ds[0]);
    $enddate   = mktime(23,59,59,$de[1],$de[2],$de[0]);
    
    $thismonth = date("Y-m-01", $base);
    $prevmonth = date("Y-m-01", strtotime($thismonth . "-1 month"));
    $nextmonth = date("Y-m-01", strtotime($thismonth . "+1 month"));

    $firstdayofmonthstr = date("Y-m-d", $firstdayofmonth);
    $lastdayofmonthstr  = date("Y-m-d", $lastdayofmonth);

    $firstdayofyearstr  = date("Y-01-01", $base);
    $lastdayofyearstr   = date("Y-12-31", $base);


    $dataproxy =& new Dataproxy($uid);
    $drivers = $dataproxy->getAllDriverNames();
    $dataproxy->setDateStart($firststr);
    $dataproxy->setDateEnd($laststr);

    $days = array();

    if ( isset($_MYCALJP2_CONF['enabled_contents']) ) {
        foreach ($drivers as $driver) {

            $content = $driver;
            if ($driver=='article') $content = 'stories';
            if (!in_array($content, $_MYCALJP2_CONF['supported_contents'])) continue;
            if (!($_MYCALJP2_CONF['enabled_contents'][$content] == 1)) continue;
            $items = $dataproxy->$driver->getItemsByDate();
            if (count($items) == 0) continue;
            foreach ($items as $item) {
                $item_date = date("Y-m-d", $item['date']);
                switch ($driver) {
                    case 'article':
                        $days[$item_date] |= _STORY_;
                        break;

                    case 'calendar':
                    case 'calendarjp':
                        break;

                    default:
                        $days[$item_date] |= _ACTIVE_;
                        break;
                }
            }
        }

        $events = array();
        foreach ( $_MYCALJP2_CONF['enabled_contents'] as $pi_name => $val )
        {
            if ( in_array( $pi_name, $_PLUGINS ) )
            {
                switch ( $pi_name ) {
                case "calendar":
                case "calendarjp":
                    if ( ! MYCALJP_pi_enabled($pi_name) ) continue;
                    if ($pi_name == "calendar") {
                        $table = $_TABLES['events'];
                    } else if ($pi_name == "calendarjp") {
                        $table = $_TABLES['eventsjp'];
                    }
                    $sql = "SELECT eid,title,url,datestart,dateend,timestart,timeend,"
                         . "group_id,owner_id,perm_owner,perm_group,perm_members,perm_anon "
                         . "FROM {$table} "
                         . "WHERE (datestart >= '$firststr') AND (datestart <= '$laststr') "
                         . COM_getPermSQL('AND');
                    $result = DB_query($sql);
                    while ( $o = DB_fetchArray($result) ) {
                        $events[ $o['datestart'] ][ $o['eid'] ] = $o; // $o['datestart']は"Y-m-d"形式文字列
                        if ( $o['datestart'] != $o['dateend'] ) {
                            $e_ds = explode("-", $o['datestart']);
                            $e_de = explode("-", $o['dateend']);
                            $e_ts = explode(":", $o['timestart']);
                            $e_te = explode(":", $o['timeend']);
                            $event_startdate = mktime($e_ts[0],$e_ts[1],$e_ts[2],$e_ds[1],$e_ds[2],$e_ds[0]);
                            $event_enddate   = mktime($e_te[0],$e_te[1],$e_te[2],$e_de[1],$e_de[2],$e_de[0]);

                            if ( $event_enddate - $event_startdate >= _MYCAL_DAY_ ) {
                                $event_middledate = $event_startdate + _MYCAL_DAY_;
                                while ( $event_enddate >= $event_middledate ) {
                                    $events[ date( "Y-m-d", $event_middledate ) ][ $o['eid'] ] = $o;
                                    $event_middledate += _MYCAL_DAY_;
                                }
                                $events[ $o['dateend'] ][ $o['eid'] ] = $o;
                            }
                        }
                    }
                    
                    break;
                }
            }
        }
    }

    if ( $caltype == 2 ) { // for slidecalendar
        MYCALJP_setLayoutPath_old();
        // もし、テンプレートパス内に config.php があればインクルードする
        @include($_MYCALJP2_CONF['path_layout'] . '/config.php');
    }
    $cal = new Template( $_MYCALJP2_CONF['path_layout'] );
    $cal->set_file( array(
        'calendar' => 'calendar.thtml',
        'header'   => 'header.thtml',
        'week'     => 'week.thtml',
        'day'      => 'day.thtml'
    ));
    $cal->set_var ( 'site_url', $_CONF['site_url'] );

    if ( $_MYCALJP2_CONF['headertitleyear'] )
        $headertitleyear = date( $_MYCALJP2_CONF['headertitleyear'], $base );
    else
        $headertitleyear = ucfirst( strftime( ( substr($_CONF['locale'],0,2) == "ja" ) ? "%Y年" : "%Y", $base ) );
    $anchor = '<a href="' . $_CONF["site_url"] . '/mycaljp/calsearch.php?datestart=' . $firstdayofyearstr 
        . '&amp;dateend=' . $lastdayofyearstr . '&amp;type=all&amp;mode=search&amp;topic=0&amp;author=0&amp;moon=' . $thismonth
        . '" title="'. $LANG_MYCALJP['headeroflink'] . $headertitleyear . $LANG_MYCALJP['footeroflink'] . '">' . $headertitleyear . '</a>';
    $cal->set_var('title_' . ( ( $_MYCALJP2_CONF['titleorder'] == '1' ) ? '1' : '2' ) ,$anchor);
    $cal->set_var('title_year',$anchor); // for slidecalendar

    if ( $_MYCALJP2_CONF['headertitlemonth'] )
        $headertitlemonth = date( $_MYCALJP2_CONF['headertitlemonth'], $base );
    else
        $headertitlemonth = ucfirst( strftime( ( substr($_CONF['locale'],0,2) == "ja" ) ? "%m月" : "%B", $base ) );
    $anchor = '<a href="' . $_CONF["site_url"] . '/mycaljp/calsearch.php?datestart=' . $firstdayofmonthstr 
        . '&amp;dateend=' . $lastdayofmonthstr . '&amp;type=all&amp;mode=search&amp;topic=0&amp;author=0'
        . '" title="'. $LANG_MYCALJP['headeroflink'] . $headertitlemonth . $LANG_MYCALJP['footeroflink'] . '">' . $headertitlemonth . '</a>';
    $cal->set_var('title_' . ( ( $_MYCALJP2_CONF['titleorder'] == '1' ) ? '2' : '1' ) ,$anchor);
    $cal->set_var('title_month',$anchor); // for slidecalendar

    // Header with Month Title and Next/Prev links
    $qstr = preg_replace( '/[&]*moon=[0-9]{4}-[0-9]{2}-[0-9]{2}/', '', $_SERVER["QUERY_STRING"] );
    $qstr = MYCALJP_htmlspecialchars( $qstr );

    $url = MYCALJP_htmlspecialchars( $_SERVER['PHP_SELF'] );

    $cal->set_var('lang_skipcalendar', $LANG_MYCALJP['skipcalendar'] ); // I consider the visually impaired.

    $tmp = $url . "?" . $qstr . "&amp;moon=" . $prevmonth;
    $tmp = str_replace( '?&amp;', '?', $tmp );
    $cal->set_var('prevmonth', $tmp );
    
    $tmp = $url . "?" . $qstr . "&amp;moon=" . $nextmonth;
    $tmp = str_replace( '?&amp;', '?', $tmp );
    $cal->set_var('nextmonth', $tmp );

    $cal->set_var('testprevmonth', $_CONF["site_url"] . "/mycaljp/showcal.php" . "?moon=" . $prevmonth );
    $cal->set_var('testnextmonth', $_CONF["site_url"] . "/mycaljp/showcal.php" . "?moon=" . $nextmonth );

    $cal->set_var('lang_prevmonth', $LANG_MYCALJP['prevmonth'] );
    $cal->set_var('lang_nextmonth', $LANG_MYCALJP['nextmonth'] );

    $wdays = array('sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday');
    for ( $i = 0; $i < 7; $i++ )
    {
        $cal->set_var( 'weekday_class', $wdays[$i] );

        if ( $_MYCALJP2_CONF[ $wdays[$i] ] )
            $cal->set_var( 'title_weekday', $_MYCALJP2_CONF[ $wdays[$i] ] );
        else
            $cal->set_var( 'title_weekday', ucfirst(strftime("%a", $firstdaytodisplay + ($i * _MYCAL_DAY_))) );

        $cal->parse( 'dayofweeks_title', 'header', true );
    }

    for ( $w = 0; $w < $totalweeks; $w++ )
    {
        for ( $d = 0; $d < 7; $d++ )
        {
            $cal->set_var( 'daylink', MYCALJP_getDaysURL($events, $firstdaytodisplay + (_MYCAL_DAY_ * ($d + ($w * 7))), $base, $days, $d, $caltype) );
            $cal->parse( 'calendar_days','day', ($d > 0) );
        }
        $cal->parse( 'calendar_week', 'week', true );
    }

    return $cal->parse ( 'output', 'calendar' );
}


function MYCALJP_getDaysURL(&$events, $timestamp, $base, &$days, $weekday, $caltype=1)
{
    global $_TABLES, $_CONF, $_MYCALJP2_CONF, $CUSTOM_MOBILE_UA;

    $day = date( "j", $timestamp );
    $space = '';
    if ($CUSTOM_MOBILE_UA) {
        $day = substr('__' . $day, -2);
        $space = '--';
    }
    $key = date( "Y-m-d", $timestamp );
    $today = ( date("Y-m-d") == $key );
    $story  = ( ( $days[ $key ] & _STORY_  ) > 0 );
    $active = ( ( $days[ $key ] & _ACTIVE_ ) > 0 );
    $thismonth = ( date( "m", $timestamp ) == date( "m", $base ) );
    $sunday = false;
    $saturday = false;
    $holiday = false;

    if ( $_MYCALJP2_CONF['showholiday'] )
    {
        $sunday   = ( $weekday == 0 );
        $saturday = ( $weekday == 6 );
        if ( $_MYCALJP2_CONF['checkjpholiday'] )
            $holiday  = is_Japanese_Holiday($timestamp);
    }

    $event = '';
    if ( ( ! empty($events) ) && count( $events[$key] ) )
    {
        while ( list($k,$v) = each($events[$key]) )
        {
            if (SEC_hasAccess($v['owner_id'],$v['group_id'],$v['perm_owner'],$v['perm_group'],$v['perm_members'],$v['perm_anon']) > 0)
            {
                if ($event != '') $event .= ', ';
                $event .= $v['title'];
            }
        }
    }

    if ( $event || $active || $story )
    {
        $anchor = '<a href="' . $_CONF["site_url"] . '/mycaljp/calsearch.php?datestart=' . $key 
            . '&amp;dateend=' . $key . '&amp;type=all&amp;mode=search&amp;topic=0&amp;author=0';
        $anchor .= $event ? '" title="'. $event : '';
        $anchor .= '">' . $day . '</a>';
    }
    
    $holidayclass = '';
    if ( $sunday )
        $holidayclass = ' sunday';
    elseif ( $saturday )
        $holidayclass = ' saturday';
    if ( $holiday )
        $holidayclass .= ' holiday';

    if ( ( ! $thismonth ) && ( $caltype != 1 ) ) return ''; // for slidecalender

    if ( $event )
    {
        if ( ! $thismonth )
        {
            $event_url = '<td class="NotThisMonth">' . $space;
        } 
        else 
        {
            $event_url = '<td class="' . ( $today ? 'TodayEvent' : 'Event' ) . $holidayclass . '">' . $anchor;
        }
    }
    elseif ( $story )
    {
        if ( ! $thismonth )
        {
            $event_url = '<td class="NotThisMonth">' . $space;
        }
        else 
        {
            if ( $today )
            {
                $event_url = '<td class="Today' . $holidayclass . '">' . $day;

                if ( $story )
                {
                    $event_url = '<td class="TodayStory' . $holidayclass . '">' . $anchor; 
                }
            }
            else
            {
                $event_url = ( $holidayclass ? '<td class="' . ltrim($holidayclass) . '">' : '<td>' ) . $day;
                
                if ( $story )
                {
                    $event_url = '<td class="Story' . $holidayclass . '">' . $anchor; 
                }
            }
        }
    }
    else
    {
        if ( ! $thismonth )
        {
            $event_url = '<td class="NotThisMonth">' . $space;
        }
        else 
        {
            if ( $today )
            {
                $event_url = '<td class="Today' . $holidayclass . '">' . $day;

                if ( $active )
                {
                    $event_url = '<td class="TodayActive' . $holidayclass . '">' . $anchor; 
                }
            }
            else
            {
                $event_url = ( $holidayclass ? '<td class="' . ltrim($holidayclass) . '">' : '<td>' ) . $day;
                
                if ( $active )
                {
                    $event_url = '<td class="Active' . $holidayclass . '">' . $anchor; 
                }
            }
        }
    }

    $event_url .= "</td>";
        
    return $event_url;
}


//
// Unixタイムスタンプ形式の日付が日本の休日かどうか判定する関数
//
// 判定ロジックは次のサイトを参照しています。
// http://www.h3.dion.ne.jp/~sakatsu/holiday_logic.htm
//
function is_Japanese_Holiday($timestamp)
{
  $weekday = strftime("%w",$timestamp);  // 日(0),月(1)‥土(6)
  $year    = strftime("%Y",$timestamp);
  $month   = strftime("%m",$timestamp);
  $day     = strftime("%d",$timestamp);

  switch ( $month )
  {
      case 1:// 1月
          if ( $day == 1 ) return true; //元日
          if ( $day == 2 && $weekday == 1 ) return true; //振替休日
          if ( $year >= 2000 )
          {
              $week = floor( ( $day - 1 ) / 7 ) + 1;
              if ( $week == 2 && $weekday == 1 ) return true; //成人の日(第2月曜日)
          }
          break;
      
      case 2:// 2月
          if ( $year >= 1967 )
          {
              if ( $day == 11 ) return true; //建国記念の日
              if ( $day == 12 && $weekday == 1 ) return true; //振替休日
          }
          break;
      
      case 3:// 3月
          if( $year > 1979 && $year < 2100 )
          {
              $SpringEquinox = floor( 20.8431 + 0.242194 * ( $year - 1980 ) - floor( ( $year - 1980 ) / 4 ) );
              if ( $day == $SpringEquinox ) return true; //春分の日
              if ( ( $day == $SpringEquinox + 1 ) && $weekday == 1 ) return true; //振替休日
          }
          break;
      
      case 4:// 4月
          if ( $day == 29 ) return true; //昭和の日
          if ( $day == 30 && $weekday == 1 ) return true; //振替休日
          break;
      
      case 5:// 5月
          if ( $day == 3 ) return true; //憲法記念日
          if ( $day == 4 )
          {
              if ( $year >= 2007 ) return true; //みどりの日
              if ( $year >= 1986 )
              {
                  // 5/4が日曜日は『只の日曜』､月曜日は『憲法記念日の振替休日』(～2006年)
                  if ( $weekday > 1 ) return true; //国民の休日
              }
          }
          if ( $day == 5 ) return true; //こどもの日
          if ( $year >= 2007 )
          {
              if ( $day == 6 && ( $weekday == 2 || $weekday == 3 ) ) return true; //振替休日
          }
          break;

      case 7:// 7月
          if ( $year >= 2003 )
          {
              $week = floor( ( $day - 1 ) / 7 ) + 1;
              if ( $week == 3 && $weekday == 1 ) return true; //海の日(第3月曜日)
          }
          elseif ( $year >= 1996 )
          {
              if ( $day == 20 ) return true; //海の日
          }
          break;
      
      case 9:// 9月
          $AutumnEquinox = floor( 23.2488 + 0.242194 * ( $year - 1980 ) - floor( ( $year - 1980 ) / 4 ) );
          if( $year > 1979 && $year < 2100 )
          {
              if ( $day == $AutumnEquinox ) return true; //秋分の日
              if ( ( $day == $AutumnEquinox + 1 ) && $weekday == 1 ) return true; //振替休日
          }
          if ( $year >= 2003 )
          {
              $week = floor( ( $day - 1 ) / 7 ) + 1;
              if ( $week == 3 && $weekday == 1 ) return true; //敬老の日(第3月曜日)
              if ( ( $day == $AutumnEquinox - 1 ) && $weekday == 2 ) return true; //国民の休日(敬老の日と秋分の日にはさまれた日)
          }
          elseif ( $year >= 1966 )
          {
              if ( $day == 15 ) return true; //敬老の日
          }
          break;
      
      case 10:// 10月
          if ( $year >= 2000 )
          {
              $week = floor( ( $day - 1 ) / 7 ) + 1;
              if ( $week == 2 && $weekday == 1 ) return true; //体育の日(第2月曜日)
          }
          elseif ( $year >= 1966 )
          {
              if ( $day == 10 ) return true; //体育の日
          }
          break;
      
      case 11:// 11月
          if ( $day == 3 ) return true; //文化の日
          if ( $day == 4 && $weekday == 1 ) return true; //振替休日
          if ( $day == 23 ) return true; //勤労感謝の日
          if ( $day == 24 && $weekday == 1 ) return true; //振替休日
          break;
      
      case 12:// 12月
          if ( $year >= 1989 )
          {
              if ( $day == 23 ) return true; //天皇誕生日
              if ( $day == 24 && $weekday == 1 ) return true; //振替休日
          }
          break;
  }
  return false;
}

/**
* Strips slashes if magic_quotes_gpc is on
*/
function MYCALJP_stripslashes($var) {
    if (get_magic_quotes_gpc()) {
        if (is_array($var)) {
            return array_map('MYCALJP_stripslashes', $var);
        } else {
            return stripslashes($var);
        }
    } else {
        return $var;
    }
}


function MYCALJP_htmlspecialchars( $str )
{
    global $_CONF, $LANG_CHARSET;

    if ( isset( $LANG_CHARSET ) )
    {
        $encoding = $LANG_CHARSET;
    }
    else if ( isset( $_CONF['default_charset'] ) )
    {
        $encoding = $_CONF['default_charset'];
    }
    else 
    {
        $encoding = 'utf-8';
    }
    $str = str_replace('%27', "'", $str);

    return htmlspecialchars( $str, ENT_QUOTES, $encoding );
}


function MYCALJP_pi_enabled($pi_name)
{
    global $_TABLES, $_MYCALJP2_CONF;
    
    if ($_MYCALJP2_CONF['enabled_contents'][$pi_name] != 1) return false;
    return ( DB_getItem($_TABLES['plugins'], 'pi_enabled', "pi_name = '$pi_name'") == 1 );
}

// PHPブロック関数
function phpblock_mycaljp2()
{
    global $LANG_MYCALJP;

    if (!class_exists('Dataproxy')) return $LANG_MYCALJP['no_dataproxy'];
    return MYCALJP_showCalendar();
}

if (!function_exists('phpblock_mycaljp'))
{
    // PHPブロック関数
    function phpblock_mycaljp()
    {
        global $LANG_MYCALJP;

        if (!class_exists('Dataproxy')) return $LANG_MYCALJP['no_dataproxy'];
        return MYCALJP_showCalendar();
    }
}

if (!function_exists('mycaljp_slidecalender'))
{
    // slidecalender
    function mycaljp_slidecalender()
    {
        global $LANG_MYCALJP;

        if (!class_exists('Dataproxy')) return $LANG_MYCALJP['no_dataproxy'];
        return MYCALJP_showCalendar( 2 );
    }
}

if ((!GL_VERSION_15) && (!function_exists('SEC_checkToken'))) {
    function SEC_checkToken() {
        return true;
    }
}

if ((!GL_VERSION_15) && (!function_exists('SEC_createToken'))) {
    function SEC_createToken() {
        return "1";
    }
}

if (!function_exists('COM_output')) {
    function COM_output($display) {
        echo $display;
    }
}

?>
