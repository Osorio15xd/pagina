-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 20-05-2025 a las 03:16:04
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `ai_chat_history` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `response` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ai_chat_history`
--

INSERT INTO `ai_chat_history` (`id`, `user_id`, `message`, `response`, `timestamp`) VALUES
(1, 21, 'hola', 'Hola bruno, no estoy seguro de cómo ayudarte con eso. ¿Puedes ser más específico? Puedo recomendarte música o ayudarte con preguntas sobre BassCulture.', '2025-03-24 05:28:23'),
(3, 21, 'hola', 'Hola bruno, ¿en qué puedo ayudarte? Puedo darte información sobre nuestra plataforma, ayudarte a encontrar música o resolver dudas sobre tu cuenta.', '2025-03-24 13:56:54'),
(4, 21, 'me puedes dar recomendaciones', 'Hola bruno, no estoy seguro de cómo ayudarte con eso. ¿Puedes ser más específico? Puedo recomendarte música o ayudarte con preguntas sobre BassCulture.', '2025-03-24 13:57:06'),
(5, 21, 'como registrarte', 'Hola bruno, no estoy seguro de cómo ayudarte con eso. ¿Puedes ser más específico? Puedo recomendarte música o ayudarte con preguntas sobre BassCulture.', '2025-03-24 13:57:34'),
(6, 21, 'hola', 'Hola bruno, ¿en qué puedo ayudarte? Puedo darte información sobre nuestra plataforma, ayudarte a encontrar música o resolver dudas sobre tu cuenta.', '2025-03-27 02:54:04'),
(7, 21, 'hola', 'Hola bruno, ¿en qué puedo ayudarte? Puedo darte información sobre nuestra plataforma, ayudarte a encontrar música o resolver dudas sobre tu cuenta.', '2025-03-27 13:59:09'),
(8, 21, 'iniciar secion', 'Hola bruno, no estoy seguro de cómo ayudarte con eso. ¿Puedes ser más específico? Puedo recomendarte música o ayudarte con preguntas sobre BassCulture.', '2025-03-27 13:59:29'),
(9, 21, 'iniciar sesion', 'Hola bruno, no estoy seguro de cómo ayudarte con eso. ¿Puedes ser más específico? Puedo recomendarte música o ayudarte con preguntas sobre BassCulture.', '2025-03-27 13:59:38'),
(10, 21, 'buenos dias', 'Hola bruno, no estoy seguro de cómo ayudarte con eso. ¿Puedes ser más específico? Puedo recomendarte música o ayudarte con preguntas sobre BassCulture.', '2025-03-27 14:00:22'),
(11, 21, 'hola', 'Hola bruno, ¿en qué puedo ayudarte? Puedo darte información sobre nuestra plataforma, ayudarte a encontrar música o resolver dudas sobre tu cuenta.', '2025-03-27 15:11:10'),
(12, 21, 'como registrarme', 'Hola bruno, no estoy seguro de cómo ayudarte con eso. ¿Puedes ser más específico? Puedo recomendarte música o ayudarte con preguntas sobre BassCulture.', '2025-03-27 15:11:18'),
(13, 21, 'hola', 'Hola bruno, ¿en qué puedo ayudarte? Puedo darte información sobre nuestra plataforma, ayudarte a encontrar música o resolver dudas sobre tu cuenta.', '2025-03-27 16:40:15'),
(14, 21, 'registrar', 'Hola bruno, no estoy seguro de cómo ayudarte con eso. ¿Puedes ser más específico? Puedo recomendarte música o ayudarte con preguntas sobre BassCulture.', '2025-03-27 16:40:21'),
(15, 21, 'hola', 'Hola , no estoy seguro de cómo ayudarte con eso. ¿Puedes ser más específico? Puedo recomendarte música o ayudarte con preguntas sobre BassCulture.', '2025-04-02 07:05:51'),
(16, 21, 'hola', 'Lo siento, no tengo una respuesta para eso. ¿Puedes ser más específico? Puedo ayudarte con información sobre BassCulture, música o artistas.', '2025-04-29 17:58:32'),
(17, 21, 'como puedo registrarme', 'Para crear una cuenta en BassCulture, ve a la página de registro desde el menú principal. Necesitarás proporcionar tu nombre, correo electrónico y crear una contraseña segura. El proceso es rápido y te permitirá acceder a todas nuestras funciones.', '2025-04-29 17:58:41'),
(18, 21, 'como puedo subir una cancion', 'Lo siento, no tengo una respuesta para eso. ¿Puedes ser más específico? Puedo ayudarte con información sobre BassCulture, música o artistas.', '2025-04-29 17:58:47'),
(19, 21, 'subir música', 'Lo siento, no tengo una respuesta para eso. ¿Puedes ser más específico? Puedo ayudarte con información sobre BassCulture, música o artistas.', '2025-04-29 17:59:58'),
(20, 21, 'como puedo subir musica', 'Lo siento, no tengo una respuesta para eso. ¿Puedes ser más específico? Puedo ayudarte con información sobre BassCulture, música o artistas.', '2025-04-29 18:00:05'),
(21, 21, 'contactar soporte', 'Siento que estés teniendo problemas. Primero, verifica tu conexión a internet. Luego, intenta actualizar la página o reiniciar la aplicación. Si el problema persiste, contacta a nuestro soporte técnico con detalles específicos del error.', '2025-04-29 18:00:16'),
(22, 21, 'como funciona bassculture', 'BassCulture funciona como una biblioteca musical digital. Puedes buscar canciones, álbumes o artistas, crear playlists personalizadas, y descubrir nueva música a través de nuestras recomendaciones. Todo esto desde cualquier dispositivo con conexión a internet.', '2025-04-29 18:00:39'),
(23, 21, 'como puedo descargar musica', 'Lo siento, no tengo una respuesta para eso. ¿Puedes ser más específico? Puedo ayudarte con información sobre BassCulture, música o artistas.', '2025-04-29 18:01:13'),
(24, 21, 'hey', '¿En qué puedo ayudarte? Puedo darte información sobre nuestra plataforma, ayudarte a encontrar música o resolver dudas sobre tu cuenta.', '2025-04-29 18:01:43'),
(25, 21, 'hey}', '¿En qué puedo ayudarte? Puedo darte información sobre nuestra plataforma, ayudarte a encontrar música o resolver dudas sobre tu cuenta.', '2025-04-29 18:03:19'),
(26, 21, 'hola', 'Lo siento, no tengo una respuesta para eso. ¿Puedes ser más específico? Puedo ayudarte con información sobre BassCulture, música o artistas.', '2025-04-29 18:03:21'),
(27, 21, 'como funciona bassculure', 'BassCulture funciona como una biblioteca musical digital. Puedes buscar canciones, álbumes o artistas, crear playlists personalizadas, y descubrir nueva música a través de nuestras recomendaciones. Todo esto desde cualquier dispositivo con conexión a internet.', '2025-04-29 18:03:31'),
(28, 21, 'contactarme con soporte tecnico', 'Siento que estés teniendo problemas. Primero, verifica tu conexión a internet. Luego, intenta actualizar la página o reiniciar la aplicación. Si el problema persiste, contacta a nuestro soporte técnico con detalles específicos del error.', '2025-04-29 18:03:47'),
(29, 21, 'como puedo hacer una suscripcion premiun', 'BassCulture ofrece un plan gratuito con publicidad y funciones básicas. También tenemos planes premium que incluyen reproducción sin anuncios, descarga de música para escuchar sin conexión, y calidad de audio superior. Visita nuestra sección de \"Planes\" para más detalles.', '2025-04-29 18:04:23'),
(30, 21, 'como puedo publicar una cancion', 'Si eres artista, puedes subir tu música a BassCulture. Ve a \"Perfil > Artista > Subir música\" y sigue las instrucciones. Asegúrate de tener los derechos de las canciones que subes y proporciona información precisa sobre cada pista.', '2025-04-29 18:06:03'),
(31, 21, 'como utilizar bassculture', 'BassCulture funciona como una biblioteca musical digital. Puedes buscar canciones, álbumes o artistas, crear playlists personalizadas, y descubrir nueva música a través de nuestras recomendaciones. Todo esto desde cualquier dispositivo con conexión a internet.', '2025-04-29 18:06:45'),
(32, 21, 'el telefono de soporte tecnico', 'Siento que estés teniendo problemas. Primero, verifica tu conexión a internet. Luego, intenta actualizar la página o reiniciar la aplicación. Si el problema persiste, contacta a nuestro soporte técnico con detalles específicos del error.', '2025-04-29 18:07:12'),
(33, 21, 'como puedo dar de baja una cancion', 'Para eliminar tu cuenta, ve a \"Perfil > Configuración > Cuenta\" y selecciona \"Eliminar cuenta\". Ten en cuenta que esta acción es irreversible y perderás todo tu historial y playlists. Si tienes dudas, contacta a nuestro soporte antes de proceder.', '2025-04-29 18:07:32'),
(34, 21, 'como comprar un disco o sencillo', 'Lo siento, no tengo una respuesta para eso. ¿Puedes ser más específico? Puedo ayudarte con información sobre BassCulture, música o artistas.', '2025-04-29 18:08:07'),
(35, 21, 'hola', 'Lo siento, no tengo una respuesta para eso. ¿Puedes ser más específico? Puedo ayudarte con información sobre BassCulture, música o artistas.', '2025-05-14 16:36:28'),
(36, 21, 'como puedo suscribirme', 'Lo siento, no tengo una respuesta para eso. ¿Puedes ser más específico? Puedo ayudarte con información sobre BassCulture, música o artistas.', '2025-05-14 16:36:37'),
(37, 21, 'sin conexion', 'Con una suscripción premium, puedes descargar música para escucharla sin conexión. Busca el ícono de descarga junto a las canciones, álbumes o playlists que quieras guardar. Las descargas estarán disponibles en la sección \"Tu biblioteca\".', '2025-05-14 16:38:07'),
(38, 21, 'dar de baja', 'Para eliminar tu cuenta, ve a \"Perfil > Configuración > Cuenta\" y selecciona \"Eliminar cuenta\". Ten en cuenta que esta acción es irreversible y perderás todo tu historial y playlists. Si tienes dudas, contacta a nuestro soporte antes de proceder.', '2025-05-14 16:38:21'),
(39, 21, 'hola', 'Lo siento, no tengo una respuesta para eso. ¿Puedes ser más específico? Puedo ayudarte con información sobre BassCulture, música o artistas.', '2025-05-19 14:11:31'),
(40, 21, 'canta la del payaso del rodeo', 'Lo siento, no tengo una respuesta para eso. ¿Puedes ser más específico? Puedo ayudarte con información sobre BassCulture, música o artistas.', '2025-05-19 14:14:00'),
(41, 21, 'cuantos albums se subieron ?', 'Lo siento, no tengo una respuesta para eso. ¿Puedes ser más específico? Puedo ayudarte con información sobre BassCulture, música o artistas.', '2025-05-19 14:14:13'),
(42, 21, 'contactar soporte', 'Siento que estés teniendo problemas. Primero, verifica tu conexión a internet. Luego, intenta actualizar la página o reiniciar la aplicación. Si el problema persiste, contacta a nuestro soporte técnico con detalles específicos del error.', '2025-05-19 14:14:31'),
(43, 21, 'tienes natael cano', 'Lo siento, no tengo una respuesta para eso. ¿Puedes ser más específico? Puedo ayudarte con información sobre BassCulture, música o artistas.', '2025-05-19 14:15:32'),
(44, 21, 'crear cuenta', 'Para crear una playlist, inicia sesión y haz clic en \"Biblioteca\" en el menú lateral. Luego selecciona \"Crear playlist\", asígnale un nombre y comienza a añadir canciones. Puedes hacerla pública o mantenerla privada según tus preferencias.', '2025-05-19 14:15:51');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ai_keywords`
--

CREATE TABLE `ai_keywords` (
  `id` int(11) NOT NULL,
  `response_id` int(11) NOT NULL COMMENT 'ID de la respuesta relacionada',
  `keyword` varchar(100) NOT NULL COMMENT 'Palabra clave',
  `weight` float NOT NULL DEFAULT 1 COMMENT 'Peso de la palabra clave (mayor = más relevante)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ai_keywords`
--

INSERT INTO `ai_keywords` (`id`, `response_id`, `keyword`, `weight`) VALUES
(1, 1, 'saludar', 0.8),
(2, 1, 'hey', 0.9),
(3, 1, 'qué tal', 0.8),
(4, 2, 'mañana', 0.7),
(5, 3, 'tarde', 0.7),
(6, 4, 'noche', 0.7),
(7, 5, 'registrar', 1),
(8, 5, 'inscribir', 0.9),
(9, 5, 'unirse', 0.8),
(10, 5, 'nueva cuenta', 1),
(11, 6, 'registrarse', 1),
(12, 6, 'inscribirse', 0.9),
(13, 6, 'sign up', 0.8),
(14, 7, 'qué es', 1),
(15, 7, 'plataforma', 0.9),
(16, 7, 'servicio', 0.8),
(17, 8, 'cómo funciona', 1),
(18, 8, 'usar', 0.9),
(19, 8, 'utilizar', 0.8),
(20, 9, 'precio', 1),
(21, 9, 'costo', 1),
(22, 9, 'suscripción', 0.9),
(23, 9, 'premium', 0.9),
(24, 10, 'olvidé', 1),
(25, 10, 'recuperar', 0.9),
(26, 10, 'restablecer', 0.9),
(27, 10, 'cambiar', 0.8),
(28, 11, 'borrar', 1),
(29, 11, 'cerrar', 0.9),
(30, 11, 'dar de baja', 0.9),
(31, 12, 'crear', 1),
(32, 12, 'lista', 0.9),
(33, 12, 'colección', 0.8),
(34, 13, 'artista', 1),
(35, 13, 'publicar', 0.9),
(36, 13, 'compartir', 0.8),
(37, 14, 'offline', 0.9),
(38, 14, 'sin conexión', 0.9),
(39, 14, 'guardar', 0.8),
(40, 15, 'problema', 1),
(41, 15, 'falla', 0.9),
(42, 15, 'bug', 0.8),
(43, 16, 'ayuda', 1),
(44, 16, 'soporte', 0.9),
(45, 16, 'asistencia', 0.8),
(46, 1, 'hey', 0.9),
(47, 1, 'qué tal', 0.8),
(48, 1, 'saludos', 0.8),
(49, 2, 'morning', 0.7),
(50, 2, 'mañana', 0.8),
(51, 3, 'tarde', 0.8),
(52, 4, 'noche', 0.8),
(53, 5, 'registrar', 1),
(54, 5, 'inscribir', 0.9),
(55, 5, 'unirse', 0.8),
(56, 5, 'nueva cuenta', 1),
(57, 6, 'registrarse', 1),
(58, 6, 'inscribirse', 0.9),
(59, 6, 'sign up', 0.8),
(60, 7, 'password', 1),
(61, 7, 'clave', 0.9),
(62, 7, 'recuperar', 0.9),
(63, 7, 'restablecer', 0.9),
(64, 8, 'qué es', 1),
(65, 8, 'plataforma', 0.9),
(66, 8, 'servicio', 0.8),
(67, 9, 'usar', 0.9),
(68, 9, 'utilizar', 0.8),
(69, 9, 'instrucciones', 0.7),
(70, 10, 'precio', 1),
(71, 10, 'costo', 1),
(72, 10, 'suscripción', 0.9),
(73, 10, 'premium', 0.9),
(74, 11, 'crear', 1),
(75, 11, 'lista', 0.9),
(76, 11, 'colección', 0.8),
(77, 12, 'offline', 0.9),
(78, 12, 'sin conexión', 0.9),
(79, 12, 'guardar', 0.8),
(80, 13, 'bitrate', 1),
(81, 13, 'kbps', 0.9),
(82, 13, 'sonido', 0.8),
(83, 14, 'falla', 0.9),
(84, 14, 'bug', 0.8),
(85, 14, 'no funciona', 0.9),
(86, 15, 'ayuda', 1),
(87, 15, 'asistencia', 0.8),
(88, 15, 'no puedo', 0.7),
(89, 16, 'email', 0.8),
(90, 16, 'teléfono', 0.7),
(91, 16, 'chat', 0.7),
(92, 17, 'thank', 0.8),
(93, 17, 'thx', 0.7),
(94, 17, 'te agradezco', 0.9),
(95, 18, 'bye', 0.8),
(96, 18, 'hasta luego', 0.9),
(97, 18, 'nos vemos', 0.8),
(98, 19, 'bye', 0.7),
(99, 19, 'hasta pronto', 0.8),
(100, 19, 'me voy', 0.7),
(101, 1, 'saludar', 0.8),
(102, 1, 'hey', 0.9),
(103, 1, 'qué tal', 0.8),
(104, 2, 'mañana', 0.7),
(105, 3, 'tarde', 0.7),
(106, 4, 'noche', 0.7),
(107, 5, 'registrar', 1),
(108, 5, 'inscribir', 0.9),
(109, 5, 'unirse', 0.8),
(110, 5, 'nueva cuenta', 1),
(111, 6, 'registrarse', 1),
(112, 6, 'inscribirse', 0.9),
(113, 6, 'sign up', 0.8),
(114, 7, 'password', 1),
(115, 7, 'clave', 0.9),
(116, 7, 'recuperar', 0.9),
(117, 7, 'restablecer', 0.9),
(118, 8, 'qué es', 1),
(119, 8, 'plataforma', 0.9),
(120, 8, 'servicio', 0.8),
(121, 9, 'usar', 0.9),
(122, 9, 'utilizar', 0.8),
(123, 9, 'instrucciones', 0.7),
(124, 10, 'precio', 1),
(125, 10, 'costo', 1),
(126, 10, 'suscripción', 0.9),
(127, 10, 'premium', 0.9),
(128, 11, 'crear', 1),
(129, 11, 'lista', 0.9),
(130, 11, 'colección', 0.8),
(131, 12, 'offline', 0.9),
(132, 12, 'sin conexión', 0.9),
(133, 12, 'guardar', 0.8),
(134, 13, 'bitrate', 1),
(135, 13, 'kbps', 0.9),
(136, 13, 'sonido', 0.8),
(137, 14, 'falla', 0.9),
(138, 14, 'bug', 0.8),
(139, 14, 'no funciona', 0.9),
(140, 15, 'ayuda', 1),
(141, 15, 'asistencia', 0.8),
(142, 15, 'no puedo', 0.7),
(143, 16, 'email', 0.8),
(144, 16, 'teléfono', 0.7),
(145, 16, 'chat', 0.7),
(146, 17, 'thank', 0.8),
(147, 17, 'thx', 0.7),
(148, 17, 'te agradezco', 0.9),
(149, 18, 'bye', 0.8),
(150, 18, 'hasta luego', 0.9),
(151, 18, 'nos vemos', 0.8),
(152, 19, 'bye', 0.7),
(153, 19, 'hasta pronto', 0.8),
(154, 19, 'me voy', 0.7);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ai_responses`
--

CREATE TABLE `ai_responses` (
  `id` int(11) NOT NULL,
  `pattern` text NOT NULL COMMENT 'Patrón o pregunta a detectar',
  `response` text NOT NULL COMMENT 'Respuesta del asistente',
  `priority` int(11) NOT NULL DEFAULT 0 COMMENT 'Prioridad de la respuesta (mayor número = mayor prioridad)',
  `category` varchar(50) DEFAULT NULL COMMENT 'Categoría de la respuesta',
  `active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Si la respuesta está activa',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ai_responses`
--

INSERT INTO `ai_responses` (`id`, `pattern`, `response`, `priority`, `category`, `active`, `created_at`, `updated_at`) VALUES
(1, 'hola', '¿En qué puedo ayudarte? Puedo darte información sobre nuestra plataforma, ayudarte a encontrar música o resolver dudas sobre tu cuenta.', 10, 'greeting', 1, '2025-03-24 14:10:54', '2025-03-24 14:10:54'),
(2, 'buenos días', 'Buenos días. ¿En qué puedo ayudarte hoy?', 10, 'greeting', 1, '2025-03-24 14:10:54', '2025-03-24 14:10:54'),
(3, 'buenas tardes', 'Buenas tardes. ¿Cómo puedo asistirte?', 10, 'greeting', 1, '2025-03-24 14:10:54', '2025-03-24 14:10:54'),
(4, 'buenas noches', 'Buenas noches. ¿En qué puedo ayudarte?', 10, 'greeting', 1, '2025-03-24 14:10:54', '2025-03-24 14:10:54'),
(5, 'registro', 'Para crear una cuenta en BassCulture, ve a la página de registro desde el menú principal. Necesitarás proporcionar tu nombre, correo electrónico y crear una contraseña segura. El proceso es rápido y te permitirá acceder a todas nuestras funciones.', 5, 'account', 1, '2025-03-24 14:10:54', '2025-03-24 14:10:54'),
(6, 'crear cuenta', 'Crear una cuenta en BassCulture es muy sencillo. Haz clic en \"Registrarse\", completa tus datos personales, acepta los términos y condiciones, y listo. Una vez registrado, podrás crear playlists, seguir a tus artistas favoritos y disfrutar de música personalizada.', 5, 'account', 1, '2025-03-24 14:10:54', '2025-03-24 14:10:54'),
(7, 'bassculture', 'BassCulture es una plataforma de streaming musical que te permite descubrir, escuchar y compartir música. Ofrecemos millones de canciones, playlists personalizadas y recomendaciones basadas en tus gustos musicales.', 3, 'info', 1, '2025-03-24 14:10:54', '2025-03-24 14:10:54'),
(8, 'funciona', 'BassCulture funciona como una biblioteca musical digital. Puedes buscar canciones, álbumes o artistas, crear playlists personalizadas, y descubrir nueva música a través de nuestras recomendaciones. Todo esto desde cualquier dispositivo con conexión a internet.', 3, 'info', 1, '2025-03-24 14:10:54', '2025-03-24 14:10:54'),
(9, 'planes', 'BassCulture ofrece un plan gratuito con publicidad y funciones básicas. También tenemos planes premium que incluyen reproducción sin anuncios, descarga de música para escuchar sin conexión, y calidad de audio superior. Visita nuestra sección de \"Planes\" para más detalles.', 3, 'info', 1, '2025-03-24 14:10:54', '2025-03-24 14:10:54'),
(10, 'contraseña', 'Si olvidaste tu contraseña, ve a la página de inicio de sesión y haz clic en \"Olvidé mi contraseña\". Ingresa tu correo electrónico y te enviaremos un enlace para restablecerla. Si sigues teniendo problemas, contacta a nuestro soporte técnico.', 5, 'account', 1, '2025-03-24 14:10:54', '2025-03-24 14:10:54'),
(11, 'eliminar cuenta', 'Para eliminar tu cuenta, ve a \"Perfil > Configuración > Cuenta\" y selecciona \"Eliminar cuenta\". Ten en cuenta que esta acción es irreversible y perderás todo tu historial y playlists. Si tienes dudas, contacta a nuestro soporte antes de proceder.', 5, 'account', 1, '2025-03-24 14:10:54', '2025-03-24 14:10:54'),
(12, 'playlist', 'Para crear una playlist, inicia sesión y haz clic en \"Biblioteca\" en el menú lateral. Luego selecciona \"Crear playlist\", asígnale un nombre y comienza a añadir canciones. Puedes hacerla pública o mantenerla privada según tus preferencias.', 4, 'features', 1, '2025-03-24 14:10:54', '2025-03-24 14:10:54'),
(13, 'subir música', 'Si eres artista, puedes subir tu música a BassCulture. Ve a \"Perfil > Artista > Subir música\" y sigue las instrucciones. Asegúrate de tener los derechos de las canciones que subes y proporciona información precisa sobre cada pista.', 4, 'features', 1, '2025-03-24 14:10:54', '2025-03-24 14:10:54'),
(14, 'descargar', 'Con una suscripción premium, puedes descargar música para escucharla sin conexión. Busca el ícono de descarga junto a las canciones, álbumes o playlists que quieras guardar. Las descargas estarán disponibles en la sección \"Tu biblioteca\".', 4, 'features', 1, '2025-03-24 14:10:54', '2025-03-24 14:10:54'),
(15, 'error', 'Lamento que estés experimentando un error. Intenta actualizar la página o reiniciar la aplicación. Si el problema continúa, envía un reporte detallado a soporte@bassculture.com incluyendo el dispositivo que usas y los pasos para reproducir el error.', 2, 'support', 1, '2025-03-24 14:10:54', '2025-03-24 14:10:54'),
(16, 'no funciona', 'Siento que estés teniendo problemas. Primero, verifica tu conexión a internet. Luego, intenta actualizar la página o reiniciar la aplicación. Si el problema persiste, contacta a nuestro soporte técnico con detalles específicos del error.', 2, 'support', 1, '2025-03-24 14:10:54', '2025-03-24 14:10:54'),
(17, 'gracias', 'De nada. Estoy aquí para ayudarte. Si tienes más preguntas, no dudes en consultarme.', 10, 'farewell', 1, '2025-03-24 14:10:54', '2025-03-24 14:10:54'),
(18, 'adiós', '¡Hasta pronto! Disfruta de BassCulture. Estaré aquí cuando necesites ayuda nuevamente.', 10, 'farewell', 1, '2025-03-24 14:10:54', '2025-03-24 14:10:54'),
(19, 'chao', '¡Hasta luego! Disfruta de tu música. Estaré aquí si necesitas más ayuda.', 10, 'farewell', 1, '2025-03-24 14:10:54', '2025-03-24 14:10:54'),
(20, 'hola', '{greeting}¿en qué puedo ayudarte hoy? Puedo recomendarte música, ayudarte con tu cuenta, o responder preguntas sobre BassCulture.', 10, 'greeting', 1, '2025-03-24 14:26:10', '2025-03-24 14:26:10'),
(21, 'buenos días', '{greeting}buenos días. ¿En qué puedo ayudarte hoy?', 10, 'greeting', 1, '2025-03-24 14:26:10', '2025-03-24 14:26:10'),
(22, 'buenas tardes', '{greeting}buenas tardes. ¿Cómo puedo asistirte?', 10, 'greeting', 1, '2025-03-24 14:26:10', '2025-03-24 14:26:10'),
(23, 'buenas noches', '{greeting}buenas noches. ¿En qué puedo ayudarte?', 10, 'greeting', 1, '2025-03-24 14:26:10', '2025-03-24 14:26:10'),
(24, 'cómo me registro', 'Para registrarte en BassCulture, haz clic en el botón \"Registrarse\" en la esquina superior derecha de la página principal. Completa el formulario con tu nombre, correo electrónico y contraseña. Luego, recibirás un correo de confirmación para activar tu cuenta.', 8, 'account', 1, '2025-03-24 14:26:10', '2025-03-24 14:26:10'),
(25, 'crear cuenta', 'Crear una cuenta en BassCulture es muy sencillo. Haz clic en \"Registrarse\", completa tus datos personales, acepta los términos y condiciones, y listo. Una vez registrado, podrás crear playlists, seguir a tus artistas favoritos y disfrutar de música personalizada.', 8, 'account', 1, '2025-03-24 14:26:10', '2025-03-24 14:26:10'),
(26, 'olvidé mi contraseña', 'Si olvidaste tu contraseña, ve a la página de inicio de sesión y haz clic en \"Olvidé mi contraseña\". Ingresa tu correo electrónico y te enviaremos un enlace para restablecerla. Si sigues teniendo problemas, contacta a nuestro soporte técnico.', 8, 'account', 1, '2025-03-24 14:26:10', '2025-03-24 14:26:10'),
(27, 'qué es bassculture', '{greeting}BassCulture es una plataforma de streaming musical que te permite descubrir, escuchar y compartir música. Ofrecemos millones de canciones, playlists personalizadas y recomendaciones basadas en tus gustos musicales.', 7, 'info', 1, '2025-03-24 14:26:10', '2025-03-24 14:26:10'),
(28, 'cómo funciona', '{greeting}BassCulture funciona como una biblioteca musical digital. Puedes buscar canciones, álbumes o artistas, crear playlists personalizadas, y descubrir nueva música a través de nuestras recomendaciones. Todo esto desde cualquier dispositivo con conexión a internet.', 7, 'info', 1, '2025-03-24 14:26:10', '2025-03-24 14:26:10'),
(29, 'planes', '{greeting}BassCulture ofrece un plan gratuito con publicidad y funciones básicas. También tenemos planes premium que incluyen reproducción sin anuncios, descarga de música para escuchar sin conexión, y calidad de audio superior. Visita nuestra sección de \"Planes\" para más detalles.', 7, 'info', 1, '2025-03-24 14:26:10', '2025-03-24 14:26:10'),
(30, 'playlist', '{greeting}para crear una playlist, inicia sesión y haz clic en \"Biblioteca\" en el menú lateral. Luego selecciona \"Crear playlist\", asígnale un nombre y comienza a añadir canciones. Puedes hacerla pública o mantenerla privada según tus preferencias.', 6, 'features', 1, '2025-03-24 14:26:10', '2025-03-24 14:26:10'),
(31, 'descargar música', '{greeting}con una suscripción premium, puedes descargar música para escucharla sin conexión. Busca el ícono de descarga junto a las canciones, álbumes o playlists que quieras guardar. Las descargas estarán disponibles en la sección \"Tu biblioteca\".', 6, 'features', 1, '2025-03-24 14:26:10', '2025-03-24 14:26:10'),
(32, 'calidad de audio', '{greeting}BassCulture ofrece diferentes calidades de audio según tu plan. Los usuarios premium pueden disfrutar de audio en alta definición (hasta 320kbps), mientras que el plan gratuito ofrece calidad estándar (128kbps).', 6, 'features', 1, '2025-03-24 14:26:10', '2025-03-24 14:26:10'),
(33, 'error', 'Lamento que estés experimentando un error. Intenta actualizar la página o reiniciar la aplicación. Si el problema continúa, envía un reporte detallado a soporte@bassculture.com incluyendo el dispositivo que usas y los pasos para reproducir el error.', 5, 'support', 1, '2025-03-24 14:26:10', '2025-03-24 14:26:10'),
(34, 'problema', '{greeting}siento que estés teniendo problemas. ¿Podrías describir con más detalle qué está ocurriendo? Así podré ayudarte mejor o dirigirte al equipo de soporte adecuado.', 5, 'support', 1, '2025-03-24 14:26:10', '2025-03-24 14:26:10'),
(35, 'contactar soporte', 'Puedes contactar a nuestro equipo de soporte enviando un correo a soporte@bassculture.com o a través del formulario de contacto en nuestra página web. El horario de atención es de lunes a viernes de 9:00 a 18:00 horas.', 5, 'support', 1, '2025-03-24 14:26:10', '2025-03-24 14:26:10'),
(36, 'gracias', 'De nada. Estoy aquí para ayudarte. Si tienes más preguntas, no dudes en consultarme.', 10, 'farewell', 1, '2025-03-24 14:26:10', '2025-03-24 14:26:10'),
(37, 'adiós', '¡Hasta pronto! Disfruta de BassCulture. Estaré aquí cuando necesites ayuda nuevamente.', 10, 'farewell', 1, '2025-03-24 14:26:10', '2025-03-24 14:26:10'),
(38, 'chao', '¡Hasta luego! Disfruta de tu música. Estaré aquí si necesitas más ayuda.', 10, 'farewell', 1, '2025-03-24 14:26:10', '2025-03-24 14:26:10'),
(39, 'hola', '{greeting}¿en qué puedo ayudarte hoy? Puedo recomendarte música, ayudarte con tu cuenta, o responder preguntas sobre BassCulture.', 10, 'greeting', 1, '2025-03-27 02:39:35', '2025-03-27 02:39:35'),
(40, 'buenos días', '{greeting}buenos días. ¿En qué puedo ayudarte hoy?', 10, 'greeting', 1, '2025-03-27 02:39:35', '2025-03-27 02:39:35'),
(41, 'buenas tardes', '{greeting}buenas tardes. ¿Cómo puedo asistirte?', 10, 'greeting', 1, '2025-03-27 02:39:35', '2025-03-27 02:39:35'),
(42, 'buenas noches', '{greeting}buenas noches. ¿En qué puedo ayudarte?', 10, 'greeting', 1, '2025-03-27 02:39:35', '2025-03-27 02:39:35'),
(43, 'cómo me registro', 'Para registrarte en BassCulture, haz clic en el botón \"Registrarse\" en la esquina superior derecha de la página principal. Completa el formulario con tu nombre, correo electrónico y contraseña. Luego, recibirás un correo de confirmación para activar tu cuenta.', 8, 'account', 1, '2025-03-27 02:39:35', '2025-03-27 02:39:35'),
(44, 'crear cuenta', 'Crear una cuenta en BassCulture es muy sencillo. Haz clic en \"Registrarse\", completa tus datos personales, acepta los términos y condiciones, y listo. Una vez registrado, podrás crear playlists, seguir a tus artistas favoritos y disfrutar de música personalizada.', 8, 'account', 1, '2025-03-27 02:39:35', '2025-03-27 02:39:35'),
(45, 'olvidé mi contraseña', 'Si olvidaste tu contraseña, ve a la página de inicio de sesión y haz clic en \"Olvidé mi contraseña\". Ingresa tu correo electrónico y te enviaremos un enlace para restablecerla. Si sigues teniendo problemas, contacta a nuestro soporte técnico.', 8, 'account', 1, '2025-03-27 02:39:35', '2025-03-27 02:39:35'),
(46, 'qué es bassculture', '{greeting}BassCulture es una plataforma de streaming musical que te permite descubrir, escuchar y compartir música. Ofrecemos millones de canciones, playlists personalizadas y recomendaciones basadas en tus gustos musicales.', 7, 'info', 1, '2025-03-27 02:39:35', '2025-03-27 02:39:35'),
(47, 'cómo funciona', '{greeting}BassCulture funciona como una biblioteca musical digital. Puedes buscar canciones, álbumes o artistas, crear playlists personalizadas, y descubrir nueva música a través de nuestras recomendaciones. Todo esto desde cualquier dispositivo con conexión a internet.', 7, 'info', 1, '2025-03-27 02:39:35', '2025-03-27 02:39:35'),
(48, 'planes', '{greeting}BassCulture ofrece un plan gratuito con publicidad y funciones básicas. También tenemos planes premium que incluyen reproducción sin anuncios, descarga de música para escuchar sin conexión, y calidad de audio superior. Visita nuestra sección de \"Planes\" para más detalles.', 7, 'info', 1, '2025-03-27 02:39:35', '2025-03-27 02:39:35'),
(49, 'playlist', '{greeting}para crear una playlist, inicia sesión y haz clic en \"Biblioteca\" en el menú lateral. Luego selecciona \"Crear playlist\", asígnale un nombre y comienza a añadir canciones. Puedes hacerla pública o mantenerla privada según tus preferencias.', 6, 'features', 1, '2025-03-27 02:39:35', '2025-03-27 02:39:35'),
(50, 'descargar música', '{greeting}con una suscripción premium, puedes descargar música para escucharla sin conexión. Busca el ícono de descarga junto a las canciones, álbumes o playlists que quieras guardar. Las descargas estarán disponibles en la sección \"Tu biblioteca\".', 6, 'features', 1, '2025-03-27 02:39:35', '2025-03-27 02:39:35'),
(51, 'calidad de audio', '{greeting}BassCulture ofrece diferentes calidades de audio según tu plan. Los usuarios premium pueden disfrutar de audio en alta definición (hasta 320kbps), mientras que el plan gratuito ofrece calidad estándar (128kbps).', 6, 'features', 1, '2025-03-27 02:39:35', '2025-03-27 02:39:35'),
(52, 'error', 'Lamento que estés experimentando un error. Intenta actualizar la página o reiniciar la aplicación. Si el problema continúa, envía un reporte detallado a soporte@bassculture.com incluyendo el dispositivo que usas y los pasos para reproducir el error.', 5, 'support', 1, '2025-03-27 02:39:35', '2025-03-27 02:39:35'),
(53, 'problema', '{greeting}siento que estés teniendo problemas. ¿Podrías describir con más detalle qué está ocurriendo? Así podré ayudarte mejor o dirigirte al equipo de soporte adecuado.', 5, 'support', 1, '2025-03-27 02:39:35', '2025-03-27 02:39:35'),
(54, 'contactar soporte', 'Puedes contactar a nuestro equipo de soporte enviando un correo a soporte@bassculture.com o a través del formulario de contacto en nuestra página web. El horario de atención es de lunes a viernes de 9:00 a 18:00 horas.', 5, 'support', 1, '2025-03-27 02:39:35', '2025-03-27 02:39:35'),
(55, 'gracias', 'De nada. Estoy aquí para ayudarte. Si tienes más preguntas, no dudes en consultarme.', 10, 'farewell', 1, '2025-03-27 02:39:35', '2025-03-27 02:39:35'),
(56, 'adiós', '¡Hasta pronto! Disfruta de BassCulture. Estaré aquí cuando necesites ayuda nuevamente.', 10, 'farewell', 1, '2025-03-27 02:39:35', '2025-03-27 02:39:35'),
(57, 'chao', '¡Hasta luego! Disfruta de tu música. Estaré aquí si necesitas más ayuda.', 10, 'farewell', 1, '2025-03-27 02:39:35', '2025-03-27 02:39:35');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `album`
--

CREATE TABLE `album` (
  `id_album` int(11) NOT NULL,
  `id_artista` int(11) NOT NULL,
  `nombre_album` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `id_genero` int(11) NOT NULL,
  `imagen_album_path` varchar(255) DEFAULT NULL,
  `fecha_lanzamiento` date DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL DEFAULT 9.99
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `album`
--

INSERT INTO `album` (`id_album`, `id_artista`, `nombre_album`, `descripcion`, `id_genero`, `imagen_album_path`, `fecha_lanzamiento`, `precio`) VALUES
(2, 4, 'Bangarang', 'Bangarang de Skrillex, con Sirah, es un llamado a los niños perdidos, así como un estímulo para que se mantengan alborotados y mantengan la cabeza en alto, independientemente de sus situaciones. El coro Bangarang es un grito fuerte y enérgico que representa el espíritu alborotador de los niños perdidos, que se vuelven más alborotadores con cada estrofa. ', 1, 'Portadas_album/portada_6745ef5e07ac8.jpg', '2011-04-04', 39.00),
(3, 3, 'Legends Never Die', 'Es el tercer álbum de estudio del rapero estadounidense Juice Wrld y el primer álbum póstumo después de su muerte por sobredosis de drogas el 8 de diciembre de 2019.1​ Fue lanzado el 10 de julio de 2020 a través de Grade A Productions y distribuido por Interscope Records. El 4 de mayo de 2020, el álbum fue anunciado originalmente por la novia de Juice Wrld, Ally Lotti, bajo el título de The Outsiders. Sin embargo, el título fue cambiado días después.2​', 5, 'Portadas_album/portada_674630aca100e.jpg', '2020-07-10', 59.00),
(5, 3, 'Fighting Dmons', 'Este es el cuarto álbum de estudio del rapero estadounidense Juice WRLD, conocido por su estilo emocional y letras introspectivas. El álbum explora temas como la lucha contra las adicciones, la salud mental, la fama, y la batalla interna contra los \"demonios\" personales. El álbum fue bien recibido por los fans y críticos, quienes elogiaron su autenticidad y la habilidad de Juice WRLD para transformar sus luchas personales en arte. Fighting Demons se siente como un diario emocional, capturando el legado del artista mientras aborda temas universales de lucha y esperanza.', 5, 'Portadas_album/portada_67473dd5286b0.jpg', '2021-12-10', 59.00),
(6, 3, 'Death Race For Love', 'Death Race for Love es el segundo álbum de estudio del rapero Juice WRLD, lanzado el 8 de marzo de 2019. Este proyecto es uno de sus trabajos más ambiciosos, tanto en duración como en temática, y muestra su crecimiento artístico.  El álbum explora temas como el amor, la traición, la autodestrucción, la lucha con adicciones, y la búsqueda de la felicidad. Juice WRLD combina sus experiencias personales con un enfoque narrativo, ofreciendo una perspectiva cruda y emocional.', 5, 'Portadas_album/portada_67473ef0a56c8.jpg', '2019-03-08', 59.00),
(7, 5, 'Dookie', 'Este álbum marcó un punto de inflexión tanto para la banda como para el punk rock, ya que ayudó a popularizar el género a nivel mainstream durante la década de 1990.El álbum aborda temas como la alienación, la ansiedad, las relaciones fallidas, y la rutina de la juventud suburbana. A pesar de su naturaleza ligera y melódica, las letras reflejan una perspectiva introspectiva y, en ocasiones, sarcástica.', 6, 'Portadas_album/portada_6747419e4dcb3.jpg', '1990-02-01', 39.00),
(8, 5, 'American Idiot', 'El álbum cuenta la historia de Jesus of Suburbia, un joven de una ciudad ficticia que lucha con la alienación, el descontento social, y la política estadounidense. A través de canciones interconectadas, el álbum narra su rebelión contra el sistema y su búsqueda de identidad en un mundo caótico. American Idiot no solo redefinió la carrera de Green Day, sino que también revitalizó el punk rock como un medio de protesta y comentario social. A día de hoy, sigue siendo una pieza central en su discografía y un álbum icónico en la historia de la música contemporánea.', 6, 'Portadas_album/portada_6747428e2debe.jpg', '2004-09-21', 40.00),
(23, 14, 'By rhe Way', 'By the Way es el octavo álbum de estudio de Red Hot Chili Peppers, lanzado el 9 de julio de 2002. Este disco representa una evolución significativa en el sonido de la banda, con un enfoque más melódico y menos centrado en el funk-rock que los caracterizaba. Combina elementos de rock alternativo, pop rock, y psicodelia, destacando la habilidad de la banda para reinventarse sin perder su esencia.', 6, 'Portadas_album/portada_6747f7f424e51.jpg', '2002-07-09', 45.00),
(28, 6, 'Minutes to Midnight', 'wjsusq', 6, 'Portadas_album/portada_67491a4d2517b.jpg', '2007-08-14', 40.00),
(29, 3, 'Goodbye &amp; Good Riddance', 'juice wrdl.............ej', 5, 'Portadas_album/portada_67491a9c9e57e.jpg', '2018-05-13', 39.00),
(30, 14, 'Californication', 'rock rock rock', 6, 'Portadas_album/portada_67491c38df99b.jpg', '2002-05-14', 40.00),
(31, 15, 'Beach Love', 'mm,,m,,', 3, 'Portadas_album/portada_674f1a85decad.jpg', '2020-06-07', 20.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `artista`
--

CREATE TABLE `artista` (
  `id_artista` int(11) NOT NULL,
  `usuario` int(11) NOT NULL,
  `foto_path` varchar(255) DEFAULT NULL,
  `popularidad` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `artista`
--

INSERT INTO `artista` (`id_artista`, `usuario`, `foto_path`, `popularidad`) VALUES
(3, 9, 'imagenes_perfiles/profile_67414ca37dd344.41440447.jpg', 0),
(4, 11, 'imagenes_perfiles/profile_6745ee806c1075.10206156.jpeg', 0),
(5, 12, 'imagenes_perfiles/profile_67474108e8d716.02098324.jpg', 0),
(6, 13, 'imagenes_perfiles/profile_674916716e74a1.62585706.jpg', 0),
(14, 14, 'imagenes_perfiles/profile_6747f778aec219.50473713.jpg', 0),
(15, 15, 'imagenes_perfiles/profile_674f1b21579909.95467575.png', 0),
(17, 21, 'imagenes_perfiles/profile_676320a89c0634.43717145.jpg', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `artistas_generos`
--

CREATE TABLE `artistas_generos` (
  `artista_id` int(11) NOT NULL,
  `genero_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `canciones`
--

CREATE TABLE `canciones` (
  `id_cancion` int(11) NOT NULL,
  `id_album` int(11) NOT NULL,
  `id_artista` int(11) NOT NULL,
  `nombre_cancion` varchar(100) NOT NULL,
  `cancion_path` varchar(255) NOT NULL,
  `precio` decimal(10,2) NOT NULL DEFAULT 0.99,
  `fecha_lanzamiento` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `canciones`
--

INSERT INTO `canciones` (`id_cancion`, `id_album`, `id_artista`, `nombre_cancion`, `cancion_path`, `precio`, `fecha_lanzamiento`) VALUES
(5, 2, 4, 'beautiful-people-obskur-remix.mp3', 'uploads/Electronica/Bangarang/6745ef5e08d6e.mp3', 0.99, NULL),
(6, 2, 4, 'provenance-128-ytshorts.savetube.me.mp3', 'uploads/Electronica/Bangarang/6745ef5e0a048.mp3', 0.99, NULL),
(7, 3, 3, '01 - Anxiety (Intro).mp3', 'uploads/Rap/Legends Never Die/674630aca7414.mp3', 0.99, NULL),
(8, 3, 3, '02 - Conversations.mp3', 'uploads/Rap/Legends Never Die/674630aca9542.mp3', 0.99, NULL),
(9, 3, 3, '03 - Titanic.mp3', 'uploads/Rap/Legends Never Die/674630acaa6dc.mp3', 0.99, NULL),
(10, 3, 3, '04 - Bad Energy.mp3', 'uploads/Rap/Legends Never Die/674630acacebc.mp3', 0.99, NULL),
(11, 3, 3, '05 - Righteous.mp3', 'uploads/Rap/Legends Never Die/674630acadfde.mp3', 0.99, NULL),
(12, 3, 3, '06 - Blood On My Jeans.mp3', 'uploads/Rap/Legends Never Die/674630acaf4bd.mp3', 0.99, NULL),
(13, 3, 3, '07 - Smile.mp3', 'uploads/Rap/Legends Never Die/674630acb08b4.mp3', 0.99, NULL),
(14, 3, 3, '08 - Tell Me U Luv Me.mp3', 'uploads/Rap/Legends Never Die/674630acb1bf4.mp3', 0.99, NULL),
(15, 3, 3, '09 - Hate The Other Side.mp3', 'uploads/Rap/Legends Never Die/674630acb2f16.mp3', 0.99, NULL),
(16, 3, 3, '10 - Get Through It (Interlude).mp3', 'uploads/Rap/Legends Never Die/674630acb40fb.mp3', 0.99, NULL),
(17, 3, 3, '11 - Life\'s A Mess.mp3', 'uploads/Rap/Legends Never Die/674630acb527d.mp3', 0.99, NULL),
(18, 3, 3, '12 - Come & Go.mp3', 'uploads/Rap/Legends Never Die/674630acb645c.mp3', 0.99, NULL),
(19, 3, 3, '13 - I Want It.mp3', 'uploads/Rap/Legends Never Die/674630acb75b1.mp3', 0.99, NULL),
(20, 3, 3, '14 - Fighting Demons.mp3', 'uploads/Rap/Legends Never Die/674630acb9359.mp3', 0.99, NULL),
(21, 3, 3, '15 - Wishing Well.mp3', 'uploads/Rap/Legends Never Die/674630acba839.mp3', 0.99, NULL),
(22, 3, 3, '16 - Screw Juice.mp3', 'uploads/Rap/Legends Never Die/674630acbb8c4.mp3', 0.99, NULL),
(23, 3, 3, '17 - Up Up And Away.mp3', 'uploads/Rap/Legends Never Die/674630acbc878.mp3', 0.99, NULL),
(24, 3, 3, '18 - The Man, The Myth, The Legend (Interlude).mp3', 'uploads/Rap/Legends Never Die/674630acbdb19.mp3', 0.99, NULL),
(25, 3, 3, '19 - Stay High.mp3', 'uploads/Rap/Legends Never Die/674630acbebb7.mp3', 0.99, NULL),
(26, 3, 3, '20 - Can\'t Die.mp3', 'uploads/Rap/Legends Never Die/674630acbfeba.mp3', 0.99, NULL),
(27, 3, 3, '21 - Man Of The Year.mp3', 'uploads/Rap/Legends Never Die/674630acc1049.mp3', 0.99, NULL),
(28, 3, 3, '22 - Juice WRLD Speaks From Heaven (Outro).mp3', 'uploads/Rap/Legends Never Die/674630acc228b.mp3', 0.99, NULL),
(46, 5, 3, '01 - Burn.mp3', 'uploads/Rap/Fighting Dmons/67473dd52cb66.mp3', 0.99, NULL),
(47, 5, 3, '02 - Already Dead.mp3', 'uploads/Rap/Fighting Dmons/67473dd52eaf5.mp3', 0.99, NULL),
(48, 5, 3, '03 - Cigarettes.mp3', 'uploads/Rap/Fighting Dmons/67473dd5300ae.mp3', 0.99, NULL),
(49, 5, 3, '04 - You Wouldn\'t Understand.mp3', 'uploads/Rap/Fighting Dmons/67473dd531812.mp3', 0.99, NULL),
(50, 5, 3, '05 - Sometimes.mp3', 'uploads/Rap/Fighting Dmons/67473dd532b7c.mp3', 0.99, NULL),
(51, 5, 3, '06 - Wandered To LA.mp3', 'uploads/Rap/Fighting Dmons/67473dd533fdd.mp3', 0.99, NULL),
(52, 5, 3, '07 - Eminem Speaks.mp3', 'uploads/Rap/Fighting Dmons/67473dd535711.mp3', 0.99, NULL),
(53, 5, 3, '08 - Rockstar In His Prime.mp3', 'uploads/Rap/Fighting Dmons/67473dd536dad.mp3', 0.99, NULL),
(54, 5, 3, '09 - Doom.mp3', 'uploads/Rap/Fighting Dmons/67473dd539c4f.mp3', 0.99, NULL),
(55, 5, 3, '10 - Go Hard 2.0.mp3', 'uploads/Rap/Fighting Dmons/67473dd53b006.mp3', 0.99, NULL),
(56, 5, 3, '11 - Juice WRLD Speaks.mp3', 'uploads/Rap/Fighting Dmons/67473dd53c441.mp3', 0.99, NULL),
(57, 5, 3, '12 - Not Enough.mp3', 'uploads/Rap/Fighting Dmons/67473dd53d9b6.mp3', 0.99, NULL),
(58, 5, 3, '13 - Feline.mp3', 'uploads/Rap/Fighting Dmons/67473dd53edce.mp3', 0.99, NULL),
(59, 5, 3, '14 - Relocate.mp3', 'uploads/Rap/Fighting Dmons/67473dd5409f6.mp3', 0.99, NULL),
(60, 5, 3, '15 - Juice WRLD Speaks 2.mp3', 'uploads/Rap/Fighting Dmons/67473dd54205d.mp3', 0.99, NULL),
(61, 5, 3, '16 - Until The Plug Comes Back Around.mp3', 'uploads/Rap/Fighting Dmons/67473dd5434c8.mp3', 0.99, NULL),
(62, 5, 3, '17 - From My Window.mp3', 'uploads/Rap/Fighting Dmons/67473dd544b8c.mp3', 0.99, NULL),
(63, 5, 3, '18 - Girl Of My Dreams.mp3', 'uploads/Rap/Fighting Dmons/67473dd54648a.mp3', 0.99, NULL),
(64, 5, 3, '19 - Feel Alone.mp3', 'uploads/Rap/Fighting Dmons/67473dd5478ec.mp3', 0.99, NULL),
(65, 5, 3, '20 - Go Hard.mp3', 'uploads/Rap/Fighting Dmons/67473dd549122.mp3', 0.99, NULL),
(66, 5, 3, '21 - My Life In A Nutshell.mp3', 'uploads/Rap/Fighting Dmons/67473dd54ab16.mp3', 0.99, NULL),
(67, 5, 3, '22 - Rich And Blind.mp3', 'uploads/Rap/Fighting Dmons/67473dd54be7c.mp3', 0.99, NULL),
(68, 5, 3, '23 - Legends.mp3', 'uploads/Rap/Fighting Dmons/67473dd54d331.mp3', 0.99, NULL),
(69, 6, 3, '01 - Empty.mp3', 'uploads/Rap/Death Race For Love/67473ef0a7374.mp3', 0.99, NULL),
(70, 6, 3, '02 - Maze.mp3', 'uploads/Rap/Death Race For Love/67473ef0aa7bb.mp3', 0.99, NULL),
(71, 6, 3, '03 - HeMotions.mp3', 'uploads/Rap/Death Race For Love/67473ef0abbf9.mp3', 0.99, NULL),
(72, 6, 3, '04 - Demonz (Interlude).mp3', 'uploads/Rap/Death Race For Love/67473ef0acff2.mp3', 0.99, NULL),
(73, 6, 3, '05 - Fast.mp3', 'uploads/Rap/Death Race For Love/67473ef0ae7ed.mp3', 0.99, NULL),
(74, 6, 3, '06 - Hear Me Calling.mp3', 'uploads/Rap/Death Race For Love/67473ef0b01ba.mp3', 0.99, NULL),
(75, 6, 3, '07 - Big.mp3', 'uploads/Rap/Death Race For Love/67473ef0b1bf0.mp3', 0.99, NULL),
(76, 6, 3, '08 - Robbery.mp3', 'uploads/Rap/Death Race For Love/67473ef0b302f.mp3', 0.99, NULL),
(77, 6, 3, '09 - Flaws And Sins.mp3', 'uploads/Rap/Death Race For Love/67473ef0b42be.mp3', 0.99, NULL),
(78, 6, 3, '10 - Feeling.mp3', 'uploads/Rap/Death Race For Love/67473ef0b581c.mp3', 0.99, NULL),
(79, 6, 3, '11 - Bandit.mp3', 'uploads/Rap/Death Race For Love/67473ef0b83dc.mp3', 0.99, NULL),
(80, 6, 3, '12 - Syphilis.mp3', 'uploads/Rap/Death Race For Love/67473ef0b9b68.mp3', 0.99, NULL),
(81, 6, 3, '13 - Who Shot Cupid_.mp3', 'uploads/Rap/Death Race For Love/67473ef0bb56a.mp3', 0.99, NULL),
(82, 6, 3, '14 - Ring Ring.mp3', 'uploads/Rap/Death Race For Love/67473ef0bc7ea.mp3', 0.99, NULL),
(83, 6, 3, '15 - Desire.mp3', 'uploads/Rap/Death Race For Love/67473ef0bda9e.mp3', 0.99, NULL),
(84, 6, 3, '16 - Out My Way.mp3', 'uploads/Rap/Death Race For Love/67473ef0bedcc.mp3', 0.99, NULL),
(85, 6, 3, '17 - The Bees Knees.mp3', 'uploads/Rap/Death Race For Love/67473ef0bfff3.mp3', 0.99, NULL),
(86, 6, 3, '18 - ON GOD.mp3', 'uploads/Rap/Death Race For Love/67473ef0c11a9.mp3', 0.99, NULL),
(87, 6, 3, '19 - 10 Feet.mp3', 'uploads/Rap/Death Race For Love/67473ef0c273a.mp3', 0.99, NULL),
(88, 6, 3, '20 - Won’t Let Go.mp3', 'uploads/Rap/Death Race For Love/67473ef0c4267.mp3', 0.99, NULL),
(89, 6, 3, '21 - She’s The One.mp3', 'uploads/Rap/Death Race For Love/67473ef0c5d8b.mp3', 0.99, NULL),
(90, 6, 3, '22 - Rider.mp3', 'uploads/Rap/Death Race For Love/67473ef0c71a0.mp3', 0.99, NULL),
(91, 6, 3, '23 - Make Believe.mp3', 'uploads/Rap/Death Race For Love/67473ef0c835d.mp3', 0.99, NULL),
(92, 7, 5, '01 - Burnout.mp3', 'uploads/Rock/Dookie/6747419e4f4ce.mp3', 0.99, NULL),
(93, 7, 5, '02 - Having a Blast.mp3', 'uploads/Rock/Dookie/6747419e5080b.mp3', 0.99, NULL),
(94, 7, 5, '03 - Chump.mp3', 'uploads/Rock/Dookie/6747419e51b17.mp3', 0.99, NULL),
(95, 7, 5, '04 - Longview.mp3', 'uploads/Rock/Dookie/6747419e52fde.mp3', 0.99, NULL),
(96, 7, 5, '05 - Welcome to Paradise.mp3', 'uploads/Rock/Dookie/6747419e544dd.mp3', 0.99, NULL),
(97, 7, 5, '06 - Pulling Teeth.mp3', 'uploads/Rock/Dookie/6747419e55f60.mp3', 0.99, NULL),
(98, 7, 5, '07 - Basket Case.mp3', 'uploads/Rock/Dookie/6747419e57602.mp3', 0.99, NULL),
(99, 7, 5, '08 - She.mp3', 'uploads/Rock/Dookie/6747419e594a6.mp3', 0.99, NULL),
(100, 7, 5, '09 - Sassafras Roots.mp3', 'uploads/Rock/Dookie/6747419e5a80a.mp3', 0.99, NULL),
(101, 7, 5, '10 - When I Come Around.mp3', 'uploads/Rock/Dookie/6747419e5bbb7.mp3', 0.99, NULL),
(102, 7, 5, '11 - Coming Clean.mp3', 'uploads/Rock/Dookie/6747419e5ce8c.mp3', 0.99, NULL),
(103, 7, 5, '12 - Emenius Sleepus.mp3', 'uploads/Rock/Dookie/6747419e5e4d3.mp3', 0.99, NULL),
(104, 7, 5, '13 - In the End.mp3', 'uploads/Rock/Dookie/6747419e615c4.mp3', 0.99, NULL),
(105, 7, 5, '14 - F.O.D.mp3', 'uploads/Rock/Dookie/6747419e62b37.mp3', 0.99, NULL),
(106, 7, 5, '15 - All by Myself.mp3', 'uploads/Rock/Dookie/6747419e6404a.mp3', 0.99, NULL),
(107, 8, 5, '01 - American Idiot.mp3', 'uploads/Rock/American Idiot/6747428e2f9ff.mp3', 0.99, NULL),
(108, 8, 5, '02 - Jesus of Suburbia.mp3', 'uploads/Rock/American Idiot/6747428e326fb.mp3', 0.99, NULL),
(109, 8, 5, '03 - Holiday _ Boulevard of Broken Dreams.mp3', 'uploads/Rock/American Idiot/6747428e33ad1.mp3', 0.99, NULL),
(110, 8, 5, '04 - Are We the Waiting _ St. Jimmy.mp3', 'uploads/Rock/American Idiot/6747428e3505e.mp3', 0.99, NULL),
(111, 8, 5, '05 - Give Me Novacaine _ She\'s a Rebel.mp3', 'uploads/Rock/American Idiot/6747428e36741.mp3', 0.99, NULL),
(112, 8, 5, '06 - Extraordinary Girl _ Letterbomb.mp3', 'uploads/Rock/American Idiot/6747428e37dfd.mp3', 0.99, NULL),
(113, 8, 5, '07 - Wake Me up When September Ends.mp3', 'uploads/Rock/American Idiot/6747428e39253.mp3', 0.99, NULL),
(114, 8, 5, '08 - Homecoming.mp3', 'uploads/Rock/American Idiot/6747428e3a5bc.mp3', 0.99, NULL),
(115, 8, 5, '09 - Whatsername.mp3', 'uploads/Rock/American Idiot/6747428e3b9d9.mp3', 0.99, NULL),
(116, 8, 5, '10 - Too Much Too Soon.mp3', 'uploads/Rock/American Idiot/6747428e3cd0f.mp3', 0.99, NULL),
(117, 8, 5, '11 - Shoplifter.mp3', 'uploads/Rock/American Idiot/6747428e3fc1f.mp3', 0.99, NULL),
(118, 8, 5, '12 - Governator.mp3', 'uploads/Rock/American Idiot/6747428e41002.mp3', 0.99, NULL),
(163, 23, 14, '01 - By the Way.mp3', 'uploads/Rock/By rhe Way/6747f7f425417.mp3', 0.99, NULL),
(164, 23, 14, '02 - Universally Speaking.mp3', 'uploads/Rock/By rhe Way/6747f7f425899.mp3', 0.99, NULL),
(165, 23, 14, '03 - This Is the Place.mp3', 'uploads/Rock/By rhe Way/6747f7f425ca2.mp3', 0.99, NULL),
(166, 23, 14, '04 - Dosed.mp3', 'uploads/Rock/By rhe Way/6747f7f426081.mp3', 0.99, NULL),
(167, 23, 14, '05 - Don\'t Forget Me.mp3', 'uploads/Rock/By rhe Way/6747f7f4265a6.mp3', 0.99, NULL),
(168, 23, 14, '06 - The Zephyr Song.mp3', 'uploads/Rock/By rhe Way/6747f7f426ad4.mp3', 0.99, NULL),
(169, 23, 14, '07 - Can\'t Stop.mp3', 'uploads/Rock/By rhe Way/6747f7f427066.mp3', 0.99, NULL),
(170, 23, 14, '08 - I Could Die for You.mp3', 'uploads/Rock/By rhe Way/6747f7f42745a.mp3', 0.99, NULL),
(171, 23, 14, '09 - Midnight.mp3', 'uploads/Rock/By rhe Way/6747f7f427861.mp3', 0.99, NULL),
(172, 23, 14, '10 - Throw Away Your Television.mp3', 'uploads/Rock/By rhe Way/6747f7f427be9.mp3', 0.99, NULL),
(173, 23, 14, '11 - Cabron.mp3', 'uploads/Rock/By rhe Way/6747f7f429ba3.mp3', 0.99, NULL),
(174, 23, 14, '12 - Tear.mp3', 'uploads/Rock/By rhe Way/6747f7f429f0b.mp3', 0.99, NULL),
(175, 23, 14, '13 - On Mercury.mp3', 'uploads/Rock/By rhe Way/6747f7f42a257.mp3', 0.99, NULL),
(176, 23, 14, '14 - Minor Thing.mp3', 'uploads/Rock/By rhe Way/6747f7f42a7dc.mp3', 0.99, NULL),
(177, 23, 14, '15 - Warm Tape.mp3', 'uploads/Rock/By rhe Way/6747f7f42ac9f.mp3', 0.99, NULL),
(178, 23, 14, '16 - Venice Queen.mp3', 'uploads/Rock/By rhe Way/6747f7f42b156.mp3', 0.99, NULL),
(179, 23, 14, '17 - Runaway (2006 Remaster).mp3', 'uploads/Rock/By rhe Way/6747f7f42b63a.mp3', 0.99, NULL),
(180, 23, 14, '18 - Bicycle Song (2006 Remaster).mp3', 'uploads/Rock/By rhe Way/6747f7f42baa1.mp3', 0.99, NULL),
(210, 28, 6, '01 - Wake.mp3', 'uploads/Rock/Minutes to Midnight/67491a4d25bd3.mp3', 0.99, NULL),
(211, 28, 6, '02 - Given Up.mp3', 'uploads/Rock/Minutes to Midnight/67491a4d26444.mp3', 0.99, NULL),
(212, 28, 6, '03 - Leave Out All The Rest.mp3', 'uploads/Rock/Minutes to Midnight/67491a4d26be6.mp3', 0.99, NULL),
(213, 28, 6, '04 - Bleed It Out.mp3', 'uploads/Rock/Minutes to Midnight/67491a4d273a4.mp3', 0.99, NULL),
(214, 28, 6, '05 - Shadow of the Day.mp3', 'uploads/Rock/Minutes to Midnight/67491a4d27a5d.mp3', 0.99, NULL),
(215, 28, 6, '06 - What I\'ve Done.mp3', 'uploads/Rock/Minutes to Midnight/67491a4d28066.mp3', 0.99, NULL),
(216, 28, 6, '07 - Hands Held High.mp3', 'uploads/Rock/Minutes to Midnight/67491a4d287d5.mp3', 0.99, NULL),
(217, 28, 6, '08 - No More Sorrow.mp3', 'uploads/Rock/Minutes to Midnight/67491a4d28e32.mp3', 0.99, NULL),
(218, 28, 6, '09 - Valentine\'s Day.mp3', 'uploads/Rock/Minutes to Midnight/67491a4d2938b.mp3', 0.99, NULL),
(219, 28, 6, '10 - In Between.mp3', 'uploads/Rock/Minutes to Midnight/67491a4d29900.mp3', 0.99, NULL),
(220, 28, 6, '11 - In Pieces.mp3', 'uploads/Rock/Minutes to Midnight/67491a4d2a137.mp3', 0.99, NULL),
(221, 28, 6, '12 - The Little Things Give You Away.mp3', 'uploads/Rock/Minutes to Midnight/67491a4d2a704.mp3', 0.99, NULL),
(222, 29, 3, '01 - Intro.mp3', 'uploads/Rap/Goodbye &amp; Good Riddance/67491a9c9ed8f.mp3', 0.99, NULL),
(223, 29, 3, '02 - All Girls Are The Same.mp3', 'uploads/Rap/Goodbye &amp; Good Riddance/67491a9ca1328.mp3', 0.99, NULL),
(224, 29, 3, '03 - Lucid Dreams.mp3', 'uploads/Rap/Goodbye &amp; Good Riddance/67491a9ca19b2.mp3', 0.99, NULL),
(225, 29, 3, '04 - Wasted.mp3', 'uploads/Rap/Goodbye &amp; Good Riddance/67491a9ca3d10.mp3', 0.99, NULL),
(226, 29, 3, '05 - Armed And Dangerous.mp3', 'uploads/Rap/Goodbye &amp; Good Riddance/67491a9ca433c.mp3', 0.99, NULL),
(227, 29, 3, '06 - Black & White.mp3', 'uploads/Rap/Goodbye &amp; Good Riddance/67491a9ca497a.mp3', 0.99, NULL),
(228, 29, 3, '07 - Lean Wit Me.mp3', 'uploads/Rap/Goodbye &amp; Good Riddance/67491a9ca508b.mp3', 0.99, NULL),
(229, 29, 3, '08 - I\'ll Be Fine.mp3', 'uploads/Rap/Goodbye &amp; Good Riddance/67491a9ca55dd.mp3', 0.99, NULL),
(230, 29, 3, '09 - Used To.mp3', 'uploads/Rap/Goodbye &amp; Good Riddance/67491a9ca5b2b.mp3', 0.99, NULL),
(231, 29, 3, '10 - Candles.mp3', 'uploads/Rap/Goodbye &amp; Good Riddance/67491a9ca610b.mp3', 0.99, NULL),
(232, 29, 3, '11 - Scared Of Love (with instrumental by Ghost Loft).mp3', 'uploads/Rap/Goodbye &amp; Good Riddance/67491a9ca66db.mp3', 0.99, NULL),
(233, 29, 3, '12 - Hurt Me.mp3', 'uploads/Rap/Goodbye &amp; Good Riddance/67491a9ca6c8a.mp3', 0.99, NULL),
(234, 29, 3, '13 - I\'m Still.mp3', 'uploads/Rap/Goodbye &amp; Good Riddance/67491a9ca7265.mp3', 0.99, NULL),
(235, 29, 3, '14 - End Of The Road.mp3', 'uploads/Rap/Goodbye &amp; Good Riddance/67491a9ca7911.mp3', 0.99, NULL),
(236, 29, 3, '15 - Long Gone.mp3', 'uploads/Rap/Goodbye &amp; Good Riddance/67491a9ca803a.mp3', 0.99, NULL),
(237, 29, 3, '16 - Betrayal (Skit).mp3', 'uploads/Rap/Goodbye &amp; Good Riddance/67491a9ca8795.mp3', 0.99, NULL),
(238, 29, 3, '17 - Karma (Skit).mp3', 'uploads/Rap/Goodbye &amp; Good Riddance/67491a9ca8e42.mp3', 0.99, NULL),
(239, 30, 14, '01 - Around the World.mp3', 'uploads/Rock/Californication/67491c38e025b.mp3', 0.99, NULL),
(240, 30, 14, '02 - Parallel Universe.mp3', 'uploads/Rock/Californication/67491c38e0853.mp3', 0.99, NULL),
(241, 30, 14, '03 - Scar Tissue.mp3', 'uploads/Rock/Californication/67491c38e2863.mp3', 0.99, NULL),
(242, 30, 14, '04 - Otherside.mp3', 'uploads/Rock/Californication/67491c38e2dcd.mp3', 0.99, NULL),
(243, 30, 14, '05 - Get on Top.mp3', 'uploads/Rock/Californication/67491c38e321f.mp3', 0.99, NULL),
(244, 30, 14, '06 - Californication.mp3', 'uploads/Rock/Californication/67491c38e36ca.mp3', 0.99, NULL),
(245, 30, 14, '07 - Easily.mp3', 'uploads/Rock/Californication/67491c38e3dad.mp3', 0.99, NULL),
(246, 30, 14, '08 - Porcelain.mp3', 'uploads/Rock/Californication/67491c38e4300.mp3', 0.99, NULL),
(247, 30, 14, '09 - Emit Remmus.mp3', 'uploads/Rock/Californication/67491c38e48a3.mp3', 0.99, NULL),
(248, 30, 14, '10 - I Like Dirt.mp3', 'uploads/Rock/Californication/67491c38e4dbf.mp3', 0.99, NULL),
(249, 30, 14, '11 - This Velvet Glove.mp3', 'uploads/Rock/Californication/67491c38e52ea.mp3', 0.99, NULL),
(250, 30, 14, '12 - Savior.mp3', 'uploads/Rock/Californication/67491c38e57e7.mp3', 0.99, NULL),
(251, 30, 14, '13 - Purple Stain.mp3', 'uploads/Rock/Californication/67491c38e5be5.mp3', 0.99, NULL),
(252, 30, 14, '14 - Right on Time.mp3', 'uploads/Rock/Californication/67491c38e6078.mp3', 0.99, NULL),
(253, 30, 14, '15 - Road Trippin\'.mp3', 'uploads/Rock/Californication/67491c38e6581.mp3', 0.99, NULL),
(254, 30, 14, '16 - Fat Dance (2006 Remaster).mp3', 'uploads/Rock/Californication/67491c38e6ab7.mp3', 0.99, NULL),
(255, 30, 14, '17 - Over Funk (2006 Remaster).mp3', 'uploads/Rock/Californication/67491c38e6fa2.mp3', 0.99, NULL),
(256, 30, 14, '18 - Quixoticelixer (2006 Remaster).mp3', 'uploads/Rock/Californication/67491c38e75db.mp3', 0.99, NULL),
(257, 31, 15, '01 - Vacaciones.mp3', 'uploads/Indie/Beach Love/674f1a85dfcff.mp3', 0.99, NULL),
(258, 31, 15, '02 - Michelove.mp3', 'uploads/Indie/Beach Love/674f1a85e05e2.mp3', 0.99, NULL),
(259, 31, 15, '03 - Summertime Again.mp3', 'uploads/Indie/Beach Love/674f1a85e0bd2.mp3', 0.99, NULL),
(260, 31, 15, '04 - Break My Heart.mp3', 'uploads/Indie/Beach Love/674f1a85e12f2.mp3', 0.99, NULL),
(261, 31, 15, '05 - Nothing Wrong.mp3', 'uploads/Indie/Beach Love/674f1a85e1a05.mp3', 0.99, NULL),
(262, 31, 15, '06 - Alv Bye.mp3', 'uploads/Indie/Beach Love/674f1a85e20b1.mp3', 0.99, NULL),
(263, 31, 15, '07 - Valentine\'s Day.mp3', 'uploads/Indie/Beach Love/674f1a85e26c5.mp3', 0.99, NULL),
(264, 31, 15, '08 - Valentine\'s Day (Acústico).mp3', 'uploads/Indie/Beach Love/674f1a85e2b8f.mp3', 0.99, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carrito`
--

CREATE TABLE `carrito` (
  `id_carrito` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `tipo_producto` enum('sencillo','album','cancion') NOT NULL,
  `cantidad` int(11) NOT NULL DEFAULT 1,
  `fecha_agregado` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `carrito`
--

INSERT INTO `carrito` (`id_carrito`, `id_usuario`, `id_producto`, `tipo_producto`, `cantidad`, `fecha_agregado`) VALUES
(1, 12, 1, 'sencillo', 1, '2024-12-05 03:13:36'),
(2, 12, 2, 'sencillo', 1, '2024-12-05 03:13:41'),
(3, 12, 3, 'sencillo', 1, '2024-12-05 03:14:14'),
(4, 12, 4, 'sencillo', 1, '2024-12-05 03:23:32'),
(11, 9, 1, 'sencillo', 1, '2024-12-05 04:21:01');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente`
--

CREATE TABLE `cliente` (
  `id_cliente` int(11) NOT NULL,
  `id_artista` int(11) DEFAULT NULL,
  `id_cancion` int(11) DEFAULT NULL,
  `id_album` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compras`
--

CREATE TABLE `compras` (
  `id_compra` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `tipo_producto` enum('sencillo','album','cancion') NOT NULL,
  `id_producto` int(11) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `fecha_compra` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `compras`
--

INSERT INTO `compras` (`id_compra`, `id_usuario`, `tipo_producto`, `id_producto`, `precio`, `fecha_compra`) VALUES
(1, 21, 'album', 7, 39.00, '2025-04-02 06:50:21'),
(2, 21, 'album', 5, 59.00, '2025-04-02 06:50:21'),
(3, 21, 'album', 23, 45.00, '2025-04-03 05:37:41');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `genero`
--

CREATE TABLE `genero` (
  `id_genero` int(11) NOT NULL,
  `nombre_genero` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `genero`
--

INSERT INTO `genero` (`id_genero`, `nombre_genero`) VALUES
(1, 'Electronica'),
(2, 'Hip Hop'),
(3, 'Indie'),
(4, 'Pop'),
(5, 'Rap'),
(6, 'Rock');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `me_gusta`
--

CREATE TABLE `me_gusta` (
  `id_me_gusta` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_item` int(11) NOT NULL,
  `tipo_item` enum('album','cancion','sencillo','artista') NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `me_gusta`
--

INSERT INTO `me_gusta` (`id_me_gusta`, `id_usuario`, `id_item`, `tipo_item`, `fecha`) VALUES
(7, 21, 85, 'cancion', '2025-04-08 15:57:18'),
(8, 21, 248, 'cancion', '2025-04-08 15:57:20'),
(9, 21, 65, 'cancion', '2025-04-08 15:57:21');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `playlists`
--

CREATE TABLE `playlists` (
  `id_playlist` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `nombre_playlist` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `imagen_playlist` varchar(255) DEFAULT 'default_playlist.jpg',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `es_publica` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `playlist_canciones`
--

CREATE TABLE `playlist_canciones` (
  `id_playlist_cancion` int(11) NOT NULL,
  `id_playlist` int(11) NOT NULL,
  `tipo_cancion` enum('sencillo','album_cancion') NOT NULL,
  `id_cancion` int(11) NOT NULL,
  `fecha_agregado` timestamp NOT NULL DEFAULT current_timestamp(),
  `orden` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `preferencias_musicales`
--

CREATE TABLE `preferencias_musicales` (
  `id_preferencia` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_genero` int(11) NOT NULL,
  `nivel_interes` int(11) NOT NULL DEFAULT 5,
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `preferencias_musicales`
--

INSERT INTO `preferencias_musicales` (`id_preferencia`, `id_usuario`, `id_genero`, `nivel_interes`, `fecha_actualizacion`) VALUES
(12, 21, 1, 10, '2025-03-27 20:31:18'),
(13, 21, 2, 3, '2025-03-27 20:31:18'),
(14, 21, 3, 4, '2025-03-27 20:31:18'),
(15, 21, 4, 5, '2025-03-27 20:31:18'),
(16, 21, 5, 10, '2025-03-27 20:31:18'),
(17, 21, 6, 10, '2025-03-27 20:31:18');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sencillos`
--

CREATE TABLE `sencillos` (
  `id_sencillo` int(11) NOT NULL,
  `id_artista` int(11) NOT NULL,
  `nombre_sencillo` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `id_genero` int(11) NOT NULL,
  `imagen_sencillo_path` varchar(255) DEFAULT NULL,
  `cancion_path` varchar(255) NOT NULL,
  `fecha_lanzamiento` date DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL DEFAULT 0.99
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `sencillos`
--

INSERT INTO `sencillos` (`id_sencillo`, `id_artista`, `nombre_sencillo`, `descripcion`, `id_genero`, `imagen_sencillo_path`, `cancion_path`, `fecha_lanzamiento`, `precio`) VALUES
(1, 3, 'sky city', 'sencillo el mejor sencillo', 5, 'Portadas_sencillos/portada_6745198e34826.jpg', 'uploads/sencillos/Rap/6745198e34a1c.mp3', '2024-03-01', 3.00),
(2, 3, 'Lucid Dreams', 'el mejor sencillo de los mejores', 5, 'Portadas_sencillos/portada_674896c3ec29b.jpg', 'uploads/sencillos/Rap/674896c3ec589.mp3', '2020-11-14', 2.00),
(4, 15, 'Morir de Amor </3', 'Cancion super Bonita, subida por melted', 3, 'Portadas_sencillos/portada_674f208917096.png', 'uploads/sencillos/675249704cc16.mp3', '2021-01-20', 1.00),
(5, 15, 'Dime Que Me Extrañas', 'cancion bonmita nnnn\r\n', 3, 'Portadas_sencillos/portada_67524516375b8.png', 'uploads/sencillos/675248c3f2ecf.mp3', '2018-08-13', 2.00),
(8, 17, 'juego', 'prueba', 1, 'Portadas_sencillos/portada_6762f88f445f8.gif', 'uploads/sencillos/Electronica/6762f88f4479b.mp3', '2024-12-18', 0.00),
(9, 17, 'sonido', 'prueba', 5, 'Portadas_sencillos/portada_6762fa822d2a8.jpeg', 'uploads/sencillos/Rap/6762fa822d5be.mp3', '2024-12-04', 5.00),
(10, 17, 'yolo', 'prpr', 1, 'Portadas_sencillos/portada_6762fedab3c24.jpeg', 'uploads/sencillos/Electronica/6762fedab3dbf.mp3', '2024-01-31', 0.00),
(11, 17, 'tris', 'ttt', 1, 'Portadas_sencillos/portada_6763066de9c74.jpg', 'uploads/sencillos/Electronica/6763066de9e32.wav', '2024-12-02', 0.00),
(12, 17, 'popo', 'ujuju', 3, 'Portadas_sencillos/portada_67630e17582c8.jpg', 'uploads/sencillos/Indie/67630e175843a.mp3', '2022-05-11', 1.00),
(16, 17, '99', '99', 1, 'Portadas_sencillos/portada_67cf333bb99ae.gif', 'uploads/sencillos/Electronica/67cf333bb9e31.mp3', '2025-03-10', 0.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id_usuario` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido1` varchar(50) NOT NULL,
  `apellido2` varchar(50) DEFAULT NULL,
  `correo` varchar(100) NOT NULL,
  `telefono` varchar(15) DEFAULT NULL,
  `nombre_usuario` varchar(50) NOT NULL,
  `contraseña` varchar(255) NOT NULL,
  `id_artista` tinyint(1) NOT NULL DEFAULT 0,
  `id_cliente` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `nombre`, `apellido1`, `apellido2`, `correo`, `telefono`, `nombre_usuario`, `contraseña`, `id_artista`, `id_cliente`) VALUES
(1, 'luis', 'vega', 'acevedo', 'ferryz@gmail.com', '5516090806', 'luisosito3', '$2y$10$khs.1yiqoAXLlpQ2SJ5j7uPHzX7zkaEdo18iqVREbnSaMdrvGm1ke', 0, 1),
(8, 'bruno', 'osori', 'mendoz', 'bnrunooso@gmai.com', '12093789267816', 'brunonegrito2', '$2y$10$Bq88VV.nRLe4WXxS2E2ysu5hUiEUEQn3WIqRN6I0gVrxmoeFmGla.', 0, 1),
(9, 'Jarad', 'Anthony ', 'Higgins ', 'saulx@gmail.com', '1982671513', 'Juice WRDL', '$2y$10$r2wN8/M3rSMbwgXcNihst.SKFpf38kW09yOVq208ikrhZhJkBa0Xu', 1, 0),
(10, 'diego', 'escroto', 'garcia', 'escroto1234@hotmail.com', '09012873', 'escrotodiegi', '$2y$10$GgYPwqQsEsRYoPaedo7uVO0Tw5EdHfVRnnuS/ElqceTr0Eiuawvzm', 0, 1),
(11, 'Sonny', 'John', 'Moore ', 'Skrillex@gmail.com', '554346261112', 'Skrillex', '$2y$10$o1AhoDzJcq/pKSqaQNuYq.cJuYPfX.Oicd4tBpZZY95.9goQbURGW', 1, 0),
(12, 'Billie ', 'Joe', 'Armstrong', 'Greenday@gmail.com', '55160908099', 'Green Day ', '$2y$10$yXvgClSEQgJxmWo4I/C0J.lJ3D2/eyqZS6POVnoDlV1kZ3UlcdrA6', 1, 0),
(13, 'Chester ', 'Bennington', 'Benin', 'Linkinpark@gmail.com', '551609080612', 'Linkin Park', '$2y$10$Xl2Ddq4cNEj2Ks81huQDI.bqT/IEd5arEHzBrObQBz9a05Yo36BIi', 1, 0),
(14, 'Anthony', 'Kiedis', 'S', 'redhotchilliepepers@gmail.com', '2131341456654', 'Red Hot Chili Peppers', '$2y$10$knZ581DOAKdIT9e9Gq8xaeitwz0/97idO3bNXyuweZeBcYDkmaiUW', 1, 0),
(15, 'Melted ', 'Ice', 'Cream', 'MeltedIceCream@gmail.com', '12215251573615', 'Melted Ice Cream', '$2y$10$Oz0miFsQkmeakRV/UFg0DOUfaa1exYffH382qFtYbXTXzWBq9Rnze', 1, 0),
(16, 'Marco', 'Vazquez', 'XCX', 'mv360413@gmail.com', '5514907882', 'Marco XCX', '$2y$10$dADLCu8OybtoNp./mOCe1.bF6NK4DfNBZK2dvxmQUlia7a8OnKxYa', 0, 1),
(17, 'hihdowe', 'uuiyiw', 'uiufe', 'uiehfuebf@gamil.com', '55626258', 'louieuuia', '$2y$10$H00iGc2j1QujAJqpp3SIYuxTOqU6ipjMBTHRVdMvf0Js/AyEWU106', 0, 1),
(21, 'BRUNO', 'OSORIO', 'MENDOZA', 'bomosorio@gmail.com', '5515761668', 'bruno', '$2y$10$wcOh/87f0a2E9.RNsDtIgur7FJK.wvu7PMG.mxCUVNtVqUdi2WG/i', 1, 0),
(22, 'pepe', 'ortiz', 'ortiz', 'jjjj@gmail.com', '55671234', 'pepe', '$2y$10$78UtB7hcNdJrcV2gQECAHOoz/w0baL8HOGStGjFfd7gIGs7myTb1.', 1, 0),
(23, 'yahir', 'Silva', 'Lopez ', 'prueba@gmail.com', '5515671668', 'yahir', '$2y$10$gcj8G8GiMg4k/SH7aNJoBeiNalTdBwN5ddF3nWppWlheQkpgtyj/.', 1, 0),
(24, 'pedro', 'Ortiz', 'Ortiz', 'jojo@gmail.com', '5515761678', 'm02', '$2y$10$Jdn0X4RBGGRpOzIdDwOdS.S4ZFcIOT0wBEDIeCEpXNZxdLu7kBbzy', 0, 1),
(25, 'saul', 'tamayo', 'tamayo', 'lope21o@gmail.com', '4415761632', 'saul1', '$2y$10$mqYrsYnvDEFosfBVXNrlAuXMi59.M4crU6n2Aq/mg3poivJb1KmHC', 0, 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `ai_chat_history`
--
ALTER TABLE `ai_chat_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indices de la tabla `ai_keywords`
--
ALTER TABLE `ai_keywords`
  ADD PRIMARY KEY (`id`),
  ADD KEY `response_id` (`response_id`),
  ADD KEY `keyword` (`keyword`);

--
-- Indices de la tabla `ai_responses`
--
ALTER TABLE `ai_responses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category` (`category`),
  ADD KEY `active` (`active`),
  ADD KEY `priority` (`priority`);

--
-- Indices de la tabla `album`
--
ALTER TABLE `album`
  ADD PRIMARY KEY (`id_album`),
  ADD UNIQUE KEY `idx_album_artista_nombre` (`id_artista`,`nombre_album`),
  ADD KEY `id_artista` (`id_artista`),
  ADD KEY `id_genero` (`id_genero`);

--
-- Indices de la tabla `artista`
--
ALTER TABLE `artista`
  ADD PRIMARY KEY (`id_artista`),
  ADD KEY `usuario` (`usuario`);

--
-- Indices de la tabla `artistas_generos`
--
ALTER TABLE `artistas_generos`
  ADD PRIMARY KEY (`artista_id`,`genero_id`);

--
-- Indices de la tabla `canciones`
--
ALTER TABLE `canciones`
  ADD PRIMARY KEY (`id_cancion`),
  ADD UNIQUE KEY `idx_cancion_album_nombre` (`id_album`,`nombre_cancion`),
  ADD KEY `id_album` (`id_album`),
  ADD KEY `id_artista` (`id_artista`);

--
-- Indices de la tabla `carrito`
--
ALTER TABLE `carrito`
  ADD PRIMARY KEY (`id_carrito`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`id_cliente`),
  ADD KEY `id_artista` (`id_artista`),
  ADD KEY `id_cancion` (`id_cancion`),
  ADD KEY `id_album` (`id_album`);

--
-- Indices de la tabla `compras`
--
ALTER TABLE `compras`
  ADD PRIMARY KEY (`id_compra`),
  ADD UNIQUE KEY `compra_unica` (`id_usuario`,`tipo_producto`,`id_producto`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `genero`
--
ALTER TABLE `genero`
  ADD PRIMARY KEY (`id_genero`),
  ADD UNIQUE KEY `nombre_genero` (`nombre_genero`);

--
-- Indices de la tabla `me_gusta`
--
ALTER TABLE `me_gusta`
  ADD PRIMARY KEY (`id_me_gusta`),
  ADD UNIQUE KEY `usuario_item_tipo` (`id_usuario`,`id_item`,`tipo_item`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `playlists`
--
ALTER TABLE `playlists`
  ADD PRIMARY KEY (`id_playlist`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `playlist_canciones`
--
ALTER TABLE `playlist_canciones`
  ADD PRIMARY KEY (`id_playlist_cancion`),
  ADD UNIQUE KEY `playlist_cancion_unica` (`id_playlist`,`tipo_cancion`,`id_cancion`),
  ADD KEY `id_playlist` (`id_playlist`);

--
-- Indices de la tabla `preferencias_musicales`
--
ALTER TABLE `preferencias_musicales`
  ADD PRIMARY KEY (`id_preferencia`),
  ADD UNIQUE KEY `usuario_genero` (`id_usuario`,`id_genero`),
  ADD KEY `id_genero` (`id_genero`);

--
-- Indices de la tabla `sencillos`
--
ALTER TABLE `sencillos`
  ADD PRIMARY KEY (`id_sencillo`),
  ADD UNIQUE KEY `idx_sencillo_artista_nombre` (`id_artista`,`nombre_sencillo`),
  ADD KEY `id_artista` (`id_artista`),
  ADD KEY `id_genero` (`id_genero`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `correo` (`correo`),
  ADD UNIQUE KEY `nombre_usuario` (`nombre_usuario`),
  ADD UNIQUE KEY `idx_usuario_correo` (`correo`),
  ADD UNIQUE KEY `idx_usuario_nombre_usuario` (`nombre_usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `ai_chat_history`
--
ALTER TABLE `ai_chat_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT de la tabla `ai_keywords`
--
ALTER TABLE `ai_keywords`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=155;

--
-- AUTO_INCREMENT de la tabla `ai_responses`
--
ALTER TABLE `ai_responses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT de la tabla `album`
--
ALTER TABLE `album`
  MODIFY `id_album` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT de la tabla `artista`
--
ALTER TABLE `artista`
  MODIFY `id_artista` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `canciones`
--
ALTER TABLE `canciones`
  MODIFY `id_cancion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=285;

--
-- AUTO_INCREMENT de la tabla `carrito`
--
ALTER TABLE `carrito`
  MODIFY `id_carrito` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT de la tabla `cliente`
--
ALTER TABLE `cliente`
  MODIFY `id_cliente` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `compras`
--
ALTER TABLE `compras`
  MODIFY `id_compra` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `genero`
--
ALTER TABLE `genero`
  MODIFY `id_genero` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `me_gusta`
--
ALTER TABLE `me_gusta`
  MODIFY `id_me_gusta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `playlists`
--
ALTER TABLE `playlists`
  MODIFY `id_playlist` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `playlist_canciones`
--
ALTER TABLE `playlist_canciones`
  MODIFY `id_playlist_cancion` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `preferencias_musicales`
--
ALTER TABLE `preferencias_musicales`
  MODIFY `id_preferencia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `sencillos`
--
ALTER TABLE `sencillos`
  MODIFY `id_sencillo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `ai_chat_history`
--
ALTER TABLE `ai_chat_history`
  ADD CONSTRAINT `ai_chat_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `ai_keywords`
--
ALTER TABLE `ai_keywords`
  ADD CONSTRAINT `fk_ai_keywords_response` FOREIGN KEY (`response_id`) REFERENCES `ai_responses` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `album`
--
ALTER TABLE `album`
  ADD CONSTRAINT `album_ibfk_2` FOREIGN KEY (`id_genero`) REFERENCES `genero` (`id_genero`),
  ADD CONSTRAINT `fk_album_artista` FOREIGN KEY (`id_artista`) REFERENCES `artista` (`id_artista`) ON DELETE CASCADE;

--
-- Filtros para la tabla `artista`
--
ALTER TABLE `artista`
  ADD CONSTRAINT `artista_ibfk_2` FOREIGN KEY (`usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_artista_usuario` FOREIGN KEY (`usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `canciones`
--
ALTER TABLE `canciones`
  ADD CONSTRAINT `canciones_ibfk_1` FOREIGN KEY (`id_album`) REFERENCES `album` (`id_album`) ON DELETE CASCADE,
  ADD CONSTRAINT `canciones_ibfk_2` FOREIGN KEY (`id_artista`) REFERENCES `artista` (`id_artista`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_canciones_album` FOREIGN KEY (`id_album`) REFERENCES `album` (`id_album`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_canciones_artista` FOREIGN KEY (`id_artista`) REFERENCES `artista` (`id_artista`) ON DELETE CASCADE;

--
-- Filtros para la tabla `carrito`
--
ALTER TABLE `carrito`
  ADD CONSTRAINT `carrito_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `cliente`
--
ALTER TABLE `cliente`
  ADD CONSTRAINT `cliente_ibfk_1` FOREIGN KEY (`id_artista`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE,
  ADD CONSTRAINT `cliente_ibfk_2` FOREIGN KEY (`id_cancion`) REFERENCES `canciones` (`id_cancion`) ON DELETE CASCADE,
  ADD CONSTRAINT `cliente_ibfk_3` FOREIGN KEY (`id_album`) REFERENCES `album` (`id_album`) ON DELETE CASCADE;

--
-- Filtros para la tabla `compras`
--
ALTER TABLE `compras`
  ADD CONSTRAINT `compras_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `me_gusta`
--
ALTER TABLE `me_gusta`
  ADD CONSTRAINT `me_gusta_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `playlists`
--
ALTER TABLE `playlists`
  ADD CONSTRAINT `playlists_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `playlist_canciones`
--
ALTER TABLE `playlist_canciones`
  ADD CONSTRAINT `playlist_canciones_ibfk_1` FOREIGN KEY (`id_playlist`) REFERENCES `playlists` (`id_playlist`) ON DELETE CASCADE;

--
-- Filtros para la tabla `preferencias_musicales`
--
ALTER TABLE `preferencias_musicales`
  ADD CONSTRAINT `preferencias_musicales_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE,
  ADD CONSTRAINT `preferencias_musicales_ibfk_2` FOREIGN KEY (`id_genero`) REFERENCES `genero` (`id_genero`) ON DELETE CASCADE;

--
-- Filtros para la tabla `sencillos`
--
ALTER TABLE `sencillos`
  ADD CONSTRAINT `fk_sencillos_artista` FOREIGN KEY (`id_artista`) REFERENCES `artista` (`id_artista`) ON DELETE CASCADE,
  ADD CONSTRAINT `sencillos_ibfk_1` FOREIGN KEY (`id_artista`) REFERENCES `artista` (`id_artista`) ON DELETE CASCADE,
  ADD CONSTRAINT `sencillos_ibfk_2` FOREIGN KEY (`id_genero`) REFERENCES `genero` (`id_genero`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
