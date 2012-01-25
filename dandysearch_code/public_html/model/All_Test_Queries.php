<?php

//this class brings in all the test queries from the text file for testing
class All_Test_Queries
{
  private $test_query = array();

  function __construct()
  {
    $file_handle = fopen("model/trecWebTrackQueries_09.txt", "rb");
  
    $query_prep = 'text';

    while (!feof($file_handle) ) 
    {

      $line_of_text = fgets($file_handle);
      $query_prep = trim($line_of_text);

      $this->test_query[] = $query_prep;

    }

    fclose($file_handle);

    /*var_dump($this->test_query);*/

  }

  public function __set($key, $value)
  {
    $this->$key = $value;
  }

  public function __get($key)
  {
    return $this->$key;
  }
}

?>
