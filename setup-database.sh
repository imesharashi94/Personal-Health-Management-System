#!/bin/bash

# PHMS Database Setup Script
# This script sets up the database with the new ER diagram structure

echo "🚀 Setting up PHMS database with new ER diagram structure..."

# Run fresh migration (drops all tables and recreates them)
echo "📦 Running fresh migration..."
php artisan migrate:fresh

# Seed the database with demo data
echo "🌱 Seeding database with demo data..."
php artisan db:seed

echo "✅ Database setup complete!"
echo ""
echo "📊 Demo users created:"
echo "   Admin: admin@phms.test / password"
echo "   User:  demo@phms.test / password"
echo ""
echo "🎯 New schema features:"
echo "   • Unified observations table (health metrics + lab results)"
echo "   • Session tracking"
echo "   • Report tagging system"
echo "   • Medical codes"
echo "   • Enhanced relationships"
echo ""
echo "🚀 All migrations completed successfully!"
echo "Ready to go! 🎉"
