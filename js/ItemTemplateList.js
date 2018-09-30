/** Created by Jan Lamacz
 * jan.lamacz@gmail.com
 * 29. 9. 2018
 */

$(document).ready(function() {
    var templateDelete = 0;

    /** After the page is loaded, download and display list items.
     * Before this function, only placeholders are show.
     * The server is requested to calculate current items and they are displayed.
     */
    loadItems();
    function loadItems() {
        var page = document.getElementById('page').value;
        ajaxGetItems(null, null, page);
    }

    /** Filter mode option <radio> box.
     * Allow to filter items - to show all items, only outdated or only not-translated items.
     * Sends request to the server to display filtered items.
     */
    $('input[type="radio"]').on('change', function() {
        var select = document.getElementById('filter-language');
        var language = select.selectedOptions[0].value;
        if(language === 'null') { // if no language is selected, select czech
            var cookie = getCookie('template_language');
            if(cookie === null) cookie = 'cz';
            var newIndex = 1;
            for(var i = 0; i < select.length; i++) {
                if(select[i].value === cookie)
                    newIndex = i;
            }
            select.selectedIndex = newIndex;
        }
        // download new filtered items
        var page = document.getElementById('page').value;
        ajaxGetItems(this.value, language, page);
    });

    /** Language selection <select> box.
     * Changes selected language to filter items.
     * Saves users preffered language to the cookies and sends request to the
     * server to download new filtered items.
     */
    $('#filter-language').on('change', function () {
       var lang = this.selectedOptions[0].value;
       if(lang !== 'null') { // if any language is selected
           var cookie = getCookie('template_language');
           if(cookie === null || lang !== cookie) { // save the cookie
               console.log('setting '+lang);
               setCookie('template_language', lang, 365);
           }
           var option = '';
           var radios = document.getElementsByName('exampleRadios');
           for (var i = 0, length = radios.length; i < length; i++) {
               if (radios[i].checked) {
                   option = radios[i].value;
                   break;
               }
           }
           if(option === 'all') // no reason to download, language does not affect 'all'
               this.blur();
           else {// get new values
               var page = document.getElementById('page').value;
               ajaxGetItems(option, lang, page);
           }
       }
       else
           this.blur();
    });

    /** Change number of displayed items with <option> box.
     * Saves preffered item count to the cookie and sends request to the server
     * to get correct number of items.
     */
    $('.pagination-count').on('change', function () {
        this.blur();
        var value = this.value;
        var selects = document.getElementsByClassName('pagination-count');
        var indexes = {'6':0,'10':1,'20':2,'30':3};
        // update the selected value on both paginations (top and bottom of the page)
        for(var i = 0; i < 2; i++) {
            selects[i].selectedIndex = indexes[value];
        }
        // save selected value to the cookie
        setCookie('template_count', value, 365);

        // download new items with correct number
        var language = document.getElementById('filter-language').selectedOptions[0].value;
        var option = '';
        var radios = document.getElementsByName('exampleRadios');
        for (var j = 0, length = radios.length; j < length; j++) {
            if (radios[j].checked) {
                option = radios[j].value;
                break;
            }
        }
        var page = document.getElementById('page').value;
        ajaxGetItems(option, language, page);
    });

    /** Pagination navigation buttons - change page.
     * Changes the page number and downloads new items on given page.
     */
    $('.page-link').on('click', function () {
        this.blur();
        var oldPage = parseInt(document.getElementById('page').value);
        // get number of next page
        var next = 1;
        if(this.name === "prev")
            next = oldPage - 1;
        else if(this.name === "next")
            next = oldPage + 1;
        else
            next = this.innerText;
        if(parseInt(next) === oldPage) return; // nothing to do
        document.getElementById('page').value = next;

        var rows = document.getElementById('rows').value;
        updatePaginationButtons(next, rows);

        // send request to the server
        var language = document.getElementById('filter-language').selectedOptions[0].value;
        var option = '';
        var radios = document.getElementsByName('exampleRadios');
        for (var i = 0, length = radios.length; i < length; i++) {
            if (radios[i].checked) {
                option = radios[i].value;
                break;
            }
        }
        ajaxGetItems(option, language, next);
    });

    /** Recalculates number of displayed items and shows/hides pagination buttons.
     * Usually called after the data is downloaded.
     * Shows/hides pagination buttons to correspond with total number of pages
     * and enables/disables the buttons "next" and "previous" page.
     *
     * @param currentPage - currently displayed page
     * @param maxRows - number of displayed items
     */
    function updatePaginationButtons(currentPage, maxRows) {
        var maxCount = getCookie('template_count'); // max number of items on one page
        if(maxCount === null) maxCount = 10;
        // make pagination buttons blue for the selected page
        var items = document.getElementsByClassName('page-number');
        for(var j = 0; j < items.length; j++) {
            items[j].classList.remove('active');
            if(items[j].value*maxCount >= +maxRows + +maxCount) // hide some buttons if there isn't that many pages
                items[j].classList.add('d-none'); //unary operator "+" to convert stings to int
            else {
                items[j].classList.remove('d-none');
                if(items[j].value === parseInt(currentPage)) // make btn active
                    items[j].classList.add('active');
            }
        }

        // enable/disable next and previous buttons
        var btnPrev = document.getElementsByClassName('page-prev');
        for(var p = 0; p < 2; p++) {
            if(currentPage <= 1) btnPrev[p].classList.add('disabled');
            else btnPrev[p].classList.remove('disabled');
        }
        var btnNext = document.getElementsByClassName('page-next');
        for(var n = 0; n < 2; n++) {
            if(currentPage*maxCount >= maxRows) btnNext[n].classList.add('disabled');
            else btnNext[n].classList.remove('disabled');
        }
    }

    /** Downloads item blocks and displays them.
     *
     * @param filter - filter options ("all", "old", "null")
     * @param language - filter language ("cz", "sk", "pl", "de", "en")
     * @param page - current page
     */
    function ajaxGetItems(filter, language, page) {
        if(language !== null) {
            document.getElementById('filter-spinner').classList.remove('d-none');
            document.getElementById('filter-close').classList.add('d-none');
            document.getElementById('list-loading-small').classList.remove('d-none');
            document.getElementById('list-body').classList.add('d-none');
        }
        console.log('reqiestiong '+language+','+filter+","+page);
        $.ajax({
            url: './src/AjaxController.php',
            type: "POST",
            dataType: "json",
            data: {
                "request": "getTemplates",
                "language": language,
                "option": filter,
                "page": page
            },
            async: true,
            success: function (data) {
                console.log(data);
                if(language !== null) {
                    document.getElementById('filter-spinner').classList.add('d-none');
                    document.getElementById('filter-close').classList.remove('d-none');
                    document.getElementById('list-loading-small').classList.add('d-none');
                    document.getElementById('list-body').classList.remove('d-none');
                }
                if(data.status === 'success') {
                    document.getElementById('page').value = data.page;
                    document.getElementById('rows').value = data.rows;
                    updatePaginationButtons(data.page, data.rows);
                    var body = document.getElementById('list-body');
                    body.innerHTML = data.html;
                    var btn = body.getElementsByClassName('list-modal');
                    for (var i=0; i<btn.length; i++) {
                        btn[i].addEventListener('click', showListModal);
                    }
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log('ERRORS: ' + textStatus + "; " + errorThrown);
            }
        });
    }

    /** Functions to show and save cookies from Js.
     * https://www.w3schools.com/js/js_cookies.asp
     */
    function setCookie(cname, cvalue, exdays) {
        var d = new Date;
        d.setTime(d.getTime() + (exdays*24*60*60*1000));
        var expires = "expires="+ d.toUTCString();
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    }
    function getCookie(cname) {
        var name = cname + "=";
        var decodedCookie = decodeURIComponent(document.cookie);
        var ca = decodedCookie.split(';');
        for(var i = 0; i <ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) === ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) === 0) {
                return c.substring(name.length, c.length);
            }
        }
        return null;
    }

    /** Triggers when delete modal question is shown.
     * Stores id of the template to be deleted
     */
    $('#modal-delete').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Button that triggered the modal
        templateDelete = button.data('id'); // Extract info from data-* attributes
    });
    $('.template-delete').on('click', function () {
        document.getElementById('delete-spinner').classList.remove('d-none');
        $.ajax({
            url: './src/AjaxController.php',
            type: "POST",
            dataType: "json",
            data: {
                "request": "deleteTemplate",
                "template": templateDelete
            },
            async: true,
            success: function (data) {
                console.log(data);
                document.getElementById('delete-spinner').classList.add('d-none');
                if(data.status === 'success') {
                    var body = document.getElementById('list-body');
                    body.innerHTML = data.html;
                    var btn = body.getElementsByClassName('list-modal');
                    for (var i=0; i<btn.length; i++) {
                        btn[i].addEventListener('click', showListModal);
                    }
                    $('#modal-delete').modal('hide')
                }
            }
        });
    });

    /** Downloads the data for "show preview" modal window.
     * Downloads content and display modal window.
     */
    function showListModal() {
        var id = this.id.split('-')[2];
        document.getElementById('list-spinner-'+id).classList.remove('d-none');
        document.getElementById('btn-modal-'+id).classList.add('d-none');
        $.ajax({
            url: './src/AjaxController.php',
            type: "POST",
            dataType: "json",
            data: {
                "request": "getListModal",
                "template": id
            },
            async: true,
            success: function (data) {
                console.log(data);
                document.getElementById('list-spinner-'+id).classList.add('d-none');
                document.getElementById('btn-modal-'+id).classList.remove('d-none');
                if(data.status === 'success') {
                    document.getElementById('list-modal-body-'+id).innerHTML = data.html;
                    $('#list-modal-'+id).modal('show');
                }
            }
        });
    }

    /** Shows/hides filter options
     *
     */
    $('#filter-show').click(showFilter);
    function showFilter() {
        document.getElementById('filter').classList.remove('d-none');
        document.getElementById('filter-show').classList.add('d-none');
    }
    $('#filter-hide').click(hideFilter);
    $('#filter-close').click(hideFilter);
    function hideFilter() {
        document.getElementById('filter').classList.add('d-none');
        document.getElementById('filter-show').classList.remove('d-none');

    }


});