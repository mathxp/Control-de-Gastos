/**
 * SISTEMA DE CONTROL DE GASTOS - VISUALIZACIÓN DE DATOS
 * Este script utiliza la librería Chart.js para renderizar el gráfico de pastel.
 */

// 1. Obtener la referencia al elemento <canvas> del HTML mediante su ID
const ctx = document.getElementById('graficoGastos');

/**
 * 2. VALIDACIÓN DE EXISTENCIA
 * Verificamos si 'ctx' existe antes de intentar crear el gráfico.
 * Esto evita errores de JavaScript en páginas donde el gráfico no se muestra (como el Login).
 */
if (ctx) {
    // 3. Inicialización de un nuevo gráfico de Chart.js
    new Chart(ctx, {
        type: 'pie', // Tipo de gráfico: 'pie' (circular/pastel)
        
        data: {
            /**
             * 4. ESTRUCTURA DE DATOS
             * 'labels' y 'valores' deben venir definidos desde el index.php
             * usualmente inyectados mediante JSON desde el backend.
             */
            labels: labels, // Arreglo con nombres de categorías (Ej: ['Comida', 'Renta'])
            datasets: [{
                data: valores, // Arreglo con montos numéricos (Ej: [150.00, 800.00])
                // Aquí podrías añadir 'backgroundColor' para personalizar los colores
            }]
        },

        options: {
            // 5. RESPONSIVIDAD
            // Permite que el gráfico se adapte al tamaño de la pantalla (móvil/PC)
            responsive: true,
            
            // 6. ASPECT RATIO
            // 'false' permite que el gráfico llene el contenedor padre según el CSS definido
            maintainAspectRatio: false,

            // Puedes añadir plugins aquí, como leyendas o tooltips
            plugins: {
                legend: {
                    position: 'bottom' // Ubica los nombres de categorías abajo
                }
            }
        }
    });
}