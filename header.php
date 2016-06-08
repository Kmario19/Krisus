<?php

/**
 * Archivo de Inicializaci�n del Sistema
 *
 * Carga las clases base y ejecuta la solicitud.
 *
 * @name    header.php
 * @author  PHPost Team
 */
/*
 * -------------------------------------------------------------------
 *  Estableciendo variables importantes
 * -------------------------------------------------------------------
 */

if (defined('TS_HEADER'))
    return;

// Sesi�n
if (!isset($_SESSION))
    session_start();

// Reporte de errores
error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
ini_set('display_errors', TRUE);

// L�mite de ejecuci�n
set_time_limit(300);

// Zona horaria
date_default_timezone_set('America/Bogota');

/*
 * -------------------------------------------------------------------
 *  Definiendo constantes
 * -------------------------------------------------------------------
 */
define('TS_ROOT', realpath(dirname(__FILE__)));

define('TS_HEADER', TRUE);

define('TS_PHP', TS_ROOT . '/inc/php/');

define('TS_CLASS', TS_ROOT . '/inc/class/');

define('TS_EXTRA', TS_ROOT . '/inc/ext/');

define('TS_AJAX', TS_ROOT . '/inc/ajax/');

define('TS_FILES', TS_ROOT . '/files/');

set_include_path(get_include_path() . PATH_SEPARATOR . realpath('./'));

/*
 * -------------------------------------------------------------------
 *  Agregamos los archivos globales
 * -------------------------------------------------------------------
 */

// Contiene las variables de configuraci�n principal
include 'config.inc.php';

// No ha sido instalado el script...
if ($db['hostname'] == 'dbhost')
    header("Location: ./install/index.php");

// Funciones
include TS_EXTRA . 'functions.php';

// Nucleo
include TS_CLASS . 'c.core.php';

// Controlador de usuarios
include TS_CLASS . 'c.user.php';

// Monitor de usuario
include TS_CLASS . 'c.monitor.php';

// Actividad de usuario
include TS_CLASS . 'c.actividad.php';

// Mensajes de usuario
include TS_CLASS . 'c.mensajes.php';

// Smarty
include TS_CLASS . 'c.smarty.php';

// Crean requests
include TS_EXTRA . 'QueryString.php';

/*
 * -------------------------------------------------------------------
 *  Inicializamos los objetos principales
 * -------------------------------------------------------------------
 */

// Limpiar variables...
cleanRequest();

// Cargamos el nucleo
$tsCore = & tsCore::getInstance();

// Usuario
$tsUser = & tsUser::getInstance();

// Monitor
$tsMonitor = & tsMonitor::getInstance();

// Actividad
$tsActividad = & tsActividad::getInstance();

// Mensajes
$tsMP = & tsMensajes::getInstance();

// Definimos el template a utilizar
$tsTema = $tsCore->settings['tema']['t_path'];
if (empty($tsTema))
    $tsTema = 'default';
define('TS_TEMA', $tsTema);

// Smarty
$smarty = & tsSmarty::getInstance();

$currentUrl = $tsCore->currentUrl();

/*
 * -------------------------------------------------------------------
 *  Asignaci�n de variables
 * -------------------------------------------------------------------
 */

// Configuraciones
$smarty->assign('tsConfig', $tsCore->settings);

// Obtejo usuario
$smarty->assign('tsUser', $tsUser);

// Cookies
$smarty->assign("tsCookies", $_COOKIE);

// URL actual
$smarty->assign('tsURL', $currentUrl);

/*
 * -------------------------------------------------------------------
 *  Validaciones extra
 * -------------------------------------------------------------------
 */
// Baneo por IP
$ip = $_SERVER['X_FORWARDED_FOR'] ? $_SERVER['X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
if (!filter_var($ip, FILTER_VALIDATE_IP))
    die('Su ip no se pudo validar.');
if (db_exec('num_rows', db_exec(array(__FILE__, __LINE__), 'query', 'SELECT id FROM w_blacklist WHERE type = \'1\' && value = \'' . $ip . '\' LIMIT 1')))
    die('Bloqueado');

// Online/Offline
if ($tsCore->settings['offline'] == 1 && ($tsUser->is_admod != 1 && $tsUser->permisos['govwm'] == false) && $_GET['seccion'] != 'login') {
    $smarty->assign('tsTitle', $tsCore->settings['titulo'] . ' -  ' . $tsCore->settings['slogan']);
    if (empty($_GET['action']))
        $smarty->display('t.mantenimiento.tpl');
    else
        die('Espera un poco...');
    exit();
    // Banned
} elseif ($tsUser->is_banned) {
    $banned_data = $tsUser->getUserBanned();
    if (!empty($banned_data)) {
        // SI NO ES POR AJAX
        if (empty($_GET['action'])) {
            $smarty->assign('tsBanned', $banned_data);
            $smarty->display('t.suspension.tpl');
        } else
            die('<div class="msjError">Usuario suspendido</div>');
        //
        exit;
    }
}