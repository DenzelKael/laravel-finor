import BaseForm from "../commons/base-form.js";

new BaseForm(document.getElementById("create-form"));

document.querySelectorAll(".module-toggle").forEach((toggle) => {
    toggle.addEventListener("change", () => {
        const module = toggle.dataset.module;

        document
            .querySelectorAll(`.permission-checkbox[data-module="${module}"]`)
            .forEach((permission) => {
                permission.checked = toggle.checked;
            });
    });
});
