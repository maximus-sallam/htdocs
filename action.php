<?php

require __DIR__ . '/vendor/autoload.php';

$web = new \Spekulatius\PHPScraper\PHPScraper;

/**
 * Navigate to the test page. This page contains two images:
 *
 * <img src="https://test-pages.phpscraper.de/assets/cat.jpg" alt="absolute path">
 * <img src="/assets/cat.jpg" alt="relative path">
 */

$web->go(htmlspecialchars($_POST['URL']));

/**
 * [
 *     'https://test-pages.phpscraper.de/assets/cat.jpg',
 *     'https://test-pages.phpscraper.de/assets/cat.jpg',
 * ]
 *
 * @note
 * The URL is listed twice because it's included twice on the page:
 * Once with a relative path and once with an absolute path.
 * The relative paths are resolved to absolute paths by default.
 */

 // Loop over images and echo
foreach($web->images as $img) {
    echo '<img src="'.$img.'" alt="">';
}
//var_dump($web->images);
