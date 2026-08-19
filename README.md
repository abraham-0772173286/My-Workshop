# 🔧 Workshop Management System

A comprehensive PHP-based workshop management system for automotive repair shops with role-based authentication, customer management, repair job tracking, and payment processing.

![Workshop Management System](https://img.shields.io/badge/PHP-8.0+-blue.svg)
![MySQL](https://img.shields.io/badge/MySQL-8.0+-orange.svg)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-purple.svg)
![License](https://img.shields.io/badge/License-MIT-green.svg)

## 🚀 Features

### 🔐 **Role-Based Authentication System**
- **Admin**: Full system access and user management
- **Owner**: Business oversight and financial reports  
- **Cashier**: Daily operations and customer service
- Secure login with automatic account locking
- Time-based unlocking and password reset functionality

### 👥 **Customer Management**
- Customer registration and profile management
- Contact information and service history
- Vehicle ownership tracking
- Customer search and filtering

### 🚗 **Vehicle Management**
- Vehicle registration with plate numbers
- Make, model, and year tracking
- Service history per vehicle
- Customer-vehicle relationship management

### 🛠️ **Repair Job System**
- Job creation and status tracking
- Parts and labor cost calculation
- Repair type categorization
- Job completion workflow
- Real-time status updates

### 💰 **Payment Processing**
- Multiple payment methods (Cash, M-Pesa, Bank Transfer)
- Payment reference tracking
- Receipt generation and management
- Payment history and reports

### 📊 **Dashboard & Analytics**
- Role-specific dashboards
- Key performance indicators (KPIs)
- Revenue tracking and charts
- Pending jobs monitoring
- Real-time metrics

### 🔒 **Security Features**
- Auto-lock after failed login attempts
- Session management and security
- User activity logging and monitoring
- Security event dashboard
- Admin-controlled user management

## 🛠️ **Technology Stack**

- **Backend**: PHP 8.0+ with PDO
- **Database**: MySQL 8.0+
- **Frontend**: Bootstrap 5.3, jQuery 3.7
- **Charts**: Chart.js
- **Icons**: Bootstrap Icons
- **Animations**: Animate.css
- **Notifications**: Toastr.js

## 📦 **Installation**

### **Prerequisites**
- PHP 8.0 or higher
- MySQL 8.0 or higher
- Apache/Nginx web server
- Git

### **Setup Instructions**

1. **Clone the repository**
   ```bash
   git clone https://github.com/yourusername/my-workshop.git
   cd my-workshop
   ```

2. **Configure Database**
   - Create a MySQL database named `workshop`
   - Update database credentials in `configs/database.php`
   
3. **Initialize Database**
   ```bash
   # Visit this URL in your browser
   http://localhost/workshop/setup_database.php
   ```

4. **Set Permissions**
   ```bash
   chmod 755 classes/.sessions
   chmod 644 configs/database.php
   ```

5. **Access the Application**
   ```
   URL: http://localhost/workshop/inc/landing.php
   ```

### **Default Login Credentials**
- **Admin**: username `admin`, password `2212Aa@0`
- **Owner**: username `owner`, password `2212Aa@0`  
- **Cashier**: username `cashier`, password `2212Aa@0`

> ⚠️ **Important**: Change default passwords immediately after installation!

## 🎯 **Usage**

### **Getting Started**
1. Log in using the role-specific buttons on the landing page
2. Admins should first change default passwords in User Management
3. Create customer records and register vehicles
4. Start creating repair jobs and processing payments
5. Monitor performance through the dashboard

### **User Roles Guide**

#### **Administrator**
- Access User Management to create/manage accounts
- Monitor security events in Security Monitor
- Configure system settings
- View all data and generate reports

#### **Owner**
- Review business performance dashboards
- Access financial reports and analytics
- Monitor repair job completion rates
- Oversee cashier activities

#### **Cashier**  
- Register new customers and vehicles
- Create and manage repair jobs
- Process payments and issue receipts
- Update job statuses

## 📁 **Project Structure**

```
workshop/
├── assets/               # Static assets (images, CSS, JS)
│   ├── images/          # Hero images and logos
│   └── lang/            # Language files
├── classes/             # Backend PHP classes
│   ├── .sessions/       # Session storage
│   ├── Login.php        # Authentication system
│   ├── UserManagement.php # User management API
│   ├── Dashboard.php    # Dashboard data
│   └── ...              # Other business logic classes
├── configs/             # Configuration files
│   ├── database.php     # Database connection
│   └── db.sql           # Database schema
├── inc/                 # Include files
│   ├── app.php          # Core application functions
│   ├── landing.php      # Login landing page
│   └── users/           # User-related pages
├── public/              # Main application pages
│   ├── models/          # Feature-specific pages
│   ├── index.php        # Dashboard
│   ├── repair_jobs.php  # Repair job management
│   ├── customers.php    # Customer management
│   └── vehicles.php     # Vehicle management
├── json/                # JSON data files
└── setup_database.php   # Database initialization
```

## 🔧 **Configuration**

### **Database Configuration**
Edit `configs/database.php`:
```php
$server = 'localhost';
$username = 'root';
$password = 'your_password';
$database = 'workshop';
```

### **Security Settings**
- Failed login attempts: 5 (configurable in Login.php)
- Auto-lock duration: 15 minutes (configurable)
- Session timeout: Browser session
- Password requirements: Minimum 8 characters

## 🚨 **Security Considerations**

- Change all default passwords immediately
- Use HTTPS in production
- Regularly backup the database
- Monitor user activity logs
- Keep PHP and MySQL updated
- Implement proper file permissions

## 📖 **Documentation**

- [User Management Guide](USER_MANAGEMENT_GUIDE.md) - Complete guide for managing users and security
- [Database Schema](configs/db.sql) - Complete database structure
- [Setup Guide](setup_database.php) - Automated database setup

## 🤝 **Contributing**

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📝 **License**

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 📞 **Support**

If you encounter any issues or need assistance:

1. Check the [User Management Guide](USER_MANAGEMENT_GUIDE.md)
2. Review the Security Monitor for system status
3. Create an issue on GitHub
4. Contact the development team

## 🎉 **Acknowledgments**

- Bootstrap team for the UI framework
- Chart.js for analytics visualizations
- Font Awesome and Bootstrap Icons
- jQuery team for DOM manipulation
- PHP community for excellent documentation

---

**Built with ❤️ for automotive workshop management**

### 🔄 **Version History**

- **v1.0.0** - Initial release with basic workshop management
- **v2.0.0** - Added role-based authentication system
- **v2.1.0** - Enhanced user management and security features
- **v2.2.0** - Added animated landing page and improved UI

---

*Happy Workshop Management! 🚗🔧*