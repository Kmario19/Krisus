<?php
/**
 * Administración de páginas
 *
 * @name    index.php
 * @author  Kmario19
 */

/*
 * -------------------------------------------------------------------
 *  Cargando configuraciones y funciones
 * -------------------------------------------------------------------
 */

require 'header.php';

/*
 * -------------------------------------------------------------------
 *  Definiendo variables por defecto
 * -------------------------------------------------------------------
 */

$tsTitle = $tsCore->settings['titulo'].' - '.$tsCore->settings['slogan'];

$seccion = getVar('seccion', true);

$accion = getVar('accion', true);

$accionDos = getVar('accionDos', true);

$accionTres = getVar('accionTres', true);

$require = null;

$perfil = true;

if(substr($_GET['seccion'], -1) == '/' || !empty($accion)) {
	$perfil = false;
}

/*
 * -------------------------------------------------------------------
 *  Cargando archivo de página requerido
 * -------------------------------------------------------------------
 */

if(is_file(TS_PHP . $seccion . '.php')){
	$require = TS_PHP . $seccion . '.php';
} else {
	switch($seccion) {
		case '':
		case 'index':
		case 'inicio':
			$require = TS_PHP . 'home.php';
			break;
		case 'login':
		case 'registro':
		case 'recuperar-clave':
		case 'recuperar-nick':
		case 'registro-reenviar':
		case 'validar':
			$require = TS_PHP . 'anon.php';
			break;
		case 'rss.xml':
			$require = TS_PHP . 'rss.php';
			break;
		case 'sitemap.xml':
			$require = TS_PHP . 'sitemap.php';
			break;
	}
}

/*
 * -------------------------------------------------------------------
 * Si no requiere el archivo se busca el perfil de usuario
 * y si no existe y no se ha pedido entonces es un Error 404
 * -------------------------------------------------------------------
 */

if(is_null($require) || $seccion == 'perfil') {
	if($seccion == 'perfil') {
		$seccion = $accion;
		$accion = $accionDos;
		$accionDos = $accionTres;
	}
	$usuario = $tsUser->getPerfil($seccion);

	if($usuario['user_id'] > 0 && $usuario['user_activo'] && !$usuario['user_baneado']) {
		$require = TS_PHP . 'perfil.php';

	} elseif($perfil || ($usuario['user_activo'] != 1 && !$tsUser->permisos['movcud'] && !$tsUser->is_admod) || ($usuario['user_baneado'] != 0 && !$tsUser->permisos['movcus'] && !$tsUser->is_admod)) {
		$tsPage = 'aviso';
		$smarty->assign("tsAviso",array('titulo' => 'Opps!', 'mensaje' => ($perfil ? 'El usuario no existe' : 'La cuenta de <b>'.$usuario['user_name'].'</b> se encuentra inhabilitada' ), 'but' => 'Ir a p&aacute;gina principal'));

	} else {
		$require = TS_PHP . 'error404.php';
	}
}

if($require) {
	require $require;
}

/*
 * -------------------------------------------------------------------
 *  Cargando footer para mostrar en plantilla con smarty
 * -------------------------------------------------------------------
 */

require 'footer.php';