<?php

class User_query_checked
{
    private $array_of_query_types = array();

    public function __construct()
    {
      $this->array_of_query_types["input_user_query"] = 'text';
    }
    

    public function __set($key, $value)
    {
      $this->$key = $value;
    }

    public function __get($key)
    {
      return $this->$key;
    }

    public function remove_stop_words()
    {
      //array of all stop words
      $all_stop_words = array();

      //accessing the text file which contains all the words that are to be removed if stopword removal is turned on
      $file_handle = fopen("model/stopwordList.txt", "rb");
    
      $stopword_prep = 'text';

      while (!feof($file_handle) ) 
      {

        $line_of_text = fgets($file_handle);
        $stopword_prep = trim($line_of_text);

        $all_stop_words[] = $stopword_prep;

      }
      /*var_dump($all_stop_words);*/

      fclose($file_handle);


      
      /*var_dump ($this->input_user_query);*/
      $query_array = explode(' ', $this->input_user_query);
      //turn the user query string into an array

      $total_no_words = count($query_array);
      
      /*var_dump($total_no_words);*/

      $record_stopword_position = array();
      //use the above to keep track of exactly how many where stopwords occur

      //loop through all the stop words checking if any of them occur in the query
      foreach ($all_stop_words as $key) 
      {
        for ($i = 0; $i < $total_no_words; $i++)
        {
         if (strcmp($key, $query_array[$i]) === 0)
          {
            //if a stopword is found record its index position in the query
            array_push($record_stopword_position, $i);
            
          }
        }
      }

      sort($record_stopword_position);

      //loop through the quuery array and if it has been recorded as being a stopword remove it from the query array
      for ($i = 0; $i < $total_no_words; $i++) 
      {
        for ($j = 0; $j < count($record_stopword_position); $j++) 
        {
          if ($i === $record_stopword_position[$j])
          { 
            unset ($query_array[$i]);
            break;
          }
        }
      }

      //assign the modified array back to the main query by imploding it
      $this->input_user_query = implode (' ', $query_array);
      
    }
    

    public function remove_punctuation() 
    {
      /*echo('hello');*/

      $simple_array = array();
      /*var_dump($this->array_of_query_types);*/

      $output;
      
      //match all the all words of any length that ends with a white space or is at the start of the line or end
      preg_match_all('@\w+\b@', $this->input_user_query, $output);


      foreach ($output as $key) 
      {
        $simple_array = $key;
      }

      $this->input_user_query = implode(' ', $simple_array);
      /*var_dump($this->input_user_query);*/

    }
 
    public function stem_string()
    {
      $simple_array = array();
      $stemming;
      //making an instance of the class Stemmer which is an open source script for stemming
      $stemming = new Stemmer;
      $simple_array_stemmed = array();

      preg_match_all('@\w+\b@', $this->input_user_query, $output);
      /*var_dump($output);*/

      foreach ($output as $key) 
      {
        $simple_array = $key;
      }

      foreach ($simple_array as $key) 
      {
        //we dont want to stem NOT OR or AND and turn them into lowercase Boolean search would not work then
        if ($key != 'NOT' && $key != 'OR' && $key != 'AND')
        {
          $just_stemmed = $stemming->stem($key);
        }
        else
        {
          $just_stemmed = $key;
        }
        /*echo $just_stemmed;*/
        $simple_array_stemmed[] = $just_stemmed;
        /*echo $key;*/
      }

      /*var_dump($simple_array_stemmed);*/


      //put the modefied sting into $this->input_user_query
      $this->input_user_query = implode($simple_array_stemmed, ' ');
      /*echo ('I am stemmed');*/
      /*var_dump($this->input_user_query);*/

    }

    public function change_to_boolean()
    {
      /*$array_of_terms;*/

      /*var_dump ($this->input_user_query);*/
      //replacing the query entry boolean word with the operator version
      $this->input_user_query = str_replace('NOT', ' -', $this->input_user_query, $count);

      $this->input_user_query = str_replace('AND', '', $this->input_user_query, $count);

      $this->input_user_query = str_replace('OR', ' |', $this->input_user_query, $count);

      /*var_dump ($this->input_user_query);*/
      //turning the string into an array so the space after the boolean operator can be removed
      //when this is done we will get a query something like "leinster -louth"
      $array_of_characters = str_split($this->input_user_query);

      for ($i = 0; $i < count($array_of_characters); $i++) 
      {
         // code...
        if ($array_of_characters[$i] === '-' || $array_of_characters[$i] === '+' ||   $array_of_characters[$i] === '|')
        {
          unset($array_of_characters[$i+1]);   //removing the space character
          break;
        }
      }

      $this->input_user_query = implode($array_of_characters);

    }

    function turn_into_url_text()
    {
       $this->input_user_query = urlencode( $this->input_user_query);
       /*var_dump ($this->input_user_query);*/
    }


}



?>
