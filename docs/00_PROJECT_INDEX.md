# REMITTANCE MANAGEMENT SYSTEM — PROJECT INDEX

**Document ID:** 00  
**File Name:** `00_PROJECT_INDEX.md`  
**Project:** Remittance Management System  
**Document Type:** Master Project Index  
**Version:** 1.0  
**Status:** FINAL DRAFT — Pending Business Owner Approval  
**Authority:** Business Owner  

---

# 1. Purpose

This document is the master index and reading order for the Remittance Management System documentation.

The system is being developed as a new online system based on newly approved business rules.

The previous Microsoft Access system is treated as a legacy system and shall not define the architecture or financial logic of the new system.

The new system shall begin from approved opening balances after the existing business position is reviewed and approved by the partners/shareholders.

---

# 2. Project Objective

The Remittance Management System shall provide controlled management of:

- Daily customer transactions
- Remittance transactions
- Cash accounts
- Bank accounts
- BLB and similar service accounts
- Remittance service accounts
- Account-to-account transfers
- Service charges
- Remittance commissions
- Expenses
- Account balances
- Opening balances
- Month-end settlements
- Profit and loss reporting
- Shareholders
- Share holdings
- Share transfers
- Shareholder exits
- Users and permissions
- Notes and attachments
- Audit history
- Reports

The system shall provide clear visibility of where business funds are located and how balances change through transactions.

---

# 3. System Users

The system shall have three primary roles:

1. **Admin**
2. **Staff**
3. **Shareholder**

Detailed permissions shall be defined in:

`06_USER_ROLE_PERMISSION_STANDARD.md`

---

# 4. Legacy System Policy

The existing Microsoft Access system shall be treated as the **Legacy System**.

The old financial transactions shall not automatically become the financial ledger of the new system.

Before the new system becomes operational:

1. Existing accounts shall be reviewed.
2. Cash balance shall be physically verified.
3. Bank balances shall be verified.
4. BLB and similar account balances shall be verified.
5. Remittance account balances shall be verified.
6. Positive and negative balances shall be identified.
7. Shareholder holdings shall be verified.
8. Other required opening financial positions shall be verified.
9. Partners/shareholders shall approve the final position.
10. An official cut-off date shall be established.

The approved balances shall become the **Opening Balances of the New System**.

The old Microsoft Access database shall remain preserved as historical/legacy evidence.

---

# 5. Legacy Customer Migration

Customer records from the old system may be imported into the new system after cleaning and duplicate checking.

Customer migration shall not create financial balances.

A customer is a transaction reference/identity and does not maintain an account balance in this system.

Historical financial transactions shall not affect the new system's opening or current balances unless specifically approved under a separate migration decision.

---

# 6. Documentation Hierarchy

The documentation hierarchy is:

| Priority | Document | Purpose |
|---|---|---|
| 1 | `01_REMITTANCE_BUSINESS_CONSTITUTION.md` | Highest business authority and core business rules |
| 2 | `02_PROJECT_SCOPE_AND_REQUIREMENTS.md` | Project scope, requirements and exclusions |
| 3 | `03_ACCOUNT_AND_TRANSACTION_STANDARD.md` | Account and transaction financial rules |
| 4 | `04_COMMISSION_EXPENSE_AND_PROFIT_STANDARD.md` | Service charge, commission, expense and profit rules |
| 5 | `05_SHAREHOLDER_STANDARD.md` | Shareholder and share management rules |
| 6 | `06_USER_ROLE_PERMISSION_STANDARD.md` | Admin, Staff and Shareholder permissions |
| 7 | `07_DATABASE_AND_DATA_STANDARD.md` | Database, relationships, migration and data integrity |
| 8 | `08_UI_AND_WORKFLOW_STANDARD.md` | UI structure and operational workflows |
| 9 | `09_SECURITY_AUDIT_BACKUP_DEPLOYMENT_STANDARD.md` | Security, audit, backup and deployment |

---

# 7. Document Reading Order

Before development, documents shall be read in the following order:

### Step 1
`00_PROJECT_INDEX.md`

### Step 2
`01_REMITTANCE_BUSINESS_CONSTITUTION.md`

### Step 3
`02_PROJECT_SCOPE_AND_REQUIREMENTS.md`

### Step 4
`03_ACCOUNT_AND_TRANSACTION_STANDARD.md`

### Step 5
`04_COMMISSION_EXPENSE_AND_PROFIT_STANDARD.md`

### Step 6
`05_SHAREHOLDER_STANDARD.md`

### Step 7
`06_USER_ROLE_PERMISSION_STANDARD.md`

### Step 8
`07_DATABASE_AND_DATA_STANDARD.md`

### Step 9
`08_UI_AND_WORKFLOW_STANDARD.md`

### Step 10
`09_SECURITY_AUDIT_BACKUP_DEPLOYMENT_STANDARD.md`

Development shall not begin based only on isolated documents.

The Business Constitution and all relevant dependent standards must be read before implementing a module.

---

# 8. Document Register

## 00 — Project Index

**File:** `00_PROJECT_INDEX.md`

Defines:

- Documentation structure
- Document hierarchy
- Reading order
- Legacy system policy
- Development governance

---

## 01 — Remittance Business Constitution

**File:** `01_REMITTANCE_BUSINESS_CONSTITUTION.md`

Defines the highest-level business rules, including:

- System purpose
- Customer principles
- Account principles
- Remittance principles
- Deposit and withdrawal principles
- Service charge principles
- Commission principles
- Expense principles
- Opening balance principles
- Negative balance rules
- Month-end settlement principles
- Shareholder principles
- Transaction integrity
- Cancellation/correction principles
- Note and attachment principles

This document is the highest business authority.

---

## 02 — Project Scope and Requirements

**File:** `02_PROJECT_SCOPE_AND_REQUIREMENTS.md`

Defines:

- In-scope features
- Out-of-scope features
- Functional requirements
- User requirements
- Reporting requirements
- Operational requirements
- Current release scope
- Future scope

---

## 03 — Account and Transaction Standard

**File:** `03_ACCOUNT_AND_TRANSACTION_STANDARD.md`

Defines:

- Account Master
- Cash Account
- Bank Accounts
- BLB Accounts
- Remittance Accounts
- Opening balances
- Positive balances
- Negative balances
- Remittance transactions
- Deposit transactions
- Withdrawal transactions
- Account-to-account transfers
- Month-end settlements
- Transaction effects
- Ledger rules
- Balance calculation
- Cancellation/correction effects

---

## 04 — Commission, Expense and Profit Standard

**File:** `04_COMMISSION_EXPENSE_AND_PROFIT_STANDARD.md`

Defines:

- Customer service charges
- Cash effect of service charges
- Remittance company commissions
- Commission receipts
- Commission reporting
- Expense categories
- Expense payments
- Expense account effects
- Income calculations
- Profit/loss calculations
- Monthly reporting

Service Charge and Remittance Commission shall remain separate business concepts.

---

## 05 — Shareholder Standard

**File:** `05_SHAREHOLDER_STANDARD.md`

Defines:

- Shareholder Master
- Share quantity
- Share valuation
- Share purchase
- Share addition
- Share transfer
- Shareholder-to-shareholder transfer
- Company share redemption
- Partial exit
- Full exit
- Share history
- Shareholder status
- Cash/Bank effects
- Shareholder portal rules

The current base share valuation is:

**1 Share Unit = NPR 1,000**

Any future change shall follow approved business rules and shall not rewrite historical transaction values.

---

## 06 — User Role and Permission Standard

**File:** `06_USER_ROLE_PERMISSION_STANDARD.md`

Defines permissions for:

### Admin
Full authorized system management.

### Staff
Daily operational transaction activities under restricted permissions.

### Shareholder
Restricted access primarily to the shareholder's own share information and permitted history.

This document shall define the detailed permission matrix.

---

## 07 — Database and Data Standard

**File:** `07_DATABASE_AND_DATA_STANDARD.md`

Defines:

- Database architecture
- Table responsibilities
- Relationships
- Transaction references
- Account ledger integrity
- Historical records
- Customer import
- Legacy data handling
- Opening balance storage
- Note storage
- Attachment storage
- Data validation
- Cancellation records
- Audit information
- Data retention

Database design shall follow approved business rules rather than reproduce the old Microsoft Access table structure.

---

## 08 — UI and Workflow Standard

**File:** `08_UI_AND_WORKFLOW_STANDARD.md`

Defines:

- Login workflow
- Admin dashboard
- Staff dashboard
- Shareholder dashboard
- Customer workflow
- Transaction entry workflow
- Account transfer workflow
- Deposit workflow
- Withdrawal workflow
- Commission workflow
- Expense workflow
- Shareholder workflow
- Search
- Filters
- Reports
- Forms
- Notes
- Attachments
- Mobile/responsive requirements

The Staff interface shall prioritize simple and fast daily operation.

---

## 09 — Security, Audit, Backup and Deployment Standard

**File:** `09_SECURITY_AUDIT_BACKUP_DEPLOYMENT_STANDARD.md`

Defines:

- Authentication
- Authorization
- Role security
- Financial transaction protection
- Audit trail
- User activity records
- Cancellation/correction tracking
- Attachment security
- Database backup
- File backup
- Restore procedures
- Hosting
- Production deployment
- Environment configuration
- Recovery requirements

Financial history shall not be silently destroyed through normal user operations.

---

# 9. Global Business Principles

The following principles apply throughout the project:

### 9.1 Transaction-Based Balance

After approved opening balances are entered, financial account balances shall change through authorized transactions.

---

### 9.2 Customer Has No Financial Balance

Customers are transaction references.

The system shall not maintain customer receivable/payable balances unless a future approved business requirement explicitly introduces such functionality.

---

### 9.3 Financial Account Balance

Cash, Bank, BLB, Remittance and other approved operational accounts may maintain balances.

---

### 9.4 Negative Balance Allowed

Approved operational/remittance accounts may have negative balances.

Insufficient account balance shall not automatically prevent an authorized transaction where business rules permit negative balances.

---

### 9.5 Service Charge

Customer service charge is recorded separately from the principal transaction amount.

Where the business process receives the service charge in cash, the Cash Account shall reflect that amount.

---

### 9.6 Remittance Commission

Remittance company commission is separate from customer service charge.

Commission income shall be recorded according to the actual commission receipt/settlement process.

---

### 9.7 Shareholder Transfer

A shareholder-to-shareholder share transfer does not automatically affect company Cash or Bank accounts.

---

### 9.8 Shareholder Exit

Where the company pays a shareholder for an approved share exit/redemption, the selected company Cash or Bank account shall decrease accordingly.

---

### 9.9 Notes and Attachments

All major operational and financial record types shall support:

- Note
- Attachment/Upload

Attachments may include supporting receipts, vouchers, screenshots, images, PDFs or other approved evidence.

---

### 9.10 Historical Integrity

Financial and shareholder history shall remain traceable.

Cancelled or corrected transactions shall retain sufficient history for audit purposes.

---

# 10. Development Rule

No developer or AI coding agent shall invent business logic.

If a required behavior is not defined or is ambiguous:

**STOP → REVIEW BUSINESS DOCUMENTS → ASK BUSINESS OWNER → UPDATE STANDARD → IMPLEMENT**

Code shall follow the approved documentation.

Documentation shall not be rewritten merely to justify existing code.

---

# 11. Change Control

After a document reaches FINAL status:

- Business rule changes must be explicitly approved.
- Related dependent documents must be reviewed.
- Database impact must be evaluated.
- Existing financial records must not be silently recalculated.
- Historical data integrity must be preserved.

New customer requests shall not automatically become system rules without approval.

---

# 12. Project Boundary

This project is a business management and financial recording system for the defined remittance-related operation.

It is not automatically intended to provide:

- Direct remittance network processing
- Banking infrastructure
- Core banking services
- Customer wallet balances
- Public money-transfer APIs
- Regulatory settlement networks

Such functionality requires separate approval and scope.

---

# 13. Documentation Status

| Document | Status |
|---|---|
| `00_PROJECT_INDEX.md` | FINAL DRAFT — Pending Approval |
| `01_REMITTANCE_BUSINESS_CONSTITUTION.md` | Pending |
| `02_PROJECT_SCOPE_AND_REQUIREMENTS.md` | Pending |
| `03_ACCOUNT_AND_TRANSACTION_STANDARD.md` | Pending |
| `04_COMMISSION_EXPENSE_AND_PROFIT_STANDARD.md` | Pending |
| `05_SHAREHOLDER_STANDARD.md` | Pending |
| `06_USER_ROLE_PERMISSION_STANDARD.md` | Pending |
| `07_DATABASE_AND_DATA_STANDARD.md` | Pending |
| `08_UI_AND_WORKFLOW_STANDARD.md` | Pending |
| `09_SECURITY_AUDIT_BACKUP_DEPLOYMENT_STANDARD.md` | Pending |

---

# 14. Approval

This Project Index becomes authoritative after Business Owner approval.

Once approved, all subsequent Remittance Management System documentation and development shall follow the hierarchy and principles defined in this document.

---

**END OF DOCUMENT**