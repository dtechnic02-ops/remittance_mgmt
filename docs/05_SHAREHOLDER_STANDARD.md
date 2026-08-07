# REMITTANCE MANAGEMENT SYSTEM — SHAREHOLDER STANDARD

**Document ID:** 05  
**File Name:** `05_SHAREHOLDER_STANDARD.md`  
**Project:** Remittance Management System  
**Document Type:** Shareholder and Share Management Standard  
**Version:** 1.0  
**Status:** FINAL DRAFT — Pending Business Owner Approval  
**Authority:** Business Owner  

---

# 1. Purpose

This document defines the approved business rules for:

- Shareholder Master
- Share quantity
- Share valuation
- Share ownership
- Share purchase
- Share addition
- Share transfer
- Shareholder-to-shareholder transfer
- New shareholder entry
- Company share issue
- Partial exit
- Full exit
- Company redemption
- Shareholder history
- Shareholder account effects
- Shareholder portal visibility
- Notes and attachments
- Audit and correction

This document must comply with:

1. `01_REMITTANCE_BUSINESS_CONSTITUTION.md`
2. `02_PROJECT_SCOPE_AND_REQUIREMENTS.md`
3. `03_ACCOUNT_AND_TRANSACTION_STANDARD.md`
4. `04_COMMISSION_EXPENSE_AND_PROFIT_STANDARD.md`
5. `00_PROJECT_INDEX.md`

If this document conflicts with the Business Constitution, the Business Constitution shall take precedence.

---

# 2. Core Shareholder Principle

A Shareholder represents a person who owns approved share units in the business.

Shareholder ownership shall be represented through:

- Share Quantity
- Applicable Share Value
- Share Transaction History

The system shall not rely only on a manually editable current share number without transaction history.

---

# 3. Share Unit

The currently approved base share value is:

**1 Share Unit = NPR 1,000**

Example:

100 Share Units:

`100 × NPR 1,000 = NPR 100,000`

500 Share Units:

`500 × NPR 1,000 = NPR 500,000`

---

# 4. Share Quantity

Each active Shareholder shall have a current Share Quantity.

The current quantity shall be derived or maintained from approved share transactions.

Typical changes include:

- Opening Share Holding
- Share Added
- Share Received
- Share Transferred Out
- Share Redemption
- Partial Exit
- Full Exit

---

# 5. Share Valuation

Current Share Valuation shall be calculated using:

`Current Share Quantity × Applicable Share Value`

Example:

Current Share Quantity:

`500`

Share Value:

`NPR 1,000`

Current Valuation:

`NPR 500,000`

---

# 6. Share Valuation Is Not Cash Balance

Shareholder Share Valuation is not the same as:

- Company Cash
- Bank Balance
- Remittance Account Balance
- Profit
- Commission
- Expense

Share valuation represents ownership value according to the approved share rate.

---

# 7. Shareholder Master

The system shall maintain a Shareholder Master.

A Shareholder record may include:

- Shareholder ID
- Name
- Mobile Number
- Address
- Email where applicable
- Identification information where required
- Join Date
- Current Status
- Note
- Attachment
- Created By
- Created At
- Updated By
- Updated At

Exact technical fields shall be defined in the Database Standard.

---

# 8. Shareholder Status

Shareholder status may include:

- Active
- Inactive
- Exited

Historical records shall remain preserved regardless of current status.

---

# 9. Opening Share Holding

When the new system starts, each existing Shareholder's opening Share Quantity shall be based on the approved partner/shareholder meeting.

The new system shall not blindly reconstruct five years of old share activity if the historical data is incomplete.

The approved opening holding becomes the starting Share position.

---

# 10. Opening Share Record

Opening Share records should contain:

- Shareholder
- Opening Share Quantity
- Applicable Share Value
- Calculated Opening Valuation
- Effective Date
- Approval Reference
- Note
- Attachment
- Created By

---

# 11. Opening Share Approval

Opening holdings shall be restricted to authorized Admin-level operation.

They shall be based on approved business records or meeting decisions.

Staff and Shareholder users shall not directly establish their own Opening Share balances.

---

# 12. Share Transaction Types

The system shall distinguish Share transactions.

Core types may include:

1. Opening Share Holding
2. Company-Issued Share Addition
3. Shareholder-to-Shareholder Transfer
4. Share Received
5. Share Transferred Out
6. Partial Company Redemption
7. Full Company Redemption / Exit
8. Approved Share Adjustment

---

# 13. Shareholder-to-Shareholder Transfer

A Shareholder may transfer Share Units to another Shareholder.

Example:

Shareholder A:

`500 shares`

Shareholder B:

`300 shares`

A transfers:

`100 shares`

Result:

- A = `400 shares`
- B = `400 shares`

---

# 14. Financial Effect of Shareholder Transfer

A pure Shareholder-to-Shareholder transfer shall not create a Company Cash or Bank transaction.

Therefore:

- Company Cash: No Effect
- Company Bank: No Effect

The system records ownership movement only.

---

# 15. Private Sale Price Exclusion

The system shall not require the actual private amount paid between two shareholders unless the business later explicitly requires it.

The primary system valuation is based on the approved Share Value per Unit.

Example:

100 Share Units × NPR 1,000

System Valuation:

`NPR 100,000`

Even if the two parties privately settle money separately, the company system does not automatically record that payment.

---

# 16. Share Transfer Required Data

A Shareholder-to-Shareholder transfer should include:

- Transfer Date
- From Shareholder
- To Shareholder
- Share Quantity
- Applicable Share Value
- Calculated Valuation
- Reference
- Note
- Attachment
- Created/Approved By
- Status

---

# 17. Transfer Quantity Validation

A Shareholder shall not transfer more shares than currently owned.

Example:

Current Holding:

`100 shares`

Requested Transfer:

`150 shares`

The system shall reject the transfer.

---

# 18. Zero Share Transfer

A transfer of:

`0 shares`

shall not be accepted as a valid Share Transfer.

---

# 19. Same Shareholder Transfer

A Shareholder shall not transfer shares to the same Shareholder account.

The system shall reject:

`A → A`

---

# 20. New Shareholder Through Transfer

A new person may become a Shareholder by receiving shares from an existing Shareholder.

The process shall:

1. Create/activate the new Shareholder.
2. Record the transfer.
3. Reduce seller Share Quantity.
4. Increase buyer Share Quantity.
5. Preserve transaction evidence.

Company Cash/Bank shall not automatically change.

---

# 21. Existing Shareholder Buying from Another Shareholder

An existing Shareholder may acquire additional shares from another existing Shareholder.

Example:

A = `600 shares`

B = `200 shares`

A sells `100 shares` to B.

Result:

A = `500 shares`

B = `300 shares`

Company Cash/Bank remains unchanged.

---

# 22. Company-Issued New Share

A new share issued directly by the Company is different from a Shareholder Transfer.

When the Company issues new shares:

- Total Company Share Quantity may increase.
- Receiving Shareholder quantity increases.
- Company may receive Cash/Bank value.
- Capital position may change.

Detailed rules require business approval.

---

# 23. Company-Issued Share Financial Effect

If the Company issues:

`100 shares`

at:

`NPR 1,000 per share`

and receives payment into Bank:

- Shareholder: `+100 shares`
- Bank: `+100,000`
- Approved Capital position: `+100,000`

This is not Profit.

---

# 24. Company-Issued Share Is Not Income

Money received for Company-issued shares shall not be counted as normal business Income or Profit.

It represents Capital/Shareholder funding.

---

# 25. Share Addition vs Transfer

The system shall distinguish:

### Share Addition / New Issue
Company creates/allocates new approved shares.

### Share Transfer
Existing shares move from one Shareholder to another.

These shall not use the same financial logic.

---

# 26. Shareholder Partial Exit

A Shareholder may reduce Share Quantity by returning/redeeming approved shares through the Company.

Example:

Current Holding:

`500 shares`

Partial Exit:

`200 shares`

Remaining:

`300 shares`

---

# 27. Partial Exit Payment

If the Company pays for the redeemed shares:

`200 shares × NPR 1,000 = NPR 200,000`

and payment is from Bank:

- Shareholder shares: `-200`
- Bank: `-200,000`

If paid from Cash:

- Shareholder shares: `-200`
- Cash: `-200,000`

---

# 28. Partial Exit Is Not Ordinary Expense

Company redemption of Shareholder shares shall not automatically be treated as ordinary operating Expense.

It is a Shareholder/Capital transaction.

---

# 29. Full Exit

A Shareholder may exit completely.

Full Exit means all remaining approved Share Units are transferred/redeemed according to approved business rules.

After full exit:

- Current Share Quantity = `0`
- Shareholder may become `Exited`
- Historical records remain preserved

---

# 30. Full Exit Payment

If Company redemption causes the full exit, the selected Company Cash or Bank account decreases by the approved settlement amount.

Example:

Remaining Share Quantity:

`500`

Value:

`NPR 1,000`

Company pays:

`NPR 500,000`

Effect:

- Shareholder Shares: `0`
- Selected Cash/Bank: `-500,000`

---

# 31. Transfer-Out Full Exit

A Shareholder may also end with zero shares because all shares are transferred to other Shareholders.

In this case:

- Shareholder Quantity becomes `0`
- Company Cash/Bank has no automatic effect
- Shareholder may be marked Exited/Inactive according to approved rule

---

# 32. Exit Type Distinction

The system shall distinguish between:

### Company Redemption Exit
Company pays the Shareholder.

### Transfer-Out Exit
Shares are transferred to another Shareholder.

The financial effect is different.

---

# 33. Shareholder Cash Withdrawal vs Share Exit

A Shareholder taking money from the Company shall not automatically be considered a Share Exit.

The transaction must clearly identify whether the money represents:

- Share Redemption
- Profit Distribution
- Approved Loan/Advance
- Other approved Shareholder transaction

No ambiguous withdrawal shall silently reduce shareholding.

---

# 34. Shareholder Additional Deposit

A Shareholder depositing money into the Company shall not automatically receive additional Share Units.

The purpose must be identified.

Possible future types include:

- New Share Capital
- Temporary Partner Loan
- Other Approved Contribution

The system shall not guess the purpose.

---

# 35. Capital and Share Quantity Relationship

Where approved:

`Share Quantity × Applicable Share Value`

may represent Share Capital valuation.

However, Capital accounting and Share Quantity shall remain traceable separately.

---

# 36. Share Value Change

If the Business Owner/Partners approve a future change in Share Value, the system shall support a new effective rate.

Example:

Old Value:

`NPR 1,000`

New Value:

`NPR 1,200`

Historical transactions performed under NPR 1,000 shall retain NPR 1,000.

---

# 37. Historical Value Protection

Changing the current Share Value shall not automatically rewrite old Share Transfer, Opening or Redemption records.

Each relevant Share Transaction shall preserve the applicable historical value.

---

# 38. Current Share Valuation After Rate Change

If current valuation is based on a newly approved current Share Value, the system may calculate current valuation using that rate.

However, historical transaction valuation remains unchanged.

The exact valuation policy must follow business approval.

---

# 39. Shareholder Transaction History

The system shall preserve Shareholder history including:

- Date
- Transaction Type
- Quantity In
- Quantity Out
- Resulting Share Quantity
- Applicable Share Value
- Calculated Valuation
- Related Shareholder
- Payment Account where applicable
- Reference
- Note
- Attachment
- User
- Status

---

# 40. Share Ledger

Each Shareholder shall have a Share Ledger.

Example:

| Date | Type | In | Out | Balance |
|---|---|---:|---:|---:|
| Opening | Opening | 500 | 0 | 500 |
| Transfer Received | Transfer | 100 | 0 | 600 |
| Transfer Out | Transfer | 0 | 150 | 450 |
| Redemption | Exit | 0 | 50 | 400 |

---

# 41. Current Share Quantity Traceability

Current Share Quantity must be explainable from Share Ledger history.

The system shall not maintain an unexplained manually edited share quantity.

---

# 42. Shareholder Portal

Shareholder login shall primarily provide access to own Share information.

The Shareholder may view:

- Own Name/Profile
- Current Share Quantity
- Current Approved Share Value
- Current Share Valuation
- Own Share Ledger
- Own Transfer History
- Own Redemption/Exit History
- Permitted Attachments/Documents

---

# 43. Shareholder Portal Restriction

A Shareholder shall not automatically see:

- Another Shareholder's private Share details
- Company Cash
- Bank balances
- Remittance balances
- Detailed Staff transactions
- Expense management
- User management
- System configuration

Any broader visibility requires explicit approval.

---

# 44. Shareholder Self-Editing Restriction

A Shareholder shall not directly edit:

- Own Share Quantity
- Own Share Value
- Own Transfer History
- Own Exit records

These are controlled business records.

---

# 45. Admin Shareholder Management

Admin may manage Shareholder operations according to approved permissions.

This may include:

- Create Shareholder
- Update profile
- Enter approved Opening Shares
- Process Share Transfer
- Process Share Redemption
- Process Exit
- View Share Ledger
- Upload supporting documents
- Correct/cancel authorized transactions

---

# 46. Staff Shareholder Restriction

Staff shall not automatically have Shareholder management permission.

Staff daily operational duties are separate from Share ownership management.

---

# 47. Share Transaction Approval

Share transactions should be limited to authorized Admin operation.

If the business later requires approval workflow, statuses may include:

- Pending
- Approved
- Rejected
- Cancelled

Until such workflow is defined, the system shall not invent approval stages.

---

# 48. Share Transfer Atomicity

A Shareholder Transfer must be completed as one controlled operation.

Example:

A transfers 100 shares to B.

The system must not save:

- A `-100`

without also saving:

- B `+100`

Both effects must succeed together.

---

# 49. Redemption Atomicity

A Company Redemption affecting both:

- Share Quantity
- Cash/Bank Account

must complete as one controlled financial operation.

Example:

- Shareholder `-200 shares`
- Bank `-200,000`

Either both succeed or neither succeeds.

---

# 50. Cancellation of Share Transfer

If an approved Share Transfer is cancelled:

- Seller's Share Quantity must be restored.
- Buyer's Share Quantity must be reduced accordingly.
- Original transaction remains traceable.
- Cancellation reason is preserved.

---

# 51. Cancellation of Company Redemption

If a valid Redemption is cancelled:

- Share Quantity effect must reverse.
- Cash/Bank effect must reverse.
- Original transaction remains in history.

---

# 52. Hard Delete Prohibition

Share transactions with business history shall not be permanently deleted through normal system use.

Cancellation/correction shall preserve the audit trail.

---

# 53. Share Adjustment

An approved Share Adjustment may be required if an opening/reconciliation error is identified.

Adjustment shall be tightly controlled and must contain:

- Shareholder
- Quantity Adjustment
- Direction
- Reason
- Date
- Note
- Attachment
- Authorized User

Adjustment shall not be used instead of normal transfer/redemption workflows.

---

# 54. Shareholder Note

Shareholder Master shall support Note.

Share Transactions shall also support Note.

Notes may document:

- Meeting decision
- Transfer condition
- Exit reason
- Special approval
- Other relevant information

---

# 55. Shareholder Attachment

Shareholder and Share Transaction records shall support Attachment/Upload.

Examples:

- Partnership agreement
- Share certificate
- Meeting minute
- Share transfer approval
- Exit agreement
- Payment proof
- ID document where legally/business required
- PDF/Image

---

# 56. Attachment Historical Integrity

Attachments related to historical Share transactions shall not automatically be deleted if the transaction is cancelled.

They remain part of the audit record.

---

# 57. Share Certificate

A future Share Certificate/Statement may be generated from system data if required.

Such certificate shall reflect:

- Shareholder
- Share Quantity
- Applicable Value
- Date
- Relevant approval

This is optional unless specifically included in UI/report scope.

---

# 58. Shareholder Statement

The system should support a Shareholder Statement showing:

- Opening Share Quantity
- Shares Received
- Shares Added
- Shares Transferred Out
- Shares Redeemed
- Current Share Quantity
- Current Valuation

---

# 59. Share Transfer Statement

Admin should be able to view Share Transfer history by:

- Date
- Seller
- Buyer
- Quantity
- Applicable Value
- Status

---

# 60. Exited Shareholder History

Exited Shareholders shall remain searchable to authorized users.

Their historical records must remain intact.

---

# 61. Shareholder Re-Entry

If an Exited Shareholder later acquires shares again, the system may reactivate the existing Shareholder identity rather than create an unnecessary duplicate.

The new transaction history shall continue from the existing record.

---

# 62. Duplicate Shareholder Control

The system should reduce duplicate Shareholder records using available identity information such as:

- Name
- Mobile
- Identification details

Duplicate detection shall not incorrectly merge different persons.

---

# 63. Shareholder Search

Authorized Admin shall be able to search Shareholders by appropriate criteria such as:

- Name
- Mobile
- Status
- Share Quantity

---

# 64. Current Shareholder List

Admin may view a summary such as:

| Shareholder | Share Quantity | Value Per Share | Total Valuation | Status |
|---|---:|---:|---:|---|
| A | 500 | 1,000 | 500,000 | Active |
| B | 300 | 1,000 | 300,000 | Active |

---

# 65. Total Share Quantity

The system should be able to determine total active Share Quantity.

The effect of Company-issued shares and Company redemption on total issued shares must follow approved business rules.

---

# 66. Total Share Valuation

The system may provide:

`Total Active Shares × Current Approved Share Value`

for management visibility.

This shall not be treated as actual available Cash.

---

# 67. Profit Does Not Automatically Add Shares

Monthly Profit shall not automatically increase Shareholder Share Quantity.

Shares change only through approved Share transactions.

---

# 68. Loss Does Not Automatically Reduce Shares

Monthly Loss shall not automatically reduce Share Quantity.

Share Quantity remains unchanged unless an approved Share transaction occurs.

---

# 69. Profit Distribution Is Separate

If Profit is distributed to Shareholders, it shall be handled through a separately approved Profit Distribution rule.

Profit Distribution shall not automatically change Share Quantity unless explicitly approved.

---

# 70. Shareholder Bank/Cash Visibility

Shareholder ownership does not automatically grant access to Company Cash/Bank balances through the system.

Visibility is controlled by Role Permission rules.

---

# 71. Legacy Shareholder Data

The old Microsoft Access Shareholder table may be used as reference during the closing/opening review.

However, the final new-system Opening Share Quantity shall come from the approved meeting decision.

---

# 72. Legacy Share Transaction Migration

Historical Share transactions do not need to be reconstructed into the new live Share Ledger unless explicitly approved.

The old system can remain as a legacy reference.

---

# 73. Opening Meeting Requirement

Before the new system starts, the business should confirm:

- Current Shareholders
- Current Share Quantity of each Shareholder
- Current approved Share Value
- Any pending transfer
- Any pending exit/redemption
- Current status

These approved values become the new-system starting position.

---

# 74. Business Date

Each Share Transaction must have an approved Business/Effective Date.

Created At remains separate for audit.

---

# 75. Backdated Share Transaction

Backdated Share transactions shall not be unrestricted.

Exact permission and closed-period rules shall be defined in the Permission/Workflow standards.

---

# 76. Transaction Reference

Share Transactions may support:

- Meeting reference
- Agreement number
- Voucher number
- Internal reference
- Other approved reference

---

# 77. Created By

Every Share Transaction shall preserve the responsible system user.

Admin activity shall remain auditable.

---

# 78. Updated/Cancelled By

If a Share Transaction is corrected/cancelled, the system shall preserve relevant audit information.

---

# 79. No Hidden Share Movement

No background process shall change Share Quantity without an identifiable Share Transaction or approved opening event.

---

# 80. No Direct Current Share Editing

Current Share Quantity, if stored for performance, shall not be treated as an ordinary editable profile field.

Share transactions control ownership movement.

---

# 81. Share Quantity Must Not Be Negative

A Shareholder's current Share Quantity shall not become negative.

The system shall reject any transaction that would produce negative Share ownership.

---

# 82. Share Decimal Rule

Share Units are currently understood as whole units.

Fractional shares shall not be assumed.

If fractional Share Units are ever required, a new approved rule is necessary.

---

# 83. Share Value Currency

Current Share Value is in Nepalese Rupees.

Multi-currency Share valuation shall not be introduced without explicit approval.

---

# 84. Reporting Filters

Shareholder reports may support filters such as:

- Date Range
- Shareholder
- Transaction Type
- Status
- Active/Exited

---

# 85. Shareholder Dashboard Summary

The Shareholder Dashboard may show:

- Current Share Quantity
- Current Value Per Share
- Total Share Valuation
- Recent Share Transactions

It should remain simple and primarily read-only.

---

# 86. Admin Dashboard Share Summary

Admin Dashboard may show authorized summaries such as:

- Total Active Shareholders
- Total Shares
- Recent Transfers
- Recent Exits

Exact UI is defined later.

---

# 87. Confidentiality Principle

Shareholder data shall be treated as sensitive business information.

Users shall only access data allowed by Role Permission.

---

# 88. Shareholder Document Access

A Shareholder may view only those documents/attachments authorized for that Shareholder.

Admin-only internal documents shall not automatically be exposed.

---

# 89. Unresolved Shareholder Rules

The following remain to be confirmed before related implementation:

1. Whether Company-issued new shares are required in Version 1.
2. Whether Share Value can change in normal operation.
3. Who has authority to approve Share Transfers.
4. Who has authority to approve Company Redemption.
5. Whether any Shareholder transaction requires multi-partner approval.
6. Profit Distribution rules.
7. Temporary Shareholder loans/deposits.
8. Exact Shareholder mandatory fields.
9. Whether Share Certificates are required.
10. Exact treatment of company-held/redeemed shares after exit.

These shall not be guessed.

---

# 90. Testing Requirement

Testing shall verify:

- Opening Share Holdings are correct.
- Share Transfer reduces seller and increases buyer.
- Company Cash/Bank does not change in Shareholder-to-Shareholder transfer.
- Transfer cannot exceed seller's holding.
- Share Quantity never becomes negative.
- Company Redemption reduces Shareholding and selected Cash/Bank.
- Transfer-out exit does not reduce Company Cash/Bank.
- Company-issued shares, if implemented, increase correct Capital/Account.
- Profit/Loss does not automatically change shares.
- Cancellation reverses Share effects correctly.
- Historical rates remain preserved.
- Notes and Attachments remain linked.
- Shareholder sees only permitted own data.
- Staff cannot perform unauthorized Share management.

---

# 91. Acceptance Rule

The Shareholder Module is acceptable only when:

- Current ownership is traceable.
- Share transactions preserve history.
- Shareholder-to-Shareholder transfers do not falsely affect Company finances.
- Company redemption correctly affects Company Cash/Bank.
- Current Share Quantity cannot be arbitrarily overwritten.
- Exited Shareholders retain history.
- Shareholder-role access remains restricted.
- Notes and supporting documents remain preserved.
- Share valuation remains separate from Cash/Profit.

---

# 92. Development Rule

No developer or AI coding agent may invent Shareholder logic.

If a Share transaction type or financial effect is unclear:

**STOP → REVIEW BUSINESS CONSTITUTION → REVIEW THIS STANDARD → ASK BUSINESS OWNER → DOCUMENT → APPROVE → IMPLEMENT**

---

# 93. Document Authority

This document is the authoritative detailed standard for Shareholder and Share Management.

It is subordinate to:

`01_REMITTANCE_BUSINESS_CONSTITUTION.md`

and must be read together with:

- `02_PROJECT_SCOPE_AND_REQUIREMENTS.md`
- `03_ACCOUNT_AND_TRANSACTION_STANDARD.md`
- `04_COMMISSION_EXPENSE_AND_PROFIT_STANDARD.md`

Dependent documents include:

- `06_USER_ROLE_PERMISSION_STANDARD.md`
- `07_DATABASE_AND_DATA_STANDARD.md`
- `08_UI_AND_WORKFLOW_STANDARD.md`
- `09_SECURITY_AUDIT_BACKUP_DEPLOYMENT_STANDARD.md`

---

# 94. Approval

**Status:** FINAL DRAFT — Pending Business Owner Approval

After Business Owner approval, this document becomes the authoritative Shareholder Standard for design, development, testing and maintenance of the Remittance Management System.

---

**END OF DOCUMENT**