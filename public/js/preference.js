function getColorPicker(color) {
    return document.querySelector('#color-picker button[data-color=' + color + ']');
}

function getColorPicked(color) {
    return document.querySelector('#color-picked div[data-color=' + color + ']');
}

function getCurrentColor() {
    const colorPicker = document.querySelector('#color-picker button.picked');
    return {
        color: colorPicker.getAttribute('data-color'),
        picker: colorPicker,
        picked: document.querySelector('#color-picked div.picked')
    };
}

function manageAnimation() {
    const animation = localStorage.getItem('animation') === 'true';
    if (animation) {
        document.getElementById('anim-switch').removeAttribute('checked');
        document.getElementById('anim-enabled').classList.add('none');
        document.getElementById('anim-disabled').classList.remove('none');
        disabledAnimation();
    } else {
        document.getElementById('anim-switch').setAttribute('checked', '');
        document.getElementById('anim-disabled').classList.add('none');
        document.getElementById('anim-enabled').classList.remove('none');
        enabledAnimation();
    }
    localStorage.setItem('animation', '' + !animation);
}

/**
 * Main function
 */
(function() {

    /* --- Default value --- */

    if (!localStorage.getItem('color')) {
        localStorage.setItem('color', 'amber');
    }

    /* --- Load color --- */

    getColorPicker(localStorage.getItem('color')).classList.add('picked');
    getColorPicked(localStorage.getItem('color')).classList.add('picked');
    document.querySelectorAll('.link').forEach((elt) => elt.setAttribute('data-color', localStorage.getItem('color')));
    document.querySelector('#current-color').innerHTML = localStorage.getItem('color');

    /* --- Load animation --- */

    const animation = localStorage.getItem('animation') === 'true';
    if (animation) {
        document.getElementById('anim-switch').setAttribute('checked', '');
        document.getElementById('anim-enabled').classList.remove('none');
    } else {
        document.getElementById('anim-switch').removeAttribute('checked');
        document.getElementById('anim-disabled').classList.remove('none');
    }


    /* --- Color Interaction --- */

    const colorPicker = document.querySelectorAll('#color-picker button');

    for (const elt of colorPicker) {
        // Hover animation
        const color = elt.getAttribute('data-color');
        elt.addEventListener('mouseenter', function () {
            if (!elt.classList.contains('picked')) {
                getColorPicked(color)?.classList.add('hover');
            }
        });
        elt.addEventListener('mouseleave', function() {
            getColorPicked(color)?.classList.remove('hover');
        });
        // Change color
        elt.addEventListener('click', function() {
            // Read current color information
            const data = getCurrentColor();
            // If color change
            if (data.color !== color) {
                // Change the picked one
                getColorPicked(color)?.classList.add('picked');
                getColorPicker(color)?.classList.add('picked');
                data.picked?.classList.remove('picked');
                data.picker?.classList.remove('picked');
                // Change css color
                document.querySelector('#picocss').setAttribute('href', `css/picocss/pico.${color}.css`);
                document.querySelector('#current-color').innerHTML = color;
                // Change the color on the link
                document.querySelectorAll('.link').forEach((elt) => elt.setAttribute('data-color', color));
                // Set the new color in localStorage
                localStorage.setItem('color', color);
            }
        });
    }

})();