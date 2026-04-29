package pharmacy_inventory_management_system;

import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.util.HashMap;

public class SearchMedicationRunner {

    public static void main(String[] args) {
        if (args.length == 0) {
            System.out.println("NO_SEARCH_TERM");
            return;
        }

        String searchTerm = args[0].toLowerCase();

        HashMap<String, String> inventoryMap = new HashMap<>();

        try {
            Connection conn = DatabaseConnection.getConnection();

            if (conn == null) {
                System.out.println("DATABASE_CONNECTION_FAILED");
                return;
            }

            String sql = "SELECT " +
                         "m.medication_id, " +
                         "m.medication_name, " +
                         "m.category, " +
                         "m.description, " +
                         "m.reorder_threshold, " +
                         "s.supplier_name " +
                         "FROM medications m " +
                         "LEFT JOIN suppliers s ON m.supplier_id = s.supplier_id";

            PreparedStatement stmt = conn.prepareStatement(sql);
            ResultSet rs = stmt.executeQuery();

            while (rs.next()) {
                String medicationName = rs.getString("medication_name");

                String row =
                    rs.getInt("medication_id") + "|" +
                    medicationName + "|" +
                    rs.getString("category") + "|" +
                    rs.getString("description") + "|" +
                    rs.getInt("reorder_threshold") + "|" +
                    rs.getString("supplier_name");

                inventoryMap.put(medicationName.toLowerCase(), row);
            }

            boolean foundAny = false;

            for (String key : inventoryMap.keySet()) {
                if (key.contains(searchTerm)) {
                    System.out.println(inventoryMap.get(key));
                    foundAny = true;
                }
            }

            if (!foundAny) {
                System.out.println("NOT_FOUND");
            }

            rs.close();
            stmt.close();
            conn.close();

        } catch (Exception e) {
            System.out.println("SEARCH_ERROR");
            e.printStackTrace();
        }
    }
}