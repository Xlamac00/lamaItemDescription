
<div class="col-md-6" id="list-<?php echo $id ?>">
  <div class="card mb-2">
    <div class="card-body p-1 bg-light">
      <form method="post" class="card-title m-0 p-0">
        <button class="btn btn-link" name="language" value="<?php echo $filter_lang ?>"><?php echo $title ?></button>
        <input type="hidden" name="template_id" value="<?php echo $id ?>">
        <button class="btn badge badge-<?php echo $completeness[$filter_lang] ?> p-1 m-2 mr-4 float-right"
                name="language" value="<?php echo $filter_lang ?>">
          <?php echo ucfirst($filter_lang) ?>
          <i class="fas fa-<?php echo ($completeness[$filter_lang] == 'success' ? 'check-double'
              : ($completeness[$filter_lang] == 'info' ? 'history'
              : ($completeness[$filter_lang] == 'warning' ? 'check' : 'times'))) ?>"></i>
        </button>
        <input type="hidden" name="action" value="translate">
        <input type="hidden" name="id" value="<?php echo $id ?>">
      </form>
    </div>
  </div>
</div>