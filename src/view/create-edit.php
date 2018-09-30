
<div class="row my-2" id="block-<?php echo $bid ?>">
  <div class="col border border-black-50 rounded px-1">
    <?php if($type == "text") { ?>
      <div class="row">
        <?php if($image !== null && $i_position == "left") { ?>
          <img src="./src/images/<?php echo $image ?>" class="col-3 float-left">
        <?php } ?>
        <div class="col">
          <input type="text" value="<?php echo $title ?>" class="form-control" id="changeblock-title">
          <textarea class="form-control" id="changeblock-text"><?php echo $text ?></textarea>
        </div>
        <?php if($image !== null && $i_position == "right") { ?>
          <img src="./src/images/<?php echo $image ?>" height="50px" class="col-3 float-right">
        <?php } ?>
      </div>
    <?php } elseif($type == "video") { ?>
      <div class="text-center">
        <img src="https://img.youtube.com/vi/<?php echo $title ?>/0.jpg" style="max-height: 80px; max-width: 80px">
      </div>
      <input type="text" class="form-control" id="changeblock-title"
             value="http://www.youtube.com/watch?v=<?php echo $title ?>">
      <input type="hidden" id="changeblock-text" value="video">
    <?php } elseif($type == "image") { ?>
      <div class="text-center">
        <img src="./src/images/<?php echo $image ?>" style="max-height: 80px; max-width: 80px">
      </div>
      <input type="text" class="form-control" id="changeblock-title"
             value="<?php echo $title ?>">
      <input type="file" name="image" class="my-2" id="changeblock-text" value="asd">
<!--      <input type="hidden" id="changeblock-text" value="image">-->
    <?php } ?>
  </div>
  <div class="col-1 text-center mr-2 p-0">
    <i class="fas fa-check-circle fa-2x pointer text-success savechanges"
       title="Save changes" id="save-<?php echo $bid ?>"></i>
    <i class="fas fa-cog fa-spin fa-2x text-primary mt-2 d-none" id="changeblock-spinner"></i>
  </div>
</div>