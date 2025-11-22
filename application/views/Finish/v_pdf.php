<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Export Purchase Order - <?php echo $po->number_po ?? 'PO'; ?></title>
    <link rel="icon" type="image/x-icon" href="<?php echo base_url('assets/image/favicon.ico'); ?>">

    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
            color: #333;
        }

        .container {
            width: 100%;
        }

        /* TABLE STYLE */
        #tableproduct {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }

        #tableproduct th,
        #tableproduct td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
            font-size: 11px;
            vertical-align: middle;
        }

        #tableproduct th {
            background-color: #003d73;
            color: white;
            font-weight: bold;
        }

        #tableproduct td.text-left {
            text-align: left;
        }

        #tableproduct td.text-right {
            text-align: right;
        }

        /* PRODUCT IMAGE */
        .product-image {
            width: 90px;
            height: 90px;
            object-fit: cover;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        /* TOTAL ROW */
        .total-row {
            background-color: #003d73;
            color: white;
            font-weight: bold;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }

        @media print {
            .no-print {
                display: none;
            }

            body {
                margin: 0;
                padding: 5px;
            }

            .product-image {
                width: 70px;
                height: 70px;
            }
        }
    </style>
</head>

<body>
    <div class="container">

        <!-- HEADER: LOGO + COMPANY -->
        <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:20px;">

            <!-- LEFT LOGO -->
            <div style="width:40%;">
                <img src="<?php echo base_url('assets/image/Logo Warna.png') ?>" style="width:150px; margin-bottom:10px;">
            </div>

            <!-- RIGHT COMPANY INFO -->
            <div style="width:60%; text-align:right; font-size:12px; line-height:1.4;">
                <strong style="font-size:14px;">CV Surya Jaya Makmur</strong><br>
                Taman Internasional 1 B6 / 30<br>
                Citraraya<br>
                Surabaya 60219 - East Java<br>
                Indonesia<br>
                Phone: +62816536516<br>
                Email: schianger@gmail.com
            </div>
        </div>

        <!-- BLUE LINE -->
        <div style="border-top:4px solid #003d73; margin-bottom:25px;"></div>

        <!-- TWO COLUMNS: SUPPLIER & PO INFO -->
        <div style="display:flex; justify-content:space-between;">

            <!-- LEFT SUPPLIER -->
            <div style="width:48%;">
                <h3 style="margin:0 0 8px 0; font-size:15px; color:#003d73; border-bottom:2px solid #003d73;">Supplier</h3>

                <div style="font-size:12px; line-height:1.5;">
                    <strong><?php echo strtoupper($po->name_supplier ?? '-'); ?></strong><br>
                    <?php echo nl2br($po->address_supplier ?? ''); ?>
                </div>
            </div>

            <!-- RIGHT PO INFO -->
            <div style="width:48%;">
                <h3 style="margin:0 0 8px 0; font-size:15px; color:#003d73; border-bottom:2px solid #003d73;">Purchase Order</h3>

                <table style="width:100%; font-size:12px; line-height:1.6;">
                    <tr>
                        <td><strong>PO Number</strong></td>
                        <td>: <?php echo $po->number_po ?? '-'; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Order Date</strong></td>
                        <td>: <?php echo $po->order_date ?? '-'; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Shipment Number</strong></td>
                        <td>: <?php echo $po->name_container ?? '-'; ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- PRODUCT TABLE -->
        <table id="tableproduct">
            <thead>
                <tr>
                    <th width="30">No</th>
                    <th width="110">Image</th>
                    <th>Product Name</th>
                    <th width="80">SKU</th>
                    <th width="80">SGS Type</th>
                    <th width="80">Unit Type</th>
                    <th width="80">Price</th>
                    <th width="60">Order Qty</th>
                    <th width="100">Total Value</th>
                    <th width="80">Description</th>
                </tr>
            </thead>

            <tbody>
                <?php if (!empty($detail_po)) : ?>
                <?php $no = 1;
                    foreach ($detail_po as $item) : ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td>
                        <img src="<?= base_url('assets/image/' . $item['row']->gambar) ?>" class="product-image">
                    </td>
                    <td class="text-left">
                        <?= htmlspecialchars($item['row']->nama_produk) ?><br>
                        <small>SKU: <?= htmlspecialchars($item['row']->sku) ?></small>
                    </td>
                    <td><?= htmlspecialchars($item['row']->sku) ?></td>
                    <td><?= htmlspecialchars($item['row']->type_sgs) ?></td>
                    <td><?= htmlspecialchars($item['row']->type_unit) ?></td>
                    <td class="text-right">$ <?= number_format($item['row']->price, 2, '.', ',') ?></td>
                    <td><?= htmlspecialchars($item['row']->qty_order) ?></td>
                    <td class="text-right">$ <?= number_format($item['item_value'], 2, '.', ',') ?></td>
                    <td><?= htmlspecialchars($item['row']->description) ?></td>
                </tr>
                <?php endforeach; ?>

                <!-- TOTAL -->
                <tr class="total-row">
                    <td colspan="7" class="text-right"><strong>TOTAL:</strong></td>
                    <td><strong><?= number_format($total_qty) ?></strong></td>
                    <td class="text-right">
                        <strong>
                            <?php
                                $currency = ($po->money_currency == 'rmb') ? '¥' : '$';
                                echo $currency . ' ' . number_format($total_value, 2, '.', ',');
                                ?>
                        </strong>
                    </td>
                    <td></td>
                </tr>

                <?php else : ?>
                <tr>
                    <td colspan="13" style="text-align:center; padding:20px; color:#777; font-style:italic;">
                        No products available for purchase order
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- FOOTER -->
        <div class="footer">
            <p>Generated on: <?php echo date('F j, Y H:i:s'); ?></p>
            <p>Document: Purchase Order Export</p>
        </div>

    </div>

    <script>
        window.onload = function() {
            document.title = "<?php echo 'Purchase_Order_' . ($po->number_po ?? 'PO') . '_' . date('Y-m-d_H-i-s'); ?>";
            setTimeout(() => window.print(), 80);
        }
    </script>

</body>

</html>