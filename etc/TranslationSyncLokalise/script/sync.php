<?php
require __DIR__.'/../vendor/autoload.php';

const LANG_MAP = [
    //'en' => 'eng',
    'ja' => 'jpn',
    'pt' => 'por',
];

try {
    main();
} catch (\Throwable $e) {
    echo sprintf("\033[0;31mError\033[0m" . PHP_EOL);
    var_dump([
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
    ]);
}

function main() {
    // https://app.lokalise.com/profile#apitokens
    // Leave it constant value since this is a read-only token
    $apiToken = 'e954e5da821223d01f1188e8115f0932b9518ea7';
    $client = new \Lokalise\LokaliseApiClient($apiToken);
    $response = $client->files->download(
        // https://app.lokalise.com/project/128601805af3e57eec5279.20310938/?view=multi
        // Lokalise Project ID of Goalous backend for Cake2
        '128601805af3e57eec5279.20310938',
        [
            'format' => 'po',
            'replace_breaks' => false,
            'original_filenames' => false,
            'directory_prefix' => '/%LANG_ISO%/',
        ]
    );
    $bundleUrl = $response->body['bundle_url'];
    echo sprintf('bundle_url: %s' . PHP_EOL, $bundleUrl);

    $localFile = '/tmp/langs.zip';
    $extractPath = '/tmp/lokalise';

    $zip = new ZipArchive();
    $ch = curl_init();
    $fp = fopen($localFile, "w");

    curl_setopt($ch, CURLOPT_URL, $bundleUrl);
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_exec($ch);
    curl_close($ch);
    fclose($fp);

    if ($zip->open($localFile)) {
        if (!$zip->extractTo($extractPath)) {
            throw new RuntimeException(sprintf('Failed to extract %s file to %s', $localFile, $extractPath));
        }
        $zip->close();
    } else {
        throw new RuntimeException(sprintf('Failed to open %s', $localFile));
    }

    foreach (LANG_MAP as $iso6391 => $iso6392) {
        $src = sprintf('/tmp/lokalise/locale/%s.po', $iso6391);
        $dest = sprintf('/srv/www/cake/app/Locale/%s/LC_MESSAGES/default.po', $iso6392);
        if (copy($src, $dest)) {
            echo sprintf("\033[0;32mSuccess to apply %s: %s\033[0m" . PHP_EOL, $iso6391, $dest);
        } else {
            throw new RuntimeException(sprintf('Failed to copy translation file %s to %s',
                $src,
                $dest
            ));
        }
    }
}
