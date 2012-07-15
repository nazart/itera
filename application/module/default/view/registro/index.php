<?php
require_once('/../../controller/registro.php');
$registro = new registro();
print_r($registro->_params);
?>
<html>
    <?php require_once(APPLICATION_PATH.'/layout/header.php'); ?>
    <body>
        <div>
            <form method="post">
                <table>
                    <tr>
                        <td>
                            <label>nuevo</label>
                        </td>
                        <td>
                            <input type="text" name="nuevo" id="nuevo"/>
                        </td>
                    </tr>
                    <tr>
                        <td>
                        </td>
                        <td>
                            <input type="submit"/>
                        </td>
                    </tr>
                </table>    
            </form>
        </div>
        <div>
    <?php require_once(APPLICATION_PATH.'/layout/footer.php'); ?>    
            </div>
    </body>
</html>


