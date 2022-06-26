<?php
// Reminder: always indent with 4 spaces (no tabs).
// +---------------------------------------------------------------------------+
// | Site Calendar - Mycaljp Plugin for Geeklog                                |
// +---------------------------------------------------------------------------+
// | plugins/mycaljp/language/japanese_utf-8.php                               |
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

$LANG_MYCALJP = array (
    'plugin'            => 'mycaljpプラグイン',
    'access_denied'     => 'アクセスは拒否されました。',
    'access_denied_msg' => 'このページにアクセスできるのは，Rootユーザだけです。あなたのユーザ名とIPアドレスは記録されました。',
    'admin'             => 'サイトカレンダ mycaljp の管理',
    'install_header'    => 'サイトカレンダ mycaljp プラグインのインストール/アンインストール',
    'installed'         => 'サイトカレンダ mycaljp プラグインはインストールされています。',
    'uninstalled'       => 'サイトカレンダ mycaljp プラグインはインストールされていません。',
    'install_success'   => 'サイトカレンダ mycaljp プラグインのインストールに成功しました。',
    'install_failed'    => 'サイトカレンダ mycaljp プラグインのインストールに失敗しました。詳細はエラーログ(error.log)をご覧ください。',
    'uninstall_msg'     => 'サイトカレンダ mycaljp プラグインはアンインストールされました。',
    'install'           => 'インストール',
    'uninstall'         => 'アンインストール',
    'warning'           => '警告！　サイトカレンダ mycaljp プラグインは有効なままです。',
    'enabled'           => 'アンインストールする前に，サイトカレンダ mycaljp プラグインを無効にしてください。',
    'readme'            => 'ちょっと待って！　「インストール」をクリックする前に，お読みください：',
    'installdoc'        => 'インストール手順書',
    
    'blocktitle'        => 'ブロックタイトル',
    'selecttemplates'   => 'テンプレートの選択',
    'checkcontents'     => 'チェック対象のコンテンツ',
    'wdays'             => '曜日タイトル',
    'prevmonth'         => '先月へ',
    'nextmonth'         => '翌月へ',
    'skipcalendar'      => 'サイトカレンダをスキップ',
    'headeroflink'      => '',
    'footeroflink'      => 'のコンテンツ',
    'yes'               => 'はい',
    'no'                => 'いいえ',
    'sunday'            => '日',
    'monday'            => '月',
    'tuesday'           => '火',
    'wednesday'         => '水',
    'thursday'          => '木',
    'friday'            => '金',
    'saturday'          => '土',

    'applythemetmplate' => 'テーマ提供テンプレートの適用',
    'headerofdate'      => '',
    'middleofdate'      => ' ～ ',
    'footerofdate'      => ' の検索結果',
    'no_dataproxy'      => 'Dataproxy がありません。',
    'pickup_title'      => 'サイトカレンダ - ピックアップ',
);


// Localization of the Admin Configuration UI
$LANG_configsections['mycaljp'] = array(
    'label' => 'Mycaljp',
    'title' => 'Mycaljp Configulation'
);

$LANG_confignames['mycaljp'] = array(
    'headertitleyear'     => 'ヘッダタイトル（年）',
    'headertitlemonth'    => 'ヘッダタイトル（月）',
    'titleorder'          => 'ヘッダタイトルの順序',
    'sunday'              => '日',
    'monday'              => '月',
    'tuesday'             => '火',
    'wednesday'           => '水',
    'thursday'            => '木',
    'friday'              => '金',
    'saturday'            => '土',
    'showholiday'         => '土・日・休日を色分け表示する',
    'checkjpholiday'      => '日本の休日を調べる',
    'enablesrblocks'      => '右サイドバーを表示する',
    'showstoriesintro'    => '記事のイントロを表示する',
    'use_theme'           => 'テーマのテンプレートを使う',
    'template'            => 'テンプレート名',
    'date_format'         => '日付の形式',
    'supported_contents'  => 'サポートするコンテンツ',
    'enabled_contents'    => '有効にするコンテンツ',
    'sp_type'             => 'リストに掲載するタイプ',
    'sp_except'           => '除外するページID',
);

$LANG_configsubgroups['mycaljp'] = array(
    'sg_main' => 'メイン'
);

$LANG_fs['mycaljp'] = array(
    'fs_main' => 'サイトカレンダのメイン設定',
    'fs_staticpages' => '静的ページの設定',
);

// Note: entries 0, 1, and 12 are the same as in $LANG_configselects['Core']
$LANG_configselects['mycaljp'] = array(
    0 => array('はい' => 1, 'いいえ' => 0),
    1 => array('はい' => TRUE, 'いいえ' => FALSE),
    12 => array('アクセス不可' => 0, '表示' => 2, '表示・編集' => 3),
    13 => array('年 月' => TRUE, '月 年' => FALSE),
    14 => array('すべて' => 0, 'センターブロックに表示されるページのみ' => 1, 'センターブロックに表示されないページのみ' => 2),
);
?>