# REMITTANCE MANAGEMENT SYSTEM — ACCOUNT AND TRANSACTION STANDARD

**Document ID:** 03  
**File Name:** `03_ACCOUNT_AND_TRANSACTION_STANDARD.md`  
**Project:** Remittance Management System  
**Document Type:** Financial Account and Transaction Standard  
**Version:** 1.0  
**Status:** FINAL DRAFT — Pending Business Owner Approval  
**Authority:** Business Owner  

---

# 1. Purpose

This document defines the financial account structure and transaction behavior of the Remittance Management System.

It governs:

- Account Master
- Account types
- Opening balances
- Cash movement
- Bank/BLB movement
- Remittance account movement
- Deposit
- Withdrawal
- Remittance
- Account transfer
- Negative balances
- Month-end settlement
- Account ledger
- Balance calculation
- Transaction status
- Cancellation
- Correction
- Notes
- Attachments
- Financial traceability

This document must comply with:

1. `01_REMITTANCE_BUSINESS_CONSTITUTION.md`
2. `02_PROJECT_SCOPE_AND_REQUIREMENTS.md`
3. `00_PROJECT_INDEX.md`

If this document conflicts with the Business Constitution, the Business Constitution shall take precedence.

---

# 2. Core Financial Principle

The system shall manage real business money through approved Financial Accounts.

After approved Opening Balances are entered, Financial Account balances shall change only through authorized financial transactions.

Users shall not directly overwrite Current Balance merely to make the system match an expected figure.

---

# 3. Account Master

All balance-holding business accounts shall be maintained through an Account Master.

Examples include:

- Cash
- Business Bank Account
- BLB
- Citizen Remit
- City Express
- IME Remit
- IME Pay
- IPS
- Other approved operational accounts

A new account shall normally be added through Account Master rather than by creating a new database column.

---

# 4. Account Categories

Accounts may be categorized by business purpose.

Initial categories may include:

1. Cash
2. Bank
3. Remittance
4. BLB / Banking Service
5. Operational Service
6. Other Financial Account

The category assists reporting and workflow.

Account category shall not replace the actual Account identity.

---

# 5. Account Master Information

An Account may contain:

- Account ID
- Account Name
- Account Category
- Optional Account Number
- Optional Bank/Provider Name
- Opening Balance
- Current Status
- Note
- Attachment
- Created By
- Created At
- Updated By
- Updated At

Exact technical fields shall be defined in the Database Standard.

---

# 6. Account Status

An Account may have an operational status such as:

- Active
- Inactive

An inactive account shall not normally be selectable for new transactions.

Historical transactions of an inactive account shall remain preserved.

An Account shall not be deleted merely because the business no longer uses it.

---

# 7. Cash Account

The system shall contain an approved Cash Account representing physical business cash.

Cash shall increase when physical money is received.

Cash shall decrease when physical money is paid out.

Examples of Cash increase:

- Customer remittance payment
- Customer deposit
- Service Charge received in Cash
- Commission received in Cash
- Other approved Cash receipt

Examples of Cash decrease:

- Customer withdrawal
- Expense paid in Cash
- Shareholder redemption paid in Cash
- Account transfer from Cash to another account

---

# 8. Bank Account

A Business Bank Account shall represent actual money controlled by the business in a bank.

Bank shall increase when money is deposited/received into that Bank Account.

Bank shall decrease when money is transferred or paid from that Bank Account.

Bank accounts may be used for:

- Remittance settlement
- Commission receipt
- Expense payment
- Shareholder exit/redemption payment
- Account transfer
- Other approved financial activity

---

# 9. BLB / Banking Service Account

BLB and similar service accounts shall maintain an operational balance in the new system.

This corrects the limitation of the legacy system where BLB transaction principal was not fully reflected in the software.

BLB may contain:

- Positive balance
- Zero balance
- Negative balance

The actual effect depends on transaction type.

---

# 10. Remittance Account

Each remittance/service provider that maintains an operational business balance shall have its own Financial Account.

Examples:

- Citizen Remit
- City Express
- IME Remit
- IME Pay
- Other approved provider

A remittance account may become negative during normal business activity.

---

# 11. Customer Is Not an Account

Customer is not a Financial Account.

No Customer Current Balance shall be maintained.

Customer identity may be linked to transactions for history and reporting.

Customer transactions shall affect approved business accounts, not a customer ledger.

---

# 12. Opening Balance

Each applicable Financial Account may receive an approved Opening Balance when the new system begins.

Opening Balance may be:

- Positive
- Zero
- Negative

Opening Balance must originate from the approved partner/shareholder opening decision.

---

# 13. Opening Balance Requirements

An Opening Balance record should contain:

- Opening Date
- Account
- Opening Amount
- Balance Direction where technically required
- Approval Reference
- Note
- Attachment
- Created By
- Created At

Supporting evidence may include:

- Meeting minute
- Bank statement
- Physical cash verification
- Provider account statement
- Other approved document

---

# 14. Opening Balance Restriction

Opening Balance shall be restricted to authorized Admin-level operation.

Staff shall not be permitted to create or arbitrarily change approved Opening Balances.

Once finalized and transactions begin, changes to Opening Balance shall require controlled correction procedures.

---

# 15. Current Balance

Current Balance shall represent the resulting financial position of an Account after approved transactions.

Conceptually:

`Current Balance = Opening Balance + Account Increases - Account Decreases`

The technical system may calculate or maintain this efficiently, but the financial result must always be traceable to transaction history.

---

# 16. Running Ledger

Each Financial Account shall have a ledger.

The ledger should identify:

- Business Date
- Transaction Type
- Transaction Reference
- Related Customer where applicable
- Amount In
- Amount Out
- Resulting Balance
- User
- Note
- Transaction Status

This provides traceability of how the account reached its current balance.

---

# 17. Positive Balance

A positive balance indicates the Account currently has a positive financial position according to approved business logic.

Positive balance shall be displayed clearly to authorized users.

---

# 18. Zero Balance

Zero indicates the Account is currently settled to zero.

Zero balance does not mean transaction history is deleted.

The account ledger remains preserved.

---

# 19. Negative Balance

Approved Remittance, BLB or other operational accounts may become negative.

Negative balance is a valid business condition.

The system shall not automatically reject an authorized transaction merely because the account would become negative.

---

# 20. Negative Balance Example

City Express opening/current balance:

`NPR 2,000`

Customer remittance:

`NPR 10,000`

Result:

`NPR 2,000 - NPR 10,000 = NPR -8,000`

The system shall allow:

**City Express = NPR -8,000**

---

# 21. Transaction Types

The system shall distinguish transaction types.

Core transaction types include:

1. Opening Balance
2. Remittance
3. Deposit
4. Withdrawal
5. Account Transfer
6. Commission Receipt
7. Expense Payment
8. Month-End Settlement
9. Shareholder Redemption Payment
10. Approved Adjustment/Correction

Additional types require approved business requirements.

---

# 22. Remittance Transaction

A Remittance Transaction occurs when the business sends principal money through a remittance provider for a Customer.

The transaction shall normally identify:

- Business Date
- Customer
- Remittance Account
- Principal Amount
- Service Charge
- Reference Number
- Note
- Attachment
- Created By

---

# 23. Remittance Financial Effect

Where Customer provides Cash:

- Cash increases by Principal Amount.
- Remittance Account decreases by Principal Amount.
- Cash additionally increases by Service Charge collected in Cash.

Example:

Principal:

`NPR 10,000`

Service Charge:

`NPR 100`

Result:

- Cash: `+10,100`
- City Express: `-10,000`

---

# 24. Remittance with Zero Service Charge

A Remittance Transaction may have zero Service Charge where permitted.

Example:

Principal:

`NPR 10,000`

Service Charge:

`NPR 0`

Effect:

- Cash: `+10,000`
- Remittance Account: `-10,000`

---

# 25. Principal and Service Charge

Principal Amount and Service Charge must remain separately identifiable.

The system shall not save only a combined amount if doing so prevents financial reporting from identifying Service Charge.

---

# 26. Deposit Transaction

A Deposit Transaction represents the approved business process where a Customer gives Cash and the corresponding BLB/Banking Service account decreases.

Typical fields:

- Date
- Customer
- Selected BLB/Bank Service Account
- Principal Amount
- Service Charge
- Reference
- Note
- Attachment
- User

---

# 27. Deposit Financial Effect

Example:

Customer Deposit:

`NPR 10,000`

Service Charge:

`NPR 100`

Effect:

- Cash: `+10,100`
- BLB Account: `-10,000`

No Customer Balance is created.

---

# 28. Withdrawal Transaction

A Withdrawal Transaction represents the approved business process where the Customer receives Cash and the selected BLB/Bank Service Account increases.

Typical fields:

- Date
- Customer
- Selected Account
- Principal Amount
- Service Charge
- Reference
- Note
- Attachment
- User

---

# 29. Withdrawal Base Financial Effect

Ignoring Service Charge treatment until final confirmation:

Withdrawal:

`NPR 10,000`

Effect:

- Cash: `-10,000`
- BLB Account: `+10,000`

No Customer Balance is created.

---

# 30. Withdrawal Service Charge — Pending Rule

The exact Service Charge treatment for Withdrawal remains unresolved.

The system shall not guess whether:

### Option A
Customer receives full principal and pays Service Charge separately.

or

### Option B
Service Charge is deducted from the Cash amount delivered.

This must be confirmed before implementation.

---

# 31. Account Transfer

An Account Transfer moves existing business funds from one Financial Account to another.

It shall identify:

- Business Date
- Send Account
- Receive Account
- Amount
- Reference
- Note
- Attachment
- Created By

---

# 32. Account Transfer Financial Effect

Example:

Bank → City Express

Amount:

`NPR 50,000`

Effect:

- Bank: `-50,000`
- City Express: `+50,000`

No income or expense is created merely by moving money between accounts.

---

# 33. Same Account Transfer Prohibition

Send Account and Receive Account shall not be the same account.

The system must reject:

`Cash → Cash`

or

`City Express → City Express`

as a normal transfer.

---

# 34. Zero Amount Transaction

Financial transactions with zero Principal/Transfer/Expense/Commission amount shall not normally be allowed unless the specific business transaction legitimately contains only another financial component such as Service Charge.

The detailed validation shall depend on transaction type.

---

# 35. Negative Transfer Result

An Account Transfer may cause the Send Account to become negative only where that Account is authorized to allow negative balance.

Account-specific negative permission may be implemented if required.

---

# 36. Month-End Settlement

Month-End Settlement is a specific Account Transfer used to settle negative Remittance/Operational accounts.

Example:

City Express:

`-80,000`

Settlement:

Bank → City Express

`80,000`

Result:

- Bank: `-80,000`
- City Express: `0`

---

# 37. Settlement Need Not Delete Negative History

When an account is settled to zero, prior negative transactions shall remain in the ledger.

The settlement transaction shall explain how the account returned to zero.

---

# 38. Partial Settlement

The system may support partial settlement.

Example:

City Express:

`-80,000`

Bank Transfer:

`50,000`

Result:

City Express:

`-30,000`

The system shall not require all settlements to produce zero unless business rules explicitly require it.

---

# 39. Commission Receipt Transaction

Commission Receipt shall record income received from an approved remittance/service provider.

The transaction shall identify:

- Date
- Provider
- Commission Amount
- Receiving Account
- Reference
- Note
- Attachment
- User

Detailed income treatment is governed by:

`04_COMMISSION_EXPENSE_AND_PROFIT_STANDARD.md`

---

# 40. Commission Receipt Account Effect

Example:

Commission from Citizen Remit:

`NPR 5,000`

Received into Bank:

- Bank: `+5,000`
- Commission Income: `+5,000`

The account effect and income recognition are related but separate reporting concepts.

---

# 41. Expense Payment Transaction

Expense Payment shall identify:

- Date
- Expense Category
- Amount
- Payment Account
- Particular
- Reference
- Note
- Attachment
- User

Detailed Expense reporting is governed separately.

---

# 42. Expense Account Effect

Expense paid from Cash:

`NPR 20,000`

Effect:

- Cash: `-20,000`

Expense paid from Bank:

`NPR 20,000`

Effect:

- Bank: `-20,000`

The Expense amount shall also be recorded for Profit/Loss reporting.

---

# 43. Shareholder Redemption Payment

Where the company pays an approved Shareholder exit/redemption amount:

- Selected Cash or Bank account decreases.
- Shareholder holding decreases according to the Shareholder Standard.

Example:

Share redemption:

`NPR 200,000`

Paid from Bank:

- Bank: `-200,000`

The share effect is governed by:

`05_SHAREHOLDER_STANDARD.md`

---

# 44. Shareholder-to-Shareholder Transfer Exclusion

A pure Shareholder-to-Shareholder share transfer shall not create a Financial Account transaction.

Example:

A transfers 100 shares to B.

No effect on:

- Cash
- Bank
- Remittance Accounts

unless a separate approved company financial transaction also occurs.

---

# 45. Transaction Reference

Transactions should support an appropriate Reference Number.

Reference may represent:

- Remittance number
- Bank reference
- BLB reference
- Voucher number
- Internal transaction number
- Other approved reference

Reference does not replace the internal system transaction ID.

---

# 46. Internal Transaction ID

Every transaction shall have a unique internal system identifier.

The internal ID shall remain stable even if an external Reference Number is edited according to permitted rules.

---

# 47. Business Date

Every financial transaction shall have a Business Date.

Business Date represents the date of the business transaction.

System creation time represents when the record was actually entered.

These values may differ.

---

# 48. Creation Timestamp

Each transaction shall preserve:

- Created At
- Created By

These values must not be confused with Business Date.

---

# 49. Backdated Transactions

Whether Staff/Admin may create backdated transactions shall be defined before implementation.

The future rule may consider:

- Role
- Closed period
- Reason
- Approval
- Audit

No unrestricted backdating should be assumed.

---

# 50. Transaction Status

Financial transactions shall support status.

Core statuses shall include at minimum:

- Active
- Cancelled

Architecture may additionally support:

- Reversed
- Corrected

if required by the final correction workflow.

---

# 51. Active Transaction

An Active transaction contributes to financial balances according to its approved account effects.

---

# 52. Cancelled Transaction

A Cancelled transaction shall no longer contribute to active financial balances after cancellation is completed.

However, the transaction record must remain available for audit.

---

# 53. Hard Delete Prohibition

Normal users shall not permanently delete financial transactions.

Financial history shall not disappear through standard application operation.

---

# 54. Cancellation Requirements

Cancellation should require:

- Transaction ID
- Cancellation Reason
- Authorized User
- Cancelled At
- Original transaction preservation

Depending on permission rules, Staff may need Admin approval.

---

# 55. Cancellation Financial Reversal

When an active transaction is cancelled, its account effect must be reversed correctly.

Example original Remittance:

- Cash `+10,100`
- City Express `-10,000`

After approved cancellation:

- Cash effect is reversed
- City Express effect is reversed
- Service Charge effect is reversed

The original transaction remains marked Cancelled.

---

# 56. Correction Principle

Where a transaction was entered incorrectly, correction should normally use:

1. Cancel/Reversal of incorrect transaction.
2. Creation of corrected transaction.

This provides clear audit history.

Directly rewriting a completed financial transaction should be restricted.

---

# 57. Correction Link

Where applicable, the system should preserve a link between:

- Original Transaction
- Cancel/Reversal Record
- Replacement Transaction

This assists audit and reporting.

---

# 58. Adjustment Transaction

An Adjustment may be required when approved financial reconciliation identifies a legitimate difference that cannot be represented as an ordinary transaction.

Adjustments must be tightly controlled.

Adjustment should contain:

- Account
- Amount
- Direction
- Reason
- Note
- Attachment
- Approval/User
- Date

The system shall not use Adjustment as a shortcut for normal transaction entry.

---

# 59. Account Reconciliation

Authorized Admin may compare system balances with actual external balances.

Examples:

- Physical Cash
- Bank statement
- BLB/provider statement
- Remittance provider balance

Differences shall be investigated rather than silently overwriting Current Balance.

---

# 60. Cash Reconciliation

Physical Cash should be capable of being compared against system Cash.

If physical Cash differs from system Cash, the system should support investigation by reviewing:

- Daily transactions
- Service Charges
- Withdrawals
- Expenses
- Transfers
- Cancelled transactions
- Staff activity

Any final adjustment must follow approved Adjustment rules.

---

# 61. Service Charge Audit

Each transaction that contains a Service Charge shall retain enough information to identify:

- Service Charge amount
- Customer
- Transaction type
- Service/provider
- Staff/User
- Date
- Transaction status

This is required to control daily Service Charge collections.

---

# 62. Duplicate Transaction Control

The system should support reasonable validation against accidental duplicate entries.

Potential checks may include:

- Same external reference
- Same service
- Same amount
- Same customer
- Same date

Duplicate detection shall not automatically reject legitimate repeated transactions without suitable logic.

---

# 63. Transaction Atomicity

Any business operation that affects multiple financial accounts must complete as a single controlled database transaction.

Example Remittance:

- Cash increase
- Remittance Account decrease
- Service Charge effect

All effects must succeed together.

If one required operation fails, the whole financial save must fail.

Partial account updates are prohibited.

---

# 64. Balance Consistency

Account balance and ledger transaction history must remain consistent.

The system shall not maintain one balance value that cannot be reconciled with transaction history.

---

# 65. Account Ledger Immutability Principle

Ledger history shall remain traceable.

Where corrections are necessary, the system should preserve the original event rather than silently rewriting history.

---

# 66. Transaction Note

Every major transaction type shall support Note.

Note may explain:

- Special transaction condition
- Customer instruction
- Correction reason
- Settlement detail
- Supporting information

Note shall not replace mandatory financial fields.

---

# 67. Transaction Attachment

Every major transaction type shall support Attachment/Upload where applicable.

Attachments may include:

- Receipt
- Voucher
- Screenshot
- Bank transfer proof
- Provider statement
- Image
- PDF

---

# 68. Multiple Supporting Files

The data architecture should support one or more attachments per transaction if required.

Exact limits shall be defined in the Security/Data Standard.

---

# 69. Cancelled Transaction Attachment

Attachments related to a cancelled financial transaction shall not automatically be deleted.

They form part of historical evidence.

---

# 70. Account Note and Attachment

Account Master itself should also support:

- Note
- Attachment

where useful.

Example:

Bank account document or provider agreement may be attached if approved.

---

# 71. Ledger Search

Authorized users shall be able to search an account ledger using appropriate criteria such as:

- Date Range
- Transaction Type
- Customer
- Reference
- User
- Status

---

# 72. Account Balance Dashboard

Authorized Admin may view account balances in summarized form.

Example:

| Account | Balance |
|---|---:|
| Cash | NPR 250,000 |
| Bank | NPR 800,000 |
| Citizen Remit | NPR 30,000 |
| City Express | NPR -50,000 |
| BLB | NPR -20,000 |

Negative amounts shall remain visually distinguishable.

---

# 73. Staff Balance Visibility

Staff shall not automatically be granted visibility of all sensitive Financial Account balances.

The exact balances or operational account information Staff can view shall be controlled by:

`06_USER_ROLE_PERMISSION_STANDARD.md`

---

# 74. Shareholder Balance Visibility

Shareholder users shall not automatically see company Cash, Bank, Remittance or BLB balances.

Shareholder access is defined separately and primarily relates to own share information.

---

# 75. Financial Account Creation Permission

Creation or editing of Financial Accounts shall normally be restricted to Admin.

Staff shall select existing authorized accounts for daily transactions.

---

# 76. Account Deactivation

An Account with historical transactions may be made Inactive.

Historical transactions shall remain available.

Deactivation shall not remove historical ledger effects.

---

# 77. Account Deletion

An Account that has financial history shall not be hard-deleted through ordinary system use.

Where an account was created by mistake and has no valid history, deletion policy may be defined in the Database Standard.

---

# 78. Opening Account Mapping

At new-system cutover, all approved balances must be mapped to clearly identified Financial Accounts.

Examples:

- Physical Cash → Cash
- Asha Enterprises Bank → Bank Account
- BLB → BLB Account
- City Express → City Express Account
- Citizen Remit → Citizen Remit Account

Ambiguous opening figures shall be resolved before entry.

---

# 79. Legacy Transaction Isolation

Old Microsoft Access financial transactions shall not be inserted into the new live ledger unless explicitly approved.

If Legacy Records are displayed in the future, they must remain isolated from live balances.

---

# 80. Currency

The current business accounting currency is expected to be Nepalese Rupees unless otherwise approved.

The exact currency architecture shall be finalized before database implementation.

No multi-currency behavior shall be invented without approval.

---

# 81. Decimal Precision

Financial amount precision must be defined consistently across:

- Principal Amount
- Service Charge
- Commission
- Expense
- Balance
- Transfers

The Database Standard shall define the exact numeric precision.

Floating-point storage shall not be used for financial amounts.

---

# 82. Transaction Validation

Before saving a financial transaction, the system must validate required information.

Examples:

- Valid account
- Valid transaction type
- Valid amount
- Required customer where applicable
- Required receiving/sending account
- Different Send/Receive accounts
- Valid Business Date
- Authorized user

---

# 83. Financial Amount Sign

User-facing entry should normally accept positive amount values.

The system transaction type determines whether the amount increases or decreases an account.

Users should not be required to manually type negative amounts merely to represent outgoing money, except where specifically approved.

---

# 84. Negative Opening Balance

Negative amount may be valid for an Opening Balance of an approved operational/remittance account.

This represents an existing negative business position at cutover.

---

# 85. Manual Current Balance Editing

A Current Balance field, if physically stored for performance, must not be treated as an ordinary editable business field.

Authorized transactions must control its movement.

---

# 86. Reports from Transactions

Account reports shall be based on approved transaction history.

Current Balance reports must be reconcilable with the ledger.

---

# 87. End-of-Day Review

The system may provide an Admin End-of-Day review showing:

- Transaction Count
- Principal Amount
- Service Charge
- Cash movement
- Account movements
- Staff activity
- Negative account balances

This is a reporting feature and does not automatically close the day unless a separate closing rule is approved.

---

# 88. Month Closing

Formal month-locking/reopening rules are not yet finalized.

Month-End Settlement is allowed as a financial operation, but accounting-period locking requires separate approval.

---

# 89. Transaction Ownership

Every transaction shall identify the user responsible for creating it.

This supports Staff accountability.

---

# 90. Admin-Entered Transactions

Transactions created by Admin shall also preserve Created By.

Admin transactions are not exempt from audit.

---

# 91. System-Generated Transactions

If future workflows generate automatic entries, they must identify:

- Source transaction
- System-generated nature
- Creation time
- Financial effect

Automatic entries shall remain traceable.

---

# 92. No Hidden Balance Movement

No background process shall alter a Financial Account balance without an identifiable transaction or approved accounting event.

---

# 93. Account Summary

Each account may provide summary values such as:

- Opening Balance
- Total In
- Total Out
- Current Balance

These values must agree with the account ledger.

---

# 94. Transaction Detail View

Authorized users shall be able to inspect a transaction and see relevant details including:

- Transaction ID
- Date
- Type
- Customer
- Accounts
- Principal
- Service Charge
- Reference
- Note
- Attachments
- Created By
- Status
- Cancellation/Correction history

---

# 95. Print / Export

Reports and transaction information may support print/export according to the UI/Workflow Standard.

Exported information must reflect the same approved account logic as on-screen reports.

---

# 96. Unresolved Account Rules

Before coding, the following remain to be finalized:

1. Withdrawal Service Charge treatment.
2. Which specific account types may go negative.
3. Backdated transaction permission.
4. Closed-month behavior.
5. Exact adjustment approval workflow.
6. Whether Staff can see operational balances while entering transactions.
7. Exact initial Account Master fields.
8. Currency/decimal precision details if anything other than standard NPR accounting is required.

---

# 97. Development Rule

No developer or AI coding agent may create alternative financial logic based on convenience.

If the required debit/credit effect of a transaction is unclear:

**STOP → READ BUSINESS CONSTITUTION → READ THIS STANDARD → ASK BUSINESS OWNER → DOCUMENT RULE → IMPLEMENT**

---

# 98. Testing Requirement

Every financial transaction type must be tested for:

- Correct Account increase
- Correct Account decrease
- Service Charge effect
- Negative balance behavior
- Cancellation reversal
- Correction history
- Note preservation
- Attachment preservation
- User attribution
- Ledger consistency

Testing shall include both positive and negative account balance scenarios.

---

# 99. Acceptance Rule

The Account and Transaction module is acceptable only when:

- Opening Balances are correctly established.
- All active transactions affect the correct accounts.
- Negative balances behave as approved.
- Customer balance is never created.
- Account transfer does not create false income/expense.
- Service Charge remains separate from Principal.
- Cancelled transactions reverse correctly.
- Ledger balances reconcile.
- Transaction history remains auditable.
- Notes and Attachments remain linked.
- Staff/Admin permissions are respected.

---

# 100. Document Authority

This document provides the authoritative detailed rules for Financial Accounts and Financial Transactions.

It is subordinate to:

`01_REMITTANCE_BUSINESS_CONSTITUTION.md`

and must be read together with:

`02_PROJECT_SCOPE_AND_REQUIREMENTS.md`

Related documents include:

- `04_COMMISSION_EXPENSE_AND_PROFIT_STANDARD.md`
- `05_SHAREHOLDER_STANDARD.md`
- `06_USER_ROLE_PERMISSION_STANDARD.md`
- `07_DATABASE_AND_DATA_STANDARD.md`
- `08_UI_AND_WORKFLOW_STANDARD.md`
- `09_SECURITY_AUDIT_BACKUP_DEPLOYMENT_STANDARD.md`

---

# 101. Approval

**Status:** FINAL DRAFT — Pending Business Owner Approval

After Business Owner approval, this document becomes the authoritative Account and Transaction Standard for design, development, testing and maintenance of the Remittance Management System.

---

**END OF DOCUMENT**