# P2P System Technology Stack

A comprehensive overview of all technologies used in the Click P2P Procurement System.

---

## 🏗️ Core Framework

### Laravel 11.x
**What it is:** PHP web application framework  
**Why we used it:**
- Rapid development with elegant syntax
- Built-in authentication (Laravel Breeze)
- Eloquent ORM for database operations
- Blade templating engine
- Artisan CLI for code generation
- Excellent security features (CSRF, XSS protection)
- Large ecosystem and community support

---

## 🎨 Frontend Technologies

### Tailwind CSS 3.x
**What it is:** Utility-first CSS framework  
**Why we used it:**
- Rapid UI development without writing custom CSS
- Dark mode support built-in
- RTL support via `text-start` and `text-end` utilities
- Highly customizable design system
- Smaller production builds (purges unused CSS)
- Consistent styling across components

### Alpine.js 3.x
**What it is:** Lightweight JavaScript framework  
**Why we used it:**
- Simple reactivity for UI interactions
- No build step required
- Works perfectly with Blade templates
- Handles dropdowns, modals, toggles, tabs
- Much lighter than Vue/React for our needs

### Vite
**What it is:** Modern frontend build tool  
**Why we used it:**
- Fast development server with hot module replacement
- Automatic CSS/JS bundling
- Laravel's default build tool (replaces Webpack)
- Better performance than previous build tools

---

## 🗄️ Database

### MySQL / SQLite
**What it is:** Relational database  
**Why we used it:**
- Battle-tested reliability
- Strong data integrity with foreign keys
- Good performance for our scale
- Easy backup and restore
- SQLite for development convenience

### Database Tables:
| Table | Purpose |
|-------|---------|
| `users` | User accounts and roles |
| `purchase_requests` | Main purchase request data |
| `offers` | Vendor quotations for requests |
| `request_logs` | Audit trail of status changes |
| `vendors` | Vendor/supplier information |
| `budgets` | Monthly budget allocations |
| `settings` | System configuration (key-value) |
| `sessions` | User session management |
| `password_reset_tokens` | Password reset functionality |

---

## 🔐 Authentication

### Laravel Breeze
**What it is:** Lightweight authentication starter kit  
**Why we used it:**
- Simple, minimal authentication scaffolding
- Login, registration, password reset
- Email verification support
- Uses Blade templates (not Vue/React)
- Easy to customize

---

## 📊 Data Visualization

### Chart.js 4.x
**What it is:** JavaScript charting library  
**Why we used it:**
- Simple, flexible charts (bar, doughnut, line)
- Good documentation
- Responsive by default
- Works well with dark mode
- No framework dependencies

---

## 🌍 Internationalization (i18n)

### Laravel Localization
**What it is:** Built-in translation system  
**Why we used it:**
- Native Laravel feature
- JSON translation files for simple key-value pairs
- Supports 3 languages: English, Arabic, Kurdish
- Easy to add more languages

### Supported Languages:
| Language | Code | Direction |
|----------|------|-----------|
| English | `en` | LTR |
| Arabic | `ar` | RTL |
| Kurdish (Sorani) | `ku` | RTL |

---

## 🔤 Typography

### Fonts Used:
| Font | Use Case | Source |
|------|----------|--------|
| **Inter** | English text (LTR) | Bunny Fonts |
| **Tajawal** | Arabic/Kurdish (RTL) | Google Fonts |

**Why these fonts:**
- Inter: Clean, professional, excellent for UI
- Tajawal: Modern Arabic font with good bold weights

---

## 📦 PHP Packages

| Package | Version | Purpose |
|---------|---------|---------|
| `laravel/framework` | 11.x | Core framework |
| `laravel/breeze` | 2.x | Authentication |
| `laravel/sanctum` | 4.x | API tokens (if needed) |
| `laravel/tinker` | 2.x | REPL for debugging |

---

## 🛠️ Development Tools

### Composer
**What it is:** PHP dependency manager  
**Why we used it:** Standard for Laravel package management

### NPM / Node.js
**What it is:** JavaScript package manager and runtime  
**Why we used it:** Required for Vite, Tailwind, and frontend builds

### Git
**What it is:** Version control system  
**Why we used it:** Track code changes, collaboration

---

## 📁 Project Structure

```
p2p-system/
├── app/
│   ├── Http/
│   │   ├── Controllers/      # Request handlers
│   │   └── Middleware/       # Request filters
│   ├── Models/               # Eloquent models
│   └── Providers/            # Service providers
├── config/                   # Configuration files
├── database/
│   ├── migrations/           # Database schema
│   └── seeders/              # Test data
├── lang/
│   ├── en.json               # English translations
│   ├── ar.json               # Arabic translations
│   └── ku.json               # Kurdish translations
├── public/                   # Public assets
├── resources/
│   ├── css/                  # Tailwind CSS
│   ├── js/                   # Alpine.js scripts
│   └── views/                # Blade templates
├── routes/
│   └── web.php               # Web routes
├── storage/                  # Uploads, logs, cache
└── tests/                    # Test files
```

---

## 🔒 Security Features

| Feature | Implementation |
|---------|----------------|
| CSRF Protection | Laravel's `@csrf` directive |
| XSS Prevention | Blade's `{{ }}` auto-escaping |
| SQL Injection | Eloquent ORM parameterized queries |
| Password Hashing | Bcrypt via `Hash::make()` |
| Session Security | Encrypted cookies |
| Rate Limiting | Laravel's throttle middleware |

---

## 🚀 Deployment Requirements

### Server Requirements:
- PHP 8.2+
- MySQL 8.0+ or MariaDB 10.6+
- Composer 2.x
- Node.js 18+ (for building assets)
- Web server (Apache/Nginx)

### PHP Extensions Required:
- BCMath
- Ctype
- Fileinfo
- JSON
- Mbstring
- OpenSSL
- PDO
- Tokenizer
- XML

---

## 📈 Scalability Notes

The current architecture can handle:
- **Users**: Hundreds of concurrent users
- **Requests**: Thousands of purchase requests
- **Data**: Years of historical data

For larger scale, consider:
- Redis for session/cache storage
- Queue workers for email notifications
- Database read replicas
- CDN for static assets
