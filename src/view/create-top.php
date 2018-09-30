
<form method="post">
    <div class="row">
        <div class="col-12 my-2">
            <button class="btn btn-info float-left" name="action" value="cancelCreate">Return</button>
            <button class="btn btn-success float-right" name="action" value="saveCreate"
                    disabled id="btn-template-save">Save all</button>
        </div>
    </div>
    <div class="row">
        <div class="col-12 my-2">
            <span class="float-left"><i class="fas fa-user"></i> <?php echo $author ?></span>
            <span class="float-right"><?php echo $data ?></span>
        </div>
    </div>
    <div class="row">
        <input type="text" class="form-control mx-3" placeholder="Item name" name="template-title"
               value="<?php echo $title ?>" id="template-title">
        <input type="hidden" name="id" value="<?php echo $id ?>" id="template-id">
    </div>
</form>