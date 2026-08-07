# REMITTANCE MANAGEMENT SYSTEM — DEVELOPER AND AI EXECUTION STANDARD

**Document ID:** 16  
**File Name:** `16_DEVELOPER_AND_AI_EXECUTION_STANDARD.md`  
**Project:** Remittance Management System  
**Document Type:** Developer and AI Coding Execution Standard  
**Version:** 1.0  
**Status:** FINAL DRAFT — Pending Business Owner Approval  
**Authority:** Business Owner  

---

# 1. Purpose

This document defines how human developers and AI coding agents shall work on the Remittance Management System.

Its purpose is to prevent:

- Uncontrolled code changes
- Business logic invention
- Unrelated file modification
- Financial rule duplication
- Database damage
- Permission bypass
- Inconsistent module architecture
- Large unnecessary project scans
- AI-generated assumptions
- Production-risk changes

This document governs the execution process used for development, debugging, refactoring and maintenance.

---

# 2. Governing Documents

Before development, this document shall be used together with:

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

The Business Constitution remains the highest business authority.

---

# 3. Core Execution Principle

Every development task shall follow:

**READ  
→ UNDERSTAND  
→ LIMIT SCOPE  
→ INSPECT RELEVANT FILES  
→ IDENTIFY IMPACT  
→ IMPLEMENT  
→ TEST  
→ REPORT**

No developer or AI coding agent shall skip directly from request to code when the task affects financial or shareholder logic.

---

# 4. Business Rule Authority

Developers and AI agents shall not invent missing business rules.

If required behavior is unclear:

**STOP**

Then:

1. Read the relevant documentation.
2. Identify the unresolved rule.
3. Ask the Business Owner.
4. Update the authoritative document.
5. Resume development only after confirmation.

---

# 5. Code Does Not Override Business Documentation

Existing code is not automatically correct.

If existing code conflicts with approved documentation:

- Do not silently preserve the wrong behavior.
- Report the conflict.
- Follow the approved Business Owner decision.
- Update code only after confirming impact.

---

# 6. Task Scope Rule

Every development request shall define a clear task scope.

Example:

`Create Remittance transaction create page.`

The developer shall not automatically modify:

- Shareholder
- Expense
- Commission
- Reports
- User roles

unless the requested feature genuinely requires those changes.

---

# 7. Relevant File Reading Rule

Before coding, inspect only the files relevant to the task.

Possible relevant files include:

- Route
- Controller
- Service
- Model
- Migration
- Validation Request
- Policy
- View
- Relevant JavaScript
- Relevant CSS
- Relevant Test
- Relevant documentation

Do not scan the entire project without a clear reason.

---

# 8. Documentation Reading Rule

For every task, read:

### Always
- `01_REMITTANCE_BUSINESS_CONSTITUTION.md`
- Relevant module standard
- `06_USER_ROLE_PERMISSION_STANDARD.md`
- `15_SYSTEM_ARCHITECTURE_AND_MODULE_STANDARD.md`

### When Financial
Also read:
- `03_ACCOUNT_AND_TRANSACTION_STANDARD.md`
- `07_DATABASE_AND_DATA_STANDARD.md`

### When Income/Expense
Also read:
- `04_COMMISSION_EXPENSE_AND_PROFIT_STANDARD.md`

### When Shareholder
Also read:
- `05_SHAREHOLDER_STANDARD.md`

### When UI
Also read:
- `08_UI_AND_WORKFLOW_STANDARD.md`

### When Deployment/Security
Also read:
- `09_SECURITY_AUDIT_BACKUP_DEPLOYMENT_STANDARD.md`

---

# 9. AI Context Rule

AI coding agents shall receive only enough project context to perform the current task safely.

Avoid unnecessarily loading the entire codebase into the AI context.

This reduces:

- Confusion
- Token waste
- Rule drift
- Unrelated modifications

---

# 10. No Project-Wide Rewrite

AI shall not rewrite large sections of the project merely because it can produce cleaner code.

Existing stable modules shall remain untouched unless:

- Required by the task
- Required by an approved architecture change
- Required to fix a verified defect

---

# 11. Financial Balance Rule

No developer shall directly change Current Account Balance from ordinary business code outside the approved Financial Engine.

Forbidden pattern conceptually:

```text
account.balance = request.amount