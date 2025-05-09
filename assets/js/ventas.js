// Funciones para el manejo de ventas
document.addEventListener('DOMContentLoaded', function() {
    // Agregar producto a la venta
    document.getElementById('agregarProducto').addEventListener('click', agregarProductoVenta);
    
    // Delegación de eventos para los botones de eliminar producto
    document.getElementById('productosContainer').addEventListener('click', function(e) {
        if (e.target.closest('.eliminar-producto')) {
            const row = e.target.closest('.row');
            if (document.querySelectorAll('#productosContainer .row').length > 1) {
                row.remove();
                calcularTotal();
            } else {
                alert('Debe haber al menos un producto en la venta');
            }
        }
    });
    
    // Delegación de eventos para los cambios en productos y cantidades
    document.getElementById('productosContainer').addEventListener('change', function(e) {
        if (e.target.classList.contains('producto-select') || e.target.classList.contains('cantidad-input')) {
            calcularSubtotal(e.target.closest('.row'));
            calcularTotal();
        }
    });
});

function agregarProductoVenta() {
    const container = document.getElementById('productosContainer');
    const template = container.querySelector('.row').cloneNode(true);
    
    // Limpiar valores
    template.querySelector('.producto-select').value = '';
    template.querySelector('.cantidad-input').value = '';
    template.querySelector('.subtotal').value = '';
    
    container.appendChild(template);
}

function calcularSubtotal(row) {
    const select = row.querySelector('.producto-select');
    const cantidad = row.querySelector('.cantidad-input');
    const subtotal = row.querySelector('.subtotal');
    
    if (select.value && cantidad.value) {
        const precio = select.options[select.selectedIndex].dataset.precio;
        const total = precio * cantidad.value;
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
    
    document.getElementById('totalVenta').value = '$' + total.toFixed(2);
}

function guardarVenta() {
    const form = document.getElementById('nuevaVentaForm');
    const formData = new FormData(form);
    
    // Agregar el total al formData
    formData.append('total', document.getElementById('totalVenta').value.replace('$', ''));
    
    fetch('api/guardar_venta.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Venta guardada exitosamente');
            location.reload();
        } else {
            alert('Error al guardar la venta: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al guardar la venta');
    });
}

function verDetalles(id) {
    fetch(`api/obtener_venta.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            // Implementar la visualización de detalles
            alert('Detalles de la venta: ' + JSON.stringify(data));
        });
}

function editarVenta(id) {
    fetch(`api/obtener_venta.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            const form = document.getElementById('nuevaVentaForm');
            form.cliente.value = data.cliente;
            form.tipo_material.value = data.tipo_material;
            form.notas.value = data.notas;
            
            // Cambiar el título del modal y el botón
            document.querySelector('#nuevaVentaModal .modal-title').textContent = 'Editar Venta';
            document.querySelector('#nuevaVentaModal .btn-primary').textContent = 'Actualizar';
            
            // Agregar el ID al formulario
            form.dataset.id = id;
            
            // Mostrar el modal
            new bootstrap.Modal(document.getElementById('nuevaVentaModal')).show();
        });
}

function eliminarVenta(id) {
    if (confirm('¿Está seguro de eliminar esta venta?')) {
        fetch(`api/eliminar_venta.php?id=${id}`, {
            method: 'DELETE'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Venta eliminada exitosamente');
                location.reload();
            } else {
                alert('Error al eliminar la venta: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al eliminar la venta');
        });
    }
}

// Filtros
document.getElementById('filtroTipo').addEventListener('change', filtrarVentas);
document.getElementById('filtroEstado').addEventListener('change', filtrarVentas);
document.getElementById('busqueda').addEventListener('input', filtrarVentas);

function filtrarVentas() {
    const tipo = document.getElementById('filtroTipo').value;
    const estado = document.getElementById('filtroEstado').value;
    const busqueda = document.getElementById('busqueda').value.toLowerCase();
    
    const filas = document.querySelectorAll('tbody tr');
    
    filas.forEach(fila => {
        const cliente = fila.children[1].textContent.toLowerCase();
        const tipoMaterial = fila.children[4].textContent.toLowerCase();
        const estadoVenta = fila.children[5].textContent.toLowerCase();
        
        const cumpleFiltros = 
            (tipo === '' || tipoMaterial === tipo) &&
            (estado === '' || estadoVenta === estado) &&
            (busqueda === '' || cliente.includes(busqueda));
        
        fila.style.display = cumpleFiltros ? '' : 'none';
    });
} 