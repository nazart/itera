<?php
require_once('/../../controller/contactenos.php');
$contactenos = new contactenos();
$listaProductos = $contactenos->listarProductos();
$mensaje = array();
$params = array();
if ($contactenos->isPost()) {
    $mensajeError = $contactenos->validarContacto();
    $params = $contactenos->_params;
    if (empty($mensajeError)) {
        $contactenos->registrarContacto();
        $params = array();
        $contactenos->_redirect('/contactenos');
    }
}
?>
<html>
    <?php require_once(APPLICATION_PATH . '/layout/header.php'); ?>
    		<link rel="stylesheet" type="text/css" href="pcss/contactenos.css" media="screen" />
		

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
                <h2 id="imPgTitle">Contáctenos</h2>
                <div style="width: 984px; float: left;">
                    <div id="imCell_3" class="imGrid[0, 0]">
                        <div id="imCellStyleGraphics_3">

                        </div><div id="imCellStyle_3">
                            <img id="imObjectImage_3" src="images/CONTACTENOS.png" title="" alt="" height="225" width="972" /></div></div>
                </div>
                <div style="width: 984px; float: left;">
                    <div style="float: left; width: 328px;">
                        <div style="height: 437px;">&nbsp;</div>
                    </div>
                    <div style="float: left; width: 328px;">
                        <div id="imCell_2" class="imGrid[1, 1]">
                            <div id="imCellStyleGraphics_2"></div>
                            <div id="imCellStyle_2">
                                <?php
                                $mensajeFlash = $contactenos->flashMessage('mensajeContacto')->getMessage();
                                if (count($mensajeFlash) >= 1) {
                                    foreach ($mensajeFlash as $index) {
                                        echo $index;
                                    }
                                }
                                ?>

                                <form id="imObjectForm_2" action="" method="post" enctype="multipart/form-data" style="width: 316px; margin: 0; padding: 0; text-align: left;">
                                    <fieldset class="first">
                                        <div>
                                            <div style="float: left; margin: 0; padding: 0 0 2px;">
                                                <label for="imObjectForm_2_1" style="vertical-align: top; display: inline-block; margin: 3px 0 2px; width: 302px;">Nombre y Apellidos:*</label>
                                                <br />
                                                <input type="text" maxlength="51" class="mandatory valMaxLength[51]" style="float: left; width: 302px; margin-right: 10px; vertical-align: top; padding-top: 2px; padding-bottom: 2px;" name="Nombres" id="Nombres"  value="<?php echo isset($params['Nombres']) ? $params['Nombres'] : '' ?>" />
<?php echo isset($mensajeError['Nombres']) ? $mensajeError['Nombres']['message'] : '' ?>
                                            </div>
                                            <div class="imClear" style="height: 1px; line-height: 1px; width: 316px;">

                                            </div>
                                            <div style="float: left; margin: 0; padding: 0 0 2px;">
                                                <label for="imObjectForm_2_2" style="vertical-align: top; display: inline-block; margin: 3px 0 2px; width: 302px;">Empresa:*</label>
                                                <br />
                                                <input type="text" maxlength="60" class="mandatory valMaxLength[60]" style="float: left; width: 302px; margin-right: 10px; vertical-align: top; padding-top: 2px; padding-bottom: 2px;" name="Empresa" id="Empresa" value="<?php echo isset($params['Empresa']) ? $params['Empresa'] : '' ?>"/>
<?php echo isset($mensajeError['Empresa']) ? $mensajeError['Empresa']['message'] : '' ?>
                                            </div>
                                            <div class="imClear" style="height: 1px; line-height: 1px; width: 316px;">

                                            </div>
                                            <div style="float: left; margin: 0; padding: 0 0 2px;">
                                                <label for="imObjectForm_2_3" style="vertical-align: top; display: inline-block; margin: 3px 0 2px; width: 302px;">Teléfono:*</label>
                                                <br />
                                                <input type="text" maxlength="60" class="mandatory valNumber valMaxLength[60]" style="float: left; width: 302px; margin-right: 10px; vertical-align: top; padding-top: 2px; padding-bottom: 2px;" name="Telefono" id="Telefono" value="<?php echo isset($params['Telefono']) ? $params['Telefono'] : '' ?>"/>
<?php echo isset($mensajeError['Telefono']) ? $mensajeError['Telefono']['message'] : '' ?>
                                            </div>
                                            <div class="imClear" style="height: 1px; line-height: 1px; width: 316px;"></div>
                                            <div style="float: left; margin: 0; padding: 0 0 2px;">
                                                <label for="imObjectForm_2_4" style="vertical-align: top; display: inline-block; margin: 3px 0 2px; width: 302px;">Email:*
                                                </label>
                                                <br />
                                                <input type="text" class="mandatory valEmail" style="float: left; width: 302px; margin-right: 10px; vertical-align: top; padding-top: 2px; padding-bottom: 2px;" name="Email" id="Email" value="<?php echo isset($params['Email']) ? $params['Email'] : '' ?>"/>
<?php echo isset($mensajeError['Email']) ? $mensajeError['Email']['message'] : '' ?>
                                            </div>
                                            <div class="imClear" style="height: 1px; line-height: 1px; width: 316px;"></div>
                                            <div style="float: left; margin: 0; padding: 0 0 2px;">
                                                <label for="imObjectForm_2_5" style="vertical-align: top; display: inline-block; margin: 3px 0 2px; width: 302px;">
                                                    Producto:*
                                                </label>
                                                <br />
                                                <select id="IdProducto" name="IdProducto" 
                                                        class="mandatory" 
                                                        style="float: left; width: 302px; margin-right: 10px; vertical-align: top; padding-top: 2px; padding-bottom: 2px;" >
                                                        <?php foreach ($listaProductos as $index): ?>
                                                        <option <?php echo (isset($params['Producto']) && $index['IdProducto'] == $params['Producto']) ? 'selected="selected"' : '' ?>  value="<?php echo $index['IdProducto']; ?>" >
                                                        <?php echo $index['NombreProducto']; ?>
                                                        </option>
<?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="imClear" style="height: 1px; line-height: 1px; width: 316px;"></div>
                                            <div style="float: left; margin: 0; padding: 0 0 2px;"><label for="imObjectForm_2_6" style="vertical-align: top; display: inline-block; margin: 3px 0 2px; width: 302px;">Cantidad de PCs:*</label><br /><select class="mandatory" style="float: left; width: 302px; margin-right: 10px; vertical-align: top; padding-top: 2px; padding-bottom: 2px;" id="imObjectForm_2_6" name="imObjectForm_2_6"><option value="">-</option><option value="De 1 a 10 PCs">De 1 a 10 PCs</option><option value="De 11 a 30 PCs">De 11 a 30 PCs</option><option value="De 31 a 50 PCs">De 31 a 50 PCs</option><option value="De 51 a 100 PCs">De 51 a 100 PCs</option><option value="De 500 a 1000 PCs">De 500 a 1000 PCs</option><option value="+ de 1000 PCs">+ de 1000 PCs</option></select></div>
                                        </div>
                                    </fieldset>
                                    <fieldset>
                                        <div style="width: 316px; text-align: center;">
                                            <label for="imObjectForm_2_captcha" style="padding: 0;"></label><br />
                                            <input type="text" class="imCpt[5]" id="imObjectForm_2_captcha" name="imCpt" style="width: 120px;" maxlength="5" />
                                        </div>
                                        <input type="hidden" id="imObjectForm_2_prot" name="imSpProt" />
                                    </fieldset>
                                    <div style="width: 316px; text-align: center;">
                                        <input type="submit" value="Enviar" />
                                        <input type="reset" value="Resetear" />
                                    </div>
                                </form>
                                <script>x5engine.imQueue.push_init('x5engine.imForm.initForm(\'#imObjectForm_2\', false, {type: \'tip\', showAll: true, classes: \'validator\', landingPage: \'index.html\', labelColor: \'#000000\', fieldColor: \'#000000\' })');</script>
                            </div></div>
                    </div>
                    <div style="float: left; width: 328px;">
                        <div style="height: 437px;">&nbsp;</div>
                    </div>

                </div>
                <div style="width: 984px; float: left;">
                    <div style="height: 15px;">&nbsp;</div>
                </div>

                <div id="imBtMn"><a href="index.html">Home</a> | <a href="nosotros.html">Nosotros</a> | <a href="productos.html">Productos</a> | <a href="overview.html">Soluciones</a> | <a href="contactenos.html">Contáctenos</a> | <a href="imsitemap.html">Mapa general del sitio</a></div>
                <div class="imClear"></div>
            </div>
        </div>


        <?php
        $mensajeFlash = $contactenos->flashMessage('mensajeContacto')->getMessage();
        if (count($mensajeFlash) >= 1) {
            foreach ($mensajeFlash as $index) {
                echo $index;
            }
        }
        ?>
        <div>
<?php require_once(APPLICATION_PATH . '/layout/footer.php'); ?>    
        </div>
    </body>
</html>




