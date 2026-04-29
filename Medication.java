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
public class Medication {
    
     // Name of the medication (e.g., Ibuprofen 200mg)
          // Category of medication (e.g., Antibiotic, Pain Relief)
         // Storage location (e.g., Shelf A1)
        String name, category, location;
        
        // Quantity currently in stock
        int quantity;
        
        
        // Expiration date of the medication
        Date expirationDate;
        
         // ================= CONSTRUCTOR =================
        
        // Constructor initializes all fields when a Medication object is created
        public Medication(String name, String category, int quantity, Date expirationDate, String location) {
            
            // Assign parameter values to class variables
            this.name = name;
            this.category = category;
            this.quantity = quantity;
            this.expirationDate = expirationDate;
            this.location = location;
        }
        
        // ================= GETTER METHOD =================
        
        // Returns the expiration date (used for sorting)
        public Date getExpirationDate() {
            return expirationDate;
        }
        
         // ================= DISPLAY METHOD =================
        
        // Converts Medication object into readable string format
        @Override
        public String toString() {
            
            // Return formatted string with all medication details
            return name + " | " + category + " | Qty: " + quantity +
                " | Exp: " + expirationDate + " | Loc: " + location;
        }
    }
}
