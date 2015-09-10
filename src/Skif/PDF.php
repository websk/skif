<?php
namespace Skif;


class PDF
{
    public static function createPDFToString($content)
    {
        include __DIR__ . "/../vendor/mpdf/mpdf/mpdf.php";

        $mpdf = new \mPDF();

        $mpdf->WriteHTML($content);
        return $mpdf->Output('', 'S');
    }

} 