# 🤖 AI Finance Advisor

<div align="center">

[![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![MySQL](https://img.shields.io/badge/MySQL-00000F?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
[![License: MIT](https://img.shields.io/badge/License-MIT-green.svg?style=for-the-badge)](https://opensource.org/licenses/MIT)

An **AI-powered finance advisor platform** for smart financial guidance, analytics, and personalized recommendations. Track expenses, manage collaborative family budgets, and chat with an AI agent to achieve your financial goals.

[View Live Demo](https://ai-finance-advisor-ebon.vercel.app) • [Report Bug](#-support) • [Request Feature](#-support)

</div>

---

## ✨ Key Features

- **🧠 Intelligent AI Chat Engine:** Integrated with Gemini API for conversational financial guidance and Alpha Vantage for real-time market snapshots.
- **📊 Comprehensive Ledger:** Track personal and family incomes, expenses, and automatically calculate monthly averages.
- **👪 Collaborative Family Workspaces:** Secure, multi-tenant family module with magic-link email invites and role-based access control.
- **📈 Visual Analytics & Reports:** Interactive charts (QuickChart) and automated PDF monthly report generation (DOMPDF).
- **🛡️ Enterprise Security:** Activity logging for security events, granular access management, and secure authentication (Laravel Sanctum).
- **💳 Subscription System:** SaaS-style billing tiers for premium features.
- **💻 Admin Dashboard:** Master admin node for platform monitoring and user management.

---

## 🛠️ Technology Stack

### Backend
- **Laravel 8.x** (PHP Framework)
- **MySQL** (Database)
- **Laravel Sanctum** (Authentication)

### Frontend
- **Tailwind CSS 3.4** (Utility-first CSS)
- **Laravel Mix / Webpack** (Asset Bundling)
- **Axios** (HTTP Client)

### Integrations
- **Google Gemini API** (AI Engine)
- **Alpha Vantage API** (Market Data)
- **SMTP** (Email Deliverability)

---

## 🚀 Getting Started

Follow these steps to run the project locally on your machine.

### Prerequisites

Ensure you have the following installed:
- [PHP 7.3 or 8.0+](https://www.php.net/) (XAMPP recommended for Windows)
- [Composer](https://getcomposer.org/)
- [Node.js & NPM](https://nodejs.org/)
- MySQL/MariaDB

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/HetSoni28/ai-finance-advisor.git
   cd ai-finance-advisor
   ```

2. **Install PHP Dependencies**
   ```bash
   composer install
   ```

3. **Install Node Dependencies**
   ```bash
   npm install
   ```

4. **Environment Setup**
   Copy the example environment file and configure your variables:
   ```bash
   cp .env.example .env
   ```
   **Important Variables to set in `.env`:**
   - `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
   - `GEMINI_API_KEY`
   - `ALPHA_VANTAGE_API_KEY`

5. **Generate Application Key**
   ```bash
   php artisan key:generate
   ```

6. **Run Database Migrations**
   ```bash
   php artisan migrate
   ```

7. **Compile Frontend Assets**
   ```bash
   npm run dev
   ```

8. **Start the Development Server**
   ```bash
   php artisan serve
   ```
   *Visit `http://127.0.0.1:8000` in your browser.*

---

## 📸 Screenshots

*(Replace these placeholders with actual screenshots of your application)*

| Dashboard Overview | AI Chat Interface |
| :---: | :---: |
| <img src="https://via.placeholder.com/600x400.png?text=Dashboard+Screenshot" alt="Dashboard" width="100%"> | <img src="https://via.placeholder.com/600x400.png?text=AI+Chat+Screenshot" alt="AI Chat" width="100%"> |

| Expense Ledger | Family Workspace |
| :---: | :---: |
| <img src="https://via.placeholder.com/600x400.png?text=Expense+Ledger" alt="Expenses" width="100%"> | <img src="https://via.placeholder.com/600x400.png?text=Family+Workspace" alt="Family" width="100%"> |

---

## 🤝 Contributing

Contributions are what make the open source community such an amazing place to learn, inspire, and create. Any contributions you make are **greatly appreciated**.

1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the Branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

---

## 📄 License

Distributed under the MIT License. See `LICENSE` for more information.

---

## 💬 Support

For support, email hetsony143@gmail.com or open an issue in the repository.
