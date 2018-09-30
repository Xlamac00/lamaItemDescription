
<!--<div class="row">-->
<!--    <form method="post" class="col-12 my-2">-->
<!--        <button class="btn btn-primary float-right" name="action" value="create">Create new template</button>-->
<!--    </form>-->
<!--</div>-->

<button id="filter-show" class="btn btn-link align-self-left mx-auto w-100
        <?php if($_SESSION['template_filter_option'] != 'all') echo 'd-none' ?>">
    Filters <i class="fas fa-angle-double-down"></i>
</button>
<div class="<?php if($_SESSION['template_filter_option'] == 'all') echo 'd-none' ?> " id="filter">
    <button class="btn btn-link align-self-left  mx-auto w-100" id="filter-hide">
        Filters <i class="fas fa-angle-double-up"></i></button>
    <button class="float-right m-2 btn btn-link" id="filter-close">
        <i class="fas fa-times text-secondary"></i>
    </button>
    <div id="filter-spinner" class="float-right d-none">
        <i class="fas fa-cog fa-spin m-3 text-primary"></i>
    </div>
    <div class="row m-2">
        <select class="col custom-select" id="filter-language">
            <option value="null">-- select language --</option>
            <?php foreach ($languages as $lang) { ?>
                <option value="<?php echo $lang ?>" <?php if($_COOKIE['template_language'] == $lang) echo 'selected' ?>>
                  <?php echo ucfirst($lang) ?>
                </option>
            <?php } ?>
        </select>
        <div class="col">
            <div class="custom-control custom-radio">
                <input class="custom-control-input" <?php if($_SESSION['template_filter_option'] == 'all') echo 'checked' ?>
                       type="radio" name="exampleRadios" id="radio-all" value="all">
                <label class="custom-control-label" for="radio-all">
                    Show all
                </label>
            </div>
            <div class="custom-control custom-radio">
                <input class="custom-control-input" <?php if($_SESSION['template_filter_option'] == 'old') echo 'checked' ?>
                       type="radio" name="exampleRadios" id="radio-old" value="old">
                <label class="custom-control-label" for="radio-old">
                    Show outdated
                </label>
            </div>
            <div class="custom-control custom-radio">
                <input class="custom-control-input" <?php if($_SESSION['template_filter_option'] == 'null') echo 'checked' ?>
                       type="radio" name="exampleRadios" id="radio-null" value="null">
                <label class="custom-control-label" for="radio-null">
                    Show not translated
                </label>
            </div>
        </div>
    </div>
</div>

<div class="modal fade bd-modal" tabindex="-1" role="dialog" aria-hidden="true" id="modal-delete">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h5 class="modal-title">Delete template</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Do you really want to delete this template?</p>
            </div>
            <div class="modal-footer py-2">
                <div id="delete-spinner" class="d-none">
                    <i class="fas fa-cog fa-spin m-3 fa-2x text-primary"></i>
                </div>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger template-delete">Delete</button>
            </div>
        </div>
    </div>
</div>
