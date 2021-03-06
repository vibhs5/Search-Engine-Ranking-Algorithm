<?php

// make sure browsers see this page as utf-8 encoded HTML
header('Content-Type: text/html; charset=utf-8');

$limit = 10;
$query = isset($_REQUEST['q']) ? $_REQUEST['q'] : false;
$results = false;

if ($query)
{
  // The Apache Solr Client library should be on the include path
  // which is usually most easily accomplished by placing in the
  // same directory as this script ( . or current directory is a default
  // php include path entry in the php.ini)
  require_once('Apache/Solr/Service.php');

  // create a new solr service instance - host, port, and webapp
  // path (all defaults in this example)
  $solr = new Apache_Solr_Service('localhost', 8983, '/solr/myexample/');

  // if magic quotes is enabled then stripslashes will be needed
  if (get_magic_quotes_gpc() == 1)
  {
    $query = stripslashes($query);
  }

  // in production code you'll always want to use a try /catch for any
  // possible exceptions emitted  by searching (i.e. connection
  // problems or a query parsing error)
  try
  {
    if($result=="PageRank")
	{
		$pageRankStart=0;
		$pageRankRows=10;
		$additionalParameters =array('sort'=>"pageRankFile desc");
		$results=$solr->search($query,$pageRankStart,$pageRankRows,$additionalParameters);
	}
  else if($result=="solr")
	{
		$results = $solr->search($query, 0, $limit);
	}
  }
  catch (Exception $e)
  {
    // in production you'd probably log or email this error to an admin
    // and then show a special message to the user but for this example
    // we're going to show the full exception
    die("<html><head><title>SEARCH EXCEPTION</title><body><pre>{$e->__toString()}</pre></body></html>");
  }
}

?>
<html>
  <head>
    <title>PHP Solr Client Example</title>
	  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/jquery-ui.js"></script>

  </head>
  <body>
    <form  accept-charset="utf-8" method="get">
      <label for="q">Search:</label>
      <input id="q" name="q" type="text" placeholder="Enter a Query" value="<? echo isset($_GET["q"])?$_GET["q"]:""?>"/>
      <input type="submit"/><br>
      <input type="radio" id="solr" name="result" value="solr" checked <?php echo isset($_GET["result"]) && $_GET["result"] == "solr"?"checked":"";?>/>Solr
      <input type="radio" id="PageRank" name="result" value="PageRank" <?php echo isset($_GET["result"]) && $_GET["result"] == "PageRank"?"checked":""; ?>/>PageRank
    </form>
<?php

// display results
if ($results)
{
  $total = (int) $results->response->numFound;
  $start = min(1, $total);
  $end = min($limit, $total);
?>
    <div>Results <?php echo $start; ?> - <?php echo $end;?> of <?php echo $total; ?>:</div>
    <ol>
<?php
  // iterate result documents
  foreach ($results->response->docs as $doc)
  {
?>
      <li>
       
<?php
    // iterate document fields / values
   $id = $doc->id;
   $title = $doc->title;
   $descipt = $doc->description;
   $url = $doc->og_url;
?>
	<p><?php echo $id; ?></p>
	<p><a target="_blank" href="<?php echo $url; ?>"><?php echo $title; ?></a></p>
        <p><a target="_blank" href="<?php echo $url; ?>"><?php echo $url; ?></a></p>
	<p><?php echo $descipt; ?></p><br>
      </li>
<?php
  }
?>
    </ol>
<?php
}
?>
  </body>
</html>
