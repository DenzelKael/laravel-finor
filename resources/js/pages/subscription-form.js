import BaseForm from '../commons/base-form.js';

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('subscription-form');

    if (form) {
        new BaseForm(form);
    }
});