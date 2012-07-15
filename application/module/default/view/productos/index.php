<?php
require_once('/../../controller/productos.php');
$producto = new productos();

$producto->listarProductos();
?>
<html>
    <?php require_once(APPLICATION_PATH . '/layout/header.php'); ?>
    <link rel="stylesheet" type="text/css" href="pcss/productos.css" media="screen" />
<body>
		<div id="imPage">
			<div id="imHeader">
				<h1 class="imHidden">ITERA S.A.C - UNREGISTERED VERSION</h1>
				
			</div>
			<a class="imHidden" href="#imGoToCont" title="Salta el menu principal">Vaya al Contenido</a>
			<a id="imGoToMenu"></a><p class="imHidden">Menu Principal</p>
			<?php require_once (APPLICATION_PATH . '/layout/menu.php'); ?>
			<div id="imContentGraphics"></div>
			<div id="imContent">
				<a id="imGoToCont"></a>
				<h2 id="imPgTitle">Productos</h2>
				<div style="width: 984px; float: left;">
					<div id="imCell_1" class="imGrid[0, 0]"><div id="imCellStyleGraphics_1"></div><div id="imCellStyle_1"><div id="imObjectImage_1"><div id="imObjectImageContent_1"></div></div><script type="text/javascript">var flashvars = {containerId: "imObjectImage_1", key: "211206165160216183098145169131118136175191176133099207227213", cWidth: "970", cHeight: "224", copyright: "© Copyright 2011 por ITERA S.A.C Todos los derechos reservados.", watermark: "false", cpindex: "147519"}; var params = {quality: "high", bgcolor: "#ffffff", play: "true", loop: "true", wmode: "transparent", scale: "noscale", menu: "true", devicefont: "false", salign: "lt", allowscriptaccess: "sameDomain", allowFullScreen: "true"}; var attributes = {id: "imObjectImageContent_1", name: "imObjectImageContent_1", align: "middle"}; swfobject.embedSWF("/res/imImage.swf", "imObjectImageContent_1", "970", "224", "10.0.0", "res/expressInstall.swf", flashvars, params, attributes);</script></div></div>
				</div>
				<div style="width: 984px; float: left;">
					<div style="float: left; width: 164px;">
						<div id="imCell_2" class="imGrid[1, 1]"><div id="imCellStyleGraphics_2"></div><div id="imCellStyle_2"><a href="http://www.eset-la.com/" target="_blank"><img id="imObjectImage_2" src="images/ESET2_7h3w8b9i.png" title="" alt="" height="69" width="152" /></a></div></div>
					</div>
					<div style="float: left; width: 328px;">
						<div id="imCell_6" class="imGrid[1, 1]"><div id="imCellStyleGraphics_6"></div><div id="imCellStyle_6"><a href="http://www.cymphonix.com/" target="_blank"><img id="imObjectImage_6" src="images/Cymphonix_Logo_2.png" title="" alt="" height="70" width="316" /></a></div></div>
					</div>
					<div style="float: left; width: 328px;">
						<div id="imCell_12" class="imGrid[1, 1]"><div id="imCellStyleGraphics_12"></div><div id="imCellStyle_12"><a href="http://www.fortinet.com/" target="_blank"><img id="imObjectImage_12" src="images/fortinet.png" title="" alt="" height="75" width="316" /></a></div></div>
					</div>
					<div style="float: left; width: 164px;">
						<div id="imCell_3" class="imGrid[1, 1]"><div id="imCellStyleGraphics_3"></div><div id="imCellStyle_3"><a href="http://www.gfihispana.com/" target="_blank"><img id="imObjectImage_3" src="images/GFILOGO-20112.png" title="" alt="" height="72" width="150" /></a></div></div>
					</div>
					
				</div>
				<div style="width: 984px; float: left;">
					<div style="float: left; width: 164px;">
						<div id="imCell_5" class="imGrid[2, 2]"><div id="imCellStyleGraphics_5"></div><div id="imCellStyle_5"><a href="http://www8.hp.com/pe/es/home.html" target="_blank"><img id="imObjectImage_5" src="images/Nuevo-logo----HP2.png" title="" alt="" height="108" width="104" /></a></div></div>
					</div>
					<div style="float: left; width: 328px;">
						<div id="imCell_21" class="imGrid[2, 2]"><div id="imCellStyleGraphics_21"></div><div id="imCellStyle_21"><a href="http://www.microsoft.com/es-pe/default.aspx" target="_blank"><img id="imObjectImage_21" src="images/Microsoft_Logo2.png" title="" alt="" height="75" width="316" /></a></div></div>
					</div>
					<div style="float: left; width: 328px;">
						<div id="imCell_15" class="imGrid[2, 2]"><div id="imCellStyleGraphics_15"></div><div id="imCellStyle_15"><a href="http://www.ultrabac.com/" target="_blank"><img id="imObjectImage_15" src="images/ULTRABAC2.png" title="" alt="" height="82" width="316" /></a></div></div>
					</div>
					<div style="float: left; width: 164px;">
						<div id="imCell_11" class="imGrid[2, 2]"><div id="imCellStyleGraphics_11"></div><div id="imCellStyle_11"><a href="http://www.intel.com/?es_LA_08" target="_blank"><img id="imObjectImage_11" src="images/intel-logo2.png" title="" alt="" height="93" width="140" /></a></div></div>
					</div>
					
				</div>
				<div style="width: 984px; float: left;">
					<div style="float: left; width: 164px;">
						<div id="imCell_10" class="imGrid[3, 3]"><div id="imCellStyleGraphics_10"></div><div id="imCellStyle_10"><a href="http://www.cisco.com/" target="_blank"><img id="imObjectImage_10" src="images/cisco_logo2.png" title="" alt="" height="84" width="152" /></a></div></div>
					</div>
					<div style="float: left; width: 328px;">
						<div id="imCell_18" class="imGrid[3, 3]"><div id="imCellStyleGraphics_18"></div><div id="imCellStyle_18"><a href="http://www.ncomputing.com/" target="_blank"><img id="imObjectImage_18" src="images/ncomputing2.png" title="" alt="" height="77" width="316" /></a></div></div>
					</div>
					<div style="float: left; width: 328px;">
						<div id="imCell_19" class="imGrid[3, 3]"><div id="imCellStyleGraphics_19"></div><div id="imCellStyle_19"><a href="http://www.netsupportschool.com/es/index.asp" target="_blank"><img id="imObjectImage_19" src="images/netsupport2.png" title="" alt="" height="77" width="316" /></a></div></div>
					</div>
					<div style="float: left; width: 164px;">
						<div id="imCell_4" class="imGrid[3, 3]"><div id="imCellStyleGraphics_4"></div><div id="imCellStyle_4"><a href="http://www.helppeoplesoft.net/" target="_blank"><img id="imObjectImage_4" src="images/helpPEOPLE-32.png" title="" alt="" height="103" width="152" /></a></div></div>
					</div>
					
				</div>
				<div style="width: 984px; float: left;">
					<div style="float: left; width: 164px;">
						<div id="imCell_20" class="imGrid[4, 4]"><div id="imCellStyleGraphics_20"></div><div id="imCellStyle_20"><a href="http://www.ibm.com/pe/es/" target="_blank"><img id="imObjectImage_20" src="images/IBM-22.png" title="" alt="" height="84" width="152" /></a></div></div>
					</div>
					<div style="float: left; width: 328px;">
						<div id="imCell_16" class="imGrid[4, 4]"><div id="imCellStyleGraphics_16"></div><div id="imCellStyle_16"><a href="http://www.invgate.com/es/" target="_blank"><img id="imObjectImage_16" src="images/Logo-InvGate2.png" title="" alt="" height="75" width="316" /></a></div></div>
					</div>
					<div style="float: left; width: 328px;">
						<div id="imCell_22" class="imGrid[4, 4]"><div id="imCellStyleGraphics_22"></div><div id="imCellStyle_22"><a href="http://www.arandasoft.com/" target="_blank"><img id="imObjectImage_22" src="images/ARANDA2.png" title="" alt="" height="112" width="316" /></a></div></div>
					</div>
					<div style="float: left; width: 164px;">
						<div id="imCell_23" class="imGrid[4, 4]"><div id="imCellStyleGraphics_23"></div><div id="imCellStyle_23"><a href="http://www.alloy-software.com/" target="_blank"><img id="imObjectImage_23" src="images/ALLOY2.png" title="" alt="" height="48" width="152" /></a></div></div>
					</div>
					
				</div>
				
				<div id="imBtMn"><a href="index.html">Home</a> | <a href="nosotros.html">Nosotros</a> | <a href="productos.html">Productos</a> | <a href="overview.html">Soluciones</a> | <a href="contactenos.html">Contáctenos</a> | <a href="imsitemap.html">Mapa general del sitio</a></div>
				<div class="imClear"></div>
			</div>
		</div>
        <div>
<?php require_once(APPLICATION_PATH . '/layout/footer.php'); ?>    
        </div>
    </body>
</html>
