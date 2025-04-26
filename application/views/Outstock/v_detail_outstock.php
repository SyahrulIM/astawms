            <!-- Page content-->
            <div class="container-fluid">
                <div class="row">
                    <div class="col">
                        <h1 class="mt-4">Detail Barang Masuk - <?php echo $outstock_code ?></h1>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <table id="tableproduct" class="display" style="width:100%">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>SKU</th>
                                    <th>Nama Produk</th>
                                    <th>Gudang</th>
                                    <th>Jumlah</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($detailoutstock as $doskey => $dosvalue) { ?>
                                <tr>
                                    <td><?php echo $doskey + 1; ?></td>
                                    <td><?php echo $dosvalue->sku; ?></td>
                                    <td><?php echo $dosvalue->nama_produk; ?></td>
                                    <td><?php echo $dosvalue->nama_gudang; ?></td>
                                    <td><?php echo $dosvalue->jumlah; ?></td>
                                    <td><?php echo $dosvalue->keterangan; ?></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            </div>
            </div>
            <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
            <!-- 2. DataTables JS -->
            <script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>
            <!-- 3. Bootstrap bundle -->
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
            <!-- 4. Core theme JS -->
            <script src="<?php echo base_url(); ?>js/scripts.js"></script>

            <!-- Initialize DataTables AFTER all scripts are loaded -->
            <script>
                $(document).ready(function() {
                    new DataTable('#tableproduct', {
                        layout: {
                            bottomEnd: {
                                paging: {
                                    firstLast: false
                                }
                            }
                        }
                    });
                });
            </script>
            </body>

            </html>