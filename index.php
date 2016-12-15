<?php
require_once 'Barcode.class.php';

use Barcode\Barcode;

$barcode = new Barcode();
$cep = '01311-300';
$base64 = $barcode->generate128($cep)->base64(70);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-type" content="text/html;charset=utf-8"/>
        <title>Barcode 128</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"/>
    
        <style type="text/css">
            img {margin: 0 auto;display:table;text-align:center}
        </style>
    </head>
    <body>
        <div class="container">
            <div class="col-lg-6 col-md-6 col-sm-12 col-md-offset-3">
                <h3 class="text-center">Código de Barra no padrão 128</h3>
                <br/>
                <h4 class="text-center">Em Base 64</h4>
                
                <h5 class="text-center"><?php echo $cep ?></h5>
                <img src="data:image/png;base64,<?php echo $base64 ?>" alt="Barcode 128"/>

                <br/>
                
                <pre>$barcode = new Barcode();<br/>$cep = '01311-300';<br/>$base64 = $barcode->generate128($cep)->base64(70);<br/>&lt;img src="data:image/png;base64,&lt;?php echo $base64 ?>" alt="Barcode 128"/></pre>
            
                <hr/>
                
                <h4 class="text-center">Salva o arquivo</h4>
                
                <pre>$barcode = new Barcode();<br/>&lt;?php $filename = $barcode->generate128($cep)->saveImage('teste.png');?><br/>&lt;?php echo $filename ?></pre>
            
                <hr/>
                
                <h4 class="text-center">Saída no Browser</h4>
                
                <pre>&lt;?php $barcode = new Barcode();?><br/>&lt;?php $barcode->generate128($cep)->outputImage('jpg');?></pre>
            </div>
        </div>
    </body>
</html>