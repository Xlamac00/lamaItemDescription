<?php
/** Created by Jan Lamacz
 * jan.lamacz@gmail.com
 * 29. 9. 2018
 */

if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
  if(!empty($_POST['request'])) {
    include_once "AbstractController.class.php";

    session_start();
    switch ($_SESSION['template_mode']) {
      case "create":
        include_once "AjaxCreateController.php";
        new AjaxCreateController($_POST['request']);
        break;
      case "list":
        include_once "AjaxListController.php";
        new AjaxListController($_POST['request']);
        break;
      case "translate":
        include_once "AjaxTranslateController.php";
        new AjaxTranslateController($_POST['request']);
        break;
      default:
        echo json_encode('Invalid session settings');
    }
  }
}
