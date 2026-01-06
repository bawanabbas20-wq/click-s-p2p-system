# P2P System V.2

A comprehensive Purchase-to-Pay (P2P) system built with Laravel, designed to streamline the procurement process from request to payment.

## 🚀 Features

### 1. Employee Workflow
- **Request Creation**: Easy-to-use form for submitting purchase requests with priority levels (Low, Medium, High).
- **Dashboard**: "My Requests" dashboard to track the status of submitted requests.
- **Notifications**: Real-time and email notifications for request updates (Approved, Denied, Ready for Pickup).
- **Receipt Confirmation**: Employees can confirm receipt of items directly from the system.

### 2. Approval Workflow
A robust, multi-stage approval process ensuring proper oversight:
- **Manager Approval**: First line of approval for team requests.
- **Finance Approval**:
    - Automatic "Approve for Purchase" for low-cost items (< 100k IQD).
    - Escalation to Manager for high-cost items.
    - "Cash Ready" verification step.
- **Procurement Fulfillment**:
    - "Ready to Buy" dashboard for approved requests.
    - Vendor selection and quotation management.
    - Purchase logging with receipt uploads.

### 3. Quotation & Vendor Management
- **Vendor Database**: Manage vendor details and contacts.
- **Quotation System**: Request and compare multiple offers/quotations for a request.
- **Selection**: Choose the best offer based on price and vendor rating.

### 4. Financial Control
- **Budget Management**: Set and monitor monthly budgets.
- **Analytics Dashboard**: Visual insights into spending, request volume, and department performance using Chart.js.
- **Currency Support**: Multi-currency support (IQD/USD) with exchange rate handling.

### 5. User Management & Security
- **Role-Based Access Control (RBAC)**: Distinct roles for Admin, Manager, Finance, Procurement, and Employee.
- **Secure Authentication**: Powered by Laravel Breeze.
- **Profile Management**: Avatar uploads and secure password management.

## 🛠 Technology Stack

- **Framework**: [Laravel](https://laravel.com)
- **Frontend**: Blade Templates, [Tailwind CSS](https://tailwindcss.com), [Alpine.js](https://alpinejs.dev)
- **Charts**: [Chart.js](https://www.chartjs.org)
- **Database**: MySQL

## ⚙️ Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd p2p-system
   ```

2. **Install Dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment Setup**
   Copy the example environment file and configure your database and mail settings.
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database Setup**
   Run migrations to set up the database schema.
   ```bash
   php artisan migrate
   ```
   *(Optional) Seed the database with initial data if available:*
   ```bash
   php artisan db:seed
   ```

5. **Build Frontend**
   ```bash
   npm run build
   ```

6. **Run the Application**
   ```bash
   php artisan serve
   ```

## 📝 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

Copyright © 2025 P2P System. All rights reserved.
