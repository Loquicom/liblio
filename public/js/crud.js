/* --- Vars --- */

let selectedId = null;
let data = {};
let maxPage

/* --- Function --- */

function setSearchMode() {
    const simpleSearch = document.getElementById('simple-search-input');
    const advancedSearch = document.getElementById('advanced-search');
    if (advancedSearch.hasAttribute('open')) {
        simpleSearch.removeAttribute('disabled');
    } else {
        simpleSearch.setAttribute('disabled', '');
    }
}

async function loadData() {
    // Loader
    loading(true);
    // Reset data
    document.querySelectorAll('.data-loaded').forEach(elt => elt.remove());
    document.getElementById('alert-error').classList.add('none');
    document.getElementById('alert-success').classList.add('none');
    // Parameters
    const pageSize = document.getElementById('page-size-select').value;
    const params = {
        page: document.getElementById('page-select-input').value,
        number: pageSize
    };
    if (document.getElementById('sort-select').value !== '') {
        params.sort = document.getElementById('sort-select').value;
    }
    // Search
    if (document.getElementById('advanced-search').hasAttribute('open')) { // Advanced search
        for (const key in fields) {
            const elt = document.getElementById('advanced-search-' + key);
            if (elt == null) continue;
            params[key] = elt.value;
        }
    } else if (document.getElementById('simple-search-input').value !== '') { // Simple search
        params.search = document.getElementById('simple-search-input').value;
    }
    // Call API
    const result = await callGet(url.api, params);
    if (result.success) {
        // Show and map data
        generateTable(result.data.values);
        data = {};
        for (const d of result.data.values) {
            data[d['id']] = d;
        }
        // Manage pagination
        maxPage = Math.ceil(result.data.total / pageSize);
        document.getElementById('current-value').innerText = (params.page * pageSize) > result.data.total ? result.data.total : params.page * pageSize;
        document.getElementById('total-value').innerText = result.data.total;
        refreshPagination();
    } else {
        if (result.message) {
            document.getElementsByClassName('alert-error')[0].innerHTML = result.message;
        } else {
            document.getElementsByClassName('alert-error')[0].innerHTML = lang.errorAjax;
        }
        document.getElementById('alert-error').classList.remove('none');
        document.getElementById('no-data-row').classList.remove('none');
    }
    // Disabled loader
    loading(false);
}

function loading(active) {
    const searchBtn = document.getElementById('search-btn');
    if (active) {
        searchBtn.setAttribute('disabled', '');
        searchBtn.setAttribute('aria-busy', 'true');
        document.getElementById('loading-row').classList.remove('none');
        document.getElementById('total-indicator').classList.add('hide');
    } else {
        searchBtn.removeAttribute('disabled');
        searchBtn.setAttribute('aria-busy', 'false');
        document.getElementById('loading-row').classList.add('none');
        document.getElementById('total-indicator').classList.remove('hide');
    }
}

function generateTable(values) {
    // Hide default row
    document.getElementById('no-data-row').classList.add('none');
    // Check if values is not empty
    if (values.length === 0) {
        document.getElementById('no-data-row').classList.remove('none');
        return;
    }
    // Generate new line
    let html = '';
    for (const val of values) {
        const id =
        html += '<tr class="data-loaded" data-id="' + val['id'] + '">';
        for (const key in fields) {
            const field = fields[key];
            if (!field.col) continue;
            let lib = '';
            if (val[key] != null && typeof val[key] === 'object') {
                lib = val[key]['lib'];
            } else if (val[key] != null) {
                lib = val[key];
            }
            html += '<td>' + lib + '</td>';
        }
        if (mode === 'edit' || detail !== 'none') {
            html += '<td class="center">';
            if (detail === 'page') {
                const path = window.location.pathname.startsWith('/') ? window.location.pathname.substring(1) : window.location.pathname;
                const location = window.location.origin.endsWith('/') ? window.location.origin + path : window.location.origin + '/' + path
                const url = location.endsWith('/') ? location + val['id'] : location + '/' + val['id'];
                html += '<a class="secondary action-detail" href="' + url + '" data-tooltip="' + lang['detail'] + '" data-placement="top"><span class="iconify" data-icon="mdi-eye"></span></a>'
            } else if (detail === 'popup') {
                html += '<a class="secondary action-detail dialog-open" href="#" data-dialog="dialog-detail" data-tooltip="' + lang['detail'] + '" data-placement="top" onclick="openPopupDetail(\'' + val['id'] + '\')"><span class="iconify" data-icon="mdi-eye"></span></a>'
            }
            if (mode === 'edit') {
                html += '<a class="secondary action-edit dialog-open" href="#" data-dialog="dialog-edit" data-tooltip="' + lang['edit'] + '" data-placement="top" onclick=\'selectedId="'+ val['id'] +'"\'><span class="iconify" data-icon="mdi-pencil"></span></a>'
                html += '<a class="secondary action-delete dialog-open" href="#" data-dialog="dialog-delete" data-tooltip="' + lang['del'] + '" data-placement="top" onclick=\'selectedId="'+ val['id'] +'"\'><span class="iconify" data-icon="mdi-delete"></span></a>'
            }
            html += '</td>';
        }
        html += '</tr>';
    }
    // Fill table
    const tableContent = document.getElementById('table-content');
    tableContent.innerHTML = tableContent.innerHTML + html;
    // Setup dialog
    refreshDialog();
    document.querySelectorAll('.action-edit').forEach(elt => {
       elt.addEventListener('click', () => {
           for (const key in fields) {
               if (data[selectedId][key] == null) continue;
               const value = typeof data[selectedId][key] === 'object' ? data[selectedId][key]['code'] : data[selectedId][key];
               document.getElementById('edit-'+key).value = value;
           }
       });
    });
}

function clearDialog() {
    for (const key in fields) {
        document.getElementById('edit-'+key).value = '';
    }
}

function changePage(value) {
    // Check value
    const oldValue = parseInt(document.getElementById('page-select-input').value);
    if (oldValue + value < 1 || oldValue + value > maxPage) {
        // Invalid value
        if (value === 0) { // Change from input
            if (oldValue < 1) { // Set min
                document.getElementById('page-select-input').value = 1;
            } else { // Set max
                document.getElementById('page-select-input').value = maxPage;
            }
        } else { // Change from button
            // Do nothing and stop
            return;
        }
    }
    // No problem, Change value
    else {
        document.getElementById('page-select-input').value = oldValue + value;
    }
    // Block change button
    document.getElementById('next-page').setAttribute('disabled', '');
    document.getElementById('prev-page').setAttribute('disabled', '');
    // Load data
    loadData();
}

function changePageSize() {
    // Reset page
    document.getElementById('page-select-input').value = 1;
    // Block change button
    document.getElementById('next-page').setAttribute('disabled', '');
    document.getElementById('prev-page').setAttribute('disabled', '');
    // Load data
    loadData();
}

function refreshPagination() {
    const actual = document.getElementById('page-select-input').value;
    if (actual > 1) {
        document.getElementById('prev-page').removeAttribute('disabled');
    } else {
        document.getElementById('prev-page').setAttribute('disabled', '');
    }
    if (actual < maxPage) {
        document.getElementById('next-page').removeAttribute('disabled');
    } else {
        document.getElementById('next-page').setAttribute('disabled', '');
    }
}

function editData() {
    // Hide alert
    document.getElementById('alert-success').classList.add('none');
    document.getElementById('alert-error').classList.add('none');
    // Get values
    const values = {id: selectedId};
    for (const key in fields) {
        values[key] = document.getElementById('edit-' + key).value;
    }
    // Call right method
    if (selectedId == null) {
        createData(values);
    } else {
        updateData(values);
    }
}

async function createData(values) {
    const result = await callPost(url.api, values);
    if (result.success) {
        await loadData();
        document.getElementsByClassName('alert-success')[0].innerHTML = lang.createSuccess;
        document.getElementById('alert-success').classList.remove('none');
    } else {
        if (result.message) {
            document.getElementsByClassName('alert-error')[0].innerHTML = result.message;
        } else {
            document.getElementsByClassName('alert-error')[0].innerHTML = lang.errorAjax;
        }
        document.getElementById('alert-error').classList.remove('none');
    }
    selectedId = null;
}

async function updateData(values) {
    const result = await callPut(url.api + '/' + selectedId, values);
    if (result.success) {
        await loadData();
        document.getElementsByClassName('alert-success')[0].innerHTML = lang.updateSuccess;
        document.getElementById('alert-success').classList.remove('none');
    } else {
        if (result.message) {
            document.getElementsByClassName('alert-error')[0].innerHTML = result.message;
        } else {
            document.getElementsByClassName('alert-error')[0].innerHTML = lang.errorAjax;
        }
        document.getElementById('alert-error').classList.remove('none');
    }
    selectedId = null;
}

async function deleteData() {
    // Hide alert
    document.getElementById('alert-success').classList.add('none');
    document.getElementById('alert-error').classList.add('none');
    // Call API
    const result = await callDelete(url.api + '/' + selectedId);
    if (result.success) {
        await loadData();
        document.getElementsByClassName('alert-success')[0].innerHTML = lang.deleteSuccess;
        document.getElementById('alert-success').classList.remove('none');
    } else {
        if (result.message) {
            document.getElementsByClassName('alert-error')[0].innerHTML = result.message;
        } else {
            document.getElementsByClassName('alert-error')[0].innerHTML = lang.errorAjax;
        }
        document.getElementById('alert-error').classList.remove('none');
    }
    selectedId = null;
}

function sendSearch(event) {
    if (event.key === "Enter") {
        loadData();
    }
}

function openPopupDetail(id) {
    selectedId = id;
    loadPopupDetail(id);
}

/* --- Page load --- */

(function() {
    // Load data
    loadData();
})();