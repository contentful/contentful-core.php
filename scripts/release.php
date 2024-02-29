<?php

/**
 * This file is part of the contentful/contentful-core package.
 *
 * @copyright 2015-2024 Contentful GmbH
 * @license   MIT
 */

declare(strict_types=1);

require __DIR__.'/ReleaseUpdater.php';

function exit_message(string $message, int $status)
{
    message($message);
    exit($status);
}

function message(string $message = '')
{
    echo '##  '.$message."\n";
}

if (!isset($argv[1])) {
    exit_message('ERROR: Cannot proceed without a valid release version, aborting.', 1);
}

$version = $argv[1];
$pattern = '/^(?<version>[0-9]+\.[0-9]+\.[0-9]+)(?<prerelease>-[0-9a-zA-Z.]+)?(?<build>\+[0-9a-zA-Z.]+)?$/';
if (!preg_match($pattern, $version)) {
    exit_message('ERROR: Provided version is not a valid semver identifier, aborting.', 1);
}

$composerAliasVersion = null;
if (isset($argv[2])) {
    $composerAliasVersion = $argv[2];
    $pattern = '/^(?<version>[0-9]+\.[0-9]+\.[0-9]+)$/';
    if (!preg_match($pattern, $composerAliasVersion)) {
        exit_message('ERROR: Provided master alias version is not a valid semver identifier, aborting.', 1);
    }
}

$changelogPath = getcwd().'/CHANGELOG.md';
$composerConfigPath = getcwd().'/composer.json';
$url = shell_exec('git remote get-url origin');
$url = str_replace('github.com:', 'github.com/', $url);
$url = 'https://'.mb_substr($url, 4, -5);

try {
    $updater = new ReleaseUpdater($changelogPath, $composerConfigPath, $url);
    $updater->updateChangelog($version);
    if (null !== $composerAliasVersion) {
        $updater->updateComposerAliasVersion($composerAliasVersion);
    }
} catch (Exception $exception) {
    exit_message($exception->getMessage(), 1);
}

message();
message('Committing changes to changelog');
system('git add '.$changelogPath);
shell_exec('git commit --message="chore(release): Prepare version '.$version.'"');
message('Changes committed!');
message();

message('Creating git tag');
shell_exec('git tag --sign '.$version.' --message="'.$version.'"');
message('Tag created!');
message();

if (null !== $composerAliasVersion) {
    message('Committing changes to composer.json');
    shell_exec('git add '.$composerConfigPath);
    shell_exec('git commit --message="chore(release): Prepare development of next version"');
    message('Changes committed!');
    message();
}

message();

message('Release created!');
message('Execute the following command to push to Github:');
message();
message('    git push --follow-tags');
message();
message('Then open the Github page at this URL:');
message();
message('    '.$url.'/releases/tag/'.$version);
message();
message('and then update the Github release, the content is already copied in your clipboard!');
message();
