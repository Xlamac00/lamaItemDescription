
<div class='col'>
  <i><?php echo $title ?></i>
  <p><?php echo $text ?></p>
  <div class="text-secondary">
    <span class='float-left'><?php echo date('j. m. Y', strtotime($date)) ?></span>
    <span class='float-right'><?php echo $author ?></span>
  </div>
</div>
<?php if($i == 0) { ?>
  <i class="fas fa-arrow-right text-primary float-left fa-2x mt-3"></i>
<?php } ?>