<?php

/**
 * This file is part of the contentful/contentful-core package.
 *
 * @copyright 2015-2024 Contentful GmbH
 * @license   MIT
 */

declare(strict_types=1);

class ReleaseUpdater
{
    /**
     * @var string
     */
    public const PENDING_CHANGES_START = '<!-- PENDING-CHANGES -->';

    /**
     * @var string
     */
    public const PENDING_CHANGES_END = '<!-- /PENDING-CHANGES -->';

    /**
     * @var string
     */
    public const PENDING_CHANGES_PLACEHOLDER = '> No meaningful changes since last release.';

    /**
     * @var string
     */
    public const UNRELEASED_CHANGES_HEADER = '## [Unreleased](%s/compare/%s...HEAD)';

    /**
     * @var string
     */
    public const NEW_RELEASE_HEADER = '## [%s](%s/tree/%s) (%s)';

    /**
     * @var string
     */
    private $changelogPath;

    /**
     * @var string
     */
    private $composerConfigPath;

    /**
     * @var string
     */
    private $githubUrl;

    public function __construct(string $changelogPath, string $composerConfigPath, string $githubUrl)
    {
        $this->changelogPath = $changelogPath;
        $this->composerConfigPath = $composerConfigPath;
        $this->githubUrl = $githubUrl;
    }

    public function updateChangelog(string $version)
    {
        $content = file_get_contents($this->changelogPath);

        $pendingChangesStart = mb_strpos($content, self::PENDING_CHANGES_START);
        $pendingChangesEnd = mb_strpos($content, self::PENDING_CHANGES_END);

        if (false === $pendingChangesStart || false === $pendingChangesEnd) {
            throw new Exception('ERROR: Cannot reliably determine the changelog section to edit, aborting.');
        }

        $changelog = $this->createUpdatedChangelog($version, $content, $pendingChangesStart, $pendingChangesEnd);

        file_put_contents($this->changelogPath, $changelog);
    }

    private function createUpdatedChangelog(string $version, string $content, int $contentStart, int $contentEnd): string
    {
        $extractFrom = $contentStart + mb_strlen(self::PENDING_CHANGES_START);
        $extractLength = $contentEnd - $contentStart - mb_strlen(self::PENDING_CHANGES_START);

        $newReleaseChanges = trim(mb_substr($content, $extractFrom, $extractLength));

        $changelogStart = trim(mb_substr($content, 0, $contentStart));
        $changelogEnd = trim(mb_substr($content, $contentEnd + mb_strlen(self::PENDING_CHANGES_END)));

        $unreleasedLineStart = mb_strpos($changelogStart, '## [Unreleased]');
        $changelogStart = trim(mb_substr($changelogStart, 0, $unreleasedLineStart));

        $changelogTemplate = '[START]

[UNRELEASED_HEADER]

[PENDING_CHANGES_START]
[PENDING_CHANGES_PLACEHOLDER]
[PENDING_CHANGES_END]

[NEW_RELEASE_HEADER]

[NEW_RELEASE_CHANGES]

[END]
';

        $tempPath = tempnam(sys_get_temp_dir(), 'changelog-');
        file_put_contents($tempPath, $newReleaseChanges);
        system('pbcopy < '.$tempPath);

        return strtr($changelogTemplate, [
            '[START]' => $changelogStart,
            '[UNRELEASED_HEADER]' => sprintf(self::UNRELEASED_CHANGES_HEADER, $this->githubUrl, $version),
            '[PENDING_CHANGES_START]' => self::PENDING_CHANGES_START,
            '[PENDING_CHANGES_PLACEHOLDER]' => self::PENDING_CHANGES_PLACEHOLDER,
            '[PENDING_CHANGES_END]' => self::PENDING_CHANGES_END,
            '[NEW_RELEASE_HEADER]' => sprintf(
                self::NEW_RELEASE_HEADER,
                $version,
                $this->githubUrl,
                $version,
                date('Y-m-d')
            ),
            '[NEW_RELEASE_CHANGES]' => $newReleaseChanges,
            '[END]' => $changelogEnd,
        ]);
    }

    public function updateComposerAliasVersion(string $composerAliasVersion)
    {
        $json = file_get_contents($this->composerConfigPath);
        $contents = json_decode($json, true);

        $contents['extra']['branch-alias']['dev-master'] = $composerAliasVersion.'-dev';

        file_put_contents(
            $this->composerConfigPath,
            json_encode($contents, \JSON_PRETTY_PRINT | \JSON_UNESCAPED_SLASHES)."\n"
        );
    }
}
