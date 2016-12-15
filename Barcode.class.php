<?php

namespace Barcode;

/**
 * MIT License
 * 
 * Copyright (c) 2015 Leandro Teixeira
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

/**
 * Classe para geração de código de barras no padrão 128
 *
 * @author Leandro Teixeira <leandro_mnc@yahoo.com.br>
 */
class Barcode
{
    /**
     * Armazena a conversão do texto em binário
     *
     * @var int 
     */
    private $binary = null;

    /**
     * Armazena a classe que gera a imagem do código de barras
     *
     * @var BarcodeImage 
     */
    private $objImage = null;

    /**
     * Armazena o tipo do código de barras (A, B ou C)
     *
     * @var string 
     */
    private $type = null;

    function __construct() {
        $this->objImage = new BarcodeImage();
    }

    /**
     * Código binário para cada caracter do alfabeto
     *
     * @var array
     */
    private $_encoding128 = array(
        '11011001100', '11001101100', '11001100110', '10010011000',
        '10010001100', '10001001100', '10011001000', '10011000100',
        '10001100100', '11001001000', '11001000100', '11000100100',
        '10110011100', '10011011100', '10011001110', '10111001100',
        '10011101100', '10011100110', '11001110010', '11001011100',
        '11001001110', '11011100100', '11001110100', '11101101110',
        '11101001100', '11100101100', '11100100110', '11101100100',
        '11100110100', '11100110010', '11011011000', '11011000110',
        '11000110110', '10100011000', '10001011000', '10001000110',
        '10110001000', '10001101000', '10001100010', '11010001000',
        '11000101000', '11000100010', '10110111000', '10110001110',
        '10001101110', '10111011000', '10111000110', '10001110110',
        '11101110110', '11010001110', '11000101110', '11011101000',
        '11011100010', '11011101110', '11101011000', '11101000110',
        '11100010110', '11101101000', '11101100010', '11100011010',
        '11101111010', '11001000010', '11110001010', '10100110000',
        '10100001100', '10010110000', '10010000110', '10000101100',
        '10000100110', '10110010000', '10110000100', '10011010000',
        '10011000010', '10000110100', '10000110010', '11000010010',
        '11001010000', '11110111010', '11000010100', '10001111010',
        '10100111100', '10010111100', '10010011110', '10111100100',
        '10011110100', '10011110010', '11110100100', '11110010100',
        '11110010010', '11011011110', '11011110110', '11110110110',
        '10101111000', '10100011110', '10001011110', '10111101000',
        '10111100010', '11110101000', '11110100010', '10111011110',
        '10111101110', '11101011110', '11110101110', '11010000100',
        '11010010000', '11010011100', '11000111010');

    /**
     * Último carácter do código de barras
     */
    const STOPCODE = '1100011101011';

    /**
     * Gera o código de barras
     *
     * @param string $string Texto que será convertido em imagem
     * @return \Barcode_lib
     */
    public function generate128($string)
    {
        if(empty($string) == false) {
            $this->type = $this->checkType($string);

            $start_bin = $this->getStartBinary();

            $dv = (int)$this->getDvModule103($string);

            $dv_bin = $this->_encoding128[$dv];

            $binary = $this->getBinaryLine($string);

            $this->binary = $start_bin . $binary . $dv_bin . self::STOPCODE;

            $this->objImage->binary  = $this->binary;
        } else {
            $this->binary = null;
            $this->type = null;
            $this->objImage->binary = null;
        }

        return $this;
    }

    /**
     * Salva a imagem
     *
     * @param string $file Nome do arquivo
     * @param int $height Tamanho da imagem
     */
    public function saveImage($file, $height = 70)
    {
        if(empty($this->binary) == false) {
            $this->objImage->height = $height;
            return $this->objImage->save($file);
        }
        return "";
    }

    /**
     * Seta a saída e a altura do código de barras
     *
     * @param string $output (png, jpeg ou gif)
     * @param int $height Tamanho do código de barras
     */
    public function outputImage($output = 'png', $height = 70)
    {
        if(empty($this->binary) === false) {
            $this->objImage->height = $height;
            $this->objImage->output = $output;
            $this->objImage->output($output);
        }
    }

    /**
     * Converte a imagem em base64
     *
     * @param int $height Tamanho da imagem
     * @return string Base64
     */
    public function base64($height = 70)
    {
        if(empty($this->binary) == false) {
            $this->objImage->height = $height;
            $this->objImage->output = 'png';

            return $this->objImage->base64();
        }

        return null;
    }

    /**
     * Pega o binário referente ao tipo do código de barras (A, B ou C)
     *
     * @return int Binário
     */
    private function getStartBinary()
    {
        if($this->type == 'A') {
            return $this->_encoding128[103];
        } else if($this->type == 'B') {
            return $this->_encoding128[104];
        } else {
            return $this->_encoding128[105];
        }
    }

    /**
     * Pega o decimal do carácter
     *
     * @param string $letter
     * @return int Decimal
     */
    private function getPosition($letter)
    {
        if(ctype_lower($letter)) {
            return ord($letter);
        } else {
            return ord($letter) - 32;
        }
    }

    /**
     * Verifica qual será o tipo de barcode 128 (A, B ou C)
     *
     * @param string $string Texto que será convertido em código de barra
     * @return string Tipo (A, B, C)
     */
    private function checkType($string)
    {
        $this->type = null;

        $regtype_a = '/[!"#$%&\'()*+,-.\/0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ\[\]\^_]/';
        $regtype_b = '/[!"#$%&\'()*+,-.\/0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ\[\]\^_`abcdefghijklmnopqrstuvwxyz{|}~]/';

        if(is_numeric($string)) {
            $this->type = 'C';
        } else if(preg_match($regtype_a, $string)) {
            $this->type = 'A';
        } else if(preg_match($regtype_b, $string)) {
            $this->type = 'B';
        }

        return $this->type;
    }

    /**
     * Cálculo módulo 103
     *
     * @param string $string
     * @return int Digito verificador
     */
    private function getDvModule103($string)
    {
        $size = strlen((string) $string);
        $sum = 0;
        $cont = 1;
        $cont2 = 1;

        if($this->type != 'C') {
            for($i = 0; $i < $size;$i++) {
                $sum += $this->getPosition($string{$i}) * $cont++;
            }
        } else {
            $sum += 105; // Type C

            while($cont <= $size) {
                $pair = substr($string, ($cont - 1), 2);

                $sum += (int) $pair * $cont2++;

                // Reading two characters each
                if(strlen($pair) == 2) {
                    $cont += 2;
                } else {
                    $cont++;
                }

                unset($pair);
            }
        }

        $dv = $sum % 103;

        return $dv;
    }

    /**
     * Pega o código binário referente a letra
     *
     * @param string $letter Letra que será convertida em binário
     * @return int Binário
     */
    private function getBinary($letter)
    {
        $ord = $this->getPosition($letter);

        return isset($this->_encoding128[$ord]) ? $this->_encoding128[$ord] : '';
    }

    /**
     * Gera a linha binaria
     *
     * @param string $string
     * @return int Binário
     */
    private function getBinaryLine($string)
    {
        $size = strlen($string);
        $bin = '';

        if($this->type !== 'C') {
            for($i = 0; $i < $size;$i++) {
                $bin .= $this->getBinary($string{$i});
            }
        } else {
            $size = strlen($string);
            $cont = 0;

            // Quando o código binário não for do tipo C
            // Será calculado de par em par
            while($cont < $size) {
                $pair = substr($string, $cont, 2);

                if(strlen($pair) == 2) {
                    $cont += 2;
                } else {
                    $cont++;
                }

                $bin .= isset($this->_encoding128[(int)$pair]) ? $this->_encoding128[(int)$pair] : '';

                unset($pair);
            }
        }

        return $bin;
    }
}

/**
 * Classe para gerar a imagem a partir do binario
 *
 * @author Leandro Teixeira <leandro_mnc@yahoo.com.br>
 */
class BarcodeImage
{
    /**
     * Altura da imagem
     *
     * @var int 
     */
    public $height = 70;

    /**
     * Número que será convertido em imagem
     *
     * @var int 
     */
    public $binary;

    /**
     * Tipo da imagem
     *
     * @var string 
     */
    public $output = 'png';

    /**
     * Caminho completo da imagem gerada
     *
     * @var string
     */
    public $filename;
    
    /**
     * Cria a imagem
     *
     * @param string $file Filename
     * @return string Filename
     */
    public function save($file)
    {
        $this->create($file);
        return getcwd() . DIRECTORY_SEPARATOR . $this->filename;
    }

    /**
     * Gera o cabeçalho de saída do arquivo
     *
     * @return void
     */
    public function output()
    {
        header('Content-type:' . $this->getType());
        $this->create();
    }

    /**
     * Cria a imagem e converte em base64
     *
     * @return string Base64
     */
    public function base64()
    {
        ob_start();
        $this->create();
        $imagedata = ob_get_clean();

        return base64_encode($imagedata);
    }

    /**
     * Efetua o cálculo para gerar imagem
     *
     * @param string $file Nome do arquivo de saída
     */
    private function create($file = null)
    {
        $this->filename = $file;
        
        $str_size = strlen($this->binary);
        $scale = 2; // Largura de cada listra do código
        $quiet_zone = 12; // Tamanho da zona de descanso obrigatória
        $width = ($str_size + $quiet_zone) * $scale; // Largura do código de barras

        $coordenate_x = 0;
        $coordenate_y = 0;

        $image = imagecreate($width, $this->height); // Create image
        imagecolorallocate($image, 255, 255, 255); // Image Background

        $black = imagecolorallocate($image, 0, 0, 0); // Color Line
        $white = imagecolorallocate($image, 255, 255, 255); // Color Line

        // Quiet Zone
        for($j=0;$j<$quiet_zone;$j++) {
            imageline($image, $coordenate_x++, 0, $coordenate_y++, 150, $white);
        }

        // Write Line to image
        for($k=0;$k < $str_size;$k++) {
            for($l=0;$l < 2;$l++) {
                if($this->binary{$k} == '1') {
                    imageline($image, $coordenate_x++, 0, $coordenate_y++, 150, $black);
                } else {
                    imageline($image, $coordenate_x++, 0, $coordenate_y++, 150, $white);
                }
            }
        }

        // Quiet Zone
        for($j=0;$j<$quiet_zone;$j++) {
            imageline($image, $coordenate_x++, 0, $coordenate_y++, 150, $white);
        }

        if($this->output == 'png') {
            imagepng($image, $this->filename);
        } else if($this->output == 'jpg') {
            imagejpeg($image, $this->filename);
        } else if($this->output == 'gif') {
            imagegif($image, $this->filename);
        }

        imagedestroy($image);
    }

    private function getType()
    {
        $retorno = '';

        if($this->output == 'png') {
            $retorno = 'image/png';
        } else if($this->output == 'jpg') {
            $retorno = 'image/jpeg';
        } else if($this->output == 'gif') {
            $retorno = 'image/gif';
        }

        return $retorno;
    }
}