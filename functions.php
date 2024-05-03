<?php
// WP one-click updates hosted on GitHub
require 'plugin-update-checker/plugin-update-checker.php';

use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$myUpdateChecker = PucFactory::buildUpdateChecker(
	'https://github.com/R3BLcreative/theme-bvwp-child/',
	__FILE__,
	'theme-bvwp-child'
);
$myUpdateChecker->getVcsApi()->enableReleaseAssets();
// $myUpdateChecker->setBranch('production');
$myUpdateChecker->setAuthentication('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');
