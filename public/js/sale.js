const shopPromptPayID = "0843860015"; // เบอร์พร้อมเพย์ร้าน
let products = [];
let totalAmount = 0;

// รับข้อมูลสินค้าจาก Laravel ที่แปลงเป็น JSON
let productCatalog = window.productCatalog || {};

// DOM loaded
document.addEventListener('DOMContentLoaded', () => {
    renderProductList();
    updateTotalAmount();
    updateQRCode();

    // สแกนบาร์โค้ดโดยไม่ต้อง input แบบพิมพ์เอง
    let barcode = '';
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
            if (barcode) {
                addProductByBarcode(barcode);
                barcode = '';
            }
        } else {
            barcode += e.key;
        }
    });
});

function addProductByBarcode(barcode) {
    const product = productCatalog[barcode.trim()];

    if (product) {
        const existingProduct = products.find(p => p.id === product.id);
        if (existingProduct) {
            existingProduct.qty += 1;
        } else {
            products.push({ ...product, qty: 1 });
        }
        renderProductList();
        updateTotalAmount();
        updateQRCode();
    } else {
        Swal.fire({
            icon: 'error',
            title: 'ไม่พบสินค้า',
            text: `ไม่พบสินค้าที่มีบาร์โค้ด: ${barcode}`,
            confirmButtonText: 'ตกลง'
        });
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
                <td>${product.price} ฿</td>
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
            printReceipt(cash, change);
            clearCart();
            Swal.fire({
                icon: 'success',
                title: 'ชำระเงินสดสำเร็จ!',
                text: `เงินทอน: ${change.toFixed(2)} บาท`,
                confirmButtonText: 'ตกลง'
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
        productDetails += `${index + 1}. ${product.name} (${product.qty} ${product.unit}) - ${product.price}฿ x ${product.qty} = ${(product.qty * product.price).toFixed(2)}฿<br>`;
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