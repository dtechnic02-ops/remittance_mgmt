# REMITTANCE MANAGEMENT SYSTEM — USER ACCEPTANCE, GO-LIVE AND HANDOVER STANDARD

**Document ID:** 18  
**File Name:** `18_USER_ACCEPTANCE_GO_LIVE_AND_HANDOVER_STANDARD.md`  
**Project:** Remittance Management System  
**Document Type:** User Acceptance, Go-Live and Handover Standard  
**Version:** 1.0  
**Status:** FINAL DRAFT — Pending Business Owner Approval  
**Authority:** Business Owner  

---

# 1. Purpose

This document defines the final approval, User Acceptance Testing, production Go-Live, staff handover and project-completion process for the Remittance Management System.

It governs:

- User Acceptance Testing
- Business Owner acceptance
- Financial verification
- Role testing
- Opening Balance approval
- Opening Shareholder approval
- Customer migration verification
- Production readiness
- Go-Live authorization
- Staff training
- Admin training
- Shareholder portal verification
- Technical handover
- Backup handover
- Documentation handover
- Post-Go-Live monitoring
- Final project acceptance

The purpose is to ensure that the software is not declared complete merely because development is finished.

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
16. `15_SYSTEM_ARCHITECTURE_AND_MODULE_STANDARD.md`
17. `16_DEVELOPER_AND_AI_EXECUTION_STANDARD.md`
18. `17_MASTER_DATA_AND_SYSTEM_CONFIGURATION_STANDARD.md`

The Business Constitution remains the highest business authority.

---

# 3. Completion Principle

Development completion and business acceptance are different stages.

A module may be technically complete but shall not be considered accepted until:

- Approved business behavior works.
- Financial effects are correct.
- Permissions are correct.
- Reports reconcile.
- Required users test the workflow.
- Business Owner approves the result.

---

# 4. User Acceptance Testing

Before production Go-Live, the system shall undergo User Acceptance Testing (UAT).

UAT shall involve actual intended system roles:

- Admin
- Staff
- Shareholder

where those roles are included in the active release.

---

# 5. UAT Environment

Where practical, UAT should occur in a separate test/staging environment.

Test data shall not become official production financial history.

If production-like infrastructure is used, test records must remain clearly isolated and removed before official Opening.

---

# 6. UAT Test Data

UAT shall use controlled sample data.

Example Accounts:

- Cash
- Bank
- BLB
- City Express
- Citizen Remit

Example Customers:

- Test Customer A
- Test Customer B

Example Shareholders:

- Test Shareholder A
- Test Shareholder B

No actual customer financial position shall be affected by UAT.

---

# 7. Admin UAT

Admin shall test the functions authorized for Admin.

Minimum Admin UAT includes:

- Login
- Dashboard
- Customer Management
- Account Master
- Provider Master
- Opening Balance
- Remittance
- Deposit
- Withdrawal
- Account Transfer
- Settlement
- Commission
- Expense
- Account Ledger
- Reports
- Shareholder Management
- Share Transfer
- Share Redemption/Exit
- Cancellation
- Audit
- Notes
- Attachments

---

# 8. Staff UAT

Staff shall test practical daily operation.

Minimum Staff UAT includes:

- Login
- Customer Search
- Add Customer
- Remittance
- Deposit
- Withdrawal
- Service Charge entry
- Reference entry
- Note
- Attachment
- Own/permitted transaction history

The Staff workflow shall be practical for real daily use.

---

# 9. Shareholder UAT

Shareholder shall verify:

- Login
- Own Share Quantity
- Share Value
- Total Share Valuation
- Own Share History
- Own Transfer History
- Own Redemption/Exit history where applicable

Shareholder shall also verify that another Shareholder's private information is not visible.

---

# 10. Financial UAT Principle

Every financial workflow must be tested using known expected results.

The UAT reviewer shall not accept a transaction merely because the form saved successfully.

Before/after financial effects must be verified.

---

# 11. Remittance UAT Example

Before:

Cash:

`NPR 50,000`

City Express:

`NPR 5,000`

Transaction:

Principal:

`NPR 10,000`

Service Charge:

`NPR 100`

Expected:

Cash:

`NPR 60,100`

City Express:

`NPR -5,000`

Customer Balance:

**No Balance Created**

---

# 12. Deposit UAT Example

Before:

Cash:

`NPR 50,000`

BLB:

`NPR 20,000`

Deposit:

`NPR 10,000`

Service Charge:

`NPR 100`

Expected:

Cash:

`NPR 60,100`

BLB:

`NPR 10,000`

---

# 13. Withdrawal UAT

Withdrawal UAT shall follow the final approved Service Charge rule.

The test must verify:

- Correct Cash reduction
- Correct BLB/Bank operational increase
- Correct Service Charge
- No Customer Balance

---

# 14. Account Transfer UAT

Before:

Bank:

`NPR 500,000`

City Express:

`NPR -80,000`

Transfer:

Bank → City Express

`NPR 80,000`

Expected:

Bank:

`NPR 420,000`

City Express:

`NPR 0`

Income:

`No Effect`

Expense:

`No Effect`

---

# 15. Negative Balance UAT

The system shall demonstrate that approved Accounts may become negative.

Example:

City Express:

`NPR 2,000`

Remittance:

`NPR 10,000`

Expected:

City Express:

`NPR -8,000`

The valid transaction shall not be rejected.

---

# 16. Service Charge UAT

Verify that:

- Service Charge is separate from Principal.
- It appears in Service Charge reports.
- It is attributed to the correct Staff/User.
- Cancelled transaction Service Charge is excluded.
- Cash effect follows approved rule.

---

# 17. Commission UAT

Example:

Commission:

`NPR 5,000`

Received in Bank.

Expected:

Bank:

`+5,000`

Commission Income:

`+5,000`

Principal transaction volume:

No Effect.

---

# 18. Expense UAT

Example:

Rent:

`NPR 20,000`

Paid from Cash.

Expected:

Cash:

`-20,000`

Expense:

`+20,000`

---

# 19. Profit/Loss UAT

Example:

Service Charge:

`NPR 50,000`

Commission:

`NPR 30,000`

Expense:

`NPR 60,000`

Expected:

Income:

`NPR 80,000`

Profit:

`NPR 20,000`

Principal transaction amounts shall not be counted as Income.

---

# 20. Share Transfer UAT

Before:

Shareholder A:

`500 shares`

Shareholder B:

`300 shares`

Transfer:

A → B:

`100 shares`

Expected:

A:

`400`

B:

`400`

Cash:

No Change

Bank:

No Change

---

# 21. Share Redemption UAT

Before:

Shareholder A:

`500 shares`

Redemption:

`200 shares`

Value per Share:

`NPR 1,000`

Paid from Bank.

Expected:

Shareholder A:

`300 shares`

Bank:

`-200,000`

Ordinary Expense:

No automatic effect.

---

# 22. Cancellation UAT

Each critical transaction type shall test cancellation.

Example Remittance:

Original effect:

- Cash `+10,100`
- City Express `-10,000`

After cancellation:

- Active Cash effect reversed
- City Express effect reversed
- Service Charge reversed
- Original Transaction remains visible
- Cancellation reason visible
- Cancelled By visible

---

# 23. Permission UAT

Each protected function shall be tested under all relevant roles.

Example:

| Function | Admin | Staff | Shareholder |
|---|---|---|---|
| Remittance Entry | Yes | Yes | No |
| Opening Balance | Yes | No | No |
| Account Transfer | Yes | No | No |
| Commission Entry | Yes | No | No |
| Share Transfer | Yes | No | No |
| Own Share View | N/A | No | Yes |

Unauthorized attempts must be rejected server-side.

---

# 24. Direct URL UAT

Staff shall attempt to access an Admin-only URL.

Expected:

**Access Denied**

Shareholder A shall attempt to access Shareholder B record.

Expected:

**Access Denied**

---

# 25. Attachment UAT

Test:

- Valid PDF
- Valid Image
- Invalid File Type
- Oversized File
- Authorized View
- Unauthorized View
- Cancelled transaction attachment preservation

---

# 26. Note UAT

Verify Note works on approved major records.

Important records include:

- Customer
- Transaction
- Transfer
- Commission
- Expense
- Opening Balance
- Shareholder
- Share Transfer
- Share Redemption

---

# 27. Report UAT

Reports shall be tested using known source transactions.

Verify:

- Daily Report
- Account Ledger
- Current Balance
- Service Charge
- Commission
- Expense
- Profit/Loss
- Shareholder Report

---

# 28. Dashboard Reconciliation UAT

Dashboard summary and detailed report must agree.

Example:

Dashboard:

Today's Service Charge:

`NPR 5,000`

Report:

Today's Service Charge:

Must equal:

`NPR 5,000`

---

# 29. Ledger Reconciliation UAT

For every tested Account:

`Opening + In - Out = Current Balance`

must be true.

---

# 30. Share Ledger Reconciliation UAT

For every tested Shareholder:

`Opening Shares + Shares In - Shares Out = Current Share Quantity`

must be true.

---

# 31. UAT Defect Classification

Issues found during UAT shall be classified.

### Critical

Financial/security corruption.

### High

Required workflow cannot operate.

### Medium

Incorrect report or important usability issue.

### Low

Minor presentation or convenience issue.

---

# 32. Critical UAT Defects

Examples:

- Wrong Cash balance
- Wrong Provider balance
- Duplicate financial posting
- Wrong cancellation reversal
- Negative Share Quantity
- Share transfer changes Bank incorrectly
- Shareholder sees another Shareholder's data
- Staff gains Admin permission

Critical defects block Go-Live.

---

# 33. High Defects

Examples:

- Staff cannot save valid Remittance
- Expense cannot be recorded
- Account Transfer fails
- Required report cannot open

High defects should normally be resolved before Go-Live.

---

# 34. UAT Approval

After UAT, Business Owner shall classify the release as:

- Approved
- Approved with minor known issues
- Rejected / Requires Correction

Financial Critical defects shall not be accepted as minor known issues.

---

# 35. Legacy Closing Approval

Before Go-Live, verify:

- Legacy Cut-Off Date
- Final Access backup
- Closing meeting
- Approved Opening Accounts
- Approved Shareholder holdings

---

# 36. Customer Migration Acceptance

Customer migration shall be accepted after verifying:

- Record count
- Search
- Duplicate handling
- Mobile/name data
- No Customer Balance
- No Financial Ledger effect

---

# 37. Opening Balance Acceptance

The Business Owner/Admin shall verify every Opening Balance before finalization.

Example checklist:

- [ ] Cash
- [ ] Bank
- [ ] BLB
- [ ] City Express
- [ ] Citizen Remit
- [ ] IME
- [ ] IPS
- [ ] Other Accounts

---

# 38. Opening Balance Evidence

Opening evidence shall be attached or securely preserved.

Possible evidence:

- Meeting Minute
- Cash Verification
- Bank Statement
- Provider Statement
- Approved reconciliation

---

# 39. Opening Shareholder Acceptance

For each Shareholder confirm:

- Name
- Share Quantity
- Value Per Share
- Total Valuation
- Status

---

# 40. Opening Share Value

Current baseline:

**1 Share Unit = NPR 1,000**

The confirmed value shall be approved before Opening Share records are finalized.

---

# 41. Production Readiness Review

Before Go-Live verify:

### Application

- Login works
- Roles work
- Core modules work
- Reports work
- Attachments work
- Cancellation works

### Financial

- Opening Balances correct
- Ledger correct
- Negative balances correct
- Service Charge correct
- Commission correct
- Expense correct
- Profit/Loss correct

### Shareholder

- Opening Shares correct
- Share Transfer correct
- Redemption correct
- Privacy correct

---

# 42. Infrastructure Readiness

Verify:

- Production Server
- Database
- Domain/Subdomain
- HTTPS
- Storage permissions
- Backup
- Restore test
- Scheduler where required
- Production Debug Off

---

# 43. Production Admin Creation

At least one authorized Admin account shall exist before Go-Live.

The credentials shall be securely transferred to the authorized business person.

---

# 44. Staff Account Setup

Each Staff user shall receive an individual login.

Shared Staff login shall not be the standard operating model.

---

# 45. Shareholder Login Setup

Shareholder login shall link to the correct Shareholder business record.

Verify that:

Shareholder A login → A's Shares only.

---

# 46. Admin Training

Admin training shall cover at minimum:

- Dashboard
- Customer
- Accounts
- Providers
- Transactions
- Account Transfer
- Negative Balance
- Settlement
- Commission
- Expense
- Reports
- Shareholder
- Share Transfer
- Share Redemption
- Cancellation
- Backup awareness
- User Management

---

# 47. Staff Training

Staff training should remain focused on daily work.

At minimum:

- Login
- Search Customer
- Add Customer
- Remittance
- Deposit
- Withdrawal
- Service Charge
- Reference
- Note
- Attachment
- Transaction History
- Error/correction procedure

---

# 48. Staff Financial Training

Staff shall understand:

- Service Charge must be entered correctly.
- Customer does not have a system balance.
- Provider selection matters.
- Wrong transaction shall not be hidden/deleted.
- Mistakes must follow cancellation/correction process.

Staff does not need to understand internal ledger architecture.

---

# 49. Shareholder Training

Shareholder training may cover:

- Login
- Current Shares
- Share Value
- Valuation
- Share History
- Logout

Shareholder shall not be trained or enabled to alter ownership records directly.

---

# 50. Training Environment

Where practical, training should use test data before production begins.

Users shall not create unnecessary dummy financial transactions in production.

---

# 51. Training Confirmation

The Business Owner may record that:

- Admin trained
- Staff trained
- Shareholder portal demonstrated

This provides clear handover evidence.

---

# 52. Technical Handover

Technical handover shall include sufficient information to maintain production.

It may include:

- Project repository
- Production version
- Deployment instructions
- Environment requirement list
- Database migration process
- Backup process
- Restore process
- Scheduler requirements
- Storage configuration

Sensitive credentials shall be handed over securely rather than written in public documentation.

---

# 53. Documentation Handover

The approved documentation set shall be preserved with the project.

It shall not remain only inside chat history.

At minimum, the project repository/document storage shall contain the approved `.md` standards.

---

# 54. Source Code Handover

Source code shall exist in controlled version management.

Production server files shall not be the only copy.

---

# 55. Database Handover

The Business Owner/authorized technical administrator shall know:

- Database exists
- Backup location
- How recovery is initiated
- Who controls database access

The actual password shall remain securely managed.

---

# 56. Domain Handover

The project record shall identify:

- Production Domain
- Registrar/management location
- Renewal Date
- Responsible person

---

# 57. Hosting Handover

The project record shall identify:

- Hosting/VPS provider
- Server/project identifier
- Renewal date
- Backup location
- Responsible administrator

---

# 58. SSL Handover

SSL/TLS renewal shall be automated where practical.

If manual renewal is required, its responsible process shall be documented.

---

# 59. Backup Handover

Before Go-Live acceptance, demonstrate:

- Where backups are stored
- Backup frequency
- Retention
- Off-server copy
- Restore capability

---

# 60. Restore Demonstration

A successful backup file alone is not sufficient.

At least one safe restore test should verify:

- Database
- Transactions
- Account balances
- Shareholder records
- Attachments

---

# 61. Legacy Archive Handover

Preserve:

- Final Microsoft Access database
- Important Legacy reports
- Relevant attachments
- Cut-Off information

The old database shall remain historical reference.

---

# 62. Go-Live Authorization

Production operation shall begin only after explicit authorization.

Go-Live decision shall confirm:

- Opening approved
- Users ready
- Core financial tests passed
- Backup ready
- UAT passed

---

# 63. Go-Live Sequence

Recommended Go-Live sequence:

1. Freeze Legacy System.
2. Create final Legacy backup.
3. Complete closing approval.
4. Verify Production configuration.
5. Import approved Customers.
6. Enter Opening Balances.
7. Enter Opening Shareholdings.
8. Review Opening.
9. Finalize Opening.
10. Create/verify users.
11. Create new-system backup.
12. Enable production operation.
13. Perform first live transaction.
14. Verify financial effects.

---

# 64. First Live Transaction Verification

The first live transaction shall be checked manually.

Verify:

- Customer
- Transaction Type
- Amount
- Service Charge
- Account effect
- Ledger
- Created By
- Report

---

# 65. First-Day Monitoring

During the first production day, Admin should closely monitor:

- Cash
- Provider balances
- Staff entries
- Service Charges
- Cancellations
- Errors

---

# 66. First-Day Cash Reconciliation

At end of first day:

**Physical Cash vs System Cash**

shall be compared.

Any difference shall be investigated immediately.

---

# 67. First-Week Monitoring

For at least the initial operating period, review:

- Daily Cash
- Provider balances
- Negative Accounts
- Service Charge
- Staff transactions
- Cancelled transactions
- Error logs
- Backups

---

# 68. First-Month Review

At the end of the first full month, verify:

- Month-End Settlement
- Provider Commission
- Expenses
- Profit/Loss
- Account balances
- Reports
- Backup integrity

---

# 69. Post-Go-Live Defect

A defect discovered after Go-Live shall follow:

`12_OPERATION_SUPPORT_AND_CHANGE_MANAGEMENT_STANDARD.md`

Financial data shall not be directly manipulated without controlled correction.

---

# 70. Post-Go-Live Feature Request

A new feature request after delivery shall not be treated as unfinished original work unless it was part of the approved scope.

It shall be classified as:

- Existing Requirement
- Bug
- Change Request
- Future Feature

---

# 71. Support Boundary

Software support shall distinguish:

### Software Defect

System does not follow approved documented behavior.

### User Error

User entered incorrect information.

### New Requirement

Business wants functionality not included in approved release.

These require different handling.

---

# 72. Business Owner Acceptance

Final Business Owner acceptance should confirm that the software:

- Follows approved business rules.
- Is usable for daily operation.
- Shows correct financial effects.
- Provides required control of Service Charge.
- Shows Account balances.
- Supports approved Shareholder behavior.
- Preserves audit history.
- Has working backup.

---

# 73. Final Acceptance Checklist — Business

- [ ] Customer has no financial balance
- [ ] Remittance logic approved
- [ ] Deposit logic approved
- [ ] Withdrawal logic approved
- [ ] Negative balance works
- [ ] Service Charge works
- [ ] Account Transfer works
- [ ] Settlement works
- [ ] Commission works
- [ ] Expense works
- [ ] Profit/Loss works
- [ ] Shareholding works
- [ ] Share Transfer works
- [ ] Share Redemption works
- [ ] Shareholder privacy works

---

# 74. Final Acceptance Checklist — Security

- [ ] Admin access correct
- [ ] Staff access correct
- [ ] Shareholder access correct
- [ ] Direct URL protection works
- [ ] Passwords securely stored
- [ ] HTTPS enabled
- [ ] Sensitive attachments protected
- [ ] Production Debug disabled
- [ ] Audit information available

---

# 75. Final Acceptance Checklist — Data

- [ ] Customer migration checked
- [ ] Opening Balances checked
- [ ] Opening Shareholdings checked
- [ ] Ledger reconciles
- [ ] Share Ledger reconciles
- [ ] Legacy archive preserved
- [ ] No old incomplete financial data contaminates live ledger

---

# 76. Final Acceptance Checklist — Infrastructure

- [ ] Production Domain active
- [ ] Hosting active
- [ ] Database active
- [ ] Storage configured
- [ ] SSL active
- [ ] Backup configured
- [ ] Off-server backup available
- [ ] Restore tested
- [ ] Renewal information recorded

---

# 77. Final Acceptance Checklist — Operation

- [ ] Admin trained
- [ ] Staff trained
- [ ] Shareholder portal demonstrated
- [ ] Cancellation process understood
- [ ] Daily reconciliation understood
- [ ] Month-end process understood
- [ ] Support process understood

---

# 78. Known Issues List

Minor known issues permitted at Go-Live shall be documented.

Each item should include:

- Issue
- Severity
- Impact
- Workaround
- Planned Fix

Critical financial/security issues shall not be placed on a harmless known-issue list to bypass release standards.

---

# 79. Final Release Version

The official Go-Live release shall have a version identifier.

Example:

`Version 1.0.0`

The version shall correspond to:

- Source code
- Database migration state
- Documentation
- Release notes

---

# 80. Release Note

Go-Live release note should identify:

- Version
- Date
- Included Modules
- Known limitations
- Important configuration
- Migration status
- Approval status

---

# 81. Approval Record

**Project:** Remittance Management System

**Release Version:** __________________

**Go-Live Date:** _____________________

**Legacy Cut-Off Date:** ______________

**Opening Effective Date:** ___________

**Business Owner:** ___________________

**Approved:**  

- [ ] YES
- [ ] NO

**Approval Reference / Signature:**

`________________________________________`

---

# 82. Handover Record

Confirm handover of:

- [ ] Source Code
- [ ] Documentation
- [ ] Production System
- [ ] Database Backup
- [ ] Restore Instructions
- [ ] Legacy Archive
- [ ] Domain Information
- [ ] Hosting Information
- [ ] Admin Access
- [ ] Support Process

---

# 83. Project Closure Principle

Version 1 project may be considered formally delivered when:

**APPROVED SCOPE COMPLETE  
+ UAT PASSED  
+ FINANCIAL RECONCILIATION PASSED  
+ OPENING APPROVED  
+ PRODUCTION DEPLOYED  
+ BACKUP VERIFIED  
+ USERS TRAINED  
+ BUSINESS OWNER ACCEPTED**

---

# 84. Future Development

Project closure does not prevent future development.

Future requirements shall follow:

**REQUEST  
→ CLASSIFICATION  
→ DOCUMENTATION  
→ APPROVAL  
→ DEVELOPMENT  
→ TESTING  
→ DEPLOYMENT**

---

# 85. No Memory-Only Handover

The system shall not depend on the original developer remembering how the old Microsoft Access application worked.

The approved documentation, code and tests shall become the system knowledge base.

---

# 86. Final Handover Principle

The completed project shall leave the Business Owner with:

**A RUNNING SYSTEM  
+ APPROVED BUSINESS RULES  
+ TRACEABLE FINANCIAL LOGIC  
+ SOURCE CODE  
+ DATABASE BACKUP  
+ DOCUMENTATION  
+ RECOVERY PROCESS**

not merely a website that currently opens.

---

# 87. Document Authority

This document is the authoritative User Acceptance, Go-Live and Handover Standard for the Remittance Management System.

It does not override:

`01_REMITTANCE_BUSINESS_CONSTITUTION.md`

The Business Constitution remains the highest business authority.

---

# 88. Approval

**Status:** FINAL DRAFT — Pending Business Owner Approval

After Business Owner approval, this document becomes the official User Acceptance, Go-Live and Handover Standard for Version 1 and future controlled releases.

---

**END OF DOCUMENT**