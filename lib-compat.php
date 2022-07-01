<?php

/**
 * Polyfill for some COM_* functions
 *
 * Since Geeklog 1.6.0: COM_output
 *               1.6.1: COM_getTextContent
 *               1.7.0: COM_checkInstalled, COM_truncateHTML
 *               1.8.0: COM_newTemplate, COM_versionConvert, COM_versionCompare
 *               2.0.0: COM_createHTMLDocument, COM_getLangIso639Code, COM_nl2br
 *               2.1.0: COM_getEncodingt
 */

if (!is_callable('COM_output')) {

/**
 * Sends compressed output to browser.
 *
 * @param    string $display Content to send to browser
 * @return   void
 * @since    1.6.0
 */
function COM_output($display)
{
    global $_CONF;

    if (empty($display)) {
        return;
    }

    if ($_CONF['compressed_output']) {
        $gzip_accepted = false;
        if (isset($_SERVER['HTTP_ACCEPT_ENCODING'])) {
            $enc = str_replace(' ', '', $_SERVER['HTTP_ACCEPT_ENCODING']);
            $accept = explode(',', strtolower($enc));
            $gzip_accepted = in_array('gzip', $accept);
        }

        if ($gzip_accepted && function_exists('gzencode')) {
            $zlib_comp = ini_get('zlib.output_compression');
            if (empty($zlib_comp) || (strcasecmp($zlib_comp, 'off') === 0)) {
                header('Content-encoding: gzip');
                echo gzencode($display);

                return;
            }
        }
    }

    echo $display;
}

}

if (!is_callable('COM_getTextContent')) {

/**
 * Turn a piece of HTML into continuous(!) plain text
 *
 * @param    string $text original text, including HTML and line breaks
 * @return   string       continuous plain text
 * @since    1.6.1
 */
function COM_getTextContent($text)
{
    // remove everything before <body> tag
    if (($pos = stripos($text, '<body')) !== false) {
        $text = substr($text, $pos);
    }

    // remove everything after </body> tag
    if (($pos = stripos($text, '</body>')) !== false) {
        $text = substr($text, 0, $pos + strlen('</body>'));
    }

    // remove <script> tags
    if (stripos($text, '<script') !== false) {
        $text = preg_replace('@<script.*?>.*?</script>@i', ' ', $text);

        if (($pos = stripos($text, '<script')) !== false) {
            // </script> tag is missing
            $text = substr($text, 0, $pos);
        }

        if (($pos = stripos($text, '</script>')) !== false) {
            // <script> tag is missing
            $text = substr($text, $pos + strlen('</script>'));
        }
    }

    // replace <br> with spaces so that Text<br>Text becomes two words
    $text = preg_replace('/\<br(\s*)?\/?\>/i', ' ', $text);

    // add extra space between tags, e.g. <p>Text</p><p>Text</p>
    $text = str_replace('><', '> <', $text);

    // only now remove all HTML tags
    $text = GLText::stripTags($text);

    // replace all tabs, newlines, and carriage returns with spaces
    $text = str_replace(array("\011", "\012", "\015"), ' ', $text);

    // replace entities with plain spaces
    $text = str_replace(array('&#20;', '&#160;', '&nbsp;'), ' ', $text);

    // collapse whitespace
    $text = preg_replace('/\s\s+/', ' ', $text);

    return trim($text);
}

}

if (!is_callable('COM_checkInstalled')) {
/**
 * Check if Geeklog has been installed yet
 *
 * @since  1.7.0
 */
function COM_checkInstalled()
{
    global $_CONF;

    // this is the only thing we check for now ...
    $isInstalled = !empty($_CONF) && is_array($_CONF) &&
        isset($_CONF['path']) && ($_CONF['path'] !== '/path/to/Geeklog/') && @file_exists($_CONF['path']);

    if (!$isInstalled) {
        $rel = '';
        $cd = getcwd();
        if (!file_exists($cd . '/admin/install/index.php')) {
            // this should cover most (though not all) cases
            $rel = '../';
        }

        $version = VERSION;
        $display = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Welcome to Geeklog</title>
  <meta name="robots" content="noindex,nofollow" />
  <link rel="stylesheet" href="vendor/uikit3/css/uikit.min.css">
  <script src="vendor/uikit3/js/uikit.min.js"></script>
  <script src="vendor/uikit3/js/uikit-icons.min.js"></script>
  <style type="text/css">
    html, body {
      color: #000;
      background-color: #fff;
      font-family: sans-serif;
      text-align: center;
    }
  </style>
</head>

<body>
<div class="uk-container">
  <div class="uk-grid" style="max-width: 600px; margin: 5px auto;">
    <div class="uk-align-center">
      <img src="{$rel}docs/images/logo.gif" alt="" />
    </div>

    <div>
      <h1 class="uk-align-center">Geeklog {$version}</h1>
      <p class="uk-align-center"><span uk-icon="icon: warning; ratio: 2" style="color: red;"></span>  Please run the <a href="{$rel}admin/install/index.php" rel="nofollow">install script</a> first.</p>
      <p class="uk-align-center">For more information, please refer to the <a href="{$rel}docs/english/install.html" rel="nofollow">installation instructions</a>.</p>
    </div>
  </div>
</div>
</body>
</html>
HTML;
        header("HTTP/1.1 503 Service Unavailable");
        header("Status: 503 Service Unavailable");
        header('Content-Type: text/html; charset=utf-8');
        die($display);
    }
}

}

if (!is_callable('COM_truncateHTML')) {

/**
 * Truncate a string that contains HTML tags. Will close all HTML tags as needed.
 *
 * @param    string $htmlText the text string which contains HTML tags to truncate
 * @param    int    $maxLen   max. number of characters in the truncated string
 * @param    string $filler   optional filler string, e.g. '...'
 * @param    int    $endChars number of characters to show after the filler
 * @return   string           truncated string
 * @since    1.7.0
 */
function COM_truncateHTML($htmlText, $maxLen, $filler = '', $endChars = 0)
{
    $newLen = $maxLen - MBYTE_strlen($filler);
    $len = MBYTE_strlen($htmlText);
    if ($len > $maxLen) {
        $htmlText = MBYTE_substr($htmlText, 0, $newLen - $endChars);

        // Strip any mangled tags off the end
        if (MBYTE_strrpos($htmlText, '<') > MBYTE_strrpos($htmlText, '>')) {
            $htmlText = MBYTE_substr($htmlText, 0, MBYTE_strrpos($htmlText, '<'));
        }

        $htmlText = $htmlText . $filler . MBYTE_substr($htmlText, $len - $endChars, $endChars);

        // *******************************
        // Note: At some point we should probably use htmLawed here or the GLText class which uses htmLawed???
        // something like GLText::applyHTMLFilter but needs to be run with the view of an anonymous user

        // put all opened tags into an array
        preg_match_all("#<([a-z]+)( .*)?(?!/)>#iU", $htmlText, $result);
        $openedTags = $result[1];
        $openedTags = array_diff($openedTags, array('img', 'hr', 'br'));
        $openedTags = array_values($openedTags);

        // put all closed tags into an array
        preg_match_all("#</([a-z]+)>#iU", $htmlText, $result);
        $closedTags = $result[1];
        $len_opened = count($openedTags);

        // all tags are closed
        if (count($closedTags) == $len_opened) {
            return $htmlText;
        }
        $openedTags = array_reverse($openedTags);

        // close tags
        for ($i = 0; $i < $len_opened; $i++) {
            if (!in_array($openedTags[$i], $closedTags)) {
                $htmlText .= "</" . $openedTags[$i] . ">";
            } else {
                unset($closedTags[array_search($openedTags[$i], $closedTags)]);
            }
        }
        // *******************************
    }

    return $htmlText;
}

}

if (!is_callable('COM_newTemplate')) {

/**
 * Provide support for drop-in replaceable template engines
 *
 * @param    string|array $root    Path to template root. If string assume core, if array assume plugin
 * @param    string|array $options List of options to pass to constructor
 * @return   Template              An ITemplate derived object
 * @since    1.8.0
 */
function COM_newTemplate($root, $options = array())
{
    global $TEMPLATE_OPTIONS;

    if (function_exists('OVERRIDE_newTemplate')) {
        if (is_string($options)) {
            $options = array('unknowns', $options);
        }
        $T = OVERRIDE_newTemplate($root, $options);
    } else {
        $T = null;
    }

    if (!is_object($T)) {
        if (is_array($options) && array_key_exists('unknowns', $options)) {
            $options = $options['unknowns'];
        } else {
            $options = 'remove';
        }

        // Note: as of Geeklog 2.2.0
        // CTL_setTemplateRoot before was set back when child themes support was added (not sure which Geeklog version) as a hook to run as a template preprocessor.
        // This was fine for Geeklog Core but could create issues for plugins as it could not tell the difference and would add in theme and theme_default root dir locations (among other custom folders)
        // Now if root is passed as a single directory (not an array) it is assumed that Geeklog Core is setting the template or an old style plugin
        // If root is an array assume it is a plugin that supports multiple locations for its template (uses CTL_plugin_templatePath)
        // For more info see template class set_root function, CTL_setTemplateRoot, CTL_core_templatePath, CTL_plugin_templatePath

        // CTL_setTemplateRoot will be depreciated as of Geeklog 3.0.0. This means plugins will not be allowed to set the template class directly. They must use COM_newTemplate and either CTL_core_templatePath or CTL_plugin_templatePath

        if (is_array($root)) {
            $TEMPLATE_OPTIONS['hook'] = array(); // Remove default hook that sets CTL_setTemplateRoot Function found in lib-template. It is used to add the ability for child themes (old way)
        }

        $T = new Template($root, $options);
    }

    return $T;
}

}

if (!is_callable('COM_versionConvert')) {

/**
 * Common function used to convert a Geeklog version number into
 * a version number that can be parsed by PHP's "version_compare()"
 *
 * @param    string $version Geeklog version number
 * @return   string          Generic version number that can be correctly handled by PHP
 * @since    1.8.0
 */
function COM_versionConvert($version)
{
    $version = strtolower($version);
    // Check if it's a bugfix release first
    $dash = strpos($version, '-');
    if ($dash !== false) {
        // Sometimes the bugfix part is not placed in the version number
        // according to the documentation and this needs to be accounted for
        $rearrange = true; // Assume incorrect formatting
        $b = strpos($version, 'b');
        $rc = strpos($version, 'rc');
        $sr = strpos($version, 'sr');
        if ($b && $b < $dash) {
            $pos = $b;
        } elseif ($rc && $rc < $dash) {
            $pos = $rc;
        } elseif ($sr && $sr < $dash) {
            $pos = $sr;
        } else {
            // Version is correctly formatted
            $rearrange = false;
        }
        // Rearrange the version number, if needed
        if ($rearrange) {
            $ver = substr($version, 0, $pos);
            $cod = substr($version, $pos, $dash - $pos);
            $bug = substr($version, $dash + 1);
            $version = $ver . '.' . $bug . $cod;
        } else { // This bugfix release version is correctly formatted
            // So there is an extra number in the version
            $version = str_replace('-', '.', $version);
        }
        $bugfix = '';
    } else {
        // Not a bugfix release, so we add a zero to compensate for the extra number
        $bugfix = '.0';
    }
    // We change the non-numerical part in the "versions" that were passed into the function
    // beta                      -> 1
    // rc                        -> 2
    // hg                        -> ignore
    // stable (e.g: no letters)  -> 3
    // sr                        -> 4
    if (strpos($version, 'b') !== false) {
        $version = str_replace('b', $bugfix . '.1.', $version);
    } elseif (strpos($version, 'rc') !== false) {
        $version = str_replace('rc', $bugfix . '.2.', $version);
    } elseif (strpos($version, 'sr') !== false) {
        $version = str_replace('sr', $bugfix . '.4.', $version);
    } else { // must be a stable version then...
        // we always ignore the 'hg' bit
        $version = str_replace('hg', '', $version);
        $version .= $bugfix . '.3.0';
    }

    return $version;
}

}

if (!is_callable('COM_versionCompare')) {

/**
 * Common function used to compare two Geeklog version numbers
 *
 * @param    string $version1        First version number to be compared
 * @param    string $version2        Second version number to be sompared
 * @param    string $operator        optional string to define how the two versions are to be compared
 *                                   valid operators are: <, lt, <=, le, >, gt, >=, ge, ==, =, eq, !=, <>, ne
 * @return   mixed                   By default, returns -1 if the first version is lower than the second,
 *                                   0 if they are equal, and 1 if the second is lower.
 *                                   When using the optional operator argument, the function will return TRUE
 *                                   if the relationship is the one specified by the operator, FALSE otherwise.
 * @since    1.8.0
 */
function COM_versionCompare($version1, $version2, $operator = '')
{
    // Convert Geeklog version numbers to a ones that can be parsed
    // by PHP's "version_compare"
    $version1 = COM_versionConvert($version1);
    $version2 = COM_versionConvert($version2);
    // All that there should be left at this point is numbers and dots,
    // so PHP's built-in function can now take over.
    if (empty($operator)) {
        return version_compare($version1, $version2);
    } else {
        return version_compare($version1, $version2, $operator);
    }
}

}

if (!is_callable('COM_createHTMLDocument')) {

/**
 * Create and return the HTML document
 *
 * @param  string $content      Main content for the page
 * @param  array  $information  An array defining variables to be used when creating the output 
 *                              string  'what'          If 'none' then no left blocks are returned, if 'menu' (default) then right blocks are returned 
 *								string  'pagetitle'     Optional content for the page's <title> string  'breadcrumbs'   Optional content for the page's breadcrumb
 *                              string  'headercode'    Optional code to go into the page's <head> 
 *								boolean 'rightblock'    Whether or not to show blocks on right hand side default is no (-1)
 *								string	'httpstatus'	HTTP Response Status Code. 200 is assumed. WIll be passed to template
 *                              array   'custom'        An array defining custom function to be used to format
 *                              Rightblocks
 * @return string              Formatted HTML document
 * @since                       2.0.0
 */
function COM_createHTMLDocument($content = '', $information = array())
{
	$what = isset($information['what']) ? $information['what'] : '';
	$pageTitle = isset($information['pagetitle']) ? $information['pagetitle'] : '';
	$headerCode = isset($information['headercode']) ? $information['headercode'] : '';
	$rightBlock = isset($information['rightblock']) ? $information['rightblock'] : -1;
	$custom = isset($information['custom']) ? $information['custom'] : '';
	
	return COM_siteHeader($what, $pageTitle, $headerCode)
		. $content
		. COM_siteFooter($rightBlock, $custom);
}

}

if (!is_callable('COM_getLangIso639Code')) {

/**
 * Returns the ISO-639-1 language code
 *
 * @param   string $langName
 * @return  string
 * @since   2.0.0
 */
function COM_getLangIso639Code($langName = null)
{
    $mapping = [
        // GL language name   => ISO-639-1
        'afrikaans'           => 'af',
        'bosnian'             => 'bs',
        'bulgarian'           => 'bg',
        'catalan'             => 'ca',
        'chinese_simplified'  => 'zh-cn',
        'chinese_traditional' => 'zh',
        'croatian'            => 'hr',
        'czech'               => 'cs',
        'danish'              => 'da',
        'dutch'               => 'nl',
        'english'             => 'en',
        'estonian'            => 'et',
        'farsi'               => 'fa',      // Replaced by 'persian'
        'finnish'             => 'fi',
        'french_canada'       => 'fr-ca',
        'french_france'       => 'fr',
        'german'              => 'de',
        'german_formal'       => 'de',
        'hebrew'              => 'he',
        'hellenic'            => 'el',
        'indonesian'          => 'id',
        'italian'             => 'it',
        'japanese'            => 'ja',
        'korean'              => 'ko',
        'norwegian'           => 'nb',  // Norwegian (Bokmal)
        'persian'             => 'fa',
        'polish'              => 'pl',
        'portuguese'          => 'pt',
        'portuguese_brazil'   => 'pt-br',
        'romanian'            => 'ro',
        'russian'             => 'ru',
        'serbian'             => 'sr',
        'slovak'              => 'sk',
        'slovenian'           => 'sl',
        'spanish'             => 'es',
        'spanish_argentina'   => 'es',
        'swedish'             => 'sv',
        'turkish'             => 'tr',
        'ukrainian'           => 'uk',
        'ukrainian_koi8-u'    => 'uk',
    ];

    if ($langName === null) {
        $langName = COM_getLanguage();
    }

    $langName = strtolower($langName);
    $langName = str_replace('_utf-8', '', $langName);

    return isset($mapping[$langName]) ? $mapping[$langName] : 'en';
}

}

if (!is_callable('COM_nl2br')) {

/**
 * Replaces all newlines in a string with <br> or <br />,
 * depending on the detected setting.
 *
 * @param    string $string The string to modify
 * @return   string         The modified string
 * @since    2.0.0
 */
function COM_nl2br($string)
{
    if (empty($string)) {
        return $string;
    } else {
        if (!defined('XHTML')) {
            define('XHTML', '');
        }

        return str_replace(["\r\n", "\n\r", "\r", "\n"], '<br' . XHTML . '>', $string);
    }
}

}

if (!is_callable('COM_getEncodingt')) {

/**
 * Get a valid encoding for htmlspecialchars()
 *
 * @return   string      character set, e.g. 'utf-8'
 * @since    2.1.0
 */
function COM_getEncodingt()
{
    static $encoding;

    if ($encoding === null) {
        $encoding = strtolower(COM_getCharset());
        $valid_charsets = array(
            'iso-8859-1', 'iso-8859-15', 'utf-8', 'cp866', 'cp1251',
            'cp1252', 'koi8-r', 'big5', 'gb2312', 'big5-hkscs',
            'shift_jis', 'sjis', 'euc-jp',
        );
        if (!in_array($encoding, $valid_charsets)) {
            $encoding = 'iso-8859-1';
        }
    }

    return $encoding;
}

}
