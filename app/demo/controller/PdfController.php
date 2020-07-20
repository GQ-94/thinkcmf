<?php

namespace app\demo\controller;

use cmf\controller\BaseController;
use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * Class PdfController
 * @package app\demo\controller
 *
 * pdf范例 http://eclecticgeek.com/dompdf/debug.php
 */
class PdfController extends BaseController
{
    public function generate()
    {
        // instantiate and use the dompdf class
        $dompdf = new Dompdf();
        $dompdf->getOptions()->set('isHtml5ParserEnabled', true);
        $html = <<<EOT
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="content-type" content="text/html; charset=ISO-8859-1" />
    <meta name="generator" content="Kryptronic Software" />
    <meta name="keywords" content="core, meta, keywords, here" />
    <meta name="description" content="Page description here." />
    <meta name="robots" content="index,follow" />
    <base href="http://eclecticgeek.com/">
    <link rel="stylesheet" type="text/css" media="all" href="dompdf/debug_tests/css/-zoB5jYpkfs.css" />
    <title>Company Name</title>
</head>

<body>
    <div id="skin_wrapper">
        <div id="printable">
            <div id="skin_content">
                <div class="center">
                    <p class="big">Company Name</p>
                    <p>123 Main Street<br />York, Pennsylvania 17404<br />United States</p>
                    <p>Telephone: 1.800.WEB.STORE&nbsp;&nbsp;&nbsp;&nbsp;
                        Fax: 1.800.FAX.STORE</p>
                </div>
                <div class="regtablehead">Order Totals</div>
                <table id="ORDERTOTALS" class="regtable">
                    <tr class="regtable">
                        <td class="regtable" style="width: 90%">Item Subtotal</td>
                        <td class="regtable" style="width: 10%; text-align: right">&#36;59.99</td>
                    </tr>
                    <tr class="regtable">
                        <td class="regtable" style="width: 90%">Delivery Total</td>
                        <td class="regtable" style="width: 10%; text-align: right">&#36;8.00</td>
                    </tr>
                    <tr class="regtable">
                        <td class="regtable" style="width: 90%"><strong>Total</strong></td>
                        <td class="regtable" style="width: 10%; text-align: right"><strong>&#36;67.99</strong></td>
                    </tr>
                </table>
                <div class="regtablehead">Order Information</div>
                <table id="ORDERNUMTIME" class="regtable">
                    <tr class="regtable">
                        <td class="regtable" style="width: 50%">
                            <p class="strong">Order Number</p>
                            <p>ORD201204028</p>
                        </td>
                        <td class="regtable" style="width: 50%">
                            <p class="strong">Order Status</p>
                            <p>Pending Shipment</p>
                        </td>
                    </tr>
                    <tr class="regtable">
                        <td class="regtable" style="width: 50%">
                            <p class="strong">Order Date and Time</p>
                            <p>04/02/2012 04:52:08 AM</p>
                        </td>
                        <td class="regtable" style="width: 50%">
                            <p class="strong">IP Address</p>
                            <p>127.0.0.1</p>
                        </td>
                    </tr>
                </table>
                <div class="regtablehead">Billing Information</div>
                <table id="BILLINFO" class="regtable">
                    <tr class="regtable">
                        <td class="regtable" style="width: 50%">
                            <p class="strong">Billing Address</p>
                            <p>Dave Martin<br />POB 818<br />Wake Forest, North Carolina 27588<br />United States</p>
                        </td>
                        <td class="regtable" style="width: 50%">
                            <p class="strong">Email Address</p>
                            <p>dave_martin@mindspring.com</p>
                            <p class="strong">Telephone Number</p>
                            <p>5551212</p>
                        </td>
                    </tr>
                </table>
                <div class="regtablehead">Items</div>
                <table id="ORDERITEMS" class="regtable">
                    <tr class="regtable">
                        <td class="regtable" style="width: 50%">
                            <p class="strong">8.1 GB PCMCIA Hard Disk</p>
                            <p>Delivery Method: Standard Shipper (Pending Shipment)</p>
                            <p>Quantity: 1</p>
                            <p>Subtotal: &#36;59.99</p>
                            <p class="ultablesp">&nbsp;</p>

                        </td>
                        <td class="regtable" style="width: 50%">
                            <p class="strong">Delivery Address</p>
                            <p>Dave Martin<br />POB 818<br />Wake Forest, North Carolina 27588<br />United States</p>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</body>

</html>
EOT;

        $dompdf->loadHtml($html);
        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'landscape');
        // Render the HTML as PDF
        $dompdf->render();
        // Output the generated PDF to Browser
        $dompdf->stream();
    }
}