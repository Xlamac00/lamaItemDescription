<?php
/** Created by Jan Lamacz
 * jan.lamacz@gmail.com
 * 29. 9. 2018
 */

/**
 * Controller for Ajax requests from 'translate template' page.
 * */
class AjaxTranslateController extends AbstractController {

  public function __construct($request) {
    parent::__construct();

    $ret = '';
    switch ($request) {
      case "translate":
        $ret = $this->translateBlock();
        break;
      case "history":
        $ret = $this->translateHistory();
        break;
      case "historyDelete":
        $ret = $this->translateHistoryDelete();
        break;
      case "changes":
        $ret = $this->translateChangesHistory();
        break;
      case "updateChange":
        $ret = $this->translateUpdateChangeHistory();
        break;
    }

    echo json_encode($ret);
    exit;
  }

  /** Saves block translation.
   *
   * @input $_POST[blockId] - block id
   * @input $_POST[type] - block type
   * @input $_POST[title] - block title
   * @input $_POST[text] - block text
   * @input $_SESSION[template_language] - language
   */
  protected function translateBlock() {
    $error = null;
    if($_POST['type'] == "video") {
      $title = $this->getYoutubeId($_POST['title']);
      if(strlen($title) !== 11)
        return array('status' => 'error');
    }
    else $title = $_POST['title'];
    $req = $this->db->prepare("INSERT INTO block_history (id_block, title, text, language, author, date)
                        VALUES (?, ?, ?, ?, 42, now())") or $error = $this->db->error;
    $req->bind_param("dsss", $_POST['blockId'], $title, $_POST['text'], $_SESSION['template_language']);
    $req->execute() or $error = $this->db->error;
    $req->close();

    if($error !== null) return array('status' => 'error', 'msg' => $error, "text" => $_POST['text']);
    return array('status' => 'success', 'lang' => $_SESSION['template_language'],
                 'title' => $_POST['title'], 'text' => $_POST['text'], 'block' => $_POST['blockId']);
  }

  /** Selects history of all block translations.
   *
   * @input $_POST[blockId] - id of the block
   * @input $_SESSION[template_language] - language
   */
  protected function translateHistory() {
    $error = null;
    $bid = $_POST['blockId'];
    $req = $this->db->prepare("SELECT id, text, title, date, author 
                        FROM block_history
                        WHERE id_block = ?
                          AND language = ?
                          AND deleted = '0'
                        ORDER BY date DESC");
    $req->bind_param("ds", $bid, $_SESSION['template_language']);
    $req->execute() or $error = $this->db->error;
    $req->bind_result($hid, $text, $title, $date, $author);
    $html = '<table class="table table-borderless table-hover table-sm"><thead>
        <tr><th scope="col">Date</th><th scope="col"  style="width: 50%">Text</th><th scope="col">Author</th><th scope="col">Delete</th>
        </tr></thead><tbody>';
    ob_start();
    $first = array();
    for($i = 0; $req->fetch(); $i++) {
      if($i == 0) { // return values for the first (newest) element
        $first['text'] = $text;
        $first['title'] = $title;
      }
      include('./view/translate-history.php');
    }
    $html .= ob_get_clean();
    $html .= '</tbody></table>';
    $req->close();

    if($error !== null) return array('status' => 'error', 'msg' => $error, "text" => $_POST['text']);
    return array('status' => 'success', 'html' => $html, 'block' => $bid,
                 'text' => $first['text'], 'title' => $first['title']);
  }

  /** Deletes one item translation.
   *
   * @input $_POST[history] - id of the translation
   * @input $_SESSION[template_language] - language
   */
  protected function translateHistoryDelete() {
    $error = null;
    $user = 42;
    $req = $this->db->prepare("UPDATE block_history
                        SET deleted = ?
                        WHERE id = ?
                          AND language = ?") or $error = $this->db->error;
    $req->bind_param("dds", $user, $_POST['history'], $_SESSION['template_language']);
    $req->execute() or $error = $this->db->error;
    $req->close();
    if($error !== null) return array('status' => 'error', 'msg' => $error);
    return $this->translateHistory();
  }

  /** Displays reason why the block is outdated.
   * Selects newest czech content of the block and czech contect of the block
   * that was valid it the moment the translation was created.
   *
   * @input $_POST[blockId] - id of the block
   * @input $_SESSION[template_language] - language
   */
  protected function translateChangesHistory() {
    $error = null;
    // select date of the newest comment in current language
    $req = $this->db->prepare("SELECT date
                      FROM block_history
                      WHERE id_block = ?
                        AND language = ?
                        AND deleted = '0'
                      ORDER BY id DESC LIMIT 1") or $error = $this->db->error;
    $req->bind_param("ds", $_POST['blockId'], $_SESSION['template_language']);
    $req->execute() or $error = $this->db->error;
    $req->bind_result($date);
    $req->fetch();
    $req->close();

    $html = '<div class="row">';
    // then select currently newest czech and newest czech before the date (against which it was translated)
    $req2 = $this->db->prepare("SELECT text, title, author, date
                      FROM block_history
                      WHERE id_block = ?
                        AND language = 'cz'
                        AND deleted = '0'
                        AND date < ?
                      ORDER BY id DESC LIMIT 1") or $error = $this->db->error;
    // select both newest and previous text
    date_default_timezone_set('Europe/Prague');
    $dates = array($date, date("Y-m-d H:i:s"));
    ob_start();
    for($i = 0; $i < 2; $i++) {
      $req2->bind_param("ds", $_POST['blockId'], $dates[$i]);
      $req2->execute();
      $req2->bind_result($text, $title, $author, $date);
      $req2->fetch();
      include('./view/translate-changes.php');
    }
    $html .= ob_get_clean();
    $req2->close();
    if($error !== null) return array('status' => 'error', 'msg' => $error);
    return array('status' => 'success', 'html' => $html,
                 'block' => $_POST['blockId'], 'lang' => $_SESSION['template_language']);
  }

  /** Inserts block translation to the db once again with new timestamp.
   * This functions as the 'make block up-to-date' by re-inserting its content.
   *
   * @input $_POST[blockId] - id of the block
   * @input $_SESSION[template_language] - language
   */
  protected function translateUpdateChangeHistory() {
    // select newest text in given language
    $req = $this->db->prepare("SELECT text, title
                      FROM block_history
                      WHERE id_block = ?
                        AND language = ?
                        AND deleted = '0'
                      ORDER BY id DESC LIMIT 1") or $error = $this->db->error;
    $req->bind_param("ds", $_POST['blockId'], $_SESSION['template_language']);
    $req->execute() or $error = $this->db->error;
    $req->bind_result($text, $title);
    $req->fetch();
    $req->close();

    // and insert it again with new timestamp
    $req = $this->db->prepare("INSERT INTO block_history (id_block, title, text, language, author, date)
                        VALUES (?, ?, ?, ?, 42, now())") or $error = $this->db->error;
    $req->bind_param("dsss", $_POST['blockId'], $title, $text, $_SESSION['template_language']);
    $req->execute() or $error = $this->db->error;
    $req->close();
    return array('status' => 'success', 'block' => $_POST['blockId'],
                 'lang' => $_SESSION['template_language'], 'text' => $text, 'title' => $title);

  }
}
