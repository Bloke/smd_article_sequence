<?php

// This is a PLUGIN TEMPLATE for Textpattern CMS.

// Copy this file to a new name like abc_myplugin.php.  Edit the code, then
// run this file at the command line to produce a plugin for distribution:
// $ php abc_myplugin.php > abc_myplugin-0.1.txt

// Plugin name is optional.  If unset, it will be extracted from the current
// file name. Plugin names should start with a three letter prefix which is
// unique and reserved for each plugin author ("abc" is just an example).
// Uncomment and edit this line to override:
$plugin['name'] = 'smd_article_sequence';

// Allow raw HTML help, as opposed to Textile.
// 0 = Plugin help is in Textile format, no raw HTML allowed (default).
// 1 = Plugin help is in raw HTML.  Not recommended.
# $plugin['allow_html_help'] = 1;

$plugin['version'] = '0.2.0';
$plugin['author'] = 'Stef Dawson';
$plugin['author_uri'] = 'https://stefdawson.com/';
$plugin['description'] = 'Keep track of how many times an article is saved. Requires rvm_counter plugin.';

// Plugin load order:
// The default value of 5 would fit most plugins, while for instance comment
// spam evaluators or URL redirectors would probably want to run earlier
// (1...4) to prepare the environment for everything else that follows.
// Values 6...9 should be considered for plugins which would work late.
// This order is user-overrideable.
$plugin['order'] = '5';

// Plugin 'type' defines where the plugin is loaded
// 0 = public              : only on the public side of the website (default)
// 1 = public+admin        : on both the public and admin side
// 2 = library             : only when include_plugin() or require_plugin() is called
// 3 = admin               : only on the admin side (no AJAX)
// 4 = admin+ajax          : only on the admin side (AJAX supported)
// 5 = public+admin+ajax   : on both the public and admin side (AJAX supported)
$plugin['type'] = '4';

// Plugin "flags" signal the presence of optional capabilities to the core plugin loader.
// Use an appropriately OR-ed combination of these flags.
// The four high-order bits 0xf000 are available for this plugin's private use
if (!defined('PLUGIN_HAS_PREFS')) define('PLUGIN_HAS_PREFS', 0x0001); // This plugin wants to receive "plugin_prefs.{$plugin['name']}" events
if (!defined('PLUGIN_LIFECYCLE_NOTIFY')) define('PLUGIN_LIFECYCLE_NOTIFY', 0x0002); // This plugin wants to receive "plugin_lifecycle.{$plugin['name']}" events

$plugin['flags'] = '0';

// Plugin 'textpack' is optional. It provides i18n strings to be used in conjunction with gTxt().
// Syntax:
// ## arbitrary comment
// #@event
// #@language ISO-LANGUAGE-CODE
// abc_string_name => Localized String

/*$plugin['textpack'] = <<<EOT
EOT;
*/

if (!defined('txpinterface'))
        @include_once('zem_tpl.php');

# --- BEGIN PLUGIN CODE ---
if (txpinterface === 'admin') {
    register_callback('smd_article_sequence', 'article_posted');
    register_callback('smd_article_sequence', 'article_saved');
}

/**
 * Increment the article counter.
 *
 * @param  string $evt  Textpattern event
 * @param  string $stp  Textpattern step
 * @param  array  $data Article metadata
 */
function smd_article_sequence($evt, $stp, $data)
{
    global $plugins;

    if (!in_array('rvm_counter', $plugins)) {
        load_plugin('rvm_counter');
    }

    if (in_array('rvm_counter', $plugins)) {
        if ($data['Status'] >= 4) {
            rvm_counter(array('name' => 'article-'.$data['ID']));
        }
    }
}

# --- END PLUGIN CODE ---
if (0) {
?>
<!--
# --- BEGIN PLUGIN HELP ---
h1. smd_article_sequence

Keeps track of how many times an article is saved.

h2. Installation / Uninstallation

# "Download this plugin":#, paste the code into the Textpattern _Admin->Plugins_ panel, install and enable the plugin. For bug reports, please "raise an issue":#.
# Install and enable the "rvm_counter":https://vanmelick.com/txp/rvm_counter.php?gzip plugin in a similar manner.

To uninstall, delete the plugin from the _Admin->Plugins_ panel.

h2. Usage

Just create/edit articles as normal from the Write panel. The plugin keeps track of the number of times each _published_ article is saved behind the scenes, courtesy of the rvm_counter plugin.

h2. Displaying the counter

Add this tag to your Form that displays article content to show the current counter value:

bc. <txp:rvm_counter name='article-<txp:article_id />' step="0" />

Bear in mind that this will display '0' if the article has yet to be saved (i.e. if this tag is run on existing articles that were saved/created before the plugin was installed). You may require some defensive coding to check the value returned from rvm_counter is non-zero, perhaps using the @<txp:evaluate>@ tag first.

# --- END PLUGIN HELP ---
-->
<?php
}
?>