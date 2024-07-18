module.exports = function (grunt) {
	// Project config
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),
		compress: {
			dist: {
				options: {
					archive: './dist/<%= pkg.name %>.zip',
					mode: 'zip',
				},
				files: [
					{ src: './acf-json/*.json', dest: '<%= pkg.name %>/' },
					{ src: './infinite/admin/css/**', dest: '<%= pkg.name %>/' },
					{ src: './infinite/admin/images/**', dest: '<%= pkg.name %>/' },
					{ src: './infinite/admin/js/**', dest: '<%= pkg.name %>/' },
					{ src: './infinite/admin/partials/**', dest: '<%= pkg.name %>/' },
					{ src: './infinite/admin/index.php', dest: '<%= pkg.name %>/' },
					{ src: './infinite/config/**', dest: '<%= pkg.name %>/' },
					{ src: './infinite/extensions/**', dest: '<%= pkg.name %>/' },
					{ src: './infinite/pdf-templates/**', dest: '<%= pkg.name %>/' },
					{ src: './infinite/public/css/**', dest: '<%= pkg.name %>/' },
					{ src: './infinite/public/images/**', dest: '<%= pkg.name %>/' },
					{ src: './infinite/public/js/**', dest: '<%= pkg.name %>/' },
					{ src: './infinite/public/partials/**', dest: '<%= pkg.name %>/' },
					{ src: './infinite/public/index.php', dest: '<%= pkg.name %>/' },
					{ src: './infinite/sql/**', dest: '<%= pkg.name %>/' },
					{ src: './infinite/vendor/**', dest: '<%= pkg.name %>/' },
					{ src: './infinite/README.md', dest: '<%= pkg.name %>/' },
					{ src: './infinite/index.php', dest: '<%= pkg.name %>/' },
					{ src: './infinite/LICENSE.txt', dest: '<%= pkg.name %>/' },
					{ src: './plugin-update-checker/**', dest: '<%= pkg.name %>/' },
					{ src: './style.css', dest: '<%= pkg.name %>/' },
					{ src: './functions.php', dest: '<%= pkg.name %>/' },
					{ src: './screenshot.png', dest: '<%= pkg.name %>/' },
				],
			},
		},
		'string-replace': {
			dist: {
				files: { './': ['style.css'] },
				options: {
					replacements: [
						{
							pattern: '<%= pkg.last_version %>',
							replacement: '<%= pkg.version %>',
						},
					],
				},
			},
		},
	});

	// grunt.registerTask('manifest', function (key, value) {
	// 	// Get config package.json
	// 	var pkg = grunt.config.get('pkg');

	// 	// Set changing props & default props
	// 	var website = 'https://r3blcreative.com';
	// 	var rootPath = website + '/r3bl-updates/themes/' + pkg['name'] + '/';

	// 	var wp = {
	// 		version: pkg['version'],
	// 		requires: '6.4.3',
	// 		requires_php: '8.0.0',
	// 		download_url: rootPath + pkg['name'] + '.zip?v=' + pkg['version'],
	// 		details_url: rootPath + 'changelog.html',
	// 	};

	// 	// Path to write/update file
	// 	var infoJsonFile = './dist/info.json';

	// 	// Write/update file
	// 	grunt.file.write(infoJsonFile, JSON.stringify(wp));
	// });

	// Load grunt plugins
	grunt.loadNpmTasks('grunt-contrib-compress');
	grunt.loadNpmTasks('grunt-string-replace');

	// Register tasks
	// grunt.registerTask('default', ['string-replace', 'compress', 'manifest']);
	grunt.registerTask('default', ['string-replace', 'compress']);
};
