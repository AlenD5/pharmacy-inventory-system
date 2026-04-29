/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Classes/Class.java to edit this template
 */
package pharmacy_inventory_management_system;

import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;

import static pharmacy_inventory_management_system.Pharmacy_Inventory_Management_System.lowStockQueue;

/**
 *
 * @author Brandon Reagan
 */
public class loadLowStockQueue {
      // LOAD ONLY LOW STOCK → QUEUE
    public static void loadLowStockQueue(Connection conn) throws Exception {
        
            // ================= SQL QUERY =================
            
        // SQL query to get ONLY medications with low stock (quantity < 20)
        // Uses JOIN to combine medication details with inventory data
        String sql = "SELECT m.medication_name, m.category, i.quantity, i.expiration_date, i.location " +
                     "FROM medications m JOIN inventory i ON m.medication_id = i.medication_id " +
                     "WHERE i.quantity < 20";
        
         // Prepare SQL statement
        PreparedStatement stmt = conn.prepareStatement(sql);
        
        // Execute query and store results
        ResultSet rs = stmt.executeQuery();

         // Clear existing queue to avoid duplicates or outdated data
        lowStockQueue.clear();
        
        
         // ================= PROCESS RESULT SET =================
         
        // Loop through each row returned from the databas
        while (rs.next()) {
            
            
            Medication m = new Medication(
                rs.getString("medication_name"),
                rs.getString("category"),
                rs.getInt("quantity"),
                rs.getDate("expiration_date"),
                rs.getString("location")
            );
            
            // Add medication to Queue (FIFO structure)
            // Items are added in the order they are retrieved
            lowStockQueue.add(m);
        }
    }
}

