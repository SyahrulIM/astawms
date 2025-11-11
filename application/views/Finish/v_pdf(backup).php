<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Export Pre-Order - <?php echo $po->number_po ?? 'PO'; ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }

        .container {
            width: 100%;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header img {
            width: 150px;
            height: auto;
        }

        .header h1 {
            margin: 10px 0;
            font-size: 24px;
            color: #333;
        }

        .po-info {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }

        .po-info h5 {
            margin: 5px 0;
            font-size: 14px;
        }

        #tableproduct {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
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
            background-color: #f0f0f0;
            font-weight: bold;
        }

        #tableproduct td.text-left {
            text-align: left;
        }

        #tableproduct td.text-right {
            text-align: right;
        }

        .product-info {
            display: flex;
            align-items: center;
            justify-content: left;
            gap: 8px;
        }

        .product-info img {
            width: 40px;
            height: 40px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        .product-name {
            text-align: left;
        }

        .total-row {
            background-color: #e9ecef;
            font-weight: bold;
        }

        .warning-message {
            text-align: center;
            padding: 20px;
            color: #666;
            font-style: italic;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }

        @media print {
            body {
                margin: 0;
                padding: 10px;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <img src="<?php echo base_url('assets/image/Logo Warna.png') ?>" alt="Company Logo">
            <h1>Pre-Order Analysis Report</h1>
        </div>

        <!-- PO Information -->
        <div class="po-info">
            <h5><strong>Nomor PO:</strong> <?php echo $po->number_po ? htmlspecialchars($po->number_po) : '-'; ?></h5>
            <h5><strong>Nama Container:</strong> <?php echo $po->name_container ? htmlspecialchars($po->name_container) : '-'; ?></h5>
            <h5><strong>Tanggal Pesan:</strong> <?php echo $po->order_date ? htmlspecialchars($po->order_date) : '-'; ?></h5>
            <h5><strong>Mata Uang:</strong> <?php echo $po->money_currency ? strtoupper(htmlspecialchars($po->money_currency)) : '-'; ?></h5>
        </div>

        <!-- Products Table -->
        <table id="tableproduct">
            <thead>
                <tr>
                    <th width="30">No</th>
                    <th>Nama Produk</th>
                    <th width="80">SKU</th>
                    <th width="80">SGS/Non-SGS</th>
                    <th width="80">Tipe Satuan</th>
                    <th width="80">Stock Masuk Terakhir</th>
                    <th width="80">Penjualan Bulan Lalu</th>
                    <th width="80">Penjualan Bulan Ini</th>
                    <th width="80">Saldo Hari Ini</th>
                    <th width="80">Avg Sales vs Stock</th>
                    <th width="60">Qty Order</th>
                    <th width="80">Price per Unit</th>
                    <th width="100">Total Value</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($detail_po)) : ?>
                <?php $no = 1;
                    foreach ($detail_po as $item) : ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td class="text-left">
                        <div class="product-info">
                            <img src="<?= base_url('assets/image/' .  $item['row']->gambar) ?>" alt="">
                            <div class="product-name">
                                <?= htmlspecialchars($item['row']->nama_produk) ?><br>
                                <small><?= htmlspecialchars($item['row']->sku) ?></small>
                            </div>
                        </div>
                    </td>
                    <td><?= htmlspecialchars($item['row']->sku) ?></td>
                    <td><?= htmlspecialchars($item['row']->type_sgs) ?></td>
                    <td><?= htmlspecialchars($item['row']->type_unit) ?></td>
                    <td><?= htmlspecialchars($item['row']->latest_incoming_stock) ?></td>
                    <td><?= htmlspecialchars($item['row']->last_mouth_sales) ?></td>
                    <td><?= htmlspecialchars($item['row']->current_month_sales) ?></td>
                    <td><?= htmlspecialchars($item['row']->balance_per_today) ?></td>
                    <td><?= htmlspecialchars($item['avg_vs_stock']) ?></td>
                    <td><?= htmlspecialchars($item['row']->qty_order) ?></td>
                    <td class="text-right"><?= number_format($item['row']->price, 0, ',', '.') ?></td>
                    <td class="text-right"><?= number_format($item['item_value'], 2, ',', '.') ?></td>
                </tr>
                <?php endforeach; ?>

                <tr class="total-row">
                    <td colspan="10" class="text-right"><strong>TOTAL:</strong></td>
                    <td><strong><?= number_format($total_qty) ?></strong></td>
                    <td></td>
                    <td class="text-right"><strong><?= number_format($total_value, 2) ?></strong></td>
                </tr>
                <?php else : ?>
                <tr>
                    <td colspan="13" class="warning-message">
                        Tidak ada produk dengan Avg Sales vs Stock di bawah 1.00
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Footer -->
        <div class="footer">
            <p>Generated on: <?= date('Y-m-d H:i:s'); ?></p>
            <p>© <?= date('Y'); ?> Asta Homeware. All rights reserved.</p>
        </div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>

</html>