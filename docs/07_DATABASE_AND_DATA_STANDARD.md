# REMITTANCE MANAGEMENT SYSTEM — DATABASE AND DATA STANDARD

**Document ID:** 07  
**File Name:** `07_DATABASE_AND_DATA_STANDARD.md`  
**Project:** Remittance Management System  
**Document Type:** Database and Data Standard  
**Version:** 1.0  
**Status:** FINAL DRAFT — Pending Business Owner Approval  
**Authority:** Business Owner  

---

# 1. Purpose

This document defines the approved database and data-management principles for the Remittance Management System.

It governs:

- Database architecture
- Master tables
- Transaction tables
- Financial ledger structure
- Customer data
- Account data
- Service/provider data
- Commission data
- Expense data
- Shareholder data
- Share transaction data
- Opening balances
- Notes
- Attachments
- Audit data
- Cancellation/correction history
- Legacy data
- Data migration
- Data integrity
- Financial precision
- Foreign-key relationships
- Record lifecycle
- Backup-oriented data design

This document must comply with:

1. `01_REMITTANCE_BUSINESS_CONSTITUTION.md`
2. `02_PROJECT_SCOPE_AND_REQUIREMENTS.md`
3. `03_ACCOUNT_AND_TRANSACTION_STANDARD.md`
4. `04_COMMISSION_EXPENSE_AND_PROFIT_STANDARD.md`
5. `05_SHAREHOLDER_STANDARD.md`
6. `06_USER_ROLE_PERMISSION_STANDARD.md`
7. `00_PROJECT_INDEX.md`

If this document conflicts with the Business Constitution, the Business Constitution shall take precedence.

---

# 2. Core Database Principle

The new system shall use a clean database structure designed for the approved current business rules.

The database shall not reproduce the legacy Microsoft Access tables merely because they already exist.

Legacy data structure is a reference only.

The new database shall prioritize:

- Financial integrity
- Traceability
- Maintainability
- Security
- Extensibility
- Auditability

---

# 3. Database Technology Principle

The production system shall use a relational database appropriate for the selected web application architecture.

For Laravel-based implementation, MySQL/MariaDB or another approved relational database may be used.

Exact production database engine shall be finalized during deployment planning.

---

# 4. Database Naming Principle

Database tables and columns shall use:

- Clear names
- Consistent naming
- English technical naming
- Predictable singular/plural conventions
- No ambiguous legacy abbreviations

Examples of legacy-style names such as:

- `Comition`
- `Recev Meth`
- `Tarjection`

shall not be repeated in the new database.

Use clear terms such as:

- `service_charge`
- `provider_id`
- `reference_number`
- `transaction_type`

---

# 5. Primary Key Principle

Every core database record shall have a unique primary key.

Primary IDs shall not depend on:

- Customer name
- Account name
- Mobile number
- Transaction reference
- Shareholder name

Business identifiers and database primary keys are separate concepts.

---

# 6. Foreign Key Principle

Related records shall be connected using proper foreign-key relationships where technically appropriate.

Examples:

- Transaction → Customer
- Transaction → Account
- Transaction → User
- Expense → Expense Category
- Commission → Provider
- Share Transaction → Shareholder
- Attachment → Related Record

Relationships shall not rely only on repeated text names.

---

# 7. Core Data Areas

The database shall logically support the following primary data areas:

1. Users
2. Customers
3. Accounts
4. Account Categories
5. Providers/Services
6. Transactions
7. Financial Ledger Entries
8. Expense Categories
9. Expenses
10. Provider Commissions
11. Shareholders
12. Share Transactions
13. Share Holdings/Ledger
14. Notes
15. Attachments
16. Audit Records
17. System Settings
18. Legacy Import References where required

---

# 8. Users Table

The Users data structure shall support authenticated system users.

Typical fields may include:

- ID
- Name
- Email or Username
- Mobile where required
- Password Hash
- Role
- Status
- Last Login information where appropriate
- Created At
- Updated At

Passwords shall never be stored as plain text.

---

# 9. User Role Data

The system has three primary roles:

- Admin
- Staff
- Shareholder

Role storage may use:

- Controlled role field, or
- Role table/relationship

depending on implementation.

The architecture shall not create unnecessary complexity for only three primary roles.

---

# 10. User Status

Users shall support active/inactive status.

Deactivating a user shall not remove historical transactions created by that user.

Historical `created_by` references must remain valid.

---

# 11. Shareholder Login Relationship

A Shareholder business record and a login User record are related but not necessarily the same database entity.

Where a Shareholder has portal login access:

- User may link to Shareholder
- Shareholder remains a business entity
- Login deactivation shall not delete Shareholder history

---

# 12. Customer Table

Customer data shall be stored in a Customer Master.

Typical fields may include:

- ID
- Name
- Mobile
- Address
- Other approved reference information
- Status
- Note
- Created By
- Created At
- Updated At

Exact required fields shall be finalized during UI design.

---

# 13. No Customer Balance Column

The Customer table shall not contain a business balance field such as:

- current_balance
- wallet_balance
- receivable_balance
- payable_balance

under the approved current system.

Customer does not maintain financial balance.

---

# 14. Customer Transaction Relationship

Transactions may reference Customer using `customer_id`.

The customer relationship enables:

- Search
- History
- Reporting

It does not create a customer financial ledger.

---

# 15. Customer Duplicate Control

The database/application should support reasonable duplicate detection using information such as:

- Name
- Mobile
- Other available identifiers

Duplicate protection shall not automatically merge two different people.

---

# 16. Customer Legacy Import

Legacy Customer records may be imported from Microsoft Access after cleaning.

Imported customers should preserve:

- Original identity information where available
- New system primary key
- Optional legacy source ID where useful

A legacy ID may be stored only for traceability.

---

# 17. Account Categories

Account Categories shall identify the business nature of financial accounts.

Initial categories may include:

- Cash
- Bank
- Remittance
- BLB / Banking Service
- Operational Service
- Other

Categories shall be manageable without adding new columns to transaction tables.

---

# 18. Accounts Table

Financial Accounts shall be stored in an Account Master.

Typical fields may include:

- ID
- Account Name
- Account Category ID
- Provider/Bank relationship where applicable
- Account Number where applicable
- Allows Negative Balance
- Status
- Note
- Created By
- Created At
- Updated At

Opening Balance shall not necessarily be stored as an ordinary editable Account field.

---

# 19. Account Name Uniqueness

Account names should be distinguishable.

The system shall prevent confusing duplicate active accounts where appropriate.

Example:

Two active accounts both named:

`City Express`

should require clear distinction if both legitimately exist.

---

# 20. Cash Account Identification

The system shall clearly identify the primary Cash Account.

If the business later requires multiple Cash locations, architecture may support more than one Cash Account.

The first release should not assume multiple cash locations unless approved.

---

# 21. Negative Balance Configuration

The database should support identifying whether an Account is permitted to become negative.

Example field:

`allow_negative_balance`

This avoids hard-coding provider names into financial logic.

---

# 22. Provider / Service Master

Remittance and operational providers shall be maintained dynamically.

Examples:

- City Express
- Citizen Remit
- IME Remit
- IME Pay
- BLB
- IPS

Typical fields may include:

- ID
- Provider Name
- Provider Type
- Linked Account ID where applicable
- Status
- Note
- Created At
- Updated At

---

# 23. Provider and Account Separation

Provider and Account are related but conceptually different.

A Provider represents a service/company.

An Account represents a financial balance location.

The technical design may link a Provider to an Account where the business uses one operational balance per provider.

---

# 24. Dynamic Provider Principle

A new remittance provider shall not require adding a new column such as:

- city_express_amount
- citizen_amount
- ime_amount

to transaction or report tables.

Provider-specific information shall be represented by relationships.

---

# 25. Transaction Master Principle

The system shall use a controlled transaction architecture.

Each business transaction shall have a primary transaction record containing common information.

Typical information:

- Transaction ID
- Business Date
- Transaction Type
- Customer ID where applicable
- Provider/Service ID where applicable
- Principal Amount
- Service Charge
- Reference Number
- Status
- Note
- Created By
- Created At
- Cancelled By where applicable
- Cancelled At
- Cancellation Reason

---

# 26. Transaction Type

Transaction Type shall identify the business event.

Initial transaction types include:

- Opening Balance
- Remittance
- Deposit
- Withdrawal
- Account Transfer
- Commission Receipt
- Expense
- Month-End Settlement
- Shareholder Redemption
- Adjustment
- Correction/Reversal where required

The exact technical representation may use an enum/master table as appropriate.

---

# 27. Transaction Header and Ledger Separation

The system should distinguish:

### Business Transaction Record
What business event occurred.

and

### Financial Ledger Entries
How that event affected Financial Accounts.

This structure allows one transaction to affect multiple accounts safely.

---

# 28. Ledger Entry Principle

Each financial effect shall create ledger entries linked to the source transaction.

A ledger entry should identify:

- ID
- Transaction ID
- Account ID
- Direction / effect
- Amount
- Business Date
- Created At
- Status where required

The exact debit/credit technical implementation may differ, but the financial effect must remain unambiguous.

---

# 29. Ledger Effect Example — Remittance

Transaction:

Customer Remittance

Principal:

`NPR 10,000`

Service Charge:

`NPR 100`

Possible ledger effects:

- Cash `+10,000` principal
- Cash `+100` Service Charge
- City Express `-10,000`

All entries link to the same business transaction.

---

# 30. Ledger Effect Example — Transfer

Transfer:

Bank → City Express

Amount:

`NPR 50,000`

Ledger effects:

- Bank `-50,000`
- City Express `+50,000`

Both entries shall share the same source transaction.

---

# 31. Ledger Effect Example — Expense

Expense:

`NPR 20,000`

Paid from Cash.

Ledger effect:

- Cash `-20,000`

Expense information remains linked to the Expense/business transaction record.

---

# 32. Financial Atomicity

A business transaction and all required ledger entries shall be saved inside one database transaction.

If one required ledger entry fails, the entire transaction must roll back.

Partial financial posting is prohibited.

---

# 33. Current Balance Design

Current Account Balance may be:

- Calculated from ledger entries, or
- Maintained in a controlled balance field for performance

If stored, the Current Balance field shall never be independently editable through normal business forms.

The ledger remains the audit authority.

---

# 34. Balance Reconciliation

The system shall be able to reconcile:

`Opening Balance + Net Ledger Movement = Current Balance`

for each Financial Account.

Any inconsistency shall be treated as a system/data integrity issue.

---

# 35. Opening Balance Data

Opening Balance shall be represented as an auditable financial event.

It shall identify:

- Account
- Opening Date
- Amount
- Direction/sign
- Approval reference
- Note
- Attachment
- Created By

Opening Balance should contribute to the ledger.

---

# 36. Opening Balance Batch

The system may support one Opening Batch/Opening Session containing multiple account opening balances.

This may include:

- Cut-off Date
- Meeting reference
- Approval Note
- Attachment
- Individual Account Opening entries

This helps preserve the official starting position.

---

# 37. Opening Balance Lock

Once opening balances are finalized and live transactions begin, they shall not remain freely editable.

Corrections shall use an authorized adjustment/reopening process.

---

# 38. Remittance Data

A Remittance Transaction may contain:

- Customer ID
- Provider ID
- Operational Account ID
- Principal Amount
- Service Charge
- Reference Number
- Date
- Note
- Attachments
- Created By

Financial effect shall be represented in ledger entries.

---

# 39. Deposit Data

A Deposit Transaction may contain:

- Customer ID
- BLB/Bank Service Account
- Principal Amount
- Service Charge
- Reference
- Date
- Note
- Attachment
- Created By

---

# 40. Withdrawal Data

A Withdrawal Transaction may contain:

- Customer ID
- BLB/Bank Service Account
- Principal Amount
- Service Charge
- Reference
- Date
- Note
- Attachment
- Created By

The final Service Charge financial effect remains subject to approved business confirmation.

---

# 41. Account Transfer Data

Account Transfer shall identify:

- Send Account ID
- Receive Account ID
- Amount
- Date
- Reference
- Note
- Attachments
- Created By

Send and Receive accounts must be different.

---

# 42. Settlement Data

Month-End Settlement may use the same controlled Account Transfer infrastructure with a transaction purpose/type identifying it as Settlement.

This avoids unnecessary duplicate financial logic.

---

# 43. Service Charge Storage

Service Charge shall remain explicitly stored with the related operational transaction.

It shall not be inferred later from Cash difference.

This is required for Staff accountability and reporting.

---

# 44. Provider Commission Table

Provider Commission records shall be identifiable independently.

Typical fields may include:

- ID
- Business Date
- Provider ID
- Commission Amount
- Receiving Account ID
- Reference
- Note
- Status
- Created By
- Created At
- Cancellation fields

---

# 45. Commission Ledger Effect

A Commission Receipt shall create a Financial Ledger increase in the selected Receiving Account.

Commission income reporting shall derive from active Commission records.

---

# 46. Expense Category Table

Expense Categories shall be dynamic.

Typical fields:

- ID
- Category Name
- Status
- Note
- Created At
- Updated At

Historical category records shall remain available even if category becomes inactive.

---

# 47. Expense Table

Expense records may contain:

- ID
- Business Date
- Expense Category ID
- Payment Account ID
- Amount
- Particular
- Reference
- Note
- Status
- Created By
- Created At
- Cancellation fields

---

# 48. Expense Financial Effect

Expense posting shall create the corresponding reduction in the selected Financial Account ledger.

The Expense record and Account effect must remain linked.

---

# 49. Shareholder Table

Shareholder data shall be separate from Users.

Typical fields may include:

- ID
- Name
- Mobile
- Address
- Email
- Identification reference where required
- Join Date
- Status
- Note
- Created By
- Created At
- Updated At

---

# 50. Shareholder Current Quantity

Current Share Quantity may be stored for performance but shall not be manually editable through ordinary profile updates.

Share Ledger/Transactions remain the ownership authority.

---

# 51. Share Transaction Table

Share Transactions shall store:

- ID
- Business Date
- Transaction Type
- From Shareholder ID where applicable
- To Shareholder ID where applicable
- Shareholder ID where applicable
- Share Quantity
- Value Per Share
- Total Valuation
- Payment Account ID where applicable
- Reference
- Note
- Status
- Created By
- Created At
- Cancellation information

---

# 52. Share Transfer Data

A Shareholder-to-Shareholder Transfer shall preserve both:

- Seller
- Buyer

in the same controlled transaction context.

The database must prevent only one side of the ownership movement from being posted.

---

# 53. Share Ledger

The system may maintain Share Ledger entries for each Shareholder.

A Share Ledger entry may include:

- Share Transaction ID
- Shareholder ID
- Quantity In
- Quantity Out
- Resulting Quantity
- Applicable Value
- Date

This provides ownership traceability.

---

# 54. Share Redemption Data

Company Redemption shall link:

- Shareholder
- Share Quantity reduced
- Value per Share
- Settlement Amount
- Payment Account
- Share transaction
- Financial transaction/ledger effect

The ownership effect and Cash/Bank effect must be atomic.

---

# 55. Share Transfer with No Financial Account Effect

A shareholder-to-shareholder transfer shall not create a Cash/Bank ledger entry merely because the system calculates Share Valuation.

The valuation is informational/ownership-based, not company cash movement.

---

# 56. Profit/Loss Data Principle

Profit/Loss should be calculated from source transactions rather than stored as manually editable monthly values.

Source components include:

- Service Charge Income
- Provider Commission
- Other approved Income
- Expenses

---

# 57. Monthly Summary Tables

If performance requires cached monthly summary tables, those summaries shall remain reproducible from source transactions.

Summary tables shall never become the only source of financial truth.

---

# 58. Note Principle

Every major record type shall support Note either directly or through a related generic Note mechanism.

Where a simple single note is sufficient, a `note` field may be used.

---

# 59. Attachment Architecture

Attachments should be stored using a reusable attachment architecture rather than adding multiple attachment columns to every table.

An Attachment record may include:

- ID
- Related Record Type
- Related Record ID
- Original File Name
- Stored File Name/Path
- MIME Type
- File Size
- Uploaded By
- Uploaded At

Exact implementation may use a polymorphic relationship or equivalent approved design.

---

# 60. Multiple Attachments

The attachment architecture shall support multiple files per eligible record where required.

This avoids fields such as:

- attachment1
- attachment2
- attachment3

in core business tables.

---

# 61. Attachment Storage

Large uploaded files shall not normally be stored directly inside database binary fields unless specifically justified.

The database should store secure file metadata/path/reference.

Actual files shall be stored in approved application/storage infrastructure.

---

# 62. Attachment Security

The database shall not expose direct unrestricted public file paths for sensitive documents.

Attachment access must pass role/record authorization checks where required.

Detailed rules are defined in the Security Standard.

---

# 63. Attachment Deletion

Financial/share transaction attachments shall not automatically disappear when a transaction is cancelled.

Historical evidence must be preserved according to retention rules.

---

# 64. Audit Columns

Important tables should include appropriate audit fields such as:

- created_by
- created_at
- updated_by where needed
- updated_at
- cancelled_by
- cancelled_at
- cancellation_reason

Not every table requires every audit column, but important financial/share records shall remain traceable.

---

# 65. Created By Foreign Key

`created_by` should reference the responsible User where possible.

A deactivated user shall remain referenceable in historical records.

---

# 66. Audit Log

In addition to record-level audit columns, the system may maintain a dedicated Audit Log for sensitive actions.

Audit events may include:

- Login
- Opening Balance change
- Financial cancellation
- Adjustment
- Share Transfer
- Share Redemption
- Permission-sensitive actions

---

# 67. Audit Log Immutability

Ordinary users shall not edit/delete Audit Log entries.

Even Admin should not have normal UI capability to rewrite historical audit records.

---

# 68. Transaction Status

Core financial records shall support controlled status.

At minimum:

- Active
- Cancelled

Additional statuses may include:

- Pending
- Approved
- Rejected
- Reversed
- Corrected

only when approved workflow requires them.

---

# 69. Soft Delete vs Business Cancellation

Database soft delete and financial cancellation are different concepts.

A posted financial transaction shall not be made financially inactive simply by generic soft delete without executing approved reversal/cancellation logic.

---

# 70. Hard Delete Restriction

Financial and Share transactions shall not normally be hard-deleted.

Reference/master data may have more flexible deletion rules only if no historical records depend on them.

---

# 71. Master Record Deactivation

Accounts, Providers, Expense Categories and similar master records with historical use should be marked Inactive rather than deleted.

Historical relationships must remain intact.

---

# 72. Cancellation Data

Cancellation shall preserve:

- Original Record
- Original Financial Effect
- Cancellation User
- Cancellation Date/Time
- Cancellation Reason
- Reversal effect where applicable

---

# 73. Correction Data

Correction should preserve linkage between:

- Original Transaction
- Cancelled/Reversed Transaction
- Replacement Transaction

Possible fields may include:

- corrected_from_id
- replacement_transaction_id
- reversal_transaction_id

Exact schema shall be chosen during development.

---

# 74. Adjustment Data

Financial Adjustments must be distinguishable from normal business transactions.

Adjustment should include:

- Account
- Amount
- Direction
- Reason
- Supporting Note
- Attachment
- Authorized User
- Date

Adjustments shall not hide operational mistakes.

---

# 75. Business Date Storage

Every financial/share transaction shall store Business Date separately from Created At.

This allows a transaction to represent:

- Date of business event
- Date/time record was entered

without confusing the two.

---

# 76. Date Standard

The database should store dates using a technically reliable normalized format.

Nepali calendar display/reporting may be supported at application/report level as required.

The system shall avoid storing inconsistent free-text dates.

---

# 77. Financial Year

If Nepali Financial Year support is required, the system shall derive/map financial-year information consistently.

Legacy free-text values such as:

`2081-4`

shall not define the new architecture.

---

# 78. Financial Amount Data Type

Financial amounts shall use fixed-precision decimal storage.

Floating-point types such as approximate binary `float` shall not be used for monetary values.

---

# 79. Monetary Precision

The recommended initial financial precision shall support at least:

`DECIMAL(18,2)`

or an equivalent approved fixed-precision type.

If the business confirms all amounts are whole NPR values, display may omit unnecessary decimals while storage remains financially safe.

---

# 80. Share Quantity Data Type

Current rule assumes whole Share Units.

Share Quantity should use an integer-compatible data type.

Fractional Share Units shall not be supported unless later approved.

---

# 81. Share Value Data Type

Share Value and Share Valuation shall use fixed-precision financial types.

Current approved value:

`NPR 1,000 per share`

shall be stored as transaction-effective value where required.

---

# 82. Reference Number Storage

External transaction references shall be stored as text/string rather than purely numeric fields.

This supports references that may contain:

- Letters
- Leading zeroes
- Hyphens
- Other provider formats

---

# 83. Reference Uniqueness

Not every provider reference is guaranteed globally unique.

If uniqueness validation is applied, it should consider relevant scope such as:

- Provider
- Transaction Type
- Reference

The system shall not reject legitimate transactions based on an unsafe global uniqueness assumption.

---

# 84. Nullability Principle

Fields shall be mandatory only when required by business rules.

Examples:

- Customer may be required for Remittance/Deposit/Withdrawal.
- Customer may not be required for Expense.
- Provider may not be required for Account Transfer.
- Payment Account is required for Expense.

Database nullability should reflect actual business requirements.

---

# 85. Validation Layer

Data validation shall exist at the application level and, where practical, database constraints shall reinforce critical integrity.

Examples:

- Amount > 0 where required
- Share Quantity > 0
- Different Send/Receive Accounts
- Required foreign keys
- Valid role
- Valid status

---

# 86. Database Constraint Principle

Important data integrity should not depend solely on UI JavaScript.

Server-side validation and database constraints shall protect critical relationships.

---

# 87. Transaction Locking / Concurrency

The database implementation shall protect balances from corruption when two users perform transactions at the same time.

Critical financial updates should use appropriate:

- Database transaction
- Row locking
- Atomic update

strategies.

---

# 88. Concurrent Balance Example

If two Staff users simultaneously create transactions against City Express, both transactions must use a consistent balance update process.

One transaction shall not overwrite the other's balance effect.

---

# 89. Idempotency / Duplicate Submit Protection

The application should protect against accidental duplicate posting caused by:

- Double-click
- Browser retry
- Network retry
- Repeated form submission

Critical financial saves should use an appropriate duplicate-submit protection strategy.

---

# 90. Unique Transaction Identifier

Each posted business transaction shall have a unique internal identifier.

An optional human-readable transaction number may also be generated.

---

# 91. Human-Readable Transaction Number

The system may generate a reference such as:

`TRX-2026-000001`

or another approved format.

This is separate from the database primary key and external provider reference.

Exact format shall be defined in UI/workflow design.

---

# 92. Legacy Data Isolation

Old Microsoft Access financial data shall not be inserted directly into live financial ledger tables merely to preserve history.

If imported for viewing, legacy data shall remain logically isolated.

---

# 93. Legacy Customer Import Table / Process

Customer migration may use a temporary/staging table before final import.

A staging process may:

1. Read Access export.
2. Normalize names.
3. Normalize mobile numbers.
4. Detect duplicates.
5. Review invalid rows.
6. Import approved customers.
7. Preserve source reference where useful.

---

# 94. Legacy Historical Read-Only Data

If a future Legacy Viewer is required, legacy Day Book data may be stored in separate read-only tables.

Such records must be clearly marked:

- Legacy
- Read Only
- Non-Ledger

They shall not change current balances.

---

# 95. Old Shareholder Data

Legacy Shareholder data may be used during reconciliation.

Only the approved opening Shareholder position shall become live new-system Shareholding.

---

# 96. Old Profit/Loss Data

Legacy Profit/Loss or Old Month Saving values shall not be directly inserted as new period Profit/Loss transactions unless explicitly approved.

---

# 97. Data Migration Audit

Any migration/import process should produce an import log showing:

- Source File
- Import Date
- Rows Read
- Rows Imported
- Rows Rejected
- Duplicate Rows
- User/Process responsible

---

# 98. Data Import Rollback

If migration fails before approval, the process should allow safe rollback/re-import rather than leaving partially imported data.

---

# 99. Customer Import Does Not Create Ledger Entries

Importing Customer Master data shall never create:

- Cash movement
- Bank movement
- Remittance balance
- Income
- Expense
- Share movement

---

# 100. Account Opening Import

Opening balances shall be entered/approved through a controlled Opening Balance process, not inferred automatically from Customer migration.

---

# 101. Data Retention

Financial transaction, Shareholder transaction and audit history shall be retained for the required business/legal period.

The system shall not automatically purge financial history merely because data is old.

Exact retention duration may follow applicable business/legal requirements.

---

# 102. Record Version History

Where sensitive fields are editable, the system should retain enough audit history to determine important changes.

Full versioning of every table is optional unless required.

---

# 103. Master Data Editing

Changing a master name such as:

`City Express`

shall not rewrite the historical financial meaning of transactions incorrectly.

Where necessary, historical display may still reference the current master name while transaction IDs remain stable.

---

# 104. Provider Deactivation

Deactivating a Provider shall prevent new transactions but preserve historical relationships.

---

# 105. Expense Category Deactivation

Deactivating an Expense Category shall prevent new usage but preserve historical Expense reports.

---

# 106. User Deactivation

Deactivated User records must remain available for:

- Created By
- Cancelled By
- Audit History

---

# 107. Shareholder Exit

Exited Shareholder records shall not be deleted.

Their Share history must remain available.

---

# 108. Data Security Principle

Database access credentials shall not be hard-coded in publicly accessible files.

Production secrets shall be handled using approved environment configuration.

---

# 109. Least Privilege Database Access

Production application database users should receive only the permissions required by the application.

Development/admin database credentials shall not be reused carelessly in production.

---

# 110. Database Backup Requirement

Production database shall be backed up automatically.

Backup frequency and retention shall be defined in:

`09_SECURITY_AUDIT_BACKUP_DEPLOYMENT_STANDARD.md`

---

# 111. File and Database Backup Relationship

Attachments and database backups must be coordinated.

A database restored without its related attachment files may create broken evidence links.

Both must form part of backup planning.

---

# 112. Restore Testing

Backup is not considered reliable unless restoration can be tested periodically.

The Security/Backup Standard shall define detailed recovery procedure.

---

# 113. Indexing Principle

Database indexes should support common queries such as:

- Business Date
- Customer ID
- Account ID
- Provider ID
- Transaction Type
- Reference
- Created By
- Status
- Shareholder ID

Indexes shall be designed based on actual query needs rather than added randomly.

---

# 114. Reporting Performance

Reports shall query normalized source records efficiently.

If large historical volume later requires summary/caching tables, those shall remain reproducible from source records.

---

# 115. Data Volume Growth

Architecture shall assume transaction data will grow over multiple years.

The design shall avoid patterns that require loading all historical rows into memory for ordinary screens.

---

# 116. Pagination

Large transaction lists shall use pagination or an equivalent efficient loading mechanism.

The UI shall not attempt to load the entire database history on one page.

---

# 117. Search Filtering

Search queries should use indexed and validated filters where appropriate.

Examples:

- Date Range
- Customer
- Account
- Provider
- Staff
- Reference
- Status

---

# 118. No Database Logic Duplication

Business logic shall not be inconsistently duplicated across multiple tables or controllers.

Financial effect rules should use a centralized approved transaction/service architecture.

---

# 119. Business Logic vs Database

The database protects integrity.

The application/business layer determines approved transaction behavior.

The system shall not rely on ad-hoc manual SQL updates to perform normal financial operations.

---

# 120. Manual Database Editing

Direct production database editing shall not be part of normal business operation.

Emergency corrections must be controlled, documented and preferably performed through application-level correction tools.

---

# 121. Seed / Initial Data

Initial system setup may seed required fixed values such as:

- Admin Role
- Staff Role
- Shareholder Role
- Core Transaction Types

Business-specific Financial Accounts and Providers should be configured based on the approved opening setup.

---

# 122. No Hard-Coded Business Account IDs

Application code shall not depend on assumptions such as:

`Cash Account ID = 1`

or:

`City Express ID = 5`

Business entities shall be resolved through stable configuration/relationships.

---

# 123. System Settings

System-wide settings may include:

- Business Name
- Default Currency
- Current Share Value
- Date settings
- Attachment settings
- Other approved configuration

Settings that affect historical financial values require careful effective-date treatment.

---

# 124. Share Value Setting

If current Share Value is stored in Settings, historical Share Transactions must still store their own applicable Value Per Share where required.

Changing Settings must not rewrite old transactions.

---

# 125. Currency Setting

Default currency shall initially be Nepalese Rupees unless otherwise approved.

Multi-currency tables/logic shall not be introduced unnecessarily.

---

# 126. Data Export

Authorized data export shall not bypass permissions.

Exports shall be generated from the same authorized data scope visible to the user.

---

# 127. Personal Data

Customer and Shareholder personal information shall be stored only where required by business needs.

The system should avoid collecting unnecessary sensitive information.

---

# 128. Attachment File Metadata

Attachment metadata should preserve:

- Original Filename
- Secure Stored Filename
- File Type
- File Size
- Uploader
- Upload Time
- Related Record

---

# 129. File Name Security

Uploaded files shall not be stored using unsafe user-controlled paths.

The application shall generate safe storage names/paths.

---

# 130. Data Validation for Uploads

File extension alone shall not be trusted.

Technical upload validation shall consider:

- MIME type
- File size
- Allowed formats

Detailed values shall be defined in the Security Standard.

---

# 131. Data Integrity Testing

Database testing shall include:

- Foreign-key integrity
- Transaction rollback
- Duplicate-submit protection
- Account balance reconciliation
- Negative balance behavior
- Cancellation/reversal
- Share transfer atomicity
- Redemption atomicity
- User deactivation history
- Attachment linkage
- Legacy isolation

---

# 132. Account Ledger Testing

For each Account:

`Opening + Valid In - Valid Out`

must equal Current Balance.

Cancelled/reversed entries must not remain active in balance totals.

---

# 133. Profit/Loss Data Testing

Profit/Loss calculations shall use only approved active:

- Service Charges
- Commission
- Other approved Income
- Expenses

Principal, Account Transfers and Opening Balances shall not be mistakenly included.

---

# 134. Share Ledger Testing

For each Shareholder:

`Opening + Shares In - Shares Out`

must equal Current Share Quantity.

Current Share Quantity must never become negative.

---

# 135. Unresolved Database Decisions

The following technical details remain to be finalized during implementation planning:

1. Exact relational database engine.
2. Exact Customer mandatory fields.
3. Whether Providers and Accounts use one-to-one or flexible relationship.
4. Exact Ledger debit/credit representation.
5. Whether Account Current Balance is stored or fully calculated.
6. Exact human-readable Transaction Number format.
7. Exact file upload limits.
8. Exact allowed attachment formats.
9. Exact Financial Year implementation.
10. Exact audit-log event list.
11. Exact role storage architecture.
12. Exact closed-period data structure.
13. Exact legacy read-only archive requirement.

These implementation details shall not change approved business logic.

---

# 136. Development Rule

No developer or AI coding agent may design database tables by simply copying the old Microsoft Access schema.

Before creating migrations/models:

**READ BUSINESS CONSTITUTION → READ ACCOUNT STANDARD → READ SHAREHOLDER STANDARD → READ PERMISSION STANDARD → DESIGN RELATIONAL STRUCTURE → VERIFY FINANCIAL EFFECTS → IMPLEMENT**

---

# 137. Migration Modification Rule

Once production financial data exists:

- Destructive schema changes require review.
- Financial columns shall not be casually dropped.
- Historical relationships shall be preserved.
- Data migration scripts shall be tested before production execution.

---

# 138. Production Data Protection

Development/testing operations shall not be executed directly against production financial data without proper controls.

Production database copies used for testing should be handled securely.

---

# 139. Acceptance Rule

Database implementation shall be considered acceptable only when:

- Customers have no unintended financial balance.
- Accounts are dynamic and ledger-backed.
- Remittance/Deposit/Withdrawal financial effects are traceable.
- Service Charge remains separately identifiable.
- Commission and Expense link to actual accounts.
- Share ownership is transaction-based.
- Shareholder transfer does not create false Cash/Bank activity.
- Company redemption affects both shares and payment account atomically.
- Opening Balances are auditable.
- Cancelled transactions preserve history.
- Notes and attachments remain linked.
- Role relationships remain secure.
- Legacy data does not contaminate new live balances.
- Current balances reconcile with ledger data.

---

# 140. Document Authority

This document is the authoritative database and data-management standard for the Remittance Management System.

It is subordinate to:

`01_REMITTANCE_BUSINESS_CONSTITUTION.md`

and must be used together with:

- `02_PROJECT_SCOPE_AND_REQUIREMENTS.md`
- `03_ACCOUNT_AND_TRANSACTION_STANDARD.md`
- `04_COMMISSION_EXPENSE_AND_PROFIT_STANDARD.md`
- `05_SHAREHOLDER_STANDARD.md`
- `06_USER_ROLE_PERMISSION_STANDARD.md`

Dependent documents include:

- `08_UI_AND_WORKFLOW_STANDARD.md`
- `09_SECURITY_AUDIT_BACKUP_DEPLOYMENT_STANDARD.md`

---

# 141. Approval

**Status:** FINAL DRAFT — Pending Business Owner Approval

After Business Owner approval, this document becomes the authoritative Database and Data Standard for architecture, migration design, development, testing and maintenance of the Remittance Management System.

---

**END OF DOCUMENT**