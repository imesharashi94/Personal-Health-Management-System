# PHMS Database Migration Summary

## 🎯 Overview
This document summarizes the complete database schema migration from the old structure to the new ER diagram-based structure for the Personal Health Management System (PHMS).

## 📋 Migration Strategy
Since this is the project initialization phase, we've implemented a **fresh migration approach** using `migrate:fresh` and `db:seed` instead of complex data migration scripts.

## 🗄️ New Database Structure

### Core Tables
- **USERS** - Enhanced with `preferred_language` and `preferences` fields
- **REPORTS** - Renamed from `lab_reports`, added `report_type`, `metadata`, `session_id`
- **OBSERVATIONS** - New unified table replacing `health_metrics` and `lab_results`
- **ALERTS** - Enhanced with `observation_id` foreign key
- **EXPORTS** - Renamed from `export_jobs`

### New Tables
- **SESSIONS** - User session tracking
- **BUNDLES** - Data bundles for FHIR compliance
- **TRENDS** - Calculated trend analysis
- **OCR_BLOCKS** - OCR processing blocks
- **CODES** - Medical coding system (LOINC, etc.)
- **TAGS** - Report tagging system
- **REPORT_TAG** - Many-to-many relationship table

## 🔄 Key Changes

### 1. Data Consolidation
- **Before**: Separate `health_metrics` and `lab_results` tables
- **After**: Unified `observations` table with `observation_type` field
- **Benefits**: Simplified queries, consistent data structure

### 2. Enhanced Relationships
- **Sessions**: Track user interactions and data entry sessions
- **Tags**: Many-to-many relationship between reports and tags
- **Observations**: Direct relationship to alerts via `observation_id`

### 3. New Features
- **Session Tracking**: Monitor user engagement and data entry patterns
- **Report Tagging**: Categorize and organize reports
- **Medical Codes**: Standardized medical terminology
- **Trend Analysis**: Dedicated table for calculated trends

## 📁 Migration Files Created

### New Table Migrations
1. `create_sessions_table.php`
2. `create_bundles_table.php`
3. `create_observations_table.php`
4. `create_trends_table.php`
5. `create_ocr_blocks_table.php`
6. `create_codes_table.php`
7. `create_tags_table.php`
8. `create_report_tag_table.php`

### Update Migrations
1. `update_users_table_for_new_schema.php`
2. `update_lab_reports_to_reports_table.php`
3. `update_export_jobs_to_exports_table.php`
4. `update_alerts_table_for_new_schema.php`
5. `update_symptoms_and_medications_for_sessions.php`
6. `rename_tables_and_remove_old_ones.php`

## 🚀 Setup Instructions

### Quick Setup
```bash
# Run the setup script
./setup-database.sh
```

### Manual Setup
```bash
# Fresh migration (drops all tables and recreates)
php artisan migrate:fresh

# Seed with demo data
php artisan db:seed
```

## 🎭 Demo Data
The seeder creates:
- **Admin User**: `admin@phms.test` / `password`
- **Demo User**: `demo@phms.test` / `password`
- **90 days** of health metrics (steps, HR, sleep)
- **3 lab reports** with observations
- **Tags and codes** for categorization
- **Sessions** for user interaction tracking

## 🔧 Updated Application Code

### Models Updated
- ✅ **User** - Added new relationships and fields
- ✅ **Report** - Renamed from LabReport, added new relationships
- ✅ **Observation** - New unified model
- ✅ **Alert** - Added observation relationship
- ✅ **Export** - Renamed from ExportJob
- ✅ **New Models**: Session, Bundle, Trend, OcrBlock, Code, Tag

### Controllers Updated
- ✅ **LabController** - Uses Report model and observations
- ✅ **ExportController** - Updated for new observation structure
- ✅ **DashboardController** - Updated for new relationships

### Services Updated
- ✅ **TrendService** - Uses Observation model
- ✅ **AlertService** - Updated for new observation structure

## 🎯 Benefits of New Structure

1. **Unified Data Model**: All health data in one `observations` table
2. **Enhanced Tracking**: Session-based user interaction monitoring
3. **Better Organization**: Tag-based report categorization
4. **Standardized Coding**: Medical terminology compliance
5. **Improved Analytics**: Dedicated trends table for calculations
6. **Scalable Architecture**: Clean separation of concerns

## 🔍 Testing the Migration

After running the setup:

1. **Login** with demo credentials
2. **Check Dashboard** - Should show health metrics and trends
3. **View Reports** - Should display lab reports with observations
4. **Export Data** - Should work with new observation structure
5. **Alerts** - Should be linked to specific observations

## 📊 Database Schema Diagram

```
USERS (1) ──→ (M) SESSIONS
USERS (1) ──→ (M) OBSERVATIONS
USERS (1) ──→ (M) REPORTS
USERS (1) ──→ (M) ALERTS
USERS (1) ──→ (M) EXPORTS
USERS (1) ──→ (M) BUNDLES
USERS (1) ──→ (M) TRENDS

REPORTS (1) ──→ (M) OBSERVATIONS
REPORTS (1) ──→ (M) OCR_BLOCKS
REPORTS (M) ──→ (M) TAGS (via REPORT_TAG)

OBSERVATIONS (1) ──→ (M) ALERTS
SESSIONS (1) ──→ (M) SYMPTOMS
SESSIONS (1) ──→ (M) MEDICATIONS
```

## ✅ Migration Complete!

The database has been successfully migrated to the new ER diagram structure. All existing functionality is preserved while gaining the benefits of the enhanced schema design.
