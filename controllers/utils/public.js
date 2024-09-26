/*
*   Controlador es de uso general en las páginas web del sitio público.
*   Sirve para manejar las plantillas del encabezado y pie del documento.
*/

// Constante para completar la ruta de la API.
const USER_API = 'services/public/cliente.php';
// Constante para establecer el elemento del contenido principal.
const MAIN = document.querySelector('main');
MAIN.style.paddingTop = '75px';
MAIN.style.paddingBottom = '100px';
MAIN.classList.add('container');
// Se establece el título de la página web.
document.querySelector('title').textContent = 'Sport Development - Store';
// Constante para establecer el elemento del título principal.
const MAIN_TITLE = document.getElementById('mainTitle');
MAIN_TITLE.classList.add('text-center', 'py-3');

/*  Función asíncrona para cargar el encabezado y pie del documento.
*   Parámetros: ninguno.
*   Retorno: ninguno.
*/
const loadTemplate = async () => {
    // Petición para obtener en nombre del usuario que ha iniciado sesión.
    const DATA = await fetchData(USER_API, 'getUser');
    // Se comprueba si el usuario está autenticado para establecer el encabezado respectivo.
    if (DATA.session) {
        // Se verifica si la página web no es el inicio de sesión, de lo contrario se direcciona a la página web principal.
        if (!location.pathname.endsWith('login.html')) {
            // Se agrega el encabezado de la página web antes del contenido principal.
            MAIN.insertAdjacentHTML('beforebegin', `
                <<header>
                <nav class="navbar fixed-top navbar-expand-lg" style="background-color: #245C9D;">
                    <div class="container text-white border-white">
                            <a class="navbar-brand text-white border-white" href="index.html"><img class="px-2" src="../../resources/img/logo.png" height="50" alt="CoffeeShop">Sport Development</a>
                            <button class="navbar-toggler text-white border-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                            <i class="bi bi-list"></i>
                            </button>
                            <div class="collapse navbar-collapse text-white" id="navbarNavAltMarkup">
                                <div class="navbar-nav ms-auto text-white">
                                    <a class="nav-link text-white" href="index.html"><i class="bi bi-shop"></i> Catálogo</a>
                                    <a class="nav-link text-white" href="carrito.html"><i class="bi bi-cart"></i> Carrito</a>
                                    <a class="nav-link text-white" href="pedido.html"><i class="bi bi-truck"></i> Pedidos</a>
                                    <a class="nav-link text-white" href="historial.html"><i class="bi bi-hourglass-split"></i> Historial</a>
                                    <li class="nav-item dropdown">
                                        <a class="nav-link dropdown-toggle text-white" href="#" data-bs-toggle="dropdown" aria-expanded="false">Cuenta: <b>${DATA.name}</b></a>
                                        <ul class="dropdown-menu" style="background-color: #245C9D;">
                                            <li><a class="nav-link text-white" href="perfil.html"><i class="bi bi-person"></i> Editar Perfil</a></li>
                                            <li><hr class="dropdown-divider text-dark"></li>
                                            <li> <a class="nav-link text-white" href="#" onclick="logOut()"><i class="bi bi-box-arrow-left"></i> Cerrar sesión</a></li>
                                        </ul>
                                    </li>
                                </div>
                            </div>
                        </div>
                    </nav>
                </header>
            `);
        } else {
            location.href = 'index.html';
        }
    } else {
        // Se agrega el encabezado de la página web antes del contenido principal.
        MAIN.insertAdjacentHTML('beforebegin', `
            <header>
                <nav class="navbar fixed-top navbar-expand-lg" style="background-color: #245C9D;">
                    <div class="container text-white">
                        <a class="navbar-brand text-white border-white" href="index.html"><img class="px-2" src="../../resources/img/logo.png" height="50" alt="CoffeeShop">Sport Development</a>
                        <button class="navbar-toggler text-white border-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                        <i class="bi bi-list"></i>
                        </button>
                        <div class="collapse navbar-collapse text-white" id="navbarNavAltMarkup">
                            <div class="navbar-nav ms-auto text-white">
                                <a class="nav-link text-white" href="index.html"><i class="bi bi-shop"></i> Catálogo</a>
                                <a class="nav-link text-white" href="registrar.html"><i class="bi bi-person"></i> Crear cuenta</a>
                                <a class="nav-link text-white" href="login.html"><i class="bi bi-box-arrow-right"></i> Iniciar sesión</a>
                            </div>
                        </div>
                    </div>
                </nav>
            </header>
        `);
    }
    // Se agrega el pie de la página web después del contenido principal.
    MAIN.insertAdjacentHTML('afterend', `
        <footer>
            <nav class="navbar fixed-bottom bg-dark">
                <div class="container">
                    <div>
                        <h4 class="text-white">Sport Development</h4>
                        <a class="text-white"><i class="bi bi-c-square text-white"></i> 2018-2024 Todos los derechos reservados</a>
                    </div>
                    <div>
                        <h4 class="text-white">Contáctanos</h4>
                        <a class="text-white"><i class="bi bi-envelope text-white"></i> sportdevelopment@gmail.com</a>
                    </div>
                </div>
            </nav>
        </footer>
    `);
}