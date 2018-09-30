
<div class="col-md-6">
  <div class="card mb-2">
    <div class="card-body p-1 bg-light">
      <form method="post" class="card-title background-animated">
        <div class="bg-light background-masker top"></div>
        <div class="border-light background-masker right-1"></div>
        <div class="border-light background-masker right-2"></div>
        <div class="bg-light background-masker left"></div>
        <div class="bg-light background-masker middle-top"></div>
        <div class="bg-light background-masker middle-center"></div>
        <div class="bg-light background-masker middle-bottom"></div>
        <div class="bg-light background-masker bottom"></div>
      </form>
      <div class="card-text">
        <form method="post" class="float-left ml-3">
          <?php foreach($languages as $key) { ?>
            <div class="btn badge badge-secondary p-1 px-2"> <?php echo ucfirst($key) ?> </div>
          <?php } ?>
        </form>
        <div class="float-right">
          <div class="py-0 list-modal mx-3">
            <i class="fas fa-eye text-secondary"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>