// Funciones para el manejo del inventario
function guardarProducto() {
    const form = document.getElementById('nuevoProductoForm');
    const formData = new FormData(form);
    
    // Agregar el ID si estamos editando
    if (form.dataset.id) {
        formData.append('id', form.dataset.id);
    }
    
    // Mostrar indicador de carga
    const submitButton = document.querySelector('#nuevoProductoModal .btn-primary');
    const originalText = submitButton.innerHTML;
    submitButton.disabled = true;
    submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Guardando...';
    
    fetch('api/guardar_producto.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            mostrarToast('Producto guardado exitosamente', 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            mostrarToast('Error al guardar el producto: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarToast('Error de red al guardar el producto', 'error');
    })
    .finally(() => {
        // Restaurar el botón
        submitButton.disabled = false;
        submitButton.innerHTML = originalText;
    });
}

function editarProducto(id) {
    fetch(`api/obtener_producto.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            const form = document.getElementById('nuevoProductoForm');
            form.nombre.value = data.nombre;
            form.tipo.value = data.tipo;
            form.cantidad.value = data.cantidad;
            form.unidad_medida.value = data.unidad_medida;
            form.precio_unitario.value = data.precio_unitario;
            
            // Cambiar el título del modal y el botón
            document.querySelector('#nuevoProductoModal .modal-title').textContent = 'Editar Producto';
            document.querySelector('#nuevoProductoModal .btn-primary').textContent = 'Actualizar';
            
            // Agregar el ID al formulario
            form.dataset.id = id;
            
            // Mostrar el modal
            new bootstrap.Modal(document.getElementById('nuevoProductoModal')).show();
        });
}

function eliminarProducto(id) {
    if (confirm('¿Estás seguro de que deseas eliminar este producto?')) {
        fetch('api/eliminar_producto.php', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'id=' + encodeURIComponent(id)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Producto eliminado correctamente');
                location.reload();
            } else {
                alert('Error al eliminar el producto: ' + data.message);
            }
        })
        .catch(error => {
            alert('Error de red al eliminar el producto');
        });
    }
}

// Filtros
document.getElementById('filtroTipo').addEventListener('change', filtrarProductos);
document.getElementById('filtroEstado').addEventListener('change', filtrarProductos);
document.getElementById('busqueda').addEventListener('input', filtrarProductos);

function filtrarProductos() {
    const tipo = document.getElementById('filtroTipo').value;
    const estado = document.getElementById('filtroEstado').value;
    const busqueda = document.getElementById('busqueda').value.toLowerCase();
    
    const filas = document.querySelectorAll('tbody tr');
    
    filas.forEach(fila => {
        const nombre = fila.children[1].textContent.toLowerCase();
        const tipoProducto = fila.children[2].textContent.toLowerCase();
        const estadoProducto = fila.children[6].textContent.toLowerCase();
        
        const cumpleFiltros = 
            (tipo === '' || tipoProducto === tipo) &&
            (estado === '' || estadoProducto === estado) &&
            (busqueda === '' || nombre.includes(busqueda));
        
        fila.style.display = cumpleFiltros ? '' : 'none';
    });
} 