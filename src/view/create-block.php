
<div class="row my-2" id="block-<?php echo $bid ?>">
  <div class="col-1 text-center mr-2 text-light">
    <i class="fas fa-arrow-up fa-2x hiddenhover pointer btn-move-up"></i><br>
    <i class="fas fa-arrow-down fa-2x hiddenhover pointer btn-move-down"></i>
  </div>
  <div class="col border border-black-50 rounded px-1" id="blocktext-<?php echo $bid ?>">
    <div class="row">
      <?php if($type == "text") { ?>
          <?php if($image !== null && $i_position == "left") { ?>
              <img src="./src/images/<?php echo $image ?>"
                   style="max-height: 50px; max-width: 100px" class="col-3 float-left pr-0">
          <?php } ?>
          <div class="col">
            <b><?php echo $title ?></b><br>
            <?php echo $text ?>
          </div>
          <?php if($image !== null && $i_position == "right") { ?>
              <img src="./src/images/<?php echo $image ?>"
                   style="max-height: 50px; max-width: 80px" class="col-3 float-right pl-0">
          <?php } ?>
        <?php } elseif($type == "video") { ?>
            <img src="https://img.youtube.com/vi/<?php echo $title ?>/0.jpg"
                 style="max-height: 50px; max-width: 80px" class="col-3 float-left pr-0">
            <div class="col">
                <b><?php echo $text ?></b><br>
                <a href="http://www.youtube.com/watch?v=<?php echo $title ?>" target="_blank">
                    http://youtu.be/<?php echo $title ?></a>
            </div>
        <?php } elseif($type == "image") { ?>
            <img src="./src/images/<?php echo $image ?>"
                 style="max-height: 80px; max-width: 100px" class="col float-left pr-0">
            <b class="col float-right"><?php echo $title ?></b>
        <?php } ?>
    </div>
  </div>
  <div class="col-1 text-center mr-2 p-0 text-light ">
    <i class="fas fa-times fa-2x hiddenhover btn-block-delete pointer"
       title="Delete block" id="blockdelete-<?php echo $bid ?>"></i><br>
    <i class="fas fa-2x hiddenhover btn-block-edit pointer text-grey
              fa-<?php echo ($type == "text" ? "align-left" : $type) ?>"
       title="Edit block" id="blockedit-<?php echo $bid ?>"></i>
      <i class="fas fa-cog fa-spin fa-2x text-primary d-none"
         id="block-spinner-<?php echo $bid ?>"></i>
  </div>
</div>