<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookTracker - Gestor de Libros</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <!-- Sección de Autenticación -->
    <div id="auth-section" class="container auth-container">
        <div class="auth-card">
            <h1>📚 BookTracker</h1>
            <p class="subtitle">Tu gestor personal de libros</p>
            
            <!-- Pestañas de Login y Registro -->
            <div class="auth-tabs">
                <button class="tab-button active" data-tab="login">Iniciar Sesión</button>
                <button class="tab-button" data-tab="register">Registrarse</button>
            </div>

            <!-- Login Tab -->
            <div id="login-tab" class="tab-content active">
                <form id="login-form" class="auth-form">
                    <div class="form-group">
                        <label for="login-email">Correo Electrónico</label>
                        <input type="email" id="login-email" placeholder="tu@email.com" required>
                    </div>
                    <div class="form-group">
                        <label for="login-password">Contraseña</label>
                        <input type="password" id="login-password" placeholder="••••••••" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Entrar</button>
                    <div id="login-error" class="error-message"></div>
                </form>
            </div>

            <!-- Register Tab -->
            <div id="register-tab" class="tab-content">
                <form id="register-form" class="auth-form">
                    <div class="form-group">
                        <label for="register-name">Nombre Completo</label>
                        <input type="text" id="register-name" placeholder="Tu nombre" required>
                    </div>
                    <div class="form-group">
                        <label for="register-email">Correo Electrónico</label>
                        <input type="email" id="register-email" placeholder="tu@email.com" required>
                    </div>
                    <div class="form-group">
                        <label for="register-password">Contraseña</label>
                        <input type="password" id="register-password" placeholder="Mínimo 6 caracteres" minlength="6" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Registrarse</button>
                    <div id="register-error" class="error-message"></div>
                </form>
            </div>
        </div>
    </div>

    <!-- Sección de la Aplicación -->
    <div id="app-section" class="container app-container" style="display:none;">
        <header class="app-header">
            <h1>📚 BookTracker</h1>
            <div class="user-info">
                <span>Hola, <strong id="user-name"></strong></span>
                <button id="logout-btn" class="btn btn-secondary">Cerrar Sesión</button>
            </div>
        </header>

        <!-- Formulario para añadir libro -->
        <section class="add-book-section">
            <h2>Añadir Nuevo Libro</h2>
            <form id="add-book-form" class="book-form">
                <div class="form-row">
                    <div class="form-group">
                        <label for="book-title">Título del Libro *</label>
                        <input type="text" id="book-title" placeholder="Título" required>
                    </div>
                    <div class="form-group">
                        <label for="book-author">Autor *</label>
                        <input type="text" id="book-author" placeholder="Nombre del autor" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="book-pages">Número de Páginas</label>
                        <input type="number" id="book-pages" placeholder="Ej: 350" min="1" required>
                    </div>
                    <div class="form-group">
                        <label for="book-due-date">Fecha de Finalización</label>
                        <input type="date" id="book-due-date" required>
                    </div>
                    <div class="form-group button-group">
                        <button type="submit" class="btn btn-success">+ Añadir Libro</button>
                    </div>
                </div>
                <div id="add-book-error" class="error-message"></div>
            </form>
        </section>

        <!-- Filtros y Contador -->
        <section class="books-section">
            <div class="section-header">
                <h2>Mis Libros</h2>
                <div class="book-stats">
                    <span class="stat pending-count">Pendientes: <strong>0</strong></span>
                    <span class="stat reading-count">Leyendo: <strong>0</strong></span>
                    <span class="stat finished-count">Finalizados: <strong>0</strong></span>
                </div>
            </div>

            <!-- Lista de Libros -->
            <div id="books-list" class="books-list">
                <p class="empty-message">No tienes libros registrados. ¡Añade uno para empezar!</p>
            </div>
        </section>
    </div>

    <script src="main.js"></script>
</body>
</html>