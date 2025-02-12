// Función para mostrar el modal para agregar un nuevo residente
function showModalAdd() {
    document.getElementById("modal-title").textContent = "Agregar Residente";
    document.getElementById("residente-form").reset(); // Limpiar el formulario
    document.getElementById("residente-id").value = ""; // Limpiar el ID oculto
    showModal();
}

/// Función para editar un residente
function editResidente(id) {
    // Mostrar el modal de edición
    showModal();

    // Realizar solicitud AJAX para obtener los datos del residente por su ID
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "obtener_residente.php?id=" + id, true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                var residente = JSON.parse(xhr.responseText);
                // Llenar los campos del formulario con los datos del residente
                document.getElementById("nombre").value = residente.nombre;
                document.getElementById("unidad").value = residente.unidad;
                document.getElementById("telefono").value = residente.telefono;
                document.getElementById("residente-id").value = residente.id; // Guardar el ID del residente
                document.getElementById("modal-title").textContent = "Editar Residente";
            } else {
                console.error("Error en la solicitud: " + xhr.status);
            }
        }
    };
    xhr.send();
}


function deleteResidente(id) {
    if (confirm('¿Estás seguro de querer eliminar este residente?')) {
        // Realizar una solicitud AJAX para eliminar el residente
        let xhr = new XMLHttpRequest();
        xhr.open('POST', 'eliminar_residente.php', true);
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                // Recargar la página o actualizar la lista de residentes después de eliminar
                location.reload(); // Esto recarga la página
                // Puedes implementar una actualización parcial de la lista si prefieres
            }
        };
        xhr.send('id=' + id);
    }
}



function guardarResidente() {
    var formData = new FormData(document.getElementById("residente-form"));
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "guardar.php", true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            // Manejar la respuesta del servidor aquí según sea necesario
            // Por ejemplo, recargar la tabla de residentes
            location.reload(); // Recarga la página para actualizar la tabla
        }
    };
    xhr.send(formData);
    hideModal();
}


// Función para mostrar el modal
function showModal() {
    var modal = document.getElementById("modal");
    modal.style.display = "block";
}

// Función para ocultar el modal
function hideModal() {
    var modal = document.getElementById("modal");
    modal.style.display = "none";
}

// Función para cancelar la edición y cerrar el modal

function showSuccessAlert() {
    alert("¡La operación se realizó correctamente!");
}

// Llamar a la función de alerta en la carga de la página
window.onload = function() {
    showSuccessAlert();
};
