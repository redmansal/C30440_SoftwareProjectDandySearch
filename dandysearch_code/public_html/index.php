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
   * Metasearch Engine Module
   */
  include_once("controller/controller_.php");
  $controller;
  //create an instance of the Controller Class
  $controller = new Controller();

  //call the invoke method
  $controller->invoke();
?>
