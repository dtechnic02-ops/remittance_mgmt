# REMITTANCE MANAGEMENT SYSTEM — SYSTEM ARCHITECTURE AND MODULE STANDARD

**Document ID:** 15  
**File Name:** `15_SYSTEM_ARCHITECTURE_AND_MODULE_STANDARD.md`  
**Project:** Remittance Management System  
**Document Type:** Technical Architecture and Module Standard  
**Version:** 1.0  
**Status:** FINAL DRAFT — Pending Business Owner Approval  
**Authority:** Business Owner  

---

# 1. Purpose

This document defines the approved technical architecture, module boundaries and development structure of the Remittance Management System.

It governs:

- Application architecture
- Module boundaries
- Financial engine separation
- Role architecture
- Service layer responsibility
- Controller responsibility
- Database interaction
- Transaction posting
- Ledger posting
- Cancellation architecture
- Attachment architecture
- Reporting architecture
- Shareholder architecture
- Reusable components
- Module dependency
- Code organization
- Future extensibility

The purpose of this standard is to ensure that the system remains maintainable and that business logic is not scattered across unrelated files.

---

# 2. Governing Documents

This document shall comply with:

1. `00_PROJECT_INDEX.md`
2. `01_REMITTANCE_BUSINESS_CONSTITUTION.md`
3. `02_PROJECT_SCOPE_AND_REQUIREMENTS.md`
4. `03_ACCOUNT_AND_TRANSACTION_STANDARD.md`
5. `04_COMMISSION_EXPENSE_AND_PROFIT_STANDARD.md`
6. `05_SHAREHOLDER_STANDARD.md`
7. `06_USER_ROLE_PERMISSION_STANDARD.md`
8. `07_DATABASE_AND_DATA_STANDARD.md`
9. `08_UI_AND_WORKFLOW_STANDARD.md`
10. `09_SECURITY_AUDIT_BACKUP_DEPLOYMENT_STANDARD.md`
11. `10_PRE_DEVELOPMENT_DECISION_AND_APPROVAL_CHECKLIST.md`
12. `11_IMPLEMENTATION_AND_TESTING_PLAN.md`
13. `12_OPERATION_SUPPORT_AND_CHANGE_MANAGEMENT_STANDARD.md`
14. `13_DATA_MIGRATION_CUTOVER_AND_OPENING_STANDARD.md`
15. `14_REPORTING_AND_RECONCILIATION_STANDARD.md`

If this document conflicts with the Business Constitution, the Business Constitution shall take precedence.

---

# 3. Architecture Principle

The system shall use a modular web-application architecture.

The recommended implementation structure is:

**UI / Request  
→ Controller  
→ Business Service  
→ Financial Transaction Engine  
→ Database / Ledger  
→ Response / Report**

Financial business logic shall not be embedded randomly inside Blade templates or JavaScript.

---

# 4. Recommended Application Stack

The project may be implemented using:

- Laravel
- PHP
- MySQL/MariaDB
- Blade
- JavaScript
- CSS
- Secure server-side storage

The exact framework version may be selected during development.

Business rules shall remain independent of specific version numbers.

---

# 5. Core Application Modules

The application shall be divided logically into the following modules:

1. Authentication
2. User Management
3. Customer
4. Account
5. Provider/Service
6. Opening Balance
7. Remittance Transaction
8. Deposit Transaction
9. Withdrawal Transaction
10. Account Transfer
11. Settlement
12. Commission
13. Expense
14. Financial Ledger
15. Shareholder
16. Share Transfer
17. Share Redemption/Exit
18. Reporting
19. Attachment
20. Audit
21. Settings
22. Legacy Customer Import

---

# 6. Module Independence Principle

Each module shall have a clearly defined responsibility.

Example:

The Customer Module manages Customer identity.

It shall not calculate Account Balance.

The Expense Module records Expense.

It shall not independently modify account balance using separate custom logic outside the Financial Engine.

---

# 7. Financial Engine

A centralized Financial Transaction Engine shall control financial account movement.

It shall handle approved effects such as:

- Cash increase
- Cash decrease
- Bank increase
- Bank decrease
- Provider Account increase
- Provider Account decrease
- Opening Balance
- Account Transfer
- Commission Receipt
- Expense Payment
- Share Redemption payment
- Adjustment
- Reversal

---

# 8. No Direct Balance Manipulation

Business modules shall not directly execute logic equivalent to:

`account.balance = account.balance + amount`

independently in multiple controllers.

All balance movement shall pass through approved centralized financial posting logic.

---

# 9. Ledger as Financial Authority

Every financial Account movement shall create traceable Ledger entries.

The Financial Engine shall be able to answer:

- Source Transaction
- Account
- Increase
- Decrease
- Amount
- Business Date
- Status
- User

---

# 10. Business Transaction and Ledger Separation

A business transaction explains:

**WHAT happened.**

Ledger entries explain:

**HOW Accounts changed.**

Example:

Business Transaction:

`Remittance`

Financial Ledger:

- Cash +10,100
- City Express -10,000

These concepts shall remain linked but not confused.

---

# 11. Remittance Module

The Remittance Module shall manage:

- Customer
- Provider
- Principal Amount
- Service Charge
- Reference
- Date
- Note
- Attachment

It shall request the Financial Engine to apply the approved Remittance financial effects.

---

# 12. Remittance Module Financial Rule

The Remittance Module itself shall not contain arbitrary account mathematics.

It shall invoke one approved business operation such as:

**Post Remittance Transaction**

The Financial Engine shall apply:

- Cash Principal increase
- Cash Service Charge increase
- Provider Account Principal decrease

---

# 13. Deposit Module

The Deposit Module shall manage the approved customer Deposit workflow.

It shall pass validated transaction data to the Financial Engine.

Approved base effect:

- Cash increases
- Selected BLB/Operational Account decreases
- Service Charge effect applies

---

# 14. Withdrawal Module

The Withdrawal Module shall manage Customer Withdrawal.

Base financial effect:

- Cash decreases
- Selected BLB/Operational Account increases

Final Service Charge behavior shall follow the approved business decision.

No developer shall implement the unresolved rule by assumption.

---

# 15. Account Transfer Module

Account Transfer shall use centralized financial posting.

Required information:

- Send Account
- Receive Account
- Amount
- Date
- Reference
- Note
- Attachment

The module shall not create Income or Expense.

---

# 16. Settlement Module

Month-End Settlement should reuse Account Transfer financial infrastructure.

Settlement is an Account Transfer with a defined business purpose.

This prevents duplicate code for:

- Bank decrease
- Provider increase

---

# 17. Commission Module

Commission Module shall manage:

- Provider
- Commission Amount
- Receiving Account
- Date
- Reference
- Note
- Attachment

The Financial Engine shall increase the selected Receiving Account.

The reporting layer shall recognize Commission as approved Income.

---

# 18. Expense Module

Expense Module shall manage:

- Expense Category
- Particular
- Amount
- Payment Account
- Date
- Reference
- Note
- Attachment

The Financial Engine shall reduce the selected Payment Account.

---

# 19. Profit/Loss Architecture

Profit/Loss shall not be posted as a manually maintained balance.

It shall be calculated from approved active source records.

Initial calculation sources:

### Income
- Service Charge
- Provider Commission
- Other approved Income

### Expense
- Approved active Expenses

---

# 20. Customer Module

Customer Module shall manage:

- Customer Create
- Customer Edit
- Customer Search
- Customer View
- Customer History
- Customer Note
- Customer Attachment

It shall not contain:

- Customer Current Balance
- Customer Wallet
- Customer Ledger balance

---

# 21. Account Module

Account Module shall manage:

- Account Master
- Account Categories
- Account Status
- Negative Balance Permission
- Account Notes
- Account Attachments
- Account Ledger View

It shall not allow ordinary direct Current Balance editing.

---

# 22. Provider Module

Provider/Service Module shall manage dynamically configured service providers.

Examples:

- Citizen Remit
- City Express
- IME Remit
- IME Pay
- BLB
- IPS

Adding a provider shall not require creating a new application module.

---

# 23. Provider and Account Relationship

Where a Provider maintains an operational balance, it may be linked to a Financial Account.

Business code shall resolve that relationship from configuration/data.

Provider names shall not be hard-coded throughout the codebase.

---

# 24. Opening Balance Module

Opening Balance shall be a controlled initialization module.

It shall support:

- Opening Batch
- Account Opening entries
- Approval Reference
- Notes
- Attachments
- Review
- Finalization

Once finalized, normal UI editing shall be blocked.

---

# 25. Opening Module Lifecycle

Recommended lifecycle:

**Draft  
→ Review  
→ Finalized**

If approval workflow is not required, implementation may simplify the lifecycle while preserving finalization control.

---

# 26. Shareholder Module

The Shareholder Module shall manage:

- Shareholder Profile
- Opening Share Holding
- Current Share Quantity
- Share Ledger
- Status
- Notes
- Attachments

Share Quantity shall be transaction-controlled.

---

# 27. Share Engine

A centralized Share Transaction Engine should manage ownership movement.

It shall support operations such as:

- Opening Shares
- Share Transfer
- Company Share Addition where approved
- Share Redemption
- Share Adjustment
- Reversal

---

# 28. Shareholder-to-Shareholder Transfer

Share Transfer Engine shall atomically apply:

- Seller Shares decrease
- Buyer Shares increase

It shall not invoke Financial Ledger Cash/Bank posting for a pure shareholder transfer.

---

# 29. Share Redemption

Share Redemption affects both:

### Share Engine
- Shareholder Shares decrease

and

### Financial Engine
- Selected Cash/Bank decreases

Both shall complete atomically.

---

# 30. Cross-Module Atomicity

Where one business action affects multiple modules, all required effects shall succeed together.

Example:

Share Redemption:

1. Share Transaction created
2. Share Ledger updated
3. Financial Transaction created
4. Bank/Cash Ledger updated

If any required operation fails:

**Rollback everything.**

---

# 31. Cancellation Architecture

Cancellation shall not be implemented independently in every module using unrelated logic.

A centralized or consistent cancellation/reversal architecture shall be used.

Cancellation shall:

- Validate permission
- Validate transaction status
- Preserve original record
- Record reason
- Reverse financial/share effects
- Update status
- Write audit information

---

# 32. Cancellation Idempotency

An already Cancelled transaction shall not be cancelled a second time and reverse the financial effect twice.

The system must protect against duplicate reversal.

---

# 33. Correction Architecture

Recommended correction process:

**Original Transaction  
→ Cancellation/Reversal  
→ New Corrected Transaction**

The system shall preserve linkage between related records.

---

# 34. Adjustment Architecture

Adjustment shall be an exceptional controlled operation.

It shall use the same Financial Engine rather than directly altering Current Balance.

---

# 35. Controller Responsibility

Controllers should primarily handle:

- Request receipt
- Authorization
- Validation coordination
- Calling Business Services
- Returning View/Response

Controllers shall not become large repositories of financial business logic.

---

# 36. Business Service Responsibility

Business Services shall contain approved business operations.

Examples:

- Create Remittance
- Create Deposit
- Create Withdrawal
- Transfer Account
- Record Commission
- Record Expense
- Transfer Shares
- Redeem Shares
- Cancel Transaction

---

# 37. Model Responsibility

Models shall represent data and relationships.

Complex financial workflow shall not be scattered through uncontrolled model events.

---

# 38. Database Transaction Responsibility

Business Services performing financial/share operations shall use database transactions.

Example:

```text
BEGIN

Create Business Transaction
Create Ledger Entries
Update Controlled Balance Cache
Create Audit Information

COMMIT