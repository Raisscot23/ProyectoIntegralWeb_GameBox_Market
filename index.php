<?php
session_start();

$rol = $_SESSION['rol'] ?? null;
$nombre = $_SESSION['nombre'] ?? 'Invitado';

// ✅ Verificar si hay una imagen guardada en la sesión
if (!empty($_SESSION['img'])) {
    $userImage = 'data:image/jpeg;base64,' . $_SESSION['img'];
} else {
    $userImage = 'recursos/img/placeholder.jpg'; // imagen por defecto
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/headerFooter.css">
    <link rel="stylesheet" href="css/index.css">
    <title>GameBox Market</title>

    <style>
        #userIcon {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #fff;
        }

        .logout-btn {
            background-color: #ff4040;
            color: white;
            padding: 8px 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            margin-left: 15px;
            transition: background-color 0.3s ease;
        }

        .logout-btn:hover {
            background-color: #cc0000;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-name {
            font-weight: bold;
            color: white;
        }
    </style>
</head>

<body>
    <header>
        <a href="index.php">
            <img id="logo_head" src="recursos/img/palceholder 2.svg" alt="Logo GameBox">
        </a>

        <section id="menu_head">
            <ul>
                <?php if ($rol == 1): // Mostrar CRUD solo si el usuario es administrador ?>
                    <li><a href="create.php">CRUD</a></li>
                <?php endif; ?>

                <li><a href="catalogo.php">Catálogo</a></li>


                <li>
                    <?php if ($rol): ?>
                        <a href="carrito.php">Carrito de compras</a>
                    <?php else: ?>
                        <a href="login.php"">Carrito de compras</a>
                    <?php endif; ?>
                </li>
                
                <li class="user-info">
                    <?php if ($rol): ?>
                        <a href="userProfile.php">
                            <img id="userIcon" src="<?php echo htmlspecialchars($userImage); ?>" alt="Perfil del usuario">
                        </a>
                        <span class="user-name"><?php echo htmlspecialchars($nombre); ?></span>
                        <form method="POST" action="logout.php" style="display:inline;">
                            <button type="submit" class="logout-btn">Cerrar sesión</button>
                        </form>
                    <?php else: ?>
                        <a href="login.php" class="logout-btn" style="background-color:#007bff;">Iniciar sesión</a>
                    <?php endif; ?>
                </li>
            </ul>
        </section>
    </header>

    <!-- CONTENIDO PRINCIPAL -->
    <div id="contenido">
        <div id="promociones">
            <h1>Promociones</h1>
            <div id="promociones_wrapper">
                <div class="promociones_viewport">
                    <div class="promociones_contenedor">

                        <div class="tarjetaPromocion">
                            <img id="promocion_Img" src="recursos/img/palceholder 2.svg" alt="">
                            <h3>999$</h3>
                            <button><a href="carrito.php">Añadir al carrito</a></button>
                        </div>

                        <div class="tarjetaPromocion">
                            <img id="promocion_Img" src="recursos/img/palceholder 2.svg" alt="">
                            <h3>999$</h3>
                            <button><a href="carrito.php">Añadir al carrito</a></button>
                        </div>

                        <div class="tarjetaPromocion">
                            <img id="promocion_Img" src="recursos/img/palceholder 2.svg" alt="">
                            <h3>999$</h3>
                            <button><a href="carrito.php">Añadir al carrito</a></button>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div id="recientes">
            <h1>Productos Recientes</h1>
            <p>Lorem ipsum dolor sit amet et delectus accommodare.</p>

            <div class="tarjetasRecientesWrapper">
                <div class="tarjetaReciente">
                    <img id="reciente_Img" src="recursos/img/palceholder 2.svg" alt="">
                    <h3>Lorem ipsum dolor sit amet consectetur adipisicing elit..</h3>
                    <a id="readMore" href="">Leer más</a>
                </div>

                <div class="tarjetaReciente">
                    <img id="reciente_Img" src="recursos/img/palceholder 2.svg" alt="">
                    <h3>Lorem ipsum dolor sit amet consectetur adipisicing elit..</h3>
                    <a id="readMore" href="">Leer más</a>
                </div>

                <div class="tarjetaReciente">
                    <img id="reciente_Img" src="recursos/img/palceholder 2.svg" alt="">
                    <h3>Lorem ipsum dolor sit amet consectetur adipisicing elit..</h3>
                    <a id="readMore" href="">Leer más</a>
                </div>
            </div>
        </div>

        <div id="destacados">
            <h1>Destacados</h1>
            <div id="destacados_wrapper">
                <button class="prev">&#8249;</button>

                <div class="destacados_viewport">
                    <div class="destacados_contenedor">
                        <div class="tarjetaDestacado"><img src="recursos/img/placeholder.jpg" alt=""></div>
                        <div class="tarjetaDestacado"><img src="recursos/img/placeholder.jpg" alt=""></div>
                        <div class="tarjetaDestacado"><img src="recursos/img/placeholder.jpg" alt=""></div>
                        <div class="tarjetaDestacado"><img src="recursos/img/placeholder.jpg" alt=""></div>
                        <div class="tarjetaDestacado"><img src="recursos/img/placeholder.jpg" alt=""></div>
                        <div class="tarjetaDestacado"><img src="recursos/img/placeholder.jpg" alt=""></div>
                    </div>
                </div>

                <button class="next">&#8250;</button>
            </div>
        </div>

        <footer>
            <div id="redes">
                <ul>
                    <li><a href=""><img src="recursos/icons/icons (1).png" alt=""></a></li>
                    <li><a href=""><img src="recursos/icons/icons (1).webp" alt=""></a></li>
                </ul>
            </div>
            
            <div id="disclaimer">
                <h4>© 2025 GameBox Market | Todos los derechos reservados.</h4>
                <h4>Los diseños y productos que aparecen en el sitio pertenecen a sus respectivos creadores.</h4><br>
            </div>

            <h4>Si quieres conocer a los desarrolladores detrás del sitio,<a href="aboutUs.php"> haz click aquí</a></h4>

            <div id="avisos">
                <ul>
                    <li>Aviso de Cookies</li>
                    <li>Términos de uso</li>
                    <li>Aviso de privacidad</li>
                    <li>Ayuda</li>
                    <li>Política sobre uso de materiales</li>
                    <li>Declaración de afiliación</li>
                    <li>Directrices para transmisiones</li>
                    <li>Update notes</li>
                    <li>Licencias de plugins</li>
                </ul>
            </div>
        </footer>

        <script src="js/destacados.js"></script>
        <script src="js/modooscuro.js"></script>
</body>
</html>
