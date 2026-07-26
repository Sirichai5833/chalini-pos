<script>
(() => {
    'use strict';

    const initializeProductList = () => {
        const root = document.querySelector('[data-product-list]');

        if (!root) {
            return;
        }

        const barcodeCheckUrl = root.dataset.barcodeCheckUrl;
        const productNameCheckUrl = root.dataset.productNameCheckUrl;
        let barcodeTimeout;
        let unitIndex = root.querySelectorAll('.unit-group').length;

        if (root.dataset.showCategoryModal === 'true') {
            const modalElement = root.querySelector('#addCategoryModal');

            if (modalElement && window.bootstrap?.Modal) {
                new window.bootstrap.Modal(modalElement).show();
            }
        }

        const fetchExists = async (url, parameter, value) => {
            const requestUrl = new URL(url, window.location.origin);
            requestUrl.searchParams.set(parameter, value);
            const response = await fetch(requestUrl);

            return response.json();
        };

        const warningFor = (input, className) => {
            const parent = input.closest('.mb-3');

            if (!parent) {
                return null;
            }

            let warning = parent.querySelector(`.${className}`);

            if (!warning) {
                warning = document.createElement('small');
                warning.classList.add(className, 'text-danger', 'd-block', 'mt-1');
                parent.appendChild(warning);
            }

            return warning;
        };

        const reIndexUnits = () => {
            root.querySelectorAll('#units-container .unit-group').forEach((unitGroup, index) => {
                unitGroup.querySelectorAll('[name^="units["]').forEach(input => {
                    const name = input.getAttribute('name');

                    if (name) {
                        input.setAttribute('name', name.replace(/units\[\d+\]/, `units[${index}]`));
                    }
                });

                const removeButton = unitGroup.querySelector('.remove-unit');

                if (removeButton) {
                    removeButton.classList.toggle('d-none', index === 0);
                }
            });

            unitIndex = root.querySelectorAll('.unit-group').length;
        };

        const addUnitButton = root.querySelector('#add-unit');

        addUnitButton?.addEventListener('click', () => {
            const container = root.querySelector('#units-container');

            if (!container) {
                return;
            }

            const templateHtml = `
                <div class="unit-group border rounded-3 p-3 mb-3 bg-light">
                    <h6 class="text-secondary small mb-3">หน่วยนับรอง</h6>
                    <div class="mb-3">
                        <label class="form-label small text-muted">ชื่อหน่วยนับ <span class="text-danger">*</span></label>
                        <input type="text" name="units[${unitIndex}][unit_name]" class="form-control rounded-3"
                            placeholder="เช่น แพ็ค, กล่อง" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small text-muted">จำนวน (เทียบกับหน่วยเล็กสุด) <span class="text-danger">*</span></label>
                        <input type="number" name="units[${unitIndex}][unit_quantity]" class="form-control rounded-3"
                            min="2" required>
                        <small class="text-muted">
                            กรอกจำนวนของหน่วยฐาน เช่น
                            แพ็ค = 6
                            ลัง = 24
                        </small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small text-muted">รหัสบาร์โค้ด</label>
                        <div class="input-group">
                            <input type="text" name="units[${unitIndex}][unit_barcode]" class="form-control rounded-start-3"
                                placeholder="ระบุเองหรือกดสร้าง" required>
                            <button type="button" class="btn btn-outline-secondary generate-barcode-btn rounded-end-3"
                                title="สร้างบาร์โค้ดสุ่ม">
                                <i class="bi bi-arrow-clockwise me-1"></i> สร้าง
                            </button>
                        </div>
                        <div class="barcode-preview mt-2 text-center" style="display:none;">
                            <canvas class="barcode-canvas"></canvas>
                            <p class="barcode-value mt-1 fw-bold text-dark"></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label small text-muted">ราคาขายปลีก <span class="text-danger">*</span></label>
                            <input type="number" name="units[${unitIndex}][price]" step="0.01"
                                class="form-control rounded-3" placeholder="0.00" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label small text-muted">ราคาขายส่ง <span class="text-danger">*</span></label>
                            <input type="number" name="units[${unitIndex}][wholesale]" step="0.01"
                                class="form-control rounded-3" placeholder="0.00" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label small text-muted">ราคาทุน <span class="text-danger">*</span></label>
                            <input type="number" name="units[${unitIndex}][cost_price]" step="0.01"
                                class="form-control rounded-3" placeholder="0.00" required>
                        </div>
                    </div>
                    <button type="button" class="btn btn-danger btn-sm remove-unit rounded-pill">
                        <i class="bi bi-trash me-1"></i> ลบหน่วยนับนี้
                    </button>
                </div>`;

            container.insertAdjacentHTML('beforeend', templateHtml);
            reIndexUnits();
        });

        root.addEventListener('click', event => {
            const generateButton = event.target.classList.contains('generate-barcode-btn')
                ? event.target
                : null;

            if (generateButton && root.contains(generateButton)) {
                const barcodeContainer = generateButton.closest('.mb-3');
                const input = barcodeContainer?.querySelector('input');
                const preview = barcodeContainer?.querySelector('.barcode-preview');
                const canvas = barcodeContainer?.querySelector('.barcode-canvas');
                const valueText = barcodeContainer?.querySelector('.barcode-value');

                if (!input || !preview || !canvas || !valueText || typeof window.JsBarcode !== 'function') {
                    return;
                }

                const barcodeValue = Math.random().toString().slice(2, 14);
                input.value = barcodeValue;
                window.JsBarcode(canvas, barcodeValue, {
                    format: 'CODE128',
                    height: 40,
                    displayValue: false,
                });
                valueText.textContent = barcodeValue;
                preview.style.display = 'block';
                input.dispatchEvent(new Event('input'));
                return;
            }

            const removeButton = event.target.classList.contains('remove-unit')
                ? event.target
                : null;

            if (!removeButton || !root.contains(removeButton)) {
                return;
            }

            const unitGroup = removeButton.closest('.unit-group');

            if (unitGroup && root.querySelectorAll('.unit-group').length > 1) {
                unitGroup.remove();
                reIndexUnits();
            } else {
                alert('ต้องมีหน่วยนับอย่างน้อยหนึ่งหน่วย');
            }
        });

        root.addEventListener('input', async event => {
            const input = event.target;

            if (!input.name?.includes('unit_barcode')) {
                return;
            }

            const barcode = input.value.trim();

            if (!barcode || !barcodeCheckUrl) {
                return;
            }

            try {
                const data = await fetchExists(barcodeCheckUrl, 'barcode', barcode);
                const warning = warningFor(input, 'barcode-warning');

                if (data.exists && warning) {
                    warning.textContent = '⚠️ บาร์โค้ดนี้มีอยู่แล้วในระบบ กรุณาใช้บาร์โค้ดอื่น';
                    input.classList.add('is-invalid');
                }
            } catch (error) {
                console.error('Barcode check error:', error);
            }
        });

        const productForm = root.querySelector('[data-product-create-form]');
        const submitButton = productForm?.querySelector('button[type="submit"]');
        const productNameInput = productForm?.querySelector('input[name="name"]');

        const toggleSubmitButton = () => {
            if (submitButton) {
                submitButton.disabled = root.querySelectorAll('.is-invalid').length > 0;
            }
        };

        productNameInput?.addEventListener('input', async function() {
            const name = this.value.trim();
            const warning = warningFor(this, 'name-warning');
            this.classList.remove('is-invalid');

            if (warning) {
                warning.textContent = '';
            }

            if (!name || !productNameCheckUrl) {
                toggleSubmitButton();
                return;
            }

            try {
                const data = await fetchExists(productNameCheckUrl, 'name', name);

                if (data.exists && warning) {
                    this.classList.add('is-invalid');
                    warning.textContent = '⚠️ ชื่อสินค้านี้มีอยู่แล้วในระบบ';
                }
            } catch (error) {
                console.error(error);
            }

            toggleSubmitButton();
        });

        root.addEventListener('input', event => {
            const input = event.target;

            if (!input.name?.includes('unit_barcode')) {
                return;
            }

            clearTimeout(barcodeTimeout);
            barcodeTimeout = setTimeout(async () => {
                const barcode = input.value.trim();
                const warning = warningFor(input, 'barcode-warning');
                let hasError = false;

                input.classList.remove('is-invalid');

                if (warning) {
                    warning.textContent = '';
                }

                root.querySelectorAll('input[name*="[unit_barcode]"]').forEach(item => {
                    if (item !== input && item.value.trim() === barcode && barcode !== '') {
                        hasError = true;
                    }
                });

                if (hasError) {
                    input.classList.add('is-invalid');

                    if (warning) {
                        warning.textContent = '⚠️ พบบาร์โค้ดซ้ำภายในฟอร์ม';
                    }

                    toggleSubmitButton();
                    return;
                }

                if (barcode && barcodeCheckUrl) {
                    try {
                        const data = await fetchExists(barcodeCheckUrl, 'barcode', barcode);

                        if (data.exists) {
                            input.classList.add('is-invalid');

                            if (warning) {
                                warning.textContent = '⚠️ บาร์โค้ดนี้มีอยู่แล้วในระบบ';
                            }
                        }
                    } catch (error) {
                        console.error(error);
                    }
                }

                toggleSubmitButton();
            }, 400);
        });

        productForm?.addEventListener('submit', event => {
            if (root.querySelectorAll('.is-invalid').length > 0) {
                event.preventDefault();
                alert('กรุณาแก้ไขข้อมูลที่ซ้ำก่อนบันทึก');
            }
        });

        const searchInput = root.querySelector('#searchProduct');

        searchInput?.addEventListener('input', function() {
            const keyword = this.value.toLowerCase().trim();

            root.querySelectorAll('.product-item').forEach(product => {
                const name = product.dataset.name || '';
                const id = product.dataset.id || '';
                product.style.display = name.includes(keyword) || id.includes(keyword) ? '' : 'none';
            });
        });
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeProductList, { once: true });
    } else {
        initializeProductList();
    }
})();
</script>
