# Pharmacy Inventory Management System

## Overview
This project is a database-driven system designed to manage pharmacy inventory, track medications, and monitor stock levels.

## Features
- CRUD operations for medications
- Inventory tracking
- Low-stock alerts
- Expiration monitoring
- Transaction history
- Search and sorting functionality

## Database Design
The system includes 5 main tables:
- Users
- Suppliers
- Medications
- Inventory
- Transactions

## Relationships
- Suppliers → Medications (1:M)
- Medications → Inventory (1:M)
- Inventory → Transactions (1:M)
- Users → Transactions (1:M)

## Technologies
- MySQL
- Java (Backend)
- Apache Tomcat

## Author
Alen Dizdaric
