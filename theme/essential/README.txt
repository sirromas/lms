The "Essential" Moodle Theme
============================

With 2.5 now released Julian thought it time to take the opportunity to build a new theme that would push the new theme engine
to it's limits a bit. With that in mind he introduced the new "Essential" theme.  Now Julian has left us for Canvassian
adventures, David and Gareth took over development and maintenance.  And now David has left for pastures new, Gareth continues
to maintain and make improvements.

The idea of this theme is to make the site look as little like Moodle as possible. In this specific instance, it would be used
on sites where Moodle would potentially serve as a company homepage rather than just a course list.

Cool things to know about the theme.
 - It attempts to load as many as possible icons from a font
 - Most of what you think are "graphics" are actually the [Awesome font](http://fortawesome.github.io/Font-Awesome/)
 - The slider on the frontpage of the demo site is completely customisable through theme settings
 - I am really trying to push what [Bootstrap](http://twitter.github.io/bootstrap/) Grids can do. As such the theme is fully
   responsive.
 - The footer is all custom Moodle regions. This means blocks can be added. The footer of the demo site is full of HTML blocks in
   this instance
 - The Theme uses [Google web fonts](http://www.google.com/fonts/) to give it that extra bit of shazam!
 - Social Network icons appear at the top of the page dynamically based on theme settings
 - The entire colour scheme can be modified with theme settings
 - The homepage main area is just a label. The theme will ship with custom classes that you can set for tables and links to modify
   their formatting.  No knowledge of code is needed as you can use the text editor to do this. Documentation will be provided
   outlining what the additional classes are.

Original Author
===============
Julian Ridden
Moodle profile: https://moodle.org/user/profile.php?id=39680
Web profile:    http://au.linkedin.com/in/eduridden/

Previous Authors
================
David Bezemer
Moodle profile | https://moodle.org/user/profile.php?id=1416592
Web profile | http://www.davidbezemer.nl

Maintained by
=============
G J Barnard MSc. BSc(Hons)(Sndw). MBCS. CEng. CITP. PGCE.
Moodle profile | http://moodle.org/user/profile.php?id=442195
Web profile | http://about.me/gjbarnard

Free Software
=============
The Essential theme is 'free' software under the terms of the GNU GPLv3 License, please see 'COPYING.txt'.

It can be obtained for free from:
http://moodle.org/plugins/view.php?plugin=theme_essential
and
https://github.com/gjb2048/moodle-theme_essential/releases

You have all the rights granted to you by the GPLv3 license.  If you are unsure about anything, then the
FAQ - http://www.gnu.org/licenses/gpl-faq.html - is a good place to look.

If you reuse any of the code then I kindly ask that you make reference to the theme.

If you make improvements or bug fixes then I would appreciate if you would send them back to me by forking from
https://github.com/gjb2048/moodle-theme_essential and doing a 'Pull Request' so that the rest of the
Moodle community benefits.

Support
=======
As Essential is licensed under the GNU GPLv3 License it comes with NO support.  If you would like support from
me then I'm happy to provide it for a fee (please see my contact details above).  Otherwise, the 'Themes' forum:
moodle.org/mod/forum/view.php?id=46 is an excellent place to ask questions.

Sponsorships
============
This theme is provided to you for free, and if you want to express your gratitude for using this theme, please consider sponsoring
by:

PayPal - Please contact me via my 'Moodle profile' (above) for details as I am an individual and therefore am unable to have
'buy me now' buttons under their terms.

Flattr - https://flattr.com/profile/gjb2048

Sponsorships help to facilitate maintenance and allow me to provide you with more and better features.  Without your support the theme
cannot be maintained.

Sponsors
========
Sponsorships gratefully received with thanks from:
Mihai Bojonca, TCM International Institute.
Guido Hornig, actXcellence http://actxcellence.de
Delvon Forrester, Esparanza co uk
iZone
Anis Jradah

Customisation
=============
If you like this theme and would like me to customise it, transpose functionality to another theme or
build a new theme from scratch, then I offer competitive rates.  Please contact me via 'www.gjbarnard.co.uk/contact/'
or 'gjbarnard at gmail dot com' or 'about.me/gjbarnard' to discuss your requirements.

Required version of Moodle
==========================
This version works with Moodle version 2015051100.00 release 2.9 (Build: 20150511) and above within the 2.9 branch until the
next release.

Please ensure that your hardware and software complies with 'Requirements' in 'Installing Moodle' on
'docs.moodle.org/29/en/Installing_Moodle'.

Installation
============
 1. Ensure you have the version of Moodle as stated above in 'Required version of Moodle'.  This is essential as the
    theme relies on underlying core code that is out of my control.
 2. Login as an administrator and put Moodle in 'Maintenance Mode' so that there are no users using it bar you as the
    administrator.
 3. Copy the extracted 'essential' folder to the '/theme/' folder.
 4. Go to 'Site administration' -> 'Notifications' and follow standard the 'plugin' update notification.
 5. Select as the theme for the site.
 6. Put Moodle out of Maintenance Mode.

Upgrading
=========
 1. Ensure you have the version of Moodle as stated above in 'Required version of Moodle'.  This is essential as the
    theme relies on underlying core code that is out of my control.
 2. Login as an administrator and put Moodle in 'Maintenance Mode' so that there are no users using it bar you as the
    administrator.
 3. Make a backup of your old 'essential' folder in '/theme/' and then delete the folder.
 4. Copy the replacement extracted 'essential' folder to the '/theme/' folder.
 5. Go to 'Site administration' -> 'Notifications' and follow standard the 'plugin' update notification.
 6. If automatic 'Purge all caches' appears not to work by lack of display etc. then perform a manual 'Purge all caches'
    under 'Home -> Site administration -> Development -> Purge all caches'.
 7. Put Moodle out of Maintenance Mode.

Uninstallation
==============
 1. Put Moodle in 'Maintenance Mode' so that there are no users using it bar you as the administrator.
 2. Change the theme to another theme of your choice.
 3. In '/theme/' remove the folder 'essential'.
 4. Put Moodle out of Maintenance Mode.

Downgrading
===========
If for any reason you wish to downgrade to a previous version of the theme (unsupported) then this procedure will inform you of how
to do so:
1.  Ensure that you have a copy of the existing and older replacement theme files.
2.  Put Moodle into 'Maintenance mode' under 'Home -> Administration -> Site administration -> Server -> Maintenance mode', so that
    there are no users using it bar you as the administrator.
3.  Switch to a core theme, 'Clean' for example, under 'Home -> Administration -> Site administration -> Appearance -> Themes ->
    Theme selector -> Default'.
4.  In '/theme/' remove the folder 'essential' i.e. ALL of the contents - this is VITAL.
5.  Put in the replacement 'essential' folder into '/theme/'.
6.  In the database, remove the row with the 'plugin' of 'theme_essential' and 'name' of 'version' in the 'config_plugins' table,
    then in the 'config' table find the 'name' with the value 'allversionhash' and clear its 'value' field.  Perform a
    'Purge all caches' under 'Home -> Site administration -> Development -> Purge all caches'.
7.  Go back in as an administrator and follow standard the 'plugin' update notification.  If needed, go to
    'Site administration' -> 'Notifications' if this does not happen.
8.  Switch the theme back to 'Essential' under 'Home -> Administration -> Site administration -> Appearance -> Themes ->
    Theme selector -> Default'.
9.  Put Moodle out of 'Maintenance mode' under 'Home -> Administration -> Site administration -> Server -> Maintenance mode'.

CSlider
=======
The original version of Essential used 'CSlider' - 
  http://tympanus.net/codrops/2012/03/15/parallax-content-slider-with-css3-and-jquery/.

It has been removed because of licencing issues: https://github.com/gjb2048/moodle-theme_essential/issues/61

Please do not request that it be put back.  The licence that CSlider has is incompatible with GPLv3 and therefore cannot be a part
of or redistributed with the theme.

Videos and FitVids
==================
Essential uses FitVids.js - http://fitvidsjs.com/ - to make embedded videos responsive.  If you do not want this feature for a
particular video, then please add the class 'fitvidsignore' to the video element.

WOFF2 Font Support
==================
WOFF2 font support will automatically kick in if you are running Moodle 2.8.5+ (Build: 20150313) - 2014111005.01 or above.  If you
are and the settings do not appear on the font setting page when using 'Custom font' for 'fontselect', then perform a
'Purge all caches'.

Reporting issues
================
Before reporting an issue, please ensure that you are running the latest version for your release of Moodle.  It is essential
that you are operating the required version of Moodle as stated at the top - this is because the theme relies on core
functionality that is out of its control.

When reporting an issue you can post in the theme's forum on Moodle.org (currently 'moodle.org/mod/forum/view.php?id=46')
or check the issue list https://github.com/gjb2048/moodle-theme_essential/issues and if the problem does not exist, create an
issue.

It is essential that you provide as much information as possible, the critical information being the contents of the theme's 
'version.php' file.  Other version information such as specific Moodle version, theme name and version also helps.  A screen shot
can be really useful in visualising the issue along with any files you consider to be relevant.

New in 2.9.1.1
==============
- FIX: Issue #553: Section highlighter in topic course format.
- FIX: Issue #554: Slide interval: settings are ignored.
- FIX: Issue #556: Course management: course (sub-)categories are not indented.
- FIX: Editing pages legend text colour.

New in 2.9.1
============
- FIX: Issue #552: Alternative colors: layer in mobile layout with wrong color.
- NEW: Stable version based upon 2.9.0.10.

New in 2.9.0.10
===============
- FIX: Issue #541: PHP Fatal error: Class 'theme_essential\\theme_config' not found in /classes/toolbox.php on line 447.
- FIX: Issue #543: Alternative colors: box borders remain in standard color.
- FIX: Issue #544: Alternative colors: background hover always black in standard color scheme.
- FIX: Issue #547: Alternative colors: background hover on the frontpage.
- FIX: Issue #548: Slideshow: slidebuttoncolor does not have an effect.
- FIX: Issue #550: Breadcrumb menu "eaten away" when tighter window.
- FIX: Tweak collapsed navbar menu.
- FIX: Availability dialogue messed up.
- FIX: Editing button icon.
- FIX: RTL fixes in alternative colours.
- FIX: Tab colour fixes for default and alternative colours.
- FIX: Button colour fixes for default and alternative colours.
- FIX: Collapsed menu layout.
- NEW: Fourth alternative colour scheme.  Thanks to Christian Niemczik for supporting and funding the work on the Essential Theme.

New in 2.9.0.9
==============
- FIX: Issue #536: FitVids targets players with mp3 files.
- FIX: Issue #537: Activities and resources in 'stealth section' are shown in the course menu.  Partial fix, see: MDL-51465.
- FIX: Issue #538: Problem in core_renderer when outputs the messages menu containing HTML special chars.
- FIX: Issue #539: Incorrect path found in thirdpartylibs.
- FIX: Incorrect path in get_include_file(). 

New in 2.9.0.8
==============
- FIX: Issue #529: Divider does not show.
- FIX: MDL-42634.
- FIX: MDL-50323.
- FIX: MDL-51194.
- FIX: MDL-51229.
- FIX: Issue #520: Alternative carousel colours not working.
- FIX: Issue #535: Group mode icons not different.
- NEW: Issue #533: PIWIK Analytics and IP addresses.
- NEW: Improved PIWIK document title.
- NEW: Reduced size of floating button area.
- NEW: Added slider caption opacity when 'on top' slide caption option.
- UPD: Issue #530: Essential Custom Menu Hamburger Behaviour - Unable to fix, help wanted.
- UPD: Updated bootstrap.js such that all modules are included as discovered that 'modals' were missing.
- UPD: Tidy up icon colours and in alternatives.
- UPD: Changed course icon to colourable Font Awesome alternative.
- UPD: Refactor marketing spot settings in settings.php.

New in 2.9.0.7
==============
- FIX: Issue #523: Forum grids do not wrap.
- NEW: Issue #524: MDL-48202.
- NEW: Issue #525: MDL-46860.
- NEW: Issue #526: MDL-50533.
- FIX: Issue #527: ###### are not converted to dividers.
- FIX: Issue #528: Repeated show / hide of custom menu fails when small screen.
- FIX: Misplaced CSS attribute with no selector.
- FIX: Removed redundant maintenance selector.
- FIX: Removed login page selector.
- FIX: JavaScript tidy up and remove html5shiv support for IE8.
- FIX: Regression of initital fix for issue 517 whereby dropdown menus cannot be seen over the carousel when screen width is less
       than the @navbarCollapseWidth, see: https://moodle.org/mod/forum/discuss.php?d=317306#p1272655.
- NEW: Update to FontAwesome 4.4.0.
- NEW: Version alert on admin pages if installed on wrong Moodle version as per 'Required version of Moodle' above.

New in 2.9.0.6
==============
- FIX: Issue #514: Quiz feedback colours are not accessible.
- FIX: Issue #515: Gradebook Tabs Not Left Justified.
- FIX: Issue #517: Mobile nav button overflow.
- FIX: Issue #518: Lesson table padding removed.
- FIX: #adminsettings h3 colour in alternative colours.
- FIX: Course drag and drop icon tricky to use.
- FIX: Action menu hover text colour.
- FIX: Navbar and dropdown adjustments to make cohesive with alternative colours.
- FIX: Drag and Drop Image Qtype Drop Zones entry boxes too big.
- FIX: Quiz navigation block preview icon not FontAwesome instance.
- FIX: MDL-50869.
- NEW: MDL-37832.
- NEW: MDL-50711.

New in 2.9.0.5
==============
- FIX: Forum submit area background colour for alternative colours.
- FIX: More navbar, breadcrumb and block colour fixes.
- NEW: Added new user preference link to 'Preferences' sub-menu.
- NEW: Added alternative icon color setting.

New in 2.9.0.4
==============
- FIX: Refactored Navbar and menu colours.
- FIX: Improved CSS font code from Shoehorn.  Now font name only used if files are available when 'Font type selector'
       is 'Custom font'.  Otherwise reverts to default font name.
- FIX: Drop down menu colours when not used on a Navbar.
- NEW: Implemented MDL-50497.
- NEW: Add Composer support, issues #508 and #513.

New in 2.9.0.3
==============
- NEW: Change to autoloaded static toolbox class to reduce duplication and uncertaincy on $OUTPUT being the correct class.

New in 2.9.0.2
==============
- FIX: Form header icon repeated.
- NEW: Implement class autoloading for renderers.

New in 2.9.0.1
==============
- NEW: Update icons.
- NEW: Update quiz LESS.
- NEW: Update moodle LESS.
- NEW: Convert jQuery from pluginfile.php to AMD.
- NEW: Convert carousel to AMD.
- NEW: Update 'Essentials' child theme for M2.9 changes above.

New in 2.8.1.6
==============
- FIX: No 'loginas' URL when logged in as another user, ref: https://moodle.org/mod/forum/discuss.php?d=315453.
- FIX: More colour adjustments to navigation menus with alternative colours.

New in 2.8.1.5
==============
- FIX: Issue #469: Top menu bar message Update notifications messages blank.
- FIX: Issue #473: Cloze answer fields overflow to the right on mobiles.
- FIX: Issue #478: Unable to find CSS when themedir set but theme is in default dir.  Thanks to Tyler Bannister.
- FIX: Issue #479: "This Course" menu only displays on course home page.
- FIX: Issue #480: Calendar issues.
- FIX: Issue #481: Drag and drop img handle shown on front page calendar when editing.
- FIX: Issue #483: Colouring a heading in TinyMCE reverts back to paragraph / body font.
- FIX: Issue #485: My courses not using correct context for view hidden courses capability.
- FIX: Issue #490: Availability date selection dropdowns.
- FIX: Issue #496: Category icons number of courses not shown on front page category list.
- FIX: Issue #504: Essentials child theme will not inherit parent settings.  Thanks to Brendan Anderson.
- FIX: Issue #506: Essential Summary error.
- FIX: Tidy up alternative colours.
- FIX: Carousel control icons slightly clipped.
- FIX: Enrol users icon -> FontAwesome one.
- FIX: Slight tweak to floating headers in gradebook.
- FIX: Production LESS -> CSS issues when generating 'background:' attributes for colours, resulting in 'background:0 0;'.
- FIX: Responsive form issues as reported here: https://moodle.org/mod/forum/discuss.php?d=315157.
- FIX: Alternative colours in dock.
- FIX: Removed out of date 'bootstrapcdn' setting.
- NEW: Issue #503: Use Alternate Name in user menu as the main name.  Gratefully funded by Mark Whitington.
- NEW: Styled 'Exit Activity' link for SCORM activities.

New in 2.8.1.4
==============
- FIX: Social icons when collapsed.
- FIX: Default user pix as svg in IE.
- FIX: Print adjustments.
- FIX: Issue #466: Forum overflow.
- FIX: Issue #471: Descriptions on profile page truncated.
- FIX: Issue #475: Popup layout does not get all settings for fonts.php.
- FIX: Issue #476: Essential hidden category headings.
- NEW: Updated to FontAwesome 4.3.0 with WOFF2 font support.  Requires 2.8.5+ (Build: 20150313) - 
       https://moodle.org/mod/forum/discuss.php?d=307270
- NEW: Dynamic WOFF2 support based upon Moodle version - see: MDL-49074.

New in 2.8.1.3
==============
- FIX  : Adjust quiz report.
- FIX  : Adjust assignment grading to have blocks underneath for more space.
- REFIX: Issue #447: Essential overlapping of Admin Settings.
- FIX  : Issue #459: Messages Screen.
- FIX  : Issue #461: Missing function errors in 2.8.1.2 - upgrade now site is blank.
- FIX  : Issue #463: Notification Time Stamp Issue.
- FIX  : Issue #464: When 'layout' set then message screen is between 768px and 979px is broken.
- FIX  : Apply MDL-49078.

New in 2.8.1.2
==============
- FIX: Incredibly strange regression when changing theme to Essential.
- FIX: Issue #458: Header logo pushing social icons off header - smaller sized screens.
- FIX: Issue #460: jQuery instead of $.

New in 2.8.1.1
==============
- FIX: Issue #417: M2.8 Mail Settings page needs checking.
- FIX: Issue #422: Regression from #179 in the core_renderer.php file.
- FIX: Issue #423: Forum floating buttons.
- FIX: Issue #425: Copyright date localization.
- FIX: Issue #429: Front page content area set to "Show before login only" still paritally displays.
- FIX: Issue #432: Adjusted lang string for 'oldnavbardesc'.  Thanks to Mathieu Pelletier (https://github.com/mkpelletier) for
                   this.
- FIX: Issue #434: Explain if a logo uploaded then no header title will be shown.
- FIX: Issue #436: Header background colour setting.
- FIX: Issue #441: Messages Screen when on mobile ( - 767px ) - background not filling area (region-main).
- FIX: Issue #447: Essential overlapping of Admin Settings.
- FIX: Issue #449: Embedded question text alignment.
- FIX: Issue #450: Undefined variable fontselect in embedded question preview.
- FIX: Issue #451: Hidden categories not aligning correctly.
- FIX: Issue #452: Messages Screen when on mobile still not quite correct.
- FIX: Issue #454: Social icon hover text is odd with 'URL' postfix.
- FIX: Issue #455: IE9 4096 selector limit.
- FIX: Apply MDL-46183.
- FIX: Apply MDL-45930.
- FIX: Apply MDL-44907.
- FIX: Improved custom font file detection and serving.
- FIX: Serving of slide show images when the parent frontpage is used in a child theme.
- FIX: doctype() warning when debugging.
- FIX: Misc tweaks I spotted - look at the commit on 22/2/2015 for details.
- NEW: Issue #428: Add setting to customise header background image.  Thanks to Jerome Charaoui (https://github.com/jcharaoui) for
                   this.
- NEW: Issue #433: Add a 'This Course' dropdown menu.  Thanks to ActionJONA (https://github.com/ActionJONA) for the ported BCU
                   theme code.
- NEW: Added 'Essentials' child theme in 'essentials' sub-folder.  To use, read the 'Installation' instructions in
       'essentials/README.txt'.  The 'essentials' sub-folder is just a place to store and distribute the child theme.  It will NOT
       be available until you install it.
- NEW: Code refactoring to make child theme creation easier.
- NEW: LESS refactoring to make future transition to Bootstrap v3 easier.

New in 2.8.1
============
First stable release.
- FIX: Issue #342: Essential Theme (version 2014101000 2.7.8 Build: 2014091804) issue with IE9 and earlier.
- FIX: Issue #414: Slider not work properly in RTL.
- FIX: Issue #416: Missing background colour in breadcrumb and footer.

New in 2.8.0.2
==============
- FIX: Issue #348: Slider controls do not work in RTL.
- FIX: Issue #403: Enroll button does not work with two or more self-enrollment options.
- FIX: Issue #404: M2.8 Adding a question to a new Quiz needs checking.
- FIX: Issue #405: Extension of blocks into Footer region with "Edit Settings" on.
- FIX: Issue #406: Assignment types: Online Audio Recording.
- FIX: Issue #408: Show text of question in list.
- FIX: Issue #409: Single view in Grades references unknown block.
- FIX: Issue #411: Atto editor causing horizontal scroll bar.
- FIX: Issue #412: Drop down background should be themeurlcolour and not themetextcolour.
- NEW: Issue #410: Use admin preference for 'My courses' menu sort order.  Thanks to Tony Butler for this.

New in 2.8.0.1
==============
NOTE: Beta version - test servers only.  Use on production servers is not recommended or supported.
- FIX: Issue #309: Moodle 2.8 file manager issue.
- FIX: Issue #335: Sorting buttons on headings on gradebook in 2.8 overlap other cells
- FIX: Issue #376: Grade report overflow in M2.8.
- FIX: Issue #381: The grid exceeds limits of the central area of the forum.
- FIX: Issue #392: Essential quiz - edit mode - action btns broken.

New in 2.7.9.3
==============
- FIX: Issue #244: Rows too long on plugins overview page.
- FIX: Issue #310: Moodle TinyMCE editor issue.
                   Thanks to Mary Evans for the fix on: https://moodle.org/mod/forum/discuss.php?d=275976 and
                   https://github.com/zahrah- for testing.
- FIX: Issue #382: Missing style in mod_feedback.
- FIX: Issue #385: Embedded YouTube videos not working.
- FIX: Issue #387: Floating "Submit" area on mobiles is evil.  Also added 'Go to bottom' icon when applicable.
- FIX: Issue #388: Navbar overlaps "Enrol Users" window, z index?
- FIX: Issue #389: Slideshow data-slide-to index regression.
- FIX: Issue #391: question bank - strings of questionnames cuted.
- FIX: Issue #395: Small overlap on Course and Category Management page.
- FIX: Issue #396: Fine tuning required on Edit Quiz page.
- FIX: Issue #397: Docked blocks are not wide enough.
- FIX: Issue #402: Moving "Automatic redirect" window.
- FIX: MDL-48246 : YUI generated class 'hidepanelicon' not styled because of a typo.

New in 2.7.9.2
==============
- FIX: Issue #372: Hovering block on Grading page.
- FIX: Issue #377: Fixed width setting breaks carousel images.
- FIX: Issue #378: Breadcrumb error on many pages when set to 'hide'.
- FIX: Issue #379: Typo on slide settings page.

New in 2.7.9.1
==============
- FIX: Issue #371: Alert Icons not rendered.

New in 2.7.9
============
- FIX: Issue #326: Submit panel overlaps the message input area.
- FIX: Issue #329: Social icons in mobile view showing odd behaviour.
- FIX: Issue #330: Slider caption below causes jump.
- FIX: Issue #346: Affix header height when using the old navbar setting.
- FIX: Issue #349: Background missing in private messages.
- FIX: Issue #350: Background missing after posting to forum.
- FIX: Issue #352: Piwik function clash with local plugin version - https://moodle.org/plugins/view.php?plugin=local_analytics.
- FIX: Issue #356: Incorrect $filename in /pluginfile.php/1/theme_essential/style/<timestamp>/essential.css.
- FIX: Issue #357: Beside slider option layout issues.
- FIX: Issue #358: 2.7.9b issues.
- FIX: Issue #359: Further 2.7.9b issues.
- FIX: Issue #361: Tweak to quiz editing in 2.7.9b.
- FIX: Issue #366: My Grades view includes course name.
- FIX: Issue #367: Course discription summary box not wide enough.
- FIX: Issue #370: Fancy breadcrumb hidden courses are strikethrough.
- UPDATE         : moodle/core.less - MDL-47340 & MDL-47097.
- UPDATE         : moodle/course.less - MDL-47340.
- UPDATE         : moodle/responsive.less - MDL-47242.
- NEW: Issue #247: Provide option to prevent automatic collapse for breadcrumbs.
- NEW: Issue #327: Font file types.
- NEW: Issue #340: Show slider navigation icons only when mouse in the slider.
- NEW: Issue #354: Ability to turn FitVids on / off.
- NEW: Issue #364: Add Shoelace dynamic footer blocks.

New in 2.7.8
============
- FIX: Issue #248: Navbar overlay on activity selection popup.
- FIX: Issue #252: LESS background: transparent; being compiled as background: 0 0;
- FIX: Issue #254: Gradebook alignment.
- FIX: Issue #257: Header options cause navbar to display incorrectly.
- FIX: Issue #258: Message menu text wrapping in IE 11.
- FIX: Issue #259: In course page icon not appears.
- FIX: Issue #260: Preview Questions.
- FIX: Issue #263: Group Image alignment in Forums.
- FIX: Issue #264: Incorrect H1 content in Password change screen.
- FIX: Issue #268: When vertical, Marketing spot urls all link to the url for Marketing spot 1.
- FIX: Issue #271: Performance information not readable when theme colour is #ffffff.
- FIX: Issue #272: Email/URL for Help link not correctly parsed.
- FIX: Issue #277: Docking clash when not using old navbar.
- FIX: Issue #284: Testing 2.7.8a - Save changes bar offset to the right.
- FIX: Issue #285: Some colours need checking.
- FIX: Issue #286: CSS caching in Google Chrome.
- FIX: Issue #287: Embedded objects overlap the navigation elements.
- FIX: Issue #289: Conflict with Turnitin plugin's navbar.
- FIX: Issue #291: Carousel images distort.
- FIX: Issue #292: Google Fonts through https FIX.  Thanks to @PiotrNawrot.
- FIX: Issue #293: Multilang tags do not work with the course heading.
- FIX: Issue #294: Undefined variable icon.
- FIX: Issue #296: Quiz settings page.
- FIX: Issue #298: User profile fields - can't edit Categories.
- FIX: Issue #300: Piwik not working in 2.7.8c.
- FIX: Issue #301: Long resource/activity names overlap editing menu with editing on.
- FIX: Issue #302: Setting "Breadcrumb Style" to "hide" does not remove the breadcrumb background.
- FIX: Issue #304: Region not defined in M2.6.
- FIX: Issue #311: Quiz submission in RTL.
- FIX: Issue #312: Calendar header in RTL.
- FIX: Issue #315: Dropdown Menu Highlighting.
- FIX: Issue #318: Quiz Order and Paging - Alignment fine-tuning.
- FIX: Issue #319: Preview Question displays directly on background image.
- FIX: Issue #321: Theme not load essential.css when using https on login page.
- FIX: Issue #322: Menu colour needs checking.
- NEW: Issue #249: Implement RTL CSS switching.  Thank you to Nadav Kavalerchik and many others for testing.
- NEW: Issue #251: Marketing spots h tags.
- NEW: Issue #269: Marketing Spots Social Media Widgets.
- NEW: Issue #278: Site name needs to be formatted in line with MDL-47080.
- NEW: Issue #295: My Courses navbar link not working.
- NEW: Issue #305: Login button in menu bar on login page.

New in 2.7.7
============
- FIX: Issue #173: Message menu shows wrong time difference in Russian.
- FIX: Issue #175: Gradebook several bugs.
- FIX: Issue #179: User picture error.
- FIX: Issue #189: Gradebook - column text and sorting doesn't fit the column.
- FIX: Issue #191: Blocks not showing on the right of the frontpage for non-admins.
- FIX: Issue #196: Gradebook - vertical size in fixed column view.
- FIX: Issue #200: Category Edit / Delete Missing in newest version of Essential.
- FIX: Issue #208: Bug: Undefined function is_loggedin().
- FIX: Issue #209: Showing white background.
- FIX: Issue #211: Image size in message menu.
- FIX: Issue #220: Standard Moodle Chat interface fails to load.
- FIX: Issue #221: Missing string 'unreadnewnotification'.
- FIX: Issue #225: Blank pages after some times.
- FIX: Issue #228: Incorrect z-index in menu.
- FIX: Issue #233: User image wrong in IE.
- FIX: Issue #237: Homepage main content block layout issues.
- FIX: Issue #238: Logged in user block styling.
- FIX: Issue #241: Background image appears as text box background.
- FIX: Issue #243: View Full toggle button not functional 2.6.9b.
- FIX: Issue #246: Menu bar down arrow caret causes shift of menu.
- NEW: Issue #74 : Fonts are CDN only.
- NEW: Issue #139: Enter own CDN font names.
- NEW: Issue #149: Add setting to not show the site shortname.
- NEW: Issue #170: Option in settings to choose how the site title in header appears.
- NEW: Issue #176: Option to Hide Calendar, Private Files, Forum Posts and Discussions from Dropdown.
- NEW: Issue #178: Error when editing user preferences.
- NEW: Issue #180: Same height marketing spots.
- NEW: Issue #181: Marketing spot header same as navbar.
- NEW: Issue #185: Custom background image dimensions.
- NEW: Issue #186: Add edit links to custom frontpage content items.
- NEW: Issue #197: Gradebook overall average font colour.
- NEW: Issue #204: Windows Mobile Apps.
- NEW: Issue #212: Upgrade FontAwesome 4.2.0.
- NEW: Issue #223: How to make a sub menu from custom menu.
- NEW: Issue #226: Move slideshow slides loading to function in lib.php.
- NEW: Issue #235: Color of course navigation block.
- NEW: Issue #240: Header title not wrapping in mobile view.

New in 2.7.6b
=============
- FIX: Issue #205: Has the eye icon used to Enable/Disable an element been reversed in functionality?
- FIX: Issue #207: Fixed solution for #175 #196 - thanks @ppv1979.
- FIX: main site regions clean-up to prevent issues with blocks.
- FIX: huge code rewrite for menu items.
- FIX: optimized more icons to be loaded from font.
- NEW: New loading gif, reducing size by 500%.
- NEW: Issue #198: Collapsed Topics and other course formats (like core weeks) need print single page the same.
       Course formats catered for: Topics, Weeks, Collapsed Topics, Columns, Grid and Noticeboard.  If you require others,
       please let us know.
       NOTE: If you are using the Collapsed Topics course format then you MUST have version 2.6.1.3 or above installed.
             If you are using the Columns course format then you MUST have version 2.6.1.1 or above installed.

New in 2.7.6a
=============
- FIX: Issue #159: Custom Category Icons not displaying in 2.7.5h (Build: 2014081404).
- FIX: Various code optimizations
- NEW: Issue #172: Google font character sets.  Implemented in #174 - thanks @vgango
- NEW: Issue #194: Centred slide show caption.

New in 2.7.6
============
- FIX: Issue #159: Custom Category Icons not displaying in 2.7.5h (Build: 2014081404).
- FIX: Issue #155: Social Icons Missing 2.7.5h
- FIX: Issue #156, #70, #56: Caption background colour frontpage slider & active slide colours
- FIX: Issue #164: When side pre is empty, then content area does not fill space when editing is off.
- FIX: Issue #165: Menu options has transparent background for a hidden block
- FIX: Issue #166: invalid email and white page for main administrator
- FIX: Issue #166: invalid email and white page for main administrator
- NEW: Full filter support in all custom areas (frontcontent, slider, alerts, footer), this includes the multilang filter
- NEW: Also show read messages, but distinct from unread ones
- NEW: Add courses titles to course pages
- NEW: Add styling to block regions for easier drag/drop regions, especially for footer
- NEW: Create github page for Essential theme
- NEW: Backported to Moodle 2.5!!!
- HELP WANTED: All language strings are now in AMOS (Moodle Language packs) please update your own language!

New in 2.7.5
============
- FIX: Issue #96:  2.7.4 breaks 'oldnavbar' setting.
- FIX: Issue #98:  Lang en/iosicondesc - Change 'them' to 'theme'.  Thanks to Skylar Kelty.
- FIX: Issue #101: Navbar not expanding properly on android mobile and tablet.
- FIX: Issue #107: No fixed width to header on login page.
- FIX: Issue #113: Remove FontAwesome from all links in section heading except for the edit icon.
- FIX: Issue #110: Refactor middle blocks wording, thanks @mkpelletier.
- FIX: Issue #109: Re-order middle blocks settings, thanks @mkpelletier.
- FIX: Issue #108: Add similar toggle options to front page content, thanks @mkpelletier.
- FIX: Issue #67:  Refactored so menus are renderer all separately.
- FIX: Issue #60:  Add styling to invisible courses in breadcrumb.
- FIX: Issue #126: Header logo right border syntax.
- FIX: Issue #127: Need to guard against uninitialised settings.
- FIX: Issue #128: Dropdown menu items are invisible when navigation bar text is white.
- FIX: Issue #134: Marketing height broken.
- FIX: Issue #135: Cannot edit topic summary.
- FIX: Issue #136: Too many docked icons.
- FIX: Issue #138: Breadcrumb above content / blocks.
- FIX: Issue #141: Check slider caption underneath in 2.7.5g.
- FIX: Issue #142: Check slider speed.
- FIX: Issue #144: Unmatched end tags in carousel.
- FIX: Issue #145: Language dropdown cut short when open.
- FIX: Correctly show no enrolments message when all courses are hidden.
- FIX: Reworked header menu and added responsive options to apps/social icons.
- FIX: Removed dnd upload status as it blocks the edit icons.
- FIX: Further work on RTL support.
- FIX: Theme Cleanup milestone completely finished!
- FIX: Further performance optimalizations using own CSS
- FIX: Provide fixes for IE8, Essential now largely works with IE8 as expected, but no support is given
- NEW: Issue #114: Add downgrade instructions.  See above.
- NEW: Removed bootstrapbase dependency for further minification.
- NEW: Split out alternative theme colours to reduce CSS loading when this is not enabled.
- NEW: Small tweak to site widths, now available options are 1400px, 1200px and 960px.
- NEW: Switched to bootstrap all in one for massive speed improvement (from 10 to 1 http request).
- NEW: Removed option for restricting block width as it was not possible to get this working properly.
- NEW: Issue #123: Add messages menu from Shoehorn and give it a FaceBook restyle
- NEW: Issue #14: Add a brand new user menu replacing the Dashboard menu
- NEW: Automatically collapsing breadcrumb
- NEW: Updated HTMLshiv
- NEW: Persistent link to your own grade report, available as long as you are enrolled in one visible course

New in 2.7.4
============
- FIX: Issue #68.  Expand all not showing on Edit course settings.
- FIX: Issue #58.  Add font colour setting.
- FIX: Issue #63.  Slider caption overlap.
- FIX: Issue #66.  Link Colour Not Working.
- FIX: Issue #85.  Header logo location in RTL.
- FIX: Reverted icons back to #999.
- FIX: Alternative colour sets all now have the same CSS, docking fixed and consistent with settings.
- FIX: Moved all RTL to separate sheet from main CSS.  Sheet 'essential-rtl' uses 'flipped' technology ('grunt-css-flip') whilst
       'rtl' sheet has manual styles.
- FIX: Issue #78.  Category icon issue in RTL.
- FIX: Issue #90.  XML Editor last used.
- FIX: Issue #88.  XML editor contrast issues.
- FIX: Issue #89.  Dock causes horizontal scroll bar.
- FIX: Issue #93.  Navbar / breadcrumb colour setting needed for contrast issues.
- FIX: Issue #94.  Docking centred text.
- FIX: Issue #95.  Navbar colour to far.
- FIX: Height of page header constrained when screen width < 767px and row-fluid spans go 100%.
- NEW: Alternative colour sets have text and link colour settings.
- NEW: Optimised svg's to be smaller.
- NEW: Warning about IE8 as M2.6 does not support it - http://docs.moodle.org/dev/Moodle_2.6_release_notes#Requirements.
- NEW: If the page width setting is narrow and there are custom menu items the navigation bar and page adjusts to cope.
- NEW: Optimised colour setting code in lib.php.
- NOTE: If you are using an RTL language then please read the instructions in the config.php file to swap to the RTL styles
        manually.  As a code solution to swapping files does not work at the moment and combining all possible CSS into one file
        leads to a big file with lots of redundant CSS.  This presents problems when it comes to portable devices and bandwidth.

New in 2.7.3
============
- FIX: Fixed slide show by replacing with Bootstrap 2.3.2 one.  Issue #18.
- FIX: Make background image fixed and set a background transparent colour
- FIX: Permanently replace edit icons with FontAwesome icons
- FIX: Massive cleanup in all files, reducing CSS with 25%
- FIX: Fixed all custom block regions to stick where they are
- FIX: Displaying footer and header on login page as well
- FIX: Further language file cleanup, looking for maintainers of all non-english language files!
- FIX: Optimize code for much improved processing time
- FIX: Optimize javascript to reduce browser lag on load
- FIX: Resolve layout issues on font-rendering
- FIX: Set layout options for responsive rendering, more mobile support to come soon
- FIX: Load fonts through htmlwriter (Thanks Mary :))
- FIX: Fix various alignment issues
- FIX: Fix popup/secure header overlay for quizzes
- FIX: optimize code to make loading much faster
- NEW: Reduced amount of fonts in theme, added legacy themes for websafe support
- NEW: New slider with up to 16 slides, full responsive
- NEW: Footer will go all the way to bottom on modern browsers (except IE of course)
- NEW: Removed output of summary to header due to potential exploits
- NEW: Breadcrumb styling
- NEW: Login Block styling
- NEW: Full custom category icon settings (Thanks Danny Wahl)
- NEW: Transparent fixed background when setting a background image

New in 2.7.2
============
- FIX: Slideshow CSS fixes
- FIX: Image alignment on slideshow
- NEW: Select slideshow background color
- NEW: Option to bring back the old navbar location
 
New in 2.7.1
============
- FIX: Numerous CSS fixes
- FIX: Translation fixes
- FIX: Updated Google Analytics code
- FIX: Cleanup of code in files
- FIX: Fixed logout page blocks in footer
- FIX: Now also outputs detailed performance info when selected
- FIX: Various menu features (messaging/badges) only enabled when feature is enabled
- NEW: Dutch translation
- NEW: Moved menu bar to top
- NEW: Now allows setting target on links
- NEW: New slideshow design (WIP)
 
New in 2.6.3
============
- FIX: Numerous CSS fixes
- FIX: Due to popular request reports are now 2 column again
- FIX: Significantly improved RTL support
- FIX: Back To Top link now moved to the right side so does not overlap on content
- FIX: Fixed layout of top icons.
- NEW: Can create alternative colour schemes for users to select.
- NEW: Can select icons for categories
- NEW: Add support for the max-layout-width feature when empty regions are used.
- NEW: Start Dutch translation

New in 2.6.2
============
- FIX: Numerous CSS fixes
- FIX: Third level dropdown in custom menu now works
- FIX: iOS7 custom menu now works when changed to a sing dropdown in portrait view
- FIX: Social networking icons now line up properly
- FIX: GoogleFonts will now load in HTTPS setups
- NEW: Frontpage content now goes full width if all blocks removed.

New in 2.6.1
============
- NEW: MAJOR UPDATES for 2.6 compatibility.
- NEW: Moved layouts to a more "Moodle standard" 1, 2 and 3 column layout.
- NEW: Can now add three columns of blocks to middle of the homepage under marketing spots.
- NEW: Theme setting added to allow admins to align frontage blocks to the left or right.
- NEW: Two designs for the slideshow available. One with image to the right, other with a background image.
- UPDATE: [Font Awesome 4.0.3](http://fontawesome.io/) now supported.
- UPDATE: Using new font setting to dynamically load the fonts.
- UPDATE: Removing autohide feature as no longer needed in Moodle 2.6
- FIX: Guest users no longer get "my courses" or "dashboard" dropdown menus.
- FIX: Nav Menu generates cleanly on IE.
- FIX: Gradebook now displays no blocks to maximise available space.
- FIX: Numerous CSS fixes and cleanup

New in 2.6
==========
- Added ability to select from 21 preset Google Font combinations or disable their use completely.
- Now includes additional Bootstrap JS plugins to allow for more dynamic formatting as shown on http://getbootstrap.com/javascript/
- New Frontpage Slideshow settings to allow to display; all the time, only before login, only after login or never.
- New Marketing Spots settings to allow to display; all the time, only before login, only after login or never.
- Course Labels are no longer in bold by default
- Now has a companion Mahara ePorfolio theme so you can keep them looking alike - https://github.com/moodleman/mahara-theme_essential
- Further minor bug fixes and tidy up.

New in 2.5.4
============
- Display current enrolled courses in dropdown menu and choose terminology (modules, courses, classes or units).
- New 'My Dashboard" in custommenu provides quick links to student tools. Can be disabled in theme settings.
- iOS home screen icons now built in. Can upload your own via settings.
- Alerts for users can be added to the frontpage. (Originally dreamed up by Shaun Daubney and re-coded by me).
- Theme options to connect to Google Analytics.
- Advanced Google Analytics function allowing Clean URL's for better reporting. Contributed by @basbrands and @ghenrick. More info
  on this feature can be found on http://www.somerandomthoughts.com/blog/2012/04/18/ireland-uk-moodlemoot-analytics-to-the-front/
- Significantly improved gradebook formatting.
- Toggle in Theme settings determines if FontAwesome is loaded from the theme or from the netdna.bootstrapcdn.com source.
- Back to top button for course pages.
- New "Frontpage Content" box to add custom content in between the slideshow and marketing spots.

Fixes in 2.5.4
==============
- Fix to frontpage slideshow. First slide now loads properly.
- Updated include method to minimise conflicts with 3rd party plugins
- Code significantly optimised. (about 1/5 less lines!)
- Many CSS Fixes
- IMPORTANT: Theme requires Moodle 2.5.1 or higher

New in 2.5.3
============
- New Settings screen just for colour selection
- Admin can now toggle to use "autohide" functionality in courses.
- Admin now upload their own background image
- Can now set colours for footer area
- Cleanup of required images (Theme now only uses 4 images)
- Performance info now neatly formatted.
- Fixed Custom Menu colour in IE8 and IE9
- Can now upload optional images into the marketing spots
- Now available in English, German, Russian, Turkish and Spanish (many thanks to the Moodle Community for translating)
- New Pinterest, Instagram, Skype and the Russian VK social networks added.
- Can now add links to Mobile apps on the iOS App Store and Google Play
- New formatting on login block
- Minor CSS Fixes
- EXPERIMENTAL: New course editing icons formatted and built with Font Awesome can now be used.
 
New in  2.5.2
=============
 - New theme setting to have user image show in the header when logged in.
 - Admin can choose to revert courses to a "standard" layout with blocks on the left and right sides
 - Admin can choose the default Navbar/breadcrumb separator
 - Frontage now is a 2 column layout by popular demand
 - Icons in navigation and administration block now rendered with Awesome Font
 - Font Awesome now loaded and cached through lib.php. Should improve performance
 - Minor CSS fixes
 
See the theme in Action
=======================
A video showing many of the core features is available for viewing at http://vimeo.com/69683774 and
https://www.youtube.com/watch?v=grhmR5PmWtA

Documentation
=============
As always, documentation is a work in progress. Available documentation is available at http://docs.moodle.org/28/en/Essential_theme
If you have questions you can post them in the issue tracker at https://github.com/gjb2048/moodle-theme_essential/issues