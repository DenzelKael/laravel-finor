import BaseForm from '../commons/base-form.js';

class PlanForm extends BaseForm {
    // Por ahora no necesita sobrescribir nada: BaseForm ya cubre
    // crear, editar, validación y el flash message.
}

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('plan-form');
    if (form) new PlanForm(form);
});