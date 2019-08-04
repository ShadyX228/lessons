import java.sql.*;
import java.util.Scanner;

/**
 * Класс database предоставляет возможность работы с базой данной студентов и предодавателей,
 * в частности мы можем: создать БД с дефолтными настройками либо задать свои
 *
 * @version 0.1 31 Jul 2019
 * @author ShadyX228
 */

public class database {
    database(){
        this.driver = "jdbc:mysql";
        this.url = driver + "://" + "localhost/";
        this.user = "root";
        this.password = "12345";
        this.dbname = "studentgroupteacher";
        System.out.println("Using default connection data.");
    }
    database(String driver, String url, String user, String password, String dbname) {
        this.driver = driver;
        this.url = driver + "://" + url;
        this.user = user;
        this.password = password;
        this.dbname = dbname;
    }

    /**
     * Метод createDatabase создает базу данных с именем dbname.
     * При этом, если существует БД с данным именем,
     * пользователь получает уведомление.
     *
     * @throws SQLException
     */
    public void createDatabase() throws SQLException{

        try (Connection connection = DriverManager.getConnection(url, user, password);
             Statement CreateStatement = connection.createStatement()) {
            System.out.print("Creating database \"" + dbname + "\".");
            String createDB = "CREATE DATABASE IF NOT EXISTS " + dbname;
            int created = CreateStatement.executeUpdate(createDB);
            if(created == 0) {
                System.out.println(" Database \"" + dbname +
                        "\" is already exists.");
            } else {
                System.out.println(" Success.");
            }
        }
    }

    /**
     * Метод createTables создает в БД таблицы вместе с атрибутами,
     * а также устанавливает связи между таблицами.
     *
     * @throws SQLException
     */
    public void createTables() throws SQLException {

        try (Connection connection = DriverManager.getConnection(url, user, password)) {
            System.out.print("Creating tables. ");

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

            Statement Statement = connection.createStatement();
            Statement.executeUpdate(Student);
            Statement.executeUpdate(Teacher);
            Statement.executeUpdate(Group);
            Statement.executeUpdate(GroupTeacher);

            System.out.println("Tables created.");

            System.out.print("Establishing links. ");

            // Student-Group
            String link = "ALTER TABLE " + dbname + ".student " +
                    "ADD INDEX(group_id);";
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
    }

    /**
     * Метод executeOperation выполняет CRUD-операции с использованием preparedStatement.
     *
     * @param operationType - определяет операцию, которая будет выполнена.
     *                  Возможные значения: insert, select, update, delete.
     *                  Регистр не имеет значения.
     *                  Если введено некорректное значение,
     *                  пользователь получает об этом уведомление.
     *
     * @param table - определяет таблицу, с которой будет производиться операция
     *              Возможные значения: student, teacher, group.
     * @throws SQLException
     */
    public void executeOperation(String operationType, String table) throws SQLException {

        try(Connection connection = DriverManager.getConnection(url, user, password)) {
            if (operationType.equalsIgnoreCase("insert")) {
                System.out.println("Inserting in table \"" + table + "\"");

                if (table.equals("student")) {
                    System.out.println("Enter name (String), birthday (YYYY-MM-dd), group id: ");

                    String query = "INSERT INTO " + dbname + ".student " +
                            "(student_id, Name, Birthday, Sex, group_id) " +
                            "VALUES (NULL, ?, ?, NULL, ?)";
                    PreparedStatement statement = connection.prepareStatement(query);

                    Scanner in = new Scanner(System.in);
                    String name = in.nextLine();
                    String birthday = in.nextLine();
                    int groupId = in.nextInt();
                    in.close();

                    statement.setString(1, name);
                    statement.setDate(2, Date.valueOf(birthday));
                    statement.setInt(3, groupId);

                    statement.executeUpdate();
                    statement.close();
                } else if (table.equals("teacher")) {
                    System.out.println("Enter name (String), birthday (YYYY-MM-dd): ");

                    String query = "INSERT INTO " + dbname + ".teacher " +
                            "(teacher_id, Name, Birthday, Sex) " +
                            "VALUES (NULL, ?, ?, NULL)";
                    PreparedStatement statement = connection.prepareStatement(query);

                    Scanner in = new Scanner(System.in);
                    String name = in.nextLine();
                    String birthday = in.nextLine();
                    in.close();

                    statement.setString(1, name);
                    statement.setDate(2, Date.valueOf(birthday));

                    statement.executeUpdate();
                    statement.close();
                } else if(table.equals("group")) {
                    System.out.println("Enter number of group (int): ");

                    String query = "INSERT INTO " + dbname + ".group " +
                            "(group_id, Number) " +
                            "VALUES (NULL, ?)";
                    PreparedStatement statement = connection.prepareStatement(query);

                    Scanner in = new Scanner(System.in);
                    int group_id = in.nextInt();
                    in.close();

                    statement.setInt(1, group_id);

                    statement.executeUpdate();
                    statement.close();
                } else if(table.equals("groupteacher")) {
                    System.out.println("Enter number of group (int), teacher id (int): ");

                    String query = "INSERT INTO + " + dbname + ".groupteacher " +
                            "(id, group_id, teacher_id) " +
                            "VALUES (NULL, ?, ?);";
                    PreparedStatement statement = connection.prepareStatement(query);

                    Scanner in = new Scanner(System.in);
                    int group_id = in.nextInt();
                    int teacher_id = in.nextInt();
                    in.close();

                    statement.setInt(1, group_id);
                    statement.setInt(2, teacher_id);

                    statement.executeUpdate();
                    statement.close();
                } else {
                    System.out.println("Can't execute operation. Select another table.");
                }
            } else if(operationType.equalsIgnoreCase("select")) {
                if((table.equals("student")) || (table.equals("teacher"))) {
                    System.out.println("Count all " + table + "s from table \"" +
                            table + "\" " + "with given birtday.");

                    String query = "SELECT COUNT(*) FROM " + dbname + "." +
                            table + " WHERE " + table + ".birthday = ?";

                    PreparedStatement statement = connection.prepareStatement(query);

                    Scanner in = new Scanner(System.in);
                    String birthday = in.nextLine();
                    in.close();

                    statement.setString(1, birthday);

                    ResultSet result = statement.executeQuery();
                    while(result.next()) {
                        System.out.println(result.getInt(1));
                    }
                    statement.close();
                } else if (table.equals("groupteacher")) {
                    System.out.println("Count how many teachers teach group with given id (int).");

                    String query = "SELECT COUNT(*) " +
                            "FROM " + dbname + ".groupteacher " +
                            "WHERE group_id = ?";

                    PreparedStatement statement = connection.prepareStatement(query);

                    Scanner in = new Scanner(System.in);
                    int groupId = in.nextInt();
                    in.close();

                    statement.setInt(1, groupId);

                    ResultSet result = statement.executeQuery();
                    while(result.next()) {
                        System.out.println(result.getInt(1));
                    }
                } else if (table.equals("group")) {
                    System.out.println("Select group number by given group id (int).");

                    String query = "SELECT Number " +
                            "FROM " + dbname + ".group " +
                            "WHERE group_id = ?";

                    PreparedStatement statement = connection.prepareStatement(query);

                    Scanner in = new Scanner(System.in);
                    int groupId = in.nextInt();
                    in.close();

                    statement.setInt(1, groupId);

                    ResultSet result = statement.executeQuery();
                    while (result.next()) {
                        System.out.println(result.getInt(1));
                    }
                    statement.close();
                } else {
                    System.out.println("Can't execute operation. Select another table.");
                }
            } else if (operationType.equalsIgnoreCase("update")) {
                if(((table.equals("student")) || (table.equals("teacher")))) {
                    System.out.println("Updating " + table + "'s name (String) by given id (int).");

                    String query = "UPDATE " + dbname + "." + table + " SET Name = ? WHERE " + table + "." + table + "_id = ?;";

                    PreparedStatement statement = connection.prepareStatement(query);

                    Scanner in = new Scanner(System.in);
                    String name = in.nextLine();
                    int id = in.nextInt();
                    in.close();

                    statement.setString(1, name);
                    statement.setInt(2, id);

                    statement.executeUpdate();
                    statement.close();

                } else if (table.equals("group")) {
                    System.out.println("Updating group number by given group id (int).");

                    String query = "UPDATE " + dbname + ".group SET Number = ? WHERE group.group_id = ?;";

                    PreparedStatement statement = connection.prepareStatement(query);

                    Scanner in = new Scanner(System.in);
                    int number = in.nextInt();
                    int id = in.nextInt();
                    in.close();

                    statement.setInt(1, number);
                    statement.setInt(2, id);

                    statement.executeUpdate();
                    statement.close();
                } else {
                    System.out.println("Can't execute operation. Select another table.");
                }
            } else if (operationType.equalsIgnoreCase("delete")) {
                if(!table.equalsIgnoreCase("groupteacher")) {
                    System.out.println("Delete row in table \"" + table + "\" with given id (int):");

                    String query = "DELETE FROM " + dbname + "." + table + " WHERE " + table + "." + table + "_id = ?;";

                    PreparedStatement statement = connection.prepareStatement(query);

                    Scanner in = new Scanner(System.in);
                    int id = in.nextInt();
                    in.close();

                    statement.setInt(1, id);

                    statement.executeUpdate();
                    statement.close();
                }
            } else {
                System.out.println("Incorrect operation name.");
            }
        }
    }

    private String driver;
    private String url;
    private String user;
    private String password;
    private String dbname;
}