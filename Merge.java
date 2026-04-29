/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Classes/Class.java to edit this template
 */
package pharmacy_inventory_management_system;

import java.util.List;

/**
 *
 * @author Brandon Reagan
 */
public class Merge {
     public static void merge(List<Medication> list, List<Medication> left, List<Medication> right) {
        
         // i = index for left list
        // j = index for right list
        // k = index for main list
        int i = 0, j = 0, k = 0;
        
        // Compare elements from left and right lists
        while (i < left.size() && j < right.size()) {
            
             // Compare expiration dates
            // If left date is earlier, place it first
            if (left.get(i).getExpirationDate().before(right.get(j).getExpirationDate())) {
                
                // Add left element into main list
                list.set(k++, left.get(i++));
                
            } 
            
            else {
                
                 // Otherwise, add right element
                list.set(k++, right.get(j++));
            }
        }
         // Add remaining elements from left list (if any)
        while (i < left.size()) {
            list.set(k++, left.get(i++));
        }
        
        // Add remaining elements from right list (if any)
        while (j < right.size()) {
            list.set(k++, right.get(j++));
        }

    }
}
