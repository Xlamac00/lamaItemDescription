<?php
/** Created by Jan Lamacz
 * jan.lamacz@gmail.com
 * 29. 9. 2018
 */

abstract class AbstractController {
  protected $db;

  /** Connects to the database.
   * TODO: change the connection on the live servers!
   */
  public function __construct() {
    if($_SERVER["SERVER_ADDR"]=="127.0.0.1" || $_SERVER["SERVER_ADDR"]=="::1") {
      $db = new mysqli("localhost","johny","futurama");
      $db->select_db('lama');
    }
    else {
      $db = new mysqli("wm46.wedos.net","w53424_eshop","Db|44z~Et|4");
      $db->select_db('d53424_eshop');
    }
    $db->query("SET NAMES utf8 COLLATE utf8_unicode_ci");
    $db->set_charset("utf8");
    $this->db = $db;
  }

  /** Converts Youtube URL to Youtube ID.
   * https://gist.github.com/Glurt/ea11b690ba4b1278e049 - url formats
   * */
  protected function getYoutubeId($link) {
    if(strlen($link) == 11) return $link;
    // get video ID from link
    // http://www.youtube.com/watch?v=-wtIMTCHWuI
    $id1 = explode('?v=', $link)[1];
    $id = explode('/', $id1)[0];
    if(strlen($id) <= 0) {
      // http://www.youtube.com/v/-wtIMTCHWuI?version=3&autohide=1
      $id1 = explode('v/', $link)[1];
      $id = explode('?', $id1)[0];
      if(strlen($id) <= 0) {
        // http://youtu.be/-wtIMTCHWuI
        $id1 = explode('.be/', $link)[1];
        $id = explode('/', $id1)[0];
      }
    }
    return trim($id);
  }
}