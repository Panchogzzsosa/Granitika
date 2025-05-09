// Funciones para el manejo de compras
document.addEventListener('DOMContentLoaded', function() {
    // Agregar producto a la compra
    document.getElementById('agregarProducto').addEventListener('click', agregarProductoCompra);
    
    // Delegación de eventos para los botones de eliminar producto
    document.getElementById('productosContainer').addEventListener('click', function(e) {
        if (e.target.closest('.eliminar-producto')) {
            const row = e.target.closest('.row');
            if (document.querySelectorAll('#productosContainer .row').length > 1) {
                row.remove();
                calcularTotal();
            } else {
                alert('Debe haber al menos un producto en la compra');
            }
        }
    });
    
    // Delegación de eventos para los cambios en productos, cantidades y precios
    document.getElementById('productosContainer').addEventListener('change', function(e) {
        if (e.target.classList.contains('producto-select') || 
            e.target.classList.contains('cantidad-input') || 
            e.target.classList.contains('precio-input')) {
            calcularSubtotal(e.target.closest('.row'));
            calcularTotal();
        }
    });
});

function agregarProductoCompra() {
    const container = document.getElementById('productosContainer');
    const template = container.querySelector('.row').cloneNode(true);
    
    // Limpiar valores
    template.querySelector('.producto-select').value = '';
    template.querySelector('.cantidad-input').value = '';
    template.querySelector('.precio-input').value = '';
    template.querySelector('.subtotal').value = '';
    
    container.appendChild(template);
}

function calcularSubtotal(row) {
    const cantidad = row.querySelector('.cantidad-input');
    const precio = row.querySelector('.precio-input');
    const subtotal = row.querySelector('.subtotal');
    
    if (cantidad.value && precio.value) {
        const total = cantidad.value * precio.value;
        subtotal.value = '$' + total.toFixed(2);
    } else {
        subtotal.value = '';
    }
}

function calcularTotal() {
    const subtotales = document.querySelectorAll('.subtotal');
    let total = 0;
    
    subtotales.forEach(subtotal => {
        const valor = parseFloat(subtotal.value.replace('$', '')) || 0;
        total += valor;
    });
    
    document.getElementById('totalCompra').value = '$' + total.toFixed(2);
}

function guardarCompra() {
    const form = document.getElementById('nuevaCompraForm');
    const formData = new FormData(form);
    
    // Agregar el total al formData
    formData.append('total', document.getElementById('totalCompra').value.replace('$', ''));
    
    fetch('api/guardar_compra.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Compra guardada exitosamente');
            location.reload();
        } else {
            alert('Error al guardar la compra: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al guardar la compra');
    });
}

function verDetalles(id) {
    fetch(`api/obtener_compra.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            // Implementar la visualización de detalles
            alert('Detalles de la compra: ' + JSON.stringify(data));
        });
}

function editarCompra(id) {
    fetch(`api/obtener_compra.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            const form = document.getElementById('nuevaCompraForm');
            form.proveedor.value = data.proveedor;
            form.fecha_entrega.value = data.fecha_entrega;
            form.notas.value = data.notas;
            
            // Cambiar el título del modal y el botón
            document.querySelector('#nuevaCompraModal .modal-title').textContent = 'Editar Compra';
            document.querySelector('#nuevaCompraModal .btn-primary').textContent = 'Actualizar';
            
            // Agregar el ID al formulario
            form.dataset.id = id;
            
            // Mostrar el modal
            new bootstrap.Modal(document.getElementById('nuevaCompraModal')).show();
        });
}

function eliminarCompra(id) {
    if (confirm('¿Está seguro de eliminar esta compra?')) {
        fetch(`api/eliminar_compra.php?id=${id}`, {
            method: 'DELETE'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Compra eliminada exitosamente');
                location.reload();
            } else {
                alert('Error al eliminar la compra: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al eliminar la compra');
        });
    }
}

// Filtros
document.getElementById('filtroEstado').addEventListener('change', filtrarCompras);
document.getElementById('busqueda').addEventListener('input', filtrarCompras);

function filtrarCompras() {
    const estado = document.getElementById('filtroEstado').value;
    const busqueda = document.getElementById('busqueda').value.toLowerCase();
    
    const filas = document.querySelectorAll('tbody tr');
    
    filas.forEach(fila => {
        const proveedor = fila.children[1].textContent.toLowerCase();
        const estadoCompra = fila.children[5].textContent.toLowerCase();
        
        const cumpleFiltros = 
            (estado === '' || estadoCompra === estado) &&
            (busqueda === '' || proveedor.includes(busqueda));
        
        fila.style.display = cumpleFiltros ? '' : 'none';
    });
} 