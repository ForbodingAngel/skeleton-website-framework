<?php
	// Parse the requested URL
	
	$requestUri = $_SERVER['REQUEST_URI']; /* This line retrieves the requested URI from the $_SERVER superglobal array. $_SERVER['REQUEST_URI'] contains the URI (Uniform Resource Identifier) that was requested by the client. */
	
	$requestedPage = trim(parse_url($requestUri, PHP_URL_PATH), '/'); /* This line parses the requested URI using the parse_url() function and extracts the path component using the PHP_URL_PATH constant. The trim() function is then used to remove any leading or trailing slashes from the path. */
	
	//echo $requestedPage; /* This line is commented out and is not executed. It appears to be for debugging purposes, possibly to echo/print the value of $requestedPage for testing. */
	
	
	/* Generate list of potential target pages */
	/* The purpose of this code block is to generate a list of files in the pages folder (and subfolders). If the requested page is not in that list, send it to 404. This can help eliminate some potential attack vectors*/
	/* This code defines a recursive function listFiles() that traverses through directories and subdirectories, collecting all file paths. Then, it checks if the requested page exists in the list of files. If it doesn't exist, it sets $pagename to "404". */
	function listFiles($dir) {
		$files = [];
		$contents = scandir($dir);
		
		foreach ($contents as $content) {
			if ($content != '.' && $content != '..') {
				$path = $dir . '/' . $content;
				if (is_dir($path)) {
					$files = array_merge($files, listFiles($path));
				} else {
					$files[] = $path;
				}
			}
		}
		
		return $files;
	}

	// Get the list of files in the pages directory and its subdirectories
	$files = listFiles("pages/");

	// Check if the requested page exists in the list of files
	if (!in_array("pages/" . $requestedPage . ".md", $files)) {
		$pagename = "404";
	}
	/* END Generate list of potential target pages */
	
	
	/* This is a catchall, just in case. There is also another catchall in the page layout file, just in case this one doesn't do the trick. THis is nowhere near as sophisticated as the approach above, but it still could potentially catch things that fell through the cracks */
	if (!file_exists("pages/" . $requestedPage .".md")) { 
		$pagename = "404"; 
	}
	
	if ($requestedPage =="") { 
	/* This block of code determines the name of the requested page based on the value of $requestedPage. If $requestedPage is empty (i.e., the root URL is requested), the page name is set to "home". Otherwise, the page name is set to the value of $requestedPage. */
		$pagename = "home";
	} else {
		$pagename = strtolower($requestedPage);
	}
	
	/* In summary, this code retrieves the requested URI, extracts the path component, and determines the name of the requested page based on the path. If no specific page is requested (e.g., accessing the root URL), it sets the page name to "home". */
	
	include './required/initialize-markdown-parser.php';
	include './config/config.php';
	include './themes/' . $theme . '/header.php';
?>