<?php
/** Created by Jan Lamacz
 * jan.lamacz@gmail.com
 * 29. 9. 2018
 */

/**
 * Controller for Ajax requests from 'create new template' page.
 * */
class AjaxCreateController extends AbstractController {
  private $blockId = null;

  public function __construct($request) {
    parent::__construct();

    $ret = '';
//     TODO: check session if the user is logged
    switch ($request) {
      case "text":
        $ret = $this->createTextBlock(); break;
      case "video":
        $ret = $this->createVideoBlock(); break;
      case "image":
        $ret = $this->createImageBlock(); break;
      case "deleteBlock":
        $ret = $this->toggleDeleteBlock(); break;
      case "startEditBlock":
        $ret = $this->startEditBlock(); break;
      case "stopEditBlock":
        $ret = $this->stopEditBlock(); break;
      case "moveBlockUp":
        $ret = $this->moveBlockUp(); break;
      case "moveBlockDown":
        $ret = $this->moveBlockDown(); break;
      case "templateTitle":
        $ret = $this->changeTemplateTitle(); break;
    }

    echo json_encode($ret);
    exit;
  }

  /** Creates new block with text fields, can also include one image.
   * @input $_POST[template] - template id
   * @input $_POST[title] - title
   * @input $_POST[text] - text fields
   * @input $_POST[position] - image position: left/right
   * @input $_FILES[] - images
   * @var PDO db connection
   * @return array
   */
  protected function createTextBlock() {
    $error = null;
    $image = null;

    foreach($_FILES as $file) {
      $image = $this->saveImageToDisc($file);
      if($image === false) $error = "move_uploaded_file rights";
    }

    if($error === null) {
      $html = $this->insertBlockToDb($_POST['template'], "text",
        $_POST['title'], $_POST['text'], $image, $_POST['position']);
      return array('status' => 'success', 'html' => $html);
    }
    else return array('status' => 'error', 'msg' => $error);
  }

  /** Creates new block with video link
   * @input $_POST[template] - template id
   * @input $_POST[link] - youtube video url
   * */
  protected function createVideoBlock() {
    $link = $this->getYoutubeId($_POST['link']);
    if(strlen($link) <= 0)
      return array('status' => 'error', 'msg' => 'Wrong link format');

    $text = 'Youtube video';
    $html = $this->insertBlockToDb($_POST['template'], "video", $link, $text, null, null);
//    return array('status' => 'error', 'msg' => 'Wrong link format');
    if(!$this->insertHistory($this->blockId, $link, $text, 'sk')) return array('status' => 'error', 'msg' => 'history');
    if(!$this->insertHistory($this->blockId, $link, $text, 'pl')) return array('status' => 'error', 'msg' => 'history');
    if(!$this->insertHistory($this->blockId, $link, $text, 'en')) return array('status' => 'error', 'msg' => 'history');
    if(!$this->insertHistory($this->blockId, $link, $text, 'de')) return array('status' => 'error', 'msg' => 'history');
    return array('status' => 'success', 'html' => $html);
  }

  /** Creates new block with only image and its description.
   * @input $_POST[template] - template id
   * @input $_POST[title] - image description
   * @input $_FILES[] - images
   */
  protected function createImageBlock() {
    $error = null;
    $image = null;

    foreach($_FILES as $file) {
      $filename = explode('.', $file['name'])[0];
      $extension = explode('.', $file['name'])[1];
      for($i = 1; true; $i++) {
        if(!file_exists('./images/'.$filename.'_'.$i.".".$extension)) {
          break;
        }
      }
      $image = $filename.'_'.$i.".".$extension;
      if(!move_uploaded_file($file['tmp_name'], './images/'.$image))
        $error = "move_uploaded_file rights";
    }

    if($error === null) {
      $html = $this->insertBlockToDb($_POST['template'], "image",$_POST['title'], '', $image, null);
      return array('status' => 'success', 'html' => $html);
    }
    else return array('status' => 'error', 'msg' => $error);
  }

  /** Inserts block to the db.
   * @return string html - code of the created block
   * */
  private function insertBlockToDb($template, $type, $title, $text, $image, $i_position) {
    $error = null;
    $req0 = $this->db->prepare("SELECT max(position) FROM block
                        WHERE id_template = ?
                          AND deleted = '0'") or $error = $this->db->error;
    $req0->bind_param("d", $template);
    $req0->execute() or $error = $this->db->error;
    $req0->bind_result($position);
    $req0->fetch();
    $req0->close();
    if($error !== null) return $error;

    $position = $position + 1;
    $req = $this->db->prepare("INSERT INTO block (id_template, type, position, image, image_position) 
                       VALUES (?, ?, ?, ?, ?)") or $error = $this->db->error;
    $req->bind_param("dsdss", $template, $type, $position, $image, $i_position);
    $req->execute() or $error = $this->db->error;
    $req->close();
    if($error !== null) return $error;

    $bid = $this->db->insert_id;
    $this->blockId = $bid;
    if(!$this->insertHistory($bid, $title, $text, 'cz'))
      return 'Error in insertHistory';

    ob_start();
    include('./view/create-block.php');
    return ob_get_clean();
  }

  private function insertHistory($block, $title, $text, $language) {
    $error = null;
    $req = $this->db->prepare("INSERT INTO block_history (id_block, title, text, language, author, date)
                        VALUES (?, ?, ?, ?, 42, now())") or $error = $this->db->error;
    $req->bind_param("dsss", $block, $title, $text, $language);
    $req->execute() or $error = $this->db->error;
    $req->close();
    return $error === null;
  }

  /** Deletes a block, or restores it if it was already deleted.
   * Also recalculates position of all blocks (to exclude deleted one).
   * @input $_POST[blockid] - id of the block
   */
  protected function toggleDeleteBlock() {
    $error = null;

    $return = null;
    $id = $deleted = null;
    $req0 = $this->db->prepare("SELECT id, deleted 
                        FROM block
                        ORDER BY position ASC, deleted ASC") or $error = $this->db->error;
    $req0->execute();
    $req0->bind_result($id, $deleted);
    $req0->store_result();
    // cycle through all items, update their position and update deleted of blockId item
    for($i = 0; $req0->fetch(); ) {
      if($id == $_POST['blockId']) { // the one to be changed
        $return = $deleted;
        $newvalue = $deleted === 0 ? 42 : 0;
        if($deleted !== 0) $i++; // increment (was deleted, now isnt)
      }
      else {
        $newvalue = $deleted;
        if($deleted === 0) $i++; // increment
      }
      $req2 = $this->db->prepare("UPDATE block
                        SET deleted = ?, position = ?
                        WHERE id = ?") or $error = $this->db->error;
      $req2->bind_param("sdd", $newvalue, $i, $id);
      $req2->execute() or $error = $this->db->error;
      $req2->close();
    }
    $req0->close();

    return ($error !== null) ? array('status' => 'error', 'msg' => $error)
      : array('status' => 'success', 'state' => $return, 'block' => $_POST['blockId']);
  }

  /** Saves file from $_FILES to the disc.
   * @return string - image name
   * */
  private function saveImageToDisc($file) {
    $filename = explode('.', $file['name'])[0];
    $extension = explode('.', $file['name'])[1];
    for($i = 1; true; $i++) {
      if(!file_exists('./images/'.$filename.'_'.$i.".".$extension)) {
        break;
      }
    }
    $image = $filename.'_'.$i.".".$extension;
    if(!move_uploaded_file($file['tmp_name'], './images/'.$image))
      return false;
    return $image;
  }

  /** Returns html block-edit.php where are editable fields.
   * @input $_POST[blockid] - id of the block
   */
  protected function startEditBlock() {
    $bid = $_POST['blockId'];
    $error = null;
    $req = $this->db->prepare("SELECT b.image, b.image_position, type, h.text, h.title
                        FROM block b 
                        JOIN block_history h ON b.id = h.id_block
                        WHERE b.deleted = '0'
                          AND h.id IN (
                              SELECT MAX(id)
                              FROM block_history
                              WHERE deleted = '0'
                                AND id_block = ?
                                AND language = 'cz'
                              GROUP BY id_block
                          )
                        LIMIT 1") or $error = $this->db->error;
    $req->bind_param('d', $bid);
    $req->execute() or $error = $this->db->error;
    /** @var string $image */
    $req->bind_result($image, $i_position, $type, $text, $title);
    $req->fetch();
    $req->close();

    ob_start();
    include('./view/create-edit.php');
    $html = ob_get_clean();
    return ($error !== null) ? array('status' => 'error', 'msg' => $error)
      : array('status' => 'success', 'block' => $bid, 'html' => $html);
  }

  /** Saves edited values to the DB and returns edited block html.
   * @input $_POST[blockid] - id of the block
   * @input $_FILES[] - images, if they are to be edited
   */
  protected function stopEditBlock() {
    $bid = $_POST['blockId'];
    $error = null;
    // select informations about a block
    $req = $this->db->prepare("SELECT b.image, b.image_position, h.text, h.title, type
                        FROM block b 
                        JOIN block_history h ON b.id = h.id_block
                        WHERE b.deleted = '0'
                          AND h.id IN (
                              SELECT MAX(id)
                              FROM block_history
                              WHERE deleted = '0'
                                AND id_block = ?
                              GROUP BY id_block
                          )
                        LIMIT 1") or $error = $this->db->error;
    $req->bind_param('d', $bid);
    $req->execute() or $error = $this->db->error;
    $req->bind_result($image, $i_position, $text, $title, $type);
    $req->fetch();
    $req->close();

    if(!empty($_FILES)) { // change image
      foreach($_FILES as $file) {
        $image = $this->saveImageToDisc($file);
        if($image === false) $error = "move_uploaded_file rights";
      }

      $req = $this->db->prepare("UPDATE block SET image = ?
                         WHERE id = ?") or $error = $this->db->error;
      $req->bind_param("sd", $image, $bid);
      $req->execute() or $error = $this->db->error;
      $req->close();
      $_POST['text'] = 'image';
    }
    if(strlen($_POST['title']) > 0 && strlen($_POST['text']) > 0) {
      $title = $_POST['title'];
      $text = $_POST['text'];
      if($text === 'video') { // its not text, its youtube video
        $title = $this->getYoutubeId($title);
        if(strlen($text) <= 0)
          return array('status' => 'error', 'msg' => 'Wrong link format');
        $text = "Youtube video";
        if(!$this->insertHistory($bid, $title, $text, 'sk')) $error = true;
        if(!$this->insertHistory($bid, $title, $text, 'pl')) $error = true;
        if(!$this->insertHistory($bid, $title, $text, 'en')) $error = true;
        if(!$this->insertHistory($bid, $title, $text, 'de')) $error = true;
      }
      if(!$this->insertHistory($bid, $title, $text, 'cz')) $error = true;
    }

    ob_start();
    include('./view/create-block.php');
    $html = ob_get_clean();
    return ($error !== null) ? array('status' => 'error', 'msg' => $error)
      : array('status' => 'success', 'block' => $bid, 'html' => $html);
  }

  /** Changes position of the block with the one above him.
   * If the block is already at the first position, error is returned.
   * @input $_POST[blockid] - id of the block
   * */
  protected function moveBlockUp() {
    $blockId = $_POST['blockId'];

    $req = $this->db->prepare("SELECT position
                        FROM block
                        WHERE deleted = '0'
                          AND id = ? LIMIT 1") or $error = $this->db->error;
    $req->bind_param('d', $blockId);
    $req->execute();
    $req->bind_result($position);
    $req->fetch();
    $req->close();
    if($position > 1)
      return $this->switchBlocks( $_POST['template'],$position - 1, $position, $blockId);
    else
      return array('status' => 'error', 'msg' => 'Cannot move item at position 1 higher: '.$position);
  }

  /** Changes position of the block with the one below him.
   * If the block is already at the last position, error is returned.
   * @input $_POST[blockid] - id of the block
   * @input $_POST[templated] - id of the template with the blocks
   * */
  protected function moveBlockDown() {
    $blockId = $_POST['blockId'];
    // select number of all blocks in template
    $req = $this->db->prepare("SELECT MAX(position)
                        FROM block
                        WHERE id_template = ?
                          AND deleted = '0'") or $error = $this->db->error;
    $req->bind_param('d', $_POST['template']);
    $req->execute();
    $req->bind_result($max);
    $req->fetch();
    $req->close();

    $req = $this->db->prepare("SELECT position
                        FROM block
                        WHERE id_template = ?
                          AND deleted = '0'
                          AND id = ? LIMIT 1") or $error = $this->db->error;
    $req->bind_param('dd', $_POST['template'], $blockId);
    $req->execute();
    $req->bind_result($position);
    $req->fetch();
    $req->close();
    if($position < $max)
      return $this->switchBlocks($_POST['template'], $position + 1, $position, $blockId);
    else
      return array('status' => 'error', 'msg' => 'Cannot move lowest item even deeper: '.$position);
  }

  /** Switches position of the two blocks on new_position and position
   * and saves their new values to the db.
   * @param template - id of the template
   * @param new_position - position of the first block
   * @param position - position of the second block to be moved
   * @param blockId - id of the block to be moved
   * @return array
   * */
  private function switchBlocks($template, $new_position, $position, $blockId) {
    $positions = array($new_position, $position);
    // select id of the block above/below (on new position) of the block to be moved
    $req0 = $this->db->prepare("SELECT id
                        FROM block
                        WHERE id_template = ?
                          AND position = ?
                          AND deleted = '0'
                        LIMIT 1") or $error = $this->db->error;
    $req0->bind_param('dd', $template, $new_position);
    $req0->execute();
    $req0->bind_result($id);
    $req0->fetch();
    $req0->close();
    $ids = array($blockId, $id);
    // update information for the two blocks - the one to be changed and one above/below
    for($i = 0; $i < 2; $i++) {
      $req2 = $this->db->prepare("UPDATE block
                        SET position = ?
                        WHERE id = ?") or $error = $this->db->error;
      $req2->bind_param("dd", $positions[$i], $ids[$i]);
      $req2->execute() or $error = $this->db->error;
      $req2->close();
    }
    return array('status' => 'success', 'block' => $blockId, 'ids' => $ids, 'positions' => $positions);
  }

  /** Changes name of the template item.
   * @input $_POST[title] - new name
   * @input $_POST[template] - template id
   */
  protected function changeTemplateTitle() {
    $title = $_POST['title'];
    $template = $_POST['template'];
    if(strlen($title) > 0) {
      $req = $this->db->prepare("UPDATE template
                        SET title = ?
                        WHERE id = ?") or $error = $this->db->error;
      $req->bind_param("sd", $title, $template);
      $req->execute() or $error = $this->db->error;
      $req->close();
      return array('status' => 'success', 'template' => $template, 'name' => $title);
    }
    return array('status' => 'error', 'msg' => 'no name');
  }
}

