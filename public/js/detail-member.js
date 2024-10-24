
async function save() {
    document.getElementById('success-text').classList.add('none');
    document.getElementById('error-text').classList.add('none');
    // Get fields
    const comment = document.getElementById('comment');
    // Set parameters
    const params = {
        comment: comment.value
    };
    // Call API
    const result = await callPut('/api/members/' + id, params);
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
