/** Created by Jan Lamacz
 * jan.lamacz@gmail.com
 * 29. 9. 2018
 */

$(document).ready(function() {
    var image = false;
    var title = false;
    var text = false;
    var activeMode = true; // whether to allow button actions

    { // block after the page load
        $('#btn-template-save').attr('disabled', document.getElementById('template-title').value.length == 0);
    }

    $('.btn-newblock').click(hideNewBlock);
    function hideNewBlock() {
        if(!activeMode) return;
        document.getElementById('newblock').classList.add('d-none');
        switch(this.value) {
            case "text":
                document.getElementById('newblock-text').classList.remove('d-none');
                document.getElementById('newtext-left').classList.remove('d-none');
                document.getElementById('newtext-right').classList.add('d-none');
                document.getElementById('newtext-image').classList.remove('d-none');
                document.getElementById('btn-newtext-center').classList.remove('btn-outline-primary');
                document.getElementById('btn-newtext-righttext').classList.remove('btn-outline-primary');
                document.getElementById('btn-newtext-center').classList.add('btn-outline-secondary');
                document.getElementById('btn-newtext-righttext').classList.add('btn-outline-secondary');
                document.getElementById('btn-newtext-lefttext').classList.remove('btn-outline-secondary');
                document.getElementById('btn-newtext-lefttext').classList.add('btn-outline-primary');
                $('#newtext-texttitle').focus();
                title = document.getElementById('newtext-texttitle').value.length > 0;
                text = document.getElementById('newtext-textarea').value.length > 0;
                var imageName = document.getElementsByClassName('newtext-image-name');
                image = imageName[0].innerHTML.length > 0 || imageName[1].innerHTML.length > 0;
                $('#btn-newtext-save').attr('disabled', !(image && title && text));
                break;
            case "video":
                document.getElementById('newblock-video').classList.remove('d-none');
                $('#newvideo-link').focus();
                image = text = true;
                title = false;
                $('#btn-newvide-save').attr('disabled', !(image && title && text));
                break;
            case "image":
                document.getElementById('newblock-image').classList.remove('d-none');
                $('#newimage-text').focus();
                text = title = true;
                image = false;
                break;
        }
    }

    /** Switch between text creation mode: text with image on left, on right and without image.
     * Hides and changes elements to show/hide form to upload image and to change colors
     * of buttons to switch between these modes.
     */
    $('.btn-newtext-text').click(newBlockText);
    function newBlockText() {
        document.getElementById('btn-newtext-lefttext').classList.add('btn-outline-secondary');
        document.getElementById('btn-newtext-center').classList.add('btn-outline-secondary');
        document.getElementById('btn-newtext-righttext').classList.add('btn-outline-secondary');
        document.getElementById('btn-newtext-lefttext').classList.remove('btn-outline-primary');
        document.getElementById('btn-newtext-center').classList.remove('btn-outline-primary');
        document.getElementById('btn-newtext-righttext').classList.remove('btn-outline-primary');
        switch(this.value) {
            case "left":
                document.getElementById('newtext-left').classList.remove('d-none');
                document.getElementById('newtext-right').classList.add('d-none');
                document.getElementById('btn-newtext-lefttext').classList.remove('btn-outline-secondary');
                document.getElementById('btn-newtext-lefttext').classList.add('btn-outline-primary');
                document.getElementById('newtext-image').classList.remove('d-none');
                var imageName = document.getElementsByClassName('newtext-image-name');
                image = imageName[0].innerHTML.length > 0 || imageName[1].innerHTML.length > 0;
                break;
            case "center":
                document.getElementById('newtext-left').classList.add('d-none');
                document.getElementById('newtext-right').classList.add('d-none');
                document.getElementById('btn-newtext-center').classList.remove('btn-outline-secondary');
                document.getElementById('btn-newtext-center').classList.add('btn-outline-primary');
                document.getElementById('newtext-image').classList.add('d-none');
                image = true;
                break;
            case "right":
            default:
                document.getElementById('newtext-left').classList.add('d-none');
                document.getElementById('newtext-right').classList.remove('d-none');
                document.getElementById('btn-newtext-righttext').classList.remove('btn-outline-secondary');
                document.getElementById('btn-newtext-righttext').classList.add('btn-outline-primary');
                document.getElementById('newtext-image').classList.remove('d-none');
                var imageName = document.getElementsByClassName('newtext-image-name');
                image = imageName[0].innerHTML.length > 0 || imageName[1].innerHTML.length > 0;
                break;
        }
        $('#newtext-texttitle').focus();
        $('#btn-newtext-save').attr('disabled', !(image && title && text));
    }

    /** Save button enables.
     * Functions that check if all fields are filled and then enables 'save' button.
     */
    $('#newtext-image').change(newBlockFileUpload);
    $('#newtext-texttitle').on('input', newBlockTextTitle);
    $('#newtext-textarea').on('input', newBlockTextArea);
    function newBlockFileUpload() {
        var filename = getUploadFileName(document.getElementById('newtext-image').value);
        if (filename) {
            var imageName = document.getElementsByClassName('newtext-image-name');
            imageName[0].innerHTML = filename;
            imageName[1].innerHTML = filename;
            document.getElementById('newtext-image-holder').classList.remove('d-none');
            image = true;
            $('#newtext-texttitle').focus();
        }
        $('#btn-newtext-save').attr('disabled', !(image && title && text));
    }
    function newBlockTextTitle() {
        title = (this.textLength > 0);
        $('#btn-newtext-save').attr('disabled', !(image && title && text));
    }
    function newBlockTextArea() {
        text = (this.textLength > 0);
        $('#btn-newtext-save').attr('disabled', !(image && title && text));
    }
    function getUploadFileName(fullPath) {
        if (fullPath) {
            var startIndex = (fullPath.indexOf('\\') >= 0 ? fullPath.lastIndexOf('\\') : fullPath.lastIndexOf('/'));
            var filename = fullPath.substring(startIndex);
            if (filename.indexOf('\\') === 0 || filename.indexOf('/') === 0) {
                filename = filename.substring(1);
            }
            return filename;
        }
        return '';
    }
    $('#newvideo-link').on('input', newVideoLink);
    function newVideoLink() {
        title = (this.textLength > 0);
        $('#btn-newvideo-save').attr('disabled', !(image && title && text));
    }
    $('#newimage-image').change(newImageFileUpload);
    function newImageFileUpload() {
        var filename = getUploadFileName(document.getElementById('newimage-image').value);
        if (filename) {
            document.getElementById('newimage-holder').classList.remove('d-none');
            image = true;
            $('#newimage-text').focus();
        }
        $('#btn-newimage-save').attr('disabled', !(image && title && text));
    }

    /** Keypress listener.
     * Listens to enter key to send requests.
     */
    $(document).keypress(function(e) {
        if(e.which === 13) { // enter
            switch(e.target.id) {
                case "newtext-texttitle":
                case "newtext-textarea":
                    if(title && text && image && e.ctrlKey) newBlockSave(e);
                    break;
                case "newvideo-link":
                    if(title && text && image) newVideoSave(e);
                    break;
                case "newimage-text":
                    if(title && text && image) newImageSave(e);
                    break;
            }
        }
    });

    /** New block creation cancel buttons.
     * Buttons that close new block dialog.
     */
    $('#btn-newtext-cancel').click(newBlockTextCancel);
    function newBlockTextCancel() {
        document.getElementById('btn-newtext-lefttext').classList.add('btn-outline-secondary');
        document.getElementById('btn-newtext-center').classList.add('btn-outline-secondary');
        document.getElementById('btn-newtext-righttext').classList.add('btn-outline-secondary');
        document.getElementById('btn-newtext-lefttext').classList.remove('btn-outline-primary');
        document.getElementById('btn-newtext-center').classList.remove('btn-outline-primary');
        document.getElementById('btn-newtext-righttext').classList.remove('btn-outline-primary');
        document.getElementById('newblock').classList.remove('d-none');
        document.getElementById('newblock-text').classList.add('d-none');
    }
    $('#btn-newvideo-cancel').click(newBlockVideoCancel);
    function newBlockVideoCancel() {
        document.getElementById('newblock').classList.remove('d-none');
        document.getElementById('newblock-video').classList.add('d-none');
    }
    $('#btn-newimage-cancel').click(newBlockImageCancel);
    function newBlockImageCancel() {
        document.getElementById('newblock').classList.remove('d-none');
        document.getElementById('newblock-image').classList.add('d-none');
    }

    /* *********************************************************************************
     *                                AJAX REQUESTS
     * *********************************************************************************/

    /** Sends the form to the server (including provided picture) and hides this element.
     * Creates FormData with title, text and image and sends that to the server. There, the
     * images is saved in the directory and everything is saved in the db.
     * Then, this element is hidden and element with 'new block' is shown.
     */
    $('#btn-newtext-save').click(newBlockSave);
    function newBlockSave(event) {
        if(!activeMode) return;
        event.stopPropagation(); // Stop stuff happening
        event.preventDefault(); // Totally stop stuff happening
        var formData = new FormData();
        formData.append('request', 'text');
        formData.append('title', document.getElementById('newtext-texttitle').value);
        formData.append('text', document.getElementById('newtext-textarea').value);
        formData.append('template', document.getElementById('template-id').value);
        formData.append('image', document.getElementById('newtext-image').files[0]);
        formData.append('position', document.getElementById('newtext-right').classList.contains('d-none') ? 'left' : 'right');
        $.ajax({
            url: './src/AjaxController.php',
            type: "POST",
            dataType: "json",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            async: true,
            success: function (data) {
                console.log(data);
                if(data.status !== 'success') {
                    alert('Unable to save file. Write permission denied - please contact the administrator.');
                }
                else {
                    // hide 'create new block' and clear form
                    document.getElementById('newblock').classList.remove('d-none');
                    document.getElementById('newblock-text').classList.add('d-none');
                    document.getElementById('newtext-texttitle').value = null;
                    document.getElementById('newtext-textarea').value = null;
                    document.getElementById('newtext-image').value = null;
                    var imageName = document.getElementsByClassName('newtext-image-name');
                    imageName[0].innerHTML = imageName[1].innerHTML = null;
                    document.getElementById('newtext-image-holder').classList.add('d-none');
                    addNewBlock(data.html);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log('ERRORS: ' + textStatus + "; " + errorThrown);
            }
        });
    }

    /** Inserts new block right in front of the 'new block' dialog,
     *  fills it with included html.
     */
    function addNewBlock(html) {
        var newblock = document.getElementById('newblock');
        var element = htmlToElement(html);
        // bind all button listeners
        element.getElementsByClassName('btn-move-up')[0].addEventListener('click', moveUp);
        element.getElementsByClassName('btn-move-down')[0].addEventListener('click', moveDown);
        element.getElementsByClassName('btn-block-delete')[0].addEventListener('click', blockDelete);
        element.getElementsByClassName('btn-block-edit')[0].addEventListener('click', blockEdit);
        // add block behind the last block
        newblock.parentNode.insertBefore(element, newblock);
    }

    /** Creates new DOM element with html inside
     */
    function htmlToElement(html) {
        var template = document.createElement('template');
        html = html.trim();
        template.innerHTML = html;
        return template.content.firstChild;
    }

    /** Sends the form with new video link to the server.
     * On the server, the data is saved to the DB, newly created block is added to the end
     * of the block-list and 'create new block' dialog is closed.
     * In case if the link is invalid youtube-link format, the dialog is not closed
     * and user is requested to enter valid link.
     */
    $('#btn-newvideo-save').click(newVideoSave);
    function newVideoSave() {
        if(!activeMode) return;
        var link = document.getElementById('newvideo-link').value;
        var template = document.getElementById('template-id').value;
        document.getElementById('newvideo-spinner').classList.remove('d-none');
        $.ajax({
            url: './src/AjaxController.php',
            type: "POST",
            dataType: "json",
            data: {
                "request": "video",
                "link": link,
                "template": template
            },
            async: true,
            success: function (data) {
                console.log(data);
                document.getElementById('newvideo-spinner').classList.add('d-none');
                if (data.status !== 'success') {
                    console.log("Error! "+data.msg);
                    document.getElementById('newvideo-link').classList.add('is-invalid');
                    $('#newvideo-link').select();
                }
                else {
                    document.getElementById('newblock-video').classList.add('d-none');
                    document.getElementById('newvideo-link').classList.remove('is-invalid');
                    var newblock = document.getElementById('newblock');
                    newblock.classList.remove('d-none');
                    document.getElementById('newvideo-link').value = null;
                    $('#btn-newvideo-save').attr('disabled', true);
                    addNewBlock(data.html);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log('ERRORS: ' + textStatus + "; " + errorThrown);
            }
        });

    }

    /** Sends the block with image and its description to the server.
     * There, the data is saved to the DB, 'create new block' dialog is hidden
     * and new block with image is added to the end of the block-list.
     */
    $('#btn-newimage-save').click(newImageSave);
    function newImageSave(event) {
        if(!activeMode) return;
        event.stopPropagation();
        event.preventDefault();
        document.getElementById('newimage-spinner').classList.remove('d-none');
        var formData = new FormData();
        formData.append('request', 'image');
        formData.append('title', document.getElementById('newimage-text').value);
        formData.append('template', document.getElementById('template-id').value);
        formData.append('image', document.getElementById('newimage-image').files[0]);
        $.ajax({
            url: './src/AjaxController.php',
            type: "POST",
            dataType: "json",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            async: true,
            success: function (data) {
                if(data.status !== 'success') {
                    alert('Unable to save file. Write permission denied - please contact the administrator.');
                }
                else {
                    document.getElementById('newimage-spinner').classList.add('d-none');
                    document.getElementById('newblock-image').classList.add('d-none');
                    var newblock = document.getElementById('newblock');
                    newblock.classList.remove('d-none');
                    document.getElementById('newimage-text').value = null;
                    document.getElementById('newimage-image').value = null;
                    document.getElementById('newimage-holder').classList.add('d-none');
                    $('#btn-newimage-save').attr('disabled', true);
                    addNewBlock(data.html);
                }
            }
        });
    }

    /** Deletes the block with data.
     * The server is requested to mark given block as deleted. The text is being made grey
     * and user has a chance to undo the deletion. After the page is reloaded, the block
     * cannot be restored.
     */
    $('.btn-block-delete').click(blockDelete);
    function blockDelete() {
        if(!activeMode) return;
        var blockId = this.id.split('-')[1];
        var btn = this.id;
        document.getElementById('block-spinner-'+blockId).classList.remove('d-none');
        document.getElementById('blockedit-'+blockId).classList.add('d-none');
        $.ajax({
            url: './src/AjaxController.php',
            type: "POST",
            dataType: "json",
            data: {
                "request": "deleteBlock",
                "blockId": blockId
            },
            async: true,
            success: function (data) {
                console.log(data);
                document.getElementById('block-spinner-'+blockId).classList.add('d-none');
                document.getElementById('blockedit-'+blockId).classList.remove('d-none');
                if (data.status !== 'success') {
                    console.log("Error! "+data.msg);
                }
                else {
                    if(data.state === 0) { // wasnt deleted before
                        document.getElementById(btn).classList.remove('fa-times');
                        document.getElementById(btn).classList.add('fa-redo');
                        document.getElementById(btn).classList.add('text-dark');
                        document.getElementById('blocktext-'+blockId).classList.add('text-light');
                        $('#block-'+blockId+' .btn-move-up')[0].classList.add('d-none');
                        $('#block-'+blockId+' .btn-move-down')[0].classList.add('d-none');
                        document.getElementById('blockedit-'+blockId).classList.add('d-none');
                    }
                    else { // was deleted, restore it
                        document.getElementById(btn).classList.remove('fa-redo');
                        document.getElementById(btn).classList.add('fa-times');
                        document.getElementById(btn).classList.remove('text-dark');
                        document.getElementById('blocktext-'+blockId).classList.remove('text-light');
                        $('#block-'+blockId+' .btn-move-up')[0].classList.remove('d-none');
                        $('#block-'+blockId+' .btn-move-down')[0].classList.remove('d-none');
                        document.getElementById('blockedit-'+blockId).classList.remove('d-none');
                    }
                }
            }
        });
    }

    /** Initiates the block edition process.
     * The block is replaced with new code that allows its inputs to be edited.
     * During this phase, all the other functions are disabled and user cannot do
     * anything else other then edit this block.
     */
    $('.btn-block-edit').click(blockEdit);
    function blockEdit() {
        if(!activeMode) return;
        var blockId = this.id.split('-')[1];
        document.getElementById('block-spinner-'+blockId).classList.remove('d-none');
        document.getElementById('blockedit-'+blockId).classList.add('d-none');
        $.ajax({
            url: './src/AjaxController.php',
            type: "POST",
            dataType: "json",
            data: {
                "request": "startEditBlock",
                "blockId": blockId
            },
            async: true,
            success: function (data) {
                document.getElementById('block-spinner-'+blockId).classList.add('d-none');
                document.getElementById('blockedit-'+blockId).classList.remove('d-none');
                if (data.status !== 'success') {
                    console.log("Error! "+data.msg);
                }
                else {
                    var blockToReplace = document.getElementById('block-'+blockId);
                    var html = htmlToElement(data.html);
                    blockToReplace.replaceWith(html);
                    $('#block-'+blockId+' .savechanges').on('click', blockEditStop);
                    activeMode = false;
                    $('#changeblock-title').select();
                }
            }
        });
    }

    /** Finished block edition process.
     * Sends the new data to the server, where they are saved to the DB.
     * The code is again replaced with previous block with new values and
     * all other functions are enabled again.
     */
    function blockEditStop() {
        var blockId = this.id.split('-')[1];
        document.getElementById('changeblock-spinner').classList.remove('d-none');
        var formData = new FormData();
        formData.append('request', 'stopEditBlock');
        formData.append('blockId', blockId);
        var text = document.getElementById('changeblock-text');
        formData.append('text', text.name === 'image' ? 'image' : text.value);
        formData.append('title', document.getElementById('changeblock-title').value);
        var files = document.getElementById('changeblock-text').files;
        if(typeof files !== "undefined" && files !== null && files.length === 1)
            formData.append('image', document.getElementById('changeblock-text').files[0]);
        $.ajax({
            url: './src/AjaxController.php',
            type: "POST",
            dataType: "json",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            async: true,
            success: function (data) {
                console.log(data);
                document.getElementById('changeblock-spinner').classList.add('d-none');
                if (data.status !== 'success') {
                    console.log("Error! "+data.msg);
                    document.getElementById('changeblock-title').classList.add('is-invalid');
                    $('#changeblock-title').select();
                }
                else {
                    var blockToReplace = document.getElementById('block-'+blockId);
                    var html = htmlToElement(data.html);
                    blockToReplace.replaceWith(html);
                    var replaced = document.getElementById('block-'+blockId);
                    replaced.getElementsByClassName('btn-move-up')[0].addEventListener('click', moveUp);
                    replaced.getElementsByClassName('btn-move-down')[0].addEventListener('click', moveDown);
                    replaced.getElementsByClassName('btn-block-delete')[0].addEventListener('click', blockDelete);
                    replaced.getElementsByClassName('btn-block-edit')[0].addEventListener('click', blockEdit);
                    activeMode = true;
                }
            }
        });
    }

    /** Sends the request to switch the block with the one about him.
     * New block position is calculated on the server side and then the block is inserted
     * before the previous one.
     * If the block is the first one, nothing happens.
     */
    $('.btn-move-up').on('click', moveUp);
    function moveUp() {
        if(!activeMode) return;
        var blockId = this.parentNode.parentNode.id.split('-')[1];
        var template = document.getElementById('template-id').value;
        document.getElementById('block-spinner-'+blockId).classList.remove('d-none');
        document.getElementById('blockedit-'+blockId).classList.add('d-none');
        console.log(blockId);
        $.ajax({
            url: './src/AjaxController.php',
            type: "POST",
            dataType: "json",
            data: {
                "request": "moveBlockUp",
                "blockId": blockId,
                "template": template
            },
            async: true,
            success: function (data) {
                console.log(data);
                document.getElementById('block-spinner-'+blockId).classList.add('d-none');
                document.getElementById('blockedit-'+blockId).classList.remove('d-none');
                if (data.status !== 'success') {
                    console.log(data.msg);
                }
                else {
                    var blockToMove = document.getElementById('block-'+data.ids[0]);
                    var blockBefore = document.getElementById('block-'+data.ids[1]);
                    blockToMove.parentNode.insertBefore(blockToMove, blockBefore);
                }
            }
        });
    }

    /** sends the request to switch the block with the one behind him.
     * New block position is calculated on the server side and then the block is inserted
     * after the next one.
     * If the block is the last one, nothing happens.
     */
    $('.btn-move-down').on('click', moveDown);
    function moveDown() {
        if(!activeMode) return;
        var blockId = this.parentNode.parentNode.id.split('-')[1];
        var template = document.getElementById('template-id').value;
        document.getElementById('block-spinner-'+blockId).classList.remove('d-none');
        document.getElementById('blockedit-'+blockId).classList.add('d-none');
        $.ajax({
            url: './src/AjaxController.php',
            type: "POST",
            dataType: "json",
            data: {
                "request": "moveBlockDown",
                "blockId": blockId,
                "template": template
            },
            async: true,
            success: function (data) {
                console.log(data);
                document.getElementById('block-spinner-'+blockId).classList.add('d-none');
                document.getElementById('blockedit-'+blockId).classList.remove('d-none');
                if (data.status !== 'success') {
                    console.log(data.msg);
                }
                else {
                    var blockToMove = document.getElementById('block-'+data.ids[0]);
                    var blockAfter = document.getElementById('block-'+data.ids[1]);
                    blockAfter.parentNode.insertBefore(blockToMove, blockAfter.nextSibling);
                }
            }
        });
    }

    /** Saves the template title after user clicks outside of the title box.
     * Sends ajax request to the server to save the title and
     * enables/disables "Save template" button.
     */
    $('#template-title').focusout(templateSaveTitle);
    function templateSaveTitle() {
        var template = document.getElementById('template-id').value;
        $.ajax({
            url: './src/AjaxController.php',
            type: "POST",
            dataType: "json",
            data: {
                "request": "templateTitle",
                "template": template,
                "title": this.value
            },
            async: true,
            success: function (data) {
                if (data.status !== 'success') {
                    console.log(data.msg);
                    $('#btn-template-save').attr('disabled', true);
                }
                else {
                    console.log(data);
                    $('#btn-template-save').attr('disabled', false);
                }
            }
        });
    }
});