# REMITTANCE MANAGEMENT SYSTEM — PROJECT SCOPE AND REQUIREMENTS

**Document ID:** 02  
**File Name:** `02_PROJECT_SCOPE_AND_REQUIREMENTS.md`  
**Project:** Remittance Management System  
**Document Type:** Project Scope and Requirements Standard  
**Version:** 1.0  
**Status:** FINAL DRAFT — Pending Business Owner Approval  
**Authority:** Business Owner  

---

# 1. Purpose

This document defines the approved functional scope, system requirements, boundaries, user-facing modules and exclusions of the Remittance Management System.

This document must comply with:

1. `01_REMITTANCE_BUSINESS_CONSTITUTION.md`
2. `00_PROJECT_INDEX.md`

If this document conflicts with the Business Constitution, the Business Constitution shall take precedence.

---

# 2. Project Goal

The project shall replace the daily operational use of the existing Microsoft Access system with a new online system.

The new system shall provide controlled management of:

- Customers
- Daily transactions
- Cash
- Bank accounts
- BLB and similar accounts
- Remittance/service accounts
- Deposits
- Withdrawals
- Account transfers
- Service charges
- Remittance commissions
- Expenses
- Account balances
- Opening balances
- Month-end settlements
- Shareholders
- Share holdings
- Share transfers
- Shareholder exits
- Reports
- Notes
- Attachments
- User activity and audit history

The new system shall start from approved opening financial positions rather than reconstructing incomplete historical accounting.

---

# 3. Project Users

The system shall support exactly three primary business roles:

1. **Admin**
2. **Staff**
3. **Shareholder**

Detailed permissions shall be governed by:

`06_USER_ROLE_PERMISSION_STANDARD.md`

---

# 4. Main Application Areas

The application shall contain the following major functional areas:

1. Authentication
2. Dashboard
3. Customer Management
4. Account Management
5. Daily Transaction Management
6. Remittance Transactions
7. Deposit Transactions
8. Withdrawal Transactions
9. Account Transfers
10. Service Charge Management
11. Commission Management
12. Expense Management
13. Opening Balance
14. Month-End Settlement
15. Shareholder Management
16. Share Transaction Management
17. Reports
18. Notes and Attachments
19. Audit/History
20. System Configuration

---

# 5. Authentication Scope

The system shall provide secure login access.

Each user shall authenticate using an approved login credential.

After successful login, the system shall determine the user's role and provide only authorized functionality.

The system shall not expose Admin functionality merely by hiding menu items.

Permission enforcement must also exist at the application authorization level.

---

# 6. Admin Scope

Admin shall be the primary controller of the system.

Subject to detailed permissions, Admin shall be able to:

- Manage Staff users
- Manage Shareholder users
- Manage Customers
- Manage Accounts
- Manage service/remittance providers
- Manage Expense Categories
- Enter approved Opening Balances
- View account balances
- Create authorized transactions
- View all operational transactions
- Manage account transfers
- Manage commission records
- Manage expenses
- Manage Shareholders
- Manage share transactions
- Process approved shareholder exits
- View reports
- Review Staff transactions
- Perform/approve authorized cancellations or corrections
- View audit information
- Manage relevant system settings

Admin operations must remain auditable.

---

# 7. Staff Scope

Staff shall primarily perform daily business transactions.

Staff functionality may include:

- Search Customer
- Select Customer
- Add Customer
- Create Remittance Transaction
- Create Deposit Transaction
- Create Withdrawal Transaction
- Enter principal amount
- Enter Service Charge
- Select service/account
- Enter transaction/reference number
- Enter Note
- Upload Attachment
- View permitted own/daily transaction history
- Search permitted operational transactions

Staff shall not automatically have access to:

- Opening Balance management
- Shareholder management
- Share transfer management
- Shareholder exit management
- Capital management
- User/role management
- System configuration
- Sensitive administrative reports
- Arbitrary balance editing
- Hard deletion of financial records

Detailed restrictions shall be defined in the permission standard.

---

# 8. Shareholder Scope

Shareholder functionality shall primarily be read-only.

A Shareholder shall be able to view permitted information relating to the shareholder's own holding.

This may include:

- Own profile
- Current share quantity
- Current approved value per share
- Current total share valuation
- Own share acquisition history
- Own share transfer history
- Own share reduction history
- Own partial/full exit history

A Shareholder shall not automatically be allowed to:

- Enter daily transactions
- Modify account balances
- Modify own share quantity
- Modify another shareholder
- View another shareholder's private details
- Manage Staff
- Manage Admin
- Manage business settings

---

# 9. Customer Management Scope

The system shall maintain a Customer Master.

Customer information may include approved fields such as:

- Customer ID
- Customer Name
- Mobile Number
- Address
- Other approved identification/reference information
- Note
- Attachment where required
- Status

Exact fields shall be finalized during database/UI design.

---

# 10. Customer Balance Exclusion

Customer shall not maintain a financial balance.

The system shall not provide:

- Customer wallet balance
- Customer account balance
- Customer receivable balance
- Customer payable balance

unless such functionality is separately approved in a future version.

Customer is primarily a reference attached to operational transactions.

---

# 11. Customer History Requirement

Authorized users shall be able to search a Customer and view permitted historical transactions.

Customer history may show:

- Date
- Transaction type
- Service/provider
- Principal amount
- Service charge
- Reference
- Note
- Attachment
- Transaction status

This history does not constitute a customer financial ledger.

---

# 12. Legacy Customer Import

Existing customer data from the Microsoft Access system may be imported.

Before import:

- Duplicate records should be identified.
- Invalid/empty records should be reviewed.
- Names should be cleaned where practical.
- Mobile numbers should be reviewed where available.

Customer import shall not import a customer financial balance.

---

# 13. Account Management Scope

The system shall support financial/operational accounts.

Examples include:

- Cash
- Business Bank Account
- BLB
- Citizen Remit
- City Express
- IME Remit
- IME Pay
- IPS
- Other approved accounts

Accounts shall be configurable.

A new provider/account should not require adding a new database column.

---

# 14. Account Classification

Accounts may be classified according to their business purpose.

Examples may include:

- Cash
- Bank
- Remittance
- BLB/Banking Service
- Operational Service
- Other approved financial account

Detailed classifications shall be finalized in:

`03_ACCOUNT_AND_TRANSACTION_STANDARD.md`

---

# 15. Account Balance Requirement

Authorized users shall be able to determine the current balance of each approved financial account.

The system shall support:

- Positive balance
- Zero balance
- Negative balance

Negative balances shall be allowed where approved by business rules.

---

# 16. Opening Balance Scope

The new system shall support approved Opening Balances.

Opening Balance entry shall occur after the legacy closing/opening meeting and verification process.

Opening balances may be entered for:

- Cash
- Banks
- BLB
- Remittance accounts
- Other approved operational accounts

Opening Balance shall be restricted to authorized users.

---

# 17. Opening Balance Approval Source

The source of the new opening position shall be the approved business/partner/shareholder closing-opening decision.

The system shall not automatically calculate the opening position solely from incomplete old Microsoft Access records.

Supporting Note and Attachment shall be available for Opening Balance records.

---

# 18. Daily Transaction Scope

The system shall support daily transaction recording.

A transaction may require:

- Business Date
- Customer
- Transaction Type
- Service/Account
- Principal Amount
- Service Charge
- Reference Number
- Note
- Attachment
- Created By
- Created Timestamp
- Status

Additional fields may be introduced only when supported by approved business requirements.

---

# 19. Remittance Transaction Scope

The system shall support Remittance transactions.

Typical workflow:

1. Select/Add Customer.
2. Select Remittance Service.
3. Enter Principal Amount.
4. Enter Service Charge.
5. Enter Reference where applicable.
6. Add Note where applicable.
7. Upload evidence where applicable.
8. Save transaction.

The financial effect shall follow the Business Constitution and Account Standard.

---

# 20. Remittance Balance Requirement

Remittance accounts may become negative.

The application shall not automatically reject a valid authorized remittance solely because the selected remittance account does not contain sufficient positive balance.

Negative balance must remain visible in authorized account reports.

---

# 21. Deposit Transaction Scope

The system shall support the approved BLB/Bank-style customer Deposit process.

The transaction shall identify:

- Customer
- Account/Service
- Amount
- Service Charge where applicable
- Reference
- Note
- Attachment
- Date
- User

Financial effect shall follow the Account and Transaction Standard.

---

# 22. Withdrawal Transaction Scope

The system shall support the approved BLB/Bank-style customer Withdrawal process.

The transaction shall identify:

- Customer
- Account/Service
- Amount
- Service Charge where applicable
- Reference
- Note
- Attachment
- Date
- User

The final Service Charge cash treatment must be confirmed before implementation.

---

# 23. Account Transfer Scope

Authorized users shall be able to transfer money between approved company-controlled financial accounts.

A transfer shall include at minimum:

- Date
- Send Account
- Receive Account
- Amount
- Reference where applicable
- Note
- Attachment
- Created By

A normal account transfer shall not automatically create income or expense.

---

# 24. Month-End Settlement Scope

The system shall support settlement of operational/remittance accounts.

Example use:

A remittance account becomes negative during the month.

At month-end, an authorized Bank/Account transfer may be made to bring the remittance account toward or to zero.

The system shall retain the settlement transaction history.

Month-end settlement shall not erase the underlying transactions that created the balance.

---

# 25. Service Charge Scope

Service Charge shall be captured separately from Principal Amount.

The system shall support:

- Service Charge entry
- Service Charge reporting
- Date-wise Service Charge
- Staff-wise Service Charge
- Service/provider-wise Service Charge
- Period-wise Service Charge

Where Service Charge is collected in Cash, Cash shall be affected according to approved business rules.

---

# 26. Commission Management Scope

The system shall support Commission received from remittance/service providers.

Commission entry shall identify relevant information such as:

- Date
- Provider/Service
- Commission Amount
- Receiving Account
- Reference
- Note
- Attachment
- Created By

Commission shall remain separate from customer Service Charge.

---

# 27. Commission Receiving Account

A Commission transaction must identify where the money was actually received.

Examples:

- Cash
- Business Bank Account
- Other approved account

The receiving account shall increase according to the approved transaction rule.

---

# 28. Expense Management Scope

The system shall provide Expense Management.

An Expense shall identify:

- Date
- Expense Category
- Amount
- Payment Account
- Particular/Description
- Reference where applicable
- Note
- Attachment
- Created By

The selected payment account shall decrease.

---

# 29. Expense Category Scope

Expense Categories shall be manageable as master data.

Examples:

- Rent
- Salary
- Electricity
- Internet
- Office Expense
- Other Expense

The application shall not require a separate database column for each Expense Category.

---

# 30. Profit and Loss Scope

The system shall provide business performance reporting based on approved income and expense rules.

Profit/Loss shall not be treated as the same value as:

- Cash
- Bank Balance
- Capital
- Share Value
- Remittance Balance

Detailed calculations shall be defined in:

`04_COMMISSION_EXPENSE_AND_PROFIT_STANDARD.md`

---

# 31. Shareholder Management Scope

Admin shall be able to maintain Shareholder records.

A Shareholder record may include:

- Shareholder identity
- Contact information
- Current status
- Share quantity
- Share history
- Note
- Attachment

Share quantity shall be controlled through approved share transactions rather than arbitrary editing after initialization.

---

# 32. Share Valuation Scope

The current approved base valuation is:

**1 Share Unit = NPR 1,000**

The system shall calculate:

`Share Quantity × Applicable Share Value`

to provide share valuation.

Historical transaction values must remain preserved if the valuation changes in the future.

---

# 33. Share Transfer Scope

The system shall support transfer of shares:

**Shareholder → Shareholder**

Required information may include:

- Date
- From Shareholder
- To Shareholder
- Number of Shares
- Applicable Value Per Share
- Calculated Valuation
- Note
- Attachment
- Approved/Created By

Company Cash and Bank shall not change merely because shares move between shareholders.

---

# 34. New Shareholder Through Transfer

An approved new person may become a Shareholder by receiving shares from an existing Shareholder.

The system shall preserve:

- Seller
- Buyer
- Quantity
- Date
- Applicable valuation
- Supporting evidence
- Transaction history

The seller's holding decreases and the buyer's holding increases.

---

# 35. Shareholder Partial Exit Scope

The system shall support an approved partial company redemption/exit.

When the company pays the shareholder:

- Share quantity decreases.
- Selected Cash/Bank account decreases.

The transaction shall retain Note and Attachment support.

---

# 36. Shareholder Full Exit Scope

The system shall support complete Shareholder exit.

After all approved shares are redeemed/transferred as required:

- Current shareholding may become zero.
- Shareholder status may become Exited/Inactive.
- Historical records shall remain available.

A shareholder shall not be hard-deleted simply because the shareholder exited.

---

# 37. Company-Issued New Shares

The system architecture shall allow future support for company-issued new shares/capital additions.

However, detailed issuance rules must be approved before implementation if this function is required in the first release.

Shareholder-to-shareholder transfer shall not be treated as company-issued new shares.

---

# 38. Note Requirement

All major operational and financial record types shall provide a Note facility.

This includes, where applicable:

- Customer
- Remittance
- Deposit
- Withdrawal
- Account Transfer
- Commission
- Expense
- Opening Balance
- Shareholder
- Share Transfer
- Shareholder Exit
- Adjustment/Correction

Note shall normally be optional unless a specific workflow requires it.

---

# 39. Attachment Requirement

All major operational and financial record types shall provide Attachment/Upload support where applicable.

Attachments may include:

- Receipt
- Voucher
- Screenshot
- Bank evidence
- Image
- PDF
- Other approved evidence

The security and technical rules for attachments shall be defined separately.

---

# 40. Multiple Attachments

The technical design should allow attachment architecture to support one or more supporting files where required.

The exact file-count limit, file-size limit and allowed formats shall be defined in the data/security standards.

---

# 41. Transaction Search

Authorized users shall be able to search/filter transactions using appropriate criteria.

Examples include:

- Date
- Date Range
- Customer
- Account
- Service
- Transaction Type
- Reference Number
- Staff/User
- Status

Search capability shall respect role permissions.

---

# 42. Daily Report

The system shall provide a Daily Transaction Report.

The report should be capable of showing relevant values such as:

- Transaction Count
- Principal Amount
- Service Charge
- Service/Account
- Customer
- Staff
- Transaction Type

Exact report columns shall be finalized in the UI/report design.

---

# 43. Account Ledger Report

Authorized users shall be able to view the movement of an account.

The ledger shall provide sufficient information to identify:

- Date
- Transaction
- Increase
- Decrease
- Running/Resulting Balance
- Reference
- Related party/customer where applicable
- User
- Status

---

# 44. Current Account Balance Report

Authorized users shall be able to view current balances for approved accounts.

The report shall clearly distinguish:

- Positive
- Zero
- Negative

balances.

---

# 45. Service Charge Report

The system shall support Service Charge reporting by:

- Day
- Date Range
- Month
- Staff
- Service
- Transaction Type

This is a key operational control requirement.

---

# 46. Commission Report

The system shall support Commission reporting by:

- Provider
- Date
- Date Range
- Month
- Receiving Account
- Amount

Commission shall not be merged with Service Charge in reporting.

---

# 47. Expense Report

The system shall support Expense reporting by:

- Date
- Date Range
- Month
- Expense Category
- Payment Account
- User

---

# 48. Shareholder Report

Authorized users shall be able to obtain Shareholder information including:

- Current Share Quantity
- Applicable Share Value
- Current Valuation
- Share History
- Transfers
- Redemptions/Exits
- Status

Shareholder-role users shall only see information allowed by their permission scope.

---

# 49. Staff Activity Requirement

The system shall record the responsible user for daily transactions.

Admin shall be able to review Staff activity according to authorized reports.

This is required for transaction and Service Charge accountability.

---

# 50. Transaction Status

Financial transactions shall support an appropriate lifecycle/status.

At minimum, architecture shall distinguish valid/active transactions from cancelled transactions.

Detailed statuses may include:

- Active
- Cancelled
- Corrected/Reversed

Final lifecycle shall be defined in the Account and Transaction Standard.

---

# 51. Financial Hard Delete Exclusion

Normal application operation shall not provide uncontrolled permanent deletion of financial transaction history.

Cancellation/correction shall be preferred.

Historical auditability must be preserved.

---

# 52. Correction Requirement

Where an incorrect financial transaction must be corrected, the system shall preserve sufficient linkage between:

- Original transaction
- Cancellation/reversal
- Corrected/replacement transaction

where applicable.

---

# 53. Audit Requirement

The system shall retain relevant audit metadata for important operations.

This includes, where applicable:

- Created By
- Created At
- Updated By
- Updated At
- Cancelled By
- Cancelled At
- Cancellation Reason

Detailed implementation shall be governed by the Security/Audit Standard.

---

# 54. Business Date Requirement

Financial transactions shall contain a Business/Transaction Date.

The system-created timestamp shall not replace the Business Date.

Rules for:

- Backdated transactions
- Closed periods
- Date corrections

must be finalized before implementation.

---

# 55. Dashboard Requirement

The system shall provide role-appropriate dashboards.

### Admin Dashboard

May include authorized summaries such as:

- Cash position
- Bank balances
- Operational/remittance balances
- Negative accounts
- Daily transactions
- Service Charges
- Commission
- Expenses
- Relevant alerts

### Staff Dashboard

Shall prioritize daily operational work and permitted transaction information.

### Shareholder Dashboard

Shall prioritize the logged-in shareholder's own share position and permitted history.

---

# 56. Responsive Access Requirement

The online system shall be designed for practical use through modern web browsers.

The UI should support:

- Desktop
- Laptop
- Tablet
- Mobile browser

Daily Staff transaction entry must remain practical on the intended business devices.

---

# 57. Data Integrity Requirement

A financial transaction shall not leave the system in a partially updated state.

Where one business operation affects multiple financial records/accounts, the operation must succeed as one controlled transaction or fail without partial financial effect.

Detailed technical implementation shall be defined in the database standard.

---

# 58. Balance Editing Restriction

After approved Opening Balances are established, users shall not normally edit Current Balance directly.

Current Balance shall reflect authorized transaction activity.

Corrections must use approved financial correction mechanisms.

---

# 59. Dynamic Master Requirement

The following types of business data should be configurable where appropriate:

- Financial Accounts
- Remittance Providers
- Banks/Services
- Expense Categories

A new provider or expense type should not normally require a database schema change.

---

# 60. Current Release In-Scope Summary

The intended core release includes:

- Admin Login
- Staff Login
- Shareholder Login
- Customer Management
- Customer Import
- Account Master
- Opening Balance
- Remittance Transactions
- Deposit Transactions
- Withdrawal Transactions
- Service Charges
- Account Transfers
- Account Balances
- Negative Balance Support
- Month-End Settlement
- Commission
- Expenses
- Shareholder Management
- Share Transfers
- Shareholder Exit
- Notes
- Attachments
- Search/Filter
- Operational Reports
- Financial Reports
- Audit Information
- Cancellation/Correction Framework

---

# 61. Explicit Out-of-Scope Items

Unless separately approved, the current project does not include:

- Direct integration with remittance company APIs
- Direct bank API integration
- Core banking functionality
- Customer wallet
- Customer financial balance
- Public customer login
- Customer mobile application
- Online customer money transfer
- Automated bank settlement network
- Regulatory KYC/AML platform
- Full general-purpose ERP
- Payroll system
- Inventory system
- Purchase/Sales system
- External accounting software integration

These may only be introduced through approved future scope.

---

# 62. Legacy Transaction Migration Exclusion

The current scope does not require reconstruction of all historical Microsoft Access financial transactions into the new live account ledger.

The old financial system shall be closed at an approved cut-off.

New Opening Balances shall establish the new financial starting point.

This protects the new system from incomplete historical account data.

---

# 63. Legacy Archive

The old Microsoft Access database shall be retained as historical evidence/reference.

A future read-only Legacy Records feature may be introduced if specifically required.

Such legacy records must remain financially isolated from the new live ledger.

---

# 64. Future Scope

Potential future functionality may include:

- Advanced shareholder approval workflows
- Profit distribution
- Company-issued share management
- Closed financial periods
- Advanced reconciliation
- Automated alerts
- Additional reports
- External service integrations
- Legacy record viewer
- Additional security controls

Future scope shall not be implemented automatically without approval.

---

# 65. Scope Change Rule

Any request made after scope approval must be classified as either:

- Clarification of existing approved requirement, or
- New/changed requirement

A new business requirement shall not silently become part of the project.

Its impact on:

- Business Rules
- Database
- UI
- Reports
- Existing data
- Development time

must be reviewed before implementation.

---

# 66. Development Gate

Development shall not invent missing requirements.

Where an implementation decision depends on unresolved business logic:

**STOP → REVIEW DOCUMENTATION → CONFIRM BUSINESS RULE → UPDATE DOCUMENT → APPROVE → DEVELOP**

This requirement applies equally to human developers and AI coding agents.

---

# 67. Unresolved Requirements Before Development

The following requirements still require Business Owner confirmation:

1. Exact Withdrawal Service Charge behavior.
2. Detailed company-issued new share rules if required in Version 1.
3. Profit distribution rules if required.
4. Backdated transaction permissions.
5. Financial month closing/reopening rules.
6. Exact Staff cancellation/correction permissions.
7. Exact mandatory Customer fields.
8. Exact attachment file restrictions.

These shall not be guessed.

---

# 68. Acceptance Principle

A feature shall be considered functionally acceptable only when:

- It follows the Business Constitution.
- It follows this approved scope.
- Financial effects match approved rules.
- Role permissions are respected.
- Required audit information is preserved.
- Notes/attachments work where required.
- Reports reflect approved transactions correctly.
- Cancellation/correction does not silently corrupt balances.

---

# 69. Document Authority

This document is subordinate to:

`01_REMITTANCE_BUSINESS_CONSTITUTION.md`

and provides the authoritative project scope for dependent technical and workflow documents.

The following documents shall use this scope as a requirement source:

- `03_ACCOUNT_AND_TRANSACTION_STANDARD.md`
- `04_COMMISSION_EXPENSE_AND_PROFIT_STANDARD.md`
- `05_SHAREHOLDER_STANDARD.md`
- `06_USER_ROLE_PERMISSION_STANDARD.md`
- `07_DATABASE_AND_DATA_STANDARD.md`
- `08_UI_AND_WORKFLOW_STANDARD.md`
- `09_SECURITY_AUDIT_BACKUP_DEPLOYMENT_STANDARD.md`

---

# 70. Approval

**Status:** FINAL DRAFT — Pending Business Owner Approval

After Business Owner approval, this document becomes the approved functional scope and requirements baseline for the Remittance Management System.

---

**END OF DOCUMENT**