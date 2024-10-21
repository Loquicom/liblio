/* --- Function --- */

function dialogSupported() {
    return typeof HTMLDialogElement === 'function';
}

function refreshDialog() {
    const animDuration = 400;
    document.querySelectorAll('.dialog-open').forEach(elt => {
        // Pass already init dialog
        if (elt.classList.contains('dialog-init')) return;
        // Init dialog
        const idDialog = elt.getAttribute('data-dialog');
        elt.addEventListener('click', () => {
            document.querySelectorAll(`#${idDialog} .dialog-close`).forEach(elt => {
                elt.addEventListener('click', () => {
                    document.querySelector('html').classList.add('modal-is-closing');
                    setTimeout(() => {
                        document.querySelector('html').classList.remove('modal-is-open', 'modal-is-closing');
                        document.getElementById(idDialog).close();
                    }, animDuration);
                });
            })
            document.querySelector('html').classList.add('modal-is-open', 'modal-is-opening');
            document.getElementById(idDialog).showModal();
            setTimeout(() => {
                document.querySelector('html').classList.remove('modal-is-opening');
            }, animDuration);
        });
        elt.classList.add('dialog-init');
    });
}

function enabledAnimation() {
    document.querySelectorAll('[data-tilt]').forEach((elt) => {
        VanillaTilt.init(elt);
    });
}

function disabledAnimation() {
    document.querySelectorAll('[data-tilt]').forEach((elt) => {
        elt.vanillaTilt?.destroy();
    });
}

/* --- Ajax --- */

async function callGet(uri, parameters) {
    const result = await call('get', uri, parameters);
    return result.respond;
}

async function callPost(uri, parameters) {
    const result = await call('post', uri, parameters);
    return result.respond;
}

async function callPut(uri, parameters) {
    const result = await call('put', uri, parameters);
    return result.respond;
}

async function callDelete(uri, parameters) {
    const result = await call('delete', uri, parameters);
    return result.respond;
}

async function call(method, uri, parameters) {
    // Check if JWT is stored
    let jwt = null;
    if (checkJwt()) {
        jwt = localStorage.getItem('jwt');
    }
    // Try the request
    let result = null;
    let success = false;
    try {
        result = await request(method, uri, parameters, jwt);
        success = true;
    } catch (err) {
        // Auth Error and no JWT provided, try to get one
        if (jwt === null && err.cause === 'HTTP Error' && err.error === 401) {
            try {
                result = await request(method, uri, parameters, await getJwt());
                success = true;
            } catch (e) {
                result = e;
            }
        } else {
            result = err;
        }
    }
    // Result
    return {
        success: success,
        respond: result
    }
}

async function getJwt() {
    const json = await request('get', 'api/login');
    localStorage.setItem('jwt', json.access_token);
    return json.access_token;
}

function checkJwt() {
    const jwt = localStorage.getItem('jwt');
    if (jwt) {
        const parts = jwt.split('.');
        const payload = JSON.parse(atob(parts[1]));
        const now = Math.trunc(new Date().getTime()/1000);
        const valid = payload.exp > now;
        if (!valid) {
            localStorage.removeItem('jwt');
        }
        return valid;
    }
    return false
}

function request(method, uri, parameters, token) {
    // Check URI
    uri = uri.startsWith('/') ? uri : '/' + uri;
    // Set options
    const opt = {
        method: method.toUpperCase(),
        headers: {
            "Content-type": "application/json;charset=UTF-8",
            "X-Requested-With": "XMLHttpRequest"
        }
    }
    if (token) {
        opt.headers['Authorization'] = 'Bearer ' + token;
    }
    // Adapt parameters to the method
    if (opt.method === "GET") {
        let params = '?';
        for (const key in parameters) {
            if (typeof parameters[key] === 'string') {
                params += key + '=' + parameters[key] + '&';
            } else {
                params += key + '=' + JSON.stringify(parameters[key]) + '&';
            }
        }
        uri += params.substring(0, params.length - 1);
    } else {
        opt.body = JSON.stringify(parameters);
    }
    // Make request
    return new Promise((resolve, reject) => {
        fetch(window.location.origin + uri, opt).then((response) => {
            if (response.status > 400) {
                reject({
                    cause: 'HTTP Error',
                    error: response.status
                });
            }
            return response.json();
        }).then((json) => {
            resolve(json);
        }).catch((error) => {
            if (error.name === 'SyntaxError') {
                reject({
                    cause: 'Invalid JSON',
                    error: error.message
                });
            } else {
                reject({
                    cause: error.name,
                    error: error.message
                });
            }
        });
    });
}

/* --- Main --- */

(function() {
    if (!dialogSupported()) {
        alert('Dialog not supported') // TODO page
    }

    // Animation
    if (!localStorage.getItem('animation')) {
        // Reduce animation
        const animation = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        localStorage.setItem('animation', '' + !animation);
    } else {
        const animation = localStorage.getItem('animation') === 'true';
        if (!animation) {
            disabledAnimation();
        }
    }

    // Dialog
    refreshDialog();
})();