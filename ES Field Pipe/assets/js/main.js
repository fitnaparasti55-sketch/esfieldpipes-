/**
 * Esfield Pipe - Master JavaScript
 * Theme Switcher, Cart AJAX, Coupon Validation & Pipe Sizing Calculator
 */

document.addEventListener('DOMContentLoaded', () => {
    initThemeToggle();
    initCartHandlers();
    initCouponHandler();
    initPipeCalculator();
});

// ==========================================
// 1. Dark & Light Theme Management
// ==========================================
function initThemeToggle() {
    const savedTheme = localStorage.getItem('esfield_theme') || 
        (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
    
    applyTheme(savedTheme);

    // Attach click listeners to all theme switcher buttons
    document.querySelectorAll('.theme-toggle-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const currentTheme = document.documentElement.getAttribute('data-bs-theme') || 'light';
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            applyTheme(newTheme);
            localStorage.setItem('esfield_theme', newTheme);
        });
    });
}

function applyTheme(theme) {
    document.documentElement.setAttribute('data-bs-theme', theme);
    document.querySelectorAll('.theme-toggle-btn').forEach(btn => {
        const icon = btn.querySelector('i');
        if (icon) {
            if (theme === 'dark') {
                icon.className = 'fa-solid fa-sun text-warning';
            } else {
                icon.className = 'fa-solid fa-moon text-secondary';
            }
        }
    });
}

// ==========================================
// 2. Toast Notification Helper
// ==========================================
function showToast(message, type = 'success') {
    let container = document.getElementById('toastContainer');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toastContainer';
        container.className = 'toast-container position-fixed top-0 end-0 p-3';
        container.style.zIndex = '9999';
        document.body.appendChild(container);
    }

    const toastId = 'toast_' + Date.now();
    const bgClass = type === 'success' ? 'bg-success' : (type === 'danger' ? 'bg-danger' : 'bg-primary');
    const iconClass = type === 'success' ? 'fa-circle-check' : (type === 'danger' ? 'fa-triangle-exclamation' : 'fa-info-circle');

    const html = `
        <div id="${toastId}" class="toast align-items-center text-white ${bgClass} border-0 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body d-flex align-items-center gap-2">
                    <i class="fa-solid ${iconClass} fs-5"></i>
                    <div>${message}</div>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `;

    container.insertAdjacentHTML('beforeend', html);
    const toastEl = document.getElementById(toastId);
    if (window.bootstrap && bootstrap.Toast) {
        const bsToast = new bootstrap.Toast(toastEl, { delay: 3500 });
        bsToast.show();
        toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
    } else {
        setTimeout(() => toastEl.remove(), 3500);
    }
}

// ==========================================
// 3. Cart AJAX Operations
// ==========================================
function initCartHandlers() {
    // Add to Cart Buttons
    document.querySelectorAll('.btn-add-to-cart').forEach(btn => {
        btn.addEventListener('click', async function (e) {
            e.preventDefault();
            const productId = this.getAttribute('data-product-id');
            const qtyInput = document.getElementById(`qty_${productId}`) || document.getElementById('productQty');
            const quantity = qtyInput ? parseInt(qtyInput.value, 10) : 1;

            const origContent = this.innerHTML;
            this.disabled = true;
            this.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Adding...';

            try {
                const formData = new FormData();
                formData.append('action', 'add');
                formData.append('product_id', productId);
                formData.append('quantity', quantity);

                const rootUrl = window.ESFIELD_BASE_URL || './';
                const res = await fetch(`${rootUrl}api/cart-action.php`, {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();

                if (data.success) {
                    showToast(data.message || 'Product added to cart!', 'success');
                    updateCartBadges(data.cart_count);
                } else {
                    showToast(data.message || 'Could not add to cart.', 'danger');
                }
            } catch (err) {
                console.error(err);
                showToast('Network error while adding to cart.', 'danger');
            } finally {
                this.disabled = false;
                this.innerHTML = origContent;
            }
        });
    });

    // Quantity Stepper on Cart Page
    document.querySelectorAll('.btn-qty-minus, .btn-qty-plus').forEach(btn => {
        btn.addEventListener('click', async function () {
            const isPlus = this.classList.contains('btn-qty-plus');
            const cartId = this.getAttribute('data-cart-id');
            const input = document.getElementById(`cart_qty_${cartId}`);
            if (!input) return;

            let currentVal = parseInt(input.value, 10) || 1;
            let newVal = isPlus ? currentVal + 1 : Math.max(1, currentVal - 1);
            if (newVal === currentVal) return;

            input.value = newVal;
            await updateCartItemQuantity(cartId, newVal);
        });
    });

    // Remove Cart Item Buttons
    document.querySelectorAll('.btn-remove-cart-item').forEach(btn => {
        btn.addEventListener('click', async function (e) {
            e.preventDefault();
            if (!confirm('Are you sure you want to remove this pipe from your order?')) return;
            const cartId = this.getAttribute('data-cart-id');
            await removeCartItem(cartId);
        });
    });
}

function updateCartBadges(count) {
    document.querySelectorAll('.cart-badge-count').forEach(badge => {
        badge.textContent = count;
        badge.style.display = count > 0 ? 'inline-block' : 'none';
    });
}

async function updateCartItemQuantity(cartId, quantity) {
    try {
        const formData = new FormData();
        formData.append('action', 'update');
        formData.append('cart_id', cartId);
        formData.append('quantity', quantity);

        const rootUrl = window.ESFIELD_BASE_URL || './';
        const res = await fetch(`${rootUrl}api/cart-action.php`, {
            method: 'POST',
            body: formData
        });
        const data = await res.json();

        if (data.success) {
            // Update line item total and summary box
            const lineEl = document.getElementById(`line_total_${cartId}`);
            if (lineEl && data.line_total) {
                lineEl.textContent = data.line_total;
            }
            updateSummaryUI(data);
            updateCartBadges(data.cart_count);
            showToast('Cart updated successfully.', 'success');
        } else {
            showToast(data.message || 'Failed to update quantity.', 'danger');
        }
    } catch (err) {
        console.error(err);
        showToast('Error communicating with server.', 'danger');
    }
}

async function removeCartItem(cartId) {
    try {
        const formData = new FormData();
        formData.append('action', 'remove');
        formData.append('cart_id', cartId);

        const rootUrl = window.ESFIELD_BASE_URL || './';
        const res = await fetch(`${rootUrl}api/cart-action.php`, {
            method: 'POST',
            body: formData
        });
        const data = await res.json();

        if (data.success) {
            const row = document.getElementById(`cart_row_${cartId}`);
            if (row) {
                row.style.opacity = '0';
                setTimeout(() => {
                    row.remove();
                    if (data.cart_count === 0) {
                        location.reload();
                    }
                }, 250);
            }
            updateSummaryUI(data);
            updateCartBadges(data.cart_count);
            showToast('Item removed from cart.', 'success');
        }
    } catch (err) {
        console.error(err);
        showToast('Error removing cart item.', 'danger');
    }
}

function updateSummaryUI(data) {
    if (data.subtotal && document.getElementById('summarySubtotal')) {
        document.getElementById('summarySubtotal').textContent = data.subtotal;
    }
    if (data.discount && document.getElementById('summaryDiscount')) {
        document.getElementById('summaryDiscount').textContent = data.discount;
    }
    if (data.gst_amount && document.getElementById('summaryGst')) {
        document.getElementById('summaryGst').textContent = data.gst_amount;
    }
    if (data.grand_total && document.getElementById('summaryGrandTotal')) {
        document.getElementById('summaryGrandTotal').textContent = data.grand_total;
    }
}

// ==========================================
// 4. Coupon Application
// ==========================================
function initCouponHandler() {
    const applyBtn = document.getElementById('btnApplyCoupon');
    const removeBtn = document.getElementById('btnRemoveCoupon');
    const couponInput = document.getElementById('couponCodeInput');

    if (applyBtn && couponInput) {
        applyBtn.addEventListener('click', async (e) => {
            e.preventDefault();
            const code = couponInput.value.trim();
            if (!code) {
                showToast('Please enter a coupon code.', 'warning');
                return;
            }

            applyBtn.disabled = true;
            applyBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';

            try {
                const formData = new FormData();
                formData.append('action', 'apply');
                formData.append('coupon_code', code);

                const rootUrl = window.ESFIELD_BASE_URL || './';
                const res = await fetch(`${rootUrl}api/apply-coupon.php`, {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();

                if (data.success) {
                    showToast(data.message, 'success');
                    setTimeout(() => location.reload(), 600);
                } else {
                    showToast(data.message || 'Invalid coupon code.', 'danger');
                }
            } catch (err) {
                console.error(err);
                showToast('Network error applying coupon.', 'danger');
            } finally {
                applyBtn.disabled = false;
                applyBtn.textContent = 'Apply';
            }
        });
    }

    if (removeBtn) {
        removeBtn.addEventListener('click', async (e) => {
            e.preventDefault();
            try {
                const formData = new FormData();
                formData.append('action', 'remove');
                const rootUrl = window.ESFIELD_BASE_URL || './';
                const res = await fetch(`${rootUrl}api/apply-coupon.php`, {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();
                if (data.success) {
                    showToast('Coupon removed.', 'info');
                    setTimeout(() => location.reload(), 500);
                }
            } catch (err) {
                console.error(err);
            }
        });
    }
}

// ==========================================
// 5. Interactive Hydraulic & Pipe Sizing Calculator
// ==========================================
function initPipeCalculator() {
    const calcForm = document.getElementById('pipeSizingForm');
    if (!calcForm) return;

    calcForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const discharge = parseFloat(document.getElementById('calcDischarge').value); // in Litres/sec
        const slope = parseFloat(document.getElementById('calcSlope').value); // e.g. 1 in 100 -> 0.01
        const pipeType = document.getElementById('calcAppType').value;

        if (isNaN(discharge) || isNaN(slope) || discharge <= 0 || slope <= 0) {
            showToast('Please enter valid positive numbers for flow and gradient slope.', 'warning');
            return;
        }

        // Hydraulic Manning's formula calculation for pipe flowing full (gravity flow)
        // Q = (1/n) * A * R^(2/3) * S^(1/2)
        // n for DWC HDPE = 0.009
        const n = 0.009;
        const S = 1 / slope; // slope gradient e.g. 0.01
        const Q_m3s = discharge / 1000; // convert L/s to m^3/s

        // D_req in meters: D = ( (Q * n * 4^(5/3)) / (pi * S^(1/2)) ) ^ (3/8)
        const dMeters = Math.pow((Q_m3s * n * Math.pow(4, 5/3)) / (Math.PI * Math.sqrt(S)), 3/8);
        const d_mm_exact = dMeters * 1000;

        // Map to standard available DWC nominal internal diameters
        const standardSizes = [100, 150, 200, 250, 300, 400, 500, 600, 800, 1000, 1200];
        let recommendedSize = standardSizes[standardSizes.length - 1];

        for (let size of standardSizes) {
            if (size >= d_mm_exact) {
                recommendedSize = size;
                break;
            }
        }

        // Calculate actual full-pipe flow capacity for recommended diameter
        const D_act = recommendedSize / 1000;
        const A = (Math.PI * Math.pow(D_act, 2)) / 4;
        const R = D_act / 4;
        const V = (1 / n) * Math.pow(R, 2/3) * Math.sqrt(S); // velocity in m/s
        const Q_cap = (V * A) * 1000; // capacity in L/s

        const resultBox = document.getElementById('calcResultBox');
        if (resultBox) {
            resultBox.style.display = 'block';
            document.getElementById('resNominalDia').textContent = `${recommendedSize} mm ID`;
            document.getElementById('resFlowVelocity').textContent = `${V.toFixed(2)} m/s (Self-cleansing: ${V >= 0.75 ? 'Optimal' : 'Adequate'})`;
            document.getElementById('resMaxCapacity').textContent = `${Q_cap.toFixed(1)} L/s`;
            document.getElementById('resStiffnessRec').textContent = pipeType === 'highway' ? 'SN8 (Heavy Axle Load)' : 'SN8 / SN4';
            
            const exploreBtn = document.getElementById('calcExploreBtn');
            if (exploreBtn) {
                exploreBtn.href = `products.php?search=${recommendedSize}mm`;
            }
            resultBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    });
}
