<?php
  /**
   * www.dandysearch.com
   *
   * @author Colm Nolan
   * @version 1.0
   * @copyright Colm Nolan, 25 July, 2011
   * @Comp 30440 MSc Software Engineering Project
   **/
  
  /**
   * Metasearch Engine Evaluation Module
   */

  include_once("controller/controller_evaluation.php");
  $controller_evaluation;
  $controller_evaluation = new Controller();
  $controller_evaluation->invoke();
?>
