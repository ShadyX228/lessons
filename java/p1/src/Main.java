/*
 * args[0]-args[4]: driver, url, username, password, db name
 * Пример: java Main "jdbc:mysql" "localhost" "root" "12345" "sgt"
 *
 * v.0.2
 *
 * Протестировано с XAMPP v3.2.2., MySQL 10.1.37-MariaDB, mysql-connector-java-8.0.16
 * Коннектор поместить в CLASSPATH
 *
 */

import java.lang.reflect.InvocationTargetException;
import java.sql.*;
import java.util.Scanner;

public class Main {
    public static void main(String[] args) throws SQLException, NoSuchMethodException, InvocationTargetException, IllegalAccessException {
        Database testDatabase;
        if(args.length == 0) {
            testDatabase = new Database();
        } else {
            testDatabase = new Database(
                    args[0],
                    args[1],
                    args[2],
                    args[3],
                    args[4]
            );
        }

        String method;
        Scanner in = new Scanner(System.in);

        System.out.println("Enter method name: ");
        method = in.next();

        testDatabase.execute(method);


    }
}