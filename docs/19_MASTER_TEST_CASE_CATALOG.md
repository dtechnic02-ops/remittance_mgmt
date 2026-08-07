# REMITTANCE MANAGEMENT SYSTEM — MASTER TEST CASE CATALOG

**Document ID:** 19  
**File Name:** `19_MASTER_TEST_CASE_CATALOG.md`  
**Project:** Remittance Management System  
**Document Type:** Functional, Financial, Permission and Regression Test Catalog  
**Version:** 1.0  
**Status:** FINAL DRAFT — Pending Business Owner Approval  
**Authority:** Business Owner  

---

# 1. Purpose

This document defines the master test cases that shall be used to verify the Remittance Management System before production Go-Live and after major future changes.

It provides repeatable tests for:

- Authentication
- Roles and Permissions
- Customer Management
- Account Master
- Opening Balance
- Remittance
- Deposit
- Withdrawal
- Service Charge
- Account Transfer
- Negative Balance
- Month-End Settlement
- Provider Commission
- Expense
- Profit/Loss
- Shareholder
- Share Transfer
- Share Redemption
- Cancellation
- Correction
- Notes
- Attachments
- Reports
- Ledger
- Audit
- Migration
- Security
- Backup
- Go-Live

The system shall not be considered financially correct merely because forms save successfully.

---

# 2. Governing Documents

Testing shall follow:

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
16. `15_SYSTEM_ARCHITECTURE_AND_MODULE_STANDARD.md`
17. `16_DEVELOPER_AND_AI_EXECUTION_STANDARD.md`
18. `17_MASTER_DATA_AND_SYSTEM_CONFIGURATION_STANDARD.md`
19. `18_USER_ACCEPTANCE_GO_LIVE_AND_HANDOVER_STANDARD.md`

The Business Constitution remains the highest authority.

---

# 3. Test Case Status

Each test case shall use one of the following statuses:

- **NOT TESTED**
- **PASS**
- **FAIL**
- **BLOCKED**
- **NOT APPLICABLE**

---

# 4. Test Severity

Test failures shall be classified as:

### Critical

Failure may cause:

- Wrong financial balance
- Lost transaction
- Duplicate financial posting
- Unauthorized financial access
- Wrong Share ownership
- Data corruption

### High

Core workflow does not operate correctly.

### Medium

Important reporting or usability problem.

### Low

Minor display or convenience issue.

Critical failures block production.

---

# 5. Standard Test Record

Every executed test should record:

- Test Case ID
- Module
- Test Date
- Environment
- User Role
- Starting Data
- Action
- Expected Result
- Actual Result
- Status
- Tested By
- Evidence/Screenshot where required
- Defect Reference if failed

---

# 6. Authentication Tests

## TC-AUTH-001 — Admin Login

**Role:** Admin

### Action

Login using valid Admin credentials.

### Expected

- Login succeeds.
- Admin Dashboard opens.
- Admin permissions available.

---

## TC-AUTH-002 — Staff Login

**Role:** Staff

### Expected

- Login succeeds.
- Staff Dashboard opens.
- Admin menu is not available.

---

## TC-AUTH-003 — Shareholder Login

**Role:** Shareholder

### Expected

- Login succeeds.
- Shareholder Dashboard opens.
- Only own permitted Share information is visible.

---

## TC-AUTH-004 — Invalid Password

Attempt login using invalid password.

### Expected

- Login fails.
- Protected screens remain inaccessible.
- Existing password is not revealed.

---

## TC-AUTH-005 — Inactive User

Deactivate user and attempt login.

### Expected

**Access Denied**

Historical transactions remain linked to that user.

---

# 7. Role Permission Tests

## TC-ROLE-001 — Staff Opens Admin Account Master

### Action

Staff manually opens Admin Account Master URL.

### Expected

**Access Denied**

---

## TC-ROLE-002 — Staff Opens Opening Balance

### Expected

**Access Denied**

---

## TC-ROLE-003 — Staff Opens Shareholder Management

### Expected

**Access Denied**

---

## TC-ROLE-004 — Shareholder Opens Daily Transactions

### Expected

**Access Denied**

---

## TC-ROLE-005 — Shareholder Accesses Another Shareholder

Shareholder A attempts to open Shareholder B using modified record ID.

### Expected

**Access Denied**

This is a Critical security test.

---

# 8. Customer Tests

## TC-CUS-001 — Create Customer

Create:

**Name:** Test Customer A

### Expected

Customer created successfully.

No financial Account movement.

---

## TC-CUS-002 — Customer Has No Balance

Open Customer Detail.

### Expected

No:

- Wallet Balance
- Customer Balance
- Receivable Balance
- Payable Balance

exists.

---

## TC-CUS-003 — Search Customer

Search by:

- Name
- Mobile

### Expected

Correct Customer appears.

---

## TC-CUS-004 — Duplicate Customer Warning

Attempt to create a Customer using same known Name/Mobile.

### Expected

System applies approved duplicate-warning behavior.

It shall not merge unrelated Customers automatically.

---

# 9. Account Master Tests

## TC-ACC-001 — Create Financial Account

Admin creates:

`Test Provider Account`

### Expected

Account created successfully.

---

## TC-ACC-002 — Staff Creates Account

Staff attempts to create Account.

### Expected

**Access Denied**

---

## TC-ACC-003 — Inactive Account

Deactivate Account.

### Expected

- Existing history remains.
- Account unavailable in new operational transaction selector.

---

## TC-ACC-004 — Account With History Delete

Attempt ordinary hard-delete of Account with Ledger history.

### Expected

System prevents unsafe deletion.

---

# 10. Provider Tests

## TC-PROV-001 — Add New Provider

Create:

`Test Remit`

Link to:

`Test Remit Account`

### Expected

Provider becomes usable without database schema modification.

---

## TC-PROV-002 — Inactive Provider

Deactivate Provider.

### Expected

- Historical transactions remain.
- Provider unavailable for new transactions.

---

# 11. Opening Balance Tests

## TC-OPEN-001 — Positive Opening

Cash Opening:

`NPR 250,000`

### Expected

Cash Current Balance:

`NPR 250,000`

No Income.

No Profit.

---

## TC-OPEN-002 — Negative Opening

City Express Opening:

`NPR -50,000`

### Expected

City Express Current Balance:

`NPR -50,000`

System accepts approved negative Opening.

---

## TC-OPEN-003 — Zero Opening

Citizen Remit:

`NPR 0`

### Expected

Valid Opening record.

---

## TC-OPEN-004 — Staff Opening Attempt

Staff attempts to create Opening Balance.

### Expected

**Access Denied**

---

## TC-OPEN-005 — Finalized Opening Edit

Finalize Opening, then attempt ordinary edit.

### Expected

Ordinary editing blocked.

---

## TC-OPEN-006 — Opening Does Not Create Profit

Opening Bank:

`NPR 800,000`

### Expected

Profit/Loss:

`NPR 0`

unless current-period valid income/expense exists.

---

# 12. Remittance Tests

## TC-REM-001 — Standard Remittance

Starting:

Cash:

`NPR 0`

City Express:

`NPR 0`

Transaction:

Principal:

`NPR 10,000`

Service Charge:

`NPR 100`

### Expected

Cash:

`NPR 10,100`

City Express:

`NPR -10,000`

Service Charge Income:

`NPR 100`

Customer Balance:

Does not exist.

---

## TC-REM-002 — Remittance From Positive Provider Balance

Starting:

City Express:

`NPR 20,000`

Principal:

`NPR 10,000`

### Expected

City Express:

`NPR 10,000`

---

## TC-REM-003 — Remittance Creates Negative Balance

Starting:

City Express:

`NPR 2,000`

Principal:

`NPR 10,000`

Service Charge:

`NPR 100`

### Expected

City Express:

`NPR -8,000`

Transaction succeeds.

---

## TC-REM-004 — Zero Service Charge

Principal:

`NPR 10,000`

Service Charge:

`NPR 0`

### Expected

Cash:

`+10,000`

Provider:

`-10,000`

Service Charge Income:

`0`

---

## TC-REM-005 — Missing Customer

Attempt Remittance without required Customer.

### Expected

Validation error.

No Ledger movement.

---

## TC-REM-006 — Invalid Principal

Principal:

`0`

### Expected

Rejected where Principal is required.

No financial effect.

---

## TC-REM-007 — Double Click Save

User presses Save repeatedly.

### Expected

Only one valid financial transaction posted.

---

# 13. Deposit Tests

## TC-DEP-001 — Standard Deposit

Starting:

Cash:

`NPR 50,000`

BLB:

`NPR 20,000`

Transaction:

Principal:

`NPR 10,000`

Service Charge:

`NPR 100`

### Expected

Cash:

`NPR 60,100`

BLB:

`NPR 10,000`

---

## TC-DEP-002 — Deposit Creates Negative BLB

Starting:

BLB:

`NPR 5,000`

Deposit:

`NPR 10,000`

### Expected

BLB:

`NPR -5,000`

if BLB is configured to allow negative.

---

## TC-DEP-003 — Deposit Customer Balance

### Expected

No Customer Balance created.

---

# 14. Withdrawal Tests

Withdrawal tests shall be finalized after the approved Service Charge rule is confirmed.

## TC-WD-001 — Base Withdrawal

Starting:

Cash:

`NPR 50,000`

BLB:

`NPR -10,000`

Withdrawal Principal:

`NPR 10,000`

Before Service Charge effect:

### Expected Base Effect

Cash:

`NPR 40,000`

BLB:

`NPR 0`

---

## TC-WD-002 — Withdrawal Service Charge

**Status:** BLOCKED UNTIL RULE FINALIZED

Test final approved Service Charge handling.

---

# 15. Account Transfer Tests

## TC-TRF-001 — Bank to Provider

Starting:

Bank:

`NPR 500,000`

City Express:

`NPR -80,000`

Transfer:

`NPR 80,000`

### Expected

Bank:

`NPR 420,000`

City Express:

`NPR 0`

Income:

`0`

Expense:

`0`

---

## TC-TRF-002 — Partial Settlement

City Express:

`NPR -80,000`

Transfer:

`NPR 50,000`

### Expected

City Express:

`NPR -30,000`

---

## TC-TRF-003 — Same Account

Transfer:

Bank → Bank

### Expected

Rejected.

No Ledger movement.

---

## TC-TRF-004 — Account Transfer Does Not Create Profit

Transfer:

`NPR 100,000`

### Expected

Profit/Loss unchanged.

---

# 16. Service Charge Tests

## TC-SC-001 — Daily Total

Transactions:

- Charge `100`
- Charge `150`
- Charge `50`

### Expected

Daily Service Charge:

`NPR 300`

---

## TC-SC-002 — Staff-wise Total

Staff A:

`100 + 150`

Staff B:

`50`

### Expected

Staff A:

`NPR 250`

Staff B:

`NPR 50`

---

## TC-SC-003 — Cancelled Service Charge

Cancel transaction with:

`NPR 150`

Service Charge.

### Expected

Active Service Charge Total:

`NPR 150`

Original cancelled transaction remains visible.

---

# 17. Provider Commission Tests

## TC-COM-001 — Commission Received in Bank

Starting Bank:

`NPR 100,000`

Commission:

`NPR 5,000`

### Expected

Bank:

`NPR 105,000`

Commission Income:

`NPR 5,000`

---

## TC-COM-002 — Provider Separation

Enter:

City Express Commission:

`NPR 5,000`

Citizen Commission:

`NPR 3,000`

### Expected

Total Commission:

`NPR 8,000`

Provider report preserves:

- City Express = 5,000
- Citizen = 3,000

---

## TC-COM-003 — Commission Cancellation

Cancel:

`NPR 5,000`

Bank Commission.

### Expected

Bank effect reversed.

Active Commission excludes 5,000.

---

# 18. Expense Tests

## TC-EXP-001 — Cash Expense

Starting Cash:

`NPR 100,000`

Rent:

`NPR 20,000`

### Expected

Cash:

`NPR 80,000`

Expense:

`NPR 20,000`

---

## TC-EXP-002 — Bank Expense

Starting Bank:

`NPR 100,000`

Internet:

`NPR 3,000`

### Expected

Bank:

`NPR 97,000`

Expense:

`NPR 3,000`

---

## TC-EXP-003 — Dynamic Category

Admin adds:

`Maintenance`

### Expected

Expense can use Maintenance without database schema change.

---

## TC-EXP-004 — Cancel Expense

Original:

Cash `-20,000`

Expense `+20,000`

After Cancel:

### Expected

Cash restored.

Active Expense removed.

History retained.

---

# 19. Profit/Loss Tests

## TC-PL-001 — Profit

Service Charge:

`NPR 50,000`

Commission:

`NPR 30,000`

Expense:

`NPR 60,000`

### Expected

Income:

`NPR 80,000`

Profit:

`NPR 20,000`

---

## TC-PL-002 — Loss

Income:

`NPR 60,000`

Expense:

`NPR 75,000`

### Expected

Loss:

`NPR 15,000`

---

## TC-PL-003 — Principal Excluded

Remittance Principal Volume:

`NPR 1,000,000`

Service Charge:

`NPR 10,000`

Expense:

`NPR 4,000`

### Expected

Profit:

`NPR 6,000`

Principal `1,000,000` is not Income.

---

## TC-PL-004 — Opening Excluded

Opening Bank:

`NPR 800,000`

### Expected

Does not create `NPR 800,000` Profit.

---

## TC-PL-005 — Account Transfer Excluded

Bank → Provider:

`NPR 50,000`

### Expected

Profit/Loss unchanged.

---

# 20. Shareholder Opening Tests

## TC-SH-001 — Opening Shares

Shareholder A:

`500`

Shareholder B:

`300`

Value:

`NPR 1,000`

### Expected

A Valuation:

`NPR 500,000`

B Valuation:

`NPR 300,000`

---

## TC-SH-002 — Opening Shares No Bank Effect

Create approved Opening Shareholding.

### Expected

Company Cash/Bank unchanged by Share Opening itself.

---

# 21. Share Transfer Tests

## TC-SHT-001 — Standard Transfer

Before:

A = `500`

B = `300`

Transfer:

A → B = `100`

### Expected

A = `400`

B = `400`

Cash:

No Change

Bank:

No Change

---

## TC-SHT-002 — Insufficient Shares

A owns:

`100`

Attempt:

`150`

### Expected

Rejected.

---

## TC-SHT-003 — Same Shareholder

A → A

### Expected

Rejected.

---

## TC-SHT-004 — New Shareholder Through Transfer

A transfers 100 shares to newly created C.

### Expected

A:

`-100`

C:

`+100`

Company Cash/Bank:

No Change.

---

## TC-SHT-005 — Transfer Cancellation

Cancel valid A → B transfer.

### Expected

Seller shares restored.

Buyer shares reduced.

Company Cash/Bank unchanged.

---

# 22. Share Redemption Tests

## TC-SHR-001 — Partial Redemption From Bank

A owns:

`500`

Redeem:

`200`

Share Value:

`NPR 1,000`

Payment Account:

Bank

### Expected

A:

`300 shares`

Bank:

`-200,000`

Operating Expense:

No automatic Expense.

---

## TC-SHR-002 — Full Redemption

A owns:

`300`

Redeem all.

### Expected

A:

`0`

Status:

Exited/Inactive according to final rule.

Payment Account decreases correctly.

History remains.

---

## TC-SHR-003 — Full Transfer-Out Exit

A transfers all shares to B.

### Expected

A:

`0`

B increases.

Company Bank:

No Change.

Company Cash:

No Change.

---

## TC-SHR-004 — Redemption Cancellation

Cancel a valid Redemption.

### Expected

Shares restored.

Payment Account restored.

History remains.

---

# 23. Share Value Tests

## TC-SHV-001 — Current Value

Share Value:

`NPR 1,000`

Quantity:

`500`

### Expected

Current Valuation:

`NPR 500,000`

---

## TC-SHV-002 — Historical Value Protection

If future Share Value changes after approval:

Old transaction:

`NPR 1,000/share`

New current value:

`NPR 1,200/share`

### Expected

Old transaction retains:

`NPR 1,000/share`

---

# 24. Cancellation Tests

## TC-CAN-001 — Remittance Cancellation

Original:

Cash `+10,100`

City Express `-10,000`

Service Charge `+100`

### Expected After Cancellation

Financial effects reversed.

Original remains Cancelled.

Reason retained.

---

## TC-CAN-002 — Double Cancellation

Attempt to cancel an already Cancelled transaction.

### Expected

Rejected.

Financial effect shall not reverse twice.

---

## TC-CAN-003 — Staff Cancellation

Staff attempts final cancellation.

### Expected

Denied under initial standard.

If Cancellation Request workflow exists, request is created instead.

---

# 25. Correction Tests

## TC-COR-001 — Incorrect Amount

Original transaction:

`15,000`

Correct amount:

`10,000`

### Expected Workflow

Original:

Cancelled/Reversed.

Replacement:

`10,000`

Both records traceable.

---

## TC-COR-002 — Direct Posted Edit

Attempt to freely change posted Principal.

### Expected

Blocked/restricted according to approved Correction workflow.

---

# 26. Note Tests

## TC-NOTE-001 — Transaction Note

Enter Note:

`Customer requested urgent transaction.`

### Expected

Note saved and displayed in detail.

---

## TC-NOTE-002 — Cancellation Reason

Attempt cancellation without reason.

### Expected

Rejected.

Cancellation Reason mandatory.

---

# 27. Attachment Tests

## TC-ATT-001 — PDF Upload

Upload valid PDF.

### Expected

Accepted if PDF approved.

---

## TC-ATT-002 — Image Upload

Upload valid JPG/PNG.

### Expected

Accepted if approved.

---

## TC-ATT-003 — Invalid Executable

Attempt `.exe` upload.

### Expected

Rejected.

---

## TC-ATT-004 — Unauthorized Attachment Access

Staff/Shareholder attempts to access an attachment outside permission.

### Expected

Access Denied.

---

## TC-ATT-005 — Cancelled Transaction Attachment

Cancel transaction containing attachment.

### Expected

Attachment remains linked to historical transaction.

---

# 28. Ledger Tests

## TC-LED-001 — Running Balance

Opening Cash:

`100,000`

Remittance Cash:

`+10,100`

Expense:

`-5,000`

### Expected

Cash:

`105,100`

Ledger Running Balance:

`105,100`

---

## TC-LED-002 — Ledger vs Current Balance

### Expected

Ledger calculated balance equals Account Current Balance.

---

## TC-LED-003 — Cancelled Entry

Cancel the `-5,000` Expense.

### Expected

Cash becomes:

`110,100`

Cancelled Expense no longer affects active running result.

---

# 29. Report Tests

## TC-REP-001 — Dashboard vs Daily Report

Dashboard:

Today's Service Charge = `5,000`

### Expected

Daily Service Charge report:

`5,000`

---

## TC-REP-002 — Report Filter

Filter:

City Express only.

### Expected

Only City Express matching records included.

---

## TC-REP-003 — Date Range

Start Date → End Date.

### Expected

Only transactions within approved Business Date range.

---

## TC-REP-004 — Cancelled Transaction Exclusion

### Expected

Cancelled transactions excluded from active financial totals.

---

## TC-REP-005 — PDF Export

### Expected

PDF totals equal on-screen filtered totals.

---

## TC-REP-006 — Spreadsheet Export

### Expected

Spreadsheet totals equal on-screen filtered totals.

---

# 30. Reconciliation Tests

## TC-REC-001 — Cash Match

System Cash:

`100,000`

Physical Cash:

`100,000`

### Expected

Difference:

`0`

---

## TC-REC-002 — Cash Difference

System:

`100,000`

Physical:

`99,500`

### Expected

Difference:

`-500`

System Cash shall not automatically change.

---

## TC-REC-003 — Provider Reconciliation

Compare system City Express Balance with provider statement.

### Expected

Difference shown/identified according to reconciliation workflow.

No automatic hidden adjustment.

---

# 31. Audit Tests

## TC-AUD-001 — Created By

Staff A creates Remittance.

### Expected

Transaction records:

Created By = Staff A.

---

## TC-AUD-002 — Cancelled By

Admin cancels transaction.

### Expected

Cancelled By = correct Admin.

Cancelled At preserved.

Reason preserved.

---

## TC-AUD-003 — Deactivated User History

Deactivate Staff A.

### Expected

Old transactions still show Staff A as creator.

---

# 32. Business Date Tests

## TC-DATE-001 — Business Date vs Created At

Enter permitted prior Business Date.

### Expected

Business Date and Created At remain separate.

---

## TC-DATE-002 — Unauthorized Backdating

Staff attempts date outside permitted range.

### Expected

Rejected.

---

## TC-DATE-003 — Closed Period

If closing is implemented, attempt transaction in closed period.

### Expected

Rejected unless authorized reopening rule applies.

---

# 33. Migration Tests

## TC-MIG-001 — Customer Import

Import 100 clean Customers.

### Expected

100 approved records imported.

No Ledger entries.

No Customer balances.

---

## TC-MIG-002 — Duplicate Customer

Import duplicate Customer.

### Expected

Flagged/handled according to approved import rule.

---

## TC-MIG-003 — Old Financial Data

### Expected

Old Access Day Book transactions do not automatically enter live Ledger.

---

## TC-MIG-004 — Opening Balance From Approval

### Expected

Opening values equal approved meeting figures, not guessed Access totals.

---

## TC-MIG-005 — BLB Opening

BLB Opening comes from approved reconciliation.

### Expected

System does not reconstruct missing five-year BLB history by assumption.

---

# 34. Security Tests

## TC-SEC-001 — Direct Admin URL

Staff opens Admin route.

### Expected

403 / Access Denied.

---

## TC-SEC-002 — Shareholder ID Manipulation

Shareholder A changes URL ID to B.

### Expected

Access Denied.

---

## TC-SEC-003 — CSRF Protection

Submit unauthorized state-changing request without valid CSRF protection.

### Expected

Rejected.

---

## TC-SEC-004 — Mass Assignment

Staff submits hidden field:

`role = admin`

or:

`balance = 999999`

### Expected

Ignored/rejected.

Protected data unchanged.

---

## TC-SEC-005 — Public Attachment URL

Attempt direct unauthenticated access to protected attachment.

### Expected

Access denied.

---

# 35. Concurrency Tests

## TC-CON-001 — Simultaneous Provider Transactions

Two Staff users post against City Express at the same time.

### Expected

Both valid transactions recorded.

No lost balance update.

Ledger remains consistent.

---

## TC-CON-002 — Simultaneous Share Transfer

Two attempts try to transfer more combined shares than seller owns.

### Expected

Final Share Quantity never becomes negative.

Unsafe transaction rejected.

---

# 36. Atomicity Tests

## TC-ATM-001 — Remittance Failure Mid-Posting

Force failure after transaction creation but before all Ledger effects.

### Expected

Entire database operation rolls back.

No partial transaction.

---

## TC-ATM-002 — Share Redemption Failure

Force Bank posting failure after Share processing begins.

### Expected

Neither Share reduction nor incomplete Bank movement remains.

---

# 37. Backup Tests

## TC-BKP-001 — Automatic Backup

Run configured backup.

### Expected

Valid backup file created.

---

## TC-BKP-002 — Off-Server Backup

### Expected

At least one valid backup copy exists outside live server.

---

## TC-BKP-003 — Restore

Restore backup into safe test environment.

### Verify

- Users
- Customers
- Accounts
- Transactions
- Ledger
- Commission
- Expenses
- Shares
- Attachments

---

# 38. Go-Live Tests

## TC-LIVE-001 — Production Debug

### Expected

Debug mode OFF.

---

## TC-LIVE-002 — HTTPS

### Expected

Production protected by valid HTTPS.

---

## TC-LIVE-003 — Opening Finalized

### Expected

Approved Opening Balance exists and ordinary edit disabled.

---

## TC-LIVE-004 — Opening Shares Finalized

### Expected

Approved Shareholder Opening Holdings correct.

---

## TC-LIVE-005 — First Live Transaction

Post first official transaction.

### Expected

Correct financial effect and report result.

---

# 39. Staff Daily Workflow Test

A Staff user shall be able to perform the normal sequence:

1. Login.
2. Search Customer.
3. Add Customer if needed.
4. Select transaction type.
5. Select Provider/Account.
6. Enter Principal.
7. Enter Service Charge.
8. Enter Reference.
9. Add Note.
10. Add Attachment.
11. Save.
12. View confirmation.

The workflow shall be practical and clear.

---

# 40. Admin Full Workflow Test

Admin shall be able to:

1. Review Dashboard.
2. Review Accounts.
3. Review negative balances.
4. Review Staff transactions.
5. Record Commission.
6. Record Expense.
7. Transfer/settle Accounts.
8. Manage Shareholders.
9. Review Reports.
10. Process cancellation/correction.
11. View Audit.

---

# 41. Shareholder Full Workflow Test

Shareholder shall be able to:

1. Login.
2. View own Shares.
3. View Share Value.
4. View Valuation.
5. View own history.
6. Logout.

No unrelated Company financial data shall be exposed.

---

# 42. Regression Test Set — Financial Engine

Whenever core Financial Engine changes, rerun at minimum:

- `TC-REM-001`
- `TC-REM-003`
- `TC-DEP-001`
- `TC-WD-001`
- `TC-TRF-001`
- `TC-COM-001`
- `TC-EXP-001`
- `TC-CAN-001`
- `TC-LED-001`
- `TC-ATM-001`

---

# 43. Regression Test Set — Share Engine

Whenever Share Engine changes, rerun:

- `TC-SH-001`
- `TC-SHT-001`
- `TC-SHT-002`
- `TC-SHT-005`
- `TC-SHR-001`
- `TC-SHR-004`
- `TC-CON-002`
- `TC-ATM-002`

---

# 44. Regression Test Set — Permissions

Whenever authentication/authorization changes, rerun:

- `TC-AUTH-001`
- `TC-AUTH-002`
- `TC-AUTH-003`
- `TC-ROLE-001`
- `TC-ROLE-002`
- `TC-ROLE-004`
- `TC-ROLE-005`
- `TC-SEC-001`
- `TC-SEC-002`

---

# 45. Regression Test Set — Reports

Whenever reporting code changes, rerun:

- `TC-REP-001`
- `TC-REP-002`
- `TC-REP-003`
- `TC-REP-004`
- `TC-PL-001`
- `TC-LED-002`
- `TC-SC-001`

---

# 46. Critical Invariants

Testing shall continuously verify the following invariants:

1. Customer has no financial balance.
2. Account movement is traceable.
3. Negative approved provider balance is allowed.
4. Principal and Service Charge remain separate.
5. Service Charge and Provider Commission remain separate.
6. Account Transfer does not create Income/Expense.
7. Opening Balance does not create Profit.
8. Shareholder-to-Shareholder transfer does not affect Company Cash/Bank.
9. Company Redemption affects both Share Quantity and payment Account.
10. Share Quantity never becomes negative.
11. Cancelled financial transactions remain historically visible.
12. Current Account Balance reconciles with Ledger.
13. Current Share Quantity reconciles with Share Ledger.

---

# 47. Blocking Tests Before Go-Live

The following categories must PASS before production:

- Authentication
- Role Permissions
- Opening Balance
- Remittance
- Deposit
- Withdrawal after rule confirmation
- Account Transfer
- Negative Balance
- Service Charge
- Commission
- Expense
- Ledger
- Cancellation
- Share Transfer
- Share Redemption
- Shareholder Privacy
- Backup Restore
- HTTPS/Security

---

# 48. Test Evidence

For critical financial tests, retain evidence where practical.

Evidence may include:

- Screenshot
- Test database record
- Exported report
- Ledger screenshot
- Test note

---

# 49. Defect Record

Failed test should generate a defect containing:

- Defect ID
- Related Test Case
- Expected Result
- Actual Result
- Severity
- Steps to Reproduce
- Screenshot/Evidence
- Assigned Developer
- Fix Version
- Retest Status

---

# 50. Retest Rule

After a defect is fixed:

1. Retest failed case.
2. Run related regression tests.
3. Record result.

A fix is not complete until retesting passes.

---

# 51. No Test Bypass

A Critical test shall not be marked PASS because the developer believes the implementation is logically correct.

The actual workflow/result must be verified.

---

# 52. AI Testing Rule

AI coding agents may help generate and execute automated tests.

However, AI shall not invent expected financial results.

Expected results must come from approved Business Rules.

---

# 53. Automated Testing

Where practical, high-risk financial rules should have automated tests.

Recommended priorities:

- Remittance posting
- Negative balance
- Account Transfer
- Commission
- Expense
- Cancellation
- Share Transfer
- Share Redemption
- Permission isolation

---

# 54. Manual Testing

Manual UAT remains necessary for:

- Staff usability
- Dashboard understanding
- Report presentation
- Attachment workflow
- Mobile workflow
- Business Owner acceptance

---

# 55. Test Environment Reset

Test data shall be reset or clearly separated between test cycles where needed.

Old failed test records shall not confuse new expected results.

---

# 56. Production Test Restriction

Do not perform destructive test scenarios using actual live financial data.

Use:

- Staging
- Development
- Controlled test records

unless a safe production verification is explicitly approved.

---

# 57. Final Test Summary

Before Go-Live, prepare a summary:

| Area | Passed | Failed | Blocked |
|---|---:|---:|---:|
| Authentication |  |  |  |
| Permissions |  |  |  |
| Financial |  |  |  |
| Shareholder |  |  |  |
| Reports |  |  |  |
| Security |  |  |  |
| Backup |  |  |  |

---

# 58. Go-Live Test Approval

Production release shall require:

**NO OPEN CRITICAL TEST FAILURE**

and Business Owner approval.

---

# 59. Future Version Testing

Every future release shall identify:

- New Test Cases
- Existing Regression Tests
- Business-rule changes
- Data migration tests
- Permission tests

---

# 60. Document Authority

This document is the authoritative Master Test Case Catalog for the Remittance Management System.

It does not override:

`01_REMITTANCE_BUSINESS_CONSTITUTION.md`

Expected financial results shall always follow the approved Business Constitution and dependent standards.

---

# 61. Approval

**Status:** FINAL DRAFT — Pending Business Owner Approval

After approval, this document becomes the official test-case baseline for development, UAT, regression testing, production release and future maintenance.

---

**END OF DOCUMENT**