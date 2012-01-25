<?php
//set the server responce time limit to zero
set_time_limit(0);

define('TOTAL_SEARCH_NO', '50');

/**
 * CLASS DEFINITIONS
 **/

//OAuth.php needed for yahoo boss authenication
require("OAuth.php");

//this class is blue print for all the results coming back from bing, yahoo, blekko and google
class Search_details
{
  private $search_details_array = array();

  function __construct()
  {
    $this->search_details_array["title"] = 'text';
    $this->search_details_array["description"] = 'text';
    $this->search_details_array["url"] = 'text';
    $this->search_details_array["display_url"] = 'text';
  }

  public function __set($key, $value)
  {
    $this->search_details_array[$key] = $value;
  }

  public function __get($key)
  {
    return $this->search_details_array[$key];
  }

}

include 'User_query_checked_Class.php';

//each object bing, yahoo, blekko and google contains an array of search_details
class Search 
{
  public $array_of_results = array();

  function __construct()
  {
    for ($i = 0; $i < TOTAL_SEARCH_NO; $i++) 
    {
      $this->array_of_results[$i] = new Search_details;
    }
  }

  function get_array_of_results($z)
  {
    return $this->array_of_results[$z];
  }

  function get_search_result()
  {
    return $this->array_of_results;
  }
    
}

//inheritance used here to give bing, yahoo, blekko and google their unique set_search_results methods to access their APIs
class Search_Bing extends Search
{
  function set_search_results($search_entry)
  {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'http://api.bing.net/xml.aspx?AppId=753288BB83E89E80D75157ACE521DB923766F77B&Verstion=2.2&Market=en-US&Query='.$search_entry->input_user_query.'&Sources=web+spell&web.count='.TOTAL_SEARCH_NO.'&xmltype=elementbased');

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $ret = curl_exec($ch);
    curl_close($ch);
    $xml_object = new SimpleXmlElement($ret);

    $children =  $xml_object->children('http://schemas.microsoft.com/LiveSearch/2008/04/XML/web');

    $counter = 0;
    foreach ($children->Web->Results->WebResult as $node) 
    {
      $this->array_of_results[$counter]->title = $node->Title; 
      $this->array_of_results[$counter]->description = $node->Description; 
      $this->array_of_results[$counter]->url = $node->Url; 
      $this->array_of_results[$counter]->display_url = $node->DisplayUrl; 
      $counter++;
    }
  }
}

//inheritance used here to give bing, yahoo, blekko and google their unique set_search_results methods to access their APIs
//additional __construct function and property consumer inherited here also to access Yahoo Boss API 
class Search_Yahoo extends Search
{
  public $consumer;

  function __construct()
  {
    parent::__construct();
    $this->consumer = new OAuthConsumer( "dj0yJmk9SlhmUUg0d2xZeElpJmQ9WVdrOWNtaERNM0pUTnpJbWNHbzlNVGM0TXpreE1ESTJNZy0tJnM9Y29uc3VtZXJzZWNyZXQmeD1lMQ--",  "3dc05893bab0fc05cbcff4c10c3da27d1a21140d");
  }

  function set_search_results($search_entry)
  {

    $cc_key  = "dj0yJmk9SlhmUUg0d2xZeElpJmQ9WVdrOWNtaERNM0pUTnpJbWNHbzlNVGM0TXpreE1ESTJNZy0tJnM9Y29uc3VtZXJzZWNyZXQmeD1lMQ--";
    $cc_secret = "3dc05893bab0fc05cbcff4c10c3da27d1a21140d";
    $url = "http://yboss.yahooapis.com/ysearch/web";
    $args = array();
    $args["q"] = $search_entry->input_user_query;
    /*echo $args["q"];*/
    $args["format"] = "xml";
    $args["count"] = TOTAL_SEARCH_NO;

    /*$consumer = new OAuthConsumer($cc_key, $cc_secret);*/
    $request = OAuthRequest::from_consumer_and_token($this->consumer, NULL,"GET", $url, $args);
    $request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $this->consumer, NULL);


    $url = sprintf("%s?%s", $url, OAuthUtil::build_http_query($args));

    $ch = curl_init();

    $headers = array($request->to_header());

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_URL, $url);


    $rsp = curl_exec($ch);

    $counter = 0;

    $results = new SimpleXmlElement($rsp);
    /*print_r ($results);*/

    //copying the $results properties that is want into the array_of_results
    foreach ($results->web->results->result as $node)
    {
      $this->array_of_results[$counter]->description = $node->abstract;
      $this->array_of_results[$counter]->display_url = $node->dispurl;
      $this->array_of_results[$counter]->title = $node->title;
      $this->array_of_results[$counter]->url = $node->url;
      $counter++;
    }
  }

}

//inheritance used here to give bing, yahoo, blekko and google their unique set_search_results methods to access their APIs
class Search_Blekko extends Search
{
  function set_search_results($search_entry)
  {
      $blekko_total_search_no = TOTAL_SEARCH_NO + 10;
      // Get the json from blekko cURL
      $ch = curl_init();
      /*echo 'blekko test';*/
      /*var_dump($search_entry->input_user_query);*/
      curl_setopt($ch, CURLOPT_URL, 'http://blekko.com/ws/?q='.$search_entry->input_user_query.'/json+/ps='.$blekko_total_search_no.'&auth=3Cb58f6ba2&p=0');

      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      $ret = curl_exec($ch);
      curl_close($ch);
      if ($ret === false) {
        echo "Could not fetch Blekko page";
        exit;
      }
      /*var_dump($ret);*/

      $blekko_object = json_decode($ret);
      
      $blekko_object_counter = 0;

      if ($blekko_object != null)
      {

      for ($i = 0; $i < TOTAL_SEARCH_NO && isset($blekko_object->RESULT[$blekko_object_counter]); $i++) 
      {
          //if statments used here because sometimes blekko was sending back blank RESULT[$blekko_object_counter]->snippets
          if(!isset($blekko_object->RESULT[$blekko_object_counter]->snippet))
          {
            $blekko_object_counter++; //
          }

          if(!isset($blekko_object->RESULT[$blekko_object_counter]->snippet)) 
          {
            $blekko_object_counter++; //
          }

          if(!isset($blekko_object->RESULT[$blekko_object_counter]->snippet))
          {
            $blekko_object_counter++; //
          }

          if(!isset($blekko_object->RESULT[$blekko_object_counter]->snippet)) 
          {
            $blekko_object_counter++; //
          }

          $this->array_of_results[$i]->description = $blekko_object->RESULT[$blekko_object_counter]->snippet; 
          $this->array_of_results[$i]->title = $blekko_object->RESULT[$blekko_object_counter]->url_title;
          $this->array_of_results[$i]->url = $blekko_object->RESULT[$blekko_object_counter]->url; 
          $this->array_of_results[$i]->display_url = $blekko_object->RESULT[$blekko_object_counter]->display_url; 
          $blekko_object_counter++;
      }
      }
    
  }
}

class Search_Google extends Search 
{

  function set_search_results($search_entry, $no_of_search_results_wanted)
  {
    //for the evaluation 100 results is needed but for the user search engine 50 is only needed
    $condition_no = 0;
    if($no_of_search_results_wanted === 100)
    {
      $condition_no = 92;
    }
    else
    {
      $condition_no = 42;
    }

    $counter = 0;
    $counter_json = 0;
    $last_index_used = 0;

    //google only returns results back 10 a time to a maximum 100 so this is why it was neccesary to query it multiple times
    for ($j = 1; $j < $condition_no; $j = $j+10) 
    {
      // create curl resource 
      $ch = curl_init('https://www.googleapis.com/customsearch/v1?key=AIzaSyB57o6TXUPsQEFO5dIjQgqyg45Gjqj6hBo&cx=000549439736960725936:ix-sdf_t3ly&q='.$search_entry->input_user_query.'&start='. $j); 

      // set url 
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 

      //return the transfer as a string 
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 

      // $output contains the output string 
      $output = curl_exec($ch); 

      /*echo ($output);*/
      // close curl resource to free up system resources 
      curl_close($ch);      
      

      $google_object = json_decode($output);
      /*var_dump($google_object);*/

      $counter_json = 0;

      for ($i = $counter; $i < $last_index_used+10; $i++) 
      {
        $this->array_of_results[$i]->title = $google_object->items[$counter_json]->title;
        $this->array_of_results[$i]->description = $google_object->items[$counter_json]->snippet; 
        $this->array_of_results[$i]->url = $google_object->items[$counter_json]->link; 
        $this->array_of_results[$i]->display_url = $google_object->items[$counter_json]->displayLink; 
        $counter_json++;
        $counter++;
      }
      $last_index_used = $counter;
    }

  }
}

include 'model/Scoreboard_Class.php';

include("model/stemming_class.php");
?>
