- Before running the crawler, change the database connection variables and the Targeted URL from the Constants class.
- Don't worry about designing the Database as the Crawler will create the needed Database and Tables.
- To run the crawler, open the Terminal and run the Crawler.php file. (php Crawler.php).
- Please note that no depth is set for the crawler so that it keeps looking for all links in the given domain.
- Links are saved on the database, with their hashed value in an indexed column (The column is made unique too) to speed up the look up process in which we check if a link is already crawled or not. The Links were not saved on a PHP array because the "in_array", "array_key_exists" and "isset" builtin functions were to consume more time. 
