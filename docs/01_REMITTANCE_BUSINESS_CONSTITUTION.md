# REMITTANCE MANAGEMENT SYSTEM — BUSINESS CONSTITUTION

**Document ID:** 01  
**File Name:** `01_REMITTANCE_BUSINESS_CONSTITUTION.md`  
**Project:** Remittance Management System  
**Document Type:** Business Constitution  
**Version:** 1.0  
**Status:** FINAL DRAFT — Pending Business Owner Approval  
**Authority:** Business Owner  
**Priority:** Highest Business Authority  

---

# 1. Purpose

This document defines the core business constitution of the Remittance Management System.

It establishes the authoritative business rules for:

- Customers
- Accounts
- Cash
- Banks
- BLB and similar services
- Remittance services
- Daily transactions
- Service charges
- Remittance commissions
- Expenses
- Account transfers
- Opening balances
- Month-end settlements
- Profit and loss
- Shareholders
- Share transfers
- Shareholder exits
- Notes and attachments
- Transaction correction and audit history

All technical documents, database structures, workflows and application code must follow this constitution.

If another project document conflicts with this document, this document has higher business authority.

---

# 2. System Purpose

The Remittance Management System is an internal business management and financial recording system.

Its primary objectives are to determine:

- What transactions were performed
- Which customer performed a transaction
- Which service was used
- How much principal amount was involved
- How much service charge was collected
- Which account increased
- Which account decreased
- Where company money is currently located
- Which accounts have positive balances
- Which accounts have negative balances
- How much commission was earned
- How much expense was incurred
- What the current shareholder position is
- Who performed each system operation

The system shall provide reliable operational and financial visibility to the business owner/admin.

---

# 3. System Boundary

This system records and manages the business's internal transactions.

It is not, by default:

- A remittance network
- A banking platform
- A customer wallet
- A core banking system
- A direct money-transfer API
- A settlement network between financial institutions

External integrations require separate approved requirements.

---

# 4. New System Principle

The new system shall not reproduce the old Microsoft Access architecture merely because the old system operated in that manner.

The old system shall be treated as a legacy business record.

The new system shall implement the approved current business rules.

---

# 5. Legacy System Closing Rule

Before the new system becomes operational, the old business position shall be reviewed.

Partners/shareholders shall hold a closing/opening meeting.

The meeting shall establish an approved cut-off date.

As of that date, the business shall verify, where applicable:

- Physical Cash
- Bank balances
- BLB balances
- Remittance account balances
- Other operational account balances
- Positive balances
- Negative balances
- Outstanding commission
- Relevant expenses or liabilities
- Shareholder holdings
- Other required financial positions

The approved figures shall become the starting position of the new system.

---

# 6. Opening Balance Authority

Opening balances shall not be generated blindly from incomplete historical transactions.

The approved partner/shareholder meeting position shall be the authority for the new system opening.

Opening balances may include positive, zero or negative amounts where permitted by the business.

After the opening process is completed, normal balances shall be controlled through system transactions.

---

# 7. Legacy Data Rule

Historical Microsoft Access transactions shall not automatically affect new-system account balances.

The legacy database shall be preserved separately.

Customer records may be migrated after:

- Cleaning
- Duplicate checking
- Basic validation

Customer migration shall not create customer financial balances.

If historical transactions are ever imported for reference, they must remain clearly identified as legacy records and must not silently alter current account balances.

---

# 8. User Roles

The system shall have three primary roles:

1. **Admin**
2. **Staff**
3. **Shareholder**

Detailed permissions shall be defined in the User Role and Permission Standard.

---

# 9. Admin Principle

Admin is the primary authorized controller of the system.

Subject to detailed permission rules, Admin may manage:

- Users
- Customers
- Accounts
- Opening balances
- Daily transactions
- Account transfers
- Remittance transactions
- Deposit/withdrawal transactions
- Service charges
- Commissions
- Expenses
- Shareholders
- Share transactions
- Reports
- Corrections/cancellations
- System configuration

Admin access does not remove the requirement for transaction history and audit records.

---

# 10. Staff Principle

Staff primarily performs daily operational work.

Staff may be permitted to:

- Select customers
- Create customers
- Enter transactions
- Select transaction services/accounts
- Enter transaction amounts
- Enter service charges
- Enter transaction/reference numbers
- Enter notes
- Upload attachments

Staff shall not automatically receive administrative authority.

Sensitive functions shall remain restricted according to the permission standard.

---

# 11. Shareholder Principle

Shareholder access is primarily for viewing the shareholder's own approved share information.

A shareholder may view permitted information such as:

- Own current share quantity
- Approved value per share
- Own total share valuation
- Own share transaction history
- Own share transfer history
- Own exit/redemption history where applicable

A shareholder shall not automatically receive access to another shareholder's private information or administrative functions.

---

# 12. Customer Principle

A Customer is a transaction identity/reference.

A Customer does **not** maintain a financial account balance in this system.

The system shall not treat the customer as a debtor, creditor or wallet merely because the customer performs a transaction.

For example, a customer may deposit money through the business today and later withdraw through another bank/service.

This does not create a persistent customer balance in this system.

---

# 13. Customer Transaction History

Although customers do not have financial balances, their transaction history may be retained.

The system may provide customer-based searching and reporting to identify:

- Customer
- Transaction date
- Service used
- Transaction type
- Amount
- Service charge
- Reference number
- Notes
- Attachments

Transaction history and customer balance are separate concepts.

---

# 14. Financial Account Principle

Financial balances belong to approved business accounts.

Examples include:

- Cash
- Bank
- BLB
- Citizen Remit
- City Express
- IME Remit
- IME Pay
- IPS
- Other approved remittance/service accounts

New accounts may be added according to authorized system rules.

The system shall not require database structural changes merely because a new remittance/service account is introduced.

---

# 15. Account Balance Principle

Each financial account may have:

- Opening Balance
- Incoming transactions
- Outgoing transactions
- Current Balance

Current balance shall be derived/maintained from authorized financial activity according to the Account and Transaction Standard.

After opening, users shall not arbitrarily overwrite account balances.

---

# 16. Negative Balance Principle

Approved operational/remittance accounts may have negative balances.

A transaction shall not necessarily be blocked because the selected remittance account has insufficient balance.

Example:

City Express Balance Before Transaction:

`NPR 2,000`

Remittance Principal:

`NPR 10,000`

Balance After Transaction:

`NPR -8,000`

This is a valid business condition.

---

# 17. Remittance Transaction Principle

When a customer provides cash and the business sends a remittance using a remittance service:

- The selected Remittance Account decreases by the principal remittance amount.
- Cash increases by the principal cash received from the customer.
- Any cash service charge collected also increases Cash.

Example:

Principal Amount:

`NPR 10,000`

Service Charge:

`NPR 100`

Financial effect:

- Cash: `+10,100`
- City Express: `-10,000`

The customer receives transaction history but no customer financial balance.

---

# 18. Principal Amount and Service Charge Separation

The principal transaction amount and service charge are separate values.

They shall not be stored or reported as if they are the same business value.

A transaction may therefore contain:

- Principal Amount
- Service Charge
- Total cash effect where applicable

This separation is required for correct commission/service-charge reporting.

---

# 19. Service Charge Principle

Service Charge is the amount charged directly to the customer for providing a transaction/service.

Where Service Charge is collected in Cash:

**Cash shall increase by the Service Charge amount.**

Service Charge shall remain identifiable separately from the transaction principal.

The system shall be able to report service charges by relevant criteria such as:

- Date
- Staff
- Service
- Transaction type
- Account
- Reporting period

This supports control over daily service-charge collection.

---

# 20. Service Charge vs Remittance Commission

Service Charge and Remittance Commission are different business concepts.

### Service Charge

Paid/collected from the customer as part of providing the service.

### Remittance Commission

Income provided by a remittance/service company to the business.

They shall not be combined into one indistinguishable financial field.

---

# 21. Deposit Principle

For the approved BLB/Bank-style business process, when a customer deposits money and the business receives Cash:

- Cash increases.
- The selected operational Bank/BLB account decreases.

Example:

Customer Deposit:

`NPR 10,000`

Financial effect:

- Cash: `+10,000`
- BLB: `-10,000`

If a Service Charge is additionally collected in Cash, that amount also increases Cash.

No customer balance is created.

---

# 22. Withdrawal Principle

For the approved BLB/Bank-style business process, when the customer receives Cash:

- Cash decreases.
- The selected operational Bank/BLB account increases.

Example:

Withdrawal:

`NPR 10,000`

Financial effect:

- Cash: `-10,000`
- BLB: `+10,000`

No customer balance is created.

The exact treatment of withdrawal Service Charge shall follow the approved transaction standard once the business confirms whether the charge is collected separately or deducted from the amount delivered.

---

# 23. Account-to-Account Transfer Principle

Money may be transferred between company-controlled financial accounts.

An account transfer must identify:

- Send Account
- Receive Account
- Amount

Example:

Bank → City Express

`NPR 50,000`

Effect:

- Bank: `-50,000`
- City Express: `+50,000`

An ordinary account transfer is not automatically income or expense.

It represents movement of existing business funds.

---

# 24. Month-End Settlement Principle

Remittance/service accounts may become negative during normal business operation.

At an approved settlement point, including month-end, the business may transfer money from an appropriate Bank or other account to settle the negative balance.

Example:

City Express:

`NPR -80,000`

Transfer:

Bank → City Express = `NPR 80,000`

Result:

- Bank: `-80,000`
- City Express: `0`

The settlement transaction must remain recorded.

---

# 25. Remittance Commission Principle

Remittance/service companies may provide commission to the business.

Examples may include commission from:

- Citizen Remit
- City Express
- IME Remit
- IME Pay
- Other approved providers

Commission shall be recognized separately from customer Service Charge.

---

# 26. Commission Receipt Principle

When a remittance/service company transfers commission to the business:

1. Commission Income shall be recorded.
2. The actual receiving financial account shall increase.

Example:

City Express Commission:

`NPR 5,000`

Received into business Bank Account:

- Commission Income: `+5,000`
- Bank Account: `+5,000`

This allows the system to answer both:

**How much commission was earned?**

and

**Where was the commission money received?**

---

# 27. Expense Principle

Business expenses shall be recorded separately.

Examples may include:

- Rent
- Employee Salary
- Electricity
- Internet
- Office expenses
- Other approved expenses

Expense categories shall be configurable rather than requiring a separate database column for every expense type.

---

# 28. Expense Payment Principle

Every financial Expense must identify the account from which the expense was paid.

Example:

Office Rent:

`NPR 20,000`

Paid from Bank:

- Expense: `+20,000`
- Bank: `-20,000`

If paid from Cash:

- Expense: `+20,000`
- Cash: `-20,000`

This allows the system to identify both the expense and the location from which money was spent.

---

# 29. Capital Principle

Business Capital shall be treated separately from:

- Current Cash
- Current Bank Balance
- Remittance Account Balances
- Profit
- Loss

The old practice of treating original investment plus/minus accumulated profit/loss as a single running money value shall not define the new system architecture.

---

# 30. Profit and Loss Principle

Profit/Loss represents business performance.

It is not the same as:

- Cash Balance
- Bank Balance
- Capital
- Shareholder Share Value
- Remittance Account Balance

The detailed calculation shall be defined in the Commission, Expense and Profit Standard.

---

# 31. Shareholder Principle

The system shall maintain approved Shareholder information separately from daily customer transactions.

A Shareholder may:

- Hold shares
- Receive shares
- Transfer shares
- Purchase approved shares
- Reduce shareholding
- Exit the company

All shareholding changes must retain history.

---

# 32. Share Unit Valuation

The currently approved base share valuation is:

**1 Share Unit = NPR 1,000**

Example:

500 Share Units × NPR 1,000

Total Share Valuation:

`NPR 500,000`

The system shall calculate shareholder valuation based on approved share quantity and applicable approved per-share value.

---

# 33. Historical Share Value Protection

If the approved value per share changes in the future, historical share transactions shall not be silently rewritten.

A share transaction shall preserve the applicable valuation/rate information required for historical accuracy.

---

# 34. Shareholder-to-Shareholder Transfer

A Shareholder may transfer shares to another Shareholder.

Example:

Shareholder A:

500 shares

Transfers:

100 shares to Shareholder B

Result:

- Shareholder A: `-100 shares`
- Shareholder B: `+100 shares`

This transaction does **not** automatically affect:

- Company Cash
- Company Bank

because the share ownership moves between shareholders.

The company system primarily records the ownership transfer.

---

# 35. New Shareholder Through Transfer

A person who is not currently a shareholder may become a shareholder by receiving/purchasing approved shares from an existing shareholder.

The system shall:

- Create/activate the new shareholder as authorized.
- Reduce the seller's share quantity.
- Increase the buyer's share quantity.
- Preserve the transfer history.

Company Cash/Bank shall not automatically change merely because the transfer occurred between the two parties.

---

# 36. Company Share Issue / Capital Addition

Where shares are issued directly by the company rather than transferred from another shareholder, the transaction is different from a shareholder-to-shareholder transfer.

Any company-issued share/capital addition must follow separately approved rules regarding:

- Share quantity
- Capital
- Receiving account
- Approval

It shall not be treated as an ordinary shareholder transfer.

---

# 37. Shareholder Partial Exit

A shareholder may return/redeem part of the shareholder's holding through the company where approved.

The shareholder's share quantity decreases.

If the company pays the shareholder:

- The selected Cash or Bank account decreases.

Example:

200 shares × NPR 1,000 = NPR 200,000

Paid from Bank:

- Shareholder shares: `-200`
- Bank: `-200,000`

---

# 38. Shareholder Full Exit

A shareholder may exit completely through an approved company settlement.

When all shares are redeemed/returned:

- Shareholding becomes zero.
- Approved payment account decreases by the paid amount.
- Shareholder may become `Exited` or `Inactive`.

Historical shareholder information must remain preserved.

The shareholder record shall not be deleted merely because the shareholder exited.

---

# 39. Shareholder History Principle

The system shall preserve relevant shareholder history including:

- Share additions
- Share reductions
- Share received
- Share transferred
- Company redemption
- Partial exit
- Full exit
- Applicable share valuation
- Date
- Approval information
- Notes
- Attachments

Current share quantity must be traceable to authorized share transactions.

---

# 40. Notes Rule

Every major business transaction/record type shall support a **Note** field.

Notes allow authorized users to record additional context that is not represented by structured fields.

Notes shall not replace mandatory structured financial information.

---

# 41. Attachment / Upload Rule

Every major operational and financial record type shall support Attachment/Upload functionality where applicable.

Examples include:

- Remittance transaction
- Deposit
- Withdrawal
- Account transfer
- Commission
- Expense
- Opening balance
- Share transaction
- Share transfer
- Shareholder exit
- Adjustment/correction

Supported business evidence may include:

- Receipt
- Voucher
- Bank transfer proof
- Screenshot
- Image
- PDF
- Other approved supporting document

Detailed technical file restrictions shall be defined in the security/data standards.

---

# 42. Attachment Historical Integrity

Attachments associated with financial transactions form part of the transaction evidence.

Cancellation of a transaction shall not automatically destroy its supporting evidence.

Historical attachments shall remain linked according to audit and retention rules.

---

# 43. Transaction Reference Principle

Where applicable, a transaction may contain an external/internal reference such as:

- Transaction Number
- Reference Number
- Voucher Number
- Bank Reference
- Remittance Reference

The reference shall assist transaction tracing and reconciliation.

---

# 44. Transaction Integrity Principle

After the approved Opening Balance is established, normal financial balances shall be affected through authorized transactions.

Users shall not arbitrarily overwrite current account balances to make reports appear correct.

If a balance is incorrect, the underlying transaction or an authorized correction/adjustment process must be used.

---

# 45. Hard Delete Principle

Financial transactions shall not normally be permanently deleted through ordinary application use.

If a transaction is incorrect, the system shall use an approved:

- Cancel
- Correction
- Reversal
- Adjustment

process as defined by detailed standards.

This protects historical accountability.

---

# 46. Cancellation Principle

A cancelled transaction shall no longer contribute to active financial calculations after the approved cancellation logic is applied.

However, the historical record shall remain traceable.

The system should be able to identify:

- Original transaction
- Who created it
- Who cancelled/corrected it
- When it was cancelled/corrected
- Reason
- Related correction/replacement transaction where applicable

---

# 47. Audit Principle

Financial and shareholder activities must be attributable to the responsible system user.

Relevant records shall retain sufficient audit information to determine:

- Who created the record
- When it was created
- Who changed/cancelled it where applicable
- When the action occurred

Detailed audit requirements shall be defined separately.

---

# 48. Staff Accountability Principle

One objective of the system is to provide control over daily collections, particularly Service Charges and other cash-related transactions.

The system shall therefore support reporting and audit that can identify transactions entered by Staff.

Staff transaction activity shall be traceable.

---

# 49. Dynamic Business Master Principle

Business categories that may grow over time should not be implemented as permanently fixed database columns when they logically represent master data.

Examples include:

- Remittance providers
- Operational accounts
- Banks
- Expense categories

Adding a new provider/account/category should normally be possible through authorized master management rather than structural database modification.

---

# 50. Financial Traceability Principle

For every financial transaction, the system must be able to determine the financial effect.

Where applicable, this includes:

- Which account increased
- Which account decreased
- Principal amount
- Service charge
- Commission
- Expense
- Transaction type
- Customer
- Staff/User
- Date
- Reference
- Note
- Attachment

A transaction shall not create unexplained balance movement.

---

# 51. Current Balance Visibility

Authorized users shall be able to determine the current position of approved financial accounts.

Examples:

- Cash
- Bank
- BLB
- Citizen Remit
- City Express
- IME Remit
- IME Pay
- IPS
- Other configured accounts

The system shall distinguish positive, zero and negative balances.

---

# 52. Reporting Principle

Reports shall be generated from approved system transactions and business rules.

The system shall support reporting requirements such as:

- Daily transactions
- Date-range transactions
- Customer history
- Account ledger
- Account balances
- Service charges
- Staff transaction activity
- Remittance commissions
- Expenses
- Monthly summaries
- Profit/Loss
- Shareholder holdings
- Shareholder history

Detailed report specifications shall be defined in dependent standards.

---

# 53. Business Date Principle

Every financial transaction must have an approved business/transaction date.

System creation timestamp and business transaction date are separate concepts.

The detailed rules for backdated entries, editing dates and financial periods shall be defined before implementation.

---

# 54. Business Rule Before Code

No developer, AI coding agent or implementation tool may invent financial business logic.

Where behavior is unclear:

**STOP → REVIEW → ASK BUSINESS OWNER → DOCUMENT → APPROVE → IMPLEMENT**

Code shall follow approved business rules.

Existing code shall not override this Constitution.

---

# 55. No Silent Financial Recalculation

Future software updates shall not silently change historical financial results.

Changes to:

- Account logic
- Service charge logic
- Commission logic
- Expense logic
- Share valuation logic
- Profit/Loss logic

must consider historical data impact before implementation.

---

# 56. Change Control

After this Constitution is approved, changes to business rules require explicit Business Owner approval.

A business-rule change may require review of:

- Project Scope
- Account Standard
- Commission/Expense Standard
- Shareholder Standard
- Permission Standard
- Database Standard
- UI Workflow
- Reports
- Existing data

Dependent documents shall not contradict an approved Constitution change.

---

# 57. Unresolved Rules

The following items remain subject to detailed confirmation before implementation:

### 57.1 Withdrawal Service Charge

Confirm whether withdrawal Service Charge is:

- Collected separately from the customer, or
- Deducted from the amount paid to the customer.

### 57.2 Share Issue

Detailed rules for company-issued new shares/capital increase require confirmation.

### 57.3 Profit Distribution

Detailed rules for distributing profit to shareholders require confirmation if this functionality is required.

### 57.4 Financial Period Control

Rules for:

- Backdated transactions
- Closed months
- Reopening periods

require confirmation.

### 57.5 Staff Cancellation

Whether Staff can request cancellation only, or can perform limited cancellation directly, shall be finalized in the permission standard.

These unresolved rules shall not be guessed during development.

---

# 58. Document Authority

This document is the highest business authority for the Remittance Management System.

The following documents must conform to it:

- `02_PROJECT_SCOPE_AND_REQUIREMENTS.md`
- `03_ACCOUNT_AND_TRANSACTION_STANDARD.md`
- `04_COMMISSION_EXPENSE_AND_PROFIT_STANDARD.md`
- `05_SHAREHOLDER_STANDARD.md`
- `06_USER_ROLE_PERMISSION_STANDARD.md`
- `07_DATABASE_AND_DATA_STANDARD.md`
- `08_UI_AND_WORKFLOW_STANDARD.md`
- `09_SECURITY_AUDIT_BACKUP_DEPLOYMENT_STANDARD.md`

Where a conflict exists, this Constitution takes precedence unless the Business Owner formally changes the Constitution.

---

# 59. Approval

**Status:** FINAL DRAFT — Pending Business Owner Approval

After Business Owner approval, this document becomes the authoritative business constitution for design, database planning, development, testing and future maintenance of the Remittance Management System.

---

**END OF DOCUMENT**