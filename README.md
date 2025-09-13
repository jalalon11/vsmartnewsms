# Hardware Repair Management System

A comprehensive Laravel-based hardware repair shop management system designed to streamline repair operations, customer management, and business analytics.

## 🚀 Features

### Core Management
- **Customer Management**: Complete customer profiles with contact information, repair history, and device tracking
- **Device Management**: Track customer devices with detailed specifications, repair history, and status monitoring
- **Repair Order Management**: Full repair workflow from intake to delivery with status tracking
- **Technician Management**: Staff management with specializations, performance tracking, and workload distribution
- **Service Management**: Configurable repair services with pricing, duration estimates, and device type associations
- **Parts Inventory**: Comprehensive parts management with stock levels, pricing, and supplier information

### Business Operations
- **Invoice System**: Automated invoice generation with partial payment support and payment tracking
- **Multi-Service Support**: Handle multiple services per repair order with individual pricing and notes
- **Role-Based Access**: Admin and technician roles with appropriate permissions and dashboard views
- **Registration Control**: Admin-controlled user registration with blocked registration page

### Analytics & Reporting
- **Customer Analytics**: Customer acquisition trends, repeat customer analysis, and geographic distribution
- **Sales Analytics**: Revenue tracking, service performance, and financial reporting
- **Parts Analytics**: Inventory analysis, usage patterns, and reorder recommendations
- **Device Repair Analytics**: Device type trends, repair frequency, and success rates
- **Technician Performance**: Individual performance metrics, workload analysis, and efficiency tracking
- **Service Analytics**: Service popularity, profitability, and duration analysis

### User Experience
- **Responsive Design**: Modern, mobile-friendly interface built with Vue.js and Tailwind CSS
- **Real-time Updates**: Live status updates and notifications
- **Interactive Charts**: Visual analytics with Chart.js integration
- **Optimized Sidebar**: Scrollable navigation with responsive behavior
- **AI Chat Integration**: Built-in customer service chat with predefined service information

## 🛠 Technical Stack

### Backend
- **Framework**: Laravel 12.x
- **PHP**: 8.2+
- **Database**: MySQL
- **Authentication**: Laravel Sanctum
- **API**: RESTful architecture

### Frontend
- **Framework**: Vue.js 3.4
- **Build Tool**: Vite 7.x
- **Styling**: Tailwind CSS 3.2
- **Components**: Inertia.js for SPA experience
- **Charts**: Chart.js with vue-chartjs
- **Routing**: Ziggy for Laravel route integration

### Development Tools
- **Code Quality**: Laravel Pint for code formatting
- **Testing**: PHPUnit for backend testing
- **Package Management**: Composer (PHP), npm (JavaScript)
- **Development Server**: Laravel Sail support

## 📋 System Requirements

- PHP 8.2 or higher
- Composer
- Node.js 18+ and npm
- MySQL 8.0+
- Web server (Apache/Nginx)

## 🚀 Installation

### 1. Clone the Repository
```bash
git clone <repository-url>
cd hardware-repair-system
```

### 2. Install Dependencies
```bash
# Install PHP dependencies
composer install

# Install JavaScript dependencies
npm install
```

### 3. Environment Setup
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Database Configuration
Update your `.env` file with database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hardware_repair_system
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 5. Database Setup
```bash
# Run migrations
php artisan migrate

# Seed the database (optional)
php artisan db:seed
```

### 6. Build Assets
```bash
# Development build
npm run dev

# Production build
npm run build
```

### 7. Start the Application
```bash
# Start Laravel development server
php artisan serve

# In another terminal, start Vite dev server
npm run dev
```

The application will be available at `http://localhost:8000`

## 🔧 Configuration

### User Roles
- **Admin**: Full system access including user management, reports, and system configuration
- **Technician**: Limited access to assigned repair orders and customer information

### Service Categories
The system supports various device types and services:
- Smartphone Services (screen, battery, water damage)
- Laptop/PC Services (screen, keyboard, hardware upgrades)
- Printer Services (maintenance, repairs)
- Custom services with configurable pricing

### Pricing Structure
Base pricing examples (configurable):
- LCD Replacement: ₱350-₱750 (varies by device)
- Battery Replacement: ₱300-₱350
- Software Services: ₱280-₱500
- Website Development: ₱5,000+ (custom pricing)

## 📊 Key Features Detail

### Repair Order Workflow
1. **Intake**: Customer and device registration
2. **Assessment**: Issue description and service selection
3. **Assignment**: Technician allocation
4. **Progress Tracking**: Status updates (pending → in_progress → completed)
5. **Quality Control**: Review and testing
6. **Delivery**: Customer notification and handover

### Inventory Management
- Real-time stock tracking
- Automatic reorder alerts
- Supplier management
- Cost and selling price tracking
- Device-specific part associations

### Analytics Dashboard
- Revenue trends and forecasting
- Customer satisfaction metrics
- Technician productivity analysis
- Popular services and devices
- Inventory turnover rates

## 🔒 Security Features

- Role-based access control
- Secure authentication with Laravel Sanctum
- CSRF protection
- SQL injection prevention
- XSS protection
- Secure password hashing

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🆘 Support

For support and questions:
- Create an issue in the repository
- Contact the development team
- Check the documentation for common solutions

## 🔄 Version History

- **v1.0.0**: Initial release with core repair management features
- **v1.1.0**: Added comprehensive analytics and reporting
- **v1.2.0**: Enhanced user interface and mobile responsiveness
- **v1.3.0**: Integrated AI chat support and advanced inventory management

---

*Built with ❤️ for efficient hardware repair shop management*
