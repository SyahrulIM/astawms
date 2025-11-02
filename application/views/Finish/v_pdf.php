<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Export Pre-Order</title>
</head>

<style>
    #tableproduct,
    #tableproduct th,
    #tableproduct td {
        border: 1px solid black;
        border-collapse: collapse;
    }

    #tableproduct th,
    #tableproduct td {
        padding: 8px;
        text-align: left;
    }
</style>

<body>
    <div class="container">
        <div class="row">
            <div class="col" style="text-align: center;">
                <img src="<?php echo base_url('assets/image/Logo Warna.png') ?>" alt="" style="width: 20%;">
            </div>
        </div>

        <div class="row">
            <div class="col" style="text-align: center;">
                <h1>Pre-order</h1>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <h5>Nomer PO: <?php echo $po->number_po ?></h5>
                <h5>Tanggal Pesan: <?php echo $po->order_date ?></h5>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <table id="tableproduct" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Produk</th>
                            <th>SKU</th>
                            <th>SGS/Non-SGS</th>
                            <th>Tipe Satuan</th>
                            <th>Stock Masuk Terakhir</th>
                            <th>Penjualan Bulan Lalu</th>
                            <th>Minggu 1</th>
                            <th>Minggu 2</th>
                            <th>Minggu 3</th>
                            <th>Minggu 4</th>
                            <th>Saldo Hari Ini</th>
                            <th>Avg Sales vs Stock (Bulan)</th>
                            <th>Qty Order</th>
                            <th>Price per Unit</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($detail_po as $key => $value) {

                            // Hitung total penjualan 4 minggu
                            $total_sales = floatval($value->sale_week_one)
                                + floatval($value->sale_week_two)
                                + floatval($value->sale_week_three)
                                + floatval($value->sale_week_four);

                            // Hitung rata-rata per minggu
                            $avg_sales = $total_sales / 4;

                            // Hindari pembagian nol
                            if ($avg_sales > 0) {
                                $avg_vs_stock = floatval($value->balance_per_today) / $avg_sales;
                                $avg_vs_stock = number_format($avg_vs_stock, 2); // 2 angka desimal
                            } else {
                                $avg_vs_stock = '<span class="text-muted">N/A</span>';
                            }
                            ?>
                        <tr>
                            <td><?php echo $key + 1; ?></td>
                            <td><?php echo $value->nama_produk; ?></td>
                            <td><?php echo $value->sku; ?></td>
                            <td><?php echo $value->type_sgs; ?></td>
                            <td><?php echo $value->type_unit; ?></td>
                            <td><?php echo $value->latest_incoming_stock; ?></td>
                            <td><?php echo $value->sale_last_mouth; ?></td>
                            <td><?php echo $value->sale_week_one; ?></td>
                            <td><?php echo $value->sale_week_two; ?></td>
                            <td><?php echo $value->sale_week_three; ?></td>
                            <td><?php echo $value->sale_week_four; ?></td>
                            <td><?php echo $value->balance_per_today; ?></td>
                            <td><?php echo $avg_vs_stock; ?></td>
                            <td><?php echo $value->qty_order; ?></td>
                            <td><?php echo $value->price; ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

<script>
    window.onload = function() {
        window.print();
    };
</script>

</html>