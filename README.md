# Pharmacy Inventory Management System

## Overview
A full-stack, web-based application for managing pharmacy medication inventory in real time. Built as a Computer Science senior project, Spring 2026.

## Features
- CRUD operations for medications
- Real-time inventory tracking with low-stock alerts
- Expiration date monitoring
- Transaction history and audit trail
- Search and sorting functionality
- Role-based user access control

## Tech Stack
- **Frontend:** HTML, CSS, JavaScript
- **Backend:** Java (Apache Tomcat)
- **Database:** MySQL
- **Server:** Apache HTTP Server
- **Version Control:** Git / GitHub
- **Project Management:** Jira

## Database Design
The system includes 5 main tables: Users, Suppliers, Medications, Inventory, and Transactions

**Relationships**
- Suppliers → Medications (1:M)
- Medications → Inventory (1:M)
- Inventory → Transactions (1:M)
- Users → Transactions (1:M)

## Authors
Alen Dizdaric · Brandon Reagen · Ensar Fejzic
