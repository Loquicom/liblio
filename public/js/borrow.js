/* --- Vars --- */

let dataMember = null;
let dataBooks = {};
let selectedBook = null;

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
    checkData();
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
    } else {
        member.setAttribute('aria-invalid', 'true');
        dataMember = null;
    }
    checkData();
}

/* --- Select books --- */

async function getBook() {
    // Get element
    const isbn = document.getElementById('isbn');
    // Reset
    isbn.removeAttribute('aria-invalid');
    document.getElementById('add-book').setAttribute('disabled', '');
    // Get ID
    const id = isbn.value;
    if (id.trim() === '') {
        return;
    }
    // Call API
    const result = await callGet('/api/books/' + id);
    if (result.success) {
        document.getElementById('add-book').removeAttribute('disabled');
        isbn.setAttribute('aria-invalid', 'false');
        selectedBook = result.data;
    } else {
        isbn.setAttribute('aria-invalid', 'true');
        selectedBook = null;
    }
}

function addBook() {
    if (selectedBook == null) {
        return;
    }
    if (dataBooks[selectedBook['isbn']]) {
        return;
    }
    document.getElementById('no-data-row').classList.add('none');

    // Add line in tab
    const delay = document.getElementById('delay').value;
    let html = '<tr class="book-added" data-isbn="'+ selectedBook['isbn'] +'">';
    html += '<td>' + selectedBook['isbn'] + '</td>';
    html += '<td>' + selectedBook['title'] + '</td>';
    html += '<td>' + selectedBook['author']['lib'] + '</td>';
    html += '<td>' + selectedBook['publisher']['lib'] + '</td>';
    html += '<td class="center">' + delay + '</td>';
    html += '<td class="center"><a class="secondary" href="#" data-tooltip="' + lang['del'] + '" data-placement="top" onclick="removeBook('+ selectedBook['isbn'] +')"><span class="iconify" data-icon="mdi-delete"></span></a></td>'
    html += '</tr>';
    document.getElementById('books-data').innerHTML += html;

    // Add line in data
    selectedBook['delay'] = delay;
    dataBooks[selectedBook['isbn']] = selectedBook;
    checkData();

    // Reset form
    document.getElementById('isbn').removeAttribute('aria-invalid');
    document.getElementById('isbn').value = '';
    document.getElementById('add-book').setAttribute('disabled', '');
    selectedBook = null;
}

function removeBook(isbn) {
    // Remove from data
    delete dataBooks[isbn];

    // Remove in table
    document.querySelector(`[data-isbn='${isbn}']`).remove();

    // If table is empty show line no data
    if (Object.keys(dataBooks).length === 0) {
        document.getElementById('no-data-row').classList.remove('none');
        checkData();
    }
}

/* --- Send data --- */

function checkData() {
    if (dataMember != null && Object.keys(dataBooks).length > 0) {
        document.getElementById('btn-valid').removeAttribute('disabled');
        return true;
    } else {
        document.getElementById('btn-valid').setAttribute('disabled', '');
        return false;
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
    for (const isbn in dataBooks) {
        params.books.push(dataBooks[isbn]);
    }
    // Call API
    const result = await callPost(`/api/members/${id}/borrow`, params);
    if (result.success) {
        // Message
        document.getElementsByClassName('alert-success')[0].innerHTML = lang.saveSuccess;
        document.getElementById('alert-success').classList.remove('none');
        // Reset
        reset();
        checkData();
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
    if (active) {
        btnValid.setAttribute('disabled', '');
        btnValid.setAttribute('aria-busy', 'true');
        searchMember.setAttribute('disabled', '');
        searchMember.setAttribute('aria-busy', 'true');
    } else {
        checkData();
        btnValid.setAttribute('aria-busy', 'false');
        searchMember.removeAttribute('disabled');
        searchMember.setAttribute('aria-busy', 'false');
    }
}

function reset() {
    // Vars
    dataMember = null;
    dataBooks = {};
    selectedBook = null;
    // Field
    document.getElementById('member').value = '';
    document.getElementById('firstname').value = '';
    document.getElementById('lastname').value = '';
    document.getElementById('member').value = '';
    document.getElementById('isbn').value = '';
    document.getElementById('delay').value = defaultDelay;
    document.getElementById('dialog-member-firstname').value = '';
    document.getElementById('dialog-member-lastname').value = '';
    document.getElementById('member').removeAttribute('aria-invalid');
    document.getElementById('isbn').removeAttribute('aria-invalid');
    // Table
    document.querySelectorAll('.data-loaded').forEach(elt => elt.remove());
    document.querySelectorAll('.book-added').forEach(elt => elt.remove());
    document.getElementById('dialog-member-no-data-row').classList.remove('none');
    document.getElementById('no-data-row').classList.remove('none');
}

/* --- Scanner --- */

function startScan(scanner, selectedDeviceId) {
    scanner.decodeFromVideoDevice(selectedDeviceId, 'video', (result, err) => {
        if (result) {
            console.log(result);
            document.getElementById('scan-result').value = result.text;
        }
        if (err && !(err instanceof ZXing.NotFoundException)) {
            console.error(err);
            document.getElementById('scan-message').textContent = err;
        }
    });
}

function initScan(scanner) {
    scanner.decodeFromVideoDevice(undefined, 'video', (result, err) => {
        // NO-OP
    });
    // Check when scanner is enabled
    const interval = setInterval(async () => {
        const videoInputDevices = await scanner.listVideoInputDevices();
        const selectedDeviceId = videoInputDevices[0].deviceId;
        // If devices is find scanner is enabled
        if (selectedDeviceId != null) {
            // Reset scanner and active button
            scanner.reset();
            document.getElementById('scan-btn').removeAttribute('disabled');
            clearInterval(interval);
            console.log('Scanner setup: Ok');
        }
    }, 1000);

}

function closeScan() {
    const value = document.getElementById('scan-result').value;
    if (value == null || value.trim() === '') {
        return;
    }
    document.getElementById('isbn').value = value;
    getBook();
}

/* --- On load --- */

window.addEventListener('load', function () {
    let selectedDeviceId;
    const scanner = new ZXing.BrowserMultiFormatReader();
    // List video inputs
    scanner.listVideoInputDevices().then((videoInputDevices) => {
        const sourceSelect = document.getElementById('video-source-select');
        selectedDeviceId = videoInputDevices[0].deviceId;
        // Enable button only if source is find
        if (selectedDeviceId == null) {
            initScan(scanner);
        } else {
            document.getElementById('scan-btn').removeAttribute('disabled');
        }
        if (videoInputDevices.length > 1) {
            videoInputDevices.forEach((element) => {
                const sourceOption = document.createElement('option');
                sourceOption.text = element.label;
                sourceOption.value = element.deviceId;
                sourceSelect.appendChild(sourceOption);
            });
            // Manage select change
            sourceSelect.onchange = () => {
                selectedDeviceId = sourceSelect.value;
                scanner.reset();
                startScan(scanner, selectedDeviceId);
            };
            // Show select
            document.getElementById('video-source').classList.remove('none');
        }

        // Defined start action
        document.getElementById('scan-btn').addEventListener('click', () => {
            startScan(scanner, selectedDeviceId);
        });

        document.querySelectorAll('.stop-scan').forEach(elt => {
            elt.addEventListener('click', () => {
                scanner.reset();
            });
        });
    }).catch((err) => {
        console.error(err);
    });
})