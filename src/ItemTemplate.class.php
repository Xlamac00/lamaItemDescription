<?php
/** Created by Jan Lamacz
 * jan.lamacz@gmail.com
 * 29. 9. 2018
 */

/** Module created for Lama Plus s.r.o. to manage item descriptions and their translations.
 *
 * Example how to initialize this module:
 * include ('./src/ItemTemplate.class.php');
 * $it = new ItemTemplate();
 * <head>
 * <?php echo $it->headers(); ?>
 * </head>
 * <body>
 * <?php echo $it->draw(); ?>
 * </body>
 *
 * Variables this module is saving:
 * $_SESSION[template_id]
 * $_SESSION[template_filter_page]
 * $_SESSION[template_filter_option]
 * $_SESSION[template_language]
 * $_COOKIE[template_count]
 * $_COOKIE[template_language]
 *
 * Libraries this module requires to be functional:
 * JQuery - https://jquery.com/
 * Bootstrap - https://getbootstrap.com/
 * Fontawesome - https://fontawesome.com/
 * Popper - https://popper.js.org
 *
 * PHP version this module was developed on:
 * PHP 7.1
 *
 * DB Server this module was developed on:
 * MySQL Ver 15.1 Distrib 10.2.17-MariaDB
 */
class ItemTemplate {
  private $mode;
  private $controller;

  public function __construct() {
    session_start();
    include_once 'TemplateController.php';
    $this->controller = new TemplateController;

    // button controller
    if(isset($_POST['action'])) {
      switch ($_POST['action']) {
        case "create":
          $id = $this->controller->createNewTemplate();
          $_SESSION['template_mode'] = "create";
          $_SESSION['template_id'] = $id;
          header("Location: .");
          break;
        case "edit":
          $_SESSION['template_mode'] = "create";
          $_SESSION['template_id'] = $_POST['template_id'];
          header("Location: .");
          break;
        case "cancelCreate":
          $this->controller->cancelTemplate();
          $_SESSION['template_mode'] = "list";
          $_SESSION['template_id'] = null;
          header("Location: .");
          break;
        case "saveCreate":
          $title = $_POST['template-title'];
          $this->controller->changeTemplateTitle($_POST['id'], $title);
          $_SESSION['template_mode'] = "list";
          $_SESSION['template_id'] = null;
          header("Location: .");
          break;
        case "translate":
          $_SESSION['template_mode'] = "translate";
          $_SESSION['template_language'] = $_POST['language'];
          $_SESSION['template_id'] = $_POST['id'];
          header("Location: .");
          break;
        case "cancelTranslate":
          $_SESSION['template_mode'] = "list";
          $_SESSION['template_language'] = null;
          $_SESSION['template_id'] = null;
          header("Location: .");
          break;
      }
    }
    if(!isset($_SESSION['template_mode']))
      $_SESSION['template_mode'] = 'list';
    $this->mode = $_SESSION['template_mode'];
  }

  public function headers() {
    $header = '<link rel="stylesheet" type="text/css" href="./css/itemTemplate.css">';
    switch ($this->mode) {
      case "create":
        $header .= '<script src="./js/ItemTemplateCreate.js"></script>';
        break;
      case "list":
        $header .= '<link rel="stylesheet" type="text/css" href="./css/itemTemplate-loading.css">';
        $header .= '<script src="./lib/popper-1.14.3/popper.min.js"></script>';
        $header .= '<script src="./js/ItemTemplateList.js"></script>';
        break;
      case "translate":
        $header .= '<script src="./js/ItemTemplateTranslate.js"></script>';
        break;
    }
    return $header;
  }

  public function draw() {
    switch ($this->mode) {
      case "list":
        return $this->controller->drawListTemplate();
      case "create":
        return $this->controller->drawCreateTemplate();
      case "translate":
        return $this->controller->drawTranslateTemplate();
    }
  }

}