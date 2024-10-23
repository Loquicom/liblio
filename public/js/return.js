/* --- Vars --- */

let dataMember = null;
let dataBooks = {};

/* --- Members search --- */

async function loadMembers() {
    // Reset values
    document.getElementById('dialog-member-no-data-row').classList.add('none');
    document.querySelectorAll('.data-loaded').forEach(elt => elt.remove());
    // Loader
    loadingDialogMember(true);
    // Parameters
    const params = {
        number: 20,
        firstname: document.getElementById('dialog-member-firstname').value,
        lastname: document.getElementById('dialog-member-lastname').value
    }
    // Call API
    const result = await callGet('/api/members', params);
    if (result.success) {
        generateTableMembers(result.data.values);
        // Save data
        dataMember = {};
        for (const d of result.data.values) {
            dataMember[d['id']] = d;
        }
    } else {
        console.error(result);
    }
    // Disabled loader
    loadingDialogMember(false);
}

function generateTableMembers(values) {
    // Check if values is not empty
    if (values.length === 0) {
        document.getElementById('dialog-member-no-data-row').classList.remove('none');
        return;
    }
    // Generate new line
    let html = '';
    for (const val of values) {
        html += '<tr class="data-loaded selectable" data-id="' + val['id'] + '">';
        html += '<td>' + val['id'] + '</td>';
        html += '<td>' + val['firstname'] + '</td>';
        html += '<td>' + val['lastname'] + '</td>';
        html += '</tr>';
    }
    // Fill table
    const tableContent = document.getElementById('dialog-member-data');
    tableContent.innerHTML = tableContent.innerHTML + html;
    // Setup selection
    document.querySelectorAll('.selectable').forEach(elt => {
       elt.addEventListener('click', () => {
           document.querySelectorAll('.selectable').forEach(elt => elt.classList.remove('selected'));
           elt.classList.add('selected');
       })
    });
}

function loadingDialogMember(active) {
    if (active) {
        document.getElementById('dialog-member-btn-search').setAttribute('aria-busy', 'true');
        document.getElementById('dialog-member-btn-search').setAttribute('disabled', '');
        document.getElementById('dialog-member-btn-select').setAttribute('disabled', '');
        document.getElementById('dialog-member-loading-row').classList.remove('none');
    } else {
        document.getElementById('dialog-member-btn-search').setAttribute('aria-busy', 'false');
        document.getElementById('dialog-member-btn-search').removeAttribute('disabled');
        document.getElementById('dialog-member-btn-select').removeAttribute('disabled');
        document.getElementById('dialog-member-loading-row').classList.add('none');
    }
}

function selectMemberFromSearch() {
    const id = document.querySelector('.selected')?.getAttribute('data-id');
    if (id == null) {
        return;
    }
    document.getElementById('member').value = id;
    document.getElementById('firstname').value = dataMember[id]['firstname'];
    document.getElementById('lastname').value = dataMember[id]['lastname'];
    document.getElementById('member').setAttribute('aria-invalid', 'false');
    loadBooks();
}

async function getMember() {
    // Get element
    const member = document.getElementById('member');
    const firstname = document.getElementById('firstname');
    const lastname = document.getElementById('lastname');
    // Reset
    member.removeAttribute('aria-invalid');
    firstname.value = '';
    lastname.value = '';
    // Get ID
    const id = member.value;
    if (id.trim() === '') {
        return;
    }
    // Call API
    const result = await callGet('/api/members/' + id);
    if (result.success) {
        firstname.value = result.data['firstname'];
        lastname.value = result.data['lastname'];
        member.setAttribute('aria-invalid', 'false');
        dataMember = result.data;
        loadBooks();
    } else {
        member.setAttribute('aria-invalid', 'true');
        dataMember = null;
    }
}

/* --- Select books --- */

async function loadBooks() {
    // Loader
    loading(true);
    // Reset table content
    document.querySelectorAll('.book-added').forEach(elt => elt.remove());
    document.getElementById('no-data-row').classList.add('none');
    document.getElementById('error-row').classList.add('none');
    // Get member id
    const id = document.getElementById('member').value;
    // Call API
    const result = await callGet('/api/members/' + id + '/borrow');
    console.log(result);
    if (result.success) {
        dataBooks = result.data;
        generateTableBooks(result.data);
    } else {
        dataBooks = {error: result.message ?? ''};
        document.getElementById('error-row').classList.remove('none');
    }
    // Disabled loader
    loading(false);
}

function generateTableBooks(values) {
    // Check if values is not empty
    if (values.length === 0) {
        document.getElementById('no-data-row').classList.remove('none');
        return;
    }
    // Generate new line
    let html = '';
    for (const val of values) {
        html += '<tr class="book-added tr-value" data-id="' + val['isbn'] + '">';
        html += '<td>' + val['isbn'] + '</td>';
        html += '<td>' + val['title'] + '</td>';
        html += '<td>' + val['author'] + '</td>';
        html += '<td>' + val['publisher'] + '</td>';
        html += '<td>' + val['out_date'] + '</td>';
        html += '<td class="center"><input class="select-book" type="checkbox" value="' + val['isbn'] + '" onchange="checkData()"></td>';
        html += '</tr>';
    }
    // Fill table
    const tableContent = document.getElementById('books-data');
    tableContent.innerHTML = tableContent.innerHTML + html;
}

/* --- Send data --- */

function checkData() {
    const id = document.getElementById('member').value;
    let checked = document.querySelectorAll('.select-book:checked').length > 0;
    if (id.trim() !== '' && checked) {
        document.getElementById('btn-valid').removeAttribute('disabled');
    } else {
        document.getElementById('btn-valid').setAttribute('disabled', '');
    }
}

async function sendData() {
    // Loader
    loading(true);
    // Get member id
    const id = document.getElementById('member').value.trim();
    // Set parameters
    const params = {
        books: []
    };
    document.querySelectorAll('.select-book:checked').forEach(elt => {
        params.books.push(elt.value);
    });
    // Call API
    const result = await callPost(`/api/members/${id}/return`, params);
    if (result.success) {
        // Message
        document.getElementsByClassName('alert-success')[0].innerHTML = lang.saveSuccess;
        document.getElementById('alert-success').classList.remove('none');
        // Reset
        reset();
    } else {
        if (result.message) {
            document.getElementsByClassName('alert-error')[0].innerHTML = result.message;
        } else {
            document.getElementsByClassName('alert-error')[0].innerHTML = lang.errorAjax;
        }
        document.getElementById('alert-error').classList.remove('none');
    }
    // Disable loader
    loading(false);
    // Hide message after 10s
    setTimeout(() => {
        document.getElementById('alert-success').classList.add('none');
        document.getElementById('alert-error').classList.add('none');
    }, 10000);
}

function loading(active) {
    const btnValid = document.getElementById('btn-valid');
    const searchMember = document.getElementById('search-member');
    const loadingRow = document.getElementById('loading-row');
    const trValues = document.querySelectorAll('.tr-value');
    if (active) {
        btnValid.setAttribute('disabled', '');
        btnValid.setAttribute('aria-busy', 'true');
        searchMember.setAttribute('disabled', '');
        searchMember.setAttribute('aria-busy', 'true');
        loadingRow.classList.remove('none');
        trValues.forEach(elt => elt.classList.add('none'));
    } else {
        checkData();
        btnValid.setAttribute('aria-busy', 'false');
        searchMember.removeAttribute('disabled');
        searchMember.setAttribute('aria-busy', 'false');
        loadingRow.classList.add('none');
        trValues.forEach(elt => elt.classList.remove('none'));
        if (Object.keys(dataBooks).length > 0) document.getElementById('no-data-row').classList.add('none');
    }
}

function reset() {
    // Vars
    dataMember = null;
    dataBooks = {};
    // Field
    document.getElementById('member').value = '';
    document.getElementById('firstname').value = '';
    document.getElementById('lastname').value = '';
    document.getElementById('member').value = '';
    document.getElementById('dialog-member-firstname').value = '';
    document.getElementById('dialog-member-lastname').value = '';
    document.getElementById('member').removeAttribute('aria-invalid');
    // Table
    document.querySelectorAll('.data-loaded').forEach(elt => elt.remove());
    document.querySelectorAll('.book-added').forEach(elt => elt.remove());
    document.getElementById('dialog-member-no-data-row').classList.remove('none');
    document.getElementById('no-data-row').classList.remove('none');
}