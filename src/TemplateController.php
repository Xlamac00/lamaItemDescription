<?php
/** Created by Jan Lamacz
 * jan.lamacz@gmail.com
 * 29. 9. 2018
 */

include_once "AbstractController.class.php";

/** Controller for all actions that are not-Ajax and usually require reload of the page.
 * */
class TemplateController extends AbstractController {

  /** Displays default content of the 'List page'.
   * Default content of the page with list of all items.
   * The items are not created there, only their placeholders are displayed
   * and items are downloaded with JS and Ajax when the page is loaded
   * to increase render/download speed.
   *
   * @input $_SESSION[template_filter_page] - page number
   * @input $_SESSION[template_filter_option] - filter options ('all', 'old', 'null')
   * @input $_COOKIE[template_count] - max number of items on one page
   * */
  public function drawListTemplate() {
    $req = $this->db->prepare("SELECT id FROM template
                      WHERE deleted = '0'") or die($this->db->error);
    $req->execute();
    $req->bind_result($id);
    $req->store_result();
    $rows = $req->num_rows;
//    $result = '<div class="px-3 col-6">';
    $languages = array("cz", "sk", "pl", "en", "de");
    if(!isset($_SESSION['template_filter_page']))
      $_SESSION['template_filter_page'] = 1;
    if(!isset($_SESSION['template_filter_option']))
      $_SESSION['template_filter_option'] = 'all';
    $count = isset($_COOKIE['template_count']) ? $_COOKIE['template_count'] : 10;

    ob_start();
    include('./src/view/list-top.php');
    include('./src/view/list-pagination.php');
    echo '<input type="hidden" id="page" value="'.$_SESSION['template_filter_page'].'">';
    echo '<input type="hidden" id="rows" value="'.$rows.'">';
    include('./src/view/list-loading.php');
    echo '<div class="row" id="list-body">';
    // for every template
    for($i = 0; $req->fetch() && $i < $count; $i++) {
      include('./src/view/list-block-empty.php');
    }
    $req->close();
    echo '</div>';
    include('./src/view/list-pagination.php');
    $result = ob_get_clean();
//    $result .= '</div>';
    return $result;
  }

  /** Displays default content of the 'translation page'
   * Default content of the page to translate blocks of the template
   * to one language.
   *
   * @input $_SESSION[template_id] - id of the template
   * @input $_SESSION[template_language] - language
   */
  public function drawTranslateTemplate() {
    $template = $_SESSION['template_id'];

    $req = $this->db->prepare("SELECT id, title, author, date
                      FROM template
                      WHERE id = ? LIMIT 1") or die($this->db->error);
    $req->bind_param("d", $template);
    $req->execute();
    $req->bind_result($id, $title, $author, $data);
    $req->fetch();
    $req->close();

    $req = $this->db->prepare("SELECT b.id, type, image, image_position
                        FROM block b
                        WHERE id_template = ?
                          AND b.deleted = '0'
                        ORDER BY position ASC") or die($this->db->error);
    $req->bind_param("d", $template);
    $req->execute();
    $req->bind_result($bid, $type, $image, $i_position);
    $req->store_result();

// TODO START smazat po prechodu na MySQL 8.0 / MariaDB 10.2
//    $req2 = $this->db->prepare("WITH ranked_history AS (
//        SELECT m.*, ROW_NUMBER() OVER (PARTITION BY language ORDER BY id DESC) AS rh
//        FROM block_history AS m
//        WHERE id_block = ?
//          AND language IN ('cz', '".$_SESSION['template_language']."')
//          AND deleted = '0'
//        )SELECT text, title, language, date
//        FROM ranked_history WHERE rh = 1");
    $req2 = $this->db->prepare("SELECT text, title, date
                        FROM block_history
                        WHERE language = 'cz'
                          AND deleted = '0'
                          AND id_block = ?
                        ORDER BY date DESC LIMIT 1") or $error = $this->db->error;
    $req3 = $this->db->prepare("SELECT text, title, date
                        FROM block_history
                        WHERE language = ?
                          AND deleted = '0'
                          AND id_block = ?
                        ORDER BY date DESC LIMIT 1") or $error = $this->db->error;
// TODO END smazat po prechodu na MySQL 8.0 / MariaDB 10.2
//    $result = '<div class="px-3 col-6">';
    ob_start();
    include('./src/view/translate-top.php');
    while($req->fetch()) {
// TODO START smazat po prechodu na MySQL 8.0 / MariaDB 10.2
//      $req2->bind_param("d", $bid);
//      $req2->execute();
//      $req2->bind_result($text, $title, $language, $date);
//      $texts = array();
//      while($req2->fetch()) {
//        $texts[$language] = array("text" => $text, "title" => $title, "date" => $date);
//      }
      $req2->bind_param("d", $bid);
      $req2->execute();
      $req2->bind_result($text, $title, $date);
      $req2->fetch();
      $texts['cz'] = array("text" => $text, "title" => $title, "date" => $date);
      $req2->free_result();
      $req3->bind_param("sd", $_SESSION['template_language'], $bid);
      $req3->execute();
      $req3->bind_result($text, $title, $date);
      $req3->fetch();
      $texts[$_SESSION['template_language']] = array("text" => $text, "title" => $title, "date" => $date);
      $req3->free_result();
// TODO END smazat po prechodu na MySQL 8.0 / MariaDB 10.2
      include('./src/view/translate-block.php');
    }
    $req3->close();     // TODO smazat po prechodu na MySQL 8.0 / MariaDB 10.2
    $req2->close();
    $req->close();
    $result = ob_get_clean();
//    $result .= '</div>';
    return $result;
  }

  /** Displays default content of the 'new template creation' page
   * Default content of the page to create new czech template.
   *
   * @input $_SESSION[template_id] - id of the template
   */
  public function drawCreateTemplate() {
    $template = $_SESSION['template_id'];

    $req = $this->db->prepare("SELECT id, title, author, date
                      FROM template
                      WHERE id = ? LIMIT 1") or die($this->db->error);
    $req->bind_param("d", $template);
    $req->execute();
    $req->bind_result($id, $title, $author, $data);
    $req->fetch();
    $req->close();
    $req = $this->db->prepare("SELECT b.id, h.title, h.text, type, image, image_position, position
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
    $req->bind_param("d", $template);
    $req->execute();
    $req->bind_result($bid, $title, $text, $type, $image, $i_position, $position);
//    $result = '<div class="px-3 col-6">';
    ob_start();
    include('./src/view/create-top.php');
    while($req->fetch()) {
      include('./src/view/create-block.php');
    }
    $req->close();
    include('./src/view/create-bottom.html');
    $result = ob_get_clean();
//    $result .= '</div>';
    return $result;
  }

  public function createNewTemplate() {
    $author = 42;
    $req = $this->db->prepare("INSERT INTO template
                      (title, image, author, date) VALUES('', null, ?, now())") or die($this->db->error);
    $req->bind_param("d", $author);
    $req->execute();
    $req->close();
    return $this->db->insert_id;
  }

  public function changeTemplateTitle($template, $title) {
    if(strlen($title) > 0) {
      $req = $this->db->prepare("UPDATE template
                        SET title = ?
                        WHERE id = ?") or $error = $this->db->error;
      $req->bind_param("sd", $title, $template);
      $req->execute() or $error = $this->db->error;
      $req->close();
    }
  }

  public function cancelTemplate() {
    $template = $_SESSION['template_id'];
    $req = $this->db->prepare("SELECT COUNT(id)
                      FROM block
                      WHERE id_template = ?
                        AND deleted = '0'") or die($this->db->error);
    $req->bind_param("d", $template);
    $req->execute();
    $req->bind_result($count);
    $req->fetch();
    $req->close();
    // if there are no blocks, just deleted
    if($count <= 0 || strlen($_POST['template-title']) <= 0) {
      $req = $this->db->prepare("UPDATE template
                      SET deleted = '1'
                      WHERE id = ?") or die($this->db->error);
      $req->bind_param("d", $template);
      $req->execute();
      $req->close();
    }
  }
}

