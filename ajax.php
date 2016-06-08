<?php 
/**
 * Controlador de peticiones ajax
 *
 * @name	ajax.php
 * @author	Kmario19
 */

/*
 * -------------------------------------------------------------------
 *  Cargando configuraciones y funciones
 * -------------------------------------------------------------------
 */

require 'header.php';

/*
 * -------------------------------------------------------------------
 *  Estableciendo variables y archivos 
 * -------------------------------------------------------------------
 */

$seccion = isset($_GET['seccion']) ? htmlspecialchars($_GET['seccion']) : '';

$accion = isset($_GET['accion']) ? htmlspecialchars($_GET['accion']) : '';

if (empty($seccion) || empty($accion))
	die("Invalid request.");

$file = TS_AJAX . 'ajax.'.$seccion.'.php';

if (file_exists($file))
	include $file;
else
	die("0: No se encontro el archivo <b>ajax.".$seccion.".php</b>que se ha solicitado.");

$tsPage = 'ajax';

$tsFile = $seccion.'/'.$files[$accion]['p'];

$tsAjax = empty($files[$accion]['p']) || $tsAjax;

/*
 * -------------------------------------------------------------------
 *  Si la petición es por ajax solo se imprime en pantalla, de lo
 *  contrario se carga el footer para mostrar en plantilla con smarty
 * -------------------------------------------------------------------
 */

if(empty($tsAjax)) {
    $smarty->template_ts = false;

	require 'footer.php';
}