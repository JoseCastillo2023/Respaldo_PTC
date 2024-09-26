// Constantes para completar la ruta de la API.
const PRODUCTO_API = 'services/public/producto.php';
const PEDIDO_API = 'services/public/pedido.php';
// Constante tipo objeto para obtener los parámetros disponibles en la URL.
const PARAMS = new URLSearchParams(location.search);
// Constante para establecer el formulario de agregar un producto al carrito de compras.
const SHOPPING_FORM = document.getElementById('shoppingForm');
// Constante para establecer el campo de cantidad del producto.
const CANTIDAD_INPUT = document.getElementById('cantidadProducto');
// Constante para establecer el campo de existencias del producto.
const EXISTENCIAS_INPUT = document.getElementById('existenciasProducto');
// Constante para establecer el formulario de agregar comentarios.
const ADD_COMMENT_FORM = document.getElementById('addCommentForm');
const COMMENT_ID_PRODUCTO_INPUT = document.getElementById('commentIdProducto');
const COMMENT_CALIFICACION_INPUT = document.getElementById('commentCalificacion');
const COMMENT_TEXT_INPUT = document.getElementById('commentText');

document.addEventListener('DOMContentLoaded', async () => {
    // Llamada a la función para mostrar el encabezado y pie del documento.
    loadTemplate();
    // Se establece el título del contenido principal.
    MAIN_TITLE.textContent = 'Detalles del producto';

    // Constante tipo objeto con los datos del producto seleccionado.
    const FORM = new FormData();
    FORM.append('idProducto', PARAMS.get('id'));

    // Petición para solicitar los datos del producto seleccionado.
    const DATA = await fetchData(PRODUCTO_API, 'readOne', FORM);

    // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
    if (DATA.status) {
        // Se colocan los datos del producto en la página web de acuerdo con el producto seleccionado previamente.
        document.getElementById('imagenProducto').src = SERVER_URL.concat('images/productos/', DATA.dataset.imagen_producto);
        document.getElementById('nombreProducto').textContent = DATA.dataset.nombre_producto;
        document.getElementById('descripcionProducto').textContent = DATA.dataset.descripcion_producto;
        document.getElementById('precioProducto').textContent = DATA.dataset.precio_producto;
        EXISTENCIAS_INPUT.textContent = DATA.dataset.existencias_producto;
        document.getElementById('idProducto').value = DATA.dataset.id_producto;
        COMMENT_ID_PRODUCTO_INPUT.value = DATA.dataset.id_producto; // Establecer el ID del producto en el formulario de comentarios

        // Mostrar la calificación promedio
        await displayAverageRating(DATA.dataset.id_producto);

        // Ahora solicitamos los comentarios del producto.
        await displayComments(DATA.dataset.id_producto);
    } else {
        // Se presenta un mensaje de error cuando no existen datos del producto.
        document.getElementById('mainTitle').textContent = DATA.error;
        document.getElementById('detalle').innerHTML = '';
    }
});
async function displayAverageRating(idProducto) {
    const FORM = new FormData();
    FORM.append('idProducto', idProducto);

    try {
        const AVG_RATING_DATA = await fetchData(PRODUCTO_API, 'averageRating', FORM);

        // Verifica si la respuesta es satisfactoria
        if (AVG_RATING_DATA.status) {
            const averageRatingContainer = document.getElementById('averageRatingStars');
            const averageRatingValue = document.getElementById('averageRatingValue');

            averageRatingContainer.innerHTML = ''; // Limpiar el contenedor de estrellas
            const averageRating = parseFloat(AVG_RATING_DATA.dataset.promedio);

            // Mostrar estrellas para la calificación promedio
            for (let i = 1; i <= 5; i++) {
                const star = document.createElement('span');
                star.textContent = '★';
                star.style.fontSize = '1.5rem'; // Tamaño de las estrellas
                star.style.color = (i <= averageRating) ? 'black' : 'lightgrey'; // Color según la calificación
                averageRatingContainer.appendChild(star);
            }

            averageRatingValue.textContent = `${averageRating.toFixed(1)} / 5`;
        } else {
            document.getElementById('averageRatingContainer').innerHTML = `<p>${AVG_RATING_DATA.error}</p>`;
        }
    } catch (error) {
        console.error('Error al obtener la calificación promedio:', error);
    }
}



// Método del evento para cuando se envía el formulario de agregar un producto al carrito.
SHOPPING_FORM.addEventListener('submit', async (event) => {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    // Constante tipo objeto con los datos del formulario.
    const FORM = new FormData(SHOPPING_FORM);
    // Obtiene el valor de la cantidad ingresada.
    const CANTIDAD = parseInt(CANTIDAD_INPUT.value);
    // Obtiene el valor de las existencias del producto.
    const EXISTENCIAS = parseInt(EXISTENCIAS_INPUT.textContent);
    // Verifica si la cantidad supera las existencias.
    if (CANTIDAD > EXISTENCIAS) {
        sweetAlert(2, 'La cantidad a comprar no puede ser mayor que las existencias', false);
        return;
    }
    // Petición para guardar los datos del formulario.
    const DATA = await fetchData(PEDIDO_API, 'createDetail', FORM);
    // Se comprueba si la respuesta es satisfactoria, de lo contrario se constata si el cliente ha iniciado sesión.
    if (DATA.status) {
        sweetAlert(1, DATA.message, false, 'carrito.html');
    } else if (DATA.session) {
        sweetAlert(2, DATA.error, false);
    } else {
        sweetAlert(3, DATA.error, true, 'login.html');
    }
});


// Método del evento para cuando se envía el formulario de agregar un comentario.
ADD_COMMENT_FORM.addEventListener('submit', async (event) => {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();

    // Constante tipo objeto con los datos del formulario.
    const FORM = new FormData(ADD_COMMENT_FORM);

    // Petición para agregar el comentario.
    const DATA = await fetchData(PRODUCTO_API, 'addComment', FORM);
    // Limpiar el formulario.
    ADD_COMMENT_FORM.reset();

    if (DATA.status) {
        sweetAlert(1, DATA.message, false);
        location.reload();
    } else if (DATA.session) {
        sweetAlert(2, DATA.error, false);
    } else {
        sweetAlert(3, DATA.error, true);
    }
});



async function displayComments(idProducto) {
    const FORM = new FormData();
    FORM.append('idProducto', idProducto);

    try {
        const COMMENTS_DATA = await fetchData(PRODUCTO_API, 'readComments', FORM);

        // Verifica si la respuesta es satisfactoria
        if (COMMENTS_DATA.status) {
            const commentsContainer = document.getElementById('commentsContainer');
            commentsContainer.innerHTML = '';

            // Itera sobre los comentarios y muestra cada uno
            COMMENTS_DATA.dataset.forEach(comment => {
                const commentElement = document.createElement('div');
                commentElement.classList.add('card', 'mb-3');
                commentElement.innerHTML = `
                    <div class="card-body">
                        <h5 class="card-title">${comment.nombre_cliente} ${comment.apellido_cliente}</h5>
                        <div class="d-flex align-items-center">
                            <div class="d-inline-flex" style="font-size: 1.5rem; color: black;">
                                ${'★'.repeat(Math.round(comment.calificacion_producto))}${'☆'.repeat(5 - Math.round(comment.calificacion_producto))}
                            </div>
                            <span class="ms-2">${comment.calificacion_producto} / 5</span>
                        </div>
                        <p class="card-text mt-2">Comentario: ${comment.comentario_producto}</p>
                        <p class="text-muted mb-0">Fecha: ${new Date(comment.fecha_valoracion).toLocaleDateString()}</p>
                    </div>
                `;
                commentsContainer.appendChild(commentElement);
            });
        } else {
            document.getElementById('commentsContainer').innerHTML = `<p>${COMMENTS_DATA.error}</p>`;
        }
    } catch (error) {
        console.error('Error al obtener los comentarios:', error);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const ratingStars = document.getElementById('ratingStars');
    const ratingInput = document.getElementById('commentCalificacion');

    // Crear las estrellas interactivas
    for (let i = 1; i <= 5; i++) {
        const star = document.createElement('span');
        star.textContent = '★';
        star.style.cursor = 'pointer';
        star.style.fontSize = '2rem'; // Tamaño de las estrellas
        star.style.color = 'black'; // Color de las estrellas
        star.dataset.value = i; // Valor de la estrella

        star.addEventListener('mouseover', () => {
            highlightStars(i);
        });

        star.addEventListener('mouseout', () => {
            highlightStars(parseInt(ratingInput.value) || 0);
        });

        star.addEventListener('click', () => {
            ratingInput.value = i;
            highlightStars(i);
        });

        ratingStars.appendChild(star);
    }

    function highlightStars(count) {
        const stars = ratingStars.querySelectorAll('span');
        stars.forEach(star => {
            if (parseInt(star.dataset.value) <= count) {
                star.style.color = 'black'; // Estrellas llenas
            } else {
                star.style.color = 'lightgrey'; // Estrellas vacías
            }
        });
    }
});









