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
                    "studentgroupteacher.Student " +
                    "(student_id INT NOT NULL AUTO_INCREMENT , " +
                    "Name VARCHAR(65) NOT NULL , " +
                    "Birthday DATE NOT NULL , " +
                    "Sex CHAR(1) NULL DEFAULT NULL , " +
                    "group_id INT NOT NULL , " +
                    "PRIMARY KEY (student_id)) ENGINE = InnoDB;";
            String Teacher = "CREATE TABLE IF NOT EXISTS " +
                    "studentgroupteacher.Teacher " +
                    "(teacher_id INT NOT NULL AUTO_INCREMENT , " +
                    "Name VARCHAR(65) NOT NULL , " +
                    "Birthday DATE NOT NULL , " +
                    "Sex CHAR(1) NULL DEFAULT NULL , " +
                    "PRIMARY KEY (teacher_id)) ENGINE = InnoDB;";
            String Group = "CREATE TABLE IF NOT EXISTS " +
                    "studentgroupteacher.Group " +
                    "( group_id INT NOT NULL AUTO_INCREMENT , " +
                    "Number INT NOT NULL , " +
                    "PRIMARY KEY (group_id)) ENGINE = InnoDB;";
            String GroupTeacher = "CREATE TABLE IF NOT EXISTS " +
                    "studentgroupteacher.GroupTeacher " +
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
            String link = "ALTER TABLE studentgroupteacher.student " +
                    "ADD INDEX(group_id);";
            Statement.executeUpdate(link);
            link = "ALTER TABLE studentgroupteacher.student " +
                    "ADD FOREIGN KEY (group_id) " +
                    "REFERENCES studentgroupteacher.group(group_id) " +
                    "ON DELETE RESTRICT ON UPDATE RESTRICT;";
            Statement.executeUpdate(link);

            //Group-Teacher
            link = "ALTER TABLE studentgroupteacher.groupteacher ADD INDEX(group_id);";
            Statement.executeUpdate(link);
            link = "ALTER TABLE studentgroupteacher.groupteacher ADD INDEX(teacher_id);";
            Statement.executeUpdate(link);
            link = "ALTER TABLE studentgroupteacher.groupteacher " +
                    "ADD FOREIGN KEY (group_id) " +
                    "REFERENCES studentgroupteacher.group(group_id) " +
                    "ON DELETE RESTRICT ON UPDATE RESTRICT;";
            Statement.executeUpdate(link);
            link = "ALTER TABLE studentgroupteacher.groupteacher " +
                    "ADD FOREIGN KEY (teacher_id) " +
                    "REFERENCES studentgroupteacher.teacher(teacher_id) " +
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
     * @throws SQLException
     */
    public void executeOperation(String operationType) throws SQLException {

        try(Connection connection = DriverManager.getConnection(url, user, password)) {
            if (operationType.equalsIgnoreCase("insert")) {
                System.out.println("Inserting in table \"student\". " +
                        "Enter name (String), birthday (YYYY-MM-dd), group");
                String query = "INSERT INTO studentgroupteacher.student " +
                        "(student_id, Name, Birthday, Sex, group_id) " +
                        "VALUES (NULL, ?, ?, NULL, ?)";
                PreparedStatement statement = connection.prepareStatement(query);

                Scanner in = new Scanner(System.in);
                String name = in.nextLine();
                String birthday = in.nextLine();
                int group = in.nextInt();
                in.close();

                statement.setString(1, name);
                statement.setDate(2, Date.valueOf(birthday));
                statement.setInt(3, group);
                statement.execute();
                statement.close();
            } else if (operationType.equalsIgnoreCase("select")) {
                System.out.println("Count students in group with given id. " +
                        "Enter group id (int): ");

                String query = "SELECT COUNT(*) FROM studentgroupteacher.student " +
                        "WHERE student.group_id = ?";
                PreparedStatement statement = connection.prepareStatement(query);

                Scanner in = new Scanner(System.in);
                int groupId = in.nextInt();
                statement.setInt(1, groupId);
                in.close();

                ResultSet result = statement.executeQuery();
                while (result.next()) {
                    System.out.println(result.getInt(1));
                }
                statement.close();
                result.close();
            } else if (operationType.equalsIgnoreCase("update")) {
                System.out.println("Updating teacher's name with given id. " +
                        "Enter teacher's id (int), teacher's new name (String): ");
                String query = "UPDATE studentgroupteacher.teacher SET Name = ? " +
                        "WHERE teacher.teacher_id = ?;";
                PreparedStatement statement = connection.prepareStatement(query);

                Scanner in = new Scanner(System.in);
                int id = in.nextInt();
                String teacherName = in.next();
                in.close();

                statement.setInt(2, id);
                statement.setString(1, teacherName);
                statement.executeUpdate();

                statement.close();
            } else if (operationType.equalsIgnoreCase("delete")) {
                System.out.println("Deleting student with given id. " +
                        "Enter student's id (int): ");
                String op = "DELETE FROM studentgroupteacher.student " +
                        "WHERE student.student_id = ?";
                PreparedStatement statement = connection.prepareStatement(op);

                Scanner in = new Scanner(System.in);
                int studentId = in.nextInt();

                statement.setInt(1, studentId);
                statement.executeUpdate();
            } else {
                System.err.println("Incorrect executeOperation name. ");
            }
        }
    }


    /**
     * @param driver - драйвер JDBC
     * @param url - адрес БД
     * @param user - имя пользователя БД
     * @param password - пароль пользователя БД
     * @param dbname - имя БД
     */
    private String driver;
    private String url;
    private String user;
    private String password;
    private String dbname;
}
