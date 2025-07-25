# Admin Panel Codebase Changes Log

## Problems Identified

1. **Inconsistent session role checks** (mixed use of `$_SESSION['user_role']` and `$_SESSION['role']`).
2. **UI inconsistency**: Only some pages used Bootstrap, others were plain HTML; no unified navigation/sidebar.
3. **Missing CRUD**: No edit/delete for buses and users, no edit for routes, no create for users.
4. **Insecure delete actions**: Used GET instead of POST, no CSRF protection.
5. **No feedback for actions**: Add/edit/delete actions did not show success/failure messages.
6. **No input validation or error handling**: Only basic HTML `required` attributes, no server-side validation.
7. **No pagination or search**: All lists are unpaginated, which can be problematic for large datasets.
8. **No protection against deleting self/admin**: Users could potentially delete their own admin account.

---

## Session Role Check Unification

### Problem

Session role checks were inconsistent across admin files. Some files used `$_SESSION['user_role']`, others used `$_SESSION['role']`. The login process sets `$_SESSION['role']`, so all admin checks should use this for consistency and reliability.

### Changes Made

- **user_profile.php**
  - Changed session role check to use `$_SESSION['role']`.
- **users.php**
  - Changed session role check to use `$_SESSION['role']`.
- **sales.php**
  - Changed session role check to use `$_SESSION['role']`.
- **routes.php**
  - Changed session role check to use `$_SESSION['role']`.
- **buses.php**
  - Changed session role check to use `$_SESSION['role']`.
- **bookings.php**
  - Changed session role check to use `$_SESSION['role']`.
- **dashboard.php**
  - Confirmed only `$_SESSION['role']` is used and removed outdated comment.

### Why?

- Ensures all admin pages consistently check for admin access using the correct session variable.
- Prevents accidental lockout or unauthorized access due to mismatched session variable names.
- Aligns with how the session is set in `login.php`.

---

## Admin UI Unification & Bootstrap Styling (April 2024)

### Problem

Admin pages had inconsistent UI, lacked Bootstrap styling, and did not provide a unified navigation/sidebar. There was no feedback for actions, and the user experience was not modern or consistent with the dashboard.

### Changes Made

- **buses.php**
  - Added Bootstrap 5 styling to forms and tables.
  - Added a sidebar navigation (matching dashboard).
  - Added feedback alert for successful add.
  - Unified layout and container usage.
- **routes.php**
  - Added Bootstrap 5 styling to forms and tables.
  - Added a sidebar navigation (matching dashboard).
  - Added feedback alerts for add/delete actions.
  - Unified layout and container usage.
  - Fixed linter error in delete button link.
- **users.php**
  - Added Bootstrap 5 styling to tables.
  - Added a sidebar navigation (matching dashboard).
  - Unified layout and container usage.
  - Fixed linter error in user profile view link.
- **user_profile.php**
  - Added Bootstrap 5 styling using card and list group.
  - Added a back button to users list.
  - Unified layout and container usage.
- **sales.php**
  - Added Bootstrap 5 styling to tables.
  - Added a sidebar navigation (matching dashboard).
  - Unified layout and container usage.

### Why?

- Provides a modern, consistent, and user-friendly admin interface.
- Makes navigation easier for admins.
- Gives feedback for actions (add/delete).
- Lays the foundation for further CRUD and UX improvements.

---

## Admin CRUD Improvements (April 2024)

### Problem

Admin pages lacked full CRUD (edit/update/delete) for buses, routes, and users. Delete actions were sometimes insecure (GET), and there was no feedback for edit/delete actions.

### Changes Made

- **buses.php**
  - Added Edit and Delete functionality for buses.
  - Edit uses Bootstrap modal; Delete uses POST with confirmation.
  - Added feedback alerts for edit/delete actions.
- **routes.php**
  - Added Edit functionality for routes (Bootstrap modal).
  - Secured Delete to use POST (no longer GET), with confirmation and feedback.
- **users.php**
  - Added Edit and Delete functionality for users (Bootstrap modal for edit, POST for delete).
  - Prevented deleting the currently logged-in user.
  - Added feedback alerts for edit/delete actions.

### Why?

- Provides full CRUD for all main admin entities.
- Secures destructive actions (POST, not GET).
- Improves admin workflow and user experience.
- Lays groundwork for further improvements (CSRF, validation, etc).

---

## Admin Security & Validation Improvements (April 2024)

### Problem

Admin forms were vulnerable to CSRF attacks and lacked robust server-side validation. Destructive actions (edit/delete) could be triggered without CSRF tokens, and input validation was minimal.

### Changes Made

- Added CSRF token generation and validation to all admin forms (add, edit, delete for buses, routes, users).
- Added server-side validation for all form inputs (required fields, type checks, and basic sanitization).
- Provided error feedback for invalid submissions.

### Why?

- Protects against CSRF attacks and accidental/destructive actions.
- Ensures only valid data is processed and stored.
- Improves overall security and reliability of the admin panel.

---

**Next planned step:**

- Add pagination and search to large tables for better usability.
