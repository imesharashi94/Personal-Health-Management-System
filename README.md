# 🩺 Personal Health Management System (PHMS)

## 📌 Project Overview
The **Personal Health Management System (PHMS)** is a vendor-independent digital health assistant that collects, organizes, and analyzes health-related data from multiple sources. It empowers individuals to monitor their well-being in one centralized dashboard.

### 🎯 Problem
Currently, health data is fragmented:
- Fitness data stays inside apps like Samsung Health.
- Medical reports exist only as PDFs or paper copies.
- Medication schedules are hard to follow.
- Users cannot see a holistic view of their health.

### 💡 Our Solution
PHMS addresses these challenges by:
1. **Integrating fitness tracker data** (via Google Health Connect API).
2. **Using OCR (AI-based text recognition)** to extract key values from uploaded medical reports.
3. **Allowing users to log medications & symptoms** with reminders.
4. **Displaying health data in visual dashboards** (charts & graphs).
5. **Providing alerts & insights** when anomalies or trends are detected.

---

## 🛠️ Key Technologies
- **Laravel (Backend)** → Handles authentication, APIs, and database logic.
- **React + Inertia.js (Frontend)** → Provides an interactive dashboard and forms.
- **MySQL (Database)** → Stores structured health data.
- **Google Health Connect API** → Collects fitness data from Android devices & wearables.
- **OCR (Optical Character Recognition)** → Reads and extracts medical report details.

---

## 👩‍⚕️ Example User Journey
Meet **Sarah (35 years old):**
1. Registers on the platform.
2. Connects her Samsung Galaxy Watch via Google Health Connect.
3. Uploads her recent blood test report (PDF).
4. Logs daily symptoms like headaches & medications.
5. Dashboard shows:
   - Heart rate trends
   - Cholesterol improvements
   - Sleep–headache correlation
6. Receives an **alert** when her heart rate exceeds her normal range.

---

## 🚀 Installation & Setup (For Developers)

### 1. Clone the Repository
```bash
git clone https://github.com/your-username/phms.git
cd phms
```

### 2. Backend Setup (Laravel)
```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

### 3. Frontend Setup (React + Inertia + Vite)
```bash
npm install
npm run dev
```
Access app at: http://127.0.0.1:8000

## 📊 Features Roadmap
- User authentication (Laravel Breeze + Inertia)

- Connect Google Health Connect API

- OCR integration for medical report uploads

- Symptom & medication tracking

- Interactive health dashboards

- Rule-based alerts & notifications

## 🤝 Contributing
Pull requests are welcome! Please fork the repo and submit a PR.

## 📜 License
This project is licensed under the [MIT License](License.txt).

## 👨‍💻 Authors

Imesha Rashini Samaranayake – Project Developer

[MIT Final Project 2025]
