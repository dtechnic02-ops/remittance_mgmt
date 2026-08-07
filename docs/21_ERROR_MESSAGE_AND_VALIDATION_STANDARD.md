# REMITTANCE MANAGEMENT SYSTEM — ERROR MESSAGE AND VALIDATION STANDARD

**Document ID:** 21  
**File Name:** `21_ERROR_MESSAGE_AND_VALIDATION_STANDARD.md`  
**Project:** Remittance Management System  
**Document Type:** Validation, Error Handling and User Feedback Standard  
**Version:** 1.0  
**Status:** FINAL DRAFT — Pending Business Owner Approval  
**Authority:** Business Owner  

---

# 1. Purpose

This document defines the approved validation, error-message, warning and user-feedback rules for the Remittance Management System.

It governs:

- Required-field validation
- Financial amount validation
- Customer validation
- Account validation
- Provider validation
- Shareholder validation
- Share quantity validation
- Date validation
- Duplicate transaction validation
- Attachment validation
- Permission errors
- Financial warning messages
- Cancellation validation
- Correction validation
- API/integration errors
- System errors
- User-facing success messages
- Logging of technical errors

The purpose of this standard is to ensure that invalid transactions are stopped safely and users receive clear, understandable feedback.

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
19. `18_USER_ACCEPTANCE_GO_LIVE_AND_HANDOVER_STANDARD.md`
20. `19_MASTER_TEST_CASE_CATALOG.md`
21. `20_API_AND_INTEGRATION_STANDARD.md`

If this document conflicts with the Business Constitution, the Business Constitution shall prevail.

---

# 3. Core Validation Principle

The system shall validate every business-critical request before posting data.

Validation shall exist at:

- UI level for usability
- Server level for authority
- Database level where appropriate for integrity

Client-side validation alone shall never be considered sufficient.

---

# 4. Error Message Principle

Error messages shall be:

- Clear
- Short
- Specific
- Actionable
- Non-technical for ordinary users

Poor example:

`Invalid request.`

Preferred example:

`Customer is required.`

or:

`Send Account and Receive Account cannot be the same.`

---

# 5. Technical Error Separation

Users shall see safe business-friendly messages.

Detailed technical errors may be logged securely for developers/Admin.

Production screens shall not expose:

- SQL queries
- Passwords
- Database credentials
- Stack traces
- API secrets
- Server paths

---

# 6. Validation Before Financial Posting

Financial Ledger updates shall only begin after all required business validation succeeds.

If validation fails:

- No transaction is posted.
- No Ledger entry is created.
- No Account Balance changes.
- No Service Charge income changes.
- No Share Quantity changes.

---

# 7. Required Field Validation

Every form shall clearly identify required fields.

Examples may include:

- Customer
- Provider
- Account
- Principal Amount
- Service Charge where required
- Business Date
- Expense Category
- Payment Account
- Shareholder
- Share Quantity

---

# 8. Customer Validation

Where Customer is required:

- Customer must exist.
- Customer must be valid/active according to approved rules.
- Unauthorized Customer IDs must be rejected.

Error example:

`Please select a valid customer.`

---

# 9. Customer Quick-Create Validation

When creating a new Customer:

- Name must meet minimum requirement.
- Mobile validation shall follow final mandatory-field rule.
- Obvious duplicate may trigger warning.

The system shall not invent missing personal information.

---

# 10. Customer Duplicate Warning

Possible warning:

`A customer with the same mobile number already exists. Please review before creating a new record.`

Duplicate warning does not automatically merge records.

---

# 11. Financial Amount Validation

Financial amounts shall be validated as fixed-precision numeric values.

Invalid examples:

- Text
- NaN
- Scientific notation where not supported
- Empty required amount
- Invalid negative input

---

# 12. Positive Amount Rule

User-facing business amount fields shall normally require:

`Amount > 0`

Examples:

- Principal
- Transfer Amount
- Commission
- Expense
- Share Redemption amount

unless an approved rule explicitly allows zero.

---

# 13. Service Charge Validation

Service Charge may be:

`0`

where permitted.

Service Charge shall not normally accept a negative value.

Error example:

`Service Charge cannot be negative.`

---

# 14. Negative Balance vs Negative Input

A Financial Account may legitimately become negative.

This does **not** mean Staff should type negative Principal Amount.

Transaction direction determines Account effect.

---

# 15. Remittance Validation

Before Remittance save, validate:

- Business Date
- Customer
- Provider/Remittance Account
- Principal Amount
- Service Charge
- User authorization
- Provider/Account status
- Required Reference where applicable

---

# 16. Remittance Negative Balance Warning

If transaction causes an allowed negative balance:

Warning example:

`City Express balance will become NPR -8,000 after this transaction.`

This is a warning, not an error, where negative balance is allowed.

---

# 17. Negative Balance Block

If selected Account does not allow negative balance:

Error example:

`This transaction would make the selected account negative. The transaction cannot be completed.`

---

# 18. Deposit Validation

Deposit shall validate:

- Customer
- BLB/Operational Account
- Principal Amount
- Service Charge
- Business Date
- Authorization

---

# 19. Withdrawal Validation

Withdrawal shall validate:

- Customer
- Operational Account
- Principal
- Service Charge according to final rule
- Business Date
- Authorization

The exact Service Charge financial behavior must not be implemented until approved.

---

# 20. Cash Availability Warning/Rule

If the business later requires preventing Cash from going below zero, that rule shall be separately approved.

The developer shall not assume this rule merely from generic accounting logic.

---

# 21. Account Transfer Validation

Before Account Transfer:

- Send Account exists.
- Receive Account exists.
- Both are Active.
- Send and Receive are different.
- Amount > 0.
- User is authorized.
- Business Date is valid.

---

# 22. Same Account Error

Error message:

`Send Account and Receive Account cannot be the same.`

---

# 23. Inactive Account Error

Error example:

`The selected account is inactive and cannot be used for new transactions.`

---

# 24. Provider Validation

Provider selection shall use Active Provider Master.

Invalid or inactive Provider ID shall be rejected server-side.

---

# 25. Expense Validation

Expense shall validate:

- Expense Category
- Amount
- Payment Account
- Business Date
- Authorization
- Required Note/Attachment where specifically required

---

# 26. Commission Validation

Commission shall validate:

- Provider
- Commission Amount
- Receiving Account
- Business Date
- Authorization

---

# 27. Opening Balance Validation

Opening Balance shall validate:

- Account
- Opening Amount
- Effective Date
- Authorized Admin
- Opening status/batch
- Approval information where required

---

# 28. Finalized Opening Validation

Attempt to edit finalized Opening through ordinary form:

Error:

`Opening Balance has been finalized and cannot be edited through this screen.`

---

# 29. Shareholder Validation

Shareholder transaction validation shall verify:

- Shareholder exists.
- Shareholder status allows transaction.
- Required share information is available.
- User is authorized.

---

# 30. Share Transfer Validation

Before transfer:

- From Shareholder valid.
- To Shareholder valid.
- Different shareholders.
- Quantity > 0.
- Seller has enough shares.
- Applicable Share Value exists.
- Business Date valid.

---

# 31. Same Shareholder Error

Error:

`From Shareholder and To Shareholder cannot be the same.`

---

# 32. Insufficient Share Error

Example:

Current Shares:

`100`

Requested Transfer:

`150`

Error:

`The shareholder does not have enough shares for this transfer.`

---

# 33. Negative Share Protection

No transaction shall make Current Share Quantity negative.

This is a Critical validation invariant.

---

# 34. Share Redemption Validation

Before Redemption:

- Shareholder valid.
- Quantity > 0.
- Quantity <= Current Holding.
- Share Value valid.
- Payment Account valid.
- Admin authorized.

---

# 35. Share Value Missing Error

Error:

`Approved Share Value is not configured. This transaction cannot be completed.`

The system shall not silently assume a hard-coded value.

---

# 36. Business Date Validation

Business Date shall be required for financial/share transactions.

The date shall be valid and within the role's allowed date range.

---

# 37. Future Date Validation

Future-dated financial transaction behavior shall follow approved rules.

Unless explicitly permitted, a future Business Date should be rejected.

Example:

`Future transaction dates are not allowed.`

---

# 38. Backdated Transaction Validation

If Staff/Admin backdating is limited:

Error example:

`You are not permitted to enter transactions for this date.`

---

# 39. Closed Period Validation

If period closing is implemented:

`This financial period is closed. New transactions cannot be posted to this date.`

---

# 40. Reference Number Validation

Reference may contain:

- Numbers
- Letters
- Hyphen
- Provider-specific characters

Do not force purely numeric storage unless required.

---

# 41. Duplicate Reference Validation

If duplicate Provider Reference is detected, the system may warn or block according to the provider/context.

Example warning:

`A transaction with this provider reference already exists. Please verify before continuing.`

---

# 42. Duplicate Transaction Detection

Possible duplicate indicators:

- Same Customer
- Same Provider
- Same Principal
- Same Reference
- Same Business Date
- Same user/session request

The system shall avoid both:

- Accidental duplicate posting
- False rejection of legitimate repeated transactions

---

# 43. Duplicate Submit Protection

After Save is submitted:

- Disable/lock repeated submit where practical.
- Use server-side duplicate/idempotency protection for financial actions.

---

# 44. Already Processed Error

If the same transaction request is submitted again:

`This transaction has already been processed.`

---

# 45. Cancellation Validation

Before cancellation:

- Transaction exists.
- Transaction is Active.
- User is authorized.
- Cancellation Reason provided.
- Transaction can be reversed safely.

---

# 46. Cancellation Reason

Cancellation Reason shall be mandatory.

Error:

`Cancellation reason is required.`

---

# 47. Double Cancellation

Attempt to cancel already Cancelled transaction:

`This transaction has already been cancelled.`

No second reversal shall occur.

---

# 48. Cancellation Dependency

If an external integration or dependent settlement prevents immediate cancellation, show specific status rather than silently cancelling.

---

# 49. Correction Validation

Correction shall preserve linkage to original transaction where applicable.

A replacement transaction shall not erase original history.

---

# 50. Posted Financial Edit Validation

Attempt to directly change Principal/Service Charge on posted transaction:

`Posted financial values cannot be edited directly. Use the approved correction process.`

---

# 51. Adjustment Validation

Adjustment shall require:

- Account
- Amount
- Direction
- Reason
- Authorized Admin
- Business Date
- Supporting evidence where required

---

# 52. Adjustment Warning

Before final Adjustment:

`This action will directly adjust the selected financial account through an auditable adjustment transaction.`

---

# 53. Permission Validation

Unauthorized actions shall return a clear Access Denied response.

Do not reveal whether inaccessible sensitive data exists.

---

# 54. Permission Error

Recommended message:

`You do not have permission to perform this action.`

---

# 55. Shareholder Ownership Error

If Shareholder requests another Shareholder's resource:

`You do not have permission to access this record.`

Do not disclose the other person's details.

---

# 56. Login Validation

Login shall validate credentials securely.

Error should not reveal whether username/email exists in a way that unnecessarily assists attackers.

Recommended generic message:

`The login details are incorrect.`

---

# 57. Inactive Login Error

Depending on security policy:

`This account is inactive. Please contact the administrator.`

---

# 58. Password Validation

Password changes shall validate approved password policy.

Existing password shall never be returned/displayed.

---

# 59. Attachment Validation

Attachment validation shall check:

- Allowed file type
- MIME type
- Maximum file size
- Number of files
- Authorization

---

# 60. Invalid File Type Error

`This file type is not allowed.`

---

# 61. File Too Large Error

`The selected file exceeds the maximum allowed size.`

---

# 62. Too Many Files Error

If file-count limit exists:

`You have selected more attachments than allowed.`

---

# 63. Attachment Upload Failure

If file storage fails:

`The transaction could not be completed because the attachment upload failed. Please try again.`

For workflows requiring atomic attachment save, transaction posting shall roll back accordingly.

---

# 64. Optional Attachment Failure

If Attachment is optional and architecture allows transaction to save without it, the exact behavior must be defined.

Do not silently lose a selected evidence file without notifying the user.

---

# 65. Note Validation

Optional Note may be blank.

If Note length exceeds technical limit:

`The note is too long.`

---

# 66. Cancellation Note vs General Note

General transaction Note may be optional.

Cancellation Reason is mandatory.

These shall not be treated as the same validation rule.

---

# 67. API Validation Errors

API responses should use predictable validation structures.

Example conceptual response:

```text
Validation failed:
- customer_id: Customer is required.
- amount: Amount must be greater than zero.