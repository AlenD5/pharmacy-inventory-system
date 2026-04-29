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
public class loadInventoryMap {
    
     public static void loadInventoryMap(Connection conn) throws Exception {
        
          // ================= SQL QUERY =================

         // SQL query to retrieve all medication data by joining two tables:
         // medications (name, category) and inventory (quantity, expiration, location)

        String sql = "SELECT m.medication_name, m.category, i.quantity, i.expiration_date, i.location " +
                     "FROM medications m JOIN inventory i ON m.medication_id = i.medication_id";
        
        // Prepare SQL statement
        java.sql.PreparedStatement stmt = conn.prepareStatement(sql);
        
                // Execute query and store results
        ResultSet rs = stmt.executeQuery();
        
        // Clear existing HashMap to avoid duplicate or outdated data
        inventoryMap.clear();
        
        // ================= PROCESS RESULT SET =================
        
        // Loop through each row returned from the database
        while (rs.next()) {
            
            Medication m = new Medication(
                rs.getString("medication_name"), // get medication name
                rs.getString("category"),        // get category
                rs.getInt("quantity"),            // get quantity
                rs.getDate("expiration_date"),   // get expiration date
                rs.getString("location")        // get location
            );
            
            // Store the medication in HashMap
            // Key = lowercase name (for consistent searching)
            // Value = Medication object
            inventoryMap.put(m.name.toLowerCase(), m);
        }
    }
}
