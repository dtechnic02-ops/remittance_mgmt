# REMITTANCE MANAGEMENT SYSTEM — SOURCE CONTROL, RELEASE AND VERSIONING STANDARD

**Document ID:** 23  
**File Name:** `23_SOURCE_CONTROL_RELEASE_AND_VERSIONING_STANDARD.md`  
**Project:** Remittance Management System  
**Document Type:** Source Control, Release and Versioning Standard  
**Version:** 1.0  
**Status:** FINAL DRAFT — Pending Business Owner Approval  
**Authority:** Business Owner  

---

# 1. Purpose

This document defines the approved rules for source control, code versioning, release management, branch handling, commit discipline, deployment traceability and rollback preparation for the Remittance Management System.

It governs:

- Git repository usage
- Branch strategy
- Commit standards
- Release versions
- Production tags
- Database migration tracking
- Documentation versioning
- Change traceability
- Hotfixes
- Rollback
- Deployment records
- Source code backup
- Developer responsibility
- AI-assisted coding changes

The purpose is to ensure that every production change can be identified, reviewed, tested and recovered safely.

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
23. `22_NOTIFICATION_AND_ACTIVITY_STANDARD.md`

If this document conflicts with the Business Constitution, the Business Constitution shall prevail.

---

# 3. Core Source Control Principle

All application source code shall be maintained in version control.

The production server shall never be the only source of truth for application code.

Every meaningful production change shall be traceable to:

- A commit
- A release/version
- A deployment record

---

# 4. Repository Principle

The project shall use a dedicated source-code repository.

The repository should contain:

- Application source code
- Database migrations
- Tests
- Project documentation
- Configuration examples
- Deployment-related scripts where appropriate

It shall not contain production secrets.

---

# 5. Repository Ownership

Repository access shall remain under controlled project/business ownership.

At least one authorized person besides a single developer should be able to recover access to the repository.

---

# 6. Production Secrets Exclusion

The repository shall not contain:

- Production database password
- API secrets
- Private keys
- Real user passwords
- Sensitive tokens
- Backup credentials

Use approved environment configuration.

---

# 7. Environment Example

The repository may contain a safe example file such as:

`.env.example`

It shall include configuration keys without real production secret values.

---

# 8. Default Branch

The project shall maintain a primary stable branch.

Recommended name:

`main`

The production release shall normally originate from reviewed code on the stable branch.

---

# 9. Development Branching Principle

Development may use short-lived branches for controlled changes.

Examples:

- `feature/remittance-create`
- `feature/account-transfer`
- `fix/service-charge-report`
- `hotfix/duplicate-posting`

Exact naming may vary.

---

# 10. Branch Scope

One branch should preferably represent one clear task or related group of changes.

Avoid mixing unrelated changes such as:

- Shareholder logic
- UI redesign
- Expense report
- Database refactor

into one uncontrolled branch.

---

# 11. Small Project Simplicity

The project does not require an unnecessarily complex enterprise branching strategy.

A practical model may be:

**main  
+ short-lived feature/fix branches**

provided release controls are followed.

---

# 12. Direct Production Coding Prohibition

Developers shall not make undocumented code edits directly on the production server as the normal workflow.

Required normal flow:

**Local/Test Code  
→ Git Commit  
→ Test  
→ Release  
→ Production Deploy**

---

# 13. Emergency Production Edit

If an emergency direct production edit is unavoidable:

1. Record the reason.
2. Backup affected files/data.
3. Apply minimal fix.
4. Verify production.
5. Immediately reproduce the same fix in source control.
6. Commit and tag the corrected source.
7. Prevent production and repository from diverging.

---

# 14. Commit Principle

Each commit should represent a meaningful, understandable change.

Avoid commits containing large unrelated modifications.

---

# 15. Commit Message

Commit messages should describe what changed.

Preferred examples:

`Add remittance transaction validation`

`Fix duplicate account transfer posting`

`Add shareholder transfer authorization`

`Update opening balance documentation`

Avoid unclear messages such as:

`update`

`changes`

`fix`

for important financial work.

---

# 16. Commit Business Context

For important financial/business changes, commit messages may reference:

- Module
- Change Request
- Bug ID
- Business Rule

Example:

`Fix remittance cash posting for service charge`

---

# 17. Commit Before Major Experiment

Before a risky refactor, stable work should be committed so that the developer can return to a known state.

---

# 18. Uncommitted Production Deployment

Production should not normally be deployed from uncommitted local code.

The exact source version running in production must be identifiable.

---

# 19. Code Review

Important financial/security changes should be reviewed before production where practical.

Review shall consider:

- Business Rule
- Account effect
- Permissions
- Database impact
- Cancellation
- Audit
- Tests

---

# 20. AI-Generated Code Review

AI-generated code shall be treated like developer-generated code.

It shall not bypass:

- Review
- Testing
- Business Rule verification
- Version control

---

# 21. AI Commit Rule

AI shall not generate random large commits across unrelated modules.

Changes shall remain scoped to the approved task.

---

# 22. Versioning Principle

Production releases shall use a recognizable version.

Recommended format:

**Semantic Versioning**

`MAJOR.MINOR.PATCH`

Example:

`1.0.0`

---

# 23. Major Version

Increment MAJOR when there is a substantial or breaking change.

Example:

`1.0.0 → 2.0.0`

Possible reasons:

- Major architecture change
- Significant business-model change
- Breaking database/workflow changes

---

# 24. Minor Version

Increment MINOR for backward-compatible approved feature additions.

Example:

`1.0.0 → 1.1.0`

Possible examples:

- New report
- New approved module
- New optional workflow

---

# 25. Patch Version

Increment PATCH for backward-compatible bug fixes and small corrections.

Example:

`1.0.0 → 1.0.1`

Examples:

- Fix report calculation defect
- Fix validation issue
- Fix UI bug
- Fix permission bug

---

# 26. Pre-Release Version

Testing releases may use labels such as:

- `1.0.0-beta.1`
- `1.0.0-rc.1`

This is optional.

Official production Go-Live shall use a stable release identifier.

---

# 27. First Production Release

Recommended first official release:

`v1.0.0`

only after:

- Version 1 scope complete
- UAT passed
- Opening approved
- Security checked
- Backup verified

---

# 28. Git Tag

Each official production release should have a Git tag.

Example:

`v1.0.0`

This tag identifies the exact source code used for that release.

---

# 29. Tag Immutability Principle

An existing release tag shall not be silently moved to different code.

If a fix is required, create a new version.

Example:

Do not replace `v1.0.0`.

Create:

`v1.0.1`

---

# 30. Release Record

Each production release shall have a release record containing:

- Version
- Date
- Git Commit/Tag
- Summary
- Database migration status
- Documentation changes
- Known issues
- Deployed By
- Verification result

---

# 31. Release Notes

Release Notes should identify:

### Added
New approved features.

### Changed
Approved behavior/configuration changes.

### Fixed
Bug corrections.

### Security
Relevant security fixes.

### Known Issues
Approved unresolved minor issues.

---

# 32. Release Notes Financial Rule

If a release changes financial behavior, the release note shall state it clearly.

Example:

`Fixed Withdrawal Service Charge posting according to approved rule.`

---

# 33. Documentation Version Synchronization

Business documents that changed for a release should be committed with or before related code.

Code shall not become the only place where a new business rule exists.

---

# 34. Business Rule Change Release

If a release changes approved Business Rules:

1. Update document.
2. Approve rule.
3. Commit documentation.
4. Modify code.
5. Test.
6. Release.

---

# 35. Database Migration Versioning

All database schema changes shall exist in controlled migrations.

The repository shall reveal the schema changes required between releases.

---

# 36. Migration and Release Relationship

The release record should state whether deployment includes database migrations.

Example:

`v1.2.0 requires migrations 2026_xx_xx_xxxxxx...`

---

# 37. Destructive Migration Release

Any release containing destructive database migration requires:

- Backup
- Migration review
- Test
- Recovery plan
- Explicit deployment care

---

# 38. Migration Ordering

Production migrations shall be executed in the approved order.

Developers shall not manually skip required migrations without technical review.

---

# 39. Migration State Verification

After deployment verify that:

- Expected migrations completed
- Application starts
- Existing data remains available
- Financial records remain intact

---

# 40. Data Migration vs Schema Migration

The system shall distinguish:

### Schema Migration

Changes database structure.

### Business Data Migration

Transforms/imports business records.

Business Data Migration requires additional reconciliation where financial data is involved.

---

# 41. Release Branch

A separate long-lived release branch is not mandatory for the initial small project.

If future operational scale increases, release branching may be introduced.

---

# 42. Hotfix

A Hotfix is a production correction for an urgent defect.

Examples:

- Duplicate financial posting
- Security permission bypass
- Critical transaction failure

---

# 43. Hotfix Flow

Recommended flow:

1. Confirm defect.
2. Identify production version.
3. Create hotfix branch from production version/main.
4. Apply minimal fix.
5. Test.
6. Backup if financial/database impact exists.
7. Deploy.
8. Verify.
9. Merge fix into stable development history.
10. Release new Patch version.

---

# 44. Hotfix Version Example

Production:

`v1.2.0`

Critical fix:

`v1.2.1`

---

# 45. Hotfix Scope

A Critical hotfix shall remain narrowly focused.

Do not combine unrelated new features into an emergency financial fix.

---

# 46. Rollback Principle

Every high-risk release shall consider rollback before deployment.

Rollback means returning the application to a safe previous state without losing valid new financial data.

---

# 47. Code Rollback vs Database Rollback

These are different.

### Code Rollback

Return application source to previous release.

### Database Rollback

Reverse schema/data changes.

A code rollback may be safe while a database rollback may not be.

---

# 48. Financial Data Rollback Warning

Do not restore an old database backup over current production merely to undo a code bug if valid transactions occurred after the backup.

This may destroy live financial data.

---

# 49. Rollback Analysis

Before rollback determine:

- What changed?
- Did database schema change?
- Did production data change?
- Were new live transactions entered?
- Is previous code compatible with current schema?
- Is reconciliation required?

---

# 50. Bad Release Recovery

If a release causes serious failure:

1. Stop affected workflow if required.
2. Preserve live data.
3. Backup current state.
4. Identify release defect.
5. Decide Hotfix vs Code rollback.
6. Protect transactions entered since deployment.
7. Verify system after recovery.

---

# 51. Deployment Commit Identification

Production shall have a way to determine which commit/version is currently deployed.

This may be recorded through:

- Release file
- Environment setting
- Deployment record
- Application version display for Admin

---

# 52. Version Display

The application may display a small version identifier in an Admin/System Information page.

Example:

`Version 1.0.3`

This is optional but useful for support.

---

# 53. Staff Version Visibility

Staff do not require detailed Git information.

A simple application version may be sufficient for support.

---

# 54. Shareholder Version Visibility

Shareholders do not need source-control information.

---

# 55. Release Approval

Production release shall require appropriate approval depending on impact.

### Patch Bug Fix
Technical verification + required Business approval where financial behavior is involved.

### New Feature
Business Owner approval.

### Business Rule Change
Business Owner approval mandatory.

---

# 56. Pre-Release Checklist

Before creating a production release:

- [ ] Scope complete
- [ ] Relevant documents updated
- [ ] Code committed
- [ ] Tests passed
- [ ] Financial regression passed
- [ ] Permission regression passed
- [ ] Database migration reviewed
- [ ] Security reviewed
- [ ] Backup available
- [ ] Known issues documented

---

# 57. Release Tag Checklist

Before tagging:

- [ ] Correct commit selected
- [ ] Working tree clean
- [ ] Version number updated where used
- [ ] Documentation committed
- [ ] Migration files committed
- [ ] Required tests passed

---

# 58. Deployment Checklist

Before production deploy:

- [ ] Correct release/tag
- [ ] Production backup
- [ ] Environment configuration ready
- [ ] Maintenance window if required
- [ ] Database migration plan
- [ ] Rollback/recovery plan

---

# 59. Post-Deployment Checklist

After deployment:

- [ ] Application opens
- [ ] Admin Login works
- [ ] Staff Login works
- [ ] Shareholder Login works where active
- [ ] Core transaction works
- [ ] Account balances verify
- [ ] Reports open
- [ ] Attachments work
- [ ] No unexpected error
- [ ] Version recorded

---

# 60. Production Verification Transaction

For significant financial releases, perform a safe controlled verification of affected functionality.

Do not create unnecessary dummy financial records in live production.

Use an approved verification method.

---

# 61. Release Failure

If post-deployment checks fail:

The release shall not automatically be considered successful.

Investigate before continuing normal operation.

---

# 62. Documentation Commit Principle

The documentation set shall be version-controlled with source code.

This ensures future developers can identify which Business Rules applied to which release.

---

# 63. Documentation Version History

When major standards change, their internal Version may be updated.

Example:

`03_ACCOUNT_AND_TRANSACTION_STANDARD.md`

Version:

`1.0 → 1.1`

---

# 64. Document Change Log

Important documents may later include a Change History table.

Example:

| Version | Date | Change |
|---|---|---|
| 1.0 | Initial | Initial approved standard |
| 1.1 | ... | Updated Withdrawal rule |

This is optional initially but recommended for future rule changes.

---

# 65. No Document Deletion

Old business-rule history should not be casually deleted if it is required to understand historical transactions/releases.

Git history itself also preserves prior versions.

---

# 66. Release and Business Effective Date

A code release date and a Business Rule effective date may differ.

Example:

Code deployed:

`2026-09-01`

New Share Value effective:

`2026-09-10`

The system shall respect the approved effective date.

---

# 67. Feature Toggle

A future feature may be deployed but disabled until approved activation.

If used, feature toggles shall be controlled and documented.

---

# 68. Feature Toggle Financial Warning

Financial business logic shall not have ambiguous simultaneous old/new behavior due to poorly managed feature toggles.

Any financial toggle requires explicit effective behavior.

---

# 69. Development Snapshot

Before major refactoring, developers may create a tag/branch snapshot for recovery.

This is optional but useful for high-risk work.

---

# 70. Local Backup Is Not Source Control

Copying a folder such as:

`project_backup_final2`

is not a replacement for Git/version control.

---

# 71. Git Is Not Database Backup

Git stores source code.

It does not replace:

- Production database backup
- Attachment backup

Both systems are required.

---

# 72. Repository Backup

The remote Git repository provides source redundancy.

A local clone may provide an additional copy.

---

# 73. Repository Access Security

Repository access shall use secure credentials.

Access shall be removed when no longer required.

---

# 74. Public vs Private Repository

Because the application may contain business-specific implementation details, a private repository is recommended unless there is a deliberate reason for public release.

Secrets are prohibited even in a private repository.

---

# 75. `.gitignore`

The project shall exclude inappropriate generated/sensitive files.

Examples may include:

- `.env`
- Logs
- Local cache
- Vendor dependencies where framework practice excludes them
- Temporary files
- Sensitive local backup files

---

# 76. Attachment Files and Git

Production user uploads shall not be committed into normal source-control history.

They belong in application storage/backups.

---

# 77. Database Dumps and Git

Production database dumps containing customer/business data shall not be committed to Git.

---

# 78. Test Data in Git

Seed/demo data may be committed only if it contains safe dummy information.

---

# 79. Commit Review Before Push

Developers shall inspect changed files before committing/pushing.

This helps catch:

- Accidental secrets
- Debug files
- Unrelated changes
- Production exports

---

# 80. Large Unrelated Diff

If a small task unexpectedly creates a very large code diff, review before committing.

Possible causes:

- Formatter changed entire project
- Wrong file encoding
- Dependency lock rewrite
- AI modified unrelated files

---

# 81. Formatting Changes

Avoid mixing massive formatting-only changes with critical financial business-rule changes.

Separate them when practical.

---

# 82. Dependency Lock Files

Dependency changes shall include appropriate lock-file updates according to framework/tooling practice.

---

# 83. Dependency Upgrade Release

Major dependency/framework upgrades shall be treated as controlled releases.

They require regression testing.

---

# 84. Security Update

Urgent dependency security fixes may be Patch releases, but critical financial workflows must still be verified afterward.

---

# 85. Branch Deletion

Merged short-lived feature branches may be deleted from the remote after their history is safely preserved in main.

---

# 86. Force Push

Force-pushing shared stable production history should be avoided.

Rewriting release history may destroy traceability.

---

# 87. Protected Main Branch

Where repository tooling supports it, the stable branch may be protected against accidental destructive pushes.

The exact protection level may depend on team size.

---

# 88. Single Developer Environment

Even with only one primary developer, version-control discipline shall still be followed.

The lack of a large team does not remove the need for recoverable history.

---

# 89. AI Working Branch

For larger AI-assisted changes, using a separate task branch is recommended.

This makes review and rollback easier.

---

# 90. AI Patch Inspection

Before accepting AI-generated changes:

- Review diff
- Confirm expected files
- Verify no unrelated changes
- Run tests
- Confirm business rules

---

# 91. Release Automation

Future deployment automation may be introduced.

Possible flow:

**Push/Tag  
→ Tests  
→ Build  
→ Deploy**

Automation is optional and shall not bypass production approval.

---

# 92. CI Testing

Future Continuous Integration may run:

- Unit tests
- Feature tests
- Security/lint checks

before merge/release.

This is recommended as the project matures.

---

# 93. CI Failure

A failed critical test shall block production release.

---

# 94. Automated Deployment Caution

Automatic deployment on every commit to main is not recommended unless the release process is intentionally designed for it.

Financial systems benefit from controlled deployment.

---

# 95. Manual Approval Gate

A production deployment pipeline may require manual approval before final deployment.

This is recommended for major/financial changes.

---

# 96. Database Backup Gate

Deployment automation shall not bypass required database backup for high-risk migrations.

---

# 97. Release Artifact

If the deployment process creates build artifacts, they shall be tied to a source version/tag.

---

# 98. Support Case to Commit Traceability

Important bugs may link:

**Support/Defect ID  
→ Commit  
→ Release Version**

This makes maintenance easier.

---

# 99. Change Request Traceability

New features may link:

**Change Request  
→ Documentation Update  
→ Branch/Commit  
→ Test  
→ Release**

---

# 100. Production Issue Traceability

When a production issue occurs, support should be able to identify:

- Current Version
- Relevant Commit
- Recent Release
- Database migration
- Known issue

---

# 101. Release Frequency

The system does not require frequent releases for their own sake.

Prefer stable tested releases over constant unnecessary production changes.

---

# 102. Batch Changes

Several low-risk related improvements may be grouped into one planned Minor release.

High-risk financial fixes should not wait unnecessarily if urgent.

---

# 103. Production Freeze

During critical financial periods or cutover, the Business Owner may temporarily freeze non-essential deployments.

---

# 104. Go-Live Freeze

Immediately before first Go-Live:

- Version 1 scope should be frozen.
- Only critical approved fixes should be added.
- New feature development should wait until after stable Go-Live.

---

# 105. First Go-Live Tag

The exact production source used for initial Go-Live must be tagged and preserved.

Example:

`v1.0.0`

---

# 106. Post-Go-Live Patch

Any immediate post-Go-Live fixes shall receive new Patch versions.

Example:

`v1.0.1`

not silent replacement of `v1.0.0`.

---

# 107. Rollforward Preference

Where live data has changed, a controlled forward fix may be safer than destructive database rollback.

The technical decision shall be based on actual impact.

---

# 108. Release Retention

Historical source releases/tags shall remain available.

Do not delete old official production release tags without strong reason.

---

# 109. Final Source Control Principle

Every production state should answer:

**WHAT CODE IS RUNNING?**

**WHAT BUSINESS RULES APPLY?**

**WHAT DATABASE MIGRATIONS WERE APPLIED?**

**WHEN WAS IT DEPLOYED?**

---

# 110. Final Release Principle

A production release shall follow:

**APPROVED REQUIREMENT  
→ DOCUMENTATION  
→ CODE  
→ COMMIT  
→ TEST  
→ VERSION  
→ BACKUP  
→ DEPLOY  
→ VERIFY**

---

# 111. Developer Rule

Developers and AI coding agents shall not bypass Git/version control for normal production changes.

A change that exists only on one computer or server is not considered safely maintained.

---

# 112. Acceptance Criteria

Source control and release management are acceptable when:

- All source code is version-controlled.
- Production secrets are excluded.
- Production version is identifiable.
- Important changes have meaningful commits.
- Official releases have versions/tags.
- Database migrations are tracked.
- Documentation is version-controlled.
- Hotfixes receive new versions.
- Production changes are verified.
- Rollback/recovery is considered.
- Production server is not the only source copy.

---

# 113. Document Authority

This document is the authoritative Source Control, Release and Versioning Standard for the Remittance Management System.

It is subordinate to:

`01_REMITTANCE_BUSINESS_CONSTITUTION.md`

and shall guide all source management, releases, hotfixes and production deployment history.

---

# 114. Approval

**Status:** FINAL DRAFT — Pending Business Owner Approval**

After approval, this document becomes the official Source Control, Release and Versioning Standard for the Remittance Management System.

---

**END OF DOCUMENT**