<?php

/*
 * wiki.php
 *
 * (c) 2014-205 Sam Nazarko
 * email@samnazarko.co.uk
 *
 */

$num_errors = 0;
$calls = 0;

$base_file = "../../static/wiki.json";
$parent_contents_url = "https://discourse.osmc.tv/t/table-of-contents/6543.json";

function get_json_obj($url, $is_fatal) {
  global $num_errors;
  global $calls;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
  $output = curl_exec($ch);
  $errno = curl_errno($ch);
  curl_close($ch);
  if ($errno) {
    if ($is_fatal) {
       die("Couldn't fetch resource " . $url);
    }
    $num_errors +=1;
  } elseif (!empty($output)) {
    $calls +=1;
    return json_decode($output);
  } else {
		exit();
	}
}

function get_slug_url($url) {
  $url = strtolower($url);
  $url = str_replace(" ", "-", $url);
  $url = str_replace("'", "", $url);
  $url = str_replace("?", "", $url);
  $url = str_replace("(", "", $url);
  $url = str_replace(")", "", $url);
  return $url;
}

/* Get the list of all available categories */
echo ("Downloading list of all available categories <br>");
$json_categories = get_json_obj($parent_contents_url, 1);

$cat_json = array();

/* foreach $json_categories */
for ($i = 0; $i < count($json_categories->details->links); $i++) {

  $tz = $json_categories->details->links[$i];

  $cat_title = $tz->title;
  $cat_slug = get_slug_url($tz->title);
    
  $cat_json["title"] = "Wiki";
  $cat_json["categories"][$i]["title"] = $cat_title;
  $cat_json["categories"][$i]["slug"] = $cat_slug;

  echo ("<br>" . $cat_title . "<br>");
  
  $json_category_pages = get_json_obj($tz->url . ".json", 1);
  
  /* get urls from body - sorted */
  $cat_body = $json_category_pages->post_stream->posts[0]->cooked;
  $dom = new DOMDocument();
  $dom->loadHTML($cat_body);
  $divs = $dom->getElementsByTagName('img');
  
  /* get post ids */
  $sorted_ids = array();
  foreach ($divs as $key=>$div) {
    $class = $div->getAttribute("class");
    if ($class === "avatar") {
      $url = $div->nextSibling->getAttribute("href");
      $sorted_id = substr($url, strrpos($url, '/') + 1);
      array_push($sorted_ids, $sorted_id);
    }
  }
  
  /* unsorted ids to search */
  $unsorted_ids = array();
  for ($i2 = 0; $i2 < count($json_category_pages->details->links); $i2++) {
    $url = $json_category_pages->details->links[$i2]->url;
    $unsorted_id = substr($url, strrpos($url, '/') + 1);
    array_push($unsorted_ids, $unsorted_id);
  }
  
  $cat_json_post_num = 0;
  /* foreach https://discourse.osmc.tv/t/vero/6559.json */
  for ($i3 = 0; $i3 < count($json_category_pages->details->links); $i3++) {
    
    $sorted_key = array_search($sorted_ids[$i3], $unsorted_ids);
    $tz = $json_category_pages->details->links[$sorted_key];
    
    if ($sorted_key !== false && $tz->reflection == "0" && substr($tz->title, 0, 6) !== '/About') {
      
      /* Get the post contents */
      echo ("- " . $tz->title . "<br>");

      $post = get_json_obj($tz->url . ".json", 0);
      $post_content = $post->post_stream->posts[0];
      $post_body = $post_content->cooked;

      $post_title = $post->title;
      $post_slug = get_slug_url($post_title);
      $post_url = $tz->url;
      $post_cat = $cat_title;
      
      $cat_json["categories"][$i]["posts"][$cat_json_post_num]["title"] = $post_title;
      $cat_json["categories"][$i]["posts"][$cat_json_post_num]["slug"] = $post_slug;
      $cat_json["categories"][$i]["posts"][$cat_json_post_num]["url"] = "/wiki/" . $cat_slug . "/" . $post_slug;
      $cat_json["categories"][$i]["posts"][$cat_json_post_num]["discourseUrl"] = $post_url;
      $cat_json["categories"][$i]["posts"][$cat_json_post_num]["category"] = $post_cat;
      $cat_json["categories"][$i]["posts"][$cat_json_post_num]["body"] = $post_body;
                 
      $cat_json_post_num += 1;
      
    }
    elseif ($sorted_key !== false && substr($tz->title, 0, 6) === '/About') {

      /* Get description */
      echo ("- " . $tz->title . "<br>");
      $post = get_json_obj($tz->url . ".json", 0);
      $post_content = $post->post_stream->posts[0];
      $excerpt = $post_content->cooked;
      $cat_json["categories"][$i]["description"] = $excerpt;
    }
  }
}

/* Generate json file */
file_put_contents($base_file, json_encode($cat_json, JSON_PRETTY_PRINT));

echo "<br><br>Errors: " . $num_errors;
echo "<br>Calls: " . $calls;

date_default_timezone_set("Europe/London");
$date = new \DateTime();
echo "<br><br>" . date_format($date, 'd-m-Y - H:i');
