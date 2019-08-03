/*
 * args[0]-args[4]: driver, url, username, password, db name
 * Example: java Main "jdbc:mysql" "localhost" "root" "12345" "sgt"
 *
 * v.0.1
 *
 * Petrovich incorporated. All rights reserved.
 */

import java.sql.*;
import java.util.Scanner;

public class Main {
    public static void main(String[] args) throws SQLException {
        database testDatabase;
        if(args.length == 0) {
            testDatabase = new database();
        }
        else {
            testDatabase = new database(args[0], args[1], args[2], args[3], args[4]);
        }
        testDatabase.createDatabase();
        testDatabase.createTables();

        System.out.println("\nEnter operation type and table name. " +
                "Allowed variants for operation type:\n" +
                "select, insert, delete, update.\n" +
                "For table name: student, teacher, group.");
        Scanner in = new Scanner(System.in);
        String operation = in.next();
        String table = in.next();
        testDatabase.executeOperation(operation, table);
    }
}