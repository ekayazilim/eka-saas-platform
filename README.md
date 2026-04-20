# Eka SaaS Platform

> 🚀 Production-ready multi-tenant SaaS platform built with pure PHP 8+ and Tailwind CSS

Eka SaaS Platform is a scalable, high-performance B2B SaaS system developed from scratch without using any external frameworks. It is designed for real-world production environments and provides a complete multi-tenant architecture including user management, subscription logic, API systems and modern dashboards.

---

## ⚡ Core Features

* 🧠 Multi-Tenant Architecture
  Fully isolated tenant structure where each company manages its own data, users and resources

* 🔐 Authentication & Role System
  Secure login system with role-based access control (Super Admin, Owner, Admin, Member)

* 📊 Modern Dashboard
  Tailwind CSS powered interface with real-time usage statistics, widgets and activity overview

* 🏢 Tenant & Workspace Management
  Each company has its own workspace, users, projects and limits

* 📦 Subscription & Plan System
  Free, Pro and Business plans with limits:

  * User limit
  * Project limit
  * API usage limit
  * Storage limit

* 🔑 API Key Management
  Secure API key generation, revocation and usage tracking

* 📈 Usage Tracking
  Real-time monitoring of platform usage with visual indicators and limits

* 🧾 Activity Logs
  Detailed logging of important system actions

* 🔔 Notification System
  In-app notifications with read/unread states

* 🔗 Dynamic Base URL
  Automatically detects domain and works without configuration

---

## 🛠️ Tech Stack

* Backend: PHP 8.1+ (framework-free)
* Frontend: Tailwind CSS
* Database: MySQL (PDO prepared statements)
* Architecture: Custom MVC (modular and scalable)

---

## 🔒 Security

* CSRF protection middleware
* PDO prepared statements (SQL injection protection)
* Secure password hashing
* Role-based access control
* Tenant-level data isolation

---

## ⚙️ Installation

1. Import database:

```
database/eka_saas.sql
```

2. Configure database connection:

```
config/database.php
```

3. Run the project from root directory:

```
http://your-domain.com
```

No /public directory required. The system runs directly from root.

---

## 🔐 Demo Credentials

Super Admin
[admin@ekayazilim.com](mailto:admin@ekayazilim.com) / ekayazilim

Tenant Owner
[owner@ekayazilim.com](mailto:owner@ekayazilim.com) / ekayazilim

Demo User
[demo@ekayazilim.com](mailto:demo@ekayazilim.com) / ekayazilim

---

## 🏗️ Architecture Overview

* Multi-tenant SaaS design
* Modular MVC structure
* Scalable and production-ready system

---

## 🛣️ Roadmap

* Payment gateway integration
* Advanced billing system
* Webhook support
* API expansion
* Multi-tenant scaling improvements

---

## ⭐ Why this project?

This platform is designed based on real SaaS architecture principles and production needs. It is ideal for developers and companies who want to build scalable SaaS systems without relying on heavy frameworks.

---

## 🤝 Contributing

Pull requests are welcome. The project is actively maintained and continuously improved.

---

## 📄 License

Eka Yazılım ve Bilişim Sistemleri © All rights reserved.
