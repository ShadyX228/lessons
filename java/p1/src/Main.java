/*
 * args[0]-args[4]: driver, url, username, password, db name
 * Пример: java Main "jdbc:mysql" "localhost" "root" "12345" "sgt"
 *
 * v.0.1
 *
 * Протестировано с XAMPP v3.2.2., MySQL 10.1.37-MariaDB, mysql-connector-java-8.0.16
 * Коннектор поместить в CLASSPATH
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
        } else {
            testDatabase = new database(
                    args[0],
                    args[1],
                    args[2],
                    args[3],
                    args[4]
            );
        }

        System.out.println("\nEnter table name. " +
                "Allowed variants for table name: student, teacher, group.");
        Scanner in = new Scanner(System.in);
        String table = in.next();

        testDatabase.executeUpdate(table);

        in.close();
    }
}