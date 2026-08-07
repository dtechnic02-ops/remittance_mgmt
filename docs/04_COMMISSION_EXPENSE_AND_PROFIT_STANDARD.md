# REMITTANCE MANAGEMENT SYSTEM — COMMISSION, EXPENSE AND PROFIT STANDARD

**Document ID:** 04  
**File Name:** `04_COMMISSION_EXPENSE_AND_PROFIT_STANDARD.md`  
**Project:** Remittance Management System  
**Document Type:** Commission, Expense and Profit Standard  
**Version:** 1.0  
**Status:** FINAL DRAFT — Pending Business Owner Approval  
**Authority:** Business Owner  

---

# 1. Purpose

This document defines the approved business rules for:

- Customer Service Charge
- Remittance/Service Provider Commission
- Commission Receipt
- Expense
- Expense Categories
- Expense Payment
- Income
- Profit
- Loss
- Monthly Financial Summary
- Account effects
- Reporting
- Cancellation and correction

This document must comply with:

1. `01_REMITTANCE_BUSINESS_CONSTITUTION.md`
2. `02_PROJECT_SCOPE_AND_REQUIREMENTS.md`
3. `03_ACCOUNT_AND_TRANSACTION_STANDARD.md`
4. `00_PROJECT_INDEX.md`

If this document conflicts with the Business Constitution, the Business Constitution shall take precedence.

---

# 2. Core Principle

The system shall keep the following business concepts separate:

1. **Principal Transaction Amount**
2. **Customer Service Charge**
3. **Remittance/Provider Commission**
4. **Expense**
5. **Profit/Loss**
6. **Financial Account Balance**

These values shall not be mixed into one running balance.

---

# 3. Principal Amount Is Not Income

The principal amount handled during a Remittance, Deposit or Withdrawal transaction is not automatically business income.

Example:

Customer gives:

`NPR 10,000`

for Remittance.

The `NPR 10,000` is the principal transaction amount.

It shall affect the relevant Cash and Remittance Accounts according to the Account Standard.

It shall not automatically be counted as Profit.

---

# 4. Customer Service Charge

Service Charge is the fee collected directly from the customer for providing the service.

Examples:

- Remittance Service Charge
- Deposit Service Charge
- Withdrawal Service Charge
- Other approved transaction Service Charge

Service Charge shall remain separately identifiable from Principal Amount.

---

# 5. Service Charge Cash Effect

Where Service Charge is collected in Cash, Cash shall increase.

Example:

Principal Amount:

`NPR 10,000`

Service Charge:

`NPR 100`

Customer pays:

`NPR 10,100`

Effect:

- Cash Principal Receipt: `+10,000`
- Cash Service Charge Receipt: `+100`

Total Cash increase:

`NPR 10,100`

---

# 6. Service Charge Is Business Income

Customer Service Charge shall be treated as business income for Profit/Loss reporting, subject to any future approved accounting adjustment.

Example:

Daily Service Charges:

`NPR 5,000`

Service Charge Income:

`NPR 5,000`

The principal amounts of the underlying transactions shall not be added again as income.

---

# 7. Service Charge Stored With Transaction

Service Charge shall be recorded with the related operational transaction.

The transaction shall retain:

- Customer
- Transaction Type
- Service/Provider
- Principal Amount
- Service Charge
- Date
- Staff/User
- Reference
- Note
- Attachment
- Status

This enables direct transaction-level accountability.

---

# 8. Zero Service Charge

A valid transaction may contain:

`Service Charge = 0`

where business rules permit.

Zero Service Charge shall not invalidate an otherwise valid transaction.

---

# 9. Service Charge Accountability

The system shall support identifying Service Charge by:

- Staff
- Date
- Service
- Transaction Type
- Customer
- Reporting Period

This is required because Service Charge may be collected physically in Cash by Staff.

---

# 10. Staff Service Charge Control

Admin shall be able to compare:

- Transactions entered by Staff
- Service Charges entered by Staff
- Total expected Service Charge
- Cash position where relevant

The system should help identify missing or incorrect Service Charge entry.

---

# 11. Service Charge Report

The system shall support Service Charge reports including:

- Daily Service Charge
- Date Range Service Charge
- Monthly Service Charge
- Staff-wise Service Charge
- Provider-wise Service Charge
- Transaction Type-wise Service Charge

Cancelled transactions shall not contribute to active Service Charge totals.

---

# 12. Service Charge and Commission Are Different

Service Charge and Remittance Commission shall never be treated as the same business value.

### Service Charge
Collected from Customer.

### Remittance Commission
Paid or credited by the Remittance/Service Provider to the business.

They may both contribute to income but originate from different sources.

---

# 13. Remittance/Provider Commission

A provider may pay Commission to the business based on transaction activity or an agreed commercial arrangement.

Examples include:

- City Express Commission
- Citizen Remit Commission
- IME Remit Commission
- IME Pay Commission
- Other approved provider Commission

---

# 14. Commission Recognition

Commission shall be recorded when the approved business event occurs.

The Commission record shall identify:

- Date
- Provider
- Commission Amount
- Receiving Account
- Reference
- Note
- Attachment
- Created By
- Status

---

# 15. Commission Receiving Account

Commission must identify where the money was actually received.

Examples:

- Cash
- Business Bank Account
- Asha Enterprises Account
- Other approved Financial Account

The receiving account shall increase by the Commission Amount.

---

# 16. Commission Example

City Express pays Commission:

`NPR 5,000`

to Business Bank.

Financial result:

- Bank Account: `+5,000`
- Commission Income: `+5,000`

This allows the system to answer:

- How much Commission did City Express pay?
- Where was the money received?

---

# 17. Commission Paid to Company Account

Where multiple remittance providers transfer monthly Commission to a common company account, each provider's Commission shall remain individually identifiable.

Example:

City Express:

`NPR 5,000`

Citizen Remit:

`NPR 3,000`

IME Remit:

`NPR 2,000`

Received into Asha Enterprises Bank:

Total Bank Increase:

`NPR 10,000`

But reporting shall preserve:

- City Express Commission = 5,000
- Citizen Remit Commission = 3,000
- IME Remit Commission = 2,000

---

# 18. Commission Is Income

Valid Commission received/recognized according to approved business rules shall contribute to business income.

Commission shall not be confused with:

- Account Transfer
- Principal Remittance Amount
- Shareholder Capital
- Opening Balance

---

# 19. Account Transfer Is Not Commission

A transfer such as:

`Bank → City Express`

is not Commission.

It only moves existing company money between Financial Accounts.

The system shall not count Account Transfer as Income.

---

# 20. Expense Principle

Expense represents money spent for approved business operations.

Examples may include:

- Office Rent
- Employee Salary
- Electricity
- Internet
- Office Supplies
- Maintenance
- Transport
- Bank Charges
- Other approved expenses

---

# 21. Expense Category Master

Expense Categories shall be maintained as configurable master data.

Each Expense Category may include:

- Category Name
- Status
- Note
- Attachment where applicable

A new Expense Category shall not require adding a new database column.

---

# 22. Expense Transaction

An Expense transaction shall identify:

- Business Date
- Expense Category
- Particular/Description
- Amount
- Payment Account
- Reference
- Note
- Attachment
- Created By
- Status

---

# 23. Expense Payment Account

Every financial Expense must identify the account from which money was actually paid.

Examples:

- Cash
- Bank
- Other approved Financial Account

---

# 24. Cash Expense Example

Office Rent:

`NPR 20,000`

Paid from Cash.

Effect:

- Cash: `-20,000`
- Expense: `+20,000`

---

# 25. Bank Expense Example

Internet Expense:

`NPR 3,000`

Paid from Bank.

Effect:

- Bank: `-3,000`
- Expense: `+3,000`

---

# 26. Expense Is Not Account Transfer

Expense shall not be recorded as a normal Account Transfer.

An Account Transfer moves money between company-controlled accounts.

Expense represents money leaving the business for an expense purpose.

---

# 27. Expense with Attachment

Expense records shall support attachments such as:

- Invoice
- Bill
- Receipt
- Voucher
- Bank payment screenshot
- PDF
- Image

Attachment shall remain linked to the Expense record.

---

# 28. Expense Note

Expense shall support Note.

The Note may explain:

- Payment purpose
- Special approval
- Period covered
- Adjustment reason
- Other relevant information

---

# 29. Expense Cancellation

A cancelled Expense shall:

- Stop contributing to active Expense totals.
- Reverse the Financial Account effect.
- Remain available in history.
- Preserve cancellation reason.
- Preserve related attachment/history.

---

# 30. Commission Cancellation

A cancelled Commission Receipt shall:

- Stop contributing to active Commission Income.
- Reverse the receiving Account effect.
- Remain traceable.

---

# 31. Service Charge Cancellation

When the underlying transaction is cancelled, its Service Charge financial and reporting effect shall also be reversed according to the transaction cancellation rules.

---

# 32. Income Components

Initial approved business income may include:

1. Customer Service Charge
2. Remittance/Service Provider Commission
3. Other separately approved business income categories

New income categories require approved business rules.

---

# 33. Principal Amount Exclusion from Income

The following shall not automatically count as Income:

- Remittance Principal
- Deposit Principal
- Withdrawal Principal
- Account Transfer
- Opening Balance
- Shareholder Capital
- Shareholder-to-Shareholder Share Transfer

---

# 34. Expense Components

Approved Expense totals shall be calculated from active Expense transactions.

Cancelled Expense records shall not contribute to active Expense totals.

---

# 35. Basic Profit Calculation

At the initial business-rule level:

`Profit/Loss = Total Approved Income - Total Approved Expense`

Where:

`Total Approved Income = Service Charge Income + Provider Commission Income + Other Approved Income`

---

# 36. Profit Example

Monthly Service Charge:

`NPR 50,000`

Provider Commission:

`NPR 30,000`

Total Income:

`NPR 80,000`

Expenses:

`NPR 55,000`

Result:

`NPR 25,000 Profit`

---

# 37. Loss Example

Monthly Income:

`NPR 60,000`

Monthly Expense:

`NPR 75,000`

Result:

`NPR -15,000`

This represents:

**Loss = NPR 15,000**

---

# 38. Profit Is Not Cash

Profit does not mean the same amount must physically exist in Cash.

Money may be located in:

- Cash
- Bank
- Remittance Accounts
- BLB
- Other operational accounts

Profit represents business performance, not physical cash location.

---

# 39. Loss Is Not Negative Cash

A business may report a Loss while still holding positive Cash or Bank balances.

Likewise, a business may report Profit while one Remittance Account remains negative.

Therefore Account Balance and Profit/Loss shall remain separate reports.

---

# 40. Capital Is Not Income

Money introduced as approved Shareholder/Company Capital shall not be counted as ordinary business income.

Example:

Shareholder contributes:

`NPR 500,000`

This may increase an approved Cash/Bank account and Capital position.

It shall not create `NPR 500,000 Profit`.

---

# 41. Opening Balance Is Not Income

Opening Balance represents the approved starting position of an Account.

It shall not be counted as current-period business income.

---

# 42. Shareholder Exit Is Not Expense

Where the company pays an approved shareholder redemption/exit amount, the Financial Account decreases.

However, this shall not automatically be treated as an ordinary operating Expense.

Its treatment shall follow the Shareholder Standard.

---

# 43. Shareholder-to-Shareholder Transfer Has No Profit Effect

A share transfer directly between shareholders shall not affect:

- Business Income
- Business Expense
- Profit/Loss
- Company Cash
- Company Bank

unless there is a separate approved company transaction.

---

# 44. Monthly Financial Summary

The system shall provide an authorized Monthly Financial Summary.

The summary may include:

- Total Principal Transactions
- Total Service Charge
- Provider Commission
- Total Income
- Expense totals
- Profit/Loss
- Cash Balance
- Bank Balance
- Remittance Account Balances
- Negative Account Balances

Principal transaction totals shall remain distinct from Income.

---

# 45. Service-Wise Income Reporting

The system should support identifying income generated from each Service/Provider.

Example:

| Service | Service Charge | Provider Commission |
|---|---:|---:|
| City Express | NPR 10,000 | NPR 5,000 |
| Citizen Remit | NPR 8,000 | NPR 4,000 |
| IME Remit | NPR 6,000 | NPR 3,000 |

The exact UI/report layout shall be defined later.

---

# 46. Staff-Wise Service Charge Report

Admin shall be able to identify:

- Staff Name
- Number of Transactions
- Principal handled
- Service Charge collected
- Cancelled transactions
- Reporting period

This supports daily Staff accountability.

---

# 47. Expense Category Report

Expense reporting shall support grouping by Expense Category.

Example:

- Rent
- Salary
- Electricity
- Internet
- Other Expense

This replaces the legacy design of separate fixed columns for every Expense type.

---

# 48. Payment Account Expense Report

Authorized users shall be able to report Expense by Payment Account.

Example:

- Expenses paid from Cash
- Expenses paid from Bank
- Expenses paid from another approved Account

---

# 49. Provider Commission Report

Commission reports should support:

- Provider
- Month
- Date Range
- Receiving Account
- Amount
- Reference
- Status

---

# 50. Commission Reconciliation

Admin should be able to compare expected/known provider Commission against actual received Commission where sufficient business information exists.

The system shall not invent expected Commission calculation formulas unless separately defined.

---

# 51. Provider Commission Rate — Not Assumed

The system shall not assume that provider Commission is always a fixed percentage of Principal Amount.

If Commission rates are required in the future, a separate business rule must define:

- Provider
- Service
- Rate
- Effective Date
- Calculation method

Until approved, Commission may be entered as an actual received amount.

---

# 52. Service Charge Rate — Not Assumed

The system shall not assume that customer Service Charge is always:

- Fixed
- Percentage-based
- Automatically calculated

unless a separate approved rule defines such behavior.

Initial design shall permit the approved transaction Service Charge amount to be recorded.

---

# 53. Commission Period

Provider Commission may be received:

- Daily
- Weekly
- Monthly
- At another provider-defined settlement period

The system shall record actual Commission according to the business event.

Month-end Commission is common but shall not be hard-coded as the only possibility.

---

# 54. Commission Reference

Commission records should support:

- Provider statement reference
- Bank transaction reference
- Voucher number
- Internal reference
- Other approved evidence

---

# 55. Commission Attachment

Commission records shall support Attachment.

Typical evidence may include:

- Provider statement
- Bank transfer screenshot
- Commission statement
- Voucher
- PDF/Image

---

# 56. Profit Reporting Period

Profit/Loss reporting shall support an approved date range.

At minimum:

- Daily summary where useful
- Monthly
- Financial Year/Annual reporting where required

Detailed financial-year behavior shall be finalized separately.

---

# 57. Nepali Financial Year

The legacy system used Nepali financial-year references.

The new system may support Nepali financial-year reporting where required.

Exact date/financial-year rules must be finalized before implementation.

---

# 58. Business Date vs Entry Date

Income and Expense reports shall normally follow the approved Business Date.

Created At shall remain available for audit.

Example:

Transaction Business Date:

`2083-04-10`

Record entered:

`2083-04-11`

The accounting report may use Business Date while audit history preserves actual entry time.

---

# 59. Closed Period

Formal month/year closing rules are not yet finalized.

Once period-closing functionality is approved, transactions in a closed period should not be freely changed.

This shall be defined before implementing financial locking.

---

# 60. Backdated Income/Expense

Backdated Commission or Expense entry shall follow approved role and financial-period rules.

Unrestricted backdating shall not be assumed.

---

# 61. Financial Adjustment

Any adjustment to Service Charge, Commission or Expense after posting shall use an approved:

- Cancellation
- Reversal
- Correction
- Adjustment

workflow.

Silent alteration of historical Profit/Loss is prohibited.

---

# 62. No Direct Profit Editing

Profit/Loss shall be calculated from approved source transactions.

Users shall not manually edit:

`Current Profit`

or:

`Current Loss`

as ordinary fields.

---

# 63. Legacy Profit/Loss Exclusion

The old Microsoft Access method of carrying:

`Original Investment + Profit/Loss = Old Month Saving`

shall not define the new system.

The new system shall separate:

- Capital
- Income
- Expense
- Profit/Loss
- Financial Account Balances

---

# 64. Legacy Historical Profit

Any historical Profit/Loss retained from the old system shall remain legacy/reference information unless specifically approved as part of opening financial position.

It shall not be silently reconstructed into new live transactions.

---

# 65. Opening Day Profit

Opening Balances shall not generate current-period Profit.

New system Profit/Loss starts from new-system approved transactions unless a separate opening retained-profit rule is formally approved.

---

# 66. Profit Distribution

Distribution of Profit to Shareholders is not yet fully defined.

If required, future rules must define:

- Approved distributable amount
- Shareholder entitlement
- Share basis
- Payment account
- Approval
- Tax/other treatment where applicable
- Transaction history

No Profit Distribution logic shall be invented before approval.

---

# 67. Retained Profit

If the business wishes to retain Profit instead of distributing it, the accounting/reporting treatment must be defined before implementation of a formal retained-earnings feature.

---

# 68. Expense Approval

Whether all Expenses require Admin approval or whether Staff may enter certain Expenses shall be defined in the Role Permission Standard.

The financial effect shall remain the same once an Expense becomes approved/active.

---

# 69. Expense Status

Expense records shall at minimum support:

- Active
- Cancelled

Additional statuses such as:

- Pending
- Approved
- Rejected

may be introduced if an approval workflow is required.

---

# 70. Commission Status

Commission records shall at minimum support:

- Active
- Cancelled

Additional approval states may be added if business workflow requires them.

---

# 71. Service Charge Status

Service Charge inherits the status of its related operational transaction unless otherwise required.

A cancelled operational transaction shall not leave an active Service Charge income entry.

---

# 72. Transaction Atomicity

Where a transaction creates both:

- Financial Account effect, and
- Income/Expense effect

both must succeed together.

Example Commission Receipt:

- Bank +5,000
- Commission Income +5,000

Either both succeed or neither succeeds.

Partial posting is prohibited.

---

# 73. Expense Atomicity

Expense save must not create:

- Expense record without reducing payment account,

or:

- Payment-account reduction without Expense record.

Both effects must remain consistent.

---

# 74. Profit Recalculation Consistency

Reports must consistently exclude Cancelled transactions.

A cancellation or correction must be reflected correctly in:

- Income Report
- Expense Report
- Profit/Loss
- Account Ledger
- Account Balance

---

# 75. Income Audit Trail

Income-related records shall identify:

- Source
- Amount
- Date
- User
- Reference
- Status
- Note
- Attachment

where applicable.

---

# 76. Expense Audit Trail

Expense-related records shall identify:

- Expense Category
- Amount
- Payment Account
- Date
- User
- Reference
- Status
- Note
- Attachment

---

# 77. No Hidden Income

No process shall increase reported Income without an identifiable approved income-producing transaction.

---

# 78. No Hidden Expense

No process shall increase reported Expense without an identifiable approved Expense or other approved financial event.

---

# 79. Dashboard Income Summary

Admin Dashboard may provide summaries such as:

- Today's Service Charge
- This Month's Service Charge
- This Month's Commission
- This Month's Expense
- This Month's Profit/Loss

Dashboard summaries must match underlying reports.

---

# 80. Staff Dashboard

Staff Dashboard may show permitted operational Service Charge information.

Sensitive total business Profit/Loss shall not automatically be visible to Staff.

Final visibility is governed by the Role Permission Standard.

---

# 81. Shareholder Dashboard

Shareholder shall not automatically receive access to detailed Service Charge, Commission or Expense reports.

Shareholder visibility shall follow approved Shareholder and Permission rules.

---

# 82. Report Export

Income, Commission, Service Charge, Expense and Profit reports may support:

- Print
- PDF
- Spreadsheet export

where approved by the UI/Workflow Standard.

Exported totals must match system calculations.

---

# 83. Report Filters

Relevant reports should support filters such as:

- Start Date
- End Date
- Month
- Financial Year
- Service/Provider
- Staff
- Expense Category
- Payment Account
- Receiving Account
- Status

---

# 84. Zero-Income Period

A reporting period may legitimately have:

- Zero Service Charge
- Zero Commission
- Zero Income

The system shall still generate valid reports.

---

# 85. Negative Profit

Negative Profit shall be displayed as Loss.

Example:

Income:

`NPR 50,000`

Expense:

`NPR 70,000`

Result:

`NPR -20,000`

Report:

**Loss: NPR 20,000**

---

# 86. Profit Does Not Change Share Quantity

Normal Profit/Loss shall not automatically:

- Increase shareholder shares
- Decrease shareholder shares
- Change value per share

Shareholding changes require approved Share transactions.

---

# 87. Profit Does Not Automatically Move Cash

Calculating Profit does not itself create a Cash/Bank transaction.

Actual Cash/Bank movements come from underlying transactions.

---

# 88. Expense Does Not Automatically Change Capital

Ordinary Expense reduces business Profit but shall not directly rewrite Shareholder Capital or Share Quantity.

---

# 89. Share Value Separation

The approved:

`1 Share = NPR 1,000`

valuation shall not be automatically recalculated from monthly Profit/Loss unless the business later explicitly changes the share valuation rule.

---

# 90. Note and Attachment Global Rule

Service Charge-related transactions, Commission records and Expense records shall support:

- Note
- Attachment

according to the global Business Constitution.

---

# 91. Deleted Master Category Protection

An Expense Category or Provider with historical records shall normally be made Inactive rather than hard-deleted.

Historical reporting must remain intact.

---

# 92. Income Source Master

If additional Income Categories are required in the future, they should be implemented through an approved dynamic master rather than hard-coded columns where appropriate.

---

# 93. Financial Precision

All Service Charge, Commission, Expense and Profit calculations shall use approved financial decimal precision.

Floating-point data types shall not be used for financial storage.

Exact technical precision shall be defined in:

`07_DATABASE_AND_DATA_STANDARD.md`

---

# 94. Unresolved Rules

The following remain unresolved and shall be confirmed before related coding:

1. Withdrawal Service Charge exact handling.
2. Whether Service Charge may sometimes be received through Bank instead of Cash.
3. Whether Staff may enter Expenses.
4. Whether Expense requires Admin approval.
5. Whether Provider Commission has expected-rate calculations.
6. Profit Distribution to Shareholders.
7. Retained Profit treatment.
8. Financial Period closing/reopening.
9. Exact Financial Year implementation.
10. Any tax-specific treatment required by the business.

These rules shall not be guessed.

---

# 95. Testing Requirement

Testing shall verify:

- Principal is not counted as Income.
- Service Charge is calculated/reported separately.
- Service Charge affects Cash correctly.
- Commission increases correct receiving account.
- Commission contributes to Income.
- Expense reduces correct payment account.
- Expense contributes to Profit/Loss.
- Transfers do not create false Income.
- Opening Balance does not create Profit.
- Capital does not create Profit.
- Cancelled records disappear from active totals.
- Corrected transactions produce correct reports.
- Profit/Loss matches source transactions.
- Notes and Attachments remain linked.
- User attribution remains available.

---

# 96. Acceptance Rule

This module is acceptable only when:

- Service Charge is fully traceable.
- Provider Commission is fully traceable.
- Expense is linked to the actual payment account.
- Income and Expense remain separate from account balances.
- Principal transaction amounts are not incorrectly reported as Income.
- Profit/Loss is calculated from approved active transactions.
- Cancelled records are excluded correctly.
- Historical records remain auditable.
- Dynamic categories can be maintained without fixed-column architecture.

---

# 97. Development Rule

No developer or AI coding agent may invent income, expense or profit behavior.

If a financial rule is unclear:

**STOP → REVIEW CONSTITUTION → REVIEW ACCOUNT STANDARD → ASK BUSINESS OWNER → DOCUMENT → APPROVE → IMPLEMENT**

---

# 98. Document Authority

This document is the authoritative detailed standard for:

- Service Charge
- Provider Commission
- Expense
- Income
- Profit/Loss

It is subordinate to:

`01_REMITTANCE_BUSINESS_CONSTITUTION.md`

and must be used together with:

- `02_PROJECT_SCOPE_AND_REQUIREMENTS.md`
- `03_ACCOUNT_AND_TRANSACTION_STANDARD.md`

Dependent documents include:

- `05_SHAREHOLDER_STANDARD.md`
- `06_USER_ROLE_PERMISSION_STANDARD.md`
- `07_DATABASE_AND_DATA_STANDARD.md`
- `08_UI_AND_WORKFLOW_STANDARD.md`
- `09_SECURITY_AUDIT_BACKUP_DEPLOYMENT_STANDARD.md`

---

# 99. Approval

**Status:** FINAL DRAFT — Pending Business Owner Approval

After Business Owner approval, this document becomes the authoritative Commission, Expense and Profit Standard for the Remittance Management System.

---

**END OF DOCUMENT**