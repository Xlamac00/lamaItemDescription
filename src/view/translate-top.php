
<?php $lang = $_SESSION['template_language'] ?>
<form method="post">
  <div class="row">
    <div class="col-4 my-2">
      <button class="btn btn-info float-left" name="action" value="cancelTranslate">Return</button>
<!--      <button class="btn btn-success float-right" name="action" value="saveCreate"-->
<!--              disabled id="btn-template-save">Save all</button>-->
    </div>
    <div class="col-4 text-center">
        <img src="./src/resources/<?php echo $lang ?>.png" height="50px">
    </div>
  </div>
</form>
<div class="form-row my-1">
    <span class="col-1 text-center mt-1">Cz: </span>
    <div class="col-md">
        <input type="text" class="form-control" value="<?php echo $title ?>" disabled>
    </div>
<!--    <span class="col-1 text-center mt-1">--><?php //echo ucfirst($lang) ?><!--: </span>-->
<!--    <div class="col-md">-->
<!--        <input type="text" class="form-control">-->
<!--    </div>-->
</div>