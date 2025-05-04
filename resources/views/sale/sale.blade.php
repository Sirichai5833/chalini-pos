@extends('layouts.layout')

@section('content')


<div class="container-fluid py-4">
    <div class="d-flex justify-content-center">
        <div class="row g-4" style="max-width: 1200px; width: 100%;">

            <!-- ตารางสินค้า -->
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="mb-3">ชื่อพนักงาน: {{ Auth::user()->name ?? 'Guest' }}</h5>
                        <h6 class="mb-4">วันที่: {{ date('d/m/Y') }}</h6>

                        <h4 class="mb-3">รายการสินค้า</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover text-center align-middle" id="productTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ลำดับ</th>
                                        <th>รหัสสินค้า</th>
                                        <th>ชื่อสินค้า</th>
                                        <th>ของแถม</th>
                                        <th>หน่วย</th>
                                        <th>จำนวน</th>
                                        <th>ราคาสินค้า</th>
                                        <th>ราคารวม</th>
                                    </tr>
                                </thead>
                                <tbody id="product-list">
                                    <!-- สินค้าจะเติมจาก JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ช่องชำระเงิน -->
            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h4 class="card-title mb-4">ชำระเงิน</h4>

                        <h5>ยอดรวมทั้งหมด</h5>
                        <h2 class="text-success mb-4" id="totalAmount">0 บาท</h2>

                        <div class="mb-3">
                            <label for="cash" class="form-label">เงินที่รับมา (เงินสด)</label>
                            <input type="number" class="form-control" id="cash" placeholder="ใส่จำนวนเงินที่ลูกค้าให้มา">
                        </div>

                        <div class="mb-4">
                            <label for="change" class="form-label">เงินทอน</label>
                            <input type="text" class="form-control" id="change" readonly placeholder="เงินทอนจะขึ้นที่นี่">
                        </div>

                        <div class="d-grid gap-2">
                            <button class="btn btn-success" onclick="payCash()">💵 ชำระด้วยเงินสด</button>
                            <button class="btn btn-primary" onclick="payQRCode()">📱 ชำระด้วย QR Code</button>
                            <button id="confirmQRButton" class="btn btn-info d-none mt-2" onclick="confirmPaymentByQR()">✅ ยืนยันรับเงินแล้ว</button>
                        </div>

                        <div class="text-center mt-4">
                            <img id="qrImage" src="" alt="QR Code" class="img-fluid" style="display:none; max-width: 200px;">
                        </div>
                    </div>
                </div>
            </div>
           
        </div> <!-- /row -->
    </div> <!-- /d-flex -->
</div> <!-- /container -->

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/sale.js') }}"></script>

@endsection



