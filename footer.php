<?php if (!defined('TS_HEADER')) exit('No se permite el acceso directo al script');
/**
 * El footer permite mostrar la plantilla
 *
 * @name    footer.php
 * @author  PHPost Team
 */

/*
 * -------------------------------------------------------------------
 *  Realizamos tareas para mostrar la plantilla
 * -------------------------------------------------------------------
 */

//echo "<span style=\"position: absolute; color: red;\">$TOTAL_QUERYS consultas a la BD</span>";

if ($tsUser->is_member) {
	// Nofiticaciones
	$tsMonitor->updateCounts();
	$tsNots = $tsMonitor->getCounts();
	$smarty->assign('tsNots',$tsNots);

	// Mensajes
	$tsMP->updateCounts();
	$tsMPs = $tsMP->getCounts();
	$smarty->assign('tsMPs',$tsMPs);

	$tsTotalNotis = $tsNots['notifications'] + $tsNots['mi'] + $tsMPs['mps'];

	if ($tsTotalNotis > 99)
		$tsTitle = "(+99) $tsTitle";
	elseif ($tsTotalNotis)
		$tsTitle = "($tsTotalNotis) $tsTitle";

}

// Título de la pagina
$smarty->assign("tsTitle",$tsTitle);

// Página solicitada
$smarty->assign("tsPage",$tsPage);

// Variables GET
$smarty->assign("tsSeccion",$seccion);
$smarty->assign("tsAccion",$accion);
$smarty->assign("tsAccionDos",$accionDos);
$smarty->assign("tsAccionTres",$accionTres);

// Si es por ajax cargamos de su respectiva carpeta
$template = $tsPage == 'ajax' ? "ajax/$tsFile.tpl" : "t.$tsPage.tpl";

// Verificamos si la plantilla existe 
if(!$smarty->template_exists($template))
	die("0: Lo sentimos, se produjo un error al cargar la plantilla <b>$template</b>. Contacte al administrador");

$smarty->display($template);