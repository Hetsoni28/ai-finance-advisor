<div align="center">

<img src="https://capsule-render.vercel.app/api?type=waving&color=gradient&height=250&section=header&text=FinanceAI%20Advisor&fontSize=50&fontAlignY=35&desc=Enterprise%20Financial%20Management%20Platform&descAlignY=55&descAlign=50&animation=twinkling" />

[![Typing SVG](https://readme-typing-svg.demolab.com?font=Fira+Code&weight=600&size=22&pause=1000&color=2563EB&center=true&vCenter=true&width=600&lines=AI-Powered+Financial+Assistant;Secure+Multi-Tenant+Architecture;Built+with+Laravel+8+%26+Gemini+AI;Real-Time+Market+Data+Integration)](https://git.io/typing-svg)

<br/>

[![Built with Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP 8+](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
[![Google Gemini](https://img.shields.io/badge/Google_Gemini-8E75B2?style=for-the-badge&logo=google&logoColor=white)](https://deepmind.google/technologies/gemini/)
[![Vercel](https://img.shields.io/badge/Vercel-000000?style=for-the-badge&logo=vercel&logoColor=white)](https://vercel.com)

<br/>

**[Live Demo](https://finance-advisor-wine.vercel.app)** | **[Report Issue](#-contact--hire-me)** | **[Architecture Docs](./ARCHITECTURE.md)**

<br/>

<img src="https://komarev.com/ghpvc/?username=hetvi-finance-advisor&label=Profile%20Views&color=blue&style=flat-square" alt="Profile Views" />

</div>

---

## 🎯 Project Overview

**FinanceAI Advisor** is an enterprise-grade, AI-driven financial SaaS platform designed to bridge the gap between traditional manual ledger tracking and intelligent, automated financial forecasting.

Instead of just tracking where your money goes, FinanceAI acts as a **context-aware financial consultant**. It reads your personal spending habits and securely combines them with real-time global market data to provide personalized, actionable financial advice via the Google Gemini engine.

### 💡 The Problem It Solves
Managing shared finances across families or teams is often chaotic, and hiring a personal financial consultant is expensive. FinanceAI solves this by providing a unified, secure ledger with strict **Role-Based Access Control (RBAC)**, topped with a mathematical AI agent that understands your specific context.

---

## 🔥 Core Features

<table align="center">
  <tr>
    <td align="center" width="50%">
      <h3>🧠 Context-Aware AI</h3>
      <p>Utilizes <strong>Google Gemini</strong> and <strong>CoinGecko</strong> APIs. The AI secretly injects your exact Net Worth and spending categories into the prompt to provide highly mathematical, personalized advice.</p>
    </td>
    <td align="center" width="50%">
      <h3>🔐 Secure Family Workspaces</h3>
      <p>Strict multi-tenant architecture with <strong>Magic Link Invitations</strong>. Personal expenses stay private, while shared expenses are visible only to invited family members via RBAC.</p>
    </td>
  </tr>
  <tr>
    <td align="center" width="50%">
      <h3>📊 Dynamic Data Analytics</h3>
      <p>Integrated charting engines that render complex financial visualizations (Income vs. Expenses) dynamically on the server, ensuring zero JavaScript bundle bloat.</p>
    </td>
    <td align="center" width="50%">
      <h3>🛡️ Immutable Auditing</h3>
      <p>Integrated <strong>Spatie Activitylog</strong> to maintain a cryptographic, immutable audit trail of all sensitive user actions (logins, expense modifications) to prevent data tampering.</p>
    </td>
  </tr>
</table>

---

## 🏗️ Architecture & Documentation

To see a deep dive into the system design, including **Mermaid Flowcharts**, **Database ERDs**, and **Service Pattern** explanations, please view the dedicated architecture files:

- 🔗 **[Backend System Architecture](./ARCHITECTURE.md)**
- 🔗 **[Frontend UI & Rendering Strategy](./FRONTEND_ARCHITECTURE.md)**

---

## 💻 Tech Stack Deep Dive

<div align="center">
  <img src="https://skillicons.dev/icons?i=laravel,php,mysql,tailwind,js,git,vercel&perline=7" />
</div>

- **Core Framework:** Laravel 8 (PHP)
- **Database Architecture:** MySQL (Managed via Eloquent ORM & Query Builder)
- **Frontend Design System:** Tailwind CSS 3.4 (JIT Compiled via Laravel Mix)
- **State & Async Logic:** Vanilla JS & Axios (No heavy SPA bloat)
- **Deployment:** Vercel (Frontend / Serverless)

---

## 🚀 Local Development Setup

Want to run this enterprise platform locally?

1. **Clone the repository:**
   ```bash
   git clone https://github.com/hetvi-prajapati/finance-advisor.git
   cd finance-advisor
   ```

2. **Install Dependencies:**
   ```bash
   composer install
   npm install
   ```

3. **Configure Environment:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *(Ensure you populate the `DB_*` and `GEMINI_API_KEY` variables in `.env`)*

4. **Migrate & Serve:**
   ```bash
   php artisan migrate
   npm run dev
   php artisan serve
   ```

---

## 📸 Platform Interface

*(Replace these placeholders with high-quality GIFs or screenshots of your application)*

| **Dashboard Analytics** | **AI Financial Assistant** |
| :---: | :---: |
| <img src="https://via.placeholder.com/600x400.png?text=Animated+Dashboard+GIF" alt="Dashboard" width="100%"> | <img src="https://via.placeholder.com/600x400.png?text=AI+Chat+Interface" alt="AI Chat" width="100%"> |

---

## 🤝 Contact & Hire Me

**Are you looking for a Full-Stack Developer to build scalable, secure, and AI-integrated web applications?** 

I am currently open to freelance opportunities and full-time roles. I specialize in PHP, Laravel, React/Vue, and Enterprise AI integrations.

<div align="center">
  
  [![GitHub](https://img.shields.io/badge/GitHub-100000?style=for-the-badge&logo=github&logoColor=white)](https://github.com/hetvi-prajapati)
  [![LinkedIn](https://img.shields.io/badge/LinkedIn-0077B5?style=for-the-badge&logo=linkedin&logoColor=white)](#)
  [![Email](https://img.shields.io/badge/Email-D14836?style=for-the-badge&logo=gmail&logoColor=white)](mailto:your-email@example.com)

</div>

<br/>

<div align="center">
  <i>If you found this architecture impressive, please consider giving it a ⭐!</i>
</div>
