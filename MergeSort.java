/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Classes/Class.java to edit this template
 */
package pharmacy_inventory_management_system;

/**
 *
 * @author Brandon Reagan
 */
public class mergeSort(List<Medication> list) {
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
}
