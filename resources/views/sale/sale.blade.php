@extends('layouts.layout')

@section('content')
    @if (session('success'))
        <x-sweet-alert icon="success" title="Oh Yeah!" text="{{ session('success') }}" confirm-button-text="Ok" />
    @endif

    @if (session('error'))
        <x-sweet-alert icon="error" title="Oops..." text="{{ session('error') }}" confirm-button-text="Ok" />
    @endif

    <div class="container-fluid py-4 px-0">
        <div class="row g-0 m-0">
            <!-- Product Table -->
            <div class="col-lg-8 col-12 px-3">
                <div class="card shadow-sm h-100 rounded-0">
                    <div class="card-body">
                        <h5 class="mb-3">ชื่อพนักงาน: {{ Auth::user()->name ?? 'Guest' }}</h5>
                        <h6 class="mb-4">วันที่: {{ date('d/m/Y') }}</h6>

                        <h4 class="mb-3">รายการสินค้า</h4>
                        <div class="mb-3">
                            <label class="form-label">ประเภทการขาย</label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="priceType" id="retail"
                                        value="retail" checked onchange="updatePriceTypeForAll(this.value)">
                                    <label class="form-check-label" for="retail">ปลีก</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="priceType" id="wholesale"
                                        value="wholesale" onchange="updatePriceTypeForAll(this.value)">
                                    <label class="form-check-label" for="wholesale">ส่ง</label>
                                </div>
                            </div>
                        </div>

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
                                    <!-- Products will be populated via JS -->
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Payment Section -->
            <div class="col-lg-4 col-12 px-3">
                <div class="card shadow-sm h-100 rounded-0">
                    <div class="card-body">
                        <h4 class="card-title mb-4">ชำระเงิน</h4>
                        <h5>ยอดรวมทั้งหมด</h5>
                        <h2 class="text-success mb-4" id="totalAmount">0 บาท</h2>

                        <div class="mb-3">
                            <label for="cash" class="form-label">เงินที่รับมา (เงินสด)</label>
                            <input type="number" class="form-control" id="cash"
                                placeholder="ใส่จำนวนเงินที่ลูกค้าให้มา">
                        </div>

                        <div class="mb-4">
                            <label for="change" class="form-label">เงินทอน</label>
                            <input type="text" class="form-control" id="change" readonly
                                placeholder="เงินทอนจะขึ้นที่นี่">
                        </div>

                        <div class="d-grid gap-2">
                            <button class="btn btn-success" onclick="payCash()">💵 ชำระด้วยเงินสด</button>
                            <button class="btn btn-primary" onclick="payQRCode()">📱 ชำระด้วย QR Code</button>
                            <button id="confirmQRButton" class="btn btn-info d-none mt-2" onclick="confirmPaymentByQR()">✅
                                ยืนยันรับเงินแล้ว</button>
                        </div>

                        <div class="text-center mt-4">
                            <img id="qrImage" src="" alt="QR Code" class="img-fluid"
                                style="display:none; max-width: 200px;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        const shopPromptPayID = "0843860015";
        let products = [];
        let totalAmount = 0;

        // รับข้อมูลสินค้าจาก Laravel (แบบ array)
        const productCatalog = @json($products);

        document.addEventListener('DOMContentLoaded', () => {
            renderProductList();
            updateTotalAmount();
            updateQRCode();

            let barcode = '';
            document.addEventListener('keydown', (e) => {
                // ถ้าผู้ใช้กด Enter (การแสกนเสร็จแล้ว)
                if (e.key === 'Enter') {
                    if (barcode.trim()) {
                        addProductByBarcode(barcode.trim()); // เพิ่มสินค้าโดยใช้บาร์โค้ด
                        barcode = ''; // รีเซ็ตบาร์โค้ดหลังจากแสกนเสร็จ
                    }
                } else {
                    barcode += e.key; // เพิ่มตัวอักษรที่เครื่องสแกนส่งมา
                }
            });
        });

        let isAlertOpen = false;

        function addProductByBarcode(barcode) {
            const product = productCatalog.find(p => p.barcode === barcode);
            if (product) {
                // เช็คว่ามีสินค้าที่มี ID และ Unit เดียวกันอยู่ในตะกร้าไหม
                const existingProduct = products.find(p => p.id === product.id && p.unit === product.unit);

                if (existingProduct) {
                    // ถ้ามีอยู่แล้ว เพิ่มจำนวน
                    existingProduct.qty += 1;
                } else {
                    // ถ้าไม่มี ให้เพิ่มเข้าไปใหม่
                    products.push({
                        ...product,
                        qty: 1,
                        price: parseFloat(product.retail_price),
                        price_type: 'retail'
                    });
                }

                renderProductList();
                updateTotalAmount();
                updateQRCode();
            } else {
                if (!isAlertOpen) {
                    isAlertOpen = true;
                    Swal.fire({
                        icon: 'error',
                        title: 'ไม่พบสินค้า',
                        text: `ไม่พบสินค้าที่มีบาร์โค้ด: ${barcode}`,
                        confirmButtonText: 'ตกลง',
                        allowOutsideClick: false,
                        allowEscapeKey: false
                    }).then(() => {
                        isAlertOpen = false;
                    });
                }
            }
        }

        function renderProductList() {
            const tbody = document.getElementById('product-list');
            tbody.innerHTML = '';

            products.forEach((product, index) => {
                const row = `
    <tr>
        <td>${index + 1}</td>
        <td>${product.id}</td>
        <td>${product.name}</td>
        <td>${product.freebie}</td>
        <td>${product.unit}</td>
        <td>
            <input type="number" class="form-control form-control-sm text-center" value="${product.qty}" min="1" onchange="updateQty(${index}, this.value)">
        </td>
        <td>${(product.price).toFixed(2)} ฿</td>
         <td>${(product.qty * product.price).toFixed(2)} ฿</td>
    </tr>
`;

                tbody.insertAdjacentHTML('beforeend', row);
            });
        }

        function updateQty(index, newQty) {
            let qty = parseInt(newQty);
            if (qty > 0) {
                products[index].qty = qty;
                renderProductList();
                updateTotalAmount();
                updateQRCode();
            }
        }

        function updateTotalAmount() {
            totalAmount = products.reduce((sum, p) => sum + (p.qty * p.price), 0);
            document.getElementById('totalAmount').innerText = totalAmount.toFixed(2) + " บาท";
        }

        function updateQRCode() {
            document.getElementById('qrImage').src = `https://promptpay.io/${shopPromptPayID}/${totalAmount.toFixed(2)}`;
        }

        function payCash() {
            const cash = parseFloat(document.getElementById('cash').value);
            const changeInput = document.getElementById('change');

            if (isNaN(cash) || cash < totalAmount) {
                Swal.fire({
                    icon: 'error',
                    title: 'ยอดเงินไม่พอ',
                    text: 'กรุณารับเงินมาให้มากกว่าหรือเท่ากับยอดรวม',
                    confirmButtonText: 'ตกลง'
                });
                return;
            }

            Swal.fire({
                title: 'ยืนยันการชำระเงิน?',
                text: `ยอดเงิน: ${totalAmount.toFixed(2)} บาท`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'ยืนยัน',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    const change = cash - totalAmount;
                    changeInput.value = change.toFixed(2) + ' บาท';
                    playSound();
                    submitSaleData(() => {
                        playSound();
                        printReceipt(cash, change);
                        clearCart();
                        Swal.fire({
                            icon: 'success',
                            title: 'ชำระเงินสดสำเร็จ!',
                            text: `เงินทอน: ${change.toFixed(2)} บาท`,
                            confirmButtonText: 'ตกลง'
                        });
                    });

                }
            });
        }

        function payQRCode() {
            document.getElementById('qrImage').style.display = 'block';
            document.getElementById('confirmQRButton').classList.remove('d-none');
            playSound();
            Swal.fire({
                icon: 'info',
                title: 'แสดง QR ให้ลูกค้าสแกน',
                text: 'หลังจากลูกค้าจ่ายเสร็จ กด "ยืนยันรับเงินแล้ว"',
                confirmButtonText: 'ตกลง'
            });
        }

        function confirmPaymentByQR() {
            Swal.fire({
                icon: 'success',
                title: 'รับชำระเรียบร้อย',
                confirmButtonText: 'ตกลง'
            }).then(() => {
                playSound();
                printReceipt(0, 0);
                clearCart();
            });
        }

        function printReceipt(cash, change) {
            const receiptWindow = window.open('', '', 'width=800,height=600');
            let productDetails = '';

            products.forEach((product, index) => {
                productDetails +=
                    `${index + 1}. ${product.name} (${product.qty} ${product.unit}) - ${product.price}฿ x ${product.qty} = ${(product.qty * product.price).toFixed(2)}฿<br>`;
            });

            receiptWindow.document.write(`
            <html>
            <head><title>ใบเสร็จรับเงิน</title></head>
            <body>
                <h2>Chalini POS</h2>
                <p>วันที่: ${new Date().toLocaleString()}</p>
                <hr>
                ${productDetails}
                <hr>
                <p>ยอดรวม: ${totalAmount.toFixed(2)} บาท</p>
                ${cash ? `<p>เงินสดรับมา: ${cash.toFixed(2)} บาท</p><p>เงินทอน: ${change.toFixed(2)} บาท</p>` : ''}
                <hr>
                <p>ขอบคุณที่ใช้บริการ</p>
            </body>
            </html>
        `);
            receiptWindow.document.close();
            receiptWindow.print();
        }

        function clearCart() {
            products = [];
            renderProductList();
            updateTotalAmount();
            updateQRCode();
            document.getElementById('cash').value = '';
            document.getElementById('change').value = '';
            document.getElementById('qrImage').style.display = 'none';
            document.getElementById('confirmQRButton').classList.add('d-none');
        }

        function playSound() {
            const soundUrl = '/sounds/cash.mp3';
            const audio = new Audio(soundUrl);
            audio.onerror = () => console.warn('ไม่พบไฟล์เสียง cash.mp3');
            audio.play();
        }

        function submitSaleData(callback) {
            fetch('{{ route('staff.update.stock') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        products: products.map(p => ({
                            id: p.id,
                            qty: p.qty
                        }))
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        callback();
                    } else {
                        Swal.fire('เกิดข้อผิดพลาด', data.message || 'ไม่สามารถบันทึกการขายได้', 'error');
                    }
                })
                .catch(err => {
                    console.error(err);
                    Swal.fire('ข้อผิดพลาด', 'การเชื่อมต่อกับเซิร์ฟเวอร์ล้มเหลว', 'error');
                });
        }

        function updatePriceTypeForAll(priceType) {
            products.forEach(product => {
                product.price_type = priceType;
                product.price = parseFloat(priceType === 'wholesale' ? product.wholesale_price : product
                    .retail_price);
            });
            renderProductList();
            updateTotalAmount();
            updateQRCode();
        }
    </script>
@endsection

