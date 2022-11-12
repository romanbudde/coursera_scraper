<?php
require '../vendor/autoload.php';
// require_once("../vendor/fabpot/goutte/Goutte/Client.php");
// require_once("../vendor/symfony/panther/src/Client.php");

use \Symfony\Component\Panther\Client;

// $httpClient = new \Goutte\Client();

// $reponse_courses_panther = $client->request('GET', 'https://www.coursera.org/courses');

// https://github.com/symfony/panther/issues/110
// https://github.com/symfony/panther/issues/185
// https://stackoverflow.com/questions/59720768/change-user-agent-for-symfony-panther-chromeclient
// https://github.com/symfony/panther/issues/172

try {
	$selected_category = $_POST["category_selected"] ?? '';
	$httpClient = new \Goutte\Client();
	$options = array(
		'port' => get_unused_tcp_port()
	);
	$client = Client::createChromeClient(null, [
		'--user-agent=Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.88 Safari/537.36',
		'--window-size=1200,1100',
		'--headless',
		'--disable-gpu',
		'--disable-dev-shm-usage',
		'--no-sandbox'
	], $options);
	// $client = Client::createChromeClient(null, null, $options);

	//code here
	$reponse_courses_panther = $client->request('GET', 'https://www.coursera.org/courses');
	$crawler = $client->waitFor('button[aria-label="Show more Subject options"]');

	// click on the show more button (thus getting modal with all categories)
	$client->executeScript("document.querySelector('[aria-label=\'Show more Subject options\']').click();");
	$client->executeScript("
		var categories = document.getElementById('checkbox-group').children;

		for (var i = 0; i < categories.length; i++) {
			if(categories[i].innerText === ' . $selected_category . '){
				categories[i].children[0].getElementsByTagName('input')[0].click();
			}
			else{console.log('not found')}
		}
	");

	// once category is clicked, await for div.ais-InfiniteHits to load
	$crawler = $client->waitFor('div.ais-InfiniteHits');
	//refresh crawler so I can crawl newly loaded page
	$crawler = $client->refreshCrawler();
	// once courses are reloaded (filtered by selected category) - get each individual course
	$last_page = false;
	$course_count = 0;
	file_put_contents('/var/www/html/' . $selected_category . '.log', "Category Name, Course Name, First Instructor Name, Course Description, # of Students Enrolled, # of Ratings \n", FILE_APPEND);
	while(!$last_page){
		$crawler = $client->refreshCrawler();
		$crawler = $client->waitFor('div.ais-InfiniteHits');
		$crawler->filter('[data-track-component="search_card"]')->each(function ($node) use ($client, $crawler, $httpClient, &$course_count, $selected_category) {
	
			// TO DO: Create different functions depending on if it is a certificate
			// or specialization
			$course = [];
			$courses_client = $httpClient->click($node->link());

			if(str_contains($node->link()->getUri() ,'data-analytics-japanese')){
				$asd = 123;
			}

			if(str_contains($node->attr('to'), '/professional-certificates')){
				$course["course_name"] = $courses_client->filter('main h1')->count() > 0 ? $courses_client->filter('main h1')->text() : '';
				$course["course_description"] = $courses_client->filter('main p')->count() > 0 ?$courses_client->filter('main p')->text() : '';
				$course["course_instructors"] = $courses_client->filter('main [data-track-component="nav_item_instructors"]')->count() > 0 ? $courses_client->filter('main [data-track-component="nav_item_instructors"]')->text() : '';
				if($courses_client->filter('main p.css-14d8ngk')->count() > 0){
					$course["course_ratings_and_enrolled"] = $courses_client->filter('main p.css-14d8ngk')->text();
				}
				else{
					if($courses_client->filter("span[data-test='ratings-count-without-asterisks']")->count() > 0){
						$courses_client->filter("span[data-test='ratings-count-without-asterisks']")->text();
					}
				}
			}

			if(str_contains($node->attr('to'), '/specializations')){
				$course["course_name"] = $courses_client->filter('h1.banner-title')->count() > 0 ? $courses_client->filter('h1.banner-title')->text() : '';

				if(! str_contains($courses_client->filter('h1.banner-title')->attr('class'), 'banner-title-without--subtitle')){
					$course["course_description"] = $courses_client->filter('div.BannerTitle p')->text();
				}
				else {
					$course["course_description"] = '-';
				}

				if($courses_client->filter('div.instructor-count-display > span')->count() > 0){
					$course["course_instructors"] = $courses_client->filter('div.instructor-count-display > span')->text();
				}
				else{
					if($courses_client->filter('div.rc-BannerInstructorInfo span')->count() > 0){
						$course["course_instructors"] = $courses_client->filter('div.rc-BannerInstructorInfo span')->text();
					}
				}
				if(strpos($course["course_instructors"], "+") == true){
					$course["course_instructors"] = substr($course["course_instructors"], 0, strpos($course["course_instructors"], "+") - 1);
				}
				if(strpos($course["course_instructors"], ",") == true){
					$course["course_instructors"] = substr($course["course_instructors"], 0, strpos($course["course_instructors"], ",") - 1);
				}

				if($courses_client->filter('div.horizontal-box div.rc-ProductMetrics div span strong span')->count() > 0){
					$course["course_enrolled"] = $courses_client->filter('div.horizontal-box div.rc-ProductMetrics div span strong span')->text();
				}
				else{
					$course["course_enrolled"] = 'Not shown publicly in website.';
				}

				$course["course_ratings"] = $courses_client->filter('div.ratings-count-expertise-style span span')->count() > 0 ? $courses_client->filter('div.ratings-count-expertise-style span span')->text() : '';

			}

			if(str_contains($node->attr('to'), '/learn')){
				$course["course_name"] = $courses_client->filter('h1.banner-title')->count() > 0 ? $courses_client->filter('h1.banner-title')->text() : '';

				if(! str_contains($courses_client->filter('h1.banner-title')->attr('class'), 'banner-title-without--subtitle')){
					$course["course_description"] = $courses_client->filter('div.BannerTitle p')->text();
				}
				else {
					$course["course_description"] = '-';
				}

				$course["course_instructors"] = $courses_client->filter('div.rc-BannerInstructorInfo')->count() > 0 ? $courses_client->filter('div.rc-BannerInstructorInfo')->text() : '';
				if(strpos($course["course_instructors"], "+") == true){
					$course["course_instructors"] = substr($course["course_instructors"], 0, strpos($course["course_instructors"], "+") - 1);
				}
				if(strpos($course["course_instructors"], ",") == true){
					$course["course_instructors"] = substr($course["course_instructors"], 0, strpos($course["course_instructors"], ",") - 1);
				}

				$course["course_enrolled"] = $courses_client->filter('div.horizontal-box div.rc-ProductMetrics div span strong span')->count() > 0 ? $courses_client->filter('div.horizontal-box div.rc-ProductMetrics div span strong span')->text() : '';
				$course["course_ratings"] = $courses_client->filter("span[data-test='ratings-count-without-asterisks']")->count() > 0 ? $courses_client->filter("span[data-test='ratings-count-without-asterisks']")->text() : '';
			}
			$course_count++;
			file_put_contents('/var/www/html/' . $selected_category . '.log', $course, FILE_APPEND);
			file_put_contents('/var/www/html/' . $selected_category . '.log', "\n $course_count--------------------------------- \n", FILE_APPEND);
		});
	
		// once the whole page has been scraped, go to the following page if it this is not already the last page.

		if($client->executeScript("return document.querySelectorAll('[aria-label=\'Next Page\']')[0].disabled")){
			// then we are on last page, stop looping
			$last_page = true;
		}
		else{
			// then click on next page
			$client->executeScript("document.querySelector('[aria-label=\'Next Page\']').click();");
			// once next page is clicked, await for div.ais-InfiniteHits to load
			$crawler = $client->waitFor('div.ais-InfiniteHits');
			//refresh crawler so I can crawl newly loaded page
			$crawler = $client->refreshCrawler();
		}
	}

} catch (Exception $e) {
	echo $e->getMessage();
} finally {
	$client->quit();
}

function get_unused_tcp_port(){
	$address = '127.0.0.1';
	// Create a new socket
    $sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
	// Bind the source address
    socket_bind($sock, $address);
    socket_getsockname($sock, $address, $port);
    #echo $port;
    socket_close($sock);
    return $port;
}


// get all categories
// $categories = [];
// $response_categories->filter('section.rc-TopicAndSkills a.explore-domains-card')->each(function ($node) use (&$categories) {
// 	$categories[] = $node->text();
// });



// $courses_in_page['entire_text'] = $node->text();
// $courses_in_page['aria_label'] = $node->children()->attr('aria-label');

// go to course's page
// $to = $node->attr('to');

// $client->executeScript("document.querySelector('[aria-label=\'Show more Subject options\']').click();");
// $client->executeScript("document.querySelector('[to=\'/professional-certificates/google-data-analytics\']').click();");
// $crawler = $client->refreshCrawler();

// $httpClient->click($node->link())->filter('main h1')->text();