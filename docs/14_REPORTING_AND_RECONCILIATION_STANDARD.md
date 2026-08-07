# REMITTANCE MANAGEMENT SYSTEM — REPORTING AND RECONCILIATION STANDARD

**Document ID:** 14  
**File Name:** `14_REPORTING_AND_RECONCILIATION_STANDARD.md`  
**Project:** Remittance Management System  
**Document Type:** Reporting and Reconciliation Standard  
**Version:** 1.0  
**Status:** FINAL DRAFT — Pending Business Owner Approval  
**Authority:** Business Owner  

---

# 1. Purpose

This document defines the approved reporting, financial reconciliation and management-review rules for the Remittance Management System.

It governs:

- Daily reports
- Date-range reports
- Customer transaction history
- Account balance reports
- Account ledgers
- Negative balance reports
- Service Charge reports
- Staff transaction reports
- Provider Commission reports
- Expense reports
- Profit/Loss reports
- Monthly summaries
- Shareholder reports
- Share ledgers
- Cancellation reporting
- Account reconciliation
- Cash reconciliation
- Provider reconciliation
- Opening balance reconciliation
- Export and print behavior

Reports shall reflect approved transaction data and shall not create separate financial logic.

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

If any reporting requirement conflicts with the Business Constitution, the Business Constitution shall prevail.

---

# 3. Core Reporting Principle

Reports shall be generated from approved source transactions.

Reports shall not maintain a separate manually editable financial truth.

The following must reconcile:

**Transaction Data  
→ Ledger  
→ Account Balance  
→ Dashboard  
→ Reports**

If these values disagree, the disagreement shall be treated as a defect or reconciliation issue.

---

# 4. Business Date Principle

Financial reports shall primarily use the approved **Business Date**.

`Created At` remains an audit timestamp.

A transaction entered today for an approved earlier Business Date shall appear in financial reporting according to the Business Date, subject to backdating/period rules.

---

# 5. Active Transaction Principle

Only financially active transactions shall contribute to active business totals.

Cancelled transactions shall not contribute to:

- Principal totals
- Service Charge totals
- Commission totals
- Expense totals
- Account balances
- Profit/Loss

unless a specific audit report intentionally includes them.

---

# 6. Cancelled Transaction Visibility

Cancelled transactions shall remain visible in history.

Reports may support status filters:

- Active
- Cancelled
- All

A cancelled transaction shall be clearly identified.

---

# 7. Financial Report Permission

Reports shall follow role permissions.

### Admin

May access authorized business-wide reports.

### Staff

May access only approved operational reports.

### Shareholder

May access only approved own Shareholder reports.

Report export shall not bypass these permissions.

---

# 8. Daily Transaction Report

The system shall provide a Daily Transaction Report.

It may include:

- Business Date
- Transaction ID
- Customer
- Transaction Type
- Provider/Service
- Principal Amount
- Service Charge
- Reference
- Staff/User
- Status

---

# 9. Daily Transaction Summary

The Daily Report may provide totals such as:

- Number of Transactions
- Total Principal Amount
- Total Service Charge
- Remittance Transaction Count
- Deposit Count
- Withdrawal Count

Principal transaction amount shall remain separate from Income.

---

# 10. Date Range Report

Authorized users shall be able to select:

- Start Date
- End Date

The report shall show transactions within that approved Business Date range.

---

# 11. Transaction Type Filter

Transaction reports should support filters such as:

- Remittance
- Deposit
- Withdrawal
- Account Transfer
- Commission
- Expense
- Settlement
- Adjustment

where relevant.

---

# 12. Provider Filter

Reports may support Provider/Service filters such as:

- City Express
- Citizen Remit
- IME Remit
- IME Pay
- BLB
- IPS
- Other configured provider

Providers shall be dynamically sourced from master data.

---

# 13. Customer Transaction History

Authorized Admin/Staff may search a Customer and view permitted historical transactions.

Customer history may include:

- Date
- Transaction Type
- Provider
- Principal Amount
- Service Charge
- Reference
- Staff
- Status
- Note
- Attachment access where authorized

---

# 14. Customer History Is Not Customer Ledger

Customer History shall not show a running financial Customer Balance.

Customer does not maintain financial balance under approved business rules.

---

# 15. Account Balance Report

Admin shall be able to view current Financial Account balances.

Example:

| Account | Category | Current Balance |
|---|---|---:|
| Cash | Cash | NPR 250,000 |
| Bank | Bank | NPR 800,000 |
| City Express | Remittance | NPR -50,000 |
| Citizen Remit | Remittance | NPR 30,000 |
| BLB | Operational | NPR -20,000 |

---

# 16. Positive and Negative Balance Display

The report shall clearly distinguish:

- Positive
- Zero
- Negative

balances.

A negative account shall not be hidden or converted to zero.

---

# 17. Account Ledger Report

Each Financial Account shall have a ledger report.

Recommended columns:

- Business Date
- Transaction ID
- Transaction Type
- Particular/Customer
- Reference
- Amount In
- Amount Out
- Running Balance
- User
- Status

---

# 18. Ledger Opening Position

The ledger shall begin from the approved Opening Balance for the selected reporting boundary where appropriate.

---

# 19. Ledger Calculation

Conceptually:

`Running Balance = Previous Balance + Amount In - Amount Out`

The technical direction shall follow the approved Account Standard.

---

# 20. Ledger Reconciliation Rule

For each Account:

`Opening Balance + Total Active In - Total Active Out = Current Balance`

This equality must hold.

---

# 21. Negative Account Report

Admin shall have a dedicated report for negative Financial Accounts.

Recommended fields:

- Account
- Category
- Current Negative Balance
- Last Transaction Date
- Provider where applicable

---

# 22. Negative Account Purpose

The report allows Admin to identify accounts requiring review or settlement.

A negative balance itself is not necessarily an error.

---

# 23. Settlement Report

Admin may view settlement transactions separately.

Fields may include:

- Date
- Send Account
- Receive Account
- Settlement Amount
- Previous Balance
- Resulting Balance
- Reference
- User

---

# 24. Account Transfer Report

Account Transfer report shall show:

- Business Date
- Send Account
- Receive Account
- Amount
- Reference
- User
- Status

Transfers shall not appear as Income or Expense.

---

# 25. Service Charge Report

Service Charge is a key management report.

It shall support filters such as:

- Date
- Date Range
- Staff
- Provider
- Transaction Type
- Status

---

# 26. Service Charge Report Columns

The report may include:

- Date
- Transaction ID
- Customer
- Provider
- Principal
- Service Charge
- Staff
- Reference
- Status

---

# 27. Service Charge Total

Active Service Charge total shall be calculated from active transactions.

Cancelled transactions shall be excluded.

---

# 28. Staff Service Charge Report

Admin shall be able to compare Staff activity.

Example:

| Staff | Transactions | Principal | Service Charge |
|---|---:|---:|---:|
| Staff A | 30 | NPR 300,000 | NPR 3,500 |
| Staff B | 25 | NPR 250,000 | NPR 2,900 |

This supports control of collected charges.

---

# 29. Staff Activity Report

Admin may review:

- Staff Name
- Transactions Created
- Transaction Types
- Principal Amount
- Service Charge
- Cancelled Transactions
- Entry Time
- Business Date

---

# 30. Staff Report Limitation

Staff shall not automatically access another Staff member's complete financial activity.

Permission shall follow `06_USER_ROLE_PERMISSION_STANDARD.md`.

---

# 31. Provider Transaction Report

Admin may view provider-specific transaction activity.

Example:

**City Express**

- Transaction Count
- Principal Sent
- Service Charge
- Current Account Balance
- Provider Commission Received

These values shall remain separate.

---

# 32. Provider Principal vs Commission

Provider transaction principal and Provider Commission shall not be combined.

Example:

City Express Principal Volume:

`NPR 1,000,000`

Commission:

`NPR 10,000`

These represent different business values.

---

# 33. Commission Report

Commission report shall support:

- Provider
- Date
- Date Range
- Receiving Account
- Status

---

# 34. Commission Report Fields

Recommended fields:

- Business Date
- Provider
- Commission Amount
- Receiving Account
- Reference
- User
- Status

---

# 35. Commission Summary

Monthly Commission Summary may show:

| Provider | Commission |
|---|---:|
| City Express | NPR 5,000 |
| Citizen Remit | NPR 3,000 |
| IME Remit | NPR 2,000 |
| Total | NPR 10,000 |

---

# 36. Expense Report

Expense report shall support:

- Date Range
- Expense Category
- Payment Account
- User
- Status

---

# 37. Expense Report Fields

Recommended fields:

- Date
- Expense Category
- Particular
- Amount
- Payment Account
- Reference
- User
- Status

---

# 38. Expense Category Summary

Monthly Expense report may group:

- Rent
- Salary
- Electricity
- Internet
- Office Expense
- Other categories

Categories shall come from Expense Category Master.

---

# 39. Expense Payment Account Report

Admin may review expenses according to Payment Account.

Example:

Cash Expenses:

`NPR 20,000`

Bank Expenses:

`NPR 40,000`

Total:

`NPR 60,000`

---

# 40. Income Report

Approved Income Report shall include:

- Customer Service Charge
- Provider Commission
- Other approved Income

It shall exclude ordinary Principal movement.

---

# 41. Profit/Loss Report

The system shall calculate:

`Total Approved Income - Total Approved Expense`

---

# 42. Profit/Loss Report Layout

Recommended structure:

### Income

Service Charge  
Provider Commission  
Other Approved Income  

**Total Income**

### Expense

Expense Categories / Total Expense  

**Total Expense**

### Result

**Net Profit / Loss**

---

# 43. Profit/Loss Example

Service Charge:

`NPR 50,000`

Provider Commission:

`NPR 30,000`

Total Income:

`NPR 80,000`

Expense:

`NPR 60,000`

Result:

`NPR 20,000 Profit`

---

# 44. Loss Report

If:

Income:

`NPR 60,000`

Expense:

`NPR 80,000`

Report shall show:

**Loss = NPR 20,000**

---

# 45. Principal Exclusion

The following shall not automatically appear as Income in Profit/Loss:

- Remittance Principal
- Deposit Principal
- Withdrawal Principal
- Account Transfer
- Opening Balance
- Share Capital
- Share Transfer

---

# 46. Capital Exclusion

Shareholder capital/share additions shall not be ordinary Profit.

---

# 47. Shareholder Redemption Exclusion

Company Share Redemption payment shall not automatically be included in ordinary operating Expense.

It shall remain identifiable as Shareholder/Capital activity.

---

# 48. Monthly Summary

Admin Monthly Summary may combine management information including:

- Total Transaction Count
- Total Principal
- Total Service Charge
- Total Provider Commission
- Total Expense
- Profit/Loss
- Current Cash
- Current Bank
- Remittance Balances
- Negative Accounts

---

# 49. Monthly Summary Separation

Operational volume and financial income must be visually separated.

Example:

**Transaction Volume:** NPR 5,000,000

does not mean:

**Income:** NPR 5,000,000

---

# 50. End-of-Day Summary

Admin may use End-of-Day Summary containing:

- Total Transactions
- Principal Volume
- Service Charge
- Cash movement
- Negative balances
- Staff totals
- Cancelled transactions

This is a review report, not necessarily a formal closing process.

---

# 51. Cash Reconciliation Report

Admin should be able to compare:

**System Cash Balance**

with:

**Physical Cash Count**

---

# 52. Cash Reconciliation Record

Where a reconciliation process is implemented, it may contain:

- Reconciliation Date
- System Cash
- Physical Cash
- Difference
- Note
- Attachment
- User
- Status

---

# 53. Cash Difference

Conceptually:

`Difference = Physical Cash - System Cash`

Example:

System:

`NPR 100,000`

Physical:

`NPR 99,500`

Difference:

`NPR -500`

This does not automatically change the system balance.

---

# 54. Reconciliation Does Not Auto-Adjust

Recording a difference shall not automatically generate Adjustment unless Admin deliberately approves an Adjustment workflow.

---

# 55. Bank Reconciliation

Admin may compare system Bank balance against actual Bank statement.

Any difference shall be investigated.

---

# 56. Provider Reconciliation

Admin may compare provider account balances against:

- Provider statement
- Provider application balance
- Approved manual statement

where available.

---

# 57. BLB Reconciliation

Because BLB was historically maintained outside the old software, the new system should allow Admin to compare BLB system balance with actual BLB records.

---

# 58. Reconciliation Status

If formal reconciliation records are implemented, statuses may include:

- Pending
- Matched
- Difference Found
- Adjusted/Resolved

This feature may be simplified in Version 1.

---

# 59. Opening Balance Reconciliation

Opening balances shall be compared with the approved Opening Sheet/meeting record before finalization.

---

# 60. Shareholder Current Holding Report

Admin shall be able to view:

- Shareholder
- Current Share Quantity
- Applicable Share Value
- Current Valuation
- Status

---

# 61. Shareholder Own Statement

Shareholder may view only own permitted Share Statement.

It may include:

- Opening Shares
- Shares Received
- Shares Transferred Out
- Redemption
- Current Shares
- Current Valuation

---

# 62. Share Ledger Report

Share Ledger may contain:

- Business Date
- Transaction Type
- Shares In
- Shares Out
- Running Share Quantity
- Value Per Share
- Related Shareholder
- Reference
- Status

---

# 63. Share Transfer Report

Admin report may show:

- Date
- From Shareholder
- To Shareholder
- Quantity
- Value Per Share
- Calculated Valuation
- Status

No Company Cash/Bank movement shall be inferred from this report.

---

# 64. Share Redemption Report

Admin may view:

- Date
- Shareholder
- Redeemed Shares
- Value Per Share
- Settlement Amount
- Payment Account
- Status

---

# 65. Shareholder Exit Report

The system may report:

- Shareholder
- Exit Date
- Exit Type
- Final Shares
- Redemption Amount where applicable
- Status

---

# 66. Active Shareholder Report

An Active Shareholder report shall exclude Exited/Inactive shareholders where filter requires Active only.

---

# 67. Historical Shareholder Report

Admin may include Exited Shareholders when historical reporting is required.

---

# 68. Cancellation Report

Admin shall be able to review cancelled transactions.

Fields may include:

- Original Transaction
- Business Date
- Transaction Type
- Amount
- Service Charge
- Created By
- Cancelled By
- Cancelled At
- Cancellation Reason

---

# 69. Correction Report

Where correction links exist, Admin should be able to trace:

**Original → Cancelled/Reversed → Replacement**

---

# 70. Adjustment Report

Financial Adjustments shall be separately reportable.

Recommended fields:

- Date
- Account
- Amount
- Direction
- Reason
- User
- Attachment
- Reference

Adjustments shall not disappear inside ordinary transaction reports.

---

# 71. Opening Balance Report

Admin shall be able to print/view the approved Opening Balance position.

The report may include:

- Account
- Opening Amount
- Date
- Approval Reference
- Finalized By

---

# 72. Opening Share Report

Admin shall be able to view/print Opening Shareholder holdings.

---

# 73. Legacy Boundary Disclosure

Reports covering the new system may display:

`New system financial records begin from approved opening date: ______`

where useful.

---

# 74. Legacy Report Separation

Legacy Access reports shall not be combined with live reports in a way that creates false continuous balances.

If historical data is displayed, it shall be clearly labeled.

---

# 75. Dashboard Reporting Rule

Dashboard numbers are report summaries.

They shall use the same business logic and source data as full reports.

---

# 76. Dashboard vs Report Reconciliation

Example:

Admin Dashboard:

Today's Service Charge = `NPR 5,000`

Daily Service Charge Report:

Must equal `NPR 5,000`

If not, the system has a reporting defect.

---

# 77. Report Filters

Common filters may include:

- Start Date
- End Date
- Customer
- Provider
- Account
- Transaction Type
- Staff
- Expense Category
- Shareholder
- Status

Only relevant filters should appear for each report.

---

# 78. Default Report Period

Operational reports may default to:

**Today**

Monthly reports may default to:

**Current Month**

The user may change the period according to permission.

---

# 79. Financial Year Report

If Nepali Financial Year is approved, reports shall support consistent Financial Year filtering.

No report shall calculate Financial Year using inconsistent ad-hoc logic.

---

# 80. Search by Reference

Transaction reports should support searching by:

- Internal Transaction Number
- External Reference Number

where applicable.

---

# 81. Internal vs External Reference

Internal Transaction Number and Provider/Bank Reference Number shall remain separate.

---

# 82. Report Pagination

Large reports shall use efficient pagination or equivalent server-side processing.

The application shall not load years of transaction data into a browser unnecessarily.

---

# 83. Report Totals with Pagination

Report grand totals shall represent the entire selected filtered dataset, not only the visible page, where the UI claims to show overall totals.

---

# 84. Print Report

Authorized reports may support printing.

Printed output shall preserve:

- Report Title
- Date Range
- Filters
- Data
- Totals
- Generated Date where appropriate

---

# 85. PDF Export

PDF exports may be provided for approved reports.

Export shall use the same filter and permission rules as screen reporting.

---

# 86. Spreadsheet Export

Spreadsheet export may be provided to Admin for approved reports.

Exported values shall not contain hidden unauthorized data.

---

# 87. Shareholder Export

Shareholder may export only own authorized Share Statement if this feature is approved.

---

# 88. Staff Export

Staff export shall be limited to permitted operational information.

Staff shall not export company-wide sensitive data without authorization.

---

# 89. Attachment Link in Reports

Reports may indicate that an Attachment exists.

Sensitive attachment download shall still require authorization.

---

# 90. Note Display

Long Notes should not overload summary reports.

A report may show:

- Short Note
- View Detail

where appropriate.

Full Note remains available in transaction detail.

---

# 91. Financial Precision

All financial reports shall use the same approved monetary precision as source data.

---

# 92. Currency

Initial report currency:

**NPR**

unless another rule is approved.

---

# 93. Share Quantity Precision

Share quantities shall display as whole units under the current rule.

---

# 94. Historical Share Value

Historical Share reports shall display the applicable historical Share Value stored with the transaction.

They shall not substitute a newly changed current Share Value.

---

# 95. Reconciliation Frequency

Recommended operational reconciliation:

### Daily
- Physical Cash
- Important transaction totals
- Service Charge

### Periodic / Month-End
- Bank
- BLB
- Provider Accounts
- Commission
- Expense
- Profit/Loss

Exact operational frequency may be adjusted by the business.

---

# 96. Reconciliation Evidence

Reconciliation may support:

- Note
- Attachment
- Statement image/PDF
- User
- Date

---

# 97. Reconciliation Difference Investigation

Differences shall be investigated using:

- Account Ledger
- Daily Transactions
- Service Charge Report
- Expense Report
- Account Transfer Report
- Cancelled Transaction Report
- Staff Activity Report

---

# 98. No Manual Report Override

Users shall not manually overwrite calculated report totals.

If the report is wrong, source data or reporting logic shall be corrected.

---

# 99. No Report-Only Adjustment

The system shall not support a value that changes a report total without creating an approved underlying financial/business record.

---

# 100. Report Auditability

An Admin reviewing a total should be able to drill down to the transactions producing that total where practical.

---

# 101. Profit/Loss Drill-Down

Example:

Service Charge Income = `NPR 50,000`

Admin should be able to view the source transactions contributing to that total.

---

# 102. Expense Drill-Down

Total Expense should be traceable to individual Expense transactions.

---

# 103. Commission Drill-Down

Total Commission shall be traceable to individual Provider Commission records.

---

# 104. Account Balance Drill-Down

Current Account Balance shall be traceable through Account Ledger.

---

# 105. Share Balance Drill-Down

Current Share Quantity shall be traceable through Share Ledger.

---

# 106. Report Access Logging

Sensitive report export/download actions may be audited where appropriate.

This is especially useful for:

- Shareholder data
- Financial reports
- Large exports

---

# 107. Report Security

Report endpoints shall enforce authorization server-side.

Changing URL parameters shall not expose restricted data.

---

# 108. Shareholder Privacy in Reports

Shareholder A shall not obtain Shareholder B's report by manipulating:

- URL
- Export parameter
- Record ID

---

# 109. Staff Privacy in Reports

Staff report permissions shall not automatically expose:

- Full Profit/Loss
- Shareholder holdings
- Complete business account balances

---

# 110. Report Naming

Reports shall use clear business names.

Examples:

- Daily Transaction Report
- Account Ledger
- Service Charge Report
- Provider Commission Report
- Expense Report
- Profit & Loss Report
- Shareholder Statement

Avoid unclear legacy labels.

---

# 111. Report Version Consistency

A report generated before and after a software update should not change historical results unless:

- Source data changed through approved correction, or
- Approved business logic changed with documented historical effect

Silent historical report changes are prohibited.

---

# 112. Monthly Lock and Reports

If formal month closing is later implemented, closed-month reports shall be protected from casual backdated changes.

---

# 113. Report Status After Cancellation

If a transaction is cancelled after an earlier report was printed, new reports shall reflect the cancellation.

The historical printed report itself remains external evidence of what was printed at that time.

---

# 114. Snapshot Reports

If the business later requires immutable period snapshots, this shall be a separately approved feature.

Version 1 may calculate reports dynamically from source data.

---

# 115. Report Performance

Reporting queries shall use appropriate:

- Database indexes
- Filters
- Pagination
- Aggregation

to remain usable as data grows.

---

# 116. Large Export

Very large exports may be generated asynchronously in the future if required.

This is not necessary for initial low-volume operation unless performance requires it.

---

# 117. Reconciliation Adjustment Link

If a reconciliation difference results in an authorized Adjustment, the reconciliation record should link to the resulting Adjustment transaction where implemented.

---

# 118. Report Testing

Every financial report shall be tested against known transactions.

Example dataset:

- Remittance
- Deposit
- Withdrawal
- Service Charge
- Commission
- Expense
- Transfer
- Settlement
- Cancellation

Expected totals must be known before testing.

---

# 119. Account Report Test

Starting Cash:

`NPR 100,000`

Remittance Cash Receipt:

`+10,100`

Expense:

`-5,000`

Expected Cash:

`NPR 105,100`

Account Balance Report and Ledger must both show:

`NPR 105,100`

---

# 120. Service Charge Test

Three active Service Charges:

`100 + 150 + 50 = 300`

Cancel the `150` transaction.

Expected Service Charge Report:

`NPR 150`

---

# 121. Profit/Loss Test

Service Charge:

`NPR 10,000`

Commission:

`NPR 5,000`

Expenses:

`NPR 8,000`

Expected Profit:

`NPR 7,000`

---

# 122. Share Report Test

Opening:

A = `500`

Transfer A → B:

`100`

Expected:

A = `400`

B increases by `100`

Shareholder report and Share Ledger must agree.

---

# 123. Cancelled Share Transfer Test

Cancel the transfer.

Expected:

A returns to `500`

B loses the transferred `100`

Reports must update accordingly.

---

# 124. Reconciliation Testing

Testing shall verify that recording a physical difference does not silently alter the Financial Account until an authorized adjustment is processed.

---

# 125. Export Testing

Verify PDF/Spreadsheet totals equal on-screen report totals for the same filters.

---

# 126. Permission Testing

Each report shall be tested as:

- Admin
- Staff
- Shareholder
- Unauthenticated user

---

# 127. Unresolved Reporting Decisions

Before final UI development, confirm:

1. Exact Staff report visibility.
2. Whether Staff sees selected Account Balance.
3. Whether Shareholder sees Profit/Loss summary.
4. Whether Shareholder Statement download is required.
5. Exact Nepali Financial Year filters.
6. Exact PDF reports required.
7. Exact Spreadsheet exports required.
8. Whether formal Cash Reconciliation screen is Version 1.
9. Whether Bank/Provider Reconciliation screens are Version 1.
10. Whether immutable month-end snapshot reports are required.

These shall not be guessed.

---

# 128. Acceptance Rule

Reporting is acceptable only when:

- Dashboard totals match reports.
- Account Balance matches Ledger.
- Service Charge totals match source transactions.
- Commission totals match Commission records.
- Expense totals match Expense records.
- Profit/Loss excludes principal and transfers.
- Cancelled transactions are excluded from active totals.
- Negative balances display correctly.
- Shareholder current quantity matches Share Ledger.
- Shareholders see only own permitted information.
- Reports respect role permissions.
- Exported reports match filtered on-screen values.
- Reconciliation differences do not silently change balances.

---

# 129. Development Rule

No developer or AI coding agent may create separate financial formulas only for reports.

All reports shall use approved source transactions and financial logic.

If report behavior is unclear:

**READ BUSINESS CONSTITUTION  
→ READ FINANCIAL STANDARD  
→ VERIFY SOURCE DATA  
→ ASK BUSINESS OWNER  
→ DOCUMENT  
→ IMPLEMENT**

---

# 130. Final Reporting Principle

The reporting system shall answer three core questions clearly:

**1. WHAT HAPPENED?**  
Transactions and activity.

**2. WHERE IS THE MONEY?**  
Cash, Bank, BLB and Remittance Account balances.

**3. WHAT DID THE BUSINESS EARN OR SPEND?**  
Service Charge, Commission, Expense and Profit/Loss.

Shareholder reporting shall separately answer:

**WHO OWNS HOW MANY SHARES?**

---

# 131. Document Authority

This document is the authoritative Reporting and Reconciliation Standard for the Remittance Management System.

It does not override the Business Constitution.

Highest business authority remains:

`01_REMITTANCE_BUSINESS_CONSTITUTION.md`

---

# 132. Approval

**Status:** FINAL DRAFT — Pending Business Owner Approval

After approval, this document becomes the official Reporting and Reconciliation Standard for design, development, testing, operation and future maintenance of the Remittance Management System.

---

**END OF DOCUMENT**