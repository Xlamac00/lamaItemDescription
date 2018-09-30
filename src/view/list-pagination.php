
<div class="row">
  <select class="col col-lg-2 custom-select pagination-count mb-2 mb-lg-0 ml-1">
      <option value="6" <?php if($count == 6) echo 'selected' ?>>6</option>
      <option value="10" <?php if($count == 10) echo 'selected' ?>>10</option>
      <option value="20" <?php if($count == 20) echo 'selected' ?>>20</option>
      <option value="30" <?php if($count == 30) echo 'selected' ?>>30</option>
  </select>
  <div class="col">
    <ul class="pagination justify-content-center">
      <li class="page-item <?php if($_SESSION['template_filter_page'] == 1) echo 'disabled' ?> page-prev">
        <button class="page-link" aria-label="Previous" type="button" name="prev">
          <span aria-hidden="true">&laquo;</span>
          <span class="sr-only">Previous</span>
        </button>
      </li>
      <?php for($i = 1; $i-1 < $rows/6; $i++) { ?>
        <li class="page-item <?php if($_SESSION['template_filter_page'] == $i) echo 'active'?> page-number"
            value="<?php echo $i ?>">
          <button class="page-link" type="button"><?php echo $i ?></button>
        </li>
      <?php } ?>
      <li class="page-item page-next
        <?php if($_SESSION['template_filter_page'] == round($rows/$_COOKIE['template_count'])) echo 'disabled' ?>">
        <button class="page-link" aria-label="Next" type="button" name="next">
          <span aria-hidden="true">&raquo;</span>
          <span class="sr-only">Next</span>
        </button>
      </li>
    </ul>
  </div>
  <form method="post" class="col col-lg-2 mb-2 mb-lg-0">
      <button class="btn btn-primary  w-100" name="action" value="create">New template</button>
  </form>
</div>