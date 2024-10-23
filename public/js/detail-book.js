
async function save() {
    document.getElementById('success-text').classList.add('none');
    document.getElementById('error-text').classList.add('none');
    // Get fields
    const description = document.getElementById('description');
    const comment = document.getElementById('comment');
    // Set parameters
    const params = {
        description: description.value,
        comment: comment.value
    };
    // Call API
    const result = await callPut('/api/books/' + id, params);
    if (result.success) {
        document.getElementById('success-text').classList.remove('none');
    } else {
        if (result.message) {
            document.getElementById('error-text').innerHTML = result.message;
        } else {
            document.getElementById('error-text').innerHTML = lang.errorAjax;
        }
        document.getElementById('error-text').classList.remove('none');
    }
}

async function addAuthor() {
    const selected = document.getElementById('author');
    const role = document.getElementById('author-role');
    // Check data
    if (selected.value.trim() === '') {
        return;
    }
    // Call API
    const result = await callPost('/api/books/' + id + '/authors/' + selected.value, {role: role.value});
    if (!result.success) {
        console.error(result);
        return;
    }
    // Add in table
    const name = document.querySelector("option[value='" + selected.value + "']").innerText;
    let html = '<tr data-author="' + selected.value + '">';
    html += '<td>' + name + '</td>';
    html += '<td>' + role.value + '</td>';
    html += '<td class="center"><input type="checkbox" disabled></td>';
    html += '<td class="center">';
    html += '<a class="secondary" href="' + authorReturnURL + selected.value + '?return=manage/books" data-tooltip="' + lang.detail + '" data-placement="top"><span class="iconify" data-icon="mdi-eye"></span></a>';
    html += '<a class="secondary" href="#" data-tooltip="' + lang.del + '" data-placement="top" onclick="deleteAuthor(' + selected.value + ', \'' + name + '\')"><span class="iconify" data-icon="mdi-delete"></span></a>'
    html += '</td>';
    html += '</tr>';
    const tableAuthorContent = document.getElementById('table-author-content');
    tableAuthorContent.innerHTML = tableAuthorContent.innerHTML + html;
    // Remove option
    document.getElementById('author-' + selected.value)?.remove();
    // Reset
    selected.value = '';
    role.value = '';
}

async function deleteAuthor(authorId, authorName) {
    // Call API
    const result = await callDelete('/api/books/' + id + '/authors/' + authorId);
    if (!result.success) {
        console.error(result);
        return;
    }
    // Remove row
    document.querySelector(`tr[data-author='${authorId}']`).remove();
    // Add option
    let html = '<option id="author-' + authorId + '" value="' + authorId + '">' + authorName + '</option>';
    const select = document.getElementById('author');
    select.innerHTML = select.innerHTML + html;
}