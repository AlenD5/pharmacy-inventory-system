Pharmacy Inventory Management System
A full-stack, web-based application for managing pharmacy medication inventory in real time. Built as a Computer Science senior project at the University of Iowa, Spring 2026.
Features

CRUD operations for medications
Real-time inventory tracking with low-stock alerts
Expiration date monitoring
Transaction history and audit trail
Search and sorting functionality
Role-based user access control

Tech Stack

Frontend: HTML, CSS, JavaScript
Backend: Java (Apache Tomcat)
Database: MySQL
Server: Apache HTTP Server
Version Control: Git / GitHub
Project Management: Jira

Database Design
5 core tables: Users, Suppliers, Medications, Inventory, Transactions
Relationships:

Suppliers → Medications (1:M)
Medications → Inventory (1:M)
Inventory → Transactions (1:M)
Users → Transactions (1:M)

Authors
Alen Dizdaric · Brandon Reagen · Ensar Fejzic
