import api from "../commons/api-client.js";

document.querySelectorAll(".js-delete-role").forEach((btn) => {
    btn.addEventListener("click", async () => {
        if (!confirm("¿Eliminar este rol?")) return;

        btn.disabled = true;

        try {
            await api.delete(btn.dataset.url);
            btn.closest("tr").remove();
        } catch (error) {
            btn.disabled = false;
            alert(error.data?.message ?? "No se pudo eliminar el rol.");
        }
    });
});
