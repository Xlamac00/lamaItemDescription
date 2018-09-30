
<div class="col-md-6" id="list-<?php echo $id ?>">
    <div class="card mb-2">
        <div class="card-body p-1 bg-light">
            <form method="post" class="">
                <button class="btn btn-link" name="action" value="edit"><?php echo $title ?></button>
                <input type="hidden" name="template_id" value="<?php echo $id ?>">
                <div class="float-right">
                    <i class="fas fa-user-circle text-secondary mt-2 pt-1"></i>
                    <div class="dropdown float-right">
                        <button class="btn btn-link text-secondary" type="button"
                                id="dropdownMenuButton"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <div class="dropdown-menu m-0 p-0 dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                            <button class="btn dropdown-item" type="button" data-id="<?php echo $id ?>"
                                    data-toggle="modal" data-target=".bd-modal">Delete template</button>
                        </div>
                    </div>
                </div>
            </form>
            <div class="card-text">
                <form method="post" class="float-left ml-3">
                    <?php foreach($completeness as $key => $item) { ?>
                        <button class="btn badge badge-<?php echo $item ?> p-1 px-2"
                                name="language" value="<?php echo $key ?>">
                          <?php echo ucfirst($key) ?>
<!--                            <i class="fas fa---><?php //echo ($item == 'success' ? 'check-double'
//                                                        : ($item == 'info' ? 'history'
//                                                        : ($item == 'warning' ? 'check' : 'times'))) ?><!--"></i>-->
                        </button>
                    <?php } ?>
                    <input type="hidden" name="action" value="translate">
                    <input type="hidden" name="id" value="<?php echo $id ?>">
                </form>
                <div class="float-right">
                    <button class="btn btn-link py-0 list-modal" title="Show preview" id="btn-modal-<?php echo $id ?>">
                        <i class="fas fa-eye text-secondary"></i>
                    </button>
                    <i class="fas fa-cog fa-spin text-primary mr-3 d-none" id="list-spinner-<?php echo $id ?>"></i>
                    <div class="modal fade" id="list-modal-<?php echo $id ?>" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header bg-light">
                                    <h5 class="modal-title">Template preview - <?php echo $title ?></h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body" id="list-modal-body-<?php echo $id ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>