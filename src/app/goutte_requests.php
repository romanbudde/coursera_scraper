<?php
require '../vendor/autoload.php';
require_once("../vendor/fabpot/goutte/Goutte/Client.php");

$httpClient = new \Goutte\Client();
// $domDocument = new \DOMDocument();

$response = $httpClient->request('GET', 'https://www.coursera.org/specializations/practical-data-science');
$response_categories = $httpClient->request('GET', 'https://www.coursera.org/browse');
$response_courses = $httpClient->request('GET', 'https://www.coursera.org/courses');


// get all categories
$categories = [];
$response_categories->filter('section.rc-TopicAndSkills a.explore-domains-card')->each(function ($node) use (&$categories) {
	$categories[] = $node->text();
});


// get all courses from a category
$all_courses = [];

$response_courses->filter('button')->attr('aria-label');

// get aria-label from all buttons
// $aria_labels = $response_courses->filter('button')->extract(['aria-label']);
// i need to click 'Show more' button on Subjects section. Then click corresponding category.

// $response_courses->filterXPath('//main//button')->children()->attr('class');

// $response_courses->filter('main#main div.rc-SearchFiltersContent')->children()->each(function ($node) use (&$all_courses) {
// 	$asd = 123;
// 	$all_courses[] = $node->attr('aria-label');
// });

$response_courses->filterXPath('//div[@class="rc-SearchFiltersContent"]')->children()->each(function ($node) use (&$all_courses) {
	$asd = 123;
	$all_courses[] = $node->attr('aria-label');
});

$response_courses->filterXPath('//button[@aria-label="Submit Search"]')->children()->each(function ($node) use (&$all_courses) {
	$asd = 123;
	$all_courses[] = $node->attr('aria-label');
});

// $response_courses->filter('.row li article h3 a')
// ->each(function ($node) use ($prices, &$priceIndex, $httpClient) {
// 	$title = $node->text();
// 	$price = $prices[$priceIndex];
// 	//getting the description
// 	$description = $httpClient->click($node->link())->filter('.content #content_inner article p')->eq(3)->text();
// 	// display the result
// 	echo "{$title} @ {$price} : {$description}\n\n";
// 	$priceIndex++;
// });


// foreach ($categories as $category){
// 	echo $category;
// 	echo '<br>';
// }


// get info from a course
$course_info = [];
$response->filter('div.ratings-count-expertise-style span span')->each(function ($node) use (&$course_info) {
	$course_info["ratings"] = $node->text();
});
$response->filter('div.instructor-count-display > span')->each(function ($node) use (&$course_info) {
	$course_info["first_instructor"] = $node->text();
	if(strpos($course_info["first_instructor"], "+") == true){
		$course_info["first_instructor"] = substr($course_info["first_instructor"], 0, strpos($course_info["first_instructor"], "+") - 1);
	}
	if(strpos($course_info["first_instructor"], ",") == true){
		$course_info["first_instructor"] = substr($course_info["first_instructor"], 0, strpos($course_info["first_instructor"], ",") - 1);
	}
});
$response->filter('h1.banner-title')->each(function ($node) use (&$course_info) {
	$course_info["name"] = $node->text();
});
$response->filter('div.BannerTitle p')->each(function ($node) use (&$course_info) {
	$course_info["description"] = $node->text();
});
$response->filter('div.horizontal-box div.rc-ProductMetrics div span strong span')->each(function ($node) use (&$course_info) {
	$course_info["amount_enrolled"] = $node->text();
});
// echo '<pre>';
// echo print_r($course_info);
// echo '</pre>';