import java.lang.reflect.InvocationTargetException;
import java.lang.reflect.Method;
import java.sql.*;
import java.util.Scanner;

/**
 * Класс Database предоставляет возможность работы с базой данной студентов и предодавателей,
 * в частности мы можем: создать БД с дефолтными настройками либо задать свои
 *
 * @version 0.1 31 Jul 2019
 * @author ShadyX228
 */

public class Database {
    private String driver;
    private String url;
    private String user;
    private String password;
    private String dbname;


    Database() throws SQLException {
        this.driver = "jdbc:mysql";
        this.url = driver + "://" + "localhost/";
        this.user = "root";
        this.password = "12345";
        this.dbname = "studentgroupteacher";
        System.out.println("Using default connection data.");

        Connection connection = DriverManager.getConnection(
                url,
                user,
                password
        );
        this.createDatabase(connection);
        connection.close();
    }

    Database(String driver, String url, String user,
             String password, String dbname) throws SQLException {
        this.driver = driver;
        this.url = driver + "://" + url;
        this.user = user;
        this.password = password;
        this.dbname = dbname;

        Connection connection = DriverManager.getConnection(
                url,
                user,
                password
        );
        this.createDatabase(connection);
        connection.close();
    }


    /**
     * Метод createDatabase создает базу данных с именем dbname.
     * При этом, если существует БД с данным именем,
     * пользователь получает уведомление.
     *
     * @throws SQLException
     */
    private void createDatabase(Connection connection) throws SQLException{
            Statement CreateStatement = connection.createStatement();
            String createDB = "CREATE DATABASE IF NOT EXISTS " + dbname;
            int created = CreateStatement.executeUpdate(createDB);

            System.out.print("Creating Database \"" + dbname + "\".");
            if(created == 0) {
                System.out.println(" Database \"" + dbname +
                        "\" is already exists.");
            } else {
                System.out.println(" Success.");
                this.createTables(connection);
            }
            CreateStatement.close();
    }

    /**
     * Метод createTables создает в БД таблицы вместе с атрибутами,
     * а также устанавливает связи между таблицами.
     *
     * @throws SQLException
     */
    private void createTables(Connection connection) throws SQLException {
            String Student = "CREATE TABLE IF NOT EXISTS " +
                    dbname + ".Student " +
                    "(student_id INT NOT NULL AUTO_INCREMENT , " +
                    "Name VARCHAR(65) NOT NULL , " +
                    "Birthday DATE NOT NULL , " +
                    "Sex CHAR(1) NULL DEFAULT NULL , " +
                    "group_id INT NOT NULL , " +
                    "PRIMARY KEY (student_id)) ENGINE = InnoDB;";
            String Teacher = "CREATE TABLE IF NOT EXISTS " +
                    dbname + ".Teacher " +
                    "(teacher_id INT NOT NULL AUTO_INCREMENT , " +
                    "Name VARCHAR(65) NOT NULL , " +
                    "Birthday DATE NOT NULL , " +
                    "Sex CHAR(1) NULL DEFAULT NULL , " +
                    "PRIMARY KEY (teacher_id)) ENGINE = InnoDB;";
            String Group = "CREATE TABLE IF NOT EXISTS " +
                    dbname + ".Group " +
                    "( group_id INT NOT NULL AUTO_INCREMENT , " +
                    "Number INT NOT NULL , " +
                    "PRIMARY KEY (group_id)) ENGINE = InnoDB;";
            String GroupTeacher = "CREATE TABLE IF NOT EXISTS " +
                    dbname + ".GroupTeacher " +
                    "( id INT NOT NULL AUTO_INCREMENT , " +
                    "group_id INT NOT NULL , " +
                    "teacher_id INT NOT NULL , " +
                    "PRIMARY KEY (id)) ENGINE = InnoDB;";
            String link = "ALTER TABLE " + dbname + ".student " +
                    "ADD INDEX(group_id);";
            Statement Statement = connection.createStatement();


            System.out.print("Creating tables. ");
            Statement.executeUpdate(Student);
            Statement.executeUpdate(Teacher);
            Statement.executeUpdate(Group);
            Statement.executeUpdate(GroupTeacher);
            System.out.println("Tables created.");


            System.out.print("Establishing links. ");
            // Student-Group
            Statement.executeUpdate(link);
            link = "ALTER TABLE " + dbname + ".student " +
                    "ADD FOREIGN KEY (group_id) " +
                    "REFERENCES studentgroupteacher.group(group_id) " +
                    "ON DELETE RESTRICT ON UPDATE RESTRICT;";
            Statement.executeUpdate(link);
            //Group-Teacher
            link = "ALTER TABLE " + dbname + ".groupteacher ADD INDEX(group_id);";
            Statement.executeUpdate(link);
            link = "ALTER TABLE " + dbname + ".groupteacher ADD INDEX(teacher_id);";
            Statement.executeUpdate(link);
            link = "ALTER TABLE " + dbname + ".groupteacher " +
                    "ADD FOREIGN KEY (group_id) " +
                    "REFERENCES " + dbname + ".group(group_id) " +
                    "ON DELETE RESTRICT ON UPDATE RESTRICT;";
            Statement.executeUpdate(link);
            link = "ALTER TABLE " + dbname + ".groupteacher " +
                    "ADD FOREIGN KEY (teacher_id) " +
                    "REFERENCES " + dbname + ".teacher(teacher_id) " +
                    "ON DELETE RESTRICT ON UPDATE RESTRICT;";
            Statement.executeUpdate(link);
            System.out.print("Links established.");


            Statement.close();
    }

    @withConnection
    private void insertStudent(Connection connection) throws SQLException {
        String query = "INSERT INTO " + dbname + ".student " +
                "(student_id, Name, Birthday, Sex, group_id) " +
                "VALUES (NULL, ?, ?, NULL, ?)";
        PreparedStatement statement = connection.prepareStatement(query);
        Scanner in = new Scanner(System.in);
        String name;
        String birthday;
        int groupId;


        System.out.println("Inserting in table \"student\"");
        System.out.println("Enter name (String), birthday (YYYY-MM-dd), group id: ");


        name = in.nextLine();
        birthday = in.nextLine();
        groupId = in.nextInt();


        statement.setString(1, name);
        statement.setDate(2, Date.valueOf(birthday));
        statement.setInt(3, groupId);

        statement.executeUpdate();
        in.close();
        statement.close();
    }

    @withConnection
    private void insertTeacher(Connection connection) throws SQLException {
        String query = "INSERT INTO " + dbname + ".teacher " +
                "(teacher_id, Name, Birthday, Sex) " +
                "VALUES (NULL, ?, ?, NULL)";
        PreparedStatement statement = connection.prepareStatement(query);
        String name;
        String birthday;


        System.out.println("Inserting in table \"teacher\"");
        System.out.println("Enter name (String), birthday (YYYY-MM-dd): ");


        Scanner in = new Scanner(System.in);
        name = in.nextLine();
        birthday = in.nextLine();


        statement.setString(1, name);
        statement.setDate(2, Date.valueOf(birthday));


        statement.executeUpdate();
        in.close();
        statement.close();
    }

    @withConnection
    private void insertGroup(Connection connection) throws SQLException {
        String query = "INSERT INTO " + dbname + ".group " +
                "(group_id, Number) " +
                "VALUES (NULL, ?)";
        PreparedStatement statement = connection.prepareStatement(query);
        Scanner in = new Scanner(System.in);
        int groupId;


        System.out.println("Inserting in table \"teacher\".");
        System.out.println("Enter number of group (int): ");
        groupId = in.nextInt();
        statement.setInt(1, groupId);


        statement.executeUpdate();
        in.close();
        statement.close();
    }

    @withConnection
    private void selectStudent(Connection connection) throws SQLException {
        String query = "SELECT COUNT(*) FROM " + dbname + ".student " +
                "WHERE student.birthday = ?";
        PreparedStatement statement = connection.prepareStatement(query);
        Scanner in = new Scanner(System.in);
        String birthday;


        System.out.println("Count all students from table " +
                "with given birtday.");


        birthday = in.nextLine();
        statement.setString(1, birthday);


        ResultSet result = statement.executeQuery();
        while(result.next()) {
            System.out.println(result.getInt(1));
        }
        in.close();
        statement.close();
    }

    @withConnection
    private void selectTeacher(Connection connection) throws SQLException {
        String query = "SELECT COUNT(*) FROM " + dbname + ".teacher " +
                "WHERE teacher.birthday = ?";
        PreparedStatement statement = connection.prepareStatement(query);
        Scanner in = new Scanner(System.in);
        String birthday;


        System.out.println("Count all teachers from table " +
                "with given birthday.");


        birthday = in.nextLine();
        statement.setString(1, birthday);


        ResultSet result = statement.executeQuery();
        while(result.next()) {
            System.out.println(result.getInt(1));
        }
        in.close();
        statement.close();
    }

    @withConnection
    private void updateStudent(Connection connection,
                              String updatedColumn,
                              String criterion) throws SQLException {
        String query = "UPDATE " + dbname + ".student SET " +
                updatedColumn + "= ? WHERE student." + criterion + "= ?;";
        PreparedStatement statement = connection.prepareStatement(query);
        Scanner in = new Scanner(System.in);
        String updateColumnValue;
        String criterionValue;


        System.out.println("Updating column \"" +
                updatedColumn + "\" in table \"student\" by \"" + criterion + "\".");


        System.out.println("Enter updated column value: ");
        updateColumnValue = in.nextLine();
        System.out.println("Enter criterion value: ");
        criterionValue = in.nextLine();
        statement.setString(1,updateColumnValue);
        statement.setString(2, criterionValue);


        statement.executeUpdate();
        in.close();
        statement.close();
    }

    @withConnection
    private void updateTeacher(Connection connection,
                              String updatedColumn,
                              String criterion) throws SQLException {
        String query = "UPDATE " + dbname + ".teacher SET " +
                updatedColumn + "= ? WHERE teacher." + criterion + "= ?;";
        PreparedStatement statement = connection.prepareStatement(query);
        Scanner in = new Scanner(System.in);
        String updateColumnValue;
        String criterionValue;


        System.out.println("Updating column \"" +
                updatedColumn + "\" in table \"teacher\" by \"" + criterion + "\".");


        System.out.println("Enter updated column value: ");
        updateColumnValue = in.nextLine();
        System.out.println("Enter criterion value: ");
        criterionValue = in.nextLine();
        statement.setString(1,updateColumnValue);
        statement.setString(2, criterionValue);


        statement.executeUpdate();
        in.close();
        statement.close();
    }

    @withConnection
    private void updateGroup(Connection connection) throws SQLException {
        String query = "UPDATE " + dbname + ".group SET Number = ? WHERE group.group_id = ?;";
        PreparedStatement statement = connection.prepareStatement(query);
        Scanner in = new Scanner(System.in);
        int number;
        int id;


        System.out.println("Updating group number by given group id (int).");


        number = in.nextInt();
        id = in.nextInt();

        statement.setInt(1, number);
        statement.setInt(2, id);


        statement.executeUpdate();
        in.close();
        statement.close();
    }

    @withConnection
    private void deleteRow(Connection connection,
                          String table,
                          String criterion)
            throws SQLException {
        if(!table.equalsIgnoreCase("groupteacher")) {
            String query = "DELETE FROM " + dbname + "." + table + " WHERE " + table + "." + criterion + " = ?;";
            PreparedStatement statement = connection.prepareStatement(query);
            Scanner in = new Scanner(System.in);
            int id;


            System.out.println("Delete row in table \"" + table + "\" by \"" + criterion + "\".");


            id = in.nextInt();
            statement.setInt(1, id);


            statement.executeUpdate();
            in.close();
            statement.close();
        } else {
            System.err.println("Incorrect table name.");
        }
    }

    public void execute(String method) throws
            SQLException,
            IllegalAccessException,
            IllegalArgumentException {
        try(Connection connection = DriverManager.getConnection(
                url,
                user,
                password
        ))
        {
            if((method.equals("updateStudent")) ||
                    (method.equals("updateTeacher")) ||
                    (method.equals("deleteRow"))) {
                try {
                    Method checkMethod = Database.class.getDeclaredMethod(method,
                            Connection.class,
                            String.class,
                            String.class
                    );
                    if (checkMethod.isAnnotationPresent(withConnection.class)) {
                        Scanner in = new Scanner(System.in);

                        String arg1;

                        String arg2;


                        System.out.println("Method: " + method + ".");


                        System.out.println("Enter updated column " +
                                "(or table for \"deleteRow\"):"
                        );
                        arg1 = in.next();
                        System.out.println("Enter criterion: ");
                        arg2 = in.next();


                        try{
                            checkMethod.invoke(this, connection, arg1, arg2);
                        }
                        catch (InvocationTargetException exc) {
                            exc.printStackTrace();
                        }
                    }
                }
                catch (NoSuchMethodException exc) {
                    exc.printStackTrace();
                }
            } else {
                try {
                    Method checkMethod = Database.class.getDeclaredMethod(method,
                            Connection.class
                    );
                    if (checkMethod.isAnnotationPresent(withConnection.class)) {
                        System.out.println("Method: " + method + ".");


                        try{
                            checkMethod.invoke(this, connection);
                        }
                        catch (InvocationTargetException exc) {
                            exc.printStackTrace();
                        }
                    }
                }
                catch (NoSuchMethodException exc) {
                    exc.printStackTrace();
                }
            }
        }
    }
}