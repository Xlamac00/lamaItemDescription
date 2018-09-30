<?php
/** Created by Jan Lamacz
 * jan.lamacz@gmail.com
 * 29. 9. 2018
 */

/**
 * Controller for Ajax requests from default page with all items.
 * */
class AjaxListController extends AbstractController {

  public function __construct($request) {
    parent::__construct();

    $ret = '';
    switch ($request) {
      case "getTemplates":
        $ret = $this->getFilteredTemplates();
        break;
      case "getListModal":
        $ret = $this->getListModal();
        break;
      case "deleteTemplate":
        $ret = $this->deleteTemplate();
        break;
    }

    echo json_encode($ret);
    exit;
  }

  /** Returns all item block to be displayed.
   * Checks sessions or post data for filter options and returns filtered items.
   *
   * @input $_POST[option] - filter options ("all", "old", "null")
   * @input $_POST[language] - filter language ("cz", "sk", "pl", "de", "en")
   * @input $_POST[page] - page number
   * @input $_COOKIE[template_count] - max number of items on one page
   * */
  protected function getFilteredTemplates() {
    if(isset($_POST['option']) && strlen($_POST['option']) > 0) {
      $option = $_POST['option'];
      $language = $_POST['language'];
      $page = $_POST['page'];
      $_SESSION['template_filter_option'] = $option;
      $_SESSION['template_filter_language'] = $language;
      $_SESSION['template_filter_page'] = $page;
    }
    elseif(isset($_SESSION['template_filter_option']) && strlen($_SESSION['template_filter_option']) > 0) {
      $option = $_SESSION['template_filter_option'];
      $language = $_SESSION['template_filter_language'];
      $page = $_SESSION['template_filter_page'];
    }
    else {
      $option = 'all';
      $language = 'cz';
      $page = 1;
    }
    $count = isset($_COOKIE['template_count']) ? $_COOKIE['template_count'] : "10";

    if($option === 'all') // no filtering
      $result = $this->getTemplates($page, $count);
    else // filter for only one language
      $result = $this->getTemplates($page, $count, $language, $option);
    if($page*$count >= $result['rows']+$count) { // current page is higher then max page with this amount of rows
      $page = ceil($result['rows']/$count); // set page to highest page possible
      if($page <= 0) $page = 1;
      $_SESSION['template_filter_page'] = $page;
    }
    return array('status' => 'success', 'language' => $language, 'rows' => $result['rows'],
                 'option' => $option, 'html' => $result['html'], 'page' => $page, 'count' => $count);
  }

  /** Filters items from DB and calculates translation-state for all languages.
   *
   * @param      $page - page number
   * @param      $max_items - max number of items on one page
   * @param null $filter_lang - if it has to be filtered by one language and which one
   * @param null $filter_option - if it has to be filtered - how ("old" for outdated or "null" for not translated)
   *
   * @return array
   */
  private function getTemplates($page, $max_items, $filter_lang = null, $filter_option = null) {
    $error = null;
    $req = $this->db->prepare("SELECT id, title FROM template
                      WHERE deleted = '0'") or $error = $this->db->error;
    $req->execute() or $error = $this->db->error;
    $req->bind_result($id, $title);
    $req->store_result();
    $languages = $filter_lang == null ? array("cz", "sk", "pl", "en", "de") : array($filter_lang);
    if($error !== null) return array('status' => 'error', 'html' => $error);

    $req2 = $this->db->prepare("SELECT id
                       FROM block
                       WHERE id_template = ?
                         AND deleted = '0'") or $error = $this->db->error;
    if($error !== null) return array('status' => 'error', 'html' => $error);
// TODO START smazat po prechodu na MySQL 8.0 / MariaDB 10.2
//    $req3 = $this->db->prepare("WITH ranked_history AS (
//        SELECT m.*, ROW_NUMBER() OVER (PARTITION BY language ORDER BY id DESC) AS rh
//            FROM block_history AS m
//            WHERE id_block = ?
//              AND language IN ('cz', ?)
//              AND deleted = '0'
//              ORDER BY language ASC
//            )SELECT text, title, date, language
//            FROM ranked_history WHERE rh = 1") or $error = $this->db->error;
    $req3 = $this->db->prepare("SELECT date
                        FROM block_history
                        WHERE language = 'cz'
                          AND deleted = '0'
                          AND id_block = ?
                        ORDER BY date DESC LIMIT 1") or $error = $this->db->error;
    $req4 = $this->db->prepare("SELECT title,text, date
                        FROM block_history
                        WHERE language = ?
                          AND deleted = '0'
                          AND id_block = ?
                        ORDER BY date DESC LIMIT 1") or $error = $this->db->error;
// TODO END smazat po prechodu na MySQL 8.0 / MariaDB 10.2
    if($error !== null) return array('status' => 'error', 'html' => $error);
    $completeness = array();
    ob_start();
    // for every template
    $showed = 0;
    for($count = 0; $req->fetch();) {
      if($filter_lang !== null || $showed <= $max_items) { // dont calculated if filter_lang IS null AND already showed max
        $req2->bind_param("d", $id);
        $req2->execute() or $error = $this->db->error;
        $req2->bind_result($bid);
        $req2->store_result();
        $check = array();
        foreach($languages as $lang) {
          $check[$lang] = array('success' => true, 'danger' => true, 'history' => false);
        }
        // for every block in the template
        while($req2->fetch()) {
// TODO START smazat po prechodu na MySQL 8.0 / MariaDB 10.2
          $req3->bind_param("d", $bid);
          $req3->execute() or $error = $this->db->error;
          $req3->bind_result($czech);
          $req3->store_result();
          $req3->fetch();
          if($error !== null) return array('status' => 'error', 'html' => "ASD".$error);
// TODO END smazat po prechodu na MySQL 8.0 / MariaDB 10.2
          // for every language we are translating into
          foreach($languages as $lang) {
// TODO START smazat po prechodu na MySQL 8.0 / MariaDB 10.2
//            $req3->bind_param("ds", $bid, $lang);
//            $req3->execute() or $error = $this->db->error;
//            $req3->bind_result($text, $header, $date, $language);
//            $czdate = null;
//            // for newest original (czech) text and newest $lang text
//            $i = $lang == 'cz' ? 1 : 0;
//            for(; $req3->fetch(); $i++) {
//              if($language == 'cz') $czdate = $date;
//              if($lang == $language) { // it can be cz or that $lang
//                if(strlen($header) > 1) $check[$lang]['danger'] = false; // at least something is translated
//                if($czdate !== null && $czdate > $date) $check[$lang]['history'] = true; // something is out of date
//              }
//            }
//            // there even isn't second row in the db - it was NOT translated
//            if($i <  2) $check[$lang]['success'] = false;
            $req4->bind_param("sd", $lang, $bid);
            $req4->execute() or $error = $this->db->error;
            $req4->bind_result($header, $text, $date);
            $req4->store_result();
            $req4->fetch();
            if($error !== null) return array('status' => 'error', 'html' => "ds".$error);
            if(strlen($header) > 1 && $text != 'Youtube video') $check[$lang]['danger'] = false; // at least something is translated
            if($czech !== null && $date != null && $czech > $date) $check[$lang]['history'] = true; // something is
            // out of date
            if(strlen($header) < 1 || $header == null) $check[$lang]['success'] = false; // nothing was translated
            $req4->free_result();
// TODO END smazat po prechodu na MySQL 8.0 / MariaDB 10.2
          }
          $req3->free_result();
        }

        // set state for each language (how they are translated)
        foreach($languages as $lang) {
          $completeness[$lang] = ($check[$lang]['history'] ? 'info' : ($check[$lang]['danger'] ? 'danger' :
            ($check[$lang]['success'] ? 'success' : 'warning')));
        }
      }

      // print block content if it has to be displayed
      //  - if number of $showed items is lower them number of maximum items to be displayed
      //  - and, if it has to be filtered whether it corresponds with filter option ('null' or 'old')
      if($filter_lang !== null) {
        if($filter_option == 'null') {
          if($completeness[$filter_lang] == 'warning' || $completeness[$filter_lang] == 'danger') {
            if($count >= ($page-1)*$max_items && $showed < $max_items) {
              include('./view/list-block-filter.php');
              $showed++;
            }
            $count++;
          }
        }
        elseif($filter_option == 'old' && $completeness[$filter_lang] == 'info') {
          if($count >= ($page-1)*$max_items && $showed < $max_items) {
            include('./view/list-block-filter.php');
            $showed++;
          }
          $count++;
        }
      }
      else { // dont filter, show 'full' block with all languages
        if($count >= ($page-1)*$max_items) {
          include('./view/list-block.php');
          $showed++;
        }
        if($showed >= $max_items) break; // no filter and showed max already, can stop
        $count++;
      }
    }
    $html = ob_get_clean();
    // all possible items to be shown
    // if no filter, its number of rows, if its filtered, then calculated amount
    $rows = ($filter_lang === null ? $req->num_rows : $count);
    $req4->close();     // TODO smazat po prechodu na MySQL 8.0 / MariaDB 10.2
    $req3->close();
    $req2->close();
    $req->close();
    return array('html' => $html, 'rows' => $rows);
  }

  /** Returns content for modal window with template preview.
   *
   * @input $_POST[template] - id of the template
   */
  protected function getListModal() {
    $id = $_POST['template'];
    $req = $this->db->prepare("SELECT b.id, h.title, h.text, type, image, image_position
                        FROM block b
                        JOIN block_history h ON h.id_block = b.id
                        WHERE id_template = ?
                          AND b.deleted = '0'
                          AND h.id IN (
                              SELECT MAX(id)
                              FROM block_history
                              WHERE deleted = '0'
                                AND language = 'cz'
                              GROUP BY id_block
                          )
                        ORDER BY position ASC") or die($this->db->error);
    $req->bind_param("d", $id);
    $req->execute();
    $req->bind_result($bid, $title, $text, $type, $image, $i_position);
    ob_start();
    while($req->fetch()) {
      include('./view/preview-block.php');
    }
    $html = ob_get_clean();
    return array('status' => 'success', 'html' => $html);
  }

  /** Deletes whole template.
   *
   * @input $_POST[template] - template id
   */
  protected function deleteTemplate() {
    $id = $_POST['template'];
    $user = 42;
    $req = $this->db->prepare("UPDATE template
                      SET deleted = ?
                      WHERE id = ?
                      LIMIT 1");
    $req->bind_param("dd", $user, $id);
    $req->execute();
    $req->close();

    // return all items without the deleted one
    return $this->getFilteredTemplates();
  }
}