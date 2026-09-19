# Phase 0 UI Audit

Branch: `redesign/production-ready-ui`

This inventory is based on the active Laravel route registry, controller return values, Blade references, and static CSS/JavaScript inspection. It intentionally separates active HTML pages from JSON/supporting endpoints and legacy surfaces.

## Active UI inventory

### Staff/Admin and dashboard

| Page | Route | Controller method | Blade | Access | Layout | Audit note |
|---|---|---|---|---|---|---|
| Admin entry | `admin` | `AdminController::index` | `index` | admin | Missing | Active route currently references a missing view |
| Manager dashboard | `manager.dashboard` | `ManagerController::dashboard` | `manager.dashboard` | admin, manager, owner | `layouts.layout` | Large inline CSS/JS and chart/dashboard logic |
| Shop settings | `staff.settings` | `SettingController::index` | `staff.settings` | admin, manager, staff, owner | `layouts.layout` | Update action is admin-only |
| Staff list | `staff.index` | `StaffController::index` | `staff.index` | admin, manager, staff, owner | `layouts.layout` | Destructive delete has confirmation |
| Create staff | `staff.create` | `StaffController::create` | `staff.create-staff` | admin, manager, staff, owner | `layouts.layout` | Role options require later UI permission review |
| Staff profile | `staff.edit` | `StaffController::edit` | `staff.edit` | admin, manager, staff, owner | `layouts.layout` | Profile and management use the same surface |
| Administrative staff edit | `staff.alledit` | `StaffController::alledit` | `staff.alledit` | admin, manager, staff, owner | `layouts.layout` | High-impact role/account form |
| Audit log | `staff.audits` | `StaffController::auditLogs` | `staff.audit_logs` | admin, manager, staff, owner | `layouts.layout` | Wide table |
| Member list | `members.index` | `MemberController::index` | `members.index` | admin, manager, staff, owner | `layouts.layout` | Large room-card grid; destructive actions confirmed |
| Create member | `members.create` | `MemberController::create` | `members.create-member` | admin, manager, staff, owner | `layouts.layout` | Several modal/script blocks |
| Member detail | `members.show` | `MemberController::show` | `members.show` | admin, manager, staff, owner | `layouts.layout` | Credit/customer actions require later review |
| Edit member | `members.edit` | `MemberController::edit` | `members.edit` | admin, manager, staff, owner | `layouts.layout` | Ownership remains enforced server-side |

### POS and sales

| Page | Route | Controller method | Blade | Access | Layout | Audit note |
|---|---|---|---|---|---|---|
| POS | `sale` | `SaleController::index` | `sale.sale` | admin, manager, owner, staff + open shop/shift | `layouts.layout` | Highest-risk page: 1,300+ lines, 29 inline handlers, receipt/payment/idempotency contracts |
| Resource POS entry | `staff.sales.index` | `SaleController::index` | `sale.sale` | admin, manager, staff, owner | `layouts.layout` | Duplicate entry surface; not the primary sidebar URL |
| Sales history | `staff.sales.history` | `SaleController::history` | `sale.history` | admin, manager, staff, owner | `layouts.layout` | Print/export JavaScript |
| Sale detail/receipt | `staff.sales.show` | `SaleController::show` | `sale.show` | admin, manager, staff, owner | `layouts.layout` | Print and cancellation/destructive handler |

### Orders

| Page | Route | Controller method | Blade | Access | Layout | Audit note |
|---|---|---|---|---|---|---|
| All orders | `orders.list` | `OrderController::ordersList` | `sale.order` | admin, manager, staff, owner | `layouts.layout` | Livewire list contract |
| My accepted orders | `orders.myAccepted` | `OrderController::myAcceptedOrders` | `sale.show-order` | admin, manager, staff, owner | `layouts.layout` | Status actions and assigned ownership |
| My orders | `orders.my` | `OrderController::myOrders` | `sale.show-order` | admin, manager, staff, owner | `layouts.layout` | Status filters/actions |
| Order detail | `orders.show` | `OrderController::show` | `sale.show-order` | admin, manager, staff, owner | `layouts.layout` | Dynamic record surface |
| Order history | `orders.history` | `OrderController::orderHistory` | `sale.order-history` | admin, manager, staff, owner | `layouts.layout` | Filter form |

### Products and inventory

| Page | Route | Controller method | Blade | Access | Layout | Audit note |
|---|---|---|---|---|---|---|
| Product list/add modal | `product.product.index` | `ProductController::index` | `products.index` | admin, manager, staff, owner | `layouts.layout` | Extracted IIFE asset; client-side search; no paginator |
| Product create | `product.product.create` | `ProductController::create` | `products.create` | admin, manager, staff, owner | `layouts.layout` | Dynamic unit JavaScript |
| Product detail/history | `product.product.show` | `ProductController::show` | `products.show-histroy` | admin, manager, staff, owner | `layouts.layout` | Route name and view purpose do not align |
| Product edit | `product.product.edit` | `ProductController::edit` | `products.edit` | admin, manager, staff, owner | `layouts.layout` | Dynamic units, image deletion and validation scripts |
| Add stock | `product.products.add-stock-form` | `ProductController::showAddStockForm` | `products.add_stock` | admin, manager, staff, owner | `layouts.layout` | Transaction form and multi-row interaction |
| Stock overview | `product.indexstock` | `ProductController::indexstock` | `products.show-stock` | admin, manager, staff, owner | `layouts.layout` | Table lacks consistent responsive wrapper |
| Product audit history | `product.products.allHistory` | `ProductController::allHistory` | `products.show-histroy` | admin, manager, staff, owner | `layouts.layout` | Same view used for two different data shapes |
| Warehouse-to-store transfer | `product.stock.to.store` | `StockController::formMoveToStore` | `products.move-to-store` | admin, manager, staff, owner | `layouts.layout` | High-risk stock form and confirmation JavaScript |
| Stock-in history | `product.stock-in-history` | `ProductController::searchStockInHistory` | `products.stock-in-history` | admin, manager, staff, owner | `layouts.layout` | SheetJS and print behavior |
| Movement history | `product.movement-history` | `ProductController::movementHistory` | `products.movement-history` | admin, manager, staff, owner | `layouts.layout` | Table lacks shared responsive wrapper |
| Barcode management | `product.barcodes.index` | `BarcodeController::index` | `barcodes.index` | admin, manager, staff, owner | `layouts.layout` | Large printing JavaScript and generated print documents |
| Category list/create | `categories.index` | `CategoryController::index` | `Categories.index` | admin, manager, staff, owner | `layouts.layout` | Table not responsive; destructive action confirmed |
| Category create page | `categories.create` | `CategoryController::create` | `Categories.create` | admin, manager, staff, owner | Missing | Active route references a missing view |
| Category edit page | `categories.edit` | `CategoryController::edit` | `categories.edit` | admin, manager, staff, owner | Missing | Active route references a missing view/case mismatch |
| Units | `units.index` | `UnitController::index` | `units.index` | admin, manager, staff, owner | `layouts.layout` | Table not responsive |
| Create unit | `units.create` | `UnitController::create` | `units.create` | admin, manager, staff, owner | `layouts.layout` | Basic form |
| Edit unit | `units.edit` | `UnitController::edit` | `units.edit` | admin, manager, staff, owner | Missing | Active route references a missing view |
| Low-stock notification | `notification.OutStock` | `notifiController::lowStock` | `notification.OutStock` | admin, manager, staff, owner | `layouts.layout` | Warning semantics need standard badge |
| Expiry notification | `notification.expire` | `notifiController::nearExpiry` | `notification.expire` | admin, manager, staff, owner | `layouts.layout` | Acknowledge action has confirmation |
| Stock count | `staff.stock.check.index` | `StockCheckController::index` | `staff.stock_check.index` | admin, manager, staff, owner | `layouts.layout` | Table and inline JavaScript |
| Stock count report | `staff.stock.check.report` | `StockCheckController::report` | `staff.stock_check.report` | admin, manager, staff, owner | `layouts.layout` | Report table |
| Stock count detail | `staff.stock.check.detail` | `StockCheckController::detail` | `staff.stock_check.check_detail` | admin, manager, staff, owner | `layouts.layout` | Record detail |

### Credit and customers

| Page | Route | Controller method | Blade | Access | Layout | Audit note |
|---|---|---|---|---|---|---|
| Credit customers | `staff.credit.sales.index` | `CreditSaleController::index` | `credit_sales.index` | admin, manager, staff, owner | `layouts.layout` | Modal-heavy payment entry surface |
| Credit sale detail | `staff.credit.sales.show` | `CreditSaleController::show` | `credit_sales.show` | admin, manager, staff, owner | `layouts.layout` | Wide financial table |
| Customer bills/payment | `staff.credit.customer` | `CreditSaleController::customerBills` | `credit_sales.customer_bills` | admin, manager, staff, owner | `layouts.layout` | 1,200+ lines; QR and payment/idempotency JavaScript |

### Shifts

| Page | Route | Controller method | Blade | Access | Layout | Audit note |
|---|---|---|---|---|---|---|
| Open shift | `shift.open` | `ShiftController::create` | `shift.open` | admin, manager, owner, staff | `layouts.layout` | Shared shell; transaction form needs submit state |
| Close shift | `shift.close` | `ShiftController::close` | `shift.close` | admin, manager, owner, staff | `layouts.layout` | Financial discrepancy should be clearer in later phase |

### Reports

| Page | Route | Controller method | Blade | Access | Layout | Audit note |
|---|---|---|---|---|---|---|
| Daily report | `reports.daily` | `ReportsController::index` | `reports.daily` | admin, manager, staff, owner | `layouts.layout` | Chart.js, SheetJS, print CSS, inline script; tables not consistently wrapped |

### Online/member

| Page | Route | Controller method | Blade | Access | Layout | Audit note |
|---|---|---|---|---|---|---|
| Online product browsing | `online.index` | `OnlineController::index` | `online.pos` | public | `layouts.online` | Mobile-facing, large inline CSS/JS |
| Cart | `online.cart` | `OnlineController::cart` | `online.cart` | authenticated + cart middleware | `layouts.online` | Quantity/session contracts and large script |
| Checkout | `online.checkout` | `OnlineController::showCheckoutForm` | `online.checkout` | authenticated | `layouts.online` | High-risk order/stock/idempotency form |
| Order tracking | `online.track` | `OrderController::track` | `online.track` | authenticated | `layouts.online` | Livewire customer order component |
| Member profile | `online.edit` | `OnlineController::edit` | `online.edit` | authenticated, ownership checked in controller | `layouts.online` | Profile update contract |

### Authentication

| Page | Route | Controller method | Blade | Access | Layout | Audit note |
|---|---|---|---|---|---|---|
| Login | `login` | `AuthController::loginPage` | `auth.login` | guest | `layouts.app` | Large inline CSS and separate Font Awesome version |
| Forgot password | `password.request` | `ForgotPasswordController::showRequestForm` | `auth.forgot-password` | guest | `layouts.app` | Large inline CSS |
| Verify OTP | `password.otp.verify` | `ForgotPasswordController::showVerifyForm` | `auth.verify-otp` | guest/session state | `layouts.app` | OTP workflow |
| Reset password | `password.reset` | `ResetPasswordController::showResetForm` | `auth.reset-password` | guest/session state | `layouts.app` | Inline CSS/JS |

## Supporting non-page endpoints

- `products.json`
- `barcode.check`
- `barcode.verify`
- `product.name.check`
- `product.barcode.lookup`
- `units.byProduct`
- `online.generate.qr`

These endpoints are part of active JavaScript/UI contracts and must not be redesigned as pages.

## Existing layout and component inventory

- `layouts.layout`: staff/admin shell, used by 46 views.
- `layouts.online`: online/member shell, used by 5 views.
- `layouts.app`: authentication shell, used by 9 views.
- Existing reusable pieces: topbar, flash messages, validation errors, JsBarcode include, SweetAlert component, Livewire order/badge components, product-list script/style partials.
- `components/button.blade.php` is a placeholder and is not a usable button abstraction.

## Repeated structures

- At least 48 form tags, 30 table tags, more than 100 card structures, and multiple modal families are implemented directly in views.
- Flash messages use both SweetAlert and Bootstrap alerts.
- Validation summaries and field-level errors are inconsistent.
- Tables without a local `table-responsive` token were found in categories, member room, stock overview, movement history, show-stock, reports, sale edit/show, stock count, and units.
- Status badges use inconsistent Bootstrap colors and Thai/English status labels.
- Money and quantity values do not use a common tabular-number/alignment utility.

## Dependency audit

- Bootstrap 5.3.5 in staff layout and Bootstrap 5.3.0 in auth/online layouts.
- Bootstrap Icons 1.11.3 is now centralized by the design-system partial.
- Font Awesome 6.0 and 6.4 are both present.
- SweetAlert is loaded by staff/online layouts and still has page-level includes on sale pages.
- Chart.js is loaded by the daily report.
- SheetJS is loaded independently by daily report and stock-in history.
- JsBarcode 3.11.5 is centralized with an `@once` partial.
- QRCode.js is loaded by customer bills.
- Sarabun/Prompt Google Fonts are loaded only on selected surfaces.

## Responsive and accessibility risks

- Several tables can overflow without a responsive wrapper.
- POS uses many inline click handlers and dense controls that need tablet testing.
- Fixed pixel widths appear in layouts, receipt/print templates, barcode views, product forms, and dashboard cards.
- Icon-only controls do not consistently provide `aria-label`.
- Labels and inputs are not consistently connected with `for`/`id`.
- Modal focus relies on Bootstrap defaults, while generated print windows are outside normal accessibility flow.
- Long Thai text and large monetary values have not been systematically tested at 375px/768px.

## Safety UX risks

- Server-side idempotency exists for hardened transaction flows, but there is no shared client submitting state.
- POS, checkout, credit payment, stock movement, shift close, and order status actions require phase-specific double-click testing.
- Most visible DELETE forms have confirmations; cancellation and custom JavaScript handlers require later browser review.
- Product delete markup in the product list is commented and inactive.

## High-risk JavaScript surfaces

1. `sale/sale.blade.php`
2. `credit_sales/customer_bills.blade.php`
3. `online/checkout.blade.php`
4. `online/cart.blade.php`
5. `online/pos.blade.php`
6. `sale/show-order.blade.php` and Livewire order components
7. `products/move-to-store.blade.php`
8. `reports/daily.blade.php`
9. `barcodes/index.blade.php`
10. `manager/dashboard.blade.php`

## Legacy, missing, or ambiguous surfaces

- Active but missing views: `index`, `Categories.create`, `categories.edit`, and `units.edit`.
- No active route/caller found for `OnlineController::pagenologin`, `MemberController::view`, `StockCheckController::form`, or `SaleController::edit/update`.
- `products/create-stock.blade.php`, `products/indexstock.blade.php`, `members/room.blade.php`, and `sale/edit.blade.php` appear legacy or disconnected from the active route registry.
- `PostController`, `HomeController`, and their referenced CRUD/home views are not part of the active POS route registry.
- `staff.sales.index` duplicates the POS controller entry but is not the primary POS route.

These findings are audit results only. Missing or legacy surfaces were not fixed in Phase 0/1.
