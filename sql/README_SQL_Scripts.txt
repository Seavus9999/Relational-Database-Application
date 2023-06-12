/*
Difference between .sql scripts:
team001_db_schema.sql contains only the tables and constaints (no seed/insert data) used for Phase 2 submission
team001_seed_data.sql contains only the insert statements needed to have something to look at for the demo project and allow users to login to the app.
team001_complete_v7.sql includes both the schema (tables and constraints) and test/seed data (insert values) used for Phase 3 submission
to recreate the default state of your database used at the beginning of your project demo. 
Note: need to run team001_db_schema.sql import first before adding seed data (or just run the team001_complete_v7.sql script which has both)

Required: a full featured relational DBMS: teams are free to use either PostgreSQL or MySQL.  
Non-relational noSQL: (Hadoop, Cassandra,  MongoDB) and Non-full featured (SQLite) are not allowed for this course
https://dev.mysql.com/doc/refman/5.7/en/introduction.html
https://www.postgresql.org/docs/9.6/static/index.html
The limited GT Online example below assumes a team will use MySQL- it is designed as a learning tool to display of full SQL queries with respective error messages/logging mysqli_errno() + mysqli_error() to the UI.    

CS6400 students are expected to write their own SQL.  We'll say it again: students are expected to write their own SQL! 
Note: if your SQL script is a dump (auto generated via phpMyAdmin/Workbench export), there will be ZERO points awarded for that portion of the submission.
Please change the team number in the database name 'cs6400_sp17_team001' to reflect your correct team number including the leading zero. 

Note: we may run your PostgreSQL or MySQL script to see if it creates your schema (tables/constraints) 
successfully via phpMyAdmin/PgAdmin import, if not, point deductions may apply. 

Optional: MySQL centric items 
    MySQL: DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
    MySQL Storage Engines: SET default_storage_engine=InnoDB;
    Note: "IF EXISTS" is not universal, and the "IF NOT EXISTS" is uncommonly supported, so this functionaly may not work if outside MySQL RDBMS.

SQL Naming Conventions: 
      Entity/Table Names (singular noun):  UpperCamelCase	Example: 'RegularUser'  (note: singular nouns without the ‘s’ at the end)
      Attribute Names: lowercase_underscore 	Example: 'first_name' or 'date_connected'
      Primary Surrogate Key (phase3 only):  tableNameID   Example: userID unsigned NOT NULL AUTO_INCREMENT  (‘ID’ is capitalized)
      Foreign Keys: FK_ChildTable_childColumn_ParentTable_parentColumn
            http://stackoverflow.com/questions/29561123/identify-parent-and-child-table-in-a-database
            ParentTable= User  
            ChildTable = RegularUser
            fk_ChildTable_childColumn_ParentTable_parentColumn
            FK Result: fk_RegularUser_email_User_email

      Note: there are many convensions out there: 
            http://www.vertabelo.com/blog/technical-articles/naming-conventions-in-database-modeling
            https://launchbylunch.com/posts/2014/Feb/16/sql-naming-conventions/

SQL Centric Characters:
    Grave Accent ` = U+60 (good escape character)  	
    http://stackoverflow.com/questions/7857278/what-is-the-meaning-of-grave-accent-aka-backtick-quoted-characters-in-mysql
    Apostrophe ' = U+27 (good for $param application variables)   
    https://unicode-table.com/en/search/?q=%27
    Left Single Quote ‘ = U+2018 (BAD: queries will fail)
    Right Single Quote ’ = U+2019 (BAD: queries will fail)

Case Sensitive Table Names: may cause portability issues- Windows vs. Linux
    https://dev.mysql.com/doc/refman/5.7/en/identifier-case-sensitivity.html
    # The MySQL server
    [mysqld]
    lower_case_table_names = 2   (options: 0,1,2)
    https://dev.mysql.com/doc/refman/5.7/en/identifier-case-sensitivity.html

Resources:
    http://www.w3schools.com/
    http://www.agiledata.org/essays/keys.html
    https://dev.mysql.com/doc/refman/5.7/en/data-types.html
    https://bitnami.com/stacks/infrastructure
    https://www.jetbrains.com/phpstorm/
    https://v4-alpha.getbootstrap.com/components/forms/
    http://www.cs.montana.edu/~halla/csci440/index.html
    https://lagunita.stanford.edu/courses/Engineering/db/2014_1/about
    http://web.stanford.edu/class/cs145/
*/