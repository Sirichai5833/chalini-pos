@extends('layouts.layout')

@section('content')
    <div class="container-fluid py-5 px-3">
        <form action="{{ route('product.products.add-stock-multi') }}" method="POST">
            @csrf

            <div class="row g-4">
                <!-- ... box แรก ข้อมูลพนักงานและวันที่ ... -->
                <div class="col-lg-4 col-12">
                    <div class="card shadow-sm rounded-3 h-100">
                        <div class="card-body">
                            <h4 class="mb-4 fw-bold">📋 ข้อมูลเบื้องต้น</h4>
                            <p class="mb-3"><strong>👤 พนักงาน:</strong> {{ Auth::user()->name }}
                                ({{ Auth::user()->role }})</p>
                            <p><strong>📅 วันที่:</strong> {{ now()->format('d/m/Y') }}</p>
                        </div>
                    </div>
                </div>
                <!-- กล่องตารางสินค้า -->
                <div class="col-lg-8 col-12">
                    <div class="card shadow-sm rounded-3">
                        <div class="card-body">
                            <h4 class="mb-4 fw-bold">รายการสินค้า</h4>
                            <div class="table-responsive">
                                <div class="mb-3">
    <label class="form-label fw-bold">สแกน / พิมพ์บาร์โค้ดสินค้า</label>
    <input 
        type="text"
        id="barcodeInput"
        class="form-control"
        placeholder="ยิงบาร์โค้ด หรือพิมพ์แล้วกด Enter">
</div>
                                <table class="table table-bordered table-hover text-center align-middle" id="productTable">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>สินค้า</th>
                                            <th>หน่วย</th>
                                            <th>จำนวน</th>
                                            <th>เพิ่มเข้าที่</th>
                                            <th>เป็นของแถม</th>                                           
                                            <th>วันหมดอายุ</th>
                                            <th>ลบ</th>
                                        </tr>
                                    </thead>
                                    <tbody id="product-body">
                                        {{-- JS เพิ่มแถวที่นี่ --}}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ปุ่มบันทึก -->
                <div class="col-12">
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary btn-lg shadow px-4">
                            ✅ บันทึกการเพิ่มสินค้า
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- JS สำหรับการแสกนบาร์โค้ด --}}
    <script>
        let barcode = '';
let lastTime = Date.now();
const barcodeInput = document.getElementById('barcodeInput');
document.addEventListener('keydown', function(e) {

    const tag = e.target.tagName.toLowerCase();
    if (tag === 'input' || tag === 'textarea') return;

    const currentTime = Date.now();

    if (currentTime - lastTime > 100) {
        barcode = '';
    }

    if (e.key === 'Enter') {
        e.preventDefault();

        if (barcode.length > 0) {
            fetchProduct(barcode);
            barcode = '';
        }

    } else {
        barcode += e.key;
    }

    lastTime = currentTime;
});

barcodeInput.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();

        const code = this.value.trim();
        if (code !== '') {
            fetchProduct(code);
            this.value = '';
        }
    }
});

        function fetchProduct(barcode) {

    fetch(`/products/barcode/${barcode.trim()}`)

    .then(res => res.json())

    .then(data => {


        if(data.success){


            console.log(data.product);


            addProductRow(data.product);


        }else{


            alert('❌ ไม่พบสินค้านี้');


        }


    })

    .catch(err => console.error(err));

}

        function addProductRow(product) {

    const tbody = document.getElementById('product-body');

    const rowKey = `${product.id}_${product.unit_id}`;

    const existingRow = Array.from(tbody.rows)
        .find(row => row.dataset.rowKey === rowKey);


    // จำนวนเริ่มต้น = 1 หน่วย
    const defaultQty = 1;


    if (existingRow) {

        const qty = existingRow.querySelector(
            `input[name="items[${product.id}][${product.unit_id}][quantity]"]`
        );

        qty.value = parseInt(qty.value) + 1;


    } else {


        const row = document.createElement('tr');

        row.dataset.rowKey = rowKey;


        row.innerHTML = `

<td>

    ${product.name}


    <input type="hidden"
        name="items[${product.id}][${product.unit_id}][product_id]"
        value="${product.id}">


    <input type="hidden"
        name="items[${product.id}][${product.unit_id}][unit_id]"
        value="${product.unit_id}">


    <input type="hidden"
        name="items[${product.id}][${product.unit_id}][unit_name]"
        value="${product.unit_name}">


    <input type="hidden"
        name="items[${product.id}][${product.unit_id}][unit_quantity]"
        value="${product.unit_quantity || 1}">

</td>


<td>
    ${product.unit_name || '-'}
</td>


<td>

<input type="number"
    name="items[${product.id}][${product.unit_id}][quantity]"
    value="${defaultQty}"
    min="1"
    class="form-control text-center">

</td>


<td>

<select 
name="items[${product.id}][${product.unit_id}][location]"
class="form-select">

<option value="warehouse">
คลัง
</option>

<option value="store">
หน้าร้าน
</option>

</select>

</td>



<td>

<input type="hidden"
name="items[${product.id}][${product.unit_id}][is_free]"
value="0">


<div class="form-check mt-1">

<input class="form-check-input"
type="checkbox"
onchange="toggleFree(this)">


<label class="form-check-label">
เป็นของแถม
</label>


</div>

</td>




<td>


<input 
type="date"
name="items[${product.id}][${product.unit_id}][expiry_date]"
class="form-control expiry-input">


<div class="form-check mt-1">


<input class="form-check-input"
type="checkbox"
onchange="toggleNoExpiry(this)">


<label class="form-check-label">
ไม่มีวันหมดอายุ
</label>


</div>


</td>



<td>

<button type="button"
onclick="this.closest('tr').remove()"
class="btn btn-outline-danger btn-sm">

ลบ

</button>


</td>


`;


        tbody.appendChild(row);

    }

}
        function toggleFree(checkbox) {
    const hiddenInput = checkbox.closest('td').querySelector('input[type="hidden"][name*="[is_free]"]');
    if (hiddenInput) {
        hiddenInput.value = checkbox.checked ? 1 : 0;
    }
}
document.addEventListener("DOMContentLoaded", function(){
    barcodeInput.focus();
});

function toggleNoExpiry(checkbox) {
    const td = checkbox.closest('td');
    const dateInput = td.querySelector('.expiry-input');

    if (checkbox.checked) {
        dateInput.value = '2099-12-31';
        dateInput.disabled = true;
    } else {
        dateInput.disabled = false;
    }
}
    </script>
@endsection
