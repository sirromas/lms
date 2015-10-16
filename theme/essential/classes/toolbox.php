<?php

namespace theme_essential;

class toolbox {

    static protected $theme;

    // Moodle CSS file serving.
    static public function get_csswww() {
        global $CFG;

        if (!self::lte_ie9()) {
            if (\right_to_left()) {
                $moodlecss = 'essential-rtl.css';
            } else {
                $moodlecss = 'essential.css';
            }

            $syscontext = \context_system::instance();
            $itemid = \theme_get_revision();
            $url = \moodle_url::make_file_url("$CFG->wwwroot/pluginfile.php", "/$syscontext->id/theme_essential/style/$itemid/$moodlecss");
            $url = preg_replace('|^https?://|i', '//', $url->out(false));
            return '<link rel="stylesheet" href="'.$url.'">';
        } else {
            if (\right_to_left()) {
                $moodlecssone = 'essential-rtl_ie9-blessed1.css';
                $moodlecsstwo = 'essential-rtl_ie9.css';
            } else {
                $moodlecssone = 'essential_ie9-blessed1.css';
                $moodlecsstwo = 'essential_ie9.css';
            }

            $syscontext = \context_system::instance();
            $itemid = \theme_get_revision();
            $urlone = \moodle_url::make_file_url("$CFG->wwwroot/pluginfile.php", "/$syscontext->id/theme_essential/style/$itemid/$moodlecssone");
            $urlone = preg_replace('|^https?://|i', '//', $urlone->out(false));
            $urltwo = \moodle_url::make_file_url("$CFG->wwwroot/pluginfile.php", "/$syscontext->id/theme_essential/style/$itemid/$moodlecsstwo");
            $urltwo = preg_replace('|^https?://|i', '//', $urltwo->out(false));
            return '<link rel="stylesheet" href="'.$urlone.'"><link rel="stylesheet" href="'.$urltwo.'">';
        }
    }

    static public function get_setting($setting, $format = false, $theme = null) {
        if (empty($theme)) {
            if (empty(self::$theme)) {
                self::$theme = \theme_config::load('essential');
            }
            $theme = self::$theme;
        }

        global $CFG;
        require_once($CFG->dirroot . '/lib/weblib.php');
        if (empty($theme->settings->$setting)) {
            return false;
        } else if (!$format) {
            return $theme->settings->$setting;
        } else if ($format === 'format_text') {
            return format_text($theme->settings->$setting, FORMAT_PLAIN);
        } else if ($format === 'format_html') {
            return format_text($theme->settings->$setting, FORMAT_HTML, array('trusted' => true, 'noclean' => true));
        } else {
            return format_string($theme->settings->$setting);
        }
    }

    /**
     * Finds the given include file in the theme.  If it does not exist for the Essential child theme then the parent is checked.
     * @param string $filename Filename without extension to get.
     * @return string Complete path of the file.
     */
    static public function get_include_file($filename) {
        global $CFG, $PAGE;
        $themedir = $PAGE->theme->dir;
        $filename .= '.php';
        // Check only if a child of 'Essential' to prevent conflicts with other themes using the 'includes' folder....
        if (in_array('essential', $PAGE->theme->parents)) {
            $themename = $PAGE->theme->name;
            if (file_exists("$themedir/layout/includes/$filename")) {
                return "$themedir/layout/includes/$filename";
            } else if (file_exists("$CFG->dirroot/theme/$themename/layout/includes/$filename")) {
                return "$CFG->dirroot/theme/$themename/layout/includes/$filename";
            } else if (!empty($CFG->themedir) and file_exists("$CFG->themedir/$themename/layout/includes/$filename")) {
                return "$CFG->themedir/$themename/layout/includes/$filename";
            }
        }
        // Check Essential.
        if (file_exists("$CFG->dirroot/theme/essential/layout/includes/$filename")) {
            return "$CFG->dirroot/theme/essential/layout/includes/$filename";
        } else if (!empty($CFG->themedir) and file_exists("$CFG->themedir/essential/layout/includes/$filename")) {
            return "$CFG->themedir/essential/layout/includes/$filename";
        } else {
            return dirname(__FILE__)."$filename";
        }
    }

    /**
     * Includes the given folder for the theme in the caller.
     * If it does not exist for the Essential child theme then the parent is checked.
     * @param string $folder Folder to include relative to the theme root.
     * @return void.
     */
    static public function include_folder($folder) {
        global $CFG, $PAGE;
        $themedir = $PAGE->theme->dir;
        $thefolder = false;
        // Check only if a child of 'Essential'.
        if (in_array('essential', $PAGE->theme->parents)) {
            $themename = $PAGE->theme->name;
            if (file_exists("$themedir/$folder")) {
                $thefolder = "$themedir/$folder";
            } else if (file_exists("$CFG->dirroot/theme/$themename/$folder")) {
                $thefolder = "$CFG->dirroot/theme/$themename/$folder";
            } else if (!empty($CFG->themedir) and file_exists("$CFG->themedir/$themename/$folder")) {
                $thefolder = "$CFG->themedir/$themename/$folder";
            }
        }
        if (!$thefolder) {
            // Check Essential.
            if (file_exists("$CFG->dirroot/theme/essential/$folder")) {
                $thefolder = "$CFG->dirroot/theme/essential/$folder";
            } else if (!empty($CFG->themedir) and file_exists("$CFG->themedir/essential/$folder")) {
                $thefolder = "$CFG->themedir/essential/$folder";
            } else {
                $thefolder = dirname(__FILE__)."$folder";
            }
        }
        foreach (glob($thefolder.'/*.php') as $filename) {
            require_once $filename;
        }
    }

    static public function showslider() {
        global $CFG;
        $noslides = self::get_setting('numberofslides');
        if ($noslides && (intval($CFG->version) >= 2013111800)) {
            $devicetype = \core_useragent::get_device_type(); // In moodlelib.php.
            if (($devicetype == "mobile") && self::get_setting('hideonphone')) {
                $noslides = false;
            } else if (($devicetype == "tablet") && self::get_setting('hideontablet')) {
                $noslides = false;
            }
        }
        return $noslides;
    }

    static public function render_slide($i, $captionoptions, $theme = null) {

        if (empty($theme)) {
            if (empty(self::$theme)) {
                self::$theme = \theme_config::load('essential');
            }
            $theme = self::$theme;
        }

        $slideurl = self::get_setting('slide' . $i . 'url', false, $theme);
        $slideurltarget = self::get_setting('slide' . $i . 'target', false, $theme);
        $slidetitle = self::get_setting('slide' . $i, true, $theme);
        $slidecaption = self::get_setting('slide' . $i . 'caption', true, $theme);
        if ($captionoptions == 0) {
            $slideextraclass = ' side-caption';
        } else {
            $slideextraclass = '';
        }
        $slideextraclass .= ($i === 1) ? ' active' : '';
        $slideimagealt = strip_tags($slidetitle);

        // Get slide image or fallback to default.
        $slideimage = self::get_setting('slide' . $i . 'image', false, $theme);
        if ($slideimage) {
            $slideimage = $theme->setting_file_url('slide' . $i . 'image', 'slide' . $i . 'image');
        } else {
            global $OUTPUT;
            $slideimage = $OUTPUT->pix_url('default_slide', 'theme');
        }

        if ($slideurl) {
            $slide = '<a href="' . $slideurl . '" target="' . $slideurltarget . '" class="item' . $slideextraclass . '">';
        } else {
            $slide = '<div class="item' . $slideextraclass . '">';
        }

        if ($captionoptions == 0) {
            $slide .= '<div class="container-fluid">';
            $slide .= '<div class="row-fluid">';
        
            if ($slidetitle || $slidecaption) {
                $slide .= '<div class="span5 the-side-caption">';
                $slide .= '<div class="the-side-caption-content">';
                $slide .= '<h4>' . $slidetitle . '</h4>';
                $slide .= '<p>' . $slidecaption . '</p>';
                $slide .= '</div>';
                $slide .= '</div>';
                $slide .= '<div class="span7">';
            } else {
                $slide .= '<div class="span10 offset1 nocaption">';
            }
            $slide .= '<div class="carousel-image-container">';
            $slide .= '<img src="' . $slideimage . '" alt="' . $slideimagealt . '" class="carousel-image"/>';
            $slide .= '</div>';
            $slide .= '</div>';

            $slide .= '</div>';
            $slide .= '</div>';
        } else {
            $nocaption = (!($slidetitle || $slidecaption)) ? ' nocaption' : '';
            $slide .= '<div class="carousel-image-container'.$nocaption.'">';
            $slide .= '<img src="' . $slideimage . '" alt="' . $slideimagealt . '" class="carousel-image"/>';
            $slide .= '</div>';

            // Output title and caption if either is present
            if ($slidetitle || $slidecaption) {
                $slide .= '<div class="carousel-caption">';
                $slide .= '<div class="carousel-caption-inner">';
                $slide .= '<h4>' . $slidetitle . '</h4>';
                $slide .= '<p>' . $slidecaption . '</p>';
                $slide .= '</div>';
                $slide .= '</div>';
            }
        }
        $slide .= ($slideurl) ? '</a>' : '</div>';

        return $slide;
    }

    static public function render_slide_controls($left) {
        $faleft = 'left';
        $faright = 'right';
        if (!$left) {
            $temp = $faleft;
            $faleft = $faright;
            $faright = $temp;
        }
        $prev = '<a class="left carousel-control" href="#essentialCarousel" data-slide="prev"><i class="fa fa-chevron-circle-' . $faleft . '"></i></a>';
        $next = '<a class="right carousel-control" href="#essentialCarousel" data-slide="next"><i class="fa fa-chevron-circle-' . $faright . '"></i></a>';

        return $prev . $next;
    }

    static public function get_nav_links($course, $sections, $sectionno) {
        // FIXME: This is really evil and should by using the navigation API.
        $course = \course_get_format($course)->get_course();
        $left = 'left';
        $right = 'right';
        if (\right_to_left()) {
            $temp = $left;
            $left = $right;
            $right = $temp;
        }
        $previousarrow = '<i class="fa fa-chevron-circle-' . $left . '"></i>';
        $nextarrow = '<i class="fa fa-chevron-circle-' . $right . '"></i>';
        $canviewhidden = \has_capability('moodle/course:viewhiddensections', \context_course::instance($course->id))
                or ! $course->hiddensections;

        $links = array('previous' => '', 'next' => '');
        $back = $sectionno - 1;
        while ($back > 0 and empty($links['previous'])) {
           if ($canviewhidden || $sections[$back]->uservisible) {
                $params = array('id' => 'previous_section');
                if (!$sections[$back]->visible) {
                    $params['class'] = 'dimmed_text';
                }
                $previouslink = \html_writer::start_tag('div', array('class' => 'nav_icon'));
                $previouslink .= $previousarrow;
                $previouslink .= \html_writer::end_tag('div');
                $previouslink .= \html_writer::start_tag('span', array('class' => 'text'));
                $previouslink .= \html_writer::start_tag('span', array('class' => 'nav_guide'));
                $previouslink .= \get_string('previoussection', 'theme_essential');
                $previouslink .= \html_writer::end_tag('span');
                $previouslink .= \html_writer::empty_tag('br');
                $previouslink .= \get_section_name($course, $sections[$back]);
                $previouslink .= \html_writer::end_tag('span');
                $links['previous'] = \html_writer::link(course_get_url($course, $back), $previouslink, $params);
            }
            $back--;
        }

        $forward = $sectionno + 1;
        while ($forward <= $course->numsections and empty($links['next'])) {
            if ($canviewhidden || $sections[$forward]->uservisible) {
                $params = array('id' => 'next_section');
                if (!$sections[$forward]->visible) {
                    $params['class'] = 'dimmed_text';
                }
                $nextlink = \html_writer::start_tag('div', array('class' => 'nav_icon'));
                $nextlink .= $nextarrow;
                $nextlink .= \html_writer::end_tag('div');
                $nextlink .= \html_writer::start_tag('span', array('class' => 'text'));
                $nextlink .= \html_writer::start_tag('span', array('class' => 'nav_guide'));
                $nextlink .= \get_string('nextsection', 'theme_essential');
                $nextlink .= \html_writer::end_tag('span');
                $nextlink .= \html_writer::empty_tag('br');
                $nextlink .= \get_section_name($course, $sections[$forward]);
                $nextlink .= \html_writer::end_tag('span');
                $links['next'] = \html_writer::link(course_get_url($course, $forward), $nextlink, $params);
            }
            $forward++;
        }

        return $links;
    }

    static public function print_single_section_page(&$that, &$courserenderer, $course, $sections, $mods, $modnames, $modnamesused,
        $displaysection) {
        global $PAGE;

        $modinfo = \get_fast_modinfo($course);
        $course = \course_get_format($course)->get_course();

        // Can we view the section in question?
        if (!($sectioninfo = $modinfo->get_section_info($displaysection))) {
            // This section doesn't exist
            print_error('unknowncoursesection', 'error', null, $course->fullname);
            return false;
        }

        if (!$sectioninfo->uservisible) {
            if (!$course->hiddensections) {
                echo $that->start_section_list();
                echo $that->section_hidden($displaysection);
                echo $that->end_section_list();
            }
            // Can't view this section.
            return false;
        }

        // Copy activity clipboard..
        echo $that->course_activity_clipboard($course, $displaysection);
        $thissection = $modinfo->get_section_info(0);
        if ($thissection->summary or ! empty($modinfo->sections[0]) or $PAGE->user_is_editing()) {
            echo $that->start_section_list();
            echo $that->section_header($thissection, $course, true, $displaysection);
            echo $courserenderer->course_section_cm_list($course, $thissection, $displaysection);
            echo $courserenderer->course_section_add_cm_control($course, 0, $displaysection);
            echo $that->section_footer();
            echo $that->end_section_list();
        }

        // Start single-section div
        echo \html_writer::start_tag('div', array('class' => 'single-section'));

        // The requested section page.
        $thissection = $modinfo->get_section_info($displaysection);

        // Title with section navigation links.
        $sectionnavlinks = $that->get_nav_links($course, $modinfo->get_section_info_all(), $displaysection);

        // Construct navigation links
        $sectionnav = \html_writer::start_tag('nav', array('class' => 'section-navigation'));
        $sectionnav .= $sectionnavlinks['previous'];
        $sectionnav .= $sectionnavlinks['next'];
        $sectionnav .= \html_writer::empty_tag('br', array('style' => 'clear:both'));
        $sectionnav .= \html_writer::end_tag('nav');
        $sectionnav .= \html_writer::tag('div', '', array('class' => 'bor'));

        // Output Section Navigation
        echo $sectionnav;

        // Define the Section Title
        $sectiontitle = '';
        $sectiontitle .= \html_writer::start_tag('div', array('class' => 'section-title'));
        // Title attributes
        $titleattr = 'title';
        if (!$thissection->visible) {
            $titleattr .= ' dimmed_text';
        }
        $sectiontitle .= \html_writer::start_tag('h3', array('class' => $titleattr));
        $sectiontitle .= \get_section_name($course, $displaysection);
        $sectiontitle .= \html_writer::end_tag('h3');
        $sectiontitle .= \html_writer::end_tag('div');

        // Output the Section Title.
        echo $sectiontitle;

        // Now the list of sections..
        echo $that->start_section_list();

        echo $that->section_header($thissection, $course, true, $displaysection);

        // Show completion help icon.
        $completioninfo = new \completion_info($course);
        echo $completioninfo->display_help_icon();

        echo $courserenderer->course_section_cm_list($course, $thissection, $displaysection);
        echo $courserenderer->course_section_add_cm_control($course, $displaysection, $displaysection);
        echo $that->section_footer();
        echo $that->end_section_list();

        // Close single-section div.
        echo \html_writer::end_tag('div');
    }

    /**
     * Checks if the user is switching colours with a refresh
     *
     * If they are this updates the users preference in the database
     */
    static protected function check_colours_switch() {
        $colours = \optional_param('essentialcolours', null, PARAM_ALPHANUM);
        if (in_array($colours, array('default', 'alternative1', 'alternative2', 'alternative3', 'alternative4'))) {
            \set_user_preference('theme_essential_colours', $colours);
        }
    }

    /**
     * Adds the JavaScript for the colour switcher to the page.
     *
     * The colour switcher is a YUI moodle module that is located in
     *     theme/udemspl/yui/udemspl/udemspl.js
     *
     * @param moodle_page $page
     */
    static public function initialise_colourswitcher(\moodle_page $page) {
        self::check_colours_switch();
        \user_preference_allow_ajax_update('theme_essential_colours', PARAM_ALPHANUM);
        $page->requires->yui_module(
                'moodle-theme_essential-coloursswitcher', 'M.theme_essential.initColoursSwitcher',
                array(array('div' => '.dropdown-menu'))
        );
    }

    /**
     * Gets the theme colours the user has selected if enabled or the default if they have never changed.
     *
     * @param string $default The default theme colors to use.
     * @return string The theme colours the user has selected.
     */
    static public function get_colours($default = 'default') {
        $preference = \get_user_preferences('theme_essential_colours', $default);
        foreach (range(1, 4) as $alternativethemenumber) {
            if ($preference == 'alternative' . $alternativethemenumber && self::get_setting('enablealternativethemecolors' . $alternativethemenumber)) {
                return $preference;
            }
        }
        return $default;
    }

    static public function set_font($css, $type, $fontname) {
        $familytag = '[[setting:' . $type .'font]]';
        $facetag = '[[setting:fontfiles' . $type . ']]';
        if (empty($fontname)) {
            $familyreplacement = 'Verdana';
            $facereplacement = '';
        } else if (\theme_essential\toolbox::get_setting('fontselect') === '3') {
            static $theme;
            if (empty($theme)) {
                $theme = \theme_config::load('essential');  // $theme needs to be us for child themes.
            }

            $fontfiles = array();
            $fontfileeot = $theme->setting_file_url('fontfileeot' . $type, 'fontfileeot' . $type);
            if (!empty($fontfileeot)) {
                $fontfiles[] = "url('" . $fontfileeot . "?#iefix') format('embedded-opentype')";
            }
            $fontfilewoff = $theme->setting_file_url('fontfilewoff' . $type, 'fontfilewoff' . $type);
            if (!empty($fontfilewoff)) {
                $fontfiles[] = "url('" . $fontfilewoff . "') format('woff')";
            }
            $fontfilewofftwo = $theme->setting_file_url('fontfilewofftwo' . $type, 'fontfilewofftwo' . $type);
            if (!empty($fontfilewofftwo)) {
                $fontfiles[] = "url('" . $fontfilewofftwo . "') format('woff2')";
            }
            $fontfileotf = $theme->setting_file_url('fontfileotf' . $type, 'fontfileotf' . $type);
            if (!empty($fontfileotf)) {
                $fontfiles[] = "url('" . $fontfileotf . "') format('opentype')";
            }
            $fontfilettf = $theme->setting_file_url('fontfilettf' . $type, 'fontfilettf' . $type);
            if (!empty($fontfilettf)) {
                $fontfiles[] = "url('" . $fontfilettf . "') format('truetype')";
            }
            $fontfilesvg = $theme->setting_file_url('fontfilesvg' . $type, 'fontfilesvg' . $type);
            if (!empty($fontfilesvg)) {
                $fontfiles[] = "url('" . $fontfilesvg . "') format('svg')";
            }

            if (!empty($fontfiles)) {
                $familyreplacement = '"'.$fontname.'"';
                $facereplacement = '@font-face {' . PHP_EOL . 'font-family: "' . $fontname . '";' . PHP_EOL;
                $facereplacement .= !empty($fontfileeot) ? "src: url('" . $fontfileeot . "');" . PHP_EOL : '';
                $facereplacement .= "src: ";
                $facereplacement .= implode("," . PHP_EOL . " ", $fontfiles);
                $facereplacement .= ";";
                $facereplacement .= '' . PHP_EOL . "}";
            } else {
                // No files back to default.
                $familyreplacement = 'Verdana';
                $facereplacement = '';
            }
        } else {
            $familyreplacement = '"'.$fontname.'"';
            $facereplacement = '';
        }

        $css = str_replace($familytag, $familyreplacement, $css);
        $css = str_replace($facetag, $facereplacement, $css);

        return $css;
    }

    static public function set_color($css, $themecolor, $tag, $default) {
        if (!($themecolor)) {
            $replacement = $default;
        } else {
            $replacement = $themecolor;
        }
        $css = str_replace($tag, $replacement, $css);
        return $css;
    }

    static public function set_alternativecolor($css, $type, $customcolor, $defaultcolor) {
        $tag = '[[setting:alternativetheme' . $type . ']]';
        if (!($customcolor)) {
            $replacement = $defaultcolor;
        } else {
            $replacement = $customcolor;
        }
        $css = str_replace($tag, $replacement, $css);
        return $css;
    }

    static public function set_headerbackground($css, $headerbackground) {
        global $OUTPUT;
        $tag = '[[setting:headerbackground]]';
        if ($headerbackground) {
            $replacement = $headerbackground;
        } else {
            $replacement = $OUTPUT->pix_url('bg/header', 'theme');
        }
        $css = str_replace($tag, $replacement, $css);
        return $css;
    }

    static public function set_pagebackground($css, $pagebackground) {
        $tag = '[[setting:pagebackground]]';
        if (!($pagebackground)) {
            $replacement = 'none';
        } else {
            $replacement = 'url(\'' . $pagebackground . '\')';
        }
        $css = str_replace($tag, $replacement, $css);
        return $css;
    }

    static public function set_pagebackgroundstyle($css, $style) {
        $tagattach = '[[setting:backgroundattach]]';
        $tagrepeat = '[[setting:backgroundrepeat]]';
        $tagsize = '[[setting:backgroundsize]]';
        $replacementattach = 'fixed';
        $replacementrepeat = 'no-repeat';
        $replacementsize = 'cover';
        if ($style === 'tiled') {
            $replacementrepeat = 'repeat';
            $replacementsize = 'initial';
        } else if ($style === 'stretch') {
            $replacementattach = 'scroll';
        }

        $css = str_replace($tagattach, $replacementattach, $css);
        $css = str_replace($tagrepeat, $replacementrepeat, $css);
        $css = str_replace($tagsize, $replacementsize, $css);
        return $css;
    }

    static public function set_marketingheight($css, $marketingheight) {
        $tag = '[[setting:marketingheight]]';
        $replacement = $marketingheight;
        if (!($replacement)) {
            $replacement = 100;
        }
        $css = str_replace($tag, $replacement . 'px', $css);
        return $css;
    }

    static public function set_marketingimage($css, $marketingimage, $setting) {
        $tag = '[[setting:' . $setting . ']]';
        if (!($marketingimage)) {
            $replacement = 'none';
        } else {
            $replacement = 'url(\'' . $marketingimage . '\')';
        }
        $css = str_replace($tag, $replacement, $css);
        return $css;
    }

    static public function set_customcss($css, $customcss) {
        $tag = '[[setting:customcss]]';
        $customcss = str_replace('themecredit', 'themepagefooter', $customcss);
        $replacement = $customcss;
        $css = str_replace($tag, $replacement, $css);
        return $css;
    }

    static public function set_logo($css, $logo) {
        $tag = '[[setting:logo]]';
        if (!($logo)) {
            $replacement = 'none';
        } else {
            $replacement = 'url(\'' . $logo . '\')';
        }
        $css = str_replace($tag, $replacement, $css);
        return $css;
    }

    static public function set_pagewidth($css, $pagewidth) {
        $tag = '[[setting:pagewidth]]';
        $imagetag = '[[setting:pagewidthimage]]';
        $replacement = $pagewidth;
        if (!($replacement)) {
            $replacement = '1200';
        }
        if ($replacement == "100") {
            $css = str_replace($tag, $replacement . '%', $css);
            $css = str_replace($imagetag, '90' . '%', $css);
        } else {
            $css = str_replace($tag, $replacement . 'px', $css);
            $css = str_replace($imagetag, $replacement . 'px', $css);
        }
        return $css;
    }

    /**
     * Convert a colour hex string to an opacity supporting rgba one.
     *
     * @param string $hex Hex RGB string.
     * @param float $opacity between 0.0 and 1.0.
     * @return string.
     */
    static public function hex2rgba($hex, $opacity) {
        $hex = str_replace("#", "", $hex);

        if (strlen($hex) == 3) {
            $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
            $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
            $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
        } else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }
        return "rgba($r, $g, $b, $opacity)";
    }

    /**
     * States if the browser is not IE9 or less.
     */
    static public function not_lte_ie9() {
        $properties = self::ie_properties();
        if (!is_array($properties)) {
            return true;
        }
        // We have properties, it is a version of IE, so is it greater than 9?
        return ($properties['version'] > 9.0);
    }

    /**
     * States if the browser is IE9 or less.
     */
    static public function lte_ie9() {
        $properties = self::ie_properties();
        if (!is_array($properties)) {
            return false;
        }
        // We have properties, it is a version of IE, so is it greater than 9?
        return ($properties['version'] <= 9.0);
    }

    /**
     * States if the browser is IE by returning properties, otherwise false.
     */
    static protected function ie_properties() {
        $properties = \core_useragent::check_ie_properties(); // In /lib/classes/useragent.php.
        if (!is_array($properties)) {
            return false;
        } else {
            return $properties;
        }
    }
}
