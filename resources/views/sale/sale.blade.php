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
                                        <th>ลบ</th>
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
    const productCatalog = @json($products);
    let isAlertOpen = false;

    document.addEventListener('DOMContentLoaded', () => {
        renderProductList();
        updateTotalAmount();
        updateQRCode();
        setupBarcodeScanner();
    });

    function setupBarcodeScanner() {
    let barcode = '';
    let lastTime = Date.now();

    document.addEventListener('keydown', (e) => {
        // ✅ ข้ามถ้า user กำลังพิมพ์ใน input/textarea
        const tag = e.target.tagName.toLowerCase();
        if (tag === 'input' || tag === 'textarea') return;

        const currentTime = Date.now();
        const timeDiff = currentTime - lastTime;

        if (timeDiff > 100) {
            barcode = ''; // reset ถ้าห่างเกินไป
        }

        if (e.key === 'Enter') {
            if (barcode.trim()) {
                addProductByBarcode(barcode.trim());
                barcode = '';
            }
        } else {
            barcode += e.key;
        }

        lastTime = currentTime;
    });
}


   function addProductByBarcode(barcode) {
    const product = productCatalog.find(p => p.barcode === barcode);

    // ❌ ใช้ alert() แบบเดิมสำหรับกรณีพิเศษ
    if (!product) {
        return alert(`ไม่พบสินค้าที่มีบาร์โค้ด: ${barcode}`);
    }

    if (!product.is_active || product.is_active == 0) {
        return alert(`สินค้า "${product.name}" (หน่วย: ${product.unit}) ถูกปิดการขาย`);
    }

    const existing = products.find(p => p.id === product.id && p.unit === product.unit);

    if (existing) {
        existing.qty += 1;
    } else {
        products.push({
            ...product,
            qty: 1,
            price: parseFloat(product.retail_price),
            price_type: 'retail',
        });
    }

    renderProductList();
    updateTotalAmount();
    updateQRCode();
}


   function renderProductList() {
    const tbody = document.getElementById('product-list');
    tbody.innerHTML = '';

    products.forEach((p, i) => {
        const row = `
            <tr>
                <td>${i + 1}</td>
                <td>${p.id}</td>
                <td>${p.name}</td>
                <td>${p.freebie ? p.freebie : 'ไม่มี'}</td>
                <td>${p.unit}</td>
                <td>
                    <input type="number" class="form-control form-control-sm text-center"
                        value="${p.qty}" min="1" onchange="updateQty(${i}, this.value)">
                </td>
                <td>${p.price.toFixed(2)} ฿</td>
                <td>${(p.qty * p.price).toFixed(2)} ฿</td>
                <td>
                    <button class="btn btn-danger btn-sm" onclick="removeProduct(${i})">ลบ</button>
                </td>
            </tr>
        `;
        tbody.insertAdjacentHTML('beforeend', row);
    });
}

function removeProduct(index) {
    products.splice(index, 1);  // ลบสินค้าออกจากตะกร้า
    renderProductList();         // แสดงรายการสินค้าหลังจากลบ
    updateTotalAmount();         // อัปเดตยอดรวม
    updateQRCode();              // อัปเดต QR Code
}


    function updateQty(index, qty) {
        const quantity = parseInt(qty);
        if (quantity > 0) {
            products[index].qty = quantity;
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
    if (products.length === 0 || totalAmount === 0) {
        return showAlert('warning', 'ไม่มีสินค้า', 'กรุณาเพิ่มสินค้าในตะกร้าก่อนชำระเงิน');
    }

    const cash = parseFloat(document.getElementById('cash').value);
    const changeInput = document.getElementById('change');

    if (isNaN(cash) || cash < totalAmount) {
        return showAlert('error', 'ยอดเงินไม่พอ', 'กรุณารับเงินมาให้มากกว่าหรือเท่ากับยอดรวม');
    }

        Swal.fire({
            title: 'ยืนยันการชำระเงิน?',
            text: `ยอดเงิน: ${totalAmount.toFixed(2)} บาท`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'ยืนยัน',
            cancelButtonText: 'ยกเลิก'
        }).then((res) => {
            if (res.isConfirmed) {
                const change = cash - totalAmount;
                changeInput.value = change.toFixed(2) + ' บาท';
                playSound();

                submitSaleData(() => {
                    playSound();
                    printReceipt(cash, change);
                    clearCart();
                    showAlert('success', 'ชำระเงินสดสำเร็จ!', `เงินทอน: ${change.toFixed(2)} บาท`);
                });
            }
        });
    }

    function payQRCode() {
    if (products.length === 0 || totalAmount === 0) {
        return showAlert('warning', 'ไม่มีสินค้า', 'กรุณาเพิ่มสินค้าในตะกร้าก่อนสร้าง QR Code');
    }

    document.getElementById('qrImage').style.display = 'block';
    document.getElementById('confirmQRButton').classList.remove('d-none');
    showAlert('info', 'แสดง QR ให้ลูกค้าสแกน', 'หลังจากลูกค้าจ่ายเสร็จ กด "ยืนยันรับเงินแล้ว"');
}


    function confirmPaymentByQR() {
    submitSaleData(() => {
        showAlert('success', 'รับชำระเรียบร้อย').then(() => {
            playSound();
            printReceipt(0, 0);
            clearCart();
        });
    });
}


    function printReceipt(cash, change) {
        const receiptWindow = window.open('', '', 'width=800,height=600');
        let content = `
            <html><head><title>ใบเสร็จรับเงิน</title></head><body>
            <h2>Chalini POS</h2>
            <p>วันที่: ${new Date().toLocaleString()}</p><hr>
        `;

        products.forEach((p, i) => {
            content += `${i + 1}. ${p.name} (${p.qty} ${p.unit}) - ${p.price}฿ x ${p.qty} = ${(p.qty * p.price).toFixed(2)}฿<br>`;
        });

        content += `
            <hr>
            <p>ยอดรวม: ${totalAmount.toFixed(2)} บาท</p>
            ${cash ? `<p>เงินสดรับมา: ${cash.toFixed(2)} บาท</p><p>เงินทอน: ${change.toFixed(2)} บาท</p>` : ''}
            <hr><p>ขอบคุณที่ใช้บริการ</p></body></html>
        `;

        receiptWindow.document.write(content);
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

    barcode = ''; // ✅ ล้างบาร์โค้ดเก่าที่อาจยังค้างอยู่
    }

    function playSound() {
        const audio = new Audio('/sounds/cash.mp3');
        audio.onerror = () => console.warn('ไม่พบไฟล์เสียง cash.mp3');
        audio.play();
    }

    function showAlert(icon, title, text = '') {
    return Swal.fire({
        icon: icon, // 'success', 'error', 'warning', 'info', 'question'
        title: title,
        text: text,
        confirmButtonText: 'ตกลง',
        confirmButtonColor: '#3085d6'
    });
}

  function submitSaleData(callback) {
    const total_price = products.reduce((sum, p) => sum + p.qty * p.price, 0);
    console.log(JSON.stringify(products, null, 2)); // ดูใน DevTools
    fetch('{{ route('update.stock') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
              'Accept': 'application/json',  // ← เพิ่มบรรทัดนี้!
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
             total_price,
            products: products.map(p => ({ id: p.id, product_unit_id: p.unit_id || 1, qty: p.qty,  price: p.price,  price_type: p.price_type   }))
        })
    })
    .then(async res => {
  const text = await res.text(); // รับเป็น text ชั่วคราว
  console.log('🔴 Raw Response:', text);
    return JSON.parse(text); // ✅ return data ออกไป
  try {
    const data = JSON.parse(text);
    if (data.success) {
      callback();
    } else {
      showAlert('error', 'เกิดข้อผิดพลาด', data.message || 'ไม่สามารถบันทึกการขายได้');
    }
  } catch (e) {
    showAlert('error', 'JSON Parse Error', text);  // แสดง HTML ตอบกลับเลย
  }
})

    .then(data => {
        if (data.success) {
            callback(); // ✅ เรียก callback ได้
        } else {
            showAlert('error', 'เกิดข้อผิดพลาด', data.message || 'ไม่สามารถบันทึกการขายได้');
        }
    })
    .catch((err) => {
        console.error("submitSaleData Error", err);
        showAlert('error', 'ข้อผิดพลาด', 'การเชื่อมต่อกับเซิร์ฟเวอร์ล้มเหลว');
    });
}




    function updatePriceTypeForAll(priceType) {
        products.forEach(p => {
            p.price_type = priceType;
            p.price = parseFloat(priceType === 'wholesale' ? p.wholesale_price : p.retail_price);
        });
        renderProductList();
        updateTotalAmount();
        updateQRCode();
    }

    function updateTotalAmount() {
    totalAmount = products.reduce((sum, p) => sum + (p.qty * p.price), 0);
    document.getElementById('totalAmount').innerText = totalAmount.toFixed(2) + " บาท";

    const hasProduct = products.length > 0 && totalAmount > 0;
    document.querySelector('button[onclick="payCash()"]').disabled = !hasProduct;
    document.querySelector('button[onclick="payQRCode()"]').disabled = !hasProduct;
}

</script>

@endsection

