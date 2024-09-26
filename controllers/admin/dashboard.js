
// Constante para completar la ruta de la API.
const GRAFICO_API = 'services/admin/grafico.php';

// Método del evento para cuando el documento ha cargado.
document.addEventListener('DOMContentLoaded', () => {
    // Constante para obtener el número de horas.
    const HOUR = new Date().getHours();
    // Se define una variable para guardar un saludo.
    let greeting = '';
    // Dependiendo del número de horas transcurridas en el día, se asigna un saludo para el usuario.
    if (HOUR < 12) {
        greeting = 'Buenos días';
    } else if (HOUR < 19) {
        greeting = 'Buenas tardes';
    } else if (HOUR <= 23) {
        greeting = 'Buenas noches';
    }
    // Llamada a la función para mostrar el encabezado y pie del documento.
    loadTemplate();
    // Se establece el título del contenido principal.
    MAIN_TITLE.textContent = `${greeting}, bienvenido`;
    // Llamada a la funciones que generan los gráficos en la página web.
    graficoBarrasCategorias();
    graficoPastelCategorias();
    graficoCrecimientoClientes();
    graficoEstadoPedidos();
    graficoVentas();
});


/*
*   Función asíncrona para mostrar un gráfico de barras con la cantidad de productos por categoría.
*   Parámetros: ninguno.
*   Retorno: ninguno.
*/

const graficoBarrasCategorias = async () => {
    // Petición para obtener los datos del gráfico.
    const DATA = await fetchData(GRAFICO_API, 'cantidadProductosCategoria');
    // Se comprueba si la respuesta es satisfactoria, de lo contrario se remueve la etiqueta canvas.
    if (DATA.status) {
        // Se declaran los arreglos para guardar los datos a graficar.
        let categorias = [];
        let cantidades = [];
        // Se recorre el conjunto de registros fila por fila a través del objeto row.
        DATA.dataset.forEach(row => {
            // Se agregan los datos a los arreglos.
            categorias.push(row.nombre);
            cantidades.push(row.cantidad);
        });

        // Generar una paleta de colores dentro del mismo tono base (#245C9D)
        const palette = generatePalette('#1a4373', categorias.length);

        // Llamada a la función para generar y mostrar un gráfico de barras.
        new Chart(document.getElementById('chart1'), {
            type: 'bar',
            data: {
                labels: categorias,
                datasets: [{
                    label: 'Cantidad de productos',
                    data: cantidades,
                    backgroundColor: palette.backgroundColors,
                    borderColor: palette.borderColors,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                },
                legend: {
                    display: true,
                    position: 'top',
                    align: 'center'
                },
                title: {
                    display: true,
                    text: 'Cantidad de productos por categoría'
                }
            }
        });
    } else {
        document.getElementById('chart1').remove();
        console.log(DATA.error);
    }
}

const graficoPastelCategorias = async () => {
    // Petición para obtener los datos del gráfico.
    const DATA = await fetchData(GRAFICO_API, 'porcentajeProductosCategoria');
    // Se comprueba si la respuesta es satisfactoria, de lo contrario se remueve la etiqueta canvas.
    if (DATA.status) {
        // Se declaran los arreglos para guardar los datos a gráficar.
        let categorias = [];
        let porcentajes = [];
        // Se recorre el conjunto de registros fila por fila a través del objeto row.
        DATA.dataset.forEach(row => {
            // Se agregan los datos a los arreglos.
            categorias.push(row.nombre);
            porcentajes.push(row.porcentaje);
        });

        // Generar una paleta de colores para el gráfico de pastel basada en el color base (#8db5e5)
        const palette = generatePieColors('#1a4373', categorias.length);

        // Llamada a la función para generar y mostrar un gráfico de pastel.
        new Chart(document.getElementById('chart2'), {
            type: 'doughnut',
            data: {
                labels: categorias,
                datasets: [{
                    label: 'Porcentaje de productos por categoría',
                    data: porcentajes,
                    backgroundColor: palette,
                    borderColor: '#ffffff',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: {
                    display: true,
                    position: 'top',
                    align: 'center'
                },
                title: {
                    display: true,
                    text: 'Porcentaje de productos por categoría'
                },
                tooltips: {
                    callbacks: {
                        label: function (tooltipItem, data) {
                            var dataset = data.datasets[tooltipItem.datasetIndex];
                            var total = dataset.data.reduce(function (previousValue, currentValue, currentIndex, array) {
                                return previousValue + currentValue;
                            });
                            var currentValue = dataset.data[tooltipItem.index];
                            var percentage = Math.floor(((currentValue / total) * 100) + 0.5);
                            return percentage + "%";
                        }
                    }
                }
            }
        });
    } else {
        document.getElementById('chart2').remove();
        console.log(DATA.error);
    }
}


const graficoCrecimientoClientes = async () => {
    const DATA = await fetchData(GRAFICO_API, 'cantidadClientePorFecha');

    if (DATA.status) {
        let fechas = [];
        let cantidades = [];

        DATA.dataset.forEach(row => {
            fechas.push(row.fecha);
            cantidades.push(row.cantidad);
        });

        new Chart(document.getElementById('chart3'), {
            type: 'line',
            data: {
                labels: fechas,
                datasets: [{
                    label: 'Crecimiento de Clientes',
                    data: cantidades,
                    backgroundColor: '#1a4373',
                    fill: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        align: 'center'
                    },
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Fecha'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Cantidad'
                        }
                    }
                }
            }
        });
    } else {
        document.getElementById('chart3').remove();
        console.log(DATA.error);
    }
};

const graficoEstadoPedidos = async () => {
    const DATA = await fetchData(GRAFICO_API, 'graficoPedido');

    console.log(DATA.dataset);

    if (DATA.status) {
        let estados = [];
        let cantidades = [];

        DATA.dataset.forEach(row => {
            estados.push(row.estado_pedido);
            cantidades.push(row.cantidad);
        });

        new Chart(document.getElementById('chart4'), {
            type: 'doughnut',
            data: {
                labels: estados,
                datasets: [{
                    label: 'Estado de Pedidos',
                    data: cantidades,
                    backgroundColor: generatePieColors('#1a4373', estados.length),
                    borderColor: '#ffffff',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        align: 'center'
                    },
                }
            }
        });
    } else {
        document.getElementById('chart4').remove();
        console.log(DATA.error);
    }
};

const graficoVentas = async () => {
    const DATA = await fetchData(GRAFICO_API, 'graficoVenta');

    if (DATA.status) {
        let fechas = [];
        let cantidades = [];

        DATA.dataset.forEach(row => {
            fechas.push(row.fecha);
            cantidades.push(row.ventas);
        });

        new Chart(document.getElementById('chart5'), {
            type: 'line',
            data: {
                labels: fechas,
                datasets: [{
                    label: 'Cantidad de Ventas',
                    data: cantidades,
                    backgroundColor: '#1a4373',
                    fill: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        align: 'center'
                    },
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Fecha'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Cantidad de Ventas'
                        }
                    }
                }
            }
        });
    } else {
        document.getElementById('chart5').remove();
        console.log(DATA.error);
    }
};














//Diseño de los graficos


// Función para generar una paleta de colores para el gráfico de pastel a partir de un color base en formato hexadecimal.
function generatePieColors(baseColor, count) {
    // Convertir el color base a formato RGB
    const baseColorRgb = hexToRgb(baseColor);
    const palette = [];

    // Generar colores variando la saturación o luminosidad
    for (let i = 0; i < count; i++) {
        // Ajustar el color en función del índice para obtener variaciones
        const variationFactor = i / count;
        const color = adjustColor(baseColorRgb, variationFactor);
        palette.push(`rgba(${color.r}, ${color.g}, ${color.b}, 0.6)`); // Color con opacidad
    }

    return palette;
}

// Función para convertir un color hexadecimal a formato RGB.
function hexToRgb(hex) {
    const bigint = parseInt(hex.substring(1), 16);
    const r = (bigint >> 16) & 255;
    const g = (bigint >> 8) & 255;
    const b = bigint & 255;
    return { r, g, b };
}

// Función para ajustar un color RGB variando la saturación o luminosidad.
function adjustColor(color, factor) {
    // Ajustar luminosidad o saturación
    const hsl = rgbToHsl(color);
    hsl.l += factor * (1 - hsl.l); // Ajuste de luminosidad
    const rgb = hslToRgb(hsl);
    return rgb;
}

// Función para convertir un color RGB a formato HSL.
function rgbToHsl(color) {
    let r = color.r / 255, g = color.g / 255, b = color.b / 255;
    const max = Math.max(r, g, b), min = Math.min(r, g, b);
    let h, s, l = (max + min) / 2;

    if (max === min) {
        h = s = 0; // achromatic
    } else {
        const d = max - min;
        s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
        switch (max) {
            case r: h = (g - b) / d + (g < b ? 6 : 0); break;
            case g: h = (b - r) / d + 2; break;
            case b: h = (r - g) / d + 4; break;
        }
        h /= 6;
    }

    return { h, s, l };
}

// Función para convertir un color HSL a formato RGB.
function hslToRgb(hsl) {
    let r, g, b;
    const h = hsl.h, s = hsl.s, l = hsl.l;

    if (s === 0) {
        r = g = b = l; // achromatic
    } else {
        const hue2rgb = (p, q, t) => {
            if (t < 0) t += 1;
            if (t > 1) t -= 1;
            if (t < 1 / 6) return p + (q - p) * 6 * t;
            if (t < 1 / 2) return q;
            if (t < 2 / 3) return p + (q - p) * (2 / 3 - t) * 6;
            return p;
        };

        const q = l < 0.5 ? l * (1 + s) : l + s - l * s;
        const p = 2 * l - q;
        r = hue2rgb(p, q, h + 1 / 3);
        g = hue2rgb(p, q, h);
        b = hue2rgb(p, q, h - 1 / 3);
    }

    return { r: Math.round(r * 255), g: Math.round(g * 255), b: Math.round(b * 255) };
}


// Función para generar una paleta de colores a partir de un color base en formato hexadecimal.
function generatePalette(baseColor, count) {
    // Convertir el color base a formato RGB
    const baseColorRgb = hexToRgb(baseColor);
    const palette = {
        backgroundColors: [],
        borderColors: []
    };

    // Generar colores variando la saturación o luminosidad
    for (let i = 0; i < count; i++) {
        // Ajustar el color en función del índice para obtener variaciones
        const variationFactor = i / count;
        const color = adjustColor(baseColorRgb, variationFactor);
        palette.backgroundColors.push(`rgba(${color.r}, ${color.g}, ${color.b}, 0.6)`); // Fondo con opacidad
        palette.borderColors.push(`rgb(${color.r}, ${color.g}, ${color.b})`); // Borde sólido
    }

    return palette;
}

// Función para convertir un color hexadecimal a formato RGB.
function hexToRgb(hex) {
    const bigint = parseInt(hex.substring(1), 16);
    const r = (bigint >> 16) & 255;
    const g = (bigint >> 8) & 255;
    const b = bigint & 255;
    return { r, g, b };
}

// Función para ajustar un color RGB variando la saturación o luminosidad.
function adjustColor(color, factor) {
    // Ajustar luminosidad o saturación
    const hsl = rgbToHsl(color);
    hsl.l += factor * (1 - hsl.l); // Ajuste de luminosidad
    const rgb = hslToRgb(hsl);
    return rgb;
}

// Función para convertir un color RGB a formato HSL.
function rgbToHsl(color) {
    let r = color.r / 255, g = color.g / 255, b = color.b / 255;
    const max = Math.max(r, g, b), min = Math.min(r, g, b);
    let h, s, l = (max + min) / 2;

    if (max === min) {
        h = s = 0; // achromatic
    } else {
        const d = max - min;
        s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
        switch (max) {
            case r: h = (g - b) / d + (g < b ? 6 : 0); break;
            case g: h = (b - r) / d + 2; break;
            case b: h = (r - g) / d + 4; break;
        }
        h /= 6;
    }

    return { h, s, l };
}

// Función para convertir un color HSL a formato RGB.
function hslToRgb(hsl) {
    let r, g, b;
    const h = hsl.h, s = hsl.s, l = hsl.l;

    if (s === 0) {
        r = g = b = l; // achromatic
    } else {
        const hue2rgb = (p, q, t) => {
            if (t < 0) t += 1;
            if (t > 1) t -= 1;
            if (t < 1 / 6) return p + (q - p) * 6 * t;
            if (t < 1 / 2) return q;
            if (t < 2 / 3) return p + (q - p) * (2 / 3 - t) * 6;
            return p;
        };

        const q = l < 0.5 ? l * (1 + s) : l + s - l * s;
        const p = 2 * l - q;
        r = hue2rgb(p, q, h + 1 / 3);
        g = hue2rgb(p, q, h);
        b = hue2rgb(p, q, h - 1 / 3);
    }

    return { r: Math.round(r * 255), g: Math.round(g * 255), b: Math.round(b * 255) };
}

