# 🩺 Personal Health Management System (PHMS)

A comprehensive Laravel 10 + React + Inertia.js application for tracking and managing personal health data with OCR-powered lab report parsing, health metrics import, and intelligent alerting.

## 📌 Project Overview

The **Personal Health Management System (PHMS)** is a vendor-independent digital health assistant that collects, organizes, and analyzes health-related data from multiple sources. It empowers individuals to monitor their well-being in one centralized dashboard.

### 🎯 Problem
Currently, health data is fragmented:
- Fitness data stays inside apps like Samsung Health, Google Fit
- Medical reports exist only as PDFs or paper copies
- Medication schedules are hard to follow
- Users cannot see a holistic view of their health

### 💡 Our Solution
PHMS addresses these challenges by:
1. **Importing fitness tracker data** via CSV/JSON exports (Google Fit, Health Connect)
2. **Using OCR (Tesseract)** to extract key values from uploaded medical reports (PDF/JPG)
3. **Allowing users to log medications & symptoms** with schedules
4. **Displaying health data in visual dashboards** with interactive charts
5. **Providing rule-based alerts** when anomalies or trends are detected
6. **Exporting health data** for backup or external analysis

---

## 🛠️ Technology Stack

### Backend
- **Laravel 10** - PHP Framework
- **MySQL** - Database
- **Laravel Breeze** - Authentication scaffolding (Inertia + React)
- **Inertia.js** - Server-driven SPAs
- **Tesseract OCR** - Lab report text extraction
- **Queue System** - Background job processing (database driver)

### Frontend
- **React 18** - UI Library
- **Vite** - Build tool
- **Recharts** - Data visualization
- **Headless UI** - Accessible components
- **Heroicons** - Icon set
- **React Dropzone** - File upload

---

## 🚀 Installation & Setup

### Prerequisites
- PHP >= 8.1
- Composer
- Node.js >= 18 & npm
- MySQL >= 8.0
- Tesseract OCR (`sudo apt install tesseract-ocr` on Linux)

### 1. Clone the Repository
```bash
git clone https://github.com/imesharashi94/Personal-Health-Management-System.git
cd Personal-Health-Management-System
```

### 2. Install Dependencies
```bash
# Backend dependencies
composer install

# Frontend dependencies
npm install
```

### 3. Environment Configuration
```bash
cp .env.example .env
```

Update `.env` with your configuration:
```env
APP_NAME=PHMS
APP_TIMEZONE=Asia/Colombo

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=phms
DB_USERNAME=root
DB_PASSWORD=

QUEUE_CONNECTION=database
FILESYSTEM_DISK=local
OCR_DRIVER=tesseract
CHART_TIMEZONE=Asia/Colombo
```

### 4. Generate Application Key
```bash
php artisan key:generate
```

### 5. Run Migrations & Seed Database
```bash
# Create database tables
php artisan migrate

# Create queue jobs table
php artisan queue:table
php artisan migrate

# Seed with demo data (creates admin and demo user)
php artisan db:seed
```

**Demo Credentials:**
- Admin: `admin@phms.test` / `password`
- User: `demo@phms.test` / `password`

### 6. Build Frontend Assets
```bash
# Development
npm run dev

# Production
npm run build
```

### 7. Start Services
```bash
# Terminal 1 - Web server
php artisan serve

# Terminal 2 - Queue worker (processes OCR & import jobs)
php artisan queue:work

# Terminal 3 - Scheduler (for daily alerts)
php artisan schedule:work
```

Access the application at: **http://127.0.0.1:8000**

---

## 📊 Features

### ✅ Implemented (MVP)

#### Authentication & Authorization
- User registration & login (Laravel Breeze)
- Email verification required
- Role-based access control (user/admin)
- Policy-based authorization

#### Dashboard
- KPI tiles: Average steps, resting HR, sleep (7d/30d/90d)
- Interactive charts: Steps (bar), HR (line), Sleep (area)
- Recent unresolved alerts
- Latest lab report summary

#### Health Metrics Import
- CSV/JSON file upload
- Background job processing
- Supports: steps, heart rate (hr), sleep
- Data source tracking

#### Lab Reports (OCR)
- Upload PDF/JPG/PNG files (max 10MB)
- Tesseract OCR extraction
- Parse analytes: LDL, Hemoglobin, Glucose, Cholesterol, HDL, Triglycerides
- Automatic flagging based on reference ranges
- Timeline view with status tracking

#### Symptoms & Medications
- Log symptoms with severity (1-10) and notes
- Add medications with dose and unit
- Create schedules (daily/weekly/custom) with specific times
- Active/paused schedule management

#### Alerts & Rules Engine
- **Rule 1:** LDL > 160 mg/dL → Warning alert
- **Rule 2:** Resting HR > baseline + 20 for 3 consecutive days → HR alert
- **Rule 3:** Sleep < 5 hours + severe symptom (≥6) → Self-care tip
- Daily evaluation job (runs at 1:30 AM)
- Email & database notifications

#### Data Export
- Date range selection
- Choose data types: steps, hr, sleep, labs, symptoms, medications
- CSV format download
- Export history with status tracking

#### Settings
- View profile information
- Manage consents & privacy
- Timezone configuration

#### Admin Panel
- User management with search
- System KPIs (total users, active users, total records, alerts)
- Alert threshold configuration view
- Audit log tracking (file views, actions)

---

## 📁 Project Structure

```
app/
├── Console/
│   └── Kernel.php                 # Scheduled tasks
├── Http/
│   ├── Controllers/
│   │   ├── DashboardController.php
│   │   ├── ImportController.php
│   │   ├── LabController.php
│   │   ├── SymptomController.php
│   │   ├── MedicationController.php
│   │   ├── ExportController.php
│   │   ├── SettingsController.php
│   │   └── Admin/
│   │       ├── UserController.php
│   │       ├── ReportController.php
│   │       └── AuditController.php
│   ├── Requests/               # Form validation
│   └── Middleware/
├── Jobs/
│   ├── ParseLabReportJob.php     # OCR processing
│   ├── ImportMetricsJob.php      # CSV import
│   └── EvaluateAlertsJob.php     # Daily alerts
├── Models/
│   ├── User.php
│   ├── HealthMetric.php
│   ├── LabReport.php
│   ├── LabResult.php
│   ├── Symptom.php
│   ├── Medication.php
│   ├── Alert.php
│   ├── Consent.php
│   ├── ExportJob.php
│   └── AuditLog.php
├── Notifications/
│   ├── AlertNotification.php
│   └── ExportReadyNotification.php
├── Policies/                   # Authorization policies
└── Services/
    ├── OcrService.php          # Tesseract wrapper
    ├── ImportService.php       # CSV/JSON parsing
    ├── TrendService.php        # Analytics & baselines
    └── AlertService.php        # Rules evaluation

resources/js/
├── Components/
│   ├── KpiTile.jsx
│   ├── ChartCard.jsx
│   ├── DataTable.jsx
│   ├── FileDropzone.jsx
│   ├── DateRangePicker.jsx
│   └── Toast.jsx
└── Pages/
    ├── Dashboard.jsx
    ├── Labs/
    │   ├── Index.jsx
    │   └── Show.jsx
    ├── Symptoms/Index.jsx
    ├── Medications/Index.jsx
    ├── Import/Index.jsx
    ├── Export/Index.jsx
    ├── Settings/Index.jsx
    └── Admin/
        ├── Users.jsx
        ├── Reports.jsx
        └── Audit.jsx

database/
├── migrations/                 # All database tables
├── factories/                  # Model factories for testing
└── seeders/
    └── DatabaseSeeder.php      # Demo data seeder
```

---

## 🧪 Demo Script

### 1. Login & Dashboard
1. Navigate to http://127.0.0.1:8000
2. Login as demo user: `demo@phms.test` / `password`
3. View dashboard with seeded 90 days of health metrics
4. Observe KPI tiles and charts with pre-populated data

### 2. Import Health Metrics (CSV)
1. Navigate to **Import** page
2. Create a sample CSV file (`health_data.csv`):
```csv
date,metric,value,unit
2025-10-21,steps,10500,steps
2025-10-21,hr,72,bpm
2025-10-21,sleep,7.8,hours
2025-10-20,steps,8200,steps
2025-10-20,hr,68,bpm
2025-10-20,sleep,6.5,hours
```
3. Upload the file
4. Check terminal running `php artisan queue:work` to see job processing
5. Return to Dashboard - charts now include new data

### 3. Upload Lab Report (OCR)
1. Navigate to **Labs** page
2. Click "Upload Report"
3. Create or upload a sample lab report PDF/image with text like:
```
Medical Laboratory Report
Date: 2025-10-15

Lipid Profile:
LDL Cholesterol: 165 mg/dL
Hemoglobin: 13.5 g/dL
```
4. Fill in report date and facility
5. Click "Upload & Parse"
6. Watch queue worker process OCR extraction
7. View parsed results with flagged LDL value

### 4. Log Symptoms & Medications
1. Navigate to **Symptoms** page
2. Click "Log Symptom"
3. Add: "Headache", severity 7, with notes
4. Navigate to **Medications** page
5. Add: "Aspirin", 100 mg, notes "Take with food"
6. View medication in list

### 5. Trigger an Alert
1. The seeded data may already have triggered alerts
2. Dashboard shows active alerts
3. Check email (if configured) or database notifications table:
```bash
php artisan tinker
>>> \App\Models\Alert::latest()->first()
```

### 6. Export Health Data
1. Navigate to **Export** page
2. Select date range: Last 30 days
3. Check metric types: steps, hr, sleep, labs
4. Click "Generate Export"
5. Download CSV file when status is "done"

### 7. Admin Panel (as admin user)
1. Logout and login as: `admin@phms.test` / `password`
2. Navigate to **Admin > Users** - see all users
3. Navigate to **Admin > Reports** - view system KPIs
4. Navigate to **Admin > Audit** - track system actions

---

## 🧪 Testing

### Run Tests
```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit
```

### Test Coverage
Basic test files are included for:
- OcrService parsing
- ImportService row mapping
- HTTP endpoints (import, lab upload)
- Component rendering (future)

---

## 📝 Environment Variables Reference

| Variable | Description | Default |
|----------|-------------|---------|
| `APP_NAME` | Application name | PHMS |
| `APP_TIMEZONE` | Application timezone | Asia/Colombo |
| `DB_DATABASE` | MySQL database name | phms |
| `QUEUE_CONNECTION` | Queue driver | database |
| `FILESYSTEM_DISK` | Storage driver | local |
| `OCR_DRIVER` | OCR engine | tesseract |
| `CHART_TIMEZONE` | Chart display timezone | Asia/Colombo |

---

## 🔧 Troubleshooting

### OCR Not Working
```bash
# Check Tesseract installation
tesseract --version

# Install on Ubuntu/Debian
sudo apt install tesseract-ocr

# Install on macOS
brew install tesseract
```

### Queue Jobs Not Processing
```bash
# Ensure queue worker is running
php artisan queue:work

# Check failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all
```

### Charts Not Displaying
```bash
# Rebuild frontend assets
npm run build

# Check browser console for errors
# Ensure Recharts is installed
npm install recharts
```

---

## 🛣️ Future Roadmap

- [ ] Real-time Google Health Connect API integration
- [ ] Advanced OCR with AI/ML model training
- [ ] Mobile app (React Native)
- [ ] Wearable device direct integration
- [ ] Doctor/clinic portal for sharing reports
- [ ] Advanced analytics & predictive insights
- [ ] Multi-language support
- [ ] HIPAA compliance features
- [ ] End-to-end encryption for sensitive data

---

## 🤝 Contributing

Pull requests are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

---

## 📜 License

This project is licensed under the [MIT License](License.txt).

---

## 👨‍💻 Authors

**Imesha Rashini Samaranayake** – Project Developer

[MIT Final Project 2025]

---

## 🙏 Acknowledgments

- Laravel & Breeze documentation
- Tesseract OCR library
- React & Recharts communities
- Health data privacy best practices
