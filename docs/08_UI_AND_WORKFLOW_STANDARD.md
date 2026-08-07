# REMITTANCE MANAGEMENT SYSTEM — UI AND WORKFLOW STANDARD

**Document ID:** 08  
**File Name:** `08_UI_AND_WORKFLOW_STANDARD.md`  
**Project:** Remittance Management System  
**Document Type:** UI and Workflow Standard  
**Version:** 1.0  
**Status:** FINAL DRAFT — Pending Business Owner Approval  
**Authority:** Business Owner  

---

# 1. Purpose

This document defines the approved user interface structure, screen behavior and operational workflows for the Remittance Management System.

It governs:

- Login
- Role-based dashboards
- Customer workflow
- Account workflow
- Remittance transaction workflow
- Deposit workflow
- Withdrawal workflow
- Account transfer workflow
- Commission workflow
- Expense workflow
- Shareholder workflow
- Share transfer workflow
- Shareholder exit workflow
- Search
- Filters
- Reports
- Notes
- Attachments
- Cancellation/correction UI
- Responsive behavior
- Usability
- Operational safety

This document must comply with:

1. `01_REMITTANCE_BUSINESS_CONSTITUTION.md`
2. `02_PROJECT_SCOPE_AND_REQUIREMENTS.md`
3. `03_ACCOUNT_AND_TRANSACTION_STANDARD.md`
4. `04_COMMISSION_EXPENSE_AND_PROFIT_STANDARD.md`
5. `05_SHAREHOLDER_STANDARD.md`
6. `06_USER_ROLE_PERMISSION_STANDARD.md`
7. `07_DATABASE_AND_DATA_STANDARD.md`
8. `00_PROJECT_INDEX.md`

If this document conflicts with the Business Constitution, the Business Constitution shall take precedence.

---

# 2. Core UI Principle

The UI shall be simple enough for daily operational use while preserving financial accuracy.

The system shall prioritize:

- Fast transaction entry
- Clear account selection
- Clear principal amount
- Clear Service Charge
- Clear transaction type
- Clear financial effect
- Minimal unnecessary fields
- Strong role separation
- Easy search
- Easy audit review

The UI shall not expose technical database complexity to ordinary users.

---

# 3. Role-Based Interface

The application shall present different interfaces according to role:

1. Admin
2. Staff
3. Shareholder

Each role shall see only the screens and actions permitted by the Role and Permission Standard.

---

# 4. Login Page

The system shall provide a common secure Login page.

Login may use:

- Email
- Username
- Mobile
- Password

depending on final authentication design.

The page shall remain simple.

It should include:

- Login Identifier
- Password
- Login Button
- Forgot Password where implemented

---

# 5. Post-Login Routing

After successful login:

- Admin → Admin Dashboard
- Staff → Staff Dashboard
- Shareholder → Shareholder Dashboard

Users shall not be routed to interfaces belonging to another role.

---

# 6. Admin Dashboard

Admin Dashboard may display:

- Today's Transaction Count
- Today's Principal Amount
- Today's Service Charge
- Current Cash Balance
- Current Bank Balance
- Remittance Account Balances
- BLB/Operational Balances
- Negative Accounts
- This Month's Commission
- This Month's Expense
- This Month's Profit/Loss
- Recent Transactions
- Recent Staff Activity
- Recent Shareholder Activity

Exact cards and layout may be adjusted during UI implementation.

---

# 7. Staff Dashboard

Staff Dashboard shall prioritize daily operations.

It should provide quick access to:

- New Remittance
- New Deposit
- New Withdrawal
- Customer Search
- Add Customer
- Today's permitted transactions
- Own transaction summary
- Own Service Charge summary where allowed

Sensitive financial details shall remain hidden unless specifically permitted.

---

# 8. Shareholder Dashboard

Shareholder Dashboard shall remain simple and mostly read-only.

It may display:

- Current Share Quantity
- Current Share Value
- Current Share Valuation
- Recent Share Transactions
- Share Transfer History
- Redemption/Exit History

It shall not display unrelated operational transaction data.

---

# 9. Main Navigation — Admin

Admin navigation may contain:

- Dashboard
- Customers
- Accounts
- Transactions
- Remittance
- Deposit/Withdrawal
- Account Transfers
- Commission
- Expenses
- Shareholders
- Reports
- Users
- Settings
- Audit/History

Menu structure may be grouped for usability.

---

# 10. Main Navigation — Staff

Staff navigation should remain limited.

Possible items:

- Dashboard
- Customers
- New Transaction
- Remittance
- Deposit
- Withdrawal
- My/Permitted Transactions

Admin-only menus shall not appear.

---

# 11. Main Navigation — Shareholder

Shareholder navigation may contain:

- Dashboard
- My Shares
- My Share History
- My Documents
- Profile

No operational financial menus shall appear.

---

# 12. Customer List Screen

Customer List should support:

- Search by Name
- Search by Mobile
- Pagination
- Status filter where applicable
- Add Customer
- View Customer
- Edit Customer where permitted

The list should remain fast even with many customers.

---

# 13. Add Customer Workflow

The Add Customer form shall only request approved required information.

Typical fields may include:

- Name
- Mobile
- Address
- Other approved reference information
- Note
- Attachment

The form shall not contain Customer Balance.

---

# 14. Customer Quick Add

During transaction entry, Staff/Admin should be able to quickly add a new Customer without leaving the workflow entirely.

After successful creation, the new Customer should become selectable in the active transaction form.

---

# 15. Customer Search During Transaction

Customer selection should support fast search.

Search may use:

- Name
- Mobile
- Customer ID

The user should not need to scroll through a very large dropdown list.

---

# 16. Transaction Type Selection

The UI shall clearly distinguish:

- Remittance
- Deposit
- Withdrawal
- Account Transfer
- Commission
- Expense
- Other authorized types

A user shall not be required to infer transaction type from hidden account behavior.

---

# 17. Remittance Entry Screen

The Remittance form shall include at minimum:

- Business Date
- Customer
- Remittance Service/Account
- Principal Amount
- Service Charge
- Reference Number
- Note
- Attachment
- Save Button

The form shall remain compact and efficient.

---

# 18. Remittance Financial Preview

Before save, the UI may display a simple financial preview.

Example:

Principal: NPR 10,000  
Service Charge: NPR 100  

Result:

- Cash +10,100
- City Express -10,000

This preview is recommended for Admin and may be shown to Staff if it improves accuracy.

---

# 19. Negative Balance Warning

If a Remittance transaction makes an allowed account negative, the UI may show a warning such as:

`City Express balance will become NPR -8,000.`

The warning shall not block the transaction where negative balance is allowed.

---

# 20. Negative Balance Prohibition

If an Account is configured not to allow negative balance, the UI shall clearly explain why the transaction cannot proceed.

---

# 21. Deposit Entry Screen

Deposit form shall include:

- Business Date
- Customer
- BLB/Bank Service Account
- Principal Amount
- Service Charge
- Reference
- Note
- Attachment

The financial effect shall follow approved rules.

---

# 22. Deposit Preview

Example:

Amount:

`NPR 10,000`

Service Charge:

`NPR 100`

Preview:

- Cash +10,100
- BLB -10,000

---

# 23. Withdrawal Entry Screen

Withdrawal form shall include:

- Business Date
- Customer
- BLB/Bank Service Account
- Principal Amount
- Service Charge
- Reference
- Note
- Attachment

The exact Service Charge handling shall follow final business approval.

---

# 24. Withdrawal Preview

The UI shall show the expected financial effect once the withdrawal Service Charge rule is finalized.

Until then, implementation shall not guess.

---

# 25. Account Transfer Screen

Account Transfer form shall include:

- Business Date
- Send Account
- Receive Account
- Amount
- Reference
- Note
- Attachment

The system must prevent selecting the same account for both Send and Receive.

---

# 26. Transfer Balance Preview

The UI may show:

Before:

Bank = NPR 500,000  
City Express = NPR -50,000

Transfer:

NPR 50,000

After:

Bank = NPR 450,000  
City Express = NPR 0

This helps reduce entry mistakes.

---

# 27. Month-End Settlement Workflow

Month-End Settlement may reuse Account Transfer UI with a special Settlement context.

Recommended workflow:

1. Open negative account list.
2. Select account.
3. View current negative balance.
4. Select source account.
5. Enter settlement amount.
6. Add Note/Attachment.
7. Confirm.
8. Save.

---

# 28. Negative Account List

Admin should have a screen/report showing accounts with negative balances.

Example columns:

- Account
- Category
- Current Balance
- Last Transaction Date
- Settlement Action

This provides quick month-end visibility.

---

# 29. Commission Entry Screen

Commission form shall include:

- Business Date
- Provider
- Commission Amount
- Receiving Account
- Reference
- Note
- Attachment

The form shall clearly distinguish Commission from Service Charge.

---

# 30. Commission Preview

Example:

Provider Commission:

`NPR 5,000`

Receiving Account:

`Asha Enterprises Bank`

Result:

- Commission Income +5,000
- Bank +5,000

---

# 31. Expense Entry Screen

Expense form shall include:

- Business Date
- Expense Category
- Particular/Description
- Amount
- Payment Account
- Reference
- Note
- Attachment

---

# 32. Expense Preview

Example:

Rent:

`NPR 20,000`

Paid from:

`Cash`

Result:

- Cash -20,000
- Expense +20,000

---

# 33. Shareholder List

Admin Shareholder List may show:

- Name
- Share Quantity
- Value Per Share
- Total Valuation
- Status
- View
- Transfer
- Redemption/Exit actions where authorized

---

# 34. Shareholder Detail Screen

Admin Shareholder Detail may show:

- Profile
- Current Share Quantity
- Current Valuation
- Share Ledger
- Transfer History
- Redemption History
- Notes
- Attachments
- Status

---

# 35. Shareholder Own View

Shareholder role should see a simplified version of the Shareholder Detail screen containing only own permitted data.

Admin controls shall not be visible.

---

# 36. Opening Share Entry

Admin may enter opening share positions during new system setup.

The screen shall include:

- Shareholder
- Opening Share Quantity
- Value Per Share
- Calculated Valuation
- Effective Date
- Approval Reference
- Note
- Attachment

---

# 37. Share Transfer Screen

Shareholder-to-Shareholder Transfer form shall include:

- Business Date
- From Shareholder
- To Shareholder
- Number of Shares
- Value Per Share
- Calculated Valuation
- Reference
- Note
- Attachment

---

# 38. Share Transfer Preview

Example:

From A:

500 shares

To B:

300 shares

Transfer:

100 shares

After:

A = 400  
B = 400

Company Cash/Bank:

No Effect

The UI should state this clearly.

---

# 39. Transfer Validation

Before save, the system shall verify:

- Seller has sufficient shares.
- Quantity > 0.
- Seller and buyer are different.
- Both records are valid.
- User is authorized.

---

# 40. New Shareholder During Transfer

Admin should be able to create a new Shareholder and then select that Shareholder as the recipient of an approved transfer.

The workflow should preserve continuity.

---

# 41. Shareholder Redemption Screen

Company Redemption form shall include:

- Business Date
- Shareholder
- Number of Shares
- Value Per Share
- Settlement Amount
- Payment Account
- Reference
- Note
- Attachment

---

# 42. Redemption Preview

Example:

Shares:

200

Value per Share:

NPR 1,000

Settlement:

NPR 200,000

Payment Account:

Bank

Result:

- Shareholder -200 shares
- Bank -200,000

---

# 43. Full Exit Workflow

Full Exit workflow should:

1. Open Shareholder.
2. Display current holding.
3. Select Full Exit.
4. Determine exit method:
   - Company Redemption, or
   - Transfer Out.
5. Complete required transaction.
6. Ensure Share Quantity becomes zero.
7. Mark Shareholder status appropriately.
8. Preserve history.

---

# 44. Shareholder Transfer-Out Exit

If all shares are transferred to another Shareholder:

- No Company Cash/Bank movement.
- Shareholder reaches zero shares.
- Status may become Exited.

The UI shall not ask for Company Payment Account unless required by a separate transaction.

---

# 45. Opening Balance Screen

Admin Opening Balance setup shall support:

- Opening Effective Date
- Account
- Opening Amount
- Positive/Negative value
- Approval Reference
- Note
- Attachment

A batch-style setup is recommended for initial deployment.

---

# 46. Opening Balance Batch Review

Before finalization, Admin should be able to review all opening balances together.

Example:

| Account | Opening Balance |
|---|---:|
| Cash | 250,000 |
| Bank | 800,000 |
| City Express | -50,000 |
| Citizen Remit | 30,000 |
| BLB | -20,000 |

The user should confirm before locking/finalizing.

---

# 47. Opening Balance Finalization

Once Opening Balance is finalized:

- Ordinary editing should be disabled.
- Any later correction should use approved Admin correction procedure.
- A confirmation warning should explain the importance of finalization.

---

# 48. Transaction List Screen

Transaction List shall support:

- Pagination
- Search
- Date filter
- Customer filter
- Transaction Type filter
- Provider/Account filter
- Staff/User filter
- Status filter
- Reference search

---

# 49. Transaction List Columns

Depending on role and transaction type, list columns may include:

- Date
- Transaction ID
- Customer
- Type
- Provider/Account
- Principal
- Service Charge
- Reference
- Staff
- Status
- View

---

# 50. Transaction Detail Screen

Transaction Detail shall display:

- Internal Transaction ID
- Business Date
- Created At
- Customer
- Type
- Provider
- Accounts affected
- Principal Amount
- Service Charge
- Reference
- Note
- Attachments
- Created By
- Status
- Cancellation/Correction information

---

# 51. Financial Effect Section

Transaction Detail should show the actual account effects.

Example:

- Cash +10,100
- City Express -10,000

This improves audit clarity.

---

# 52. Account List

Admin Account List may show:

- Account Name
- Category
- Current Balance
- Negative Allowed
- Status
- View Ledger
- Edit

---

# 53. Account Detail

Account Detail may show:

- Account Profile
- Current Balance
- Opening Balance
- Total In
- Total Out
- Ledger
- Notes
- Attachments
- Status

---

# 54. Account Ledger Screen

Ledger should support:

- Date Range
- Transaction Type
- Reference
- Customer
- User
- Status

Columns may include:

- Date
- Transaction
- In
- Out
- Running Balance
- Reference
- User

---

# 55. Running Balance

Account Ledger shall display running balance accurately according to active ledger entries.

Cancelled entries shall not distort active balance totals.

---

# 56. Service Charge Report Screen

Admin report shall support:

- Date Range
- Staff
- Provider
- Transaction Type

Report should show:

- Transaction Count
- Principal Total
- Service Charge Total

---

# 57. Staff Service Charge Report

Admin should be able to compare Staff activity.

Example columns:

- Staff
- Transactions
- Principal
- Service Charge
- Cancelled Transactions

---

# 58. Commission Report Screen

Filters may include:

- Date Range
- Provider
- Receiving Account
- Status

Columns may include:

- Date
- Provider
- Commission Amount
- Receiving Account
- Reference
- User

---

# 59. Expense Report Screen

Filters may include:

- Date Range
- Expense Category
- Payment Account
- User
- Status

Columns may include:

- Date
- Category
- Particular
- Amount
- Payment Account
- User

---

# 60. Profit/Loss Report Screen

Admin Profit/Loss report should show separate components.

Example:

Service Charge Income  
Provider Commission  
Other Approved Income  
Total Income  

Expenses  
Total Expense  

Net Profit/Loss

Principal transaction volume shall not be shown as Income.

---

# 61. Monthly Summary Screen

Monthly Summary may combine:

- Principal Transaction Volume
- Service Charge
- Commission
- Expense
- Profit/Loss
- Current Account Balances
- Negative Accounts

These sections shall remain clearly separated.

---

# 62. Shareholder Report Screen

Admin may filter by:

- Active
- Inactive
- Exited
- Name
- Share Quantity

The report should show current share position and valuation.

---

# 63. Share Ledger Screen

Share Ledger shall show:

- Date
- Transaction Type
- Shares In
- Shares Out
- Running Share Quantity
- Related Shareholder
- Value Per Share
- Reference

---

# 64. Global Search Principle

The UI should not provide one dangerous unrestricted search across sensitive data for all roles.

Search results must respect role permissions.

---

# 65. Date Filter

Financial screens should support clear Start Date and End Date filtering.

The system may support Nepali date display/input if approved.

Date behavior must remain consistent across modules.

---

# 66. Business Date Entry

Transaction forms shall show Business Date explicitly.

Users shall not confuse Business Date with system creation timestamp.

---

# 67. Default Business Date

The transaction form may default to the current business date.

Changing the date shall depend on role permission and backdating rules.

---

# 68. Backdated Date Warning

If a user is permitted to enter a prior date, the UI should clearly indicate that the transaction is backdated.

---

# 69. Closed Period UI

If period locking is later introduced, attempting to enter a transaction into a closed period shall show a clear message.

The UI shall not silently move the transaction to another date.

---

# 70. Note Field

Every applicable form shall provide a Note field.

Note should support sufficient text for business explanation.

It shall not be used instead of required structured fields.

---

# 71. Attachment Upload

Every applicable record shall support attachment upload.

The UI should provide:

- Select File
- Uploaded File List
- View/Download where authorized
- Remove-before-save where appropriate

---

# 72. Multiple Attachments

The UI should support multiple attachments where permitted by technical/security rules.

---

# 73. Attachment Preview

Images may support preview.

PDFs/documents may display filename and authorized open/download action.

---

# 74. Attachment Authorization

The UI shall not display attachment links to unauthorized roles.

---

# 75. Save Confirmation

For ordinary low-risk daily transactions, save should remain efficient.

For high-impact operations, confirmation may be required.

High-impact examples:

- Opening Balance Finalization
- Share Transfer
- Share Redemption
- Large Adjustment
- Cancellation

---

# 76. Duplicate Submit Protection

After user presses Save:

- Button may disable temporarily.
- Loading state may appear.
- Repeated click shall not create duplicate transactions.

---

# 77. Success Message

After successful save, show a clear message such as:

`Transaction saved successfully.`

The system may also show the generated Transaction Number.

---

# 78. Error Message

Validation errors shall explain the actual problem.

Examples:

- Customer is required.
- Amount must be greater than zero.
- Send and Receive Accounts must be different.
- Insufficient shares for transfer.
- You do not have permission.

Generic unexplained errors should be avoided.

---

# 79. Financial Validation Message

Where a transaction is blocked by financial/account configuration, the reason must be visible.

---

# 80. Confirmation Screen / Receipt

After a daily transaction is saved, the system may provide a compact confirmation/receipt view containing:

- Customer
- Transaction Type
- Principal
- Service Charge
- Provider
- Reference
- Date
- Transaction Number

Print support may be added where required.

---

# 81. Print Standard

Printed information shall use the same approved values as system records.

Print shall not recalculate financial values differently from the application.

---

# 82. Export Standard

Reports may support:

- PDF
- Spreadsheet
- Print

Exports shall respect current filters and role permissions.

---

# 83. Cancel Action Placement

Cancel action for posted transactions shall not appear as a casual destructive button beside ordinary Edit.

It should require a deliberate workflow.

---

# 84. Cancellation Workflow — Admin

Recommended flow:

1. Open Transaction.
2. Click Cancel.
3. Show financial effect.
4. Enter mandatory Cancellation Reason.
5. Confirm.
6. System reverses financial effects.
7. Transaction becomes Cancelled.
8. Audit history remains.

---

# 85. Staff Cancellation Request

If implemented, Staff may:

1. Open own/permitted transaction.
2. Request Cancellation.
3. Enter reason.
4. Admin reviews.
5. Admin approves/rejects.

Staff shall not directly execute final reversal under the initial permission standard.

---

# 86. Correction Workflow

Recommended correction process:

1. Identify incorrect transaction.
2. Cancel/reverse original.
3. Create corrected transaction.
4. Link replacement to original.

The UI should show the relationship between them.

---

# 87. No Normal Edit for Posted Financial Values

After posting, fields such as:

- Principal Amount
- Service Charge
- Accounts
- Expense Amount
- Commission Amount

shall not remain freely editable.

Correction workflow shall be used.

---

# 88. Audit History UI

Admin may have an Audit/History panel showing:

- Created By
- Created At
- Cancelled By
- Cancelled At
- Cancellation Reason
- Replacement Transaction
- Related user actions

---

# 89. Responsive Design

The application shall work on:

- Desktop
- Laptop
- Tablet
- Mobile Browser

Forms shall remain usable without horizontal overflow where practical.

---

# 90. Desktop Priority

Admin reports and detailed account ledgers may be optimized primarily for desktop/laptop while remaining accessible on mobile.

---

# 91. Mobile Priority

Staff daily transaction forms should be highly usable on mobile/tablet if the business operates from those devices.

---

# 92. Form Layout

Daily transaction forms should avoid overly wide complex screens.

A compact logical sequence is preferred:

Customer → Service → Amount → Service Charge → Reference → Note → Attachment → Save

---

# 93. Numeric Input

Amount inputs shall:

- Accept valid financial numbers
- Prevent invalid text
- Display currency context
- Avoid accidental scientific notation or invalid formatting

---

# 94. Currency Display

Current currency shall display as NPR/Rs. according to approved UI convention.

Formatting should use readable separators.

Example:

`NPR 1,250,000`

---

# 95. Negative Balance Display

Negative balances shall be visually clear.

Example:

`NPR -50,000`

The UI shall not hide the minus sign.

---

# 96. Status Display

Statuses such as:

- Active
- Cancelled
- Inactive
- Exited

shall be easy to distinguish.

---

# 97. Inactive Master Selection

Inactive Accounts, Providers or Expense Categories shall not normally appear in new transaction selectors.

Historical records may still display them.

---

# 98. Search Performance

Search screens shall use pagination and server-side filtering where necessary.

The UI shall not load thousands of records into one dropdown/table.

---

# 99. Customer Autocomplete

Customer selector should preferably support autocomplete/search for faster daily work.

---

# 100. Provider Selection

Provider/Service selector shall show only active approved providers.

---

# 101. Account Selection

Account dropdowns shall show clear account names.

If two similar accounts exist, include enough context to distinguish them.

---

# 102. Sensitive Balance Display

The system shall not expose account balances inside selection dropdowns to Staff unless explicitly approved.

---

# 103. Admin Master Screens

Admin should have dedicated screens for managing:

- Accounts
- Providers
- Expense Categories
- Users
- Shareholders
- Relevant Settings

---

# 104. Master Creation Safety

Creating a new master record should validate duplicates and required fields.

---

# 105. Master Deactivation UI

Where a master record has history, the UI should offer:

`Deactivate`

instead of:

`Delete`

---

# 106. Legacy Customer Import UI

If a migration tool is included, Admin may use a controlled import workflow:

1. Upload CSV/approved file.
2. Preview rows.
3. Show duplicates/errors.
4. Confirm clean records.
5. Import.
6. Show import summary.

---

# 107. Legacy Financial Data UI

Old financial transactions shall not appear mixed into live transaction screens unless a separate Legacy Viewer is implemented.

---

# 108. Legacy Viewer

If implemented in future, Legacy Viewer shall be clearly labeled:

`Legacy / Historical Records`

It must be read-only and financially isolated.

---

# 109. UI Consistency

Buttons, tables, inputs, filters, notes, attachments and status indicators shall follow consistent patterns across modules.

Users should not need to relearn interaction patterns for each screen.

---

# 110. Accessibility

The UI should use:

- Clear labels
- Readable font sizes
- Sufficient contrast
- Keyboard-friendly forms where practical
- Understandable error messages

---

# 111. Confirmation Language

Financial confirmations shall be concise and specific.

Example:

`This will reduce Bank by NPR 200,000 and reduce the shareholder holding by 200 shares.`

This is preferable to a generic:

`Are you sure?`

---

# 112. Admin Warning for High-Risk Actions

High-risk actions should show explicit impact before confirmation.

Examples:

- Opening Balance change
- Financial Adjustment
- Share Redemption
- Cancellation

---

# 113. Staff Simplicity Principle

Staff should not be forced to understand full accounting logic.

The system shall guide Staff through the approved transaction type.

The backend shall determine financial effects.

---

# 114. No Manual Plus/Minus Selection for Staff

Staff should not normally choose:

`Increase Account`

or:

`Decrease Account`

manually.

Transaction Type should define this automatically.

---

# 115. Admin Financial Clarity

Admin views should expose sufficient detail to understand how each transaction affected financial accounts.

---

# 116. Dashboard Consistency

Dashboard totals must match corresponding reports.

For example:

`Today's Service Charge`

on Dashboard must equal the same date-filtered Service Charge report.

---

# 117. Empty State

Screens with no data should show useful messages.

Example:

`No transactions found for the selected date range.`

---

# 118. Loading State

Slow actions such as:

- Large reports
- File upload
- Export

should show a loading/progress indication where practical.

---

# 119. Session Behavior

If session expires, the system shall redirect safely to Login.

Unsaved sensitive financial transactions should not be silently posted after session failure.

---

# 120. Browser Refresh

Refreshing after a successful financial POST shall not create a duplicate transaction.

Post/Redirect/Get or equivalent safe pattern should be used.

---

# 121. Permission Error UI

Unauthorized users should see a clear access-denied response rather than application failure.

---

# 122. Shareholder Privacy UI

Shareholder interfaces shall never present another Shareholder in selectors or reports unless that information is explicitly required and authorized.

---

# 123. Staff Privacy UI

Staff shall not see internal shareholder ownership screens.

---

# 124. Account Balance Privacy UI

Shareholder shall not see Cash/Bank/Remittance balances.

Staff access shall remain limited.

---

# 125. Report Date Defaults

Reports may default to:

- Today for daily operations
- Current month for monthly summary

The user must be able to change filters where authorized.

---

# 126. Financial Year Filter

If Nepali Financial Year reporting is implemented, the UI may include a Financial Year selector.

Exact date logic shall follow approved date rules.

---

# 127. Transaction Number

A human-readable internal Transaction Number may be displayed after save.

It shall be searchable.

---

# 128. Reference Number

External Reference Number shall remain separate from internal Transaction Number.

Both may be displayed on transaction detail.

---

# 129. Attachment Required Indicator

If a future transaction type requires mandatory evidence, the UI shall clearly mark Attachment as required.

---

# 130. Note Required Indicator

Where specific actions require a reason, such as cancellation, Note/Reason shall be mandatory.

---

# 131. Default Values

Safe defaults may be used for convenience.

Examples:

- Current Business Date
- Service Charge = 0

Defaults shall not create hidden financial assumptions.

---

# 132. Auto Calculation

The UI may automatically calculate:

- Total Cash Received
- Share Valuation
- Resulting Account Balance
- Settlement amount suggestion

Auto calculations must follow documented business rules.

---

# 133. Manual Override

Users shall not override calculated financial effects unless an approved workflow explicitly permits it.

---

# 134. Share Value Display

Share transaction forms shall display the applicable share value.

Current baseline:

`NPR 1,000 per Share`

Historical transactions shall show their preserved applicable value.

---

# 135. Share Quantity Format

Share quantities shall display as whole units under the current rule.

---

# 136. Admin Settings UI

Settings may include:

- Business Name
- Currency
- Current Share Value
- Date/Financial Year configuration
- Other approved system settings

Settings that affect historical records shall show warnings.

---

# 137. No Direct Current Balance Form

There shall not be an ordinary Admin form labeled:

`Edit Current Balance`

for live accounts.

Balance correction must use approved financial adjustment/reconciliation workflow.

---

# 138. No Direct Share Balance Form

There shall not be an ordinary form allowing Admin to casually type a new Current Share Quantity after system operation starts.

Ownership changes must use Share Transactions.

---

# 139. Audit-Friendly UI

Important records should provide direct links to:

- Related Customer
- Related Account
- Related Transaction
- Related Shareholder
- Related Cancellation/Correction

where authorized.

---

# 140. Reporting Accuracy

UI totals shall be calculated from the same approved source data as backend reports.

Visual convenience shall not introduce separate financial calculation logic.

---

# 141. Unresolved UI Decisions

The following shall be finalized before implementation:

1. Exact branding and visual theme.
2. Exact Nepali vs English language usage.
3. Exact Nepali date input method.
4. Withdrawal Service Charge UI.
5. Whether Staff can see selected account balance.
6. Whether Staff can edit Customer.
7. Whether Staff can enter Expense.
8. Whether Staff cancellation-request workflow is required.
9. Whether Shareholder can download a statement.
10. Whether Share Certificate print is required.
11. Exact attachment limits.
12. Whether transaction receipt print is required.
13. Exact Admin dashboard cards.
14. Whether Profit/Loss is shown to Shareholder in any summarized form.

These shall not be guessed.

---

# 142. Testing Requirement

UI/Workflow testing shall verify:

- Correct role dashboard after Login.
- Unauthorized menus/screens are inaccessible.
- Customer quick search works.
- Daily transaction forms are fast and clear.
- Remittance financial effect is correct.
- Deposit effect is correct.
- Withdrawal follows final rule.
- Negative-balance warnings behave correctly.
- Account Transfer prevents same-account selection.
- Share Transfer prevents excessive quantity.
- Share Redemption clearly shows payment effect.
- Notes save correctly.
- Attachments remain linked.
- Double-click does not duplicate transactions.
- Cancellation requires reason.
- Mobile layout remains usable.
- Reports match backend totals.
- Shareholder sees only own permitted information.

---

# 143. Acceptance Rule

The UI and Workflow implementation is acceptable only when:

- Staff can perform daily work with minimal confusion.
- Admin can clearly understand financial/account effects.
- Shareholder sees only own approved share data.
- Financial operations cannot be accidentally posted through ambiguous forms.
- Notes and attachments are available where required.
- Search and reporting are practical.
- Role restrictions are enforced.
- Cancel/correction workflow preserves history.
- Responsive behavior is usable.
- Dashboard values match reports.
- No UI permits arbitrary balance manipulation.

---

# 144. Development Rule

No developer or AI coding agent may invent workflow or expose a financial control because it is easier to implement.

If a screen behavior is unclear:

**READ BUSINESS CONSTITUTION → READ ROLE STANDARD → READ ACCOUNT STANDARD → CONFIRM WORKFLOW → DOCUMENT → IMPLEMENT**

---

# 145. Document Authority

This document is the authoritative UI and Workflow Standard for the Remittance Management System.

It is subordinate to:

`01_REMITTANCE_BUSINESS_CONSTITUTION.md`

and must be used together with:

- `02_PROJECT_SCOPE_AND_REQUIREMENTS.md`
- `03_ACCOUNT_AND_TRANSACTION_STANDARD.md`
- `04_COMMISSION_EXPENSE_AND_PROFIT_STANDARD.md`
- `05_SHAREHOLDER_STANDARD.md`
- `06_USER_ROLE_PERMISSION_STANDARD.md`
- `07_DATABASE_AND_DATA_STANDARD.md`

The final related document is:

`09_SECURITY_AUDIT_BACKUP_DEPLOYMENT_STANDARD.md`

---

# 146. Approval

**Status:** FINAL DRAFT — Pending Business Owner Approval

After Business Owner approval, this document becomes the authoritative UI and Workflow Standard for design, development, testing and maintenance of the Remittance Management System.

---

**END OF DOCUMENT**