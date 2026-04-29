/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Classes/Class.java to edit this template
 */
package pharmacy_inventory_management_system;

import java.sql.Connection;
import java.sql.ResultSet;
import static pharmacy_inventory_management_system.Pharmacy_Inventory_Management_System.inventoryMap;

/**
 *
 * @author Brandon Reagan
 */
 class DatabaseConnection {

            public static Connection getConnection() {
            
             // Create a Connection object (initially null)
            Connection conn = null;

            try {
                
                // Database URL (location of MySQL database)
                String url = "jdbc:mysql://localhost:3306/pharmacyinventorymanagement";

                // MySQL username
                String user = "root";

                // MySQL password
                String password = "Database388";
                
            // ================= ESTABLISH CONNECTION =================
            
               // Attempt to connect to the database using DriverManager
                conn = DriverManager.getConnection(url, user, password);
                
                // Print confirmation if connection is successful
                System.out.println("Connected to MySQL successfully!");

            }
            catch (SQLException e) {
                
                 // If connection fails, print error message
                System.out.println("Connection failed!");
                
                // Print detailed error information for debugging
                e.printStackTrace();
            }
            
            // Return the connection object (either valid or null if failed)
            return conn;
            }
    }
