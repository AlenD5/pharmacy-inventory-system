/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Classes/Class.java to edit this template
 */
package pharmacy_inventory_management_system;

import java.util.ArrayList;
import java.util.List;

/**
 *
 * @author Brandon Reagan
 */
public class MergeSort {
    public static void mergeSort(List<Medication> list) {
        
        // Base case: if list has 0 or 1 element, it is already sorted
        if (list.size() <= 1) return;
        
          // Find the middle index of the list
        int mid = list.size() / 2;
        
          // Split the list into left half (0 → mid)
        List<Medication> left = new ArrayList<>(list.subList(0, mid));
        
          // Split the list into right half (mid → end)
        List<Medication> right = new ArrayList<>(list.subList(mid, list.size()));
        
        // Recursively sort the left half
        mergeSort(left);
        
        
        // Recursively sort the right half
        mergeSort(right);
        
        // Merge the two sorted halves back together
        merge(list, left, right);
    }

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
