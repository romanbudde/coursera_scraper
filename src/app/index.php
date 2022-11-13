<?php
	$asd = 123;
	$hello = '';
?>

<html lang="en">
    <!-- DEfines languages of content : English -->
    <head>
    <!-- Information about website and creator -->
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Defines the compatibility of version with browser -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- for make website responsive -->
    <meta name="author" content="Mr.X">
    <meta name="Linkedin profile" content="WWW.linkedin.com/Mr.X_123" >
    <!-- To give information about author or owner -->
    <meta name="description " 
    content="A better place to learn computer science">
    <!-- to explain about website in few words -->
    <title>GeeksforGeeks</title>
    <!-- Name of website or content to display -->
</head>
<body>
    <!-- Main content of website -->
    <h1>PHP - Coursera Scraper</h1>
	<p>Developed using Symfony - DomCrawler - CssSelector - Goutte</p>
	<form method="POST" action="./panther_requests.php">
		<select name="category_selected" id="category_select">
			<?
			foreach($categories as $category):
				?>
				<option value="<?php echo $category; ?>"><?php echo $category; ?></option>
				<?
			endforeach;
			?>
		</select>
		<button type="submit">
			Scrape courses
		</button>
	</form>
</body>
</html>