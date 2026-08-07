# REMITTANCE MANAGEMENT SYSTEM — MASTER DATA AND SYSTEM CONFIGURATION STANDARD

**Document ID:** 17  
**File Name:** `17_MASTER_DATA_AND_SYSTEM_CONFIGURATION_STANDARD.md`  
**Project:** Remittance Management System  
**Document Type:** Master Data and System Configuration Standard  
**Version:** 1.0  
**Status:** FINAL DRAFT — Pending Business Owner Approval  
**Authority:** Business Owner  

---

# 1. Purpose

This document defines the approved rules for creating, maintaining and controlling Master Data and System Configuration in the Remittance Management System.

It governs:

- Business Profile
- Financial Accounts
- Account Categories
- Remittance Providers
- Operational Services
- Expense Categories
- Transaction Types
- Share Value configuration
- User configuration
- Status management
- Financial Year settings
- Date settings
- Currency settings
- Numbering settings
- Note and Attachment behavior
- Master record activation/deactivation
- Configuration changes
- Historical-data protection

The purpose is to ensure that normal business expansion can be handled through configuration instead of repeated database and source-code changes.

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

If this document conflicts with the Business Constitution, the Business Constitution shall prevail.

---

# 3. Core Master Data Principle

Business entities that may change over time shall be maintained as configurable Master Data wherever appropriate.

Examples:

- Financial Accounts
- Providers
- Expense Categories
- Users
- Shareholders
- Other approved categories

The system shall not require source-code or database-column changes for ordinary additions of these records.

---

# 4. Master Data vs Transaction Data

The system shall distinguish:

### Master Data

Defines reusable business entities.

Examples:

- City Express
- Citizen Remit
- Cash Account
- Bank Account
- Rent Expense Category

### Transaction Data

Records actual business activity.

Examples:

- Customer remittance
- Deposit
- Withdrawal
- Commission receipt
- Expense
- Share Transfer

Master records define available choices.

Transactions preserve actual historical events.

---

# 5. Business Profile

The system shall maintain a Business Profile.

Possible fields include:

- Business Name
- Registration Name where applicable
- Address
- Contact Number
- Email
- Logo
- Currency
- Financial Year setting
- Note
- Supporting document where required

---

# 6. Business Name

The Business Name shall be configurable by authorized Admin.

Changing the Business Name shall not alter historical financial transactions.

---

# 7. Business Logo

The system may support a Business Logo for:

- Dashboard
- Reports
- Printed statements
- Transaction receipts

Logo upload shall follow approved Attachment/File security rules.

---

# 8. Currency Setting

Initial approved operating currency is expected to be:

**NPR — Nepalese Rupees**

Unless multi-currency is specifically approved, the system shall operate as single-currency.

---

# 9. Currency Change Restriction

Changing the default currency after live financial transactions exist is a high-impact configuration change.

It shall not silently reinterpret historical NPR amounts as another currency.

---

# 10. Account Category Master

Account Categories define the nature of Financial Accounts.

Initial categories may include:

1. Cash
2. Bank
3. Remittance
4. BLB / Banking Service
5. Operational Service
6. Other Financial Account

---

# 11. Account Category Fields

An Account Category may contain:

- ID
- Name
- Description
- Status
- Note
- Created By
- Created At

---

# 12. Account Category Deactivation

A category used by historical Accounts shall normally be deactivated rather than deleted.

Historical Accounts shall retain their relationship.

---

# 13. Financial Account Master

Financial Accounts shall be created through Account Master.

Examples:

- Cash
- Asha Enterprises Bank
- BLB
- City Express
- Citizen Remit
- IME Remit
- IME Pay
- IPS

---

# 14. Financial Account Fields

An Account may contain:

- Account Name
- Account Category
- Account Number where applicable
- Provider relationship where applicable
- Negative Balance Allowed
- Status
- Note
- Attachment
- Created By
- Created At

---

# 15. Current Balance Exclusion from Master Editing

Current Balance shall not be treated as an ordinary editable Account Master field.

Admin shall not modify the current balance simply by editing Account Master.

Financial transactions control balance movement.

---

# 16. Opening Balance Separation

Opening Balance shall be entered through the controlled Opening Balance workflow.

It shall not be mixed with ordinary Account profile editing.

---

# 17. Negative Balance Setting

Each applicable Financial Account may contain a configuration such as:

**Allow Negative Balance: Yes / No**

This enables business rules without hard-coding specific Provider names.

---

# 18. Negative Balance Configuration Change

Changing an Account from:

`Allow Negative = Yes`

to:

`No`

shall not alter historical negative balances.

It affects future transaction validation.

---

# 19. Cash Account

At least one approved Cash Account shall exist before daily transaction operation begins.

The system shall clearly identify which Account is used for physical Cash.

---

# 20. Multiple Cash Accounts

Version 1 shall not assume multiple Cash Accounts unless explicitly approved.

Architecture may allow future expansion.

---

# 21. Bank Account Master

The business may maintain one or more Bank Accounts.

Bank Account information may include:

- Bank Name
- Account Name
- Account Number
- Branch
- Status
- Note
- Attachment

---

# 22. Bank Account Security

Sensitive bank information shall only be displayed to authorized roles.

---

# 23. Provider Master

Providers represent Remittance or operational service companies.

Examples:

- City Express
- Citizen Remit
- IME Remit
- IME Pay
- BLB
- IPS

---

# 24. Provider Fields

Provider Master may contain:

- Provider Name
- Provider Type
- Linked Financial Account
- Status
- Note
- Attachment
- Created By
- Created At

---

# 25. Provider Type

Provider Type may classify a service as:

- Remittance
- Banking/BLB
- Digital Payment
- Other Operational Service

Exact categories may be adjusted during configuration.

---

# 26. Provider Linked Account

Where a Provider maintains a balance, the Provider should link to an appropriate Financial Account.

Example:

Provider:

`City Express`

Linked Account:

`City Express Account`

---

# 27. Provider Without Balance

If a future Provider does not maintain an internal operational balance, the system may allow it without a linked balance Account where business rules permit.

---

# 28. New Provider Creation

Adding a new Provider should normally require:

1. Create Provider.
2. Create/link Account if required.
3. Configure negative-balance behavior.
4. Set Active.
5. Test transaction behavior.

No new database column should normally be required.

---

# 29. Provider Deactivation

When a Provider is no longer used:

- Mark Provider Inactive.
- Prevent new transactions.
- Preserve historical transactions.
- Preserve linked Account history.

---

# 30. Provider Deletion

A Provider with historical transactions shall not be hard-deleted through ordinary operation.

---

# 31. Expense Category Master

Expense Categories shall be dynamic.

Initial examples:

- Rent
- Salary
- Electricity
- Internet
- Office Expense
- Transport
- Maintenance
- Other Expense

---

# 32. Expense Category Fields

Possible fields:

- Category Name
- Description
- Status
- Note
- Created By
- Created At

---

# 33. New Expense Category

Admin may add a new Expense Category without source-code modification.

---

# 34. Expense Category Deactivation

An Expense Category used in historical records shall normally become Inactive instead of being deleted.

---

# 35. Income Category

Initial approved Income sources are:

- Customer Service Charge
- Provider Commission

If other Income Categories are required later, they shall follow approved Business Rules.

---

# 36. Service Charge Is Not a Master Account

Service Charge is a transaction value/income component.

It shall not be treated as a Provider Account simply because it is reported separately.

---

# 37. Commission Master Concept

Provider Commission shall use Provider Master.

The system shall not require a separate Commission Category for every Provider.

---

# 38. Transaction Type Configuration

Core Transaction Types are controlled system concepts.

Initial types include:

- Opening Balance
- Remittance
- Deposit
- Withdrawal
- Account Transfer
- Settlement
- Commission Receipt
- Expense
- Share Redemption
- Adjustment
- Cancellation/Reversal where required

---

# 39. Transaction Type Restriction

Admin shall not freely create arbitrary Financial Transaction Types from the UI if their account effects are undefined.

A new financial Transaction Type requires approved Business Rules and implementation.

---

# 40. Dynamic Master vs Controlled System Type

The system shall distinguish:

### Dynamic Master

Can be added through configuration.

Examples:

- Provider
- Account
- Expense Category

### Controlled System Type

Requires business logic.

Examples:

- Remittance
- Withdrawal
- Share Redemption

A new controlled system type is not just Master Data.

---

# 41. User Master

User Management shall support:

- Admin
- Staff
- Shareholder login linkage

Each user shall have an individual identity.

---

# 42. User Fields

Possible User fields include:

- Name
- Login Identifier
- Role
- Status
- Related Shareholder ID where applicable
- Contact information
- Created At
- Last Login where implemented

---

# 43. User Role Restriction

The system shall contain the three approved primary roles:

1. Admin
2. Staff
3. Shareholder

Admin shall not create undefined new roles unless the Role Standard is changed.

---

# 44. User Deactivation

When a user leaves or should lose access:

**Deactivate**

Do not delete historical user identity.

---

# 45. Shareholder Master Configuration

Shareholder is business master data but ownership is transaction-based.

Shareholder Profile may be edited where permitted.

Share Quantity shall not be edited through ordinary Shareholder profile configuration.

---

# 46. Share Value Configuration

Current baseline:

**1 Share Unit = NPR 1,000**

The system may maintain current Share Value as a controlled business setting.

---

# 47. Share Value Historical Protection

Changing Current Share Value shall not alter historical:

- Opening Share records
- Share Transfers
- Redemptions
- Historical Valuations

where the transaction stored the applicable value.

---

# 48. Share Value Permission

Only authorized Admin may modify the current Share Value after approved business decision.

It shall not be editable by:

- Staff
- Shareholder

---

# 49. Share Value Effective Date

If Share Value changes are permitted, configuration should include an effective date.

Example:

Value:

`NPR 1,200`

Effective From:

`__________`

---

# 50. Financial Year Setting

The system may support Nepali Financial Year where approved.

Financial Year configuration shall be centralized.

---

# 51. Financial Year Master

If a Financial Year Master is implemented, possible fields include:

- Name
- Start Date
- End Date
- Status
- Is Current
- Closed Status where applicable

---

# 52. Current Financial Year

Only one Financial Year should normally be identified as current for operational convenience.

This shall not override Business Date rules.

---

# 53. Financial Year Closing

Formal Financial Year/Month closing requires approved period-control rules.

Master configuration shall not introduce period locking before those rules are approved.

---

# 54. Date Format Setting

The UI may support:

- Nepali Date
- English Date
- Both

according to final approved configuration.

---

# 55. Database Date Standard

Regardless of UI display, internal date storage shall remain consistent.

---

# 56. Transaction Number Setting

The system may maintain a controlled internal Transaction Number format.

Possible pattern:

`TRX-YYYY-000001`

Exact format shall be finalized before implementation.

---

# 57. Transaction Number Uniqueness

Generated internal Transaction Numbers shall be unique.

---

# 58. Provider Reference vs Internal Number

Provider/Bank reference shall remain separate from the system's internal Transaction Number.

---

# 59. Business Status Master Principle

Statuses should be controlled and predictable.

Examples:

### User
- Active
- Inactive

### Account
- Active
- Inactive

### Shareholder
- Active
- Inactive
- Exited

### Financial Transaction
- Active
- Cancelled

---

# 60. Status Creation Restriction

Admin shall not create arbitrary transaction statuses that alter financial behavior without approved workflow.

---

# 61. Note Configuration

All major records shall support Note according to the global rule.

Note length limits may be technically configured.

---

# 62. Attachment Configuration

System configuration may define:

- Maximum file size
- Allowed file types
- Maximum number of attachments
- Storage location

These settings must follow Security Standard.

---

# 63. Attachment Allowed Types

Initial proposed types may include:

- PDF
- JPG
- JPEG
- PNG

Final list shall follow Business Owner decision.

---

# 64. Attachment File Size

Exact maximum size remains to be approved.

The selected value shall consider:

- Hosting storage
- Backup size
- User experience
- Security

---

# 65. Attachment Count

Architecture should support multiple attachments.

Actual UI/file-count limit may be configurable.

---

# 66. Report Configuration

Some report defaults may be configured, such as:

- Default Date Range
- Default Financial Year
- Rows Per Page
- Business Name/Logo on print

Financial formulas shall not be configurable through ordinary Settings.

---

# 67. Dashboard Configuration

Dashboard presentation may allow safe configuration later.

However, configuration shall not change the underlying financial meaning.

---

# 68. System Settings Permission

Only Admin shall manage System Settings.

Staff and Shareholder shall not access system-wide configuration.

---

# 69. High-Risk Configuration

The following are high-impact settings:

- Current Share Value
- Currency
- Financial Year
- Negative-balance behavior
- Opening configuration
- Transaction numbering
- Account status

Changes shall be audited.

---

# 70. Configuration Audit

Important configuration changes should record:

- Changed By
- Changed At
- Previous Value
- New Value
- Reason where appropriate

---

# 71. No Silent Configuration Change

Business-critical configuration shall not change through background code without an identifiable authorized event.

---

# 72. Master Record Created By

Master records should preserve:

- Created By
- Created At

where appropriate.

---

# 73. Master Record Updated By

Important Master records should preserve:

- Updated By
- Updated At

where appropriate.

---

# 74. Duplicate Account Control

Before creating an Account, the system should check for potentially duplicate active Account names.

---

# 75. Duplicate Provider Control

Before creating a Provider, the system should detect obvious duplicate Provider names.

---

# 76. Duplicate Expense Category Control

The system should prevent confusing duplicate active Expense Categories.

Example:

`Office Rent`

and another exact:

`Office Rent`

unless there is a legitimate distinction.

---

# 77. Rename Master Record

Renaming a Master record shall preserve its ID and historical relationships.

Example:

Provider display name correction shall not create a new financial history.

---

# 78. Historical Display After Rename

Historical transactions may display the current Master name unless the business requires historical name snapshotting.

Financial transaction identity remains linked by stable ID.

---

# 79. Merge Master Records

Merging two Accounts or Providers with financial history is high risk.

The system shall not provide casual Merge functionality in Version 1.

---

# 80. Account Type Change

Changing an existing Account Category after transactions exist may affect reporting.

Such changes shall require careful Admin review.

---

# 81. Cash Account Change

Changing which Account represents primary Cash is high impact.

It shall not retroactively move historical Cash transactions.

---

# 82. Provider Account Re-Link

Changing a Provider's linked Financial Account shall apply only according to an approved effective behavior.

Historical transactions must retain their actual Account effects.

---

# 83. Master Data Import

Initial Master Data may be configured manually or imported through controlled setup where useful.

It shall be verified before Go-Live.

---

# 84. Initial Account Setup

Before Opening Balance entry, Admin shall create all required Accounts.

At minimum verify:

- Cash
- Main Bank
- BLB
- Active Remittance Providers
- Other operational Accounts

---

# 85. Initial Provider Setup

Before live transaction entry, all currently used Providers shall be configured.

---

# 86. Initial Expense Category Setup

Common Expense Categories should be configured before live Expense operation.

New categories may be added later.

---

# 87. Initial User Setup

Before Go-Live:

- Required Admin account
- Staff accounts
- Shareholder login accounts where required

shall be configured.

---

# 88. Initial Shareholder Setup

Existing Shareholders shall be created before Opening Share Holdings are entered.

---

# 89. Master Setup Sequence

Recommended sequence:

1. Business Profile
2. Currency/Date Settings
3. Roles
4. Users
5. Account Categories
6. Financial Accounts
7. Providers
8. Expense Categories
9. Shareholders
10. Share Value
11. Opening Balances
12. Opening Share Holdings

---

# 90. Configuration Before Opening

All required Account/Provider masters must exist before Opening Balances can be finalized.

---

# 91. Inactive Account and Opening

An Account receiving an Opening Balance should normally be Active at Go-Live unless it is being preserved only for a specific approved reason.

---

# 92. Default Values

Safe default values may be used.

Examples:

- Status = Active
- Service Charge = 0 on transaction form

Defaults shall not create hidden financial assumptions.

---

# 93. Configuration Validation

System Settings shall validate allowed values.

Examples:

- Share Value cannot be invalid negative amount.
- File Size limit must be positive.
- Financial Year Start must precede End.
- Account name cannot be blank.

---

# 94. No Free-Text Role

Role values shall use controlled options.

Users shall not type arbitrary Role names into free-text fields.

---

# 95. No Free-Text Account Category Where Master Exists

Account Category shall be selected from approved categories.

---

# 96. No Free-Text Provider Where Master Exists

Operational transactions shall select Provider from Provider Master rather than entering Provider names repeatedly as free text.

---

# 97. No Free-Text Expense Category

Expense shall use Expense Category Master.

---

# 98. Reference Number Exception

Reference Number may remain free text because external Providers may use varying formats.

---

# 99. Particular / Description

Expense Particular/Description may remain free text.

---

# 100. Configuration Export

Admin may eventually export Master Data configuration for backup/reference.

This is optional unless specifically required.

---

# 101. Configuration Backup

System Master Data shall be included in normal database backups.

Separate manual configuration backup is not required if database backup is reliable.

---

# 102. Production Configuration Change Rule

Configuration changes in production shall follow:

**REVIEW  
→ AUTHORIZE  
→ CHANGE  
→ VERIFY**

High-risk changes should be documented.

---

# 103. Development Configuration

Development environment may contain dummy Providers/Accounts.

These must not automatically seed inappropriate test data into production.

---

# 104. Production Seed Rule

Production seeders shall only create safe approved fixed system configuration.

They shall not overwrite live Master Data.

---

# 105. No Hard-Coded Provider Seed Dependency

Application functionality shall not fail merely because Provider ID numbers differ between environments.

---

# 106. Settings Cache

If application settings are cached for performance, cache must be refreshed correctly after authorized changes.

Historical financial records remain unaffected.

---

# 107. Configuration Failure Safety

If a critical configuration is missing, the system should fail safely.

Example:

If Cash Account is not configured, Remittance posting should not guess another Account.

---

# 108. Missing Provider Account

If a Provider requires a Financial Account but none is configured:

**Block transaction and show configuration error.**

Do not post an incomplete financial transaction.

---

# 109. Missing Expense Payment Account

Expense cannot save without a valid approved Payment Account.

---

# 110. Missing Share Value

If Share Value is required for a Share transaction and no approved value exists:

**Block the Share transaction.**

Do not assume `1000` from source code if business configuration has not been initialized.

---

# 111. Historical Snapshot Principle

Where a configurable value affects historical meaning, the transaction shall store the effective value used.

Examples:

- Share Value
- Potential future Service Charge rate
- Potential future Commission rate

---

# 112. Current Setting vs Historical Transaction

Current configuration answers:

**What rule applies now?**

Transaction snapshot answers:

**What rule/value applied when this transaction happened?**

The two shall not be confused.

---

# 113. Future Service Charge Master

If automatic Service Charge rules are approved later, they may use configurable rate masters.

Possible dimensions:

- Provider
- Transaction Type
- Fixed Amount
- Percentage
- Effective Date

This is future scope unless approved.

---

# 114. Future Commission Rate Master

If expected Provider Commission auto-calculation is approved later, Commission Rates may use an effective-date master.

It is not required for manual actual-commission entry.

---

# 115. Configuration Scope Control

Adding configuration fields merely because they may be useful someday shall be avoided.

Only approved or clearly necessary configuration shall be implemented.

---

# 116. Security of Settings

Settings endpoints shall require Admin authorization server-side.

---

# 117. Shareholder Settings Access

Shareholder shall not modify:

- Share Value
- Currency
- Accounts
- Providers
- Expense Categories
- Roles
- Global Settings

---

# 118. Staff Settings Access

Staff shall not manage system Master configuration.

Staff may select approved Active Masters during operations.

---

# 119. Inactive Master UI

Inactive records should:

- Remain visible in historical detail/report
- Not appear in normal new-entry selectors

---

# 120. Master Data Search

Admin Master screens should support simple search/filter where lists become large.

---

# 121. Master List Pagination

Large Master lists shall use pagination or efficient loading.

---

# 122. Master Attachment

Major Master records may support Attachment where relevant.

Examples:

- Account document
- Provider agreement
- Shareholder document

---

# 123. Master Note

Major Master records shall support Note according to approved global rule.

---

# 124. Configuration Testing

Testing shall verify:

- New Account appears in authorized selectors.
- Inactive Account disappears from new-entry selector.
- Historical Account remains visible.
- New Provider works without schema change.
- New Expense Category works without schema change.
- Staff cannot modify Masters.
- Shareholder cannot modify Masters.
- Share Value configuration is protected.
- Historical Share Value is preserved after current value change.
- Negative-balance setting affects future validation correctly.

---

# 125. Initial Go-Live Configuration Checklist

Before Opening finalization:

- [ ] Business Profile created
- [ ] Currency confirmed
- [ ] Date configuration confirmed
- [ ] Financial Year confirmed
- [ ] Admin user active
- [ ] Staff users active
- [ ] Shareholder users linked where required
- [ ] Account Categories created
- [ ] Cash Account created
- [ ] Bank Accounts created
- [ ] BLB Account created
- [ ] Remittance Accounts created
- [ ] Providers created
- [ ] Providers linked correctly
- [ ] Negative-balance settings confirmed
- [ ] Expense Categories created
- [ ] Shareholders created
- [ ] Share Value confirmed
- [ ] Transaction number format confirmed where required
- [ ] Attachment rules configured

---

# 126. Master Data Acceptance Rule

Master Data implementation is acceptable only when:

- Providers are dynamic.
- Accounts are dynamic.
- Expense Categories are dynamic.
- Historical Masters can be deactivated without losing history.
- Staff cannot alter administrative Masters.
- Shareholder cannot alter administrative Masters.
- Current Balances cannot be edited through Account Master.
- Share Quantity cannot be edited through Shareholder Master.
- Current Share Value is controlled.
- Historical Share values remain preserved.
- High-impact configuration changes are auditable.
- No normal business expansion requires fixed provider/expense database columns.

---

# 127. Developer Rule

Before creating a new database field or hard-coded configuration, the developer/AI shall first determine:

**Is this a true system rule, or should it be Master Data?**

If it is variable business data, prefer approved Master configuration.

---

# 128. No Master-Data Business Logic Invention

A configurable Master does not allow users to define new financial behavior.

Example:

Admin may create a new Provider.

Admin may not create a new arbitrary transaction formula from the UI unless a separate approved rule-engine feature exists.

---

# 129. Final Master Data Principle

The system shall be flexible where business names/categories change, but strict where financial behavior matters.

Therefore:

**VARIABLE BUSINESS ENTITY → CONFIGURATION**

**FINANCIAL BUSINESS RULE → APPROVED CODE/STANDARD**

---

# 130. Document Authority

This document is the authoritative Master Data and System Configuration Standard for the Remittance Management System.

It is subordinate to:

`01_REMITTANCE_BUSINESS_CONSTITUTION.md`

and shall guide setup, development, Go-Live and future configuration maintenance.

---

# 131. Approval

**Status:** FINAL DRAFT — Pending Business Owner Approval

After Business Owner approval, this document becomes the official Master Data and System Configuration Standard for the Remittance Management System.

---

**END OF DOCUMENT**