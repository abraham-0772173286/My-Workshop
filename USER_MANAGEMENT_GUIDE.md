# User Lock/Unlock System Guide

## 🔐 Overview
The workshop management system includes a comprehensive user lock/unlock system designed to maintain security while providing flexible access control for administrators.

## 🎯 Key Features

### **Automatic Security Features:**
- **Auto-Lock**: Users are automatically locked after 5 failed login attempts
- **Time-Based Unlock**: Temporary locks automatically expire after the specified duration
- **Session Management**: Secure session handling with regeneration
- **Login Attempt Tracking**: Complete audit trail of all login attempts

### **Manual Admin Controls:**
- **Manual Lock/Unlock**: Admins can manually lock/unlock any user account
- **Password Reset**: Admins can reset user passwords
- **User Creation**: Create new users with specified roles
- **Activity Monitoring**: View all user activities and security events

## 🚀 How to Use

### **Accessing User Management:**
1. Log in as an **Admin** user
2. Navigate to **User Management** in the sidebar
3. View all users, their status, and recent activity

### **Locking a User:**
1. Click the **Lock** button (🔒) next to the user
2. Select a reason for locking:
   - Security violation
   - Policy breach  
   - Suspicious activity
   - Administrative action
   - Custom reason
3. Choose lock duration:
   - **Permanent**: Until manually unlocked
   - **15 minutes**: Short timeout
   - **1 hour**: Standard timeout
   - **8 hours**: Extended timeout
   - **24 hours**: Daily timeout
   - **1 week**: Long-term timeout
4. Click **Lock User** to confirm

### **Unlocking a User:**
1. Click the **Unlock** button (🔓) next to a locked user
2. Confirm the action when prompted
3. The user will be immediately unlocked and can log in again

### **Resetting Passwords:**
1. Click the **Key** button (🔑) next to any user
2. Enter the new password (minimum 8 characters)
3. Confirm the password
4. Click **Reset Password** to apply

### **Creating New Users:**
1. Click **Add User** button
2. Fill in all required information:
   - Username (letters, numbers, underscores only)
   - Full Name
   - Role (Admin/Owner/Cashier)
   - Password (minimum 8 characters)
3. Click **Create User** to add them to the system

## 🔧 Security Features

### **Automatic Protections:**
- **Failed Attempt Limit**: 5 attempts before auto-lock
- **Lock Duration**: 15 minutes for auto-locks
- **Password Requirements**: Minimum 8 characters
- **Session Security**: Secure session regeneration
- **Admin Protection**: Admins cannot lock themselves

### **Audit Trail:**
- All login attempts are logged
- User actions are tracked with timestamps
- Administrative actions are recorded
- Security events are monitored in real-time

### **Time-Based Features:**
- **Auto-Unlock**: Temporary locks expire automatically
- **Grace Period**: Users can retry after lock expires
- **Reset Counters**: Failed attempts reset after successful login

## 🛠️ Administrative Tools

### **Security Monitor:**
Access via **Security Monitor** in the admin sidebar to view:
- Real-time security metrics
- Recent security events
- Currently locked users
- Failed login attempt statistics

### **Quick Actions:**
- **Unlock Expired Users**: Manually clear all expired locks
- **Reset Failed Attempts**: Clear all failed attempt counters
- **System Health Check**: View overall system security status

## 📊 User Roles & Permissions

### **Admin**
- Full system access
- Can manage all users
- Can lock/unlock any user
- Access to security monitoring
- Cannot lock their own account

### **Owner**
- Business oversight access
- Cannot manage users
- Cannot access admin features
- Can view business data

### **Cashier**
- Daily operations access
- Limited to transactions
- Cannot access admin areas
- Standard user privileges

## 🔍 Troubleshooting

### **User Cannot Login:**
1. Check if user is locked in User Management
2. Verify lock expiration time
3. Check failed attempt count
4. Manually unlock if needed

### **Auto-Lock Not Working:**
1. Verify database connection
2. Check login attempt tracking
3. Ensure proper session management

### **Password Reset Issues:**
1. Confirm admin privileges
2. Verify password meets requirements
3. Check database updates

## 🚨 Emergency Procedures

### **Admin Locked Out:**
If the main admin account gets locked:
1. Access the database directly
2. Update the `users` table to unlock the admin
3. Reset the `status` to 'active'
4. Clear `locked_until` and `failed_attempts`

### **Database Access:**
```sql
UPDATE users 
SET status = 'active', locked_until = NULL, failed_attempts = 0 
WHERE username = 'admin';
```

## 🔧 Configuration

### **Database Setup:**
Run `/workshop/setup_database.php` to:
- Create the users table
- Set up default accounts
- Initialize security features

### **Default Accounts:**
- **Admin**: username `admin`, password `2212Aa@0`
- **Owner**: username `owner`, password `2212Aa@0`  
- **Cashier**: username `cashier`, password `2212Aa@0`

## 📈 Best Practices

### **Security:**
- Change default passwords immediately
- Use strong passwords (8+ characters)
- Monitor failed login attempts regularly
- Lock accounts proactively if needed
- Review security logs periodically

### **User Management:**
- Create role-specific accounts
- Use descriptive full names
- Lock unused accounts
- Reset passwords when staff changes
- Document lock/unlock reasons

### **Monitoring:**
- Check Security Monitor daily
- Review locked users weekly
- Export security logs monthly
- Maintain admin account hygiene

## 📞 Support

For technical support or questions about the user management system:
- Check the Security Monitor for system status
- Review audit logs for user activities
- Contact the system administrator
- Refer to this guide for common procedures

---

**Remember**: This system is designed to balance security with usability. Use the lock/unlock features responsibly to maintain both system security and user productivity.