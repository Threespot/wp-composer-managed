<?php
/**
 * Replace the starter's placeholder tokens with this site's values.
 *
 *   composer replace-fixme -- --app=acme-wp --name="Acme Foundation" --domain=www.acme.org [--site-id=<uuid>]
 *
 * Tokens (exact strings only; design notes written as "FIXME:" are left alone):
 *   fixme-app-name          Pantheon site / GitHub repo / Lando app (e.g. acme-wp)
 *   fixme-project-name      Human-readable name (e.g. Acme Foundation)
 *   fixme-domain            Production domain without protocol (e.g. www.acme.org)
 *   FIXME_pantheon_site_id  Pantheon site UUID (looked up with Terminus when omitted)
 *
 * Only git-tracked files are touched, so vendor/, web/wp/, node_modules/ and
 * uploads are never rewritten. Run it once, review `git diff`, then commit.
 */

$root = dirname(__DIR__);
chdir($root);

$opts = getopt('', ['app:', 'name:', 'domain:', 'site-id::', 'dry-run']);
foreach (['app', 'name', 'domain'] as $required) {
    if (empty($opts[$required])) {
        fwrite(STDERR, "Missing --$required.\nUsage: composer replace-fixme -- --app=acme-wp --name=\"Acme Foundation\" --domain=www.acme.org [--site-id=<uuid>] [--dry-run]\n");
        exit(1);
    }
}
if (!preg_match('/^[a-z0-9][a-z0-9-]*$/', $opts['app'])) {
    fwrite(STDERR, "--app must be a Pantheon site name (lowercase letters, digits, hyphens).\n");
    exit(1);
}

$siteId = $opts['site-id'] ?? '';
if ($siteId === '') {
    $siteId = trim((string) shell_exec('terminus site:info ' . escapeshellarg($opts['app']) . ' --field=id 2>/dev/null'));
    if ($siteId === '') {
        fwrite(STDERR, "Note: couldn't look up the Pantheon site ID with Terminus; FIXME_pantheon_site_id is left in place (pass --site-id).\n");
    }
}

$replacements = [
    'fixme-app-name' => $opts['app'],
    'fixme-project-name' => $opts['name'],
    'fixme-domain' => preg_replace('#^https?://#', '', rtrim($opts['domain'], '/')),
];
if ($siteId !== '') {
    $replacements['FIXME_pantheon_site_id'] = $siteId;
}

$skip = [
    'scripts/replace-fixme.php',
    'web/app/themes/sage/CLAUDE.md', // documents the tokens themselves
    'composer.lock',
    'yarn.lock',
    'web/app/themes/sage/composer.lock',
    'web/app/themes/sage/yarn.lock',
];

$files = array_filter(explode("\0", (string) shell_exec('git ls-files -z')));
$changed = 0;
foreach ($files as $file) {
    if (in_array($file, $skip, true) || is_link($file) || !is_file($file)) {
        continue;
    }
    $content = file_get_contents($file);
    if ($content === false || str_contains($content, "\0")) {
        continue; // binary
    }
    $updated = strtr($content, $replacements);
    if ($updated !== $content) {
        $changed++;
        echo (isset($opts['dry-run']) ? 'Would update: ' : 'Updated: ') . $file . "\n";
        if (!isset($opts['dry-run'])) {
            file_put_contents($file, $updated);
        }
    }
}

echo "$changed file(s) " . (isset($opts['dry-run']) ? 'would change' : 'changed') . ". Review with `git diff`.\n";
