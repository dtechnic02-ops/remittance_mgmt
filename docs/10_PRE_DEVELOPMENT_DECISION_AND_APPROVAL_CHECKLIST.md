# REMITTANCE MANAGEMENT SYSTEM — PRE-DEVELOPMENT DECISION AND APPROVAL CHECKLIST

**Document ID:** 10  
**File Name:** `10_PRE_DEVELOPMENT_DECISION_AND_APPROVAL_CHECKLIST.md`  
**Project:** Remittance Management System  
**Document Type:** Pre-Development Decision & Approval Checklist  
**Version:** 1.0  
**Status:** WORKING DRAFT  
**Authority:** Business Owner  

---

# 1. Purpose

This document collects all unresolved business and implementation decisions that must be confirmed before development begins.

Its purpose is to prevent:

- Developer assumptions
- AI-invented business logic
- Financial calculation mistakes
- Scope expansion
- Database redesign during development
- Permission confusion
- Old-system logic accidentally entering the new system

This document does not override the Business Constitution.

The highest authority remains:

`01_REMITTANCE_BUSINESS_CONSTITUTION.md`

---

# 2. Development Gate

Development shall not begin until all items marked:

**BLOCKING**

are confirmed.

Decision process:

**QUESTION → BUSINESS OWNER DECISION → DOCUMENT UPDATE → APPROVAL → DEVELOPMENT**

---

# 3. Legacy System Closing

### Decision 3.1 — Legacy Cut-Off Date

**Status:** BLOCKING

Confirm the official date on which the Microsoft Access system stops being the live operational system.

Decision:

`____________________________`

---

### Decision 3.2 — Legacy Financial History

Approved principle:

Old financial transactions shall not become the live ledger of the new system.

They remain:

**Legacy / Historical Records**

Status:

**APPROVED PRINCIPLE**

---

### Decision 3.3 — Customer Migration

Approved principle:

Only cleaned Customer Master data shall be migrated from the old system where required.

Customer migration shall not create financial balances.

Status:

**APPROVED PRINCIPLE**

---

# 4. Opening Balance Approval

Before Go-Live, the partners/shareholders shall verify and approve the opening position.

Required balances may include:

- Cash
- Business Bank
- BLB
- Citizen Remit
- City Express
- IME Remit
- IME Pay
- IPS
- Other active operational accounts

For each account confirm:

- Account Name
- Positive / Negative position
- Opening Amount
- Evidence
- Approval

---

# 5. Opening Shareholder Position

Before Go-Live confirm each Shareholder's:

- Name
- Current Share Quantity
- Share Value
- Total Valuation
- Status

Current approved base Share Value:

**1 Share Unit = NPR 1,000**

Status:

**APPROVED CURRENT RULE**

---

# 6. User Roles

The system shall contain exactly three primary roles:

1. Admin
2. Staff
3. Shareholder

Status:

**APPROVED**

---

# 7. Customer Balance Rule

Customer shall not maintain:

- Wallet Balance
- Receivable Balance
- Payable Balance
- Current Financial Balance

Customer is transaction identity/history only.

Status:

**APPROVED**

---

# 8. Remittance Negative Balance Rule

Approved Remittance Accounts may become negative.

Insufficient Remittance Account balance shall not automatically stop a valid transaction.

Status:

**APPROVED**

---

# 9. Remittance Transaction Rule

Where Customer gives Cash for Remittance:

Example:

Principal:

`NPR 10,000`

Service Charge:

`NPR 100`

Result:

- Cash `+10,100`
- Selected Remittance Account `-10,000`

Status:

**APPROVED**

---

# 10. Deposit Rule

Approved current rule:

Customer Deposit:

- Cash increases
- Selected BLB/Bank operational account decreases
- Customer balance does not exist

Example:

Principal:

`NPR 10,000`

Service Charge:

`NPR 100`

Result:

- Cash `+10,100`
- BLB `-10,000`

Status:

**APPROVED**

---

# 11. Withdrawal Rule

Approved base rule:

Customer Withdrawal:

- Cash decreases
- Selected BLB/Bank operational account increases
- Customer balance does not exist

Example before Service Charge:

Withdrawal:

`NPR 10,000`

Result:

- Cash `-10,000`
- BLB `+10,000`

Status:

**BASE RULE APPROVED**

---

# 12. Withdrawal Service Charge

### Decision 12.1

**Status:** BLOCKING

Confirm how Service Charge works during Withdrawal.

### Option A — Separate Charge

Customer withdraws:

`NPR 10,000`

Service Charge:

`NPR 100`

Customer receives:

`NPR 10,000`

and separately pays:

`NPR 100`

Possible Cash net effect:

`-9,900`

---

### Option B — Deduct from Withdrawal

Withdrawal:

`NPR 10,000`

Service Charge:

`NPR 100`

Customer receives:

`NPR 9,900`

Possible Cash effect:

`-9,900`

---

### Option C — Other Business Rule

Describe:

`________________________________________________`

Final Decision:

`________________________________________________`

---

# 13. Service Charge Receiving Account

Current approved normal rule:

Service Charge is received in:

**Cash**

Status:

**APPROVED CURRENT RULE**

---

### Decision 13.1

Can Service Charge ever be received directly into Bank or another account?

- [ ] No — Always Cash
- [ ] Yes — User selects receiving account
- [ ] Other

Decision:

`____________________________`

---

# 14. Provider Commission

Approved principle:

Provider Commission is different from Customer Service Charge.

Commission may be received from providers such as:

- Citizen Remit
- City Express
- IME Remit
- IME Pay
- Other providers

The actual receiving financial account must increase.

Status:

**APPROVED**

---

# 15. Commission Calculation

### Decision 15.1

**Status:** REQUIRED BEFORE AUTO-CALCULATION

Does the system calculate expected Commission automatically?

- [ ] No — Admin records actual received Commission only.
- [ ] Yes — Provider rate system required.
- [ ] Future Phase only.

Recommended initial rule:

**Actual Commission Receipt Entry only.**

Decision:

`____________________________`

---

# 16. Service Charge Calculation

### Decision 16.1

Is Service Charge:

- [ ] Manually entered for every transaction
- [ ] Fixed by Service
- [ ] Percentage-based
- [ ] Default value but editable
- [ ] Different rule per Provider

Decision:

`____________________________`

Until confirmed, system shall not invent automatic rates.

---

# 17. Account Transfer

Approved principle:

Account Transfer moves money between Company Accounts.

Example:

Bank → City Express

- Bank decreases
- City Express increases
- No Income
- No Expense

Status:

**APPROVED**

---

# 18. Month-End Settlement

Approved principle:

Negative provider/remittance account may be settled through Account Transfer.

Example:

City Express:

`NPR -80,000`

Bank → City Express:

`NPR 80,000`

Result:

`City Express = 0`

Status:

**APPROVED**

---

### Decision 18.1

Settlement shall be:

- [ ] Manual Admin action
- [ ] Automatically suggested but Admin confirms
- [ ] Automatically processed
- [ ] Other

Recommended Version 1:

**Manual Admin action**

Decision:

`____________________________`

---

# 19. Expense Entry Permission

### Decision 19.1

**Status:** BLOCKING FOR PERMISSION IMPLEMENTATION

Can Staff enter Expense?

- [ ] No — Admin only
- [ ] Staff can enter Pending Expense
- [ ] Staff can directly post Expense
- [ ] Selected Staff only

Current default:

**Admin only**

Final Decision:

`____________________________`

---

# 20. Expense Approval

If Staff Expense Entry is enabled:

- [ ] Admin approval required
- [ ] No approval required

Decision:

`____________________________`

---

# 21. Customer Editing by Staff

### Decision 21.1

Can Staff edit existing Customer details?

- [ ] No
- [ ] Name/Mobile/Address only
- [ ] Full permitted Customer profile
- [ ] Request Admin correction

Decision:

`____________________________`

---

# 22. Account Balance Visibility for Staff

### Decision 22.1

During transaction entry, should Staff see current Provider/Account balance?

- [ ] No balance shown
- [ ] Selected account balance only
- [ ] Selected account balance after transaction preview
- [ ] All operational balances

Recommended security rule:

**Selected account only, if operationally necessary.**

Decision:

`____________________________`

---

# 23. Staff Transaction History

### Decision 23.1

Staff may view:

- [ ] Own today's transactions only
- [ ] Own all transactions
- [ ] All Staff today's transactions
- [ ] Other permitted scope

Decision:

`____________________________`

---

# 24. Staff Cancellation

### Decision 24.1

When Staff makes a mistake:

- [ ] Staff cannot cancel; contacts Admin.
- [ ] Staff submits Cancellation Request.
- [ ] Staff can cancel only same-day own transactions.
- [ ] Other.

Recommended:

**Cancellation Request → Admin Final Approval**

Decision:

`____________________________`

---

# 25. Admin Cancellation

Approved principle:

Admin may perform authorized cancellation.

Cancellation must retain:

- Original transaction
- Reason
- Cancelled By
- Cancelled At
- Reversal effect
- Audit history

Status:

**APPROVED**

---

# 26. Posted Transaction Editing

Approved principle:

Posted financial values shall not be freely edited.

Correction should use:

**Cancel/Reversal → Corrected Transaction**

Status:

**APPROVED**

---

# 27. Backdated Transaction

### Decision 27.1

**Status:** BLOCKING

Admin may enter:

- [ ] Current date only
- [ ] Any date in open month
- [ ] Previous dates with mandatory reason
- [ ] Any unlocked period

Decision:

`____________________________`

---

### Decision 27.2

Staff may enter:

- [ ] Current date only
- [ ] Today and previous day
- [ ] Open month
- [ ] Other

Decision:

`____________________________`

---

# 28. Financial Period Closing

### Decision 28.1

Does Version 1 require formal month closing?

- [ ] No
- [ ] Yes

If Yes, once month is closed:

- [ ] No backdated entry
- [ ] Admin can reopen
- [ ] Reopen requires reason
- [ ] Other

Decision:

`____________________________`

---

# 29. Business Date / Calendar

### Decision 29.1

Primary operational date input:

- [ ] Nepali Date
- [ ] English Date
- [ ] Both

Decision:

`____________________________`

---

### Decision 29.2

Financial Year:

- [ ] Nepali Financial Year required
- [ ] Calendar Year sufficient
- [ ] Both required

Decision:

`____________________________`

---

# 30. Shareholder Transfer

Approved rule:

Shareholder A → Shareholder B transfer:

- Seller shares decrease
- Buyer shares increase
- Company Cash unchanged
- Company Bank unchanged

Private actual sale price between shareholders is not required.

System valuation uses applicable Share Value.

Status:

**APPROVED**

---

# 31. Shareholder Exit

Approved rule:

When Company redeems Shareholder shares:

- Share Quantity decreases
- Selected Company Cash/Bank decreases

Status:

**APPROVED**

---

# 32. Shareholder Full Transfer-Out Exit

Approved rule:

If Shareholder transfers all shares to another Shareholder:

- Shares become zero
- Company Cash/Bank unchanged
- Shareholder may become Exited

Status:

**APPROVED**

---

# 33. Company-Issued New Shares

### Decision 33.1

**Status:** BLOCKING ONLY IF REQUIRED IN VERSION 1

Does Version 1 allow the Company to issue new Share Units?

- [ ] No — Future Phase
- [ ] Yes

If Yes, detailed approval and capital rules must be added before coding.

Decision:

`____________________________`

---

# 34. Share Value Change

### Decision 34.1

Can current Share Value change from NPR 1,000?

- [ ] No — Permanently fixed at NPR 1,000
- [ ] Yes — Meeting/Admin-approved effective-date change
- [ ] Future Phase

Decision:

`____________________________`

Historical transaction values shall never be rewritten.

---

# 35. Share Transfer Approval

### Decision 35.1

Who approves Share Transfer?

- [ ] Admin
- [ ] Business Owner
- [ ] Partner meeting
- [ ] Multiple approvers
- [ ] Other

Decision:

`____________________________`

---

# 36. Share Redemption Approval

### Decision 36.1

Who approves Company Redemption/Exit?

- [ ] Admin
- [ ] Business Owner
- [ ] Partner meeting
- [ ] Multiple approvers
- [ ] Other

Decision:

`____________________________`

---

# 37. Profit Distribution

### Decision 37.1

Is Shareholder Profit Distribution required in Version 1?

- [ ] No
- [ ] Yes
- [ ] Future Phase

Recommended initial scope:

**Future Phase**

Decision:

`____________________________`

If Yes, separate detailed business rules must be written before coding.

---

# 38. Shareholder Additional Money

If a Shareholder deposits additional money into the Company, the system must know whether it is:

- New Share Capital
- Partner Loan
- Temporary Deposit
- Other Contribution

The system shall not automatically create shares.

Status:

**RULE PRINCIPLE APPROVED**

---

### Decision 38.1

Is Partner Loan/Temporary Deposit required in Version 1?

- [ ] No
- [ ] Yes

Decision:

`____________________________`

---

# 39. Shareholder Portal

Approved minimum Shareholder visibility:

- Own current Share Quantity
- Own applicable Share Value
- Own Total Valuation
- Own Share History
- Own Transfer History
- Own Exit/Redemption History

Status:

**APPROVED**

---

### Decision 39.1

Can Shareholder see Company Profit/Loss?

- [ ] No
- [ ] Summary only
- [ ] Full approved report

Current default:

**No**

Decision:

`____________________________`

---

### Decision 39.2

Can Shareholder download own Share Statement?

- [ ] Yes
- [ ] No
- [ ] Future Phase

Decision:

`____________________________`

---

# 40. Exited Shareholder Login

### Decision 40.1

After full exit:

- [ ] Login disabled immediately
- [ ] Read-only historical login retained
- [ ] Admin decides per Shareholder

Decision:

`____________________________`

---

# 41. Customer Mandatory Fields

### Decision 41.1

Minimum Customer fields:

- [ ] Name only
- [ ] Name + Mobile
- [ ] Name + Mobile + Address
- [ ] Additional fields

Final required fields:

`____________________________`

---

# 42. Transaction Mandatory Fields

Recommended mandatory fields for daily transaction:

- Business Date
- Customer
- Transaction Type
- Provider/Account
- Principal Amount
- Service Charge
- Created By

Reference may be:

- [ ] Mandatory
- [ ] Optional
- [ ] Mandatory only for selected services

Decision:

`____________________________`

---

# 43. Note Rule

Approved global rule:

Every major table/record shall support:

**Note**

Normal Note:

**Optional**

Cancellation Reason:

**Mandatory**

Status:

**APPROVED**

---

# 44. Attachment Rule

Approved global rule:

Every major operational/financial record shall support:

**Attachment / Upload**

Status:

**APPROVED**

---

### Decision 44.1 — File Types

Allowed formats:

- [ ] PDF
- [ ] JPG
- [ ] JPEG
- [ ] PNG
- [ ] DOC/DOCX
- [ ] Other

Decision:

`____________________________`

---

### Decision 44.2 — File Size

Maximum file size:

`________ MB per file`

---

### Decision 44.3 — Number of Files

- [ ] One attachment
- [ ] Multiple attachments

Recommended:

**Multiple attachments**

Decision:

`____________________________`

---

# 45. Transaction Receipt

### Decision 45.1

Does Version 1 require printable transaction receipt?

- [ ] Yes
- [ ] No
- [ ] Future Phase

Decision:

`____________________________`

---

# 46. Share Certificate

### Decision 46.1

Does Version 1 require Share Certificate printing?

- [ ] Yes
- [ ] No
- [ ] Future Phase

Decision:

`____________________________`

---

# 47. Financial Currency

Current intended currency:

**NPR — Nepalese Rupees**

### Decision 47.1

- [ ] NPR only
- [ ] Multi-currency required

Recommended initial scope:

**NPR only**

Decision:

`____________________________`

---

# 48. Monetary Decimal

### Decision 48.1

UI shall display:

- [ ] Whole NPR amounts
- [ ] Two decimal places where applicable

Database shall use safe fixed precision regardless.

Decision:

`____________________________`

---

# 49. Transaction Number

### Decision 49.1

Internal human-readable Transaction Number required?

- [ ] Yes
- [ ] No

Recommended:

**Yes**

Example:

`TRX-2026-000001`

Final format:

`____________________________`

---

# 50. Dashboard Final Scope

### Admin Dashboard

Confirm required cards:

- [ ] Cash
- [ ] Bank
- [ ] Remittance Balances
- [ ] Negative Accounts
- [ ] Today's Transactions
- [ ] Today's Service Charge
- [ ] Monthly Commission
- [ ] Monthly Expense
- [ ] Profit/Loss
- [ ] Staff Activity
- [ ] Shareholder Summary

Final selection:

`____________________________`

---

# 51. Staff Dashboard Final Scope

Confirm:

- [ ] New Remittance
- [ ] New Deposit
- [ ] New Withdrawal
- [ ] Customer Search
- [ ] Add Customer
- [ ] Today's Own Transactions
- [ ] Own Service Charge

Final selection:

`____________________________`

---

# 52. Shareholder Dashboard Final Scope

Confirm:

- [ ] Current Shares
- [ ] Share Value
- [ ] Total Valuation
- [ ] Share History
- [ ] Transfer History
- [ ] Redemption History

Final selection:

`____________________________`

---

# 53. Reports Required for Version 1

Confirm required reports:

- [ ] Daily Transactions
- [ ] Date Range Transactions
- [ ] Customer History
- [ ] Account Ledger
- [ ] Current Account Balance
- [ ] Negative Account Report
- [ ] Service Charge Report
- [ ] Staff Service Charge Report
- [ ] Commission Report
- [ ] Expense Report
- [ ] Profit/Loss
- [ ] Monthly Summary
- [ ] Shareholder List
- [ ] Share Ledger
- [ ] Share Transfer Report
- [ ] Share Redemption/Exit Report

---

# 54. Legacy Viewer

### Decision 54.1

Does the new system need to show old Access transactions?

- [ ] No — Access remains separate archive
- [ ] Yes — Read-only Legacy Viewer
- [ ] Future Phase

Recommended:

**Access remains separate archive initially**

Decision:

`____________________________`

---

# 55. Hosting

### Decision 55.1

Production hosting type:

- [ ] Shared Hosting
- [ ] VPS
- [ ] Other

Decision:

`____________________________`

---

# 56. Backup Frequency

### Decision 56.1

Database backup:

- [ ] Daily
- [ ] Twice Daily
- [ ] Other

Recommended minimum:

**Daily**

Decision:

`____________________________`

---

# 57. Off-Server Backup

At least one backup shall be outside the live server.

Destination:

`____________________________`

---

# 58. Go-Live Requirements

Before Go-Live all of the following must be completed:

- [ ] Legacy closing meeting completed
- [ ] Cut-off date approved
- [ ] Cash verified
- [ ] Bank balances verified
- [ ] BLB verified
- [ ] Remittance balances verified
- [ ] Negative balances verified
- [ ] Shareholder quantities verified
- [ ] Share Value confirmed
- [ ] Opening Balance document approved
- [ ] Customer data cleaned
- [ ] Customer import completed
- [ ] Admin users created
- [ ] Staff users created
- [ ] Shareholder users created
- [ ] Permissions tested
- [ ] Remittance tested
- [ ] Deposit tested
- [ ] Withdrawal tested
- [ ] Account Transfer tested
- [ ] Commission tested
- [ ] Expense tested
- [ ] Share Transfer tested
- [ ] Share Redemption tested
- [ ] Cancellation tested
- [ ] Backup tested
- [ ] Restore tested
- [ ] HTTPS active
- [ ] Production debug disabled

---

# 59. Version 1 Scope Freeze

After all BLOCKING decisions are confirmed:

**VERSION 1 SCOPE SHALL BE FROZEN BEFORE CODING.**

Any later requirement shall be classified as:

### Existing Scope Clarification

or

### New Change Request

A new request shall not silently enter Version 1.

---

# 60. AI / Developer Instruction

Before implementation, any AI coding agent or developer must:

1. Read `00_PROJECT_INDEX.md`
2. Read `01_REMITTANCE_BUSINESS_CONSTITUTION.md`
3. Read the relevant module standard.
4. Read this finalized Decision Checklist.
5. Identify unresolved items.
6. Stop if a required rule is unresolved.
7. Never invent financial behavior.

---

# 61. Blocking Decisions Summary

The following decisions have the highest priority:

1. Withdrawal Service Charge behavior.
2. Service Charge receiving account rule.
3. Staff Customer Edit permission.
4. Staff Expense permission.
5. Staff cancellation workflow.
6. Staff account balance visibility.
7. Backdated transaction rule.
8. Month closing requirement.
9. Nepali/English date rule.
10. Company-issued Share requirement.
11. Share Value change rule.
12. Share Transfer approval.
13. Share Redemption approval.
14. Profit Distribution Version 1 requirement.
15. Customer mandatory fields.
16. Attachment restrictions.
17. Legacy Viewer requirement.

These should be resolved before database/UI coding.

---

# 62. Approval Record

**Business Owner:** ____________________________

**Review Date:** _______________________________

**Version Approved:** __________________________

**Approved for Development:**  

- [ ] YES
- [ ] NO

**Business Owner Signature / Approval Reference:**

`______________________________________________`

---

# 63. Final Development Gate

Development may begin only when:

**BUSINESS CONSTITUTION APPROVED  
+ REQUIRED STANDARDS APPROVED  
+ BLOCKING DECISIONS RESOLVED  
+ VERSION 1 SCOPE FROZEN**

---

**END OF DOCUMENT**