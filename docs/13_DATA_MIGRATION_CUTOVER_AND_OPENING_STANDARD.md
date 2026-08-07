# REMITTANCE MANAGEMENT SYSTEM — DATA MIGRATION, CUTOVER AND OPENING STANDARD

**Document ID:** 13  
**File Name:** `13_DATA_MIGRATION_CUTOVER_AND_OPENING_STANDARD.md`  
**Project:** Remittance Management System  
**Document Type:** Legacy Migration, Cutover and Opening Standard  
**Version:** 1.0  
**Status:** FINAL DRAFT — Pending Business Owner Approval  
**Authority:** Business Owner  

---

# 1. Purpose

This document defines how the existing Microsoft Access system shall be closed and how the new Remittance Management System shall begin.

It governs:

- Legacy system closing
- Legacy backup
- Customer migration
- Old transaction handling
- Opening Balance preparation
- Opening Shareholder position
- Cut-off date
- Partner/Shareholder approval
- Data verification
- Go-Live transition
- Legacy archive
- Reconciliation
- Migration audit
- Rollback

This document prevents incomplete historical accounting from corrupting the new system.

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

---

# 3. Core Migration Principle

The new system shall **not** attempt to rebuild the full current financial position from incomplete historical Microsoft Access transactions.

The approved transition shall be:

**OLD SYSTEM CLOSE  
→ BUSINESS/PARTNER VERIFICATION  
→ APPROVED OPENING POSITION  
→ NEW SYSTEM START**

---

# 4. Legacy System Status

The Microsoft Access application shall be classified as:

**Legacy System**

It remains historical evidence for transactions entered before the approved cut-off date.

It shall not remain the primary live accounting system after Go-Live.

---

# 5. Why Full Financial Migration Is Not Required

The legacy system did not contain complete account-level financial history for all business operations.

For example, some BLB principal transactions were historically maintained outside the software while Service Charge/Commission information was entered into the Access system.

Therefore, blindly importing old transactions into the new ledger could create incorrect:

- Cash Balance
- BLB Balance
- Bank Balance
- Remittance Balance
- Profit/Loss
- Opening Position

Full historical financial reconstruction is outside the approved initial migration strategy.

---

# 6. Cut-Off Date

The business shall approve one official **Cut-Off Date**.

The Cut-Off Date represents:

- Last official operating date of the Legacy System
- Closing date of the old financial position
- Boundary between old and new systems

Example:

`Legacy System valid through: __________`

`New System starts from: __________`

---

# 7. No Overlapping Live Operation

After the official New System start date, normal transactions shall not be entered into both:

- Microsoft Access
- New Online System

as parallel official records.

Parallel live financial entry creates reconciliation risk.

---

# 8. Closing Meeting

Before new-system opening, Partners/Shareholders shall hold a review/closing meeting.

The meeting should verify the actual current business position.

---

# 9. Closing Meeting Minimum Review

The meeting should review, where applicable:

- Physical Cash
- Business Bank Account
- BLB
- Citizen Remit
- City Express
- IME Remit
- IME Pay
- IPS
- Other financial/service accounts
- Outstanding negative balances
- Outstanding positive balances
- Commission position
- Important unpaid Expenses/Liabilities if required
- Current Shareholders
- Current Share Quantity
- Current Share Value
- Pending Share Transfers
- Pending Shareholder Exits

---

# 10. Opening Approval Authority

The new system shall use the position approved by the Partners/Shareholders/Business Owner.

Legacy transaction totals shall not override the approved verified opening position where legacy accounting is incomplete.

---

# 11. Opening Balance Principle

Each approved financial account shall receive an Opening Balance.

Opening Balance may be:

- Positive
- Zero
- Negative

Negative balances are valid for approved operational/remittance accounts.

---

# 12. Opening Balance Example

Approved position:

| Account | Opening Balance |
|---|---:|
| Cash | NPR 250,000 |
| Asha Enterprises Bank | NPR 800,000 |
| City Express | NPR -50,000 |
| Citizen Remit | NPR 30,000 |
| BLB | NPR -20,000 |

These approved figures become the starting financial position.

---

# 13. Opening Balance Is Not Income

Opening Balance shall not be counted as:

- Service Charge
- Commission
- Income
- Profit

It is the approved starting financial position.

---

# 14. Opening Balance Is Not Current-Period Transaction Income

The new system's Profit/Loss shall start from new-system operational transactions after opening.

Opening amounts shall not artificially create new-period Profit.

---

# 15. Opening Balance Evidence

Opening Balance records should support evidence such as:

- Signed meeting minute
- Bank statement
- Physical Cash verification
- Provider statement
- Settlement sheet
- Other approved supporting documents

---

# 16. Opening Batch

The system should support an identifiable Opening Batch.

The Opening Batch may contain:

- Opening Effective Date
- Approval Reference
- Meeting Reference
- Note
- Attachment
- Individual Account Opening records
- Created By
- Finalized By
- Finalized At

---

# 17. Opening Balance Finalization

Before finalization, Admin shall be able to review all Opening Balances.

After approval:

**Opening Batch → Finalized**

Ordinary editing shall then be restricted.

---

# 18. Opening Correction

If an approved Opening Balance is discovered to be incorrect after Go-Live, it shall not be silently overwritten.

Correction shall use an authorized controlled process.

The correction shall preserve:

- Original Opening
- Correction Reason
- Supporting evidence
- Authorized user
- Date/time
- Resulting corrected position

---

# 19. Customer Migration Principle

Existing Customer Master data may be migrated from Microsoft Access.

Customer migration is separate from financial migration.

---

# 20. Customer Has No Opening Balance

Imported Customers shall not receive:

- Financial Balance
- Wallet Balance
- Receivable
- Payable
- Share Balance

Customer is only transaction identity/reference.

---

# 21. Customer Export

Legacy Customer data should first be exported into a manageable format.

Possible formats:

- CSV
- Excel
- Other approved structured format

---

# 22. Customer Migration Staging

Customer data should pass through a staging/cleaning process before entering the live Customer Master.

Recommended process:

1. Export
2. Clean
3. Normalize
4. Detect duplicates
5. Review invalid rows
6. Approve
7. Import
8. Verify

---

# 23. Customer Cleaning

Cleaning may include:

- Removing blank records
- Correcting obvious spacing issues
- Normalizing mobile numbers
- Normalizing repeated names where appropriate
- Removing obvious duplicates after review

The system shall not merge uncertain customers automatically.

---

# 24. Duplicate Customer Rule

Potential duplicates should be flagged.

Example indicators:

- Same Name + Same Mobile
- Same Mobile
- Same known identity reference

Different persons shall not be merged merely because names are similar.

---

# 25. Legacy Customer ID

The new system may preserve the old Access Customer ID as a legacy reference.

Example:

`legacy_customer_id`

This is optional and shall not become the new primary key.

---

# 26. Customer Import Audit

Customer import should record:

- Source File
- Import Date
- Imported By
- Total Rows
- Imported Rows
- Rejected Rows
- Duplicate Rows
- Error Rows

---

# 27. Customer Import Failure

If a migration batch fails, the process should avoid leaving a partially trusted dataset without clear status.

Import should support controlled retry/rollback where practical.

---

# 28. Legacy Financial Transactions

Historical Access financial transactions shall **not** be inserted into the live financial ledger under the initial approved strategy.

This includes old Day Book records where the accounting effects cannot be fully reconstructed.

---

# 29. Legacy Day Book

Legacy Day Book may remain available through:

- Original Access application
- Exported reports
- Future read-only Legacy Viewer

It shall not alter the new-system account balances.

---

# 30. Legacy Viewer

If a Legacy Viewer is implemented later, it must be clearly labeled:

**LEGACY / HISTORICAL — READ ONLY**

It shall not provide:

- Edit
- Cancel
- Account posting
- Live ledger effect

---

# 31. Legacy Transaction Search

Legacy data may support historical search by:

- Date
- Customer
- Provider/Method
- Amount
- Service Charge/Commission
- Reference

This remains reference-only.

---

# 32. Legacy Profit/Loss

Old monthly values such as:

- Old Month Saving
- Total Money Saving
- Legacy Profit/Loss

shall not automatically become new-system live financial transactions.

---

# 33. Legacy Initial Capital

Historical starting investment such as:

`NPR 2,100,000`

may remain part of historical business records.

The current new-system capital/share position shall be based on the approved opening meeting.

---

# 34. Shareholder Opening Migration

Shareholder opening position shall not be reconstructed solely from old historical transaction tables.

The Partners/Shareholders shall approve the current position.

---

# 35. Opening Shareholder Data

For each existing Shareholder confirm:

- Shareholder Name
- Current Share Quantity
- Value Per Share
- Total Valuation
- Status
- Effective Date
- Approval Reference
- Note
- Attachment

---

# 36. Opening Share Value

Current approved baseline:

**1 Share Unit = NPR 1,000**

Example:

500 Shares:

`NPR 500,000`

---

# 37. Opening Shareholding Is Not Cash Entry

Opening Shareholding itself shall not automatically change Cash or Bank.

The approved Cash/Bank balances are separately entered through Opening Balance.

This prevents double-counting of historical capital.

---

# 38. Historical Share Transactions

Old Share purchases/transfers/withdrawals do not need to be recreated individually in the new system unless specifically approved.

The approved current holding becomes the starting Share Ledger position.

---

# 39. Pending Share Transaction

Any Share Transfer/Exit pending at Cut-Off shall be resolved before Opening where possible.

If it remains pending, it shall be documented clearly and handled through an approved new-system transaction after Go-Live.

---

# 40. BLB Verification

Because legacy BLB principal accounting was historically maintained outside the old system, BLB Opening Balance must be independently verified.

Acceptable sources may include:

- Manual ledger/copy
- Provider balance
- Bank/provider statement
- Partner-approved reconciliation

---

# 41. Remittance Account Verification

Each Remittance Account shall be independently verified.

Examples:

- City Express
- Citizen Remit
- IME Remit
- IME Pay

The opening amount shall not be guessed from incomplete Day Book records.

---

# 42. Cash Verification

Cash Opening Balance should be based on actual physical Cash count/reconciliation as of the approved cut-off.

---

# 43. Bank Verification

Bank Opening Balance should be based on the relevant bank statement/account position as of the cut-off date.

---

# 44. Negative Provider Balance

If a Provider Account is negative at Cut-Off:

Example:

City Express:

`NPR -75,000`

the Opening Balance shall preserve:

`NPR -75,000`

The system shall not force it to zero merely because the new system is starting.

---

# 45. Opening Reconciliation Sheet

A formal Opening Reconciliation Sheet should be prepared.

Suggested columns:

| Account | Verified Balance | Source | Approved By | Note |
|---|---:|---|---|---|

---

# 46. Shareholder Opening Sheet

Suggested columns:

| Shareholder | Shares | Value/Share | Valuation | Status |
|---|---:|---:|---:|---|

---

# 47. Partner Meeting Minute

The meeting minute should state:

- Legacy system closing date
- New system opening date
- Approved account balances
- Approved shareholder holdings
- Approved Share Value
- Treatment of old records
- Authorized person for Opening entry
- Partner approval/signatures

Exact legal format may be reviewed separately if required.

---

# 48. Legacy Backup Before Cutover

Before Legacy System is frozen, create verified backup copies of:

- Access database
- Related files
- Important exported reports
- Relevant old documents

---

# 49. Multiple Legacy Backup Copies

At least two independent copies are recommended.

Example:

- Local secure backup
- External/off-device backup

---

# 50. Legacy Backup Naming

Backup names should clearly identify:

- System
- Date
- Final/Pre-Cutover status

Example:

`remittance_access_final_2026-08-xx.accdb`

---

# 51. Legacy Freeze

After final backup and approved Cut-Off, the old Access system should be treated as read-only operationally.

New official transactions go into the new system.

---

# 52. New Production Setup

Before entering Opening data, configure:

- Business information
- Admin User
- Staff Users
- Shareholder Users
- Account Categories
- Accounts
- Providers
- Expense Categories
- Required Settings

---

# 53. Account Mapping

Each approved opening figure shall map to one new Financial Account.

Example:

`Physical Cash → Cash Account`

`Asha Enterprises Bank → Bank Account`

`City Express → City Express Account`

`BLB → BLB Account`

---

# 54. No Ambiguous Mapping

If an old account cannot be clearly mapped, it shall be resolved before Opening.

No placeholder financial account such as:

`Other Unknown Balance`

should be used without explicit business approval.

---

# 55. Customer Import Timing

Recommended sequence:

Customer import may occur before Go-Live after Customer Master is stable.

However, no imported Customer shall create live financial effects.

---

# 56. Opening Entry Timing

Opening financial entries should occur near the official Go-Live date after balances are verified.

---

# 57. Cutover Sequence

Recommended final sequence:

1. Stop Legacy live entry.
2. Create final Legacy backup.
3. Verify physical Cash.
4. Verify Bank.
5. Verify BLB.
6. Verify Remittance Accounts.
7. Verify other accounts.
8. Verify Shareholders.
9. Approve Share Value.
10. Conduct Partner/Shareholder meeting.
11. Approve Cut-Off.
12. Import cleaned Customers.
13. Configure new Financial Accounts.
14. Enter Opening Balances.
15. Enter Opening Share Holdings.
16. Review.
17. Attach approval evidence.
18. Finalize Opening.
19. Activate new system.
20. Begin new live transactions.

---

# 58. Go-Live Time Boundary

The business shall define a clear boundary such as:

`All transactions up to ______ belong to Legacy.`

`All transactions after ______ belong to New System.`

This prevents duplicate or missing transactions.

---

# 59. Transactions During Cutover

If transactions occur while the old system is frozen and the new system is not yet live, they shall be recorded using an approved temporary cutover sheet.

After Go-Live, they shall be entered once into the new system with correct Business Date.

---

# 60. Cutover Temporary Sheet

The temporary sheet should include:

- Date/Time
- Customer
- Type
- Provider
- Principal
- Service Charge
- Reference
- Staff
- Note

---

# 61. Duplicate Cutover Prevention

Before entering temporary transactions into the new system, verify that they were not already posted.

---

# 62. Go-Live Verification

Immediately after Opening finalization verify:

- Cash Opening
- Bank Opening
- BLB Opening
- Remittance Openings
- Other Accounts
- Shareholder Holdings
- User Roles

---

# 63. First Live Transaction

The first live transaction should be carefully verified end-to-end.

For example:

- Customer
- Principal
- Service Charge
- Cash effect
- Provider effect
- Ledger
- Report
- Created By

---

# 64. First-Day Reconciliation

At end of first operating day compare:

- Physical Cash
- Bank movements if applicable
- Provider balances
- System ledger
- Service Charges

Any discrepancy should be investigated immediately.

---

# 65. First-Week Reconciliation

For the first week, review daily:

- Cash
- Negative Accounts
- Staff entries
- Service Charges
- Cancellations
- Account Ledgers

---

# 66. Migration Defect

A Migration Defect occurs where approved source Customer/opening data was transferred incorrectly.

Example:

Approved Bank Opening:

`NPR 800,000`

System Opening:

`NPR 80,000`

This requires controlled correction.

---

# 67. Legacy Data Difference Is Not Automatically a Migration Defect

If the new Opening differs from a number shown in the old Access system because the Partners approved a reconciled actual balance, that difference is not automatically a migration error.

The approved Opening position is authoritative.

---

# 68. Opening Approval Log

The system should record:

- Who entered Opening
- Who finalized Opening
- Date/time
- Approval Reference

---

# 69. Opening Attachments

Supporting documents should remain linked permanently to the Opening Batch/records.

---

# 70. No Opening Hard Delete

Finalized Opening records shall not be hard-deleted through normal UI.

---

# 71. No Silent Legacy Import

No developer or AI coding agent shall import old financial records into live ledger tables without explicit Business Owner approval.

---

# 72. Legacy Database Modification

The final archived Legacy database should not be modified casually after Cut-Off.

If a copy is needed for research/testing, preserve the original final backup unchanged.

---

# 73. Original Archive Principle

At least one Legacy backup shall be considered the:

**Original Final Archive**

It should remain unmodified.

---

# 74. Legacy Read Access

Authorized persons may use Legacy data for:

- Historical transaction lookup
- Audit/reference
- Customer history reference
- Old report verification

It shall not be used to post new live entries.

---

# 75. Migration Security

Legacy files may contain confidential customer/business information.

They shall not be:

- Publicly uploaded
- Committed to public Git repositories
- Shared unnecessarily

---

# 76. Import Data Security

Customer migration files shall be stored securely during the migration process.

Temporary files should be removed/protected after completion.

---

# 77. Migration Test Environment

Customer import and Opening procedures should be tested first in a safe development/test environment.

---

# 78. Trial Customer Import

Before importing all customers, test a sample batch.

Verify:

- Names
- Mobile
- Duplicate detection
- Search
- Character encoding
- No ledger effect

---

# 79. Trial Opening

Before production Opening, a trial Opening may be performed using sample data.

Verify:

- Positive balance
- Negative balance
- Account Ledger
- Current Balance
- Share Opening
- Reports

---

# 80. Production Reset

Trial financial data shall not remain in official production history.

Production begins with clean approved data.

---

# 81. Import Character Encoding

Nepali/English names and notes shall be preserved correctly.

Migration shall avoid garbled characters.

---

# 82. Empty Legacy Fields

Empty old fields shall not be filled with invented values.

If data is unavailable:

- Leave optional field blank, or
- Use an approved explicit value

depending on field requirements.

---

# 83. Invalid Legacy Data

Invalid Customer records may be:

- Rejected
- Flagged for review
- Cleaned manually

They shall not be silently guessed.

---

# 84. Legacy Attachments

If important historical attachments exist, they may remain with the Legacy archive unless separately approved for migration.

---

# 85. Current-System Attachment Migration

Only attachments required for the approved new Opening/Customer data need to be imported initially.

---

# 86. Migration Completion Report

After migration, prepare a summary such as:

- Customers exported
- Customers imported
- Duplicates excluded
- Invalid records excluded
- Opening Accounts created
- Opening Balances entered
- Shareholders opened
- Legacy backup completed
- Cut-Off completed

---

# 87. Migration Approval

The Business Owner/Admin should review the migration summary before final Go-Live.

---

# 88. Rollback Before Go-Live

If serious setup problems are detected before official Go-Live, the new production setup may be reset/rebuilt from approved Opening data.

Legacy remains the historical source up to Cut-Off.

---

# 89. Rollback After Go-Live

Once new live transactions have started, rollback must not simply erase them.

Any recovery must preserve new live financial history.

---

# 90. Post-Go-Live Legacy Correction

Discovering an error in old Legacy history after Go-Live does not automatically require changing the new Opening.

Opening changes require explicit business approval and reconciliation.

---

# 91. Opening Adjustment After Go-Live

If Partners formally determine the Opening was wrong, an authorized Opening Correction/Adjustment shall be recorded with:

- Decision
- Reason
- Amount
- Account
- Evidence
- Approval

---

# 92. Share Opening Correction

Likewise, if approved Share opening quantity was incorrect, correction shall preserve:

- Original quantity
- Adjustment
- Reason
- Approval
- Resulting quantity

---

# 93. No Double Capital Recognition

If Opening Cash/Bank already includes historic shareholder capital, the system shall not separately add the same historic capital again into financial accounts.

Opening balances and Opening Shareholdings represent different views of the approved starting position.

---

# 94. No Double Commission Recognition

Legacy Commission already reflected in the approved Opening financial position shall not be re-entered as new current-period Commission Income.

---

# 95. No Double Expense Recognition

Legacy Expenses already reflected in the closing position shall not be entered again as current-period Expenses merely for historical completeness.

---

# 96. New Period Boundary

Income and Expense reporting for the new system shall start from new-system approved live transactions after the Opening boundary.

---

# 97. Report Labeling

Initial reports may display a note such as:

`System financial records begin from approved opening date: ______`

This clarifies the reporting boundary.

---

# 98. Customer History Boundary

Customer history in the new system may begin from Go-Live unless Legacy Viewer is implemented.

Old Access history remains separately available.

---

# 99. Legal/Business Record Principle

This document defines software migration behavior.

Where formal accounting/legal requirements apply, the business should preserve required source documents and approved records accordingly.

---

# 100. Developer Rule

No developer or AI coding agent shall:

- Infer live Opening balances from incomplete historical Day Book alone.
- Reconstruct missing BLB history by guess.
- Create Customer balances.
- Import old Profit/Loss as new income.
- Import historical Share activity without approval.
- Mix Legacy records into the live ledger.

---

# 101. Migration Development Workflow

Implementation shall follow:

**LEGACY REVIEW  
→ EXPORT  
→ CLEAN  
→ MAP  
→ TEST IMPORT  
→ BUSINESS VERIFY  
→ PRODUCTION IMPORT  
→ OPENING ENTRY  
→ FINAL REVIEW  
→ GO-LIVE**

---

# 102. Blocking Cutover Decisions

Before Go-Live, the following must be final:

1. Cut-Off Date
2. New System Start Date
3. Cash Opening
4. Bank Opening
5. BLB Opening
6. Remittance Account Openings
7. Other Account Openings
8. Current Shareholder list
9. Opening Share Quantity
10. Share Value
11. Customer migration file
12. Authorized Opening Admin
13. Signed/approved meeting record

---

# 103. Cutover Acceptance Checklist

- [ ] Final Access backup created
- [ ] Backup verified
- [ ] Legacy system frozen
- [ ] Cut-Off Date approved
- [ ] Cash verified
- [ ] Bank verified
- [ ] BLB verified
- [ ] Remittance accounts verified
- [ ] Negative balances verified
- [ ] Shareholders verified
- [ ] Share quantities verified
- [ ] Share Value verified
- [ ] Customer data cleaned
- [ ] Customer duplicates reviewed
- [ ] Customer import tested
- [ ] Customer import completed
- [ ] Accounts created
- [ ] Providers created
- [ ] Opening Balances entered
- [ ] Opening Share Holdings entered
- [ ] Opening evidence attached
- [ ] Opening reviewed
- [ ] Opening finalized
- [ ] Admin permission tested
- [ ] Staff permission tested
- [ ] Shareholder permission tested
- [ ] Backup of new Opening database created
- [ ] First live transaction verified

---

# 104. Acceptance Rule

Migration and Cutover are acceptable only when:

- Old financial history remains safely archived.
- Customers are migrated without balances.
- New Account Opening figures are approved.
- Negative balances are preserved correctly.
- Shareholder Opening Holdings are approved.
- No historical income/expense is double-counted.
- Legacy transactions do not contaminate live ledger.
- Supporting meeting/approval evidence is preserved.
- New-system transactions begin from a clear cut-off boundary.

---

# 105. Final Migration Principle

The approved migration philosophy is:

**DO NOT REBUILD AN UNCERTAIN PAST INTO THE NEW LEDGER.**

Instead:

**PRESERVE THE OLD SYSTEM  
→ VERIFY THE REAL CURRENT POSITION  
→ APPROVE THE OPENING  
→ START THE NEW SYSTEM CLEANLY**

---

# 106. Document Authority

This document is the authoritative standard for:

- Legacy Migration
- Cutover
- Opening Balance Transition
- Opening Shareholder Transition
- Legacy Archive

It does not override the Business Constitution.

Highest authority remains:

`01_REMITTANCE_BUSINESS_CONSTITUTION.md`

---

# 107. Approval

**Status:** FINAL DRAFT — Pending Business Owner Approval

After approval, this document becomes the official Data Migration, Cutover and Opening Standard for the Remittance Management System.

---

**END OF DOCUMENT**