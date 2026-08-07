# REMITTANCE MANAGEMENT SYSTEM — OPERATION, SUPPORT AND CHANGE MANAGEMENT STANDARD

**Document ID:** 12  
**File Name:** `12_OPERATION_SUPPORT_AND_CHANGE_MANAGEMENT_STANDARD.md`  
**Project:** Remittance Management System  
**Document Type:** Production Operation, Support and Change Management Standard  
**Version:** 1.0  
**Status:** FINAL DRAFT — Pending Business Owner Approval  
**Authority:** Business Owner  

---

# 1. Purpose

This document defines how the Remittance Management System shall be operated, supported and changed after production Go-Live.

It governs:

- Daily production operation
- Staff operational responsibility
- Admin review
- Daily reconciliation
- Month-end operation
- Support requests
- Bug handling
- Business-rule changes
- Feature requests
- Change Requests
- Emergency fixes
- Production updates
- Version management
- Client-request control
- Maintenance responsibility
- Historical integrity

This document exists to prevent the project from becoming an uncontrolled custom-development system after delivery.

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

No maintenance request shall override approved business rules without formal approval.

---

# 3. Production System Principle

After Go-Live, the production system becomes the official system for new operational transactions from the approved cut-off date.

The old Microsoft Access system remains:

**Legacy / Historical Reference**

New operational transactions shall not continue to be entered in both systems as a normal business process.

---

# 4. Daily Operational Responsibility

Staff shall use the new system for approved daily activities such as:

- Customer creation/search
- Remittance entry
- Deposit entry
- Withdrawal entry
- Service Charge entry
- Transaction Reference
- Note
- Attachment

Staff shall not maintain separate hidden financial records outside the system where the new system is designed to record those transactions.

---

# 5. Admin Daily Responsibility

Admin should review important operational information regularly.

Recommended daily review:

- Cash position
- Today's transactions
- Staff entries
- Service Charges
- Cancelled transactions
- Negative accounts
- Unusual transactions
- Account movement

---

# 6. Daily Cash Verification

Where practical, physical Cash shall be compared with the system Cash balance.

If:

`Physical Cash ≠ System Cash`

the difference shall be investigated.

The Current Balance shall not simply be manually edited to make the two values match.

---

# 7. Cash Difference Investigation

Potential causes may include:

- Missing transaction
- Incorrect amount
- Incorrect Service Charge
- Unrecorded Withdrawal
- Unrecorded Expense
- Duplicate transaction
- Wrong Account selection
- Cancelled transaction issue
- Staff entry error

The underlying cause should be identified first.

---

# 8. Financial Adjustment

Adjustment shall only be used when a legitimate difference remains after investigation and an authorized adjustment is required.

Adjustment shall contain:

- Date
- Account
- Amount
- Direction
- Reason
- Note
- Attachment where available
- Admin/User
- Audit history

Adjustment shall not become a shortcut for poor transaction entry.

---

# 9. Provider Balance Review

Admin should periodically compare system balances with available provider/account records.

Examples:

- City Express
- Citizen Remit
- IME
- BLB
- IPS
- Other operational services

Any discrepancy shall be reviewed.

---

# 10. Negative Balance Review

Negative balances are allowed where approved.

Therefore a negative balance is not automatically an error.

Admin should distinguish:

### Valid Negative Balance
Created by legitimate operational transactions.

### Incorrect Negative Balance
Created by missing or incorrect entries.

---

# 11. Month-End Review

At month-end, Admin should review:

- All provider balances
- Negative provider accounts
- Required settlements
- Provider Commission
- Service Charges
- Expenses
- Account balances
- Profit/Loss
- Cancelled transactions
- Adjustments

---

# 12. Month-End Settlement

Where required, Admin may settle negative remittance/provider accounts using approved Account Transfer.

Example:

City Express:

`NPR -80,000`

Bank → City Express:

`NPR 80,000`

Result:

City Express:

`NPR 0`

The settlement shall remain in transaction history.

---

# 13. Month-End Commission Review

Admin shall ensure Provider Commission records reflect actual received/approved amounts.

Commission shall not be assumed merely from transaction volume unless an approved automatic commission formula exists.

---

# 14. Expense Review

Admin should review Expense transactions for:

- Correct Category
- Correct Amount
- Correct Payment Account
- Supporting Note
- Supporting Attachment where applicable

---

# 15. Profit/Loss Review

Profit/Loss shall be reviewed from source transactions.

It shall not be manually entered as a single monthly Profit value.

Approved Income sources include:

- Service Charge
- Provider Commission
- Other approved Income

Approved Expenses reduce Profit.

---

# 16. Shareholder Operational Separation

Daily Staff operation shall remain separated from Shareholder ownership management.

Shareholder transactions shall not be mixed with ordinary Customer transaction entry.

---

# 17. Shareholder Review

Admin should periodically review:

- Active Shareholders
- Share Quantity
- Share Transfers
- Redemptions
- Exits
- Current Share Value
- Supporting documents

---

# 18. Shareholder Transaction Approval

Share transactions shall only be processed after the required business approval.

The system shall not treat an informal verbal request as sufficient where formal approval is required.

---

# 19. Support Request Categories

After Go-Live, requests shall be classified into one of the following categories:

1. Bug
2. Data Correction
3. User Support
4. Configuration Change
5. Existing Scope Clarification
6. New Feature
7. Business Rule Change
8. Emergency Incident

---

# 20. Bug Definition

A Bug exists when the system does not behave according to approved documentation.

Example:

Approved rule:

Remittance Principal = `10,000`

Service Charge = `100`

Expected:

Cash `+10,100`

City Express `-10,000`

If software produces:

Cash `+10,000`

this is a Bug.

---

# 21. New Feature Definition

A request is a New Feature when the system was never approved to provide that behavior.

Example:

Customer later asks:

`Send SMS automatically after every transaction.`

If this was not part of Version 1 scope:

**New Feature / Change Request**

It is not a Bug.

---

# 22. Business Rule Change Definition

A Business Rule Change occurs when the business intentionally changes an approved rule.

Example:

Old approved rule:

`1 Share = NPR 1,000`

New request:

`1 Share = NPR 1,500 from next year.`

This is a Business Rule Change.

It requires documentation and impact review before coding.

---

# 23. Existing Scope Clarification

A clarification does not create a materially new feature.

Example:

Existing rule requires Account Transfer but the exact label on the button needs clarification.

This may be resolved without treating it as a major new feature.

However, financial behavior must not change through a mere clarification.

---

# 24. Data Correction

Data Correction means a valid system function was used incorrectly or wrong information was entered.

Example:

Staff entered:

`NPR 15,000`

instead of:

`NPR 10,000`

This is not automatically a software Bug.

It shall use approved cancellation/correction workflow.

---

# 25. User Support

User Support includes situations such as:

- User does not know how to create a transaction.
- User forgot how to search Customer.
- User needs help using a report.
- User cannot remember the correct workflow.

Support does not automatically require code changes.

---

# 26. Configuration Change

Configuration changes may include:

- New Provider
- New Account
- New Expense Category
- User activation/deactivation

Where the system already supports these dynamically, they shall normally be configuration work rather than development work.

---

# 27. Change Request Principle

After Version 1 scope is frozen, every new development request shall be evaluated before implementation.

Required process:

**REQUEST → CLASSIFY → ANALYZE → ESTIMATE → APPROVE → DOCUMENT → DEVELOP → TEST → DEPLOY**

---

# 28. No Direct Customer-to-Code Rule

A Customer/Staff request shall not go directly into production code merely because somebody asked for it.

The request must first be classified.

---

# 29. Change Request Record

A Change Request should contain:

- Request ID
- Request Date
- Requested By
- Description
- Reason
- Business Impact
- Financial Impact
- Database Impact
- Permission Impact
- Report Impact
- Estimated Work
- Decision
- Version
- Note
- Attachment

---

# 30. Change Request Status

Suggested statuses:

- New
- Under Review
- Approved
- Rejected
- Planned
- In Development
- Testing
- Deployed
- Closed

---

# 31. Change Request Approval

Only approved Change Requests shall enter development.

The person requesting the change is not automatically the person authorized to approve it.

---

# 32. Business Rule Change Gate

If a Change Request modifies financial/business behavior:

1. Update Business Constitution if necessary.
2. Update relevant Standard.
3. Review database impact.
4. Review existing historical data.
5. Approve.
6. Develop.
7. Test.
8. Deploy.

Code shall not be changed first and documentation rewritten afterward.

---

# 33. Historical Data Protection

New business rules shall not silently rewrite old transactions.

Example:

If Share Value changes from:

`NPR 1,000`

to:

`NPR 1,200`

old Share Transactions remain at their historical applicable value.

---

# 34. Effective Date Rule

Where a new rule applies only from a future date, the change shall have an effective date.

Example:

New Share Value:

`NPR 1,200`

Effective:

`2083-04-01`

Transactions before that date shall retain old applicable rules where required.

---

# 35. Financial Logic Change Review

Any change affecting:

- Cash
- Bank
- BLB
- Remittance Accounts
- Service Charge
- Commission
- Expense
- Profit/Loss
- Shareholder quantity

shall receive financial impact review.

---

# 36. Database Change Review

Before modifying database structure, determine:

- Is the change required?
- Can existing dynamic architecture support it?
- Does historical data need migration?
- Can rollback be performed?
- Does backup exist?

---

# 37. Dynamic Configuration First

Where a request can be handled through existing masters, do not create unnecessary custom code.

Example:

New remittance provider:

Use Provider/Account Master.

Do not add a new database column and custom module for every provider.

---

# 38. Customer-Specific Customization

Customer-specific customization shall be carefully controlled.

A request unique to one customer should not automatically change the core architecture.

If customer-specific behavior is required, determine whether it should be:

- Configuration
- Optional feature
- Separate customization
- Future product enhancement

---

# 39. Core System Protection

The core financial engine shall not be modified casually for visual or customer-specific convenience.

Core includes:

- Ledger
- Balance logic
- Cancellation
- Financial atomicity
- Share movement
- Permissions
- Audit

---

# 40. Bug Priority

Bugs may be classified:

### Critical
Financial corruption or security failure.

### High
Core business workflow cannot operate.

### Medium
Incorrect non-critical report/workflow.

### Low
Minor UI or convenience issue.

---

# 41. Critical Bug Examples

Examples include:

- Wrong Cash balance
- Wrong provider balance
- Transaction posted twice
- Cancellation fails to reverse
- Share transfer loses shares
- Shareholder sees another shareholder's private data
- Staff gains unauthorized Admin access

---

# 42. Critical Incident Rule

If a Critical Bug threatens financial integrity:

- Stop the affected workflow if necessary.
- Preserve data.
- Investigate.
- Backup.
- Fix in test environment.
- Verify.
- Deploy controlled fix.
- Reconcile affected transactions.

---

# 43. Emergency Fix

Emergency production fixes may be required for Critical incidents.

Even emergency fixes shall follow as much as possible:

**BACKUP → IDENTIFY → FIX → TEST → DEPLOY → VERIFY → DOCUMENT**

---

# 44. No Emergency Feature

A requested new convenience feature is not an emergency simply because the user wants it quickly.

Emergency classification is reserved for genuine operational/security/financial incidents.

---

# 45. Production Update Rule

Normal production updates shall not be deployed directly while coding.

Recommended sequence:

1. Development
2. Testing
3. Backup
4. Deployment
5. Database migration if required
6. Verification
7. Release note

---

# 46. Release Version

Each production release should have a version.

Example:

`v1.0.0`

`v1.0.1`

`v1.1.0`

---

# 47. Version Meaning

Recommended convention:

### Major
Large breaking/business architecture change.

Example:

`v2.0.0`

### Minor
New approved features.

Example:

`v1.1.0`

### Patch
Bug fixes/small corrections.

Example:

`v1.0.1`

---

# 48. Release Notes

Each production release should record:

- Version
- Date
- Changes
- Bug fixes
- New features
- Database changes
- Known issues
- Deployment result

---

# 49. Database Backup Before Release

A recent recoverable database backup shall exist before high-risk production releases.

---

# 50. Attachment Backup Before Release

Where application changes may affect storage/attachments, relevant file backups shall also be verified.

---

# 51. Production Rollback

If a release causes serious failure, the team should have a rollback/recovery strategy.

Rollback shall consider:

- Application code
- Database migration
- New transactions entered after deployment

Financial data shall not be lost merely to restore old code.

---

# 52. User Training

Before users receive new workflows, they should be informed/trained.

This is especially important for:

- Staff daily transaction changes
- Cancellation procedure
- New required fields
- Shareholder workflow

---

# 53. Staff Rule Change Communication

If Staff workflow changes, Staff shall be informed before the new version becomes mandatory.

---

# 54. Admin Rule Change Communication

Admin shall understand the financial effect of new/changed functions before production use.

---

# 55. Shareholder Portal Changes

Changes affecting what Shareholders can see shall be reviewed for privacy and permission impact.

---

# 56. Daily Backup Review

Backup processes shall be checked regularly.

The existence of a configured backup job does not guarantee successful backup.

---

# 57. Backup Failure Handling

If backup fails:

- Identify reason.
- Correct issue.
- Run a replacement backup.
- Verify backup file.
- Record repeated failures.

---

# 58. Storage Monitoring

Admin/technical support should monitor:

- Database size
- Attachment storage
- Backup storage
- Server disk capacity

---

# 59. SSL/Domain Monitoring

The production domain and SSL certificate shall be monitored for expiry/renewal.

Unexpected expiry shall be avoided.

---

# 60. Hosting Renewal

Hosting renewal date shall be tracked.

The application shall not go offline because the hosting renewal was forgotten.

---

# 61. Support Log

Important support cases should be recorded.

Suggested information:

- Date
- User
- Problem
- Category
- Priority
- Resolution
- Related Transaction
- Related Version

---

# 62. Financial Support Case

If a user reports:

`Balance is wrong`

support shall not immediately alter the balance.

Required approach:

1. Identify Account.
2. Obtain expected balance.
3. Review ledger.
4. Identify transaction difference.
5. Correct source if necessary.
6. Reconcile.

---

# 63. User Entry Error

User mistakes shall use the system's approved correction process.

Developers shall not directly modify production database rows for every normal user mistake.

---

# 64. Production Database Manual Change

Direct database updates are emergency/technical operations, not normal support workflow.

Any unavoidable direct change shall be:

- Authorized
- Backed up
- Documented
- Reconciled
- Auditable where possible

---

# 65. Support Responsibility Boundary

The software provider/developer is responsible for software behavior according to approved rules.

The business users remain responsible for:

- Correct real-world amounts
- Correct Customer selection
- Correct provider selection
- Correct supporting information
- Physical Cash custody
- Business approvals

---

# 66. Historical Legacy Support

The old Access software may remain available for historical reference.

The new system support scope does not automatically include rebuilding or correcting every old historical transaction.

---

# 67. Legacy Data Request

If the Customer later requests full historical migration:

This shall be a separate project/change scope.

It requires:

- Data audit
- Mapping
- Reconciliation
- Migration testing
- Approval

It shall not be treated as a small support task.

---

# 68. New Integration Request

Future requests such as:

- Bank API
- Remittance API
- SMS
- WhatsApp
- Mobile App
- External accounting integration

shall be treated as separate feature/integration scope unless previously approved.

---

# 69. Scope Expansion Control

Statements such as:

`यो पनि सानो कुरा हो`

or:

`एक बटन मात्रै थप्नु हो`

shall not determine development effort.

Technical/business impact shall determine whether it is a Change Request.

---

# 70. No Unlimited Customization Principle

Production delivery does not imply unlimited future custom development.

New functionality shall follow approved change-management process.

---

# 71. Reusable Product Principle

Where a requested improvement is generally useful and consistent with the product's approved business purpose, it may become a reusable core feature.

Where it is specific to only one customer, customization impact shall be evaluated separately.

---

# 72. Documentation Maintenance

Project documentation shall be updated as approved rules evolve.

Documents shall not become obsolete while code changes independently.

---

# 73. Document Versioning

When an authoritative document changes:

- Version shall increase.
- Change should be summarized.
- Effective date may be recorded where relevant.

---

# 74. Deprecated Rule

When a business rule is replaced, old documentation shall not simply disappear without trace where historical interpretation matters.

A change history may indicate:

- Old Rule
- New Rule
- Effective Date

---

# 75. Testing After Business Change

Any approved business-rule change must include targeted testing.

Example:

Withdrawal Service Charge behavior changes.

Test:

- Cash
- BLB
- Service Charge
- Reports
- Cancellation
- Staff workflow

---

# 76. Regression Test

Changes to common financial code require regression testing of related modules.

Example:

Account Transfer engine changes.

Re-test:

- Settlement
- Commission receiving account where relevant
- Share Redemption payment
- Account ledger

---

# 77. Permission Change

If a role receives a new permission:

Review:

- Screen access
- Route access
- API access
- Report access
- Attachment access
- Audit

---

# 78. Staff Permission Expansion

Staff shall not gain Admin-level functionality casually.

Any Staff permission expansion requires explicit business approval.

---

# 79. Shareholder Permission Expansion

Any expansion of Shareholder visibility shall consider privacy of:

- Other Shareholders
- Company Accounts
- Customer data
- Profit/Loss
- Attachments

---

# 80. User Deactivation Support

When Staff leaves:

- Deactivate User.
- Do not delete User.
- Preserve transactions.

When Shareholder exits:

- Preserve Shareholder history.
- Login policy follows approved rule.

---

# 81. Account Deactivation Support

When a provider/account is no longer used:

- Deactivate.
- Do not delete historical account.
- Preserve ledger and reports.

---

# 82. New Provider Setup

Adding a new provider should normally require:

1. Create Provider.
2. Create/link Financial Account.
3. Configure negative-balance behavior.
4. Configure status.
5. Test one transaction.

No database redesign should normally be required.

---

# 83. New Expense Category

A new Expense Category should be created through master configuration.

No code/database-column change should normally be required.

---

# 84. Business Continuity

If the production server becomes temporarily unavailable, the business should have an agreed temporary procedure.

Any offline/manual transactions recorded during downtime must later be entered carefully into the system with proper Business Date and audit context.

---

# 85. Downtime Entry

Transactions entered after system restoration for a downtime period should preserve:

- Actual Business Date
- Correct User
- Reference
- Note if needed

Backdating rules must accommodate approved recovery procedures.

---

# 86. No Duplicate Recovery Entry

Before entering transactions recorded manually during downtime, Staff/Admin must check that the transaction was not already successfully saved before the outage.

---

# 87. Server Recovery

After server recovery:

- Verify latest backup.
- Verify application version.
- Verify database.
- Verify attachments.
- Verify account balances.
- Verify recent transaction sequence.

---

# 88. Post-Recovery Reconciliation

After a serious incident/recovery, Admin should reconcile:

- Cash
- Bank
- Key provider accounts
- Recent Service Charges
- Recent cancellations

---

# 89. Maintenance Window

High-risk updates may be deployed during a controlled maintenance period to reduce interference with live transactions.

---

# 90. User Notification for Maintenance

Where an update requires downtime, users should be informed in advance where practical.

---

# 91. Development Environment Rule

Production data shall not be casually copied into local development.

If real data is required for diagnosing a problem:

- Minimize data copied.
- Protect it.
- Remove it after use where practical.

---

# 92. Test Data Separation

Test transactions must not appear in live production financial reports.

---

# 93. Support Access

If technical support requires temporary elevated access:

- Access should be authorized.
- Use should be limited.
- Credentials should not be shared unnecessarily.
- Actions affecting financial data must remain controlled.

---

# 94. Developer Account

If a separate developer/support login exists in production, it shall not automatically have unrestricted financial permission unless required and approved.

---

# 95. Business Owner Control

The Business Owner/Admin should retain sufficient control over:

- Admin access
- Hosting access
- Domain
- Backup
- Production ownership

The system should not become inaccessible if one developer is unavailable.

---

# 96. Documentation Backup

Project documentation should also be backed up/version-controlled.

The business logic should not exist only in chat history or developer memory.

---

# 97. Source Code Backup

Source code shall be stored in version control and backed up.

Production server should not be the only copy of source code.

---

# 98. Deployment Documentation

Production setup information shall record:

- Application version
- Server
- Domain
- Database identifier
- Deployment path
- Backup process
- Scheduler/cron requirements

Do not store public secrets in these documents.

---

# 99. Future Developer Handover

A future developer should be able to understand the system by reading the approved documentation.

They shall not need to reverse-engineer core financial logic from code alone.

---

# 100. AI Maintenance Rule

Any AI coding agent used after Go-Live shall be instructed to:

- Read relevant documentation first.
- Inspect only relevant code.
- Never invent financial rules.
- Never directly change unrelated modules.
- Never alter production financial logic without approval.
- Preserve audit/history.
- Report ambiguity before coding.

---

# 101. Maintenance Acceptance

A maintenance/change task is complete only when:

- Requirement is understood.
- Documentation is updated where needed.
- Code is implemented.
- Tests pass.
- No financial regression occurs.
- Backup exists where required.
- Deployment succeeds.
- Production behavior is verified.

---

# 102. Final Operational Principle

The production system shall operate under this principle:

**TRANSACTION FIRST  
→ TRACEABLE ACCOUNT EFFECT  
→ AUDITABLE USER ACTION  
→ CONTROLLED CORRECTION  
→ NO SILENT HISTORY CHANGE**

---

# 103. Change Management Principle

Future development shall operate under:

**REQUEST  
→ BUSINESS DECISION  
→ DOCUMENTATION  
→ SCOPE  
→ DEVELOPMENT  
→ TESTING  
→ DEPLOYMENT  
→ VERIFICATION**

---

# 104. Document Authority

This document is the authoritative post-Go-Live Operation, Support and Change Management Standard.

It does not replace the Business Constitution.

Highest business authority remains:

`01_REMITTANCE_BUSINESS_CONSTITUTION.md`

---

# 105. Approval

**Status:** FINAL DRAFT — Pending Business Owner Approval

After approval, this document becomes the official standard for production operation, maintenance, support and future change management of the Remittance Management System.

---

**END OF DOCUMENT**