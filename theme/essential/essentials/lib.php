<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Essentials is a basic child theme of Essential to help you as a theme
 * developer create your own child theme of Essential.
 *
 * @package     theme_essentials
 * @copyright   2015 Gareth J Barnard
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

function theme_essentials_process_css($css, $theme) {
    /* Change to 'true' if you want to use Essential's settings after removing the '$THEME->parents_exclude_sheets' in config.php.
       Then to get the alternive colours back, renove the overridden method 'custom_menu_themecolours' in the 'theme_essentials_core_renderer'
       class in the 'core_renderer.php' file in the 'classes' folder. */
    $usingessentialsettings = false;

    if ($usingessentialsettings) {
        if (file_exists("$CFG->dirroot/theme/essential/lib.php")) {
            require_once("$CFG->dirroot/theme/essential/lib.php");
        } else if (!empty($CFG->themedir) and file_exists("$CFG->themedir/essential/lib.php")) {
            require_once("$CFG->themedir/essential/lib.php");
        } // else will just fail when cannot find theme_essential_process_css!
        static $parenttheme;
        if (empty($parenttheme)) {
            $parenttheme = theme_config::load('essential'); 
        }
        $css = theme_essential_process_css($css, $parenttheme);
    }

    // If you have your own settings, then add them here.

    // Finally return processed CSS
    return $css;
}

function theme_essentials_set_fontwww($css) {
    global $CFG;
    $fontwww = preg_replace("(https?:)", "", $CFG->wwwroot . '/theme/essential/fonts/');

    $tag = '[[setting:fontwww]]';

    return $css;
}
