<?php
// Reminder: always indent with 4 spaces (no tabs).
// +---------------------------------------------------------------------------+
// | Site Calendar - Mycaljp Plugin for Geeklog                                |
// +---------------------------------------------------------------------------+
// | plugins/mycaljp/install_defaults.php                                      |
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

if (strpos(strtolower($_SERVER['PHP_SELF']), 'install_defaults.php') !== false) {
    die('This file can not be used on its own!');
}

/*
 * Mycaljp default settings
 *
 * Initial Installation Defaults used when loading the online configuration
 * records. These settings are only used during the initial installation
 * and not referenced any more once the plugin is installed
 *
 */

global $_CONF, $_MYCALJP2_DEFAULT, $LANG_MYCALJP;

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
 * サポートするコンテンツの設定
 * ----------------------------
 */

$_MYCALJP2_DEFAULT['supported_contents'] = array(
    'stories',          //記事
    'comments',         //コメント
    'trackback',        //トラックバック
    'staticpages',      //静的ページ
    'calendar',         //イベントカレンダ
    'links',            //リンク
    'polls',            //アンケート
    'dokuwiki',         //DokuWiki
    'forum',            //掲示板
    'filemgmt',         //ファイル管理
    'faqman',           //Faqman
    'mediagallery',     //メディアギャラリ
    'calendarjp',       //イベントカレンダ（日本語版）
    'download'          //ダウンロード
);

$_MYCALJP2_DEFAULT['enabled_contents'] = array(
    'stories'      => 1,    //記事
    'comments'     => 1,    //コメント
    'trackback'    => 1,    //トラックバック
    'staticpages'  => 1,    //静的ページ
    'calendar'     => 1,    //イベントカレンダ
    'links'        => 1,    //リンク
    'polls'        => 1,    //アンケート
    'dokuwiki'     => 1,    //DokuWiki
    'forum'        => 1,    //掲示板
    'filemgmt'     => 1,    //ファイル管理
    'faqman'       => 1,    //Faqman
    'mediagallery' => 1,    //メディアギャラリ
    'calendarjp'   => 1,    //イベントカレンダ（日本語版）
    'download'     => 1     //ダウンロード
);

/*
 * チェックするコンテンツの設定
 * ----------------------------
 */

//$_MYCALJP2_DEFAULT['contents'] = $_MYCALJP2_DEFAULT['support'];

/*
 * 土・日・休日の色分け表示の設定
 * ------------------------------
 *   true  : 色分けする (default)
 *   false : 色分けしない
 */

$_MYCALJP2_DEFAULT['showholiday'] = true;

/*
 * 日本の休日を調べるかどうかの設定
 * --------------------------------
 *   true  : 調べる (default)
 *   false : 調べない
 */

$_MYCALJP2_DEFAULT['checkjpholiday'] = true;

/*
 * タイトル(年・月)の設定
 * ----------------------
 * "m"  月．数字。先頭にゼロをつける．  (01 から 12)
 * "n"  月．数字。先頭にゼロをつけない．(1 から 12)
 * "F"  月．フルスペルの文字．          (January から December)
 * "M"  月．3 文字形式．                (Jan から Dec)
 * "Y"  年．4 桁の数字．                (例: 1999または2003)
 * "y"  年．2 桁の数字．                (例: 99 または 03)
 */

if ($_CONF['language'] == 'japanese_utf-8') {

    $_MYCALJP2_DEFAULT['headertitleyear']  = "Y年";
    $_MYCALJP2_DEFAULT['headertitlemonth'] = "m月";

} else {

    $_MYCALJP2_DEFAULT['headertitleyear']   = "Y";
    $_MYCALJP2_DEFAULT['headertitlemonth']  = "F";

}

/*
 * タイトル(年・月)の表示順序の設定
 * ----------------------
 *   true  : 年 月
 *   false : 月 年
 */

if ($_CONF['language'] == 'japanese_utf-8') {

    $_MYCALJP2_DEFAULT['titleorder'] = true;

} else {

    $_MYCALJP2_DEFAULT['titleorder'] = false;

}

/*
 * 曜日の表示文字列の設定
 * ----------------------------
 */
$_MYCALJP2_DEFAULT['sunday']    = $LANG_MYCALJP['sunday'];
$_MYCALJP2_DEFAULT['monday']    = $LANG_MYCALJP['monday'];
$_MYCALJP2_DEFAULT['tuesday']   = $LANG_MYCALJP['tuesday'];
$_MYCALJP2_DEFAULT['wednesday'] = $LANG_MYCALJP['wednesday'];
$_MYCALJP2_DEFAULT['thursday']  = $LANG_MYCALJP['thursday'];
$_MYCALJP2_DEFAULT['friday']    = $LANG_MYCALJP['friday'];
$_MYCALJP2_DEFAULT['saturday']  = $LANG_MYCALJP['saturday'];

/*
 * 日付クリック後の検索結果表示において、右サイドバーを表示するかどうかの設定
 * --------------------------------------------------------------------------
 *   true  : 表示する (default)
 *   false : 表示しない
 */

$_MYCALJP2_DEFAULT['enablesrblocks'] = true;

/*
 * 日付クリック後の検索結果表示において、記事の導入部(イントロ)を表示するかどうかの設定
 * ------------------------------------------------------------------------------------
 *   true  : 表示する (default)
 *   false : 表示しない
 */

$_MYCALJP2_DEFAULT['showstoriesintro'] = true;


/*
 * 曜日の表示文字列の設定
 * ----------------------------
 */

$_MYCALJP2_DEFAULT['template'] = 'default';

$_MYCALJP2_DEFAULT['use_theme'] = false;


$_MYCALJP2_DEFAULT['sp_type']   = 1;
$_MYCALJP2_DEFAULT['sp_except'] = 'formmail';

$_MYCALJP2_DEFAULT['date_format'] = '[Y-m-d] ';


/**
* Initialize Navigation Manager plugin configuration
*
* Creates the database entries for the configuation if they don't already
* exist. Initial values will be taken from $_MYCALJP2_CONF if available (e.g. from
* an old config.php), uses $_MYCALJP2_DEFAULT otherwise.
*
* @return   boolean     true: success; false: an error occurred
*
*/
function plugin_initconfig_mycaljp()
{
    global $_MYCALJP2_CONF, $_MYCALJP2_DEFAULT;

    if (is_array($_MYCALJP2_CONF) && (count($_MYCALJP2_CONF) > 1)) {
        $_MYCALJP2_DEFAULT = array_merge($_MYCALJP2_DEFAULT, $_MYCALJP2_CONF);
    }

    $c = config::get_instance();
    $n = 'mycaljp';
    $o = 0;
    if ($c->group_exists($n)) return true;
    $c->add('sg_main',            NULL,                                     'subgroup', 0, 0, NULL, 0,    true, $n);
    // ----------------------------------
    $c->add('fs_main',            NULL,                                     'fieldset', 0, 0, NULL, 0,    true, $n);
    $c->add('headertitleyear',    $_MYCALJP2_DEFAULT['headertitleyear'],    'text',     0, 0, 0,    $o++, true, $n);
    $c->add('headertitlemonth',   $_MYCALJP2_DEFAULT['headertitlemonth'],   'text',     0, 0, 0,    $o++, true, $n);
    $c->add('titleorder',         $_MYCALJP2_DEFAULT['titleorder'],         'select',   0, 0, 13,   $o++, true, $n);
    $c->add('sunday',             $_MYCALJP2_DEFAULT['sunday'],             'text',     0, 0, 0,    $o++, true, $n);
    $c->add('monday',             $_MYCALJP2_DEFAULT['monday'],             'text',     0, 0, 0,    $o++, true, $n);
    $c->add('tuesday',            $_MYCALJP2_DEFAULT['tuesday'],            'text',     0, 0, 0,    $o++, true, $n);
    $c->add('wednesday',          $_MYCALJP2_DEFAULT['wednesday'],          'text',     0, 0, 0,    $o++, true, $n);
    $c->add('thursday',           $_MYCALJP2_DEFAULT['thursday'],           'text',     0, 0, 0,    $o++, true, $n);
    $c->add('friday',             $_MYCALJP2_DEFAULT['friday'],             'text',     0, 0, 0,    $o++, true, $n);
    $c->add('saturday',           $_MYCALJP2_DEFAULT['saturday'],           'text',     0, 0, 0,    $o++, true, $n);
    $c->add('showholiday',        $_MYCALJP2_DEFAULT['showholiday'],        'select',   0, 0, 1,    $o++, true, $n);
    $c->add('checkjpholiday',     $_MYCALJP2_DEFAULT['checkjpholiday'],     'select',   0, 0, 1,    $o++, true, $n);
    $c->add('enablesrblocks',     $_MYCALJP2_DEFAULT['enablesrblocks'],     'select',   0, 0, 1,    $o++, true, $n);
    $c->add('showstoriesintro',   $_MYCALJP2_DEFAULT['showstoriesintro'],   'select',   0, 0, 1,    $o++, true, $n);
    $c->add('use_theme',          $_MYCALJP2_DEFAULT['use_theme'],          'select',   0, 0, 0,    $o++, true, $n);
    $c->add('template',           $_MYCALJP2_DEFAULT['template'],           'text',     0, 0, 0,    $o++, true, $n);
    $c->add('date_format',        $_MYCALJP2_DEFAULT['date_format'],        'text',     0, 0, 0,    $o++, true, $n);
    $c->add('supported_contents', $_MYCALJP2_DEFAULT['supported_contents'], '%text',    0, 0, NULL, $o++, true, $n);
    $c->add('enabled_contents',   $_MYCALJP2_DEFAULT['enabled_contents'],   '%text',    0, 0, NULL, $o++, true, $n);
    // ----------------------------------
    $c->add('fs_staticpages',     NULL,                                     'fieldset', 0, 1, NULL, 0,    true, $n);
    $c->add('sp_type',            $_MYCALJP2_DEFAULT['sp_type'],            'select',   0, 1, 14,   $o++, true, $n);
    $c->add('sp_except',          $_MYCALJP2_DEFAULT['sp_except'],          'text',     0, 1, 0,    $o++, true, $n);

    return true;
}

?>