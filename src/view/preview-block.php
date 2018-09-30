
  <div class="col px-1 mb-4">
    <div class="row">
      <?php if($type == "text") { ?>
        <?php if($image !== null && $i_position == "left") { ?>
          <img src="./src/images/<?php echo $image ?>"
               class="col-6 float-left pr-0">
        <?php } ?>
        <div class="col">
          <b><?php echo $title ?></b>
          <p class="mt-2"><?php echo $text ?></p>
        </div>
        <?php if($image !== null && $i_position == "right") { ?>
          <img src="./src/images/<?php echo $image ?>"
               class="col-6 float-right pl-0">
        <?php } ?>
      <?php } elseif($type == "video") { ?>
        <div class="w-75 mx-auto">
          <iframe height="100%" width="100%" src="https://www.youtube-nocookie.com/embed/<?php echo $title ?>?rel=0&amp;"
                  frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
        </div>
      <?php } elseif($type == "image") { ?>
        <div class="w-75 mx-auto">
          <img src="./src/images/<?php echo $image ?>" class="w-100">
          <span class="text-center w-100"><?php echo $title ?></span>
        </div>
      <?php } ?>
    </div>
  </div>