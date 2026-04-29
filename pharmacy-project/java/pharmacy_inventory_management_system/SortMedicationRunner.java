package pharmacy_inventory_management_system;

import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.util.LinkedList;

public class SortMedicationRunner {

    public static void main(String[] args) {
        LinkedList<Medication> medicationList = new LinkedList<>();
        LinkedList<String> outputRows = new LinkedList<>();

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
                         "s.supplier_name, " +
                         "i.quantity, " +
                         "i.expiration_date, " +
                         "i.location " +
                         "FROM medications m " +
                         "JOIN inventory i ON m.medication_id = i.medication_id " +
                         "LEFT JOIN suppliers s ON m.supplier_id = s.supplier_id";

            PreparedStatement stmt = conn.prepareStatement(sql);
            ResultSet rs = stmt.executeQuery();

            while (rs.next()) {
                Medication medication = new Medication(
                    rs.getString("medication_name"),
                    rs.getString("category"),
                    rs.getInt("quantity"),
                    rs.getDate("expiration_date"),
                    rs.getString("location")
                );

                medicationList.add(medication);

                String row =
                    rs.getInt("medication_id") + "|" +
                    rs.getString("medication_name") + "|" +
                    rs.getString("category") + "|" +
                    rs.getString("description") + "|" +
                    rs.getInt("reorder_threshold") + "|" +
                    rs.getString("supplier_name") + "|" +
                    rs.getDate("expiration_date");

                outputRows.add(row);
            }

            MergeSort.mergeSort(medicationList);

            for (Medication sortedMedication : medicationList) {
                for (String row : outputRows) {
                    String[] parts = row.split("\\|");

                    if (parts.length >= 7 && parts[1].equals(sortedMedication.name)) {
                        System.out.println(row);
                        break;
                    }
                }
            }

            rs.close();
            stmt.close();
            conn.close();

        } catch (Exception e) {
            System.out.println("SORT_ERROR");
            e.printStackTrace();
        }
    }
}