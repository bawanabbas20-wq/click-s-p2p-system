# P2P System Workflow Guide

A complete guide explaining all user roles and purchase request workflows.

---

## 📊 System Overview

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                        PURCHASE REQUEST LIFECYCLE                            │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                              │
│   Employee                Procurement              Finance              Manager
│      │                        │                      │                    │
│      ▼                        ▼                      ▼                    ▼
│  ┌────────┐              ┌─────────┐            ┌─────────┐          ┌─────────┐
│  │ Create │──────────────▶│ Review  │────────────▶│ Approve │──────────▶│ Final   │
│  │Request │              │& Quote  │            │ Budget  │          │Approval │
│  └────────┘              └─────────┘            └─────────┘          └─────────┘
│                               │                      │                    │
│                               ▼                      │                    ▼
│                         ┌─────────┐                  │              ┌─────────┐
│                         │ Ready   │◀─────────────────┘              │ Ready   │
│                         │ To Buy  │                                 │ To Buy  │
│                         └─────────┘                                 └─────────┘
│                               │
│                               ▼
│                         ┌─────────┐
│                         │Complete │
│                         └─────────┘
│                                                                              │
└─────────────────────────────────────────────────────────────────────────────┘
```

---

## 👥 User Roles

| Role | Description | Key Permissions |
|------|-------------|-----------------|
| **Employee** | Regular staff members | Create purchase requests, view own requests |
| **Procurement** | Purchasing department | Collect quotations, select vendors, log purchases |
| **Finance** | Finance department | Approve budgets, verify cash availability |
| **Manager** | Department managers | Final approval for high-value purchases |
| **Admin** | System administrator | All permissions + user management + settings |

---

## 🧑‍💼 Employee Role

### What Employees Can Do:
1. Create new purchase requests
2. View their own requests on the dashboard
3. Track request status
4. Receive notifications on status changes

### Creating a Purchase Request

**Step 1:** Click "New Request" button on dashboard

**Step 2:** Fill in the form:
- **Item Name** (required): What you want to purchase
- **Estimated Price** (required): Your best estimate
- **Currency**: IQD or USD
- **Date Wanted** (required): When you need it
- **Priority**: Low, Medium, or High
- **Justification** (required): Why you need this item

**Step 3:** Submit the request

**What happens next:**
- Request status becomes "Pending Procurement"
- Procurement team is notified
- You can track progress on your dashboard

### Possible Outcomes for Employee:
| Status | Meaning |
|--------|---------|
| Pending Procurement | Waiting for quotes |
| Needs Quotations | Procurement collecting vendor quotes |
| Pending Finance | Waiting for budget approval |
| Pending Manager | High-value item needs manager approval |
| Ready to Buy | Approved! Procurement will purchase soon |
| Completed | Item has been purchased |
| Denied | Request was rejected (check comments) |
| Fulfilled from Stock | Item was available in inventory |

---

## 📦 Procurement Role

### What Procurement Can Do:
1. View all pending requests
2. Collect quotations from vendors
3. Select and recommend vendors
4. Fulfill requests from existing stock
5. Print Purchase Orders
6. Log completed purchases

### Workflow Scenarios

#### Scenario 1: Fulfill from Stock
*When the requested item is already available in inventory*

1. Open request from "Approval Queue"
2. Click **"Fulfill from Stock"**
3. Add comment explaining the stock availability
4. Request is marked as **Completed** immediately

#### Scenario 2: Request Quotations
*Normal flow - need to get vendor quotes*

1. Open request from "Approval Queue"
2. Click **"Request Quotations"**
3. Go to "Needs Quotations" page
4. Click the request → "Add Offers" page
5. For each vendor quote:
   - Enter Vendor Name
   - Enter Price
   - Select Currency
   - Attach quote document (optional)
6. Select the best offer and provide a reason
7. Submit → Request moves to "Pending Finance"

#### Scenario 3: Complete Purchase
*After Finance/Manager approval*

1. Go to "Ready to Buy" page
2. Click **"Print PO"** to generate Purchase Order
3. Make the purchase from the vendor
4. Click **"Log Purchase"** to mark as completed
5. Employee is notified

---

## 💰 Finance Role

### What Finance Can Do:
1. Review purchase requests with quotations
2. Verify budget availability
3. Approve or reject purchases
4. Escalate high-value items to Manager

### Workflow Scenarios

#### Scenario 1: Low Value Purchase (< 100,000 IQD)
*Quick approval path*

1. Open request from "Approval Queue"
2. Review the selected quotation
3. Toggle **"I confirm cash is available"**
4. Click **"Approve & Ready to Buy"**
5. Request moves directly to "Ready to Buy" status

#### Scenario 2: High Value Purchase (≥ 100,000 IQD)
*Requires manager approval*

1. Open request from "Approval Queue"
2. See **"High Value Purchase"** warning
3. Review ALL submitted quotations
4. Select your recommended vendor (can differ from Procurement)
5. Enter your reasoning
6. Click **"Escalate to Manager"**
7. Request moves to "Pending Manager"

#### Scenario 3: Reject Quote Only
*Quotation not acceptable, need new quotes*

1. Click **"Reject Quote"**
2. Add comment explaining the issue
3. Request goes back to Procurement for new quotes

#### Scenario 4: Reject Entire Request
*Purchase is not approved*

1. Click **"Reject Request"**
2. Add comment explaining why
3. Request is marked as "Denied"
4. Employee is notified

---

## 👔 Manager Role

### What Managers Can Do:
1. Final approval for high-value purchases
2. Override Finance/Procurement recommendations
3. Select final vendor
4. Reject purchases

### Workflow Scenarios

#### Manager Review Page Shows:
- **Procurement Recommendation**: Their selected vendor + reason
- **Finance Recommendation**: Their selected vendor + reason
- **All Quotations**: Full list to choose from

#### Scenario 1: Approve Purchase

1. Open request from "Approval Queue"
2. Review both recommendations
3. Select final vendor (or use recommended)
4. Add approval notes (optional)
5. Click **"Final Approve"**
6. Request moves to "Ready to Buy"

#### Scenario 2: Select Different Vendor

1. Review all quotations
2. Select a different vendor than recommended
3. Provide reason for the different choice
4. Click **"Final Approve"**

#### Scenario 3: Reject

Same as Finance - can reject quote or entire request.

---

## 🔧 Admin Role

### What Admins Can Do:
Everything other roles can do, PLUS:
1. Manage all users (create, edit, delete)
2. Configure system settings
3. Set budgets
4. Change branding (logo, colors)
5. Override any workflow step

### Admin-Only Pages:
- **User Management**: Create/edit users and roles
- **Settings**: System configuration
- **Budget Management**: Set monthly budgets

---

## 📈 Status Flow Diagram

```
┌──────────────────┐
│  New Request     │ (Employee creates)
└────────┬─────────┘
         │
         ▼
┌──────────────────┐     ┌──────────────────┐
│    Pending       │────▶│ Fulfilled from   │ (if in stock)
│   Procurement    │     │     Stock        │
└────────┬─────────┘     └──────────────────┘
         │
         ▼
┌──────────────────┐
│ Needs Quotations │ (Procurement adds quotes)
└────────┬─────────┘
         │
         ▼
┌──────────────────┐     ┌──────────────────┐
│    Pending       │────▶│   Ready to Buy   │ (if LOW value)
│    Finance       │     └──────────────────┘
└────────┬─────────┘
         │ (if HIGH value)
         ▼
┌──────────────────┐
│    Pending       │
│    Manager       │
└────────┬─────────┘
         │
         ▼
┌──────────────────┐
│   Ready to Buy   │
└────────┬─────────┘
         │
         ▼
┌──────────────────┐
│    Completed     │
└──────────────────┘

        OR

┌──────────────────┐
│     Denied       │ (at any approval stage)
└──────────────────┘
```

---

## 💵 High Value Threshold

- **Threshold**: 100,000 IQD (or equivalent in USD)
- **Below threshold**: Finance can approve directly
- **At or above threshold**: Must be escalated to Manager

**USD Conversion**: Uses configured exchange rate (Admin Settings)

---

## 📝 Quick Reference

| I want to... | Role Needed | Where to Go |
|--------------|-------------|-------------|
| Create a purchase request | Employee+ | Dashboard → New Request |
| See my requests | Employee+ | Dashboard |
| Add vendor quotes | Procurement+ | Offers → Needs Quotations |
| Approve a purchase | Finance/Manager | Approval Queue |
| Print a Purchase Order | Procurement+ | Ready to Buy → Print PO |
| Create a new user | Admin | Admin → User Management |
| Change system logo | Admin | Admin → Settings |
| Set monthly budget | Finance/Admin | Budget Management |
| View analytics | Finance/Manager/Admin | Analytics |
