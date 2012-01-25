<div class="results_container_2">
  
<h4>Yahoo Search Results</h4>

<?php  
  $display_counter = 0;
  for ($x = 0; $x < TOTAL_SEARCH_NO; $x++) {
    ?>  <?php
    if($x % 2 == 0)
    {
      echo '<div class="odd_result"> ';
    }
    else
    {
      echo '<div class="even_result">';
    }
    $display_counter = $x + 1;
    echo $display_counter.'<br>  </br>';
    echo $this->yahoo_results->array_of_results[$x]->title.'<br>  </br>';
    echo $this->yahoo_results->array_of_results[$x]->description.'<br>  </br>';
    echo '<a href='.$this->yahoo_results->array_of_results[$x]->url.' target="_blank">'.
    $this->yahoo_results->array_of_results[$x]->title.'</a>'.'<br>  </br>';
    echo $this->yahoo_results->array_of_results[$x]->display_url.'<br>  </br><br>  </br>';

    ?>
    </div>
    <?php
  }
?>

</div>
