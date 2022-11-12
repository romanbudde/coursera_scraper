<?php
require '../vendor/autoload.php';

$httpClient = new \GuzzleHttp\Client();
$response = $httpClient->get('https://www.coursera.org/courses');
$htmlString = (string) $response->getBody();

// suppress any warnings that may come up
libxml_use_internal_errors(true);

$doc = new DOMDocument();
$doc->loadHTML($htmlString);
$xpath = new DOMXPath($doc);

$titles = $xpath->evaluate('//div[@class="horizontal-box"]//div[@class="rc-ProductMetrics"]//div//span//strong/span');
$extractedTitles = [];

$prices = $xpath->evaluate('//div[@class="BannerTitle"]/h1');
$extratedPrice = [];

foreach ($titles as $title) {
	$extractedTitles[] = $title->textContent.PHP_EOL;
	echo 'Course Number of Students Enrolled: ' . $title->textContent.PHP_EOL;
	echo '<br>';
}

foreach ($prices as $price) {
	$extractedPrices[] = $price->textContent.PHP_EOL;
	echo 'Course price: ' . $price->textContent.PHP_EOL;
}

$spans = $xpath->evaluate('//span[@class="cds-2 cds-button-label"]');