# CLSU Web CMS Unit Test Cases

This sheet follows the same structure as the sample guide: `Test Case ID`, `Test Script ID`, `Test Description`, `Test Data`, `Expected Result`, `Actual Result`, and `Status`.

Automated execution in this run covered the core routes, login flow, and role-scope permission rules. Wider CMS modules and third-party integrations are listed here as formal test cases and still need separate manual or integration execution.

## Login and Access Control

| Test Case ID | Test Script ID | Test Description | Test Data | Expected Result | Actual Result | Status |
| --- | --- | --- | --- | --- | --- | --- |
| TC-LOGIN-001 | TS-001 | Login with correct admin email and password | Email: `admin.testing@clsu.edu` Password: `password` Role: `admin` | User is authenticated and redirected to `/admin` dashboard | User was authenticated and redirected to dashboard in automated feature test | Pass |
| TC-LOGIN-002 | TS-002 | Access admin dashboard using editor role | Authenticated user with `role = editor` | Editor is allowed into the admin dashboard | Editor user successfully loaded `/admin` in automated feature test | Pass |
| TC-LOGIN-003 | TS-003 | Login with invalid password | Email: `admin.invalid@clsu.edu` Password: `wrong-password` | User stays on login flow and sees credential error | Request redirected back to `/admin/login` with session error in automated feature test | Pass |
| TC-LOGIN-004 | TS-004 | Open admin dashboard while not authenticated | No login session | User is redirected to `/admin/login` | Guest request redirected to admin login in automated feature test | Pass |
| TC-LOGIN-005 | TS-005 | Access admin dashboard using non-admin account | Authenticated user with `role = null`, `is_admin = false` | User is denied admin access and redirected to login | Non-admin user was redirected to admin login in automated feature test | Pass |

## Public Pages

| Test Case ID | Test Script ID | Test Description | Test Data | Expected Result | Actual Result | Status |
| --- | --- | --- | --- | --- | --- | --- |
| TC-PUBLIC-001 | TS-006 | Open homepage | URL: `/` | Homepage loads successfully | Homepage returned HTTP 200 in automated feature test | Pass |
| TC-PUBLIC-002 | TS-007 | Open About page | URL: `/about` | About page loads successfully | About page returned HTTP 200 in automated feature test | Pass |
| TC-PUBLIC-003 | TS-008 | Open Privacy Policy page | URL: `/privacy-policy` | Privacy Policy page loads successfully | Privacy Policy page returned HTTP 200 in automated feature test | Pass |
| TC-PUBLIC-004 | TS-009 | Open admin login page | URL: `/admin/login` | Login page loads successfully | Admin login page returned HTTP 200 in automated feature test | Pass |
| TC-PUBLIC-005 | TS-010 | Open News page | URL: `/news` | News page loads successfully | Pending manual execution | Pending |
| TC-PUBLIC-006 | TS-011 | Open a college public page | URL: `/college/{college}` with valid slug | College page loads with college content | Pending manual execution against seeded college data | Pending |
| TC-PUBLIC-007 | TS-012 | Open a department public page | URL: `/college/{college}/departments/{department}` with valid route keys | Department page loads with department content | Pending manual execution against seeded department data | Pending |
| TC-PUBLIC-008 | TS-013 | Open news announcement board detail page | Valid article or announcement slug | Detail page loads and shows the selected item | Pending manual execution against existing content records | Pending |

## CMS Content Management

| Test Case ID | Test Script ID | Test Description | Test Data | Expected Result | Actual Result | Status |
| --- | --- | --- | --- | --- | --- | --- |
| TC-CMS-001 | TS-014 | Create a new article from admin panel | Valid article title, slug, body, publish date, college scope | Article is saved and appears in article list and public detail page | Pending manual execution | Pending |
| TC-CMS-002 | TS-015 | Edit an existing article | Existing article plus updated title/body | Changes are saved and reflected in admin and public page | Pending manual execution | Pending |
| TC-CMS-003 | TS-016 | Create a new announcement | Valid announcement fields | Announcement is saved and appears in announcement list | Pending manual execution | Pending |
| TC-CMS-004 | TS-017 | Create a college facility entry | College slug, title, description, image | Facility record is created and appears in college facilities page | Pending manual execution | Pending |
| TC-CMS-005 | TS-018 | Create a faculty member record | College slug, department, faculty details, image | Faculty member is saved and visible in faculty listing | Pending manual execution | Pending |
| TC-CMS-006 | TS-019 | Create a department linkage | Valid college, department, linkage title, partner data | Linkage is saved and appears in department linkage section | Pending manual execution | Pending |
| TC-CMS-007 | TS-020 | Manage scholarship entry | Scholarship title, slug, body, attachment | Scholarship is saved and accessible from scholarship page | Pending manual execution | Pending |
| TC-CMS-008 | TS-021 | Manage college download | Valid file upload and metadata | Download is saved and file is downloadable from public page | Pending manual execution | Pending |
| TC-CMS-009 | TS-022 | Update college appearance settings | Logo, hero, and color settings | Appearance updates are stored and reflected on public/admin pages | Pending manual execution | Pending |

## Permissions and Scope Rules

| Test Case ID | Test Script ID | Test Description | Test Data | Expected Result | Actual Result | Status |
| --- | --- | --- | --- | --- | --- | --- |
| TC-PERM-001 | TS-023 | Superadmin college access validation | `role = superadmin` | User can access and manage any college, including global scope | Verified in automated unit test of `User` permission methods | Pass |
| TC-PERM-002 | TS-024 | College admin scope restriction | `role = admin`, `college_slug = engineering` | User can manage assigned college only and not global content | Verified in automated unit test of `User` permission methods | Pass |
| TC-PERM-003 | TS-025 | Department-bounded editor restriction | `role = editor`, `college_slug = engineering`, `department = Information Technology` | User can access only own department inside own college | Verified in automated unit test of `User` permission methods | Pass |
| TC-PERM-004 | TS-026 | Editor bounded scope flags | `role = editor`, `college_slug = engineering` | User is bounded to college, not department or organization unless assigned | Verified in automated unit test of `User` permission methods | Pass |

## Integrations and Operations

| Test Case ID | Test Script ID | Test Description | Test Data | Expected Result | Actual Result | Status |
| --- | --- | --- | --- | --- | --- | --- |
| TC-INT-001 | TS-027 | Google Drive token validation | Valid saved Google Drive credentials and refresh token | Token check command completes without credential error | Pending manual/integration execution | Pending |
| TC-INT-002 | TS-028 | Google Drive upload flow | Upload image or file through admin form with `FILESYSTEM_DISK=google` | File is uploaded, path is saved, and media is retrievable | Pending manual/integration execution | Pending |
| TC-INT-003 | TS-029 | Facebook fetch command | Active Facebook configuration and valid token | Recent posts are fetched and converted to articles without duplication error | Pending manual/integration execution | Pending |
| TC-INT-004 | TS-030 | Facebook webhook verification | Valid `FACEBOOK_VERIFY_TOKEN` and public callback URL | GET verification challenge succeeds and POST events are accepted | Pending manual/integration execution | Pending |
| TC-INT-005 | TS-031 | Queue worker health check | Database queue enabled with pending jobs | Queue worker processes jobs without failure | Pending manual/integration execution | Pending |
| TC-INT-006 | TS-032 | Scheduler task health check | Scheduler enabled | Scheduled tasks run without runtime errors | Pending manual/integration execution | Pending |
