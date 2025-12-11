# Spacexinfo - Premium Trading & Crypto Dashboard Template (PHP/MySQL for WordPress)

## 💡 Overview

This template provides a professional, fully responsive user dashboard designed specifically for a **Trading, Brokerage, or Financial platform**. This is a dynamic, full-stack solution built on PHP/MySQL and structured to function seamlessly within any standard WordPress environment as a Custom Page Template.

### 🚀 Key Selling Features

* **High-Value Niche:** A ready-made UI/UX solution for the lucrative trading and crypto brokerage market.
* **Dynamic, Full-Stack Code:** Includes server-side logic (PHP/MySQL) for personalized user experiences.
* **WordPress Integration Ready:** Designed to run as a Custom Page Template, inheriting the WordPress environment and utilizing the secure `$wpdb` object for database access.
* **Secure Authentication Ready:** Built-in logic relies on PHP sessions (`$_SESSION`) for logged-in status validation.
* **Role-Based Access Control (RBAC):** Supports conditional feature display based on the user's custom `role` (e.g., 'user', 'admin').
* **Monetization Ready:** Features real-time display of the user's current **trading balance** (fetched directly from the database).
* **Modern UI:** Fully responsive, dark-mode dashboard interface built with clean HTML5, CSS3, and Vanilla JavaScript.

### 🛠️ Technology Stack

| Component | Role | Notes |
| :--- | :--- | :--- |
| **Server-Side** | **PHP** | Logic, session management, and database queries. |
| **Platform** | **WordPress** | Template engine and provides the `$wpdb` database connection object. |
| **Database** | **MySQL/MariaDB** | Requires a custom `users` table. |
| **Frontend** | **HTML5, CSS3, JS** | Responsive design and interactive UI. |

---

## ⚙️ Setup and Installation Guide

This template requires a functional WordPress installation and a custom login script (like your assumed `login-page.php`) to handle user sessions.

### 1. Database Setup (Custom `users` Table)

The template requires a single, custom table named `users` to manage all authentication, roles, and user balance data.

Run the following SQL query to create the necessary table schema in your WordPress database:

```sql
CREATE TABLE users (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE, 
    firstname VARCHAR(100),
    lastname VARCHAR(100),
    phone VARCHAR(20),
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL, /* Stores the secure password HASH (e.g., via password_hash()) */
    role VARCHAR(10) NOT NULL DEFAULT 'user', /* Used for access control ('user', 'admin', etc.) */
    balance DECIMAL(10, 2) NOT NULL DEFAULT '0.00', /* The user's current trading balance */
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
