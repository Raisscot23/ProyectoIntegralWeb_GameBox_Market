<?php
session_start();

 //---------------------------------------------------------------------Cargar imagen de usurio en el header
$rol = $_SESSION['rol'] ?? null;
$nombre = $_SESSION['nombre'] ?? 'Invitado';

// Verificar si hay una imagen guardada en la sesión
if (!empty($_SESSION['img'])) {
    $userImage = 'data:image/jpeg;base64,' . $_SESSION['img'];
} else {
    $userImage = 'recursos/img/NoImage.png'; // imagen por defecto
}
 //---------------------------------------------------------------------
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/headerFooter.css">
    <link rel="stylesheet" href="css/AboutUs.css">

    <title>Sobre nosostros</title>

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

    <div id="contenido">
        <div id="desarrollador1">
            <div class="profile">
                <img id="profile_img" src="recursos/img/placeholder.jpg" alt="">
            </div> <br><br>

            <div class="frase">
                <h3>Lorem ipsum dolor sit amet consectetur adipisicing elit. Aperiam ex atque animi, perferendis odit
                    velit..</h3>
            </div> <br><br>

            <div class="data">
                <h3>Nombre</h3>
                <h3>Trabajo dentro del proyecto</h3>
            </div>
        </div>

        <div id="desarrollador2">
            <div class="profile">
                <img id="profile_img" src="recursos/img/placeholder.jpg" alt="">
            </div> <br><br>

            <div class="frase">
                <h3>Lorem ipsum dolor sit amet consectetur adipisicing elit. Aperiam ex atque animi, perferendis odit
                    velit..</h3>
            </div> <br><br>

            <div class="data">
                <h3>Nombre</h3>
                <h3>Trabajo dentro del proyecto</h3>
            </div>
        </div>
    </div>

    <div id="FAQ">
        <div id="title">
            <h1>FAQ</h1>
            <h2>Preguntas frecuentes hacía nosotros</h2>
            <h2>-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
            </h2>
        </div>

        <div class="preguntas">
            <div class="question">
                <h2>Lorem, ipsum dolor sit amet aut?</h2>
                <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Omnis excepturi hic tempora nulla, at culpa
                    voluptatem earum, repellendus nihil et, ab accusantium beatae dicta fuga vitae? Eligendi, dolores
                    est.
                    Neque?</p>
            </div>

            <div class="question">
                <h2>Lorem, ipsum dolor sit amet aut?</h2>
                <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Omnis excepturi hic tempora nulla, at culpa
                    voluptatem earum, repellendus nihil et, ab accusantium beatae dicta fuga vitae? Eligendi, dolores
                    est.
                    Neque?</p>
            </div>

            <div class="question">
                <h2>Lorem, ipsum dolor sit amet aut?</h2>
                <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Omnis excepturi hic tempora nulla, at culpa
                    voluptatem earum, repellendus nihil et, ab accusantium beatae dicta fuga vitae? Eligendi, dolores
                    est.
                    Neque?</p>
            </div>

            <div class="question">
                <h2>Lorem, ipsum dolor sit amet aut?</h2>
                <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Omnis excepturi hic tempora nulla, at culpa
                    voluptatem earum, repellendus nihil et, ab accusantium beatae dicta fuga vitae? Eligendi, dolores
                    est.
                    Neque?</p>
            </div>

            <div class="question">
                <h2>Lorem, ipsum dolor sit amet aut?</h2>
                <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Omnis excepturi hic tempora nulla, at culpa
                    voluptatem earum, repellendus nihil et, ab accusantium beatae dicta fuga vitae? Eligendi, dolores
                    est.
                    Neque?</p>
            </div>

            <div class="question">
                <h2>Lorem, ipsum dolor sit amet aut?</h2>
                <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Omnis excepturi hic tempora nulla, at culpa
                    voluptatem earum, repellendus nihil et, ab accusantium beatae dicta fuga vitae? Eligendi, dolores
                    est.
                    Neque?</p>
            </div>
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

</body>

</html>