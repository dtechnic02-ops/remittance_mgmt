# REMITTANCE MANAGEMENT SYSTEM — NOTIFICATION AND ACTIVITY STANDARD

**Document ID:** 22  
**File Name:** `22_NOTIFICATION_AND_ACTIVITY_STANDARD.md`  
**Project:** Remittance Management System  
**Document Type:** Notification, Activity and User Event Standard  
**Version:** 1.0  
**Status:** FINAL DRAFT — Pending Business Owner Approval  
**Authority:** Business Owner  

---

# 1. Purpose

This document defines the approved notification and user-activity rules for the Remittance Management System.

It governs:

- In-app notifications
- User activity history
- Important financial alerts
- Cancellation alerts
- Security alerts
- Shareholder-related notifications
- Staff activity visibility
- Admin notifications
- Optional Email/SMS/WhatsApp notifications
- Notification read/unread state
- Notification privacy
- Notification retention
- Notification failures

The purpose is to ensure that notifications support important business control without becoming noisy, insecure or financially authoritative.

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
22. `21_ERROR_MESSAGE_AND_VALIDATION_STANDARD.md`

If this document conflicts with the Business Constitution, the Business Constitution shall prevail.

---

# 3. Core Notification Principle

A notification informs a user about an event.

A notification shall not itself create or alter financial data.

The authoritative source remains:

**Business Transaction → Ledger / Share Ledger → Report**

A notification is only a secondary representation of that event.

---

# 4. Notification Categories

Notifications may be classified as:

1. Operational
2. Financial
3. Security
4. Shareholder
5. System
6. Support
7. Future External Notification

---

# 5. Operational Notification

Operational notifications may include:

- Transaction completed
- Transaction cancelled
- Cancellation request
- Failed transaction
- Required review

These shall support daily workflow.

---

# 6. Financial Notification

Admin may receive notifications for important financial conditions such as:

- Provider Account becomes negative
- Large Adjustment posted
- Opening Balance finalized
- Month-End Settlement completed
- Commission recorded
- Unusual financial failure

Exact thresholds require approval.

---

# 7. Negative Balance Notification

Negative balance is allowed for approved Accounts.

Therefore the system shall not treat every negative balance as an error.

A notification may state:

`City Express balance is now NPR -50,000.`

This is informational unless a separate alert threshold is configured.

---

# 8. Negative Balance Alert Threshold

If future threshold-based alerts are required, they may use:

- Account
- Minimum threshold
- Notification recipient

Example:

`Notify Admin if City Express balance becomes less than NPR -100,000.`

This is optional configuration.

---

# 9. Service Charge Control Notification

Because Service Charge accountability is important, Admin may receive or review activity related to:

- Daily Service Charge total
- Staff Service Charge summary
- Cancelled Service Charge transactions
- Missing/abnormal Service Charge if future rules support detection

The system shall not invent what amount should have been charged unless an approved rate rule exists.

---

# 10. Staff Activity Principle

Every Staff financial transaction shall already preserve:

- Created By
- Created At
- Business Date
- Transaction Type

Activity screens/notifications shall use this data.

---

# 11. Activity vs Audit

Activity History and Audit Log are related but different.

### Activity

User-friendly record of relevant events.

### Audit

Controlled system/security evidence.

Activity may be simplified for UI.

Audit remains authoritative for sensitive actions.

---

# 12. User Activity Examples

User Activity may include:

- Login
- Logout where recorded
- Customer created
- Remittance created
- Deposit created
- Withdrawal created
- Cancellation requested
- Transaction cancelled
- Expense created
- Commission created
- Share Transfer processed
- Share Redemption processed

---

# 13. Admin Activity Visibility

Admin may view authorized activity across the system.

This may include:

- Staff transaction activity
- Cancellation activity
- User account activity
- Shareholder-management activity
- Important configuration changes

---

# 14. Staff Activity Visibility

Staff may view only permitted activity.

Recommended initial scope:

- Own recent transactions
- Own cancellation requests
- Own operational notices

Staff shall not automatically see:

- Admin security events
- Other Staff sensitive activity
- Shareholder administration

---

# 15. Shareholder Activity Visibility

Shareholder may view activity related to own Share records.

Examples:

- Share transfer received
- Share transfer sent
- Share redemption
- Shareholding change

Shareholder shall not receive another Shareholder's private activity.

---

# 16. In-App Notification

The system may provide an in-app notification center.

A notification may include:

- Title
- Short message
- Date/Time
- Related Record
- Read/Unread
- Recipient User
- Notification Type

---

# 17. Notification Privacy

A notification shall not contain data the recipient is not authorized to access.

Example:

Staff notification shall not reveal full Shareholder financial information.

---

# 18. Related Record Access

A notification may link to a related record.

Before opening the record, normal role authorization shall still apply.

Notification links shall not bypass permissions.

---

# 19. Read/Unread State

In-app notifications may support:

- Unread
- Read

Changing read status shall not affect the related business transaction.

---

# 20. Notification Deletion

Removing a notification from a user's notification list shall not delete:

- Financial transaction
- Audit Log
- Share transaction
- Activity history where separately retained

---

# 21. Notification Retention

Routine notifications may have shorter retention than financial Audit Logs.

Exact retention may be finalized based on operational need.

Critical events should remain available through Audit/transaction history even if notification is later removed.

---

# 22. Transaction Success Notification

After a successful transaction, the user should already receive a UI success confirmation.

An additional persistent notification is optional.

The system should avoid duplicating every ordinary transaction into unnecessary notification noise.

---

# 23. High-Value Transaction Notification

If the business later requires alerts for transactions above a threshold, the threshold must be explicitly configured.

Example:

`Notify Admin for transaction principal >= NPR 500,000.`

No arbitrary threshold shall be hard-coded without approval.

---

# 24. Cancellation Request Notification

If Staff cancellation-request workflow is implemented:

### Staff

Submits request.

### Admin

Receives notification:

`Cancellation request received for Transaction TRX-XXXX.`

---

# 25. Cancellation Approval Notification

After Admin approves/rejects a Staff cancellation request, Staff may receive:

- Approved
- Rejected

notification.

---

# 26. Cancellation Notification Data

A cancellation notification may include:

- Transaction Number
- Transaction Type
- Reason
- Status
- Reviewed By

Sensitive information shall be limited according to recipient role.

---

# 27. Direct Admin Cancellation

If Admin directly cancels a transaction, an activity/audit record is mandatory.

A separate notification to the same Admin is not necessarily required.

---

# 28. Adjustment Alert

Financial Adjustment is high risk.

The system should record visible Admin activity for:

- Account
- Amount
- Direction
- Reason
- User

A notification to another authority is only required if multi-approval is later implemented.

---

# 29. Opening Finalization Notification

When Opening Balance is finalized, Admin may receive confirmation such as:

`Opening Balance Batch has been finalized successfully.`

The event shall also remain in Audit history.

---

# 30. Share Transfer Notification

If Shareholder login is active, an approved Share Transfer may notify:

### Seller

`100 shares were transferred from your holding.`

### Buyer

`100 shares were added to your holding.`

The exact content shall follow privacy rules.

---

# 31. Share Redemption Notification

Shareholder may receive an approved message such as:

`Your shareholding was reduced by 200 shares through approved redemption.`

If payment detail is shown, it must match authorized records.

---

# 32. Shareholder Exit Notification

Where appropriate, Shareholder may be informed that:

- Full exit has been completed.
- Current Share Quantity is zero.
- Login status may change according to approved rule.

---

# 33. User Account Notification

User may receive system notice for:

- Account activated
- Account deactivated
- Password reset initiated
- Password changed

Security-sensitive content shall remain minimal.

---

# 34. Password Notification

The system shall never send an existing password.

A reset notification may include:

- Secure reset link
- Temporary process instruction

according to approved authentication architecture.

---

# 35. Login Security Alert

Future security features may notify Admin/user about:

- Suspicious login
- Repeated failed login
- Password change
- New device/login context

These are optional unless specifically required.

---

# 36. Email Notification

Email may be used for:

- Password reset
- Shareholder statements
- Important system notifications
- Administrative reports

Email is optional unless explicitly included in Version 1.

---

# 37. SMS Notification

SMS may be used for:

- Transaction confirmation
- Security alert
- Shareholder event
- Other approved notification

SMS integration requires `20_API_AND_INTEGRATION_STANDARD.md`.

---

# 38. WhatsApp Notification

WhatsApp notifications are future integration scope unless separately approved.

---

# 39. External Notification Failure

If Email/SMS/WhatsApp fails after a valid financial transaction:

- Financial transaction remains valid.
- Ledger remains unchanged.
- Notification failure is logged/retried if configured.

Notification failure shall not automatically reverse money movement.

---

# 40. Notification Queue

External notifications may use background queues.

This separates communication reliability from financial posting.

---

# 41. Queue Retry

Notification retry is safer than financial transaction retry because it does not create financial movement.

However, duplicate messages should still be controlled where practical.

---

# 42. Notification Status

External notification records may use statuses such as:

- Pending
- Sent
- Failed
- Retrying

These statuses describe communication only.

---

# 43. Notification Failure Visibility

Admin may have access to failed notification records if external communication becomes important.

---

# 44. Customer Notification

Customer-facing notifications are not automatically included in Version 1.

If introduced later, define:

- What data may be sent
- Customer consent/contact requirement
- Message type
- Privacy
- Cost
- Retry

---

# 45. Customer Transaction SMS

If future transaction SMS is approved, it may contain:

- Transaction reference
- Transaction type
- Principal
- Service Charge
- Provider
- Business name

Only approved information shall be sent.

---

# 46. Sensitive Customer Data

Notifications shall not include unnecessary:

- Identification documents
- Full confidential records
- Internal Account balances
- Shareholder data

---

# 47. Daily Admin Summary

A future Admin Daily Summary may include:

- Transaction Count
- Service Charge
- Negative Accounts
- Commission
- Expense
- Cancellations

This may be shown in-app or sent externally if approved.

---

# 48. Daily Summary vs Report

Daily notification summary is informational.

The authoritative figures remain in system Reports.

If notification and Report disagree:

**Report/source transactions must be investigated.**

---

# 49. Monthly Admin Summary

A future monthly summary may include:

- Service Charge
- Provider Commission
- Expense
- Profit/Loss
- Negative Accounts

Month-end summary shall not replace detailed reconciliation.

---

# 50. Backup Notification

Technical/Admin notification may be generated for:

- Backup success
- Backup failure

Backup failure notification is useful because failed backups may otherwise go unnoticed.

---

# 51. Critical System Notification

Important technical notifications may include:

- Database unavailable
- Backup failure
- Disk space critical
- External provider authentication failure

These are operational alerts, not business transactions.

---

# 52. Noise Control Principle

The system shall avoid generating unnecessary notifications for every minor event.

Too many alerts reduce the value of important alerts.

Notifications should focus on events that require:

- Awareness
- Review
- Decision
- Action

---

# 53. Priority Levels

Notifications may use priority such as:

- Normal
- Important
- Critical

Exact UI treatment may be decided during implementation.

---

# 54. Critical Notification Examples

Potential Critical alerts include:

- Financial posting inconsistency
- Backup failure over repeated period
- Unauthorized access attempt where detected
- Provider transaction requiring reconciliation

---

# 55. Notification Recipients

Recipients shall be explicitly determined by event.

Examples:

### Staff cancellation request
Recipient: Admin

### Share transfer
Recipients: Relevant Shareholders where portal notifications enabled

### Backup failure
Recipient: Admin/Technical responsible user

---

# 56. No Global Broadcast by Default

Financial or shareholder notices shall not be broadcast to every user.

Only relevant authorized recipients shall receive them.

---

# 57. Notification Preferences

Future system may allow safe user preferences such as:

- Email on/off
- SMS on/off
- In-app only

Business-critical mandatory alerts may override optional preference if approved.

---

# 58. Staff Notification Preferences

Staff shall not disable required operational notices if those notices are mandatory for workflow.

---

# 59. Shareholder Notification Preferences

Shareholder may potentially choose whether to receive optional external notifications.

This is future scope.

---

# 60. Notification Template

Reusable templates may be maintained for approved events.

Templates should use placeholders such as:

- Transaction Number
- Amount
- Provider
- Date

Templates shall not contain financial calculation logic.

---

# 61. Template Change

Changing notification wording shall not change the underlying business transaction.

---

# 62. Template Security

Templates shall not allow unsafe arbitrary code execution.

---

# 63. Activity Timeline

Important record detail pages may show a timeline.

Example Transaction Timeline:

- Created
- Cancellation Requested
- Cancelled
- Corrected

---

# 64. Shareholder Timeline

Shareholder detail may show:

- Opening Shares
- Share Received
- Share Transferred
- Redemption
- Exit

This should derive from Share Ledger/transactions.

---

# 65. User Activity Timeline

Admin may view a Staff user's permitted activity timeline for audit/support.

---

# 66. Activity Search

Admin activity screens may filter by:

- User
- Date
- Event Type
- Module
- Related Transaction

---

# 67. Activity Pagination

Activity history shall use pagination/efficient query patterns as data grows.

---

# 68. Activity Storage Principle

Activity logs should store references and key event data rather than duplicating complete financial records unnecessarily.

---

# 69. Audit Authority

If Activity and Audit Log disagree, the underlying transaction and protected Audit data shall be reviewed.

Activity UI is not an alternative Ledger.

---

# 70. Notification Creation Atomicity

A non-critical notification should not cause a valid financial transaction to fail merely because notification creation failed, unless the notification is explicitly part of the required business transaction.

Core financial integrity comes first.

---

# 71. Required Internal Workflow Notice

An exception may exist where a notification itself represents a required workflow object, such as:

`Cancellation Request`

In that case, the request record must be reliably created.

The visible notification may still be derived from it.

---

# 72. Cancellation Request as Business Record

If cancellation approval workflow is implemented, the request should be stored as a real workflow record, not only a disappearing notification.

---

# 73. Notification Duplicate Control

One event should not create many identical notifications due to retries.

Where practical, notifications should link to a unique event/reference.

---

# 74. External Notification Idempotency

External communication jobs should avoid sending the same message repeatedly due to job retry unless retry policy explicitly requires it.

---

# 75. Notification Localization

If Nepali and English interfaces are supported, notification templates may support both languages.

The financial values/reference must remain identical.

---

# 76. Currency Formatting

Financial notification amounts shall follow approved currency format.

Example:

`NPR 10,000`

---

# 77. Business Date vs Sent Time

Notification may show:

- Business Date
- Notification/Sent Time

These concepts shall not be confused.

---

# 78. Historical Notification

Notification history may be used for convenience but shall not replace transaction history.

---

# 79. Deleted User Notification Records

If a user is deactivated, historical notifications/activity may remain linked.

User deactivation shall not destroy Audit history.

---

# 80. Exited Shareholder Notification Records

Exited Shareholder historical Share activity remains preserved.

Notification access after exit depends on approved login policy.

---

# 81. Notification Security Testing

Test:

- Staff receives only allowed notices.
- Shareholder sees only own Share notices.
- Notification link cannot open unauthorized record.
- Sensitive information is not leaked.
- Deactivated user cannot access notifications.

---

# 82. Activity Testing

Test that:

- Created By matches actual user.
- Cancellation activity is recorded.
- Share Transfer activity matches Share Ledger.
- User deactivation preserves history.

---

# 83. External Notification Testing

If external notifications are implemented, test:

- Successful send
- Failed send
- Retry
- Duplicate prevention
- Invalid contact
- Provider outage
- Financial transaction remains correct

---

# 84. Notification Failure Test

Example:

Financial Remittance succeeds.

SMS provider fails.

### Expected

- Remittance remains Active.
- Ledger remains correct.
- SMS status = Failed/Retrying.
- No duplicate financial transaction.

---

# 85. Security Alert Testing

If failed-login alerts are implemented, ensure alerts do not reveal user passwords or sensitive credentials.

---

# 86. Backup Alert Testing

Force a test backup failure in safe environment.

Expected:

Admin/technical alert generated according to configured mechanism.

---

# 87. Notification UI Acceptance

Notification interface is acceptable when:

- Unread count is correct.
- Read/unread works.
- Relevant link works.
- Unauthorized links are blocked.
- Messages are understandable.
- High-priority events are distinguishable.
- Ordinary workflow is not overwhelmed by noise.

---

# 88. Activity UI Acceptance

Activity interface is acceptable when Admin can answer:

- Who did what?
- When?
- Which record?
- What status/result?

without treating Activity as a substitute for detailed Audit.

---

# 89. Version 1 Notification Scope

Recommended minimal Version 1 notification scope:

- In-app cancellation request/response if that workflow is enabled
- Important success/error UI messages
- Admin activity visibility
- Audit history
- Optional Shareholder own Share activity display

Email/SMS/WhatsApp may remain future scope unless explicitly required.

---

# 90. Future Notification Scope

Possible future additions:

- Customer SMS
- Shareholder Email
- Daily Admin Summary
- Backup Failure Alert
- Provider Failure Alert
- High-Value Transaction Alert
- Push Notification

Each should be enabled only when business benefit justifies complexity/cost.

---

# 91. Cost Awareness

External SMS/WhatsApp/Email providers may create recurring costs.

The system shall not enable high-volume paid notifications without Business Owner awareness and approval.

---

# 92. Communication Provider Change

Switching SMS/Email provider should not change core transaction logic.

Communication integration should remain separated from Financial Engine.

---

# 93. Developer Rule

Developers shall not place financial posting logic inside notification jobs/listeners.

A notification service may read transaction results but shall not become the source of Account Balance changes.

---

# 94. AI Rule

AI coding agents shall not automatically add notifications to every event.

Notification requirements must come from this standard or explicit Business Owner request.

---

# 95. Notification Data Minimization

Store/send only information needed for the notification purpose.

Avoid duplicating full Customer or Shareholder profiles inside notification records.

---

# 96. Notification Export

Notification export is not required in Version 1 unless specifically approved.

Important evidence belongs in Audit/transaction reports.

---

# 97. Activity Export

Admin Activity/Audit exports may be introduced if support/compliance needs justify it.

---

# 98. Notification Master Settings

Future settings may include:

- In-app enabled
- Email enabled
- SMS enabled
- Alert thresholds

High-risk/security alerts may remain mandatory.

---

# 99. Notification Settings Permission

Only Admin may manage system-wide notification settings.

Staff/Shareholder may manage personal optional preferences only if that feature is introduced.

---

# 100. Final Notification Principle

Notifications shall follow:

**REAL APPROVED EVENT  
→ AUTHORIZED RECIPIENT  
→ MINIMUM REQUIRED INFORMATION  
→ OPTIONAL ACTION/LINK**

They shall never follow:

**NOTIFICATION  
→ INVENTED FINANCIAL EVENT**

---

# 101. Final Activity Principle

Activity tracking shall help answer:

**WHO DID WHAT AND WHEN?**

while the Financial Ledger answers:

**HOW DID MONEY MOVE?**

and the Share Ledger answers:

**HOW DID OWNERSHIP MOVE?**

These responsibilities shall remain separate.

---

# 102. Document Authority

This document is the authoritative Notification and Activity Standard for the Remittance Management System.

It does not require paid external notification integrations in Version 1.

It is subordinate to:

`01_REMITTANCE_BUSINESS_CONSTITUTION.md`

---

# 103. Approval

**Status:** FINAL DRAFT — Pending Business Owner Approval

After approval, this document becomes the official standard for notifications, activity history, workflow notices and future external communication features.

---

**END OF DOCUMENT**