/** Created by Jan Lamacz
 * jan.lamacz@gmail.com
 * 29. 9. 2018
 */

$(document).ready(function() {
    var changedBlock = null;
    var changedTitle = null;
    var changedText = null;
    var deletingHistory = false;

    $('.translate-image').on('focus', translateTitleIn);
    $('.translate-image').on('focusout', translateTitleOut);
    $('.translate-title').on('focus', translateTextIn);
    $('.translate-title').on('focusout', translateTextOut);
    $('.translate-text').on('focus', translateTextIn);
    $('.translate-text').on('focusout', translateTextOut);
    function translateTitleIn() {
        if(changedBlock === null) {
            changedBlock = this.name;
            changedTitle = this.value;
            changedText = null;
            document.getElementById('block-confirm-'+changedBlock).classList.remove('d-none');
            document.getElementById('block-info-'+changedBlock).classList.add('d-none');
            this.readOnly = false;
            this.focus();
        }
    }
    function translateTextIn() {
        if(changedBlock === null) {
            changedBlock = this.name;
            document.getElementById('block-confirm-'+changedBlock).classList.remove('d-none');
            document.getElementById('block-info-'+changedBlock).classList.add('d-none');
            var title = document.getElementById('block-title-'+changedBlock);
            var text = document.getElementById('block-text-'+changedBlock);
            changedTitle = title.value;
            changedText = text.value;
            title.readOnly = false;
            text.readOnly = false;
            this.focus();
        }
    }
    function translateTitleOut() {
        if(this.value === changedTitle) { // nothing was changed, nothing to be saved
            document.getElementById('block-confirm-'+changedBlock).classList.add('d-none');
            document.getElementById('block-info-'+changedBlock).classList.remove('d-none');
            document.getElementById('block-title-'+changedBlock).readOnly = true;
            changedBlock = null;
            changedTitle = null;
            changedText = null;
        }
        else if(this.id !== changedBlock && changedBlock !== null) {
            // it was changed and he clicked elsewere - do not allow it
            $('#block-title-'+changedBlock)[0].classList.add('is-invalid');
        }
    }
    function translateTextOut() {
        var event = this.id.split('-')[2];
        if(event === changedBlock && (this.value === changedTitle || this.value === changedText)) {
            // nothing was changed, nothing to be saved
            document.getElementById('block-confirm-'+changedBlock).classList.add('d-none');
            document.getElementById('block-info-'+changedBlock).classList.remove('d-none');
            document.getElementById('block-title-'+changedBlock).readOnly = true;
            document.getElementById('block-text-'+changedBlock).readOnly = true;
            changedBlock = null;
            changedTitle = null;
            changedText = null;
        }
        else if(changedBlock !== null) {
            $('#block-title-'+changedBlock)[0].classList.add('is-invalid');
        }
    }

    /** Restores previous value in all boxes and hides 'save' buttons
     */
    $('.block-cancel').on('click', translateCancel);
    function translateCancel() {
        if(this.value === changedBlock) {
            document.getElementById('block-title-'+changedBlock).value = changedTitle;
            document.getElementById('block-title-'+changedBlock).readOnly = true;
            document.getElementById('block-text-'+changedBlock).value = changedText;
            document.getElementById('block-text-'+changedBlock).readOnly = true;
            document.getElementById('block-title-'+changedBlock).classList.remove('is-invalid');
            document.getElementById('block-confirm-'+changedBlock).classList.add('d-none');
            document.getElementById('block-info-'+changedBlock).classList.remove('d-none');
            changedBlock = null;
            changedTitle = null;
            changedText = null;
        }
    }

    /** Keypress listener.
     * Listens to enter key to send requests.
     */
    $(document).keypress(function(e) {
        if(e.which === 13 && e.ctrlKey) { // ctrl + enter
            var event = e.target.id.split('-')[2];
            if(event === changedBlock) {
                ajaxSendTranslation();
                e.target.blur();
            }
        }
    });

    $('.block-save').on('click', translateSave);
    function translateSave() {
        if(this.value === changedBlock) {
            ajaxSendTranslation();
        }
    }

    /** Sends content of the box to the server and saves its values.
     * Hides 'save' buttons.
     */
    function ajaxSendTranslation() {
        var title = document.getElementById('block-title-'+changedBlock).value;
        var text = document.getElementById('block-text-' + changedBlock).value;
        var type = document.getElementById('block-type-' + changedBlock).value;
        document.getElementById('block-confirm-'+changedBlock).classList.add('d-none');
        var id = changedBlock;
        if(title !== changedTitle || (typeof changedText === 'string' && text !== changedText)) {
            var oldTitle = changedTitle;
            document.getElementById('block-spinner-'+id).classList.remove('d-none');
            $.ajax({
                url: './src/AjaxController.php',
                type: "POST",
                dataType: "json",
                scriptCharset: "utf-8" ,
                contentType: "application/x-www-form-urlencoded; charset=UTF-8",
                data: {
                    "request": "translate",
                    "blockId": id,
                    "title": title,
                    "text": text,
                    "type": type
                },
                async: true,
                success: function (data) {
                    console.log(data);
                    document.getElementById('block-spinner-' + id).classList.add('d-none');
                    document.getElementById('block-info-' + id).classList.remove('d-none');
                    if(data.status !== 'success') {
                        document.getElementById('block-title-'+id).value = oldTitle;
                        // document.getElementById('block-title-'+id).classList.add('is-invalid');
                    }
                    else {
                        if(data.title.length > 0) {
                            document.getElementById('block-tick-' + id).classList.remove('text-danger');
                            document.getElementById('block-tick-' + id).classList.remove('text-info');
                            document.getElementById('block-tick-' + id).classList.remove('fa-clock');
                            document.getElementById('block-tick-' + id).classList.add('text-success');
                            document.getElementById('block-tick-' + id).classList.add('fa-flag');
                            document.getElementById('block-title-'+id).classList.remove('is-invalid');
                        }
                        else {
                            document.getElementById('block-tick-' + id).classList.remove('text-success');
                            document.getElementById('block-tick-' + id).classList.add('text-danger');
                        }
                    }
                }
            });
        }
        else {
            document.getElementById('block-info-'+changedBlock).classList.remove('d-none');
            document.getElementById('block-title-'+changedBlock).classList.remove('is-invalid');
        }
        document.getElementById('block-title-'+changedBlock).readOnly = true;
        document.getElementById('block-text-'+changedBlock).readOnly = true;
        changedTitle = null;
        changedText = null;
        changedBlock = null;
    }

    /** Displays reason why the text in the block is outdated in modal window.
     * Downloads window content from the server (old and new czech text)
     * and displays it.
     */
    $('.fa-clock').click(showTranslateChanges);
    function showTranslateChanges() {
        if(changedBlock === null) {
            var id = this.parentNode.id.split('-')[2];
            document.getElementById('block-info-'+id).classList.add('d-none');
            document.getElementById('block-spinner-'+id).classList.remove('d-none');
            $.ajax({
                url: './src/AjaxController.php',
                type: "POST",
                dataType: "json",
                data: {
                    "request": "changes",
                    "blockId": id
                },
                async: true,
                success: function (data) {
                    document.getElementById('block-spinner-' + id).classList.add('d-none');
                    document.getElementById('block-info-' + id).classList.remove('d-none');
                    if(data.status === 'success') {
                        $('#modal-changes-'+id).modal('show');
                        document.getElementById('changes-body-' + id).innerHTML = data.html;
                    }
                }
            });
        }
    }

    /** Marks new content as up to date.
     * In the modal window with changes allows to rewrite timestamp on the language
     * content to make it up to date so it does not require to be translated.
     * Closes modal window.
     */
    $('.btn-changes-update').click(updateTranslateChanges);
    function updateTranslateChanges() {
        console.log(this);
        var id = this.value;
        $.ajax({
            url: './src/AjaxController.php',
            type: "POST",
            dataType: "json",
            data: {
                "request": "updateChange",
                "blockId": id
            },
            async: true,
            success: function (data) {
                if(data.status === 'success') {
                    $('#modal-changes-'+id).modal('hide');
                    document.getElementById('block-tick-' + id).classList.remove('text-danger');
                    document.getElementById('block-tick-' + id).classList.remove('text-info');
                    document.getElementById('block-tick-' + id).classList.remove('fa-clock');
                    document.getElementById('block-tick-' + id).classList.remove('pointer');
                    document.getElementById('block-tick-' + id).classList.add('fa-flag');
                    document.getElementById('block-tick-' + id).classList.add('text-success');
                    document.getElementById('block-title-'+ id).classList.remove('is-invalid');
                }
            }
        });
    }

    /** Displays modal window with history of all translations.
     * Downloads history and displays it in the modal window.
     */
    $('.translate-modal').click(showTranslateModal);
    function showTranslateModal() {
        if(changedBlock === null) {
            var id = this.id;
            document.getElementById('block-info-'+id).classList.add('d-none');
            document.getElementById('block-spinner-'+id).classList.remove('d-none');
            $.ajax({
                url: './src/AjaxController.php',
                type: "POST",
                dataType: "json",
                data: {
                    "request": "history",
                    "blockId": id
                },
                async: true,
                success: function (data) {
                    $('#modal-history-'+id).modal('show');
                    document.getElementById('block-spinner-' + id).classList.add('d-none');
                    document.getElementById('block-info-' + id).classList.remove('d-none');
                    if(data.status === 'success') {
                        var modal = document.getElementById('modal-body-' + id);
                        modal.innerHTML = data.html;
                        var btn = modal.getElementsByClassName('history-delete');
                        for (var i=0; i<btn.length; i++) {
                            btn[i].addEventListener('click', deleteHistoryModal);
                        }
                    }
                }
            });
        }
    }

    /** Allows to delete previous translation in the history modal window.
     * Sends request to delete translation to the server, displays new history table
     * in the modal window and updates the content in text block in case
     * the newest translation was deleted.
     */
    function deleteHistoryModal() {
        if(deletingHistory === false) {
            deletingHistory = true; // disable all other buttons in the meanwhile
            var node = this;
            document.getElementById('modal-spinner-'+node.value).classList.remove('d-none');
            var block = document.getElementById('modal-id-'+node.value).value;
            this.classList.add('d-none');
            $.ajax({
                url: './src/AjaxController.php',
                type: "POST",
                dataType: "json",
                data: {
                    "request": "historyDelete",
                    "blockId": block,
                    "history": node.value
                },
                async: true,
                success: function (data) {
                    deletingHistory = false;
                    if(data.status === 'success') {
                        // select parent of table and replace it with new table without deleted element
                        var modal = node.parentNode.parentNode.parentNode.parentNode.parentNode;
                        modal.innerHTML = data.html;
                        var btn = modal.getElementsByClassName('history-delete');
                        for (var i=0; i<btn.length; i++) {
                            btn[i].addEventListener('click', deleteHistoryModal);
                        }
                        document.getElementById('block-title-'+block).value = data.title;
                        document.getElementById('block-text-'+block).value = data.text;
                        if(data.title === null && data.text === null) {
                            document.getElementById('block-tick-' + block).classList.add('text-danger');
                            document.getElementById('block-tick-' + block).classList.remove('text-success');
                        }
                    }
                }
            });
        }
    }
});