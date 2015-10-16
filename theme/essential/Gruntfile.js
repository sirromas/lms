/**
 * Gruntfile for compiling theme_essential .less files.
 *
 * This file configures tasks to be run by Grunt
 * http://gruntjs.com/ for the current theme.
 *
 *
 * Requirements:
 * -------------
 * nodejs, npm, grunt-cli.
 *
 * Installation:
 * -------------
 * node and npm: instructions at http://nodejs.org/
 *
 * grunt-cli: `[sudo] npm install -g grunt-cli`
 *
 * node dependencies: run `npm install` in the root directory.
 *
 *
 * Usage:
 * ------
 * Call tasks from the theme root directory. Default behaviour
 * (calling only `grunt`) is to run the watch task detailed below.
 *
 *
 * Porcelain tasks:
 * ----------------
 * The nice user interface intended for everyday use. Provide a
 * high level of automation and convenience for specific use-cases.
 *
 * grunt watch   Watch the less directory (and all subdirectories)
 *               for changes to *.less files then on detection
 *               run 'grunt compile'
 *
 *               Options:
 *
 *               --dirroot=<path>  Optional. Explicitly define the
 *                                 path to your Moodle root directory
 *                                 when your theme is not in the
 *                                 standard location.
 * grunt compile Run the .less files through the compiler, create the
 *               RTL version of the output, then run decache so that
 *               the results can be seen on the next page load.
 *
 * Options:
 *
 *               --dirroot=<path>  Optional. Explicitly define the
 *                                 path to your Moodle root directory
 *                                 when your theme is not in the
 *                                 standard location.
 *
 *               --build=<type>    Optional. 'p'(default) or 'd'. If 'p'
 *                                 then 'production' CSS files.  If 'd'
 *                                 then 'development' CSS files unminified
 *                                 and with source map to less files.
 *
 *               --urlprefix=<path> Optional. Explicitly define
 *                                  the path between the domain
 *                                  and the installation in the
 *                                  URL, i.e. /moodle27 being:
 *                                  --urlprefix=/moodle27
 *
 * grunt amd     Create the Asynchronous Module Definition JavaScript files.  See: MDL-49046.
 *               Done here as core Gruntfile.js currently *nix only.
 *
 * grunt svg                 Change the colour of the SVGs in pix_core by
 *                           text replacing #999999 with a new hex colour.
 *                           Note this requires the SVGs to be #999999 to
 *                           start with or the replace will do nothing
 *                           so should usually be preceded by copying
 *                           a fresh set of the original SVGs.
 *
 *                           Options:
 *
 *                           --svgcolour=<hexcolour> Hex colour to use for SVGs
 *
 * Plumbing tasks & targets:
 * -------------------------
 * Lower level tasks encapsulating a specific piece of functionality
 * but usually only useful when called in combination with another.
 *
 * grunt less         Compile all less files.
 *
 * grunt decache      Clears the Moodle theme cache.
 *
 *                    Options:
 *
 *                    --dirroot=<path>  Optional. Explicitly define
 *                                      the path to your Moodle root
 *                                      directory when your theme is
 *                                      not in the standard location.
 *
 * grunt replace             Run all text replace tasks.
 *
 * grunt cssflip    Create essential-rtl.css by flipping the direction styles
 *                  in essential.css.  Ref: https://www.npmjs.org/package/css-flip
 *
 *
 * @package theme
 * @subpackage essential
 * @author G J Barnard - gjbarnard at gmail dot com and {@link http://moodle.org/user/profile.php?id=442195}
 * @author Based on code originally written by Joby Harding, Bas Brands, David Scotson and many other contributors. 
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

module.exports = function(grunt) {

    // Import modules.
    var path = require('path');

    // Theme Bootstrap constants.
    var LESSDIR         = 'less',
        MOODLEURLPREFIX = grunt.option('urlprefix') || '',
        THEMEDIR        = path.basename(path.resolve('.'));

    // PHP strings for exec task.
    var moodleroot = path.dirname(path.dirname(__dirname)),
        dirrootopt = grunt.option('dirroot') || process.env.MOODLE_DIR || '';

    // Allow user to explicitly define Moodle root dir.
    if ('' !== dirrootopt) {
        moodleroot = path.resolve(dirrootopt);
    }

    // Production / development.
    var build = grunt.option('build') || 'd'; // Development for 'watch' task.

    if ((build != 'p') && (build != 'd')) {
        build = 'p';
        console.log('-build switch only accepts \'p\' for production or \'d\' for development,');
        console.log('e.g. -build=p or -build=d.  Defaulting to development.');
    }

    var PWD = process.cwd();

    var decachephp = '../../admin/cli/purge_caches.php';

    var svgcolour = grunt.option('svgcolour') || '#999999';

    grunt.initConfig({
        less: {
            essential_p: {
                options: {
                    compress: false,
                    cleancss: false,
                    paths: "./less",
                    report: 'min',
                    sourceMap: false,
                },
                src: 'less/essential.less',
                dest: 'style/essential.css'
            },
            editor_p: {
                options: {
                    compress: false,
                    cleancss: false,
                    paths: "./less",
                    report: 'min',
                    sourceMap: false,
                },
                src: 'less/editor.less',
                dest: 'style/editor.css'
            },
            moodle_rtl_p: {
                options: {
                    compress: false,
                    cleancss: false,
                    paths: "./less",
                    report: 'min',
                    sourceMap: false,
                },
                src: 'less/moodle-rtl.less',
                dest: 'style/moodle-rtl.css'
            },
            bootstrap_pix_p: {
                options: {
                    compress: false,
                    cleancss: false,
                    paths: "./less",
                    report: 'min',
                    sourceMap: false,
                },
                src: 'less/bootstrap-pix.less',
                dest: 'style/bootstrap-pix.css'
            },
            moodle_pix_p: {
                options: {
                    compress: false,
                    cleancss: false,
                    paths: "./less",
                    report: 'min',
                    sourceMap: false,
                },
                src: 'less/moodle-pix.less',
                dest: 'style/moodle-pix.css'
            },
            essential_pix_p: {
                options: {
                    compress: false,
                    cleancss: false,
                    paths: "./less",
                    report: 'min',
                    sourceMap: false,
                },
                src: 'less/essential-pix.less',
                dest: 'style/essential-pix.css'
            },
            fontawesome_woff2_p: {
                options: {
                    compress: false,
                    cleancss: false,
                    paths: "./less",
                    report: 'min',
                    sourceMap: false,
                },
                src: 'less/fontawesome-woff2.less',
                dest: 'style/fontawesome-woff2.css'
            },
            fontawesome_no_woff2_p: {
                options: {
                    compress: false,
                    cleancss: false,
                    paths: "./less",
                    report: 'min',
                    sourceMap: false,
                },
                src: 'less/fontawesome-no-woff2.less',
                dest: 'style/fontawesome-no-woff2.css'
            },
            fontawesome_p: {
                options: {
                    compress: false,
                    cleancss: false,
                    paths: "./less",
                    report: 'min',
                    sourceMap: false,
                },
                src: 'less/fontawesome.less',
                dest: 'style/fontawesome.css'
            },
            settings_p: {
                options: {
                    compress: false,
                    cleancss: false,
                    paths: "./less",
                    report: 'min',
                    sourceMap: false,
                },
                src: 'less/essential-settings.less',
                dest: 'style/essential-settings.css'
            },
            alternative_p: {
                options: {
                    compress: false,
                    cleancss: false,
                    paths: "./less",
                    report: 'min',
                    sourceMap: false,
                },
                src: 'less/essential-alternative.less',
                dest: 'style/essential-alternative.css'
            },
            essential_d: { // Flipped.
                options: {
                    compress: false,
                    cleancss: false,
                    paths: "./less",
                    report: 'min',
                    sourceMap: true,
                    sourceMapRootpath: MOODLEURLPREFIX + '/theme/' + THEMEDIR,
                    sourceMapFilename: 'style/essential.treasure.map'
                },
                src: 'less/essential.less',
                dest: 'style/essential.css'
            },
            editor_d: {
                options: {
                    compress: false,
                    cleancss: false,
                    paths: "./less",
                    report: 'min',
                    sourceMap: true,
                    sourceMapRootpath: MOODLEURLPREFIX + '/theme/' + THEMEDIR,
                    sourceMapFilename: 'style/editor.treasure.map'
                },
                src: 'less/editor.less',
                dest: 'style/editor.css'
            },
            moodle_rtl_d: {
                options: {
                    compress: false,
                    cleancss: false,
                    paths: "./less",
                    report: 'min',
                    sourceMap: true,
                    sourceMapRootpath: MOODLEURLPREFIX + '/theme/' + THEMEDIR,
                    sourceMapFilename: 'style/moodle-rtl.treasure.map'
                },
                src: 'less/moodle-rtl.less',
                dest: 'style/moodle-rtl.css'
            },
            bootstrap_pix_d: {
                options: {
                    compress: false,
                    cleancss: false,
                    paths: "./less",
                    report: 'min',
                    sourceMap: true,
                    sourceMapRootpath: MOODLEURLPREFIX + '/theme/' + THEMEDIR,
                    sourceMapFilename: 'style/bootstrap-pix.treasure.map'
                },
                src: 'less/bootstrap-pix.less',
                dest: 'style/bootstrap-pix.css'
            },
            moodle_pix_d: {
                options: {
                    compress: false,
                    cleancss: false,
                    paths: "./less",
                    report: 'min',
                    sourceMap: true,
                    sourceMapRootpath: MOODLEURLPREFIX + '/theme/' + THEMEDIR,
                    sourceMapFilename: 'style/moodle-pix.treasure.map'
                },
                src: 'less/moodle-pix.less',
                dest: 'style/moodle-pix.css'
            },
            essential_pix_d: {
                options: {
                    compress: false,
                    cleancss: false,
                    paths: "./less",
                    report: 'min',
                    sourceMap: true,
                    sourceMapRootpath: MOODLEURLPREFIX + '/theme/' + THEMEDIR,
                    sourceMapFilename: 'style/essential-pix.treasure.map'
                },
                src: 'less/essential-pix.less',
                dest: 'style/essential-pix.css'
            },
            fontawesome_woff2_d: {
                options: {
                    compress: false,
                    cleancss: false,
                    paths: "./less",
                    report: 'min',
                    sourceMap: true,
                    sourceMapRootpath: MOODLEURLPREFIX + '/theme/' + THEMEDIR,
                    sourceMapFilename: 'style/fontawesome-woff2.treasure.map'
                },
                src: 'less/fontawesome-woff2.less',
                dest: 'style/fontawesome-woff2.css'
            },
            fontawesome_no_woff2_d: {
                options: {
                    compress: false,
                    cleancss: false,
                    paths: "./less",
                    report: 'min',
                    sourceMap: true,
                    sourceMapRootpath: MOODLEURLPREFIX + '/theme/' + THEMEDIR,
                    sourceMapFilename: 'style/fontawesome-no-woff2.treasure.map'
                },
                src: 'less/fontawesome-no-woff2.less',
                dest: 'style/fontawesome-no-woff2.css'
            },
            fontawesome_d: {
                options: {
                    compress: false,
                    cleancss: false,
                    paths: "./less",
                    report: 'min',
                    sourceMap: true,
                    sourceMapRootpath: MOODLEURLPREFIX + '/theme/' + THEMEDIR,
                    sourceMapFilename: 'style/fontawesome.treasure.map'
                },
                src: 'less/fontawesome.less',
                dest: 'style/fontawesome.css'
            },
            settings_d: {
                options: {
                    compress: false,
                    cleancss: false,
                    paths: "./less",
                    report: 'min',
                    sourceMap: true,
                    sourceMapRootpath: MOODLEURLPREFIX + '/theme/' + THEMEDIR,
                    sourceMapFilename: 'style/essential-settings.treasure.map'
                },
                src: 'less/essential-settings.less',
                dest: 'style/essential-settings.css'
            },
            alternative_d: {
                options: {
                    compress: false,
                    cleancss: false,
                    paths: "./less",
                    report: 'min',
                    sourceMap: true,
                    sourceMapRootpath: MOODLEURLPREFIX + '/theme/' + THEMEDIR,
                    sourceMapFilename: 'style/essential-alternative.treasure.map'
                },
                src: 'less/essential-alternative.less',
                dest: 'style/essential-alternative.css'
            }
        },
        exec: {
            decache: {
                cmd: 'php "' + decachephp + '"',
                callback: function(error, stdout, stderror) {
                    // exec will output error messages
                    // just add one to confirm success.
                    if (!error) {
                        grunt.log.writeln("Moodle theme cache reset.");
                    }
                }
            }
        },
        watch: {
            // Watch for any changes to less files and compile.
            files: ["less/**/*.less"],
            tasks: ["compile"],
            options: {
                spawn: false
            }
        },
        cssflip: {
            rtl_p: {
                options: {
                    compress: false
                },
                src:  'style/essential.css',
                dest: 'style/essential-rtl.css'
            },
            rtl_d: {
                options: {
                    compress: false
                },
                src:  'style/essential.css',
                dest: 'style/essential-rtl.css'
            }
        },
        bless: {
            css: {
                options: {
                    cacheBuster: true,
                    compress: true,
                    logCount: true
                },
                files: {
                    'style/essential_ie9.css': 'style/essential.css',
                    'style/essential-rtl_ie9.css': 'style/essential-rtl.css'
                }
            }
         },
         cssmin: {
            essential_p: {
                files: [{
                    expand: true,
                    cwd: 'style',
                    src: ['essential.css', 'essential-rtl.css', 'bootstrap-pix.css', 'moodle-pix.css', 'essential-pix.css', 'essential-alternative.css', 'editor.css', 'fontawesome-woff2.css', 'fontawesome-no-woff2.css', 'fontawesome.css', 'moodle-rtl.css'],
                    dest: 'style',
                    ext: '.css'
                }]
            }
        },
        cssmetrics: {
            dist: {
                src: [
                    'style/*.css'
                ]
            }
        },
        copy: {
            svg_core: {
                 expand: true,
                 cwd:  'pix_core_originals/',
                 src:  '**',
                 dest: 'pix_core/',
            },
            svg_plugins: {
                 expand: true,
                 cwd:  'pix_plugins_originals/',
                 src:  '**',
                 dest: 'pix_plugins/',
            }
        },
        replace: {
            svg_colours_core: {
                src: 'pix_core/**/*.svg',
                    overwrite: true,
                    replacements: [{
                        from: '#999999',
                        to: svgcolour
                    }]
            },
            svg_colours_plugins: {
                src: 'pix_plugins/**/*.svg',
                    overwrite: true,
                    replacements: [{
                        from: '#999999',
                        to: svgcolour
                    }]
            },
            placeholder: {
                src: 'style/essential.css',
                    overwrite: true,
                    replacements: [{
                        from: '/* Essential placeholder */',
                        to: 'div#page::before { content: "Development version - recomplile LESS with \'grunt compile -build=p\' for production CSS."; font-size: 2em; margin-top: 24px; margin-bottom: 24px; line-height: 42px; text-align: center; }'
                    }]
            }
        },
        svgmin: {
            options: {
                plugins: [{
                    removeViewBox: false
                }, {
                    removeUselessStrokeAndFill: false
                }, {
                    convertPathData: { 
                        straightCurves: false
                   }
                }]
            },
            dist: {
                files: [{
                    expand: true,
                    cwd: 'pix_core',
                    src: ['**/*.svg'],
                    dest: 'pix_core/',
                    ext: '.svg'
                }, {
                    expand: true,
                    cwd: 'pix_plugins',
                    src: ['**/*.svg'],
                    dest: 'pix_plugins/',
                    ext: '.svg'
                }]
            }
        },
        jshint: {
            options: {jshintrc: moodleroot + '/.jshintrc'},
            files: ['**/amd/src/*.js']
        },
        uglify: {
            dynamic_mappings: {
                files: grunt.file.expandMapping(
                    ['**/src/*.js', '!**/node_modules/**'],
                    '',
                    {
                        cwd: PWD,
                        rename: function(destBase, destPath) {
                            destPath = destPath.replace('src', 'build');
                            destPath = destPath.replace('.js', '.min.js');
                            destPath = path.resolve(PWD, destPath);
                            return destPath;
                        }
                    }
                )
            }
        }
    });

    // Load contrib tasks.
    grunt.loadNpmTasks("grunt-contrib-less");
    grunt.loadNpmTasks("grunt-contrib-watch");
    grunt.loadNpmTasks('grunt-bless');
    grunt.loadNpmTasks("grunt-exec");
    grunt.loadNpmTasks("grunt-text-replace");
    grunt.loadNpmTasks("grunt-css-flip");
    grunt.loadNpmTasks("grunt-css-metrics");
    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-svgmin');

    // Load core tasks.
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-jshint');

    // Register tasks.
    grunt.registerTask("default", ["watch"]);
    grunt.registerTask("decache", ["exec:decache"]);

    grunt.registerTask("css", ["less:essential_"+build, "less:editor_"+build, "less:moodle_rtl_"+build, "less:settings_"+build, "less:bootstrap_pix_"+build, "less:moodle_pix_"+build, "less:essential_pix_"+build, "less:fontawesome_woff2_"+build, "less:fontawesome_no_woff2_"+build, "less:fontawesome_"+build, "less:alternative_"+build]);
    if (build == 'd') {
        grunt.registerTask("compile", ["css", "replace:placeholder", "cssflip:rtl_"+build, "bless", 'cssmetrics', "decache"]);
    } else {
        grunt.loadNpmTasks('grunt-contrib-cssmin');
        grunt.registerTask("compile", ["css", "cssflip:rtl_"+build, "cssmin:essential_p", "bless", 'cssmetrics', "decache"]);
    }
    grunt.registerTask("copy:svg", ["copy:svg_core", "copy:svg_plugins"]);
    grunt.registerTask("replace:svg_colours", ["replace:svg_colours_core", "replace:svg_colours_plugins"]);
    grunt.registerTask("svg", ["copy:svg", "replace:svg_colours", "svgmin"]);
    grunt.registerTask("amd", ["jshint", "uglify", "decache"]);
};
