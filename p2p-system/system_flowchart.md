# P2P System Workflow Documentation

This document provides a comprehensive visual and textual guide to the **Click P2P System** workflow. It details the journey of a purchase request from initiation to fulfillment, highlighting the responsibilities of each role.

---

## 🔄 Complete Workflow Diagram

```mermaid
flowchart TD
    %% ------------------------------
    %% STYLING ( Legacy Safe )
    %% ------------------------------
    classDef actionNode fill:#f9f9f9,stroke:#333,stroke-width:1px,color:#000;
    classDef decisionNode fill:#fffde7,stroke:#fbc02d,stroke-width:2px,color:#000;
    classDef endNode fill:#000,stroke:#000,stroke-width:2px,color:#fff;

    %% ------------------------------
    %% 1. EMPLOYEE
    %% ------------------------------
    subgraph "Employee Stage"
        Start([Start: Need Item]) --> CreateReq[Create Request]
        CreateReq --> SelectPriority{Select Priority}
        SelectPriority -- "Low/Medium" --> Submit[Submit Request]
        SelectPriority -- "High" --> Submit
    end

    %% ------------------------------
    %% 2. MANAGER
    %% ------------------------------
    subgraph "Manager Stage"
        Submit --> ManagerReview{Review Request}
        ManagerReview -- "Approve" --> CheckStock[Send to Procurement]
        ManagerReview -- "Reject" --> RejectReq_Mgr([End: Request Rejected])
    end

    %% ------------------------------
    %% 3. PROCUREMENT
    %% ------------------------------
    subgraph "Procurement Stage"
        CheckStock --> StockCheck{In Stock?}
        
        %% Path A: In Stock
        StockCheck -- "Yes" --> FulfillStock[Fulfill from Stock]
        FulfillStock --> LogHandover([End: Handed Over])

        %% Path B: Purchase Needed
        StockCheck -- "No" --> RequestQuotes[Status: Needs Quotation]
        RequestQuotes --> AddQuotes[Get Vendor Quotes]
        AddQuotes --> SelectBestOffer[Select Best Offer]
        SelectBestOffer --> SubmitForFin[Submit to Finance]
    end

    %% ------------------------------
    %% 4. FINANCE
    %% ------------------------------
    subgraph "Finance Stage"
        SubmitForFin --> FinanceValidate{Validate Quote}
        
        %% Rejection
        FinanceValidate -- "Reject Quote" --> ReturnToProc[Return to Procurement]
        ReturnToProc -.-> AddQuotes
        FinanceValidate -- "Reject Request" --> RejectReq_Fin([End: Cancelled])

        %% Approval
        FinanceValidate -- "Approve" --> PendingCash[Status: Pending Payment]
        PendingCash --> CashReady{Cash Ready?}
        CashReady -- "Yes" --> MarkReady[Status: Ready to Buy]
    end

    %% ------------------------------
    %% 5. PURCHASING
    %% ------------------------------
    subgraph "Purchasing Stage"
        MarkReady --> ViewReady[Procurement: View List]
        ViewReady --> PurchaseAction[Buy Item]
        PurchaseAction --> UploadReceipt[Upload Receipt]
        UploadReceipt --> CompleteReq([End: Completed])
    end

    %% Apply Classes
    class CreateReq,Submit,CheckStock,FulfillStock,RequestQuotes,AddQuotes,SelectBestOffer,SubmitForFin,PendingCash,MarkReady,ViewReady,PurchaseAction,UploadReceipt actionNode;
    class SelectPriority,ManagerReview,StockCheck,FinanceValidate,CashReady decisionNode;
    class Start,RejectReq_Mgr,LogHandover,RejectReq_Fin,CompleteReq endNode;

```

## 📝 Process Description

### 1. Employee (Request)
*   **Action**: Employees log in and create a "New Purchase Request".
*   **Data**: They specify the Item Name, Description, Quantity, and Priority (Low/Medium/High).
*   **Outcome**: Request enters **Pending** status.

### 2. Line Manager / Approver (Review)
*   **Action**: Managers access the "Approval Queue".
*   **Decision**: 
    *   **Approve**: Moves the request to Procurement.
    *   **Reject**: Permanently closes the request with a rejection reason.

### 3. Procurement (Inventory vs. Buy)
*   **Check**: Procurement first checks if the item is available in inventory.
    *   **Yes**: They click **"Fulfill from Stock"**. The process ends here.
    *   **No**: They click **"Request Quotation"**. The status changes to **Needs Quotation**.
*   **Quotations**: Procurement gathers offers from vendors, enters them into the system, and selects the best option (based on price, quality, timeline).

### 4. Finance (Validation & Cash)
*   **Validation**: Finance reviews the *selected quotation*.
    *   **Approve**: Confirms the price and vendor are acceptable.
    *   **Reject Quote**: Sends the request back to Procurement to find a new quote (the request itself is not dead).
*   **Cash Release**: Once approved, the status becomes **Pending Final Payment**. When the cash is physically ready or transferred, Finance marks it **Ready to Buy**.

### 5. Procurement (Purchase)
*   **Execution**: Procurement sees the request in the **Ready to Buy** list.
*   **Completion**: They purchase the item, upload the receipt or invoice as proof, and mark the request as **Completed**.
