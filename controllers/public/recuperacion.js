document.addEventListener('DOMContentLoaded', async () => {
    // Llamada a la función para mostrar el encabezado y pie del documento.
    loadTemplate();
    // Se establece el título del contenido principal.
    MAIN_TITLE.textContent = 'Recupera contraseña';

    // Manejar el evento de mostrar el modal
    const passwordModal = new bootstrap.Modal(document.getElementById('passwordModal'));
    document.getElementById('passwordModal').addEventListener('show.bs.modal', function () {
        // Limpiar campos del formulario de cambio de contraseña al mostrar el modal
        document.getElementById('passwordChangeForm').reset();
    });

    // Manejar el evento de ocultar el modal
    document.getElementById('passwordModal').addEventListener('hide.bs.modal', function () {
        // Limpiar campos del formulario de cambio de contraseña al ocultar el modal
        document.getElementById('passwordChangeForm').reset();
    });
});

document.getElementById('recoveryForm').addEventListener('submit', function (event) {
    event.preventDefault(); 

    let formData = new FormData(this);

    fetch('../../api/helpers/recuperacion.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status) {
            swal('Éxito', data.message, 'success');
        } else {
            swal('Error', data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        swal('Error', 'Hubo un problema al procesar la solicitud.', 'error');
    });
});

document.getElementById('passwordChangeForm').addEventListener('submit', function (event) {
    event.preventDefault();

    let formData = new FormData(this);

    fetch('../../api/helpers/recuperacion.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status) {
            swal('Éxito', data.message, 'success').then(() => {
                window.location.href = '../../views/public/login.html'; 
            });
        } else {
            swal('Error', data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        swal('Error', 'Hubo un problema al procesar la solicitud.', 'error');
    });
});
