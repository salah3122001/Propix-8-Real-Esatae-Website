# Real Estate Management System (Laravel API & Admin Dashboard)

A robust, high-performance, and scalable Real Estate Management System built with **Laravel 12** and **Filament PHP**, adhering to **SOLID principles** and modern architectural best practices.

## 🏗 Architectural Highlights
- **SOLID Principles:** The project is designed with a focus on Single Responsibility, Open-Closed, and Dependency Inversion principles to ensure maintainability.
- **Service Layer Pattern:** Business logic is decoupled from controllers using dedicated Service classes.
- **API Resources:** Optimized data transformation layer separating database models from API responses.
- **Type Safety:** Strong usage of PHP 8.2+ type hinting and return types.

## 🚀 Key Features

### 💎 Powerful Admin Dashboard (Filament PHP)
- **Comprehensive Resource Management:** Manage Units, Cities, Compounds, Developers, and Amenities with a sleek, modern UI.
- **Dynamic Schemas:** Clean and reusable form and table schemas.
- **Role-Based Access Control:** Distinct roles for Admins and Sellers/Buyers.
- **Arabic-First Design:** Fully localized dashboard with native RTL support.
- **Stats Overview:** Real-time dashboards for system-wide analytics.

### 🏢 Specialized Unit Management
- **Intelligent API Response Optimization:** 
    - `UnitListResource`: Lightweight response for lightning-fast listings and search cards.
    - `UnitResource`: Detailed, comprehensive response for property single-views.
- **Advanced Filtering Engine:** Filter by city, compound, price range, area, rooms, bathrooms, and ownership.
- **Status Workflow:** Approval/Rejection cycle for unit listings to ensure quality control.
- **Smart Media Handling:** Seamlessly manages unit images and videos.

### 🔍 Search & Discovery System
- **Advanced Global Search:** Search across multiple entities (Units, Cities, Compounds, Developers, Sellers).
- **High Relevance Engine:** Intelligent ordering prioritizing exact matches in titles and addresses.
- **Localized Search:** Robust support for Arabic and English search queries within the same engine.

### 👥 Seller Management Dashboard
- **Seller-Specific API:** Dedicated endpoints for sellers to manage their listings securely.
- **Personalized Stats:** Sellers can track their total, approved, pending, and rejected units.

### 💳 Transactional Modules
- **Integrated Payment Flow:** Secure handling of unit-related payments.
- **Review & Rating System:** User feedback mechanism for property units.
- **Favorites & Interaction:** "Save for later" functionality for buyers.

## 🛠 Tech Stack
- **Framework:** Laravel 12.x
- **Admin Panel:** Filament PHP (v4)
- **Database:** MySQL
- **Authentication:** Laravel Sanctum (Mobile-ready)
- **UI/UX Extensions:** Filament Language Switch, Laravel DomPDF (for reports).

## 📦 Installation & Setup

1. **Clone & Install:**
   ```bash
   git clone https://github.com/salah3122001/Propix-8-Real-Esatae-Website.git
   cd Propix-8-Real-Esatae-Website
   composer install
   npm install
   ```

2. **Environment Configuration:**
   ```bash
   cp .env.example .env
   # Update DB_DATABASE, DB_USERNAME, DB_PASSWORD in .env
   php artisan key:generate
   ```

3. **Database Initialization:**
   ```bash
   php artisan migrate --seed
   # Optional: Populating with Demo Content
   php artisan db:seed --class=DemoContentSeeder
   ```

4. **Prepare Storage:**
   ```bash
   php artisan storage:link
   ```

5. **Start Development:**
   ```bash
   php artisan serve
   ```

## 📄 License
The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
