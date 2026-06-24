# Task Manager with Auth and CRUD

A lightweight, secure PHP-based Task Management application featuring user authentication (Sign Up / Login / Logout) and full CRUD (Create, Read, Update, Delete) functionality for managing tasks.

## 🚀 Features

*   **User Authentication:** Secure registration, login, and logout functionality.
*   **Task Management (CRUD):** 
    *   Create new tasks with titles and descriptions.
    *   View a list of your specific tasks.
    *   Edit/Update task details.
    *   Delete tasks.
*   **Security:** Password hashing and session-based user authentication to ensure users can only access their own tasks.

## 🛠️ Project Structure

*   `/auth` - Handles user registration and login logic.
*   `/config` - Database connection and configuration settings.
*   `/includes` - Reusable layout components (headers, footers, navigation).
*   `index.php` - The main dashboard/application landing page.
*   `logout.php` - Clear sessions and log the user out safely.

## 🔧 Installation & Setup

1. **Clone the repository:**
```bash
   git clone [https://github.com/khalledan/task-manager.git](https://github.com/khalledan/task-manager.git)
   cd task-manager
