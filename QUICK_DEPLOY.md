# 🚀 QUICK DEPLOYMENT GUIDE 

## For Your Other Computers

### Step 1: Clone the Repository
```bash
git clone https://github.com/vince0526/enterprise-console.git
cd enterprise-console
```

### Step 2: Run Setup Script

**Windows:**
```cmd
setup.bat
```

**Linux/Mac:**
```bash
chmod +x setup.sh
./setup.sh
```

### Step 3: Configure Environment
Edit `.env` file and set your database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1  
DB_DATABASE=enterprise_console
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Optional: Enable dev auto-login for testing
DEV_AUTO_LOGIN=true
```

### Step 4: Start the Application
```bash
php artisan serve
```

### Step 5: Access Your EMC
Open browser to: **http://localhost:8000**

---

**That's it!** Your Enterprise Management Console will be running with all features:
- ✅ Database Management Module (5 submodules)
- ✅ User Authentication  
- ✅ Responsive Dashboard
- ✅ All 370+ files synchronized

**Need help?** Check `SETUP_INSTRUCTIONS.md` for detailed troubleshooting.