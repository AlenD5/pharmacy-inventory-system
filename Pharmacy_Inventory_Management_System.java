/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Classes/Main.java to edit this template
 */
package pharmacy_inventory_management_system;

/**
 *
 * @author Brandon Reagan
 */

       


import java.util.ArrayList;
import java.util.Date;
import java.sql.Connection;
import java.util.LinkedList;
import java.util.List;
import java.util.Scanner;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.Statement;
import java.sql.DriverManager;
import java.sql.SQLException;
import java.util.HashMap;
import java.util.Queue;


public class Pharmacy_Inventory_Management_System {

    /**
     * @param args the command line arguments
     */
    
    static LinkedList<Medication> medicationList = new LinkedList<>();
    static HashMap<String, Medication> inventoryMap = new HashMap<>();
    static Queue<Medication> lowStockQueue = new LinkedList<>();
    
    
    public static void main(String[] args) {
        
            try {
            //  Connect to MySQL database
            Connection conn = DatabaseConnection.getConnection();

            // Check if connection failed
            if (conn == null) {
                System.out.println("Database connection failed. Exiting...");
                return;
            }
            
             // Create Scanner to read user input
            Scanner scanner = new Scanner(System.in);

            // Load all medications into HashMap
            loadInventoryMap(conn);
            
            // Load low stock medications into Queue
            loadLowStockQueue(conn);
            
            
            // Start menu loop (runs forever until exit)
            // ================= MENU =================
            while (true) {
                
                // Display menu options
                System.out.println("\n==== PHARMACY SYSTEM ====");
                System.out.println("1. Add Medication");
                System.out.println("2. View All Medications");
                System.out.println("3. Update Medication");
                System.out.println("4. Delete Medication");
                System.out.println("5. Search Medication (HashMap)");
                System.out.println("6. Show Low Stock (<20)");
                System.out.println("7. Process Restock Queue (FIFO)");
                System.out.println("8. Exit");
                
                System.out.println();
                
                
                 // Ask user for input
                System.out.print("Enter your choice (1-8): ");
                int choice = scanner.nextInt();
                
                // Clear buffer (important after nextInt)
                scanner.nextLine();
                
                // Process user choice
                switch (choice) {

                    case 1:
                        
                        // Call method to add medication
                        addMedication(conn, scanner);
                        break;

                    case 2:
                        
                        // Reload latest data from database
                        loadInventoryMap(conn);
                        
                        // Clear old list
                        medicationList.clear();
                        
                        // Copy HashMap values into LinkedList
                        medicationList.addAll(inventoryMap.values());
                        
                        // Sort medications by expiration date
                        mergeSort(medicationList);
                        
                        
                         // Print table header
                        System.out.println("\n--- ALL MEDICATIONS (Sorted by Expiration) ---");
                        System.out.printf("%-30s %-20s %-5s %-12s %-10s\n",
                                "Name", "Category", "Qty", "Exp Date", "Location");

                        System.out.println("-----------------------------------------------------------------------");

                        int count = 0;
                        
                        // Loop through all medications and print
                        for (Medication m : medicationList) {
                            System.out.printf("%-30s %-20s %-5d %-12s %-10s\n",
                                m.name,
                                m.category,
                                m.quantity,
                                m.expirationDate,
                                m.location);
                            count++;
                        }
                        
                        // Print total count
                        System.out.println("-----------------------------------------------------------------------");
                        System.out.println("Total Medications: " + count);

                        break;

                    case 3:
                        
                        // Update medication quantity
                        updateMedication(conn, scanner);
                        break;

                    case 4:
                        
                        // Delete medication
                        deleteMedication(conn, scanner);
                        break;

                    case 5:
                        
                        // Ask user for medication name
                        System.out.print("Enter medication name: ");
                        String name = scanner.nextLine().toLowerCase();
                        
                         // Search using HashMap (fast lookup)
                        Medication found = inventoryMap.get(name);
                        
                        // Display result
                        if (found != null) {
                            System.out.println("Found: " + found);
                        } else {
                            System.out.println("Medication not found.");
                        }
                        break;

                    case 6:
                        
                        // Display low stock report
                        displayLowStockMedications();
                        break;

                    case 7:
                        
                        // Process queue (FIFO)
                        System.out.println("\nProcessing Restock Queue...");
                        
                         // Create temporary queue so original is not removed
                        Queue<Medication> tempQueue = new LinkedList<>(lowStockQueue);
                        
                        
                        // Loop until queue is empty
                        while (!tempQueue.isEmpty()) {
                            Medication m = tempQueue.poll();
                            System.out.println("Restocking: " + m.name);
                        }
                        break;

                    case 8:
                        
                          // Exit program
                        System.out.println("Exiting...");
                        conn.close();
                        return;

                    default:
                        
                        // Handle invalid input
                        System.out.println("Invalid choice!");
                }
            }

        } 
            catch (Exception e) {
                
                // Print any errors
                e.printStackTrace();
            }
    }

               
    
    public static void addMedication(Connection conn, Scanner scanner) {

        try {
            
             // Ask user to enter medication name
            System.out.print("Enter name: ");
            String name = scanner.nextLine();
            
             // Ask for medication category (e.g., Antibiotic)
            System.out.print("Enter category: ");
            String category = scanner.nextLine();
            
            // Ask for quantity in stock
            System.out.print("Enter quantity: ");
            int quantity = scanner.nextInt();
            
            // Clear scanner buffer after nextInt()
            scanner.nextLine();
            
            // Ask for expiration date (must match SQL format)
            System.out.print("Enter expiration date (YYYY-MM-DD ONLY): ");
            String expDate = scanner.nextLine();
            
             // Ask for storage location (e.g., Shelf A1)
            System.out.print("Enter location: ");
            String location = scanner.nextLine();
            
            // ================= DATE CONVERSION =================
            
             // Convert String date into SQL Date format
            java.sql.Date sqlDate;
            
            try {
                
                 // Convert user input into java.sql.Date
                sqlDate = java.sql.Date.valueOf(expDate);
            }
            
            catch (IllegalArgumentException e) {
                
                  // Handle invalid date format
                System.out.println("Invalid date format! Use YYYY-MM-DD");
                return;
            }
            
               // ================= INSERT INTO medications TABLE =================
               
            // SQL query to insert medication name and category
            String sql1 = "INSERT INTO medications (medication_name, category) VALUES (?, ?)";
            
            // Prepare statement and allow retrieval of generated ID
            PreparedStatement stmt1 = conn.prepareStatement(sql1, Statement.RETURN_GENERATED_KEYS);
            
            // Set values for query
            stmt1.setString(1, name);
            stmt1.setString(2, category);
            
            // Execute insert query
            stmt1.executeUpdate();
            
             // ================= GET  ID =================

             // Retrieve  medication_id
            ResultSet keys = stmt1.getGeneratedKeys();
            int medicationId = 0;
            
             // If ID exists, store it
            if (keys.next()) {
                medicationId = keys.getInt(1);
            }
            
            // ================= INSERT INTO inventory TABLE =================
            
              // SQL query to insert inventory details
            String sql2 = "INSERT INTO inventory (medication_id, quantity, expiration_date, location) VALUES (?, ?, ?, ?)";
            
            // Prepare statement
            PreparedStatement stmt2 = conn.prepareStatement(sql2);
            
            // Set values for inventory table
            stmt2.setInt(1, medicationId);
            stmt2.setInt(2, quantity);
            stmt2.setDate(3, sqlDate);
            stmt2.setString(4, location);
            
              // Execute insert query
            stmt2.executeUpdate();
            
            // ================= UPDATE DATA STRUCTURES =================

             // Create new Medication object
            Medication m = new Medication(name, category, quantity, sqlDate, location);
            
            // Add to LinkedList (used for sorting)
            medicationList.add(m);
            
            // Add to HashMap (used for fast searching)
            inventoryMap.put(name.toLowerCase(), m);
            
             // If quantity is low (<20), add to Queue
            if (quantity < 20) {
                lowStockQueue.add(m);
            }
            
              // Confirmation message
            System.out.println("Medication added successfully!");

        } catch (Exception e) {
            
            // Catch and display any errors
            e.printStackTrace();
        }
    }


    
 
    
    public static void updateMedication(Connection conn, Scanner scanner) {
        
        try {
            
            // Ask user for medication name to update
            System.out.print("Enter medication name to update: ");
            String name = scanner.nextLine().toLowerCase();
            
             // Ask for new quantity
            System.out.print("Enter new quantity: ");
            int quantity = scanner.nextInt();
            
             // Clear scanner buffer after nextInt()
            scanner.nextLine();
            
              // ================= SQL UPDATE =================

             // SQL query to update quantity in inventory table
            // Uses JOIN to match medication name from medications table
            String sql = "UPDATE inventory i " +
                        "JOIN medications m ON i.medication_id = m.medication_id " +
                        "SET i.quantity = ? " +
                        "WHERE m.medication_name = ?";
            
              // Prepare SQL statement
            PreparedStatement stmt = conn.prepareStatement(sql);
            
            // Set values for query placeholders
            stmt.setInt(1, quantity); // new quantity
            stmt.setString(2, name); // medication name
            
            // Execute update query and get number of affected rows
            int rows = stmt.executeUpdate();
            
             // ================= CHECK RESULT =================

            if (rows > 0) {
                
                   // If update successful
                System.out.println("Medication updated successfully!");
                
                
            // ================= UPDATE DATA STRUCTURES =================
            
            
                 // Find medication in HashMap (convert to lowercase for consistency)
                Medication m = inventoryMap.get(name.toLowerCase());

                if (m != null) {
                    
                    // Update quantity in memory (LinkedList + HashMap object)
                    m.quantity = quantity;
                    
                    // If quantity is now low, add to Queue
                    if (quantity < 20) {
                        lowStockQueue.add(m);
                    }
                }

            } 
            
            else {
                
                 // If medication not found in database
                System.out.println(" Medication not found.");
            }

        } 
        
        catch (Exception e) {
            
             // Handle any errors
            e.printStackTrace();
        }
    }   
    
    public static void deleteMedication(Connection conn, Scanner scanner) {
        
        try {
            
            // Ask user for medication name to delete
            System.out.print("Enter medication name to delete: ");
            
            // Convert to lowercase for consistency with HashMap keys
            String name = scanner.nextLine().toLowerCase();
            
        // ================= DELETE FROM INVENTORY TABLE =================


            // SQL query to delete from inventory table using JOIN
            // This removes records where the medication name matches
            String sql = "DELETE i FROM inventory i " + 
                    "JOIN medications m ON i.medication_id = m.medication_id " +
                    " WHERE m.medication_name = ?";
            
            // Prepare SQL statement
            PreparedStatement stmt = conn.prepareStatement(sql);
            
            // Set medication name parameter
            stmt.setString(1, name);
            
             // Execute the delete query on inventory table
            int rows = stmt.executeUpdate();
            
            if (rows == 0) {
            System.out.println("Medication not found.");
            return; // Stop execution if nothing found
        }
            
        // ================= DELETE FROM MEDICATIONS TABLE =================
        
            // SQL query to remove the medication from medications table
            // This prevents orphan records and keeps database clean
            String sql2 = "DELETE FROM medications WHERE medication_name = ?";
            
            // Prepare second SQL statement
            PreparedStatement stmt2 = conn.prepareStatement(sql2);
            
            // Set the medication name parameter
            stmt2.setString(1, name);
            
            
            // Execute delete query on medications table
            stmt2.executeUpdate();
            
        // ================= UPDATE DATA STRUCTURES =================

            // Remove medication from HashMap (search structure)
            Medication m = inventoryMap.get(name);

            if (m != null) {
                // Remove from LinkedList (used for sorting/display)
                medicationList.remove(m);

                // Remove from HashMap (fast lookup)
                inventoryMap.remove(name);
            }

            // Note: Queue (lowStockQueue) is not updated here
            // It may still contain old references to deleted medications
            
            // Display confirmation message
            System.out.println("Deleted successfully!");

        } 
        
        catch (Exception e) {
            // Handle any errors
            e.printStackTrace();
        }
    }
        
    // LOAD ALL MEDICATIONS → HASHMAP
    public static void loadInventoryMap(Connection conn) throws Exception {
        
          // ================= SQL QUERY =================

         // SQL query to retrieve all medication data by joining two tables:
         // medications (name, category) and inventory (quantity, expiration, location)

        String sql = "SELECT m.medication_name, m.category, i.quantity, i.expiration_date, i.location " +
                     "FROM medications m JOIN inventory i ON m.medication_id = i.medication_id";
        
        // Prepare SQL statement
        PreparedStatement stmt = conn.prepareStatement(sql);
        
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
    
     // DISPLAY LOW STOCK (WITHOUT REMOVING)
    public static void displayLowStockMedications() {
        
         // Print report title/header
        System.out.println("\n================ LOW STOCK REPORT ================");
        
        // Check if queue is empty (no low stock items)
         if (lowStockQueue.isEmpty()) {
            System.out.println("No low stock medications.");
            return;
        }
         
        // Print table column headers with formatting
        System.out.printf("%-30s %-12s %-5s %-12s %-10s\n",
            "Name", "Category", "Qty", "Exp Date", "Location");

        System.out.println("-----------------------------------------------------------------------");

        int count = 0; // Counter for total low stock items
        
        
         // ================= DISPLAY QUEUE CONTENT =================
         
        // Loop through each medication in the queue
        // NOTE: This does NOT remove items from the queue
        for (Medication m : lowStockQueue) {
            
             // Print each medication in formatted table row
            System.out.printf("%-30s %-12s %-5d %-12s %-10s\n",
                    m.name,
                    m.category,
                    m.quantity,
                    m.expirationDate,
                    m.location);
            count++; // Increase counter
        }
        
        // Display total number of low stock medications
        System.out.println("-----------------------------------------------------------------------");
        System.out.println("Total Low Stock Medications: " + count);
    }
        
    //  MERGE SORT
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
}
    
    class Medication {
        
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


    
    
    

    



