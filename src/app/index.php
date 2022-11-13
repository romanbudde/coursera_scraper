<html lang="en">
    <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Mr.X">
    <meta name="Linkedin profile" content="WWW.linkedin.com/Mr.X_123" >
    <title>Coursera Scraper</title>
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