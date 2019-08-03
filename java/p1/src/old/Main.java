/*
 * args[0]-args[4]: driver, url, username, password, db name
 *
 * v.0.1
 *
 * Petrovich incorporated. All rights reserved.
 */

import java.sql.*;

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
        //testDatabase.executeOperation("delete", "student");
    }
}
