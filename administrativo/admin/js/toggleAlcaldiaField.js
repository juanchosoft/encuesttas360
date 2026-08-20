function toggleAlcaldiaField() {
    const tipoUsuarioSelect = document.getElementById('tipo');
    const alcaldiaContainer = document.getElementById('alcaldia-container');
    const alcaldiaSelect = document.getElementById('tbl_municipio_id'); // obtenemos el select


    if (tipoUsuarioSelect.value === 'Secretario_Despacho') {
        alcaldiaContainer.style.display = 'none';
        alcaldiaSelect.disabled = true; // Deshabilita el select

    } else {
        alcaldiaContainer.style.display = 'block';
        alcaldiaSelect.disabled = false; // Habilita el select

    }
}

// Ejecutar la función al cargar la página para establecer el estado inicial
document.addEventListener('DOMContentLoaded', function() {
    toggleAlcaldiaField();
});