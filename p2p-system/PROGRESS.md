# P2P System Project Progress

This file tracks the completion status of the P2P system.

## Phase 1: Setup & Foundation

- [X] **1.A: Project Setup & Database**
  - [X] Create Laravel project
  - [X] Install and configure Breeze
  - [X] Create all database migrations
  - [X] Run `php artisan migrate`
- [X] **1.B: Eloquent Models & Relationships**
  - [X] Create `PurchaseRequest` model
  - [X] Create `RequestLog` model
  - [X] Create `PurchaseLog` model
  - [X] Create `Budget` model
  - [X] Update `User` model with relationships
- [X] **1.C: Authorization & Roles**
  - [X] Define Gates in `AuthServiceProvider` (is-admin, is-finance, etc.)
  - [X] Disable public registration
- [X] **1.D: Admin User Management UI**
  - [X] Create Admin-only routes
  - [X] Create `UserManagementController`
  - [X] Create "User List" view (Tailwind)
  - [X] Create "Create User" form
  - [X] Create "Edit User" form

## Phase 2: Employee Workflow

- [X] **2.A: "My Requests" Dashboard**
  - [X] Create `PurchaseRequestController`
  - [X] Create Employee dashboard view
  - [X] Display table of user's requests with status badges
- [X] **2.B: Create New Request**
  - [X] Create "New Request" form view
  - [X] Implement `store` method in `PurchaseRequestController`
  - [X] Add validation (Form Request)
- [X] **2.C: View & Confirm**
  - [X] Create "View Request" detail page
  - [X] Implement "Confirm Receipt" button and logic
- [X] **2.D: Employee Notifications**
  - [X] Create `RequestDeniedNotification`
  - [X] Create `RequestReadyForPickupNotification`

## Phase 3: Approval Workflow

- [X] **3.A: Approval Queue Dashboard**
  - [X] Create `ApprovalController`
  - [X] Create the central "Approval Queue" view
  - [X] Add logic to show different queues based on user role (Procurement, Finance, Manager)
- [X] **3.B: Request Review Page & Actions**
  - [X] Create "Review Request" detail page
  - [X] Implement "Approve / Deny / Escalate" buttons
  - [X] Create logic in `ApprovalController` to handle status changes
  - [X] Create `RequestLog` entry on every action
- [X] **3.C: Budget Widget**
  - [X] Create `BudgetService` class
  - [X] Display budget widget on Finance review page
- [X] **3.D: Approver Notifications**
  - [X] Create `NewRequestForApprovalNotification`

## Phase 4: Procurement Workflow

- [X] **4.A: "Ready to Buy" Dashboard**
  - [X] Create view for `status = 'Approved for Purchase'`
- [X] **4.B: "Log Purchase" Form**
  - [X] Create `PurchaseLogController`
  - [X] Create "Log Purchase" form (vendor, price, currency, receipt upload)
  - [X] Implement file storage logic for receipt
  - [X] Implement `store` method to create `PurchaseLog` and update request `status`

## Phase 5: Management Features

- [X] **5.A: Budget Management UI**
  - [X] Create `BudgetController`
  - [X] Create view for Managers/Finance to set monthly budgets
  - [X] Implement `store/update` logic for budgets
- [X] **5.B: Analytics Dashboard**
  - [X] Install `Chart.js`
  - [X] Create `AnalyticsController`
  - [X] Create `Analytics` dashboard view (from design)
  - [X] Add backend logic for all KPIs and charts

---

## Phase 6: Polish & Fixes (v1.1)

- [X] **6.A: Add Currency to PR Form (Item 1)**
  - [X] Create migration for `estimated_currency` column on `purchase_requests` table.
  - [X] Create migration for `settings` table (to store exchange rate).
  - [X] Update "Budget Management" page (controller & view) to include a form field for "USD to IQD Exchange Rate."
  - [X] Update "Create Request" form (view) to include a "Currency" (IQD/USD) dropdown.
  - [X] Update `StorePurchaseRequest` to validate the new currency field.
  - [X] Update `ApprovalController` to use the exchange rate to check the 100k IQD rule for USD entries.
- [X] **6.B: Modify Procurement Actions (Item 2)**
  - [X] Update `approval.show.blade.php` to hide the "Deny" button *only* for the `procurement` role.
- [X] **6.C: Fix Finance Button Logic (Item 3)**
  - [X] Update `approval.show.blade.php` to dynamically change the "Approve" button text for the `finance` role.
  - [X] If price < 100k, text is "Approve for Purchase."
  - [X] If price >= 100k, text is "Escalate to Manager."
- [X] **6.D: Allow Manager/Finance to View Completed (Item 4)**
  - [X] Update `analytics.index.blade.php` to make "Recently Completed" table items links to the request.
  - [X] Update `PurchaseRequestController@show` authorization logic to allow `finance`, `manager`, and `admin` roles to view *any* request.
  - [X] Update `requests.show.blade.php` to include a dynamic "Back" button.
- [X] **6.E: Give Admin Full Visibility (Item 5)**
  - [X] Update `layouts.navigation.blade.php` to show "Ready to Buy" link to Admins.
  - [X] Update `PurchaseLogController` (index, create, store) to add authorization

---

## Phase 7: Polish & Fixes (v1.2)

- [X] **7.A: Fix Currency Display Bug**

  - [X] Update `requests.index.blade.php` header and column to show correct currency.
  - [X] Update `requests.show.blade.php` description list to show correct currency.
  - [X] Update `approval.queue.blade.php` header and column to show correct currency.
  - [X] Update `approval.show.blade.php` description list to show correct currency.
  - [X] Update `purchase-log.create.blade.php` summary list to show correct currency.
  - [X] **7.B: Fix Budget Widget Currency (Bug 1)**

    - [X] Update `ApprovalController@show` to pass correct "this request" cost and currency.
    - [X] Update `approval.show.blade.php` (widget) to display the correct currency cost.
  - [X] **7.C: Fix Manager 'Deny' Action (Bug 2)**

    - [X] Refactor `ApprovalController@process` to be more robust and correctly handle 'deny' action.
- [X] **7.D: Hide Comment Box for Procurement**

  - [X] Update `approval.show.blade.php` to hide the "Comment" box for the `procurement` role.
- [X] **7.E: Fix Budget Widget Layout Bug**

  - [X] Update `approval.show.blade.php` to conditionally move the "This Request" and "Remaining if Approved" lines to the correct currency (IQD or USD) block.

---

## Phase 8: Fix Self-Approval Loophole (v1.3)

- [X] **8.A: Fix Procurement Self-Approval (Creation)**
  - [X] Modify `PurchaseRequestController@store` to check the requester's role.
  - [X] If the requester is `procurement`, auto-escalate the request to `Pending Finance`.
  - [X] Create an "auto-escalated" log and notify `Finance`.
- [X] **8.B: Fix Finance Self-Approval (Escalation)**
  - [X] Modify `ApprovalController@process` logic.
  - [X] When `procurement` escalates, check if the *original requester* is `finance`.
  - [X] If yes, auto-run the 100k rule and bypass the `Pending Finance` queue.
- [X] **8.C: Implement "Safety Net" in All Queues**
  - [X] Modify `ApprovalController@index` (the queue dashboard).
  - [X] Add a `->where('user_id', '!=', auth()->id())` to all queue queries.
  - [X] This makes it impossible for *any* user (including Managers) to see their own requests in their approval queue.

## Phase 9: UI/UX Polish (Axiom Design)

- [X] **9.A: Setup Brand Colors**
  - [X] Modify `tailwind.config.js` to add the company's "brand-green" and "brand-blue".
- [X] **9.B: Redesign "Guest" Layout (Login Page)**
  - [X] Replace all code in `resources/views/layouts/guest.blade.php` with the new, light, centered-card layout.
  - [X] Replace all code in `resources/views/auth/login.blade.php` to match the new, clean "Axiom" style.
  - [X] Update `resources/views/auth/forgot-password.blade.php` to match.
- [X] **9.C: Redesign Main "App" Layout**
  - [X] Replace all code in `resources/views/layouts/app.blade.php` to create the new "floating sidebar" and "top bar" structure.
  - [X] Modify `resources/views/layouts/navigation.blade.php` to work as the new vertical sidebar.
  - [X] Add "Notification Bell" (static icon) and User Avatar to the new top bar.
- [X] **9.D: Backend for UI Features**
  - [X] Create migration to add `avatar` column to `users` table.
  - [X] Modify `AppServiceProvider.php` to share queue counts (for counters) with all views.
  - [X] Modify `Profile` page to allow avatar uploads.
- [X] **9.E: "Card-ify" All Content Pages**
  - [X] `dashboard` (redirects)
  - [X] `requests.index`
  - [X] `requests.create`
  - [X] `requests.show`
  - [X] `approval.queue`
  - [X] `approval.show`
  - [X] `purchase-log.index`
  - [X] `purchase-log.create`
  - [X] `admin.users.index`
  - [X] `admin.users.create`
  - [X] `admin.users.edit`
  - [X] `budgets.index`
  - [X] `analytics.index`
  - [X] `profile.edit`

---

## Phase 10: Final Polish (v1.4)

- [X] **10.A: Fix Missing "Escalate" Button (Bug 1)**
  - [X] Update `approval.show.blade.php` to fix `@if` logic for Procurement buttons.
- [X] **10.B: Implement Responsive (Mobile) Layout (Bug 2)**
  - [X] Add Alpine.js state to `app.blade.php` to manage the sidebar.
  - [X] Add a mobile "hamburger" menu button to the top bar.
  - [X] Modify `navigation.blade.php` to be hidden on mobile and toggleable.
- [X] **10.C: Fix "Ugly" Top Bar Design (Bug 3)**
  - [X] Remove `shadow-sm` and `border-b` from the `<header>` in `app.blade.php` to make it "flat".
- [X] **10.D: Implement Notification Bell (Bug 4)**
  - [X] Create `NotificationController` to fetch and mark notifications as read.
  - [X] Add new routes for the notification system.
  - [X] Update `AppServiceProvider` to share `unreadNotifications` and `unreadCount` with the view.
  - [X] Modify `app.blade.php` to make the bell a real, functional dropdown.
  - [X] Create `notifications` table migration and migrate.
- [X] **10.E: Remove "Help Center" Card (Bug 5)**
  - [X] Remove the "Help Center" `div` from `navigation.blade.php`.
- [X] **10.F: Fix Root Welcome Page (Bug 6)**
  - [X] Modify `routes/web.php` to redirect the `/` route to `/login`.
- [X] **10.G: Fix Blank Analytics Cards (Bug 7)**
  - [X] Update JavaScript in `analytics.index.blade.php` to show a "No data" message if chart data is empty.

---

## Phase 11: Final UI Polish (v1.5)

- [X] **11.A: Fix Mobile Responsiveness (Bug 5)**
  - [X] Re-engineer `app.blade.php` and `navigation.blade.php` to be "mobile-first".
  - [X] Ensure desktop sidebar is `hidden lg:flex`.
  - [X] Ensure main content is `w-full` and stacks correctly.
- [X] **11.B: Fix Hamburger Menu on Desktop (Bug 1)**
  - [X] Add `lg:hidden` class to the hamburger button in `app.blade.php`.
- [X] **11.C: Fix Notification Bell UI (Bug 2)**
  - [X] Add logic to `app.blade.php` to hide the notification counter if the count is 0.
  - [X] Re-style the notification dropdown for a cleaner look.
- [X] **11.D: Fix Blank Analytics Cards (Bug 3)**
  - [X] Use Blade `@if` directives in `analytics.index.blade.php`.
  - [X] If chart data is empty, render a "No data" message instead of the canvas.
- [X] **11.E: Style Edit/Delete Buttons (Bug 4)**
  - [X] Update `admin.users.index.blade.php` to style "Edit" and "Delete" as small, colored buttons.

---

## Phase 12: Final Polish & Fixes (v1.6)

- [X] **12.A: Fix Responsive Layout & Desktop Hamburger (Bug 1 & 5)**
  - [X] Re-engineer `app.blade.php` and `navigation.blade.php` to *correctly* hide the sidebar on mobile.
  - [X] Ensure the hamburger icon is *only* visible on mobile (`lg:hidden`).
  - [X] Ensure the desktop layout is fixed and does not show the hamburger.
- [X] **12.B: Fix Missing "Escalate" Button (Bug 2)**
  - [X] Correct the button classes in `approval.show.blade.php` to ensure "Escalate" and "Fulfill" buttons appear side-by-side.
- [X] **12.C: Fix "Empty" Notification Bell (Bug 3)**
  - [X] Update `PurchaseRequestController` to also send notifications to all `Admin` users.
  - [X] Update `ApprovalController` to also send all notifications to `Admin` users (for testing).

---

## Phase 14: Final UI Fixes (v1.8)

- [X] **14.A: Fix Responsive Layout & Hamburger (Bugs 1 & 2)**
  - [X] Replace `app.blade.php` with a correct mobile-first layout.
  - [X] Replace `navigation.blade.php` with a correct mobile/desktop layout.
  - [X] Ensure hamburger icon is `lg:hidden`.
- [X] **14.B: Fix Missing "Escalate" Button (Bug 2)**
  - [X] Correct button classes in `approval.show.blade.php`.
- [X] **14.C: Fix Blank Analytics Cards (Bug 3)**
  - [X] Use Blade `@if` directives in `analytics.index.blade.php` to show "No data" messages.
- [X] **14.D: Style Edit/Delete Buttons (Bug 4)**
  - [X] Update `admin.users.index.blade.php` to style actions as buttons.
- [X] **14.E: Remove "Help Center" & Fix Root Route (Bugs 5 & 6)**
  - [X] Remove "Help Center" `div` from `navigation.blade.php`.
  - [X] Modify `routes/web.php` to redirect `/` to `/login`.
- [X] **14.F: Ensure Notifications Appear (Bug 3)**
  - [X] Re-add "Notify Admin" logic to all controllers to ensure the bell can be tested.

---

## Phase 15: Implement "Cash Ready" Step (v1.9)

- [X] **15.A: Update Status Logic in `ApprovalController@process`**
  - [X] Modify `case 'manager':` -> `action 'approve'` to set `newStatus = 'Pending Final Payment'`.
  - [X] Add notification for `Finance` on manager approval.
  - [X] Modify `case 'finance':` to handle a new `action 'cash_ready'`.
  - [X] The `cash_ready` action will set `newStatus = 'Approved for Purchase'`.
  - [X] Add notification for `Procurement` on `cash_ready` action.
- [X] **15.B: Update Finance Queue**
  - [X] Modify `ApprovalController@index` to make the Finance queue show *both* `Pending Finance` and `Pending Final Payment` requests.
- [X] **15.C: Update Approval View**
  - [X] Modify `approval.show.blade.php` to show a new button ("Confirm Cash") when status is `Pending Final Payment`.
- [X] **15.D: Update Status Badge Colors**
  - [X] Add `Pending Final Payment` to all status badge `@switch` statements (e.g., in `requests.index`, `approval.queue`, etc.) to show a "pending" color.

---

## Phase 16: Quotation Management System (v2.0)

- [X] **16.A: Database & Model Overhaul**
  - [X] Create migration to `Schema::dropIfExists('purchase_logs')`.
  - [X] Delete `app/Models/PurchaseLog.php`.
  - [X] Create migration to `Schema::create('offers', ...)`.
  - [X] Run `php artisan migrate`.
  - [X] Create new model `app/Models/Offer.php` with relationships.
  - [X] Update `app/Models/PurchaseRequest.php` to remove `purchaseLog()` and add `offers()` and `chosenOffer()` relationships.
  - [ ] Update `AppServiceProvider.php` to reflect `Offer` model.
  - [X] Update `BudgetService.php` to calculate spending from `offers` table.
- [X] **16.B: Controller & Route Overhaul**
  - [X] Delete `app/Http/Controllers/PurchaseLogController.php`.
  - [X] Create new `OfferController.php` with `index`, `create`, `store`, and `select` methods.
  - [X] Update `routes/web.php` to remove all `purchase-log` routes and add new `offer` routes.
- [X] **16.C: Frontend View Overhaul**
  - [X] Rename `resources/views/purchase-log` folder to `resources/views/offers`.
  - [X] Update `layouts/navigation.blade.php` to use new `offers.index` route.
  - [X] Update `offers/index.blade.php` (was `purchase-log/index.blade.php`) to link to `offers.create`.
  - [X] Replace `offers/create.blade.php` (was `purchase-log/create.blade.php`) with the new "Add Offer" form and "Submitted Offers" table.
  - [X] Update `requests/show.blade.php` to display the new "Quotations" table instead of "Purchase Details".
  - [X] Update `analytics/index.blade.php` to get price data from `chosenOffer` relationship.

## Phase 17: Implement Priority Feature (v1.9)

- [X] **17.A: Add `priority` to Database**
  - [X] Create migration to add `priority` column to `purchase_requests` table.
  - [X] Run `php artisan migrate`.
  - [X] Update `app/Models/PurchaseRequest.php` to add `priority` to `$fillable`.
- [X] **17.B: Update Controllers & Validation**
  - [X] Update `app/Http/Requests/StorePurchaseRequest.php` to validate `priority`.
  - [X] Update `app/Http/Controllers/PurchaseRequestController.php` (`store`) to save `priority`.
  - [X] Update `app/Http/Controllers/ApprovalController.php` (`index`) to sort approval queues by priority.
- [X] **17.C: Redesign "Create Request" Form**
  - [X] Replace `resources/views/requests/create.blade.php` with the new "tube-style" priority selector.
- [X] **17.D: Add Priority Badges to Views**
  - [X] Update `requests.index.blade.php` to show the priority.
  - [X] Update `approval.queue.blade.php` to show the priority.
  - [X] Update `requests.show.blade.php` to show the priority.
  - [X] Update `approval.show.blade.php` to show the priority.

---

## Phase 18: Priority UI Refinement (v2.1)

- [X] **18.A: Add Priority Colors to Config**

  - [X] Update `tailwind.config.js` to add new `priority-low`, `priority-medium`, and `priority-high` colors.
- [X] **18.B: Redesign Priority Selector**

  - [X] Update `resources/views/requests/create.blade.php` with new "tube" shape (`rounded-full`).
  - [X] Implement new Alpine.js opacity-toggle logic for button selection.
- [X] **18.C: Update `requests.create.blade.php` with new UI**

  - [X] Change selector colors to Green (Low), Yellow (Medium), and Red (High).
  - [X] Implement opacity-toggle logic for button selection.
  - [X] Add the dynamic description text underneath the "tube" selector.

---

## Phase 19: Vendor Management Integration (v2.2)

- [X] **19.A: Create Vendor Management System**
  - [X] Create `vendors` table migration.
  - [X] Create `Vendor` model and `VendorController`.
  - [X] Create views: `index`, `create`, `edit`.
- [X] **19.B: Integrate Vendors with Offers**
  - [X] Create migration to add `vendor_id` to `offers` table.
  - [X] Update `Offer` model relationship.
  - [X] Update `OfferController` to use `Vendor` model.
  - [X] Update `offers.create` view to use Vendor dropdown.
