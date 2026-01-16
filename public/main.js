// ==================== Configuración de Rutas ====================
// Este archivo define las rutas base para las APIs

// Detectar automáticamente la ruta base
const getApiBasePath = () => {
    const pathname = window.location.pathname;
    
    // Si estamos en /public/index.php o /public/
    if (pathname.includes('/public/')) {
        return '../api/';
    }
    // Si estamos en raíz
    return './api/';
};

const API_BASE = getApiBasePath();

// Rutas de API
const API_ENDPOINTS = {
    register: API_BASE + 'register.php',
    login: API_BASE + 'login.php',
    logout: API_BASE + 'logout.php',
    books: API_BASE + 'books.php'
};

// ==================== Variables Globales ====================
let currentUser = null;

// ==================== Inicialización ====================
document.addEventListener('DOMContentLoaded', () => {
    setupEventListeners();
    checkAuthStatus();
});

// ==================== Event Listeners ====================
function setupEventListeners() {
    // Tabs de autenticación
    const tabButtons = document.querySelectorAll('.tab-button');
    tabButtons.forEach(button => {
        button.addEventListener('click', switchAuthTab);
    });

    // Formularios de autenticación
    document.getElementById('login-form').addEventListener('submit', handleLogin);
    document.getElementById('register-form').addEventListener('submit', handleRegister);

    // Formulario de libro
    document.getElementById('add-book-form').addEventListener('submit', handleAddBook);

    // Botón de logout
    document.getElementById('logout-btn').addEventListener('click', handleLogout);
}

// ==================== Autenticación ====================

// Cambiar entre tabs de login/registro
function switchAuthTab(e) {
    const tab = e.target.getAttribute('data-tab');
    
    // Actualizar botones activos
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.classList.remove('active');
    });
    e.target.classList.add('active');

    // Actualizar contenido de tabs
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.remove('active');
    });
    document.getElementById(tab + '-tab').classList.add('active');

    // Limpiar mensajes de error
    document.getElementById('login-error').textContent = '';
    document.getElementById('register-error').textContent = '';
}

// Manejo del login
async function handleLogin(e) {
    e.preventDefault();

    const email = document.getElementById('login-email').value;
    const password = document.getElementById('login-password').value;
    const errorDiv = document.getElementById('login-error');

    try {
        const formData = new FormData();
        formData.append('email', email);
        formData.append('password', password);

        const response = await fetch(API_ENDPOINTS.login, {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.status === 'success') {
            currentUser = { email };
            await loadUserInfo();
            showAppSection();
            document.getElementById('login-form').reset();
            errorDiv.textContent = '';
        } else {
            errorDiv.textContent = data.message || 'Error al iniciar sesión';
            errorDiv.style.display = 'block';
        }
    } catch (error) {
        errorDiv.textContent = 'Error de conexión: ' + error.message;
        errorDiv.style.display = 'block';
        console.error('Error en login:', error);
    }
}

// Manejo del registro
async function handleRegister(e) {
    e.preventDefault();

    const name = document.getElementById('register-name').value;
    const email = document.getElementById('register-email').value;
    const password = document.getElementById('register-password').value;
    const errorDiv = document.getElementById('register-error');

    try {
        const formData = new FormData();
        formData.append('name', name);
        formData.append('email', email);
        formData.append('password', password);

        const response = await fetch(API_ENDPOINTS.register, {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.status === 'success') {
            errorDiv.textContent = '';
            alert('Registro exitoso. Por favor, inicia sesión.');
            document.getElementById('register-form').reset();
            
            // Cambiar a tab de login
            document.querySelector('[data-tab="login"]').click();
        } else if (data.errors) {
            const errorMessages = Object.values(data.errors).join('\n');
            errorDiv.textContent = errorMessages;
            errorDiv.style.display = 'block';
        } else {
            errorDiv.textContent = data.message || 'Error al registrarse';
            errorDiv.style.display = 'block';
        }
    } catch (error) {
        errorDiv.textContent = 'Error de conexión: ' + error.message;
        errorDiv.style.display = 'block';
        console.error('Error en registro:', error);
    }
}

// Verificar estado de autenticación
async function checkAuthStatus() {
    try {
        const response = await fetch(API_ENDPOINTS.books);
        if (response.status === 401) {
            showAuthSection();
        } else if (response.ok) {
            await loadUserInfo();
            showAppSection();
            await loadBooks();
        }
    } catch (error) {
        console.error('Error al verificar autenticación:', error);
        showAuthSection();
    }
}

// ==================== Gestión de Libros ====================

// Cargar información del usuario
async function loadUserInfo() {
    try {
        const response = await fetch(API_ENDPOINTS.books);
        if (response.ok) {
            const data = await response.json();
            // Obtener el nombre del usuario desde la respuesta de la API
            const userName = data.user_name || 'Usuario';
            document.getElementById('user-name').textContent = userName;
        }
    } catch (error) {
        console.error('Error al cargar info del usuario:', error);
    }
}

// Cargar lista de libros
async function loadBooks() {
    try {
        const response = await fetch(API_ENDPOINTS.books);
        
        if (response.status === 401) {
            showAuthSection();
            return;
        }

        const data = await response.json();

        if (data.status === 'success') {
            displayBooks(data.data);
            updateStats(data.data);
        } else {
            console.error('Error al cargar libros:', data.message);
        }
    } catch (error) {
        console.error('Error en loadBooks:', error);
    }
}

// Mostrar libros en la interfaz
function displayBooks(books) {
    const booksList = document.getElementById('books-list');

    if (books.length === 0) {
        booksList.innerHTML = '<p class="empty-message">No tienes libros registrados. ¡Añade uno para empezar!</p>';
        return;
    }

    // Agrupar libros por estado
    const booksByStatus = {
        'Pendiente': [],
        'Leyendo': [],
        'Finalizado': []
    };

    books.forEach(book => {
        if (booksByStatus[book.status]) {
            booksByStatus[book.status].push(book);
        }
    });

    let html = '';

    // Mostrar libros por estado
    Object.entries(booksByStatus).forEach(([status, statusBooks]) => {
        if (statusBooks.length > 0) {
            html += `<div class="status-section status-${status.toLowerCase()}">
                        <h3>${status} (${statusBooks.length})</h3>
                        <div class="books-group">`;

            statusBooks.forEach(book => {
                const formattedDate = book.due_date ? new Date(book.due_date).toLocaleDateString('es-ES') : 'Sin fecha';
                const pagesText = book.pages ? `📖 ${book.pages} págs.` : 'Sin información de páginas';
                const statusIcon = {
                    'Pendiente': '⏳',
                    'Leyendo': '📖',
                    'Finalizado': '✅'
                }[status];

                html += `
                    <div class="book-card status-${status.toLowerCase()}">
                        <div class="book-header">
                            <div class="book-title-section">
                                <span class="status-icon">${statusIcon}</span>
                                <div class="book-titles">
                                    <h4 class="book-title">${escapeHtml(book.title)}</h4>
                                    <p class="book-author">por ${escapeHtml(book.author)}</p>
                                </div>
                            </div>
                        </div>
                        <div class="book-details">
                            <span class="pages">${pagesText}</span>
                            <span class="due-date">📅 ${formattedDate}</span>
                        </div>
                        <div class="book-actions">
                            <select class="status-select" onchange="updateBookStatus(${book.id}, this.value)" data-current="${book.status}">
                                <option value="Pendiente" ${book.status === 'Pendiente' ? 'selected' : ''}>Pendiente</option>
                                <option value="Leyendo" ${book.status === 'Leyendo' ? 'selected' : ''}>Leyendo</option>
                                <option value="Finalizado" ${book.status === 'Finalizado' ? 'selected' : ''}>Finalizado</option>
                            </select>
                            <button class="btn btn-delete" onclick="deleteBook(${book.id})">Eliminar</button>
                        </div>
                    </div>
                `;
            });

            html += '</div></div>';
        }
    });

    booksList.innerHTML = html;
}

// Actualizar estadísticas
function updateStats(books) {
    const stats = {
        pending: books.filter(b => b.status === 'Pendiente').length,
        reading: books.filter(b => b.status === 'Leyendo').length,
        finished: books.filter(b => b.status === 'Finalizado').length
    };

    document.querySelector('.pending-count strong').textContent = stats.pending;
    document.querySelector('.reading-count strong').textContent = stats.reading;
    document.querySelector('.finished-count strong').textContent = stats.finished;
}

// ==================== Operaciones CRUD ====================

// Añadir nuevo libro
async function handleAddBook(e) {
    e.preventDefault();

    const title = document.getElementById('book-title').value;
    const author = document.getElementById('book-author').value;
    const pages = document.getElementById('book-pages').value;
    const due_date = document.getElementById('book-due-date').value;
    const errorDiv = document.getElementById('add-book-error');

    try {
        const response = await fetch(API_ENDPOINTS.books, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                title,
                author,
                pages: pages ? parseInt(pages) : null,
                due_date: due_date || null
            })
        });

        const data = await response.json();

        if (data.status === 'success') {
            document.getElementById('add-book-form').reset();
            errorDiv.textContent = '';
            await loadBooks();
        } else {
            errorDiv.textContent = data.message || 'Error al añadir el libro';
            errorDiv.style.display = 'block';
        }
    } catch (error) {
        errorDiv.textContent = 'Error de conexión: ' + error.message;
        errorDiv.style.display = 'block';
        console.error('Error al añadir libro:', error);
    }
}

// Actualizar estado del libro
async function updateBookStatus(id, newStatus) {
    try {
        const response = await fetch(API_ENDPOINTS.books, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                id,
                status: newStatus
            })
        });

        const data = await response.json();

        if (data.status === 'success') {
            await loadBooks();
        } else {
            alert('Error al actualizar el estado: ' + data.message);
        }
    } catch (error) {
        alert('Error de conexión: ' + error.message);
        console.error('Error al actualizar libro:', error);
    }
}

// Eliminar libro
async function deleteBook(id) {
    if (!confirm('¿Estás seguro de que quieres eliminar este libro?')) {
        return;
    }

    try {
        const response = await fetch(API_ENDPOINTS.books, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id })
        });

        const data = await response.json();

        if (data.status === 'success') {
            await loadBooks();
        } else {
            alert('Error al eliminar el libro: ' + data.message);
        }
    } catch (error) {
        alert('Error de conexión: ' + error.message);
        console.error('Error al eliminar libro:', error);
    }
}

// ==================== Cerrar Sesión ====================

async function handleLogout() {
    if (!confirm('¿Estás seguro de que quieres cerrar sesión?')) {
        return;
    }

    try {
        await fetch(API_ENDPOINTS.logout);
        currentUser = null;
        showAuthSection();
    } catch (error) {
        console.error('Error al cerrar sesión:', error);
        alert('Error al cerrar sesión');
    }
}

// ==================== Utilidades ====================

// Mostrar/ocultar secciones
function showAuthSection() {
    document.getElementById('auth-section').style.display = 'block';
    document.getElementById('app-section').style.display = 'none';
}

function showAppSection() {
    document.getElementById('auth-section').style.display = 'none';
    document.getElementById('app-section').style.display = 'block';
}

// Escapar HTML para evitar XSS
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
