<?php
// WP one-click updates hosted on GitHub
require 'plugin-update-checker/plugin-update-checker.php';

use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$myUpdateChecker = PucFactory::buildUpdateChecker(
	'https://github.com/james-cook-tech/theme-bvwp-child/',
	__FILE__,
	'theme-bvwp-child'
);
$myUpdateChecker->getVcsApi()->enableReleaseAssets();
// $myUpdateChecker->setBranch('production');
// Configure this in wp-config.php or the server environment when authentication is needed.
// Never commit the token to this repository.
$githubUpdaterToken = defined('R3BL_GITHUB_UPDATER_TOKEN')
	? R3BL_GITHUB_UPDATER_TOKEN
	: getenv('R3BL_GITHUB_UPDATER_TOKEN');
if (is_string($githubUpdaterToken) && $githubUpdaterToken !== '') {
	$myUpdateChecker->setAuthentication($githubUpdaterToken);
}
unset($githubUpdaterToken);
