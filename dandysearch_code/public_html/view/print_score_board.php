
<div class=stretch_white>
  
<?php

  $score_board_size = TOTAL_SEARCH_NO * 3;
  for ($x = 0; $x <  $score_board_size; $x++) 
  {
    if($this->scoreboard->borda_data_list[$x]->unique_url != 'www.fake.com')
    {
      $bing_ranking = $this->scoreboard->borda_data_list[$x]->bing_index + 1;
      $yahoo_ranking = $this->scoreboard->borda_data_list[$x]->yahoo_index + 1;
      $blekko_ranking = $this->scoreboard->borda_data_list[$x]->blekko_index + 1;
      echo "<div class = white_left_margin_score_board></div>";
      ?> <?php
      if($x % 2 == 0)
      {
        echo '<div class="even_result_wide">';
      }
      else
      {
        echo '<div class="odd_result_wide">';
      }
      $z = $x + 1;
      echo $z.'<br>  </br>';

      echo  '<a href='.$this->scoreboard->borda_data_list[$x]->unique_url.' target="_blank">'.
      $this->scoreboard->borda_data_list[$x]->title.'</a>'.'<br>  </br>';
      echo $this->scoreboard->borda_data_list[$x]->description.'<br>  </br>';

      /**
       * checking to see if a snippet appears in a particalar search engine. If it does not print Not Present and if it is print its ranking
       **/
      if ($bing_ranking === 0)
      {
      echo 'Bing Ranking: Not Present <br>  </br>';
      }
      else
      {
      echo 'Bing Ranking: '.$bing_ranking.'<br>  </br>';
      }

      if ($yahoo_ranking === 0)
      {
      echo 'Yahoo Ranking: Not Present <br>  </br>';
      }
      else
      {
      echo 'Yahoo Ranking: '.$yahoo_ranking.'<br>  </br>';
      }

      if ($blekko_ranking === 0)
      {
      echo 'Blekko Ranking: Not Present _______________________________________________';
      }
      else
      {
      echo 'Blekko Ranking: '.$blekko_ranking.'_______________________________________________________';
      }
      echo 'Borda Score: '.$this->scoreboard->borda_data_list[$x]->score.'<br>  </br>';

      ?>
      </div>
      <?php
      
      echo ('<div class = white_left_margin_score_board></div>');
    }
  }

?>

</div>

