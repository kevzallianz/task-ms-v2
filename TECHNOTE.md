# Task Management System v2 — Project Documentation

**System Title:** Task Management System v2
**Version:** 2.0
**Developer:** Mark Kevin Romero — Full Stack Developer
**Status:** Development

---

## 1. Project Summary

Task Management System v2 (Task MS v2) is a centralized web application designed to streamline task and project execution across organized campaigns. The platform transitions teams from fragmented, manual tracking methods to a structured, role-driven system with real-time status visibility, activity logging, and collaborative contributor management.

### 1.1 Project Objectives

- **Centralize Task Execution:** Provide a single platform for managing campaign tasks, project tasks, and contributor assignments rather than relying on separate documents or spreadsheets.
- **Enforce Role-Based Access:** Ensure that only authorized users (viewer, contributor, manager, superadmin, admin) can perform sensitive operations such as creating campaigns or modifying task statuses.
- **Improve Traceability:** Log every significant action (project created, task updated, contributor added) in an auditable activity history per project and campaign.
- **Accelerate Onboarding:** Offer a CSV import workflow so campaign tasks can be bulk-loaded from external sources instead of being created one by one.
- **Provide Clear Ownership:** Assign specific contributors and task members so accountability is visible for every deliverable.

### 1.2 Business Value

- **Operational Clarity:** Teams gain a consolidated view of all active campaigns, projects, and tasks, eliminating the need to cross-reference multiple files.
- **Reduced Administrative Overhead:** Bulk CSV import, inline status updates, and automated email notifications reduce repetitive manual work.
- **Audit Readiness:** Project activity logs capture who did what and when, supporting performance reviews and retrospective analysis.
- **Scalability:** The campaign-centric model allows the system to grow with the organization — new campaigns, projects, and members can be added without restructuring the platform.

### 1.3 Target Users

| Role | Description |
|---|---|
| User | Standard authenticated member; can view and manage tasks and projects within their assigned campaign. |
| Admin | Oversees campaign and project portfolios; has read/supervisory access across campaigns. |
| Super Admin | Full platform authority; manages campaigns, users, roles, and bulk campaign assignments. |

---

## 2. Tech Stack

| Layer | Technology | Version |
|---|---|---|
| Frontend | Blade Templates / Vite | 7.x |
| Styling | Tailwind CSS | 4.x |
| Backend | Laravel | 12.x |
| Runtime | PHP | ^8.2 |
| Database | MySQL | 8+ |
| Testing | Pest / PHPUnit | — |
| Package Manager | Composer / npm | 2+ / 18+ |
| Hosting | Local / Docker (self-hosted) | — |
| CI/CD | Not Applicable | — |

---

## 3. Features

### 3.1 Authentication & Access Control

**Description:** A complete session-based authentication system covering registration, login, logout, and a full forgot-password / reset-password flow. Role-based middleware (`role:superadmin`, `role:admin`) guards all elevated routes, ensuring unauthorized users cannot access administrative panels. Access levels within campaigns (viewer, contributor, manager) add a second layer of granular permission enforcement at the controller level.

**User Impact:**
- **Security:** Users can only see and act on data relevant to their role and campaign membership.
- **Self-Service:** The password reset email flow allows users to recover accounts without administrator intervention.
- **Auditability:** Every authenticated session is tied to a specific user record, making activity logs reliable.

**Status:** Completed

---

### 3.2 Campaign Management

**Description:** The core organizational unit of the platform. Super Admins can create and administer campaigns; users are assigned to campaigns with a specific access level. Within a campaign, members can manage campaign-level tasks (with optional CSV bulk import) and campaign projects. Campaign task remarks and member assignments are fully supported. Campaign project activity is tracked through a dedicated activity log model (`CampaignProjectActivity`).

**User Impact:**
- **Structured Collaboration:** All work is scoped to a campaign, preventing cross-campaign data leakage.
- **Flexible Membership:** Access levels can be updated post-enrollment to reflect changing responsibilities.
- **Bulk Import:** The CSV import endpoint (`POST /campaigns/{campaign}/import`) allows teams to seed task lists from external planning tools in seconds.

**Status:** Completed

---

### 3.3 Project Management

**Description:** Users can create, update, and delete projects tied to a campaign. Each project supports a configurable status (`planning`, `in_progress`, `completed`, `on_hold`), a date range (start and target), contributors from multiple campaigns, and a full activity log. Project tasks are managed inline with their own status lifecycle and remark threads. Automated email notifications are dispatched when a contributor is added to a project (`ProjectContributorAdded` mail).

**User Impact:**
- **Full Lifecycle Control:** Teams can track a project from planning through completion with status transitions logged at every step.
- **Cross-Campaign Contribution:** A project can draw contributors from any campaign, enabling inter-team collaboration.
- **Transparency:** The activity log gives every stakeholder a timestamped record of changes without needing to ask for updates.

**Status:** Completed

---

### 3.4 Task Management

**Description:** Both campaign-level tasks and project-level tasks are fully supported. Tasks can be created, updated, deleted, and have their status changed. Task members can be assigned (campaign task members), and task remarks provide a threaded comment system per task. Automated email notifications (`NewCampaignTask`) are sent when a campaign task is created, keeping all members informed without manual communication.

**User Impact:**
- **Accountability:** Every task has an assignee and a status, making ownership unambiguous.
- **Communication:** The remarks thread reduces reliance on external communication channels for task-specific discussion.
- **Notification:** Email alerts on task creation ensure no assignment goes unnoticed.

**Status:** Completed

---

### 3.5 Super Admin Panel

**Description:** A restricted administration panel accessible only to users with the `superadmin` role (mounted under `/~/`). Super Admins can create campaigns, manage campaign membership, view all users, update user roles, and perform both individual and bulk campaign assignments. This panel is the control center for platform-wide governance.

**User Impact:**
- **Centralized Governance:** All user and campaign administration occurs in one protected area rather than being embedded in regular user flows.
- **Bulk Operations:** Assigning multiple users to a campaign simultaneously saves time during onboarding events or organizational restructures.

**Status:** Completed

---

### 3.6 Admin Oversight Panel

**Description:** A secondary elevated panel (`role:admin`, mounted under `/~/~/`) that provides read and supervisory access across all campaigns and projects. Admins can view campaign lists, drill into specific campaigns, inspect individual projects, and review the full project portfolio without performing destructive operations.

**User Impact:**
- **Executive Visibility:** Management can monitor execution status across all campaigns without being granted write permissions that could inadvertently alter data.
- **Separation of Duties:** Admin oversight is clearly separated from Super Admin governance, following the principle of least privilege.

**Status:** Completed

---

## 4. System Design Diagrams

### 4.1 Entity Relationship Diagram (High-Level)

```
User ─────────────────────────────────────────────┐
 │                                                 │
 ├──< CampaignMember >──── Campaign ───< CampaignTask >──< CampaignTaskMember
 │                             │               │
 │                             │               └──< CampaignTaskRemark
 │                             │
 │                             └──< CampaignProject >──< CampaignProjectActivity
 │
 └──< Project >──< ProjectTask >──< ProjectRemarks
          │
          ├──< ProjectContributor
          └──< ProjectActivity
```

> A formal ERD diagram (PNG/PDF) should be generated from the migration files and attached here.

### 4.2 Use Case Diagram

| Actor | Use Cases |
|---|---|
| Guest | Register, Login, Forgot Password, Reset Password |
| User | View Overview, Manage Tasks, Manage Projects, Manage Campaign Tasks, Import CSV Tasks, Add/Remove Contributors, Add Remarks |
| Admin | View All Campaigns, View Campaign Projects, View Project Details |
| Super Admin | Create Campaign, Manage Campaign Members, Manage Users, Assign Roles, Bulk Assign Campaigns |

> A formal UML Use Case Diagram should be attached here.

### 4.3 Flowchart — Campaign Task Import Flow

```
[User selects campaign]
        │
        ▼
[Uploads CSV file via Import endpoint]
        │
        ▼
[System validates CSV structure]
        │
  ┌─────┴─────┐
  │ Valid?     │
  Yes          No
  │            │
  ▼            ▼
[Tasks        [Return validation
 created]      error to user]
  │
  ▼
[Email notification dispatched to campaign members]
  │
  ▼
[Tasks visible in campaign task list]
```

---

## 5. Project Timeline

| Phase | Duration | Start – End | Owner | Deliverables | Status |
|---|---|---|---|---|---|
| 1. Planning | 1 week | Feb 23 – Mar 01 | Developer / Client | Requirements, domain model, route plan | Completed |
| 2. Design | 3 days | Mar 02 – Mar 04 | Developer | ERD, wireframes, role matrix | Completed |
| 3. Development | 3 weeks | Mar 05 – Mar 22 | Developer | Backend, Frontend, Email notifications, CSV import | In Progress |
| 4. Testing | 3 days | Mar 23 – Mar 25 | Developer | Unit tests, Feature tests (Pest), manual QA | Planned |
| 5. Risk Assessment | 1 day | Mar 26 | Developer / Compliance | Security audit, role validation, data integrity checks | Planned |
| 6. UAT | 1 week | Mar 30 – Apr 03 | Developer / Stakeholders | User acceptance testing, feedback collection | Planned |
| 7. Deployment | 1 day | Apr 04 | Developer | Production launch (Docker or bare-metal) | Planned |

### 5.1 Timeline Summary

> **Project:** Task Management System v2 | **Duration:** ~6 weeks | **Status:** In Progress
> **Start:** February 23, 2026 | **Go-Live:** April 04, 2026

---

## 6. Physical / Server Requirements

### 6.1 Production Server Specifications

| Component | Minimum | Recommended | Notes |
|---|---|---|---|
| CPU | Intel Core i3-12100 or Ryzen 3 4100 | Intel Core i5-13400 or Ryzen 5 7600 | Higher IPC improves PHP-FPM throughput and ORM query resolution. |
| RAM | 8 GB DDR4 | 16 GB DDR4/DDR5 | Laravel caches config, routes, and views in memory; more RAM reduces disk I/O. |
| Storage | 128 GB SSD | 256 GB NVMe SSD | Faster I/O benefits CSV imports, log writes, and session storage. |
| Network | 100 Mbps | 1 Gbps | Required for responsive Vite asset delivery and email dispatch throughput. |
| Power | 400W (80+ Bronze) | 500W (80+ Gold) | Stable power ensures uninterrupted queue workers and scheduled tasks. |

### 6.2 Operating System & Software Requirements

| Category | Requirement | Version |
|---|---|---|
| OS | Windows / Ubuntu Server (recommended) | 22.04 LTS or higher |
| Web Server | Nginx or Apache | Latest stable |
| Runtime | PHP | 8.2+ |
| Database | MySQL | 8.0+ |
| Package Manager | Composer | 2.x |
| Node.js | Node.js (for Vite build) | 18+ (20+ recommended) |
| Process Manager | Supervisor (for queue workers) | Latest stable |

---

## 7. Configuration Reference

| Key | Purpose |
|---|---|
| `APP_NAME` | Application display name |
| `APP_ENV` | Environment (`local`, `production`) |
| `APP_DEBUG` | Debug mode toggle (set `false` in production) |
| `APP_URL` | Base URL of the application |
| `DB_CONNECTION` | Database driver (`mysql`) |
| `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` | Database credentials |
| `QUEUE_CONNECTION` | Queue driver (use `database` or `redis` in production) |
| `SESSION_DRIVER` | Session storage driver |
| `CACHE_STORE` | Cache backend |
| `MAIL_MAILER`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_FROM_ADDRESS` | Mail transport configuration |

---

## 8. Production Checklist

- [ ] Set `APP_ENV=production` and `APP_DEBUG=false`
- [ ] Generate a strong `APP_KEY`
- [ ] Configure real mail transport credentials
- [ ] Enable HTTPS at the reverse proxy or load balancer
- [ ] Run `php artisan optimize` and `php artisan migrate --force` on deploy
- [ ] Configure Supervisor or systemd for queue worker persistence
- [ ] Set up database backup and restore procedure
- [ ] Verify role-based route guards (`user`, `admin`, `superadmin`)
- [ ] Validate CSV import endpoint with representative test files

---

## 9. Approval Signatures

### Internal Team Approval

| Role | Name | Signature | Date |
|---|---|---|---|
| Developer | Mark Kevin Romero | | |
| Team Leader | | | |
| Operational Manager | | | |

### External Stakeholder Approval

| Role | Name | Signature | Date |
|---|---|---|---|
| Client Representative | | | |
| Compliance Officer | | | |

---

*Document prepared by: Mark Kevin Romero — Full Stack Developer*
*Date: March 4, 2026*
