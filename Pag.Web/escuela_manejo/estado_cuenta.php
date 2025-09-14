<?php
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);

/* Para habilitar el uso de NameSpaces */
spl_autoload_register (function ($clase) {
    $archivo = __DIR__ . "/" . str_replace ("\\", "/", $clase) ."php";
    if (is_file ($archivo)) {
        require_once $archivo;
    }
});

use Spipu\Html2Pdf\Html2Pdf;
use Spipu\Html2Pdf\Exception\Html2PdfException;
require_once "Utils/Html2Pdf/Html2Pdf.php";

ob_start ();
include "pdf.php";
$html = ob_get_clean();

try {
$html2pdf = new Html2Pdf ();
$html2pdf -> writeHtml ($html);
ob_end_clean ();
$html2pdf -> output ("Estado.pdf");
} catch (Html2PdfException $ex) {
    echo $ex;
    die ();
}

?>