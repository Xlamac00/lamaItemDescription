
<?php $lang = $_SESSION['template_language']; ?>
<div class="row mb-1">
    <div class="col-md">
      <div class="card ">
        <input type="hidden" value="<?php echo $type ?>" id="block-type-<?php echo $bid ?>">
        <div class="card-body p-0 px-3">
            <?php if($type == "text") { ?>
              <div class="row">
                <?php if($image !== null && $i_position == "left") { ?>
                    <div class="col-2 d-none d-lg-flex">
                        <img src="./src/images/<?php echo $image ?>"
                             style="max-height: 110px;" class="w-100">
                    </div>
                <?php } ?>
                  <div class="col">
<!--                      <div class="row d-none d-lg-flex">-->
<!--                          <span class="col-6">Cz:</span>-->
<!--                          <span class="col-6">--><?php //echo ucfirst($lang) ?><!--:</span>-->
<!--                      </div>-->
                      <div class="row">
                          <input type="text" class="form-control col-6"
                                 readonly value="<?php echo $texts['cz']['title'] ?>">
                          <input type="text" class="form-control col-6 bg-white translate-title"
                                 id="block-title-<?php echo $bid ?>" readonly
                                 value="<?php echo $texts[$lang]['title'] ?>" name="<?php echo $bid ?>">
                      </div>
                      <div class="row">
                          <textarea class="form-control col-6" readonly
                          ><?php echo $texts['cz']['text'] ?></textarea>
                          <textarea class="form-control col-6 bg-white translate-text"
                                    id="block-text-<?php echo $bid ?>" readonly
                                    name="<?php echo $bid ?>"
                          ><?php echo $texts[$lang]['text'] ?></textarea>
                      </div>
                  </div>
                <?php if($image !== null && $i_position == "right") { ?>
                    <div class="col-2 d-none d-lg-flex">
                        <img src="./src/images/<?php echo $image ?>"
                             style="max-height: 110px;" class="w-100">
                    </div>
                <?php } ?>
              </div>
            <?php } elseif($type == "image") { ?>
              <div class="row">
                  <img src="./src/images/<?php echo $image ?>"
                       style="max-height: 80px; max-width: 100px" class="col float-left pr-0">
                  <div class="col px-0 px-lg-3">
                      <div class="form-group row m-0 p-0">
                          <label class="col-sm-1 col-form-label d-none d-lg-block mx-0 px-0">Cz:</label>
                          <div class="col-sm px-0 px-lg-3">
                              <input type="text" class="form-control" value="<?php echo $texts['cz']['title'] ?>"
                                     readonly>
                          </div>
                      </div>
                      <div class="form-group row m-0">
                          <label class="col-sm-1 col-form-label d-none d-lg-block mx-0 px-0">
                            <?php echo ucfirst($lang) ?>:</label>
                          <div class="col-sm px-0 px-lg-3">
                              <input type="text" class="form-control translate-image bg-white"
                                     id="block-title-<?php echo $bid ?>" readonly
                                     value="<?php echo $texts[$lang]['title'] ?>" name="<?php echo $bid ?>">
                              <input type="hidden" value="<?php echo $texts[$lang]['text'] ?>"
                                     id="block-text-<?php echo $bid ?>">
                          </div>
                      </div>
                  </div>
              </div>
            <?php } elseif($type == "video") { ?>
              <div class="row">
                  <img src="https://img.youtube.com/vi/<?php echo $texts['cz']['title'] ?>/0.jpg"
                       style="max-height: 80px; max-width: 100px" class="col float-left pr-0">
                  <div class="col px-0 px-lg-3">
                      <div class="form-group row m-0 p-0">
                          <label class="col-sm-1 col-form-label d-none d-lg-block mx-0 px-0">Cz:</label>
                          <div class="col-sm px-0 px-lg-3">
                              <input type="text" class="form-control" readonly
                                     value="https://www.youtube.com/watch?v=<?php echo $texts['cz']['title'] ?>">
                          </div>
                      </div>
                      <div class="form-group row m-0">
                          <label class="col-sm-1 col-form-label d-none d-lg-block mx-0 px-0">
                            <?php echo ucfirst($lang) ?>:</label>
                          <div class="col-sm px-0 px-lg-3">
                              <input type="text" class="form-control translate-image bg-white"
                                     id="block-title-<?php echo $bid ?>" readonly name="<?php echo $bid ?>"
                                     value="https://www.youtube.com/watch?v=<?php echo $texts[$lang]['title'] ?>">
                              <input type="hidden" id="block-text-<?php echo $bid ?>"
                                     value="<?php echo $texts[$lang]['text'] ?>">
                          </div>
                      </div>
                  </div>
              </div>
            <?php } ?>
        </div>
      </div>
    </div>

    <div class="col-1 m-0">
        <div id="block-info-<?php echo $bid ?>">
          <i class="fas fa-2x
                <?php if(strlen($texts[$lang]['title']) == 0) { ?>fa-flag text-danger
                <?php } elseif($texts[$lang]['date'] < $texts['cz']['date']) { ?>fa-clock text-info pointer
                <?php } else { ?>fa-flag text-success<?php } ?>"
             id="block-tick-<?php echo $bid ?>"></i>
          <div class=" d-none d-md-block">
              <button type="button" class="btn btn-link m-0 p-0 translate-modal" id="<?php echo $bid ?>">
                  <i class="fas fa-history fa-2x text-grey"></i>
              </button>

<!--                  <i class="fas fa-user fa-2x text-grey"></i>-->
          </div>
        </div>
        <div id="block-confirm-<?php echo $bid ?>" class="d-none">
            <button class="btn btn-link pt-0 px-0 block-save" value="<?php echo $bid ?>">
                <i class="fas fa-save fa-2x text-primary"></i></button>
            <button class="btn btn-link p-0 block-cancel" value="<?php echo $bid ?>">
                <i class="fas fa-trash fa-2x text-danger"></i></button>
        </div>
        <i class="fas fa-cog fa-spin fa-2x text-primary d-none"
           id="block-spinner-<?php echo $bid ?>"></i>
    </div>
</div>

<div class="modal fade" id="modal-history-<?php echo $bid ?>" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h5 class="modal-title" >
                    Block history - <?php echo $texts['cz']['title'] ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modal-body-<?php echo $bid ?>">
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-changes-<?php echo $bid ?>" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h5 class="modal-title" >
                    Block changes - <?php echo $texts['cz']['title'] ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="changes-body-<?php echo $bid ?>">
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button class="btn btn-success btn-changes-update" value="<?php echo $bid ?>">
                    Mark as up to date</button>
            </div>
        </div>
    </div>
</div>