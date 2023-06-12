
-- CREATE USER 'newuser'@'localhost' IDENTIFIED BY 'password';
CREATE USER IF NOT EXISTS gatechUser@localhost IDENTIFIED BY 'gatech123';

DROP DATABASE IF EXISTS `cs6400_spring23_team040`; 
SET default_storage_engine=InnoDB;
SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE DATABASE IF NOT EXISTS cs6400_spring23_team040 
    DEFAULT CHARACTER SET utf8mb4 
    DEFAULT COLLATE utf8mb4_unicode_ci;
USE cs6400_spring23_team040;

GRANT SELECT, INSERT, UPDATE, DELETE, FILE ON *.* TO 'gatechUser'@'localhost';
GRANT ALL PRIVILEGES ON `gatechuser`.* TO 'gatechUser'@'localhost';
GRANT ALL PRIVILEGES ON `cs6400_fa17_team001`.* TO 'gatechUser'@'localhost';
FLUSH PRIVILEGES;

-- Tables 

CREATE TABLE Location (

  postal_code varchar(5) NOT NULL,
  city varchar(50) NOT NULL,
  state_abre varchar(2) NOT NULL,
  latitude float(10) NOT NULL,
  longitude float(10) NOT NULL,
  PRIMARY KEY (postal_code)
);

CREATE TABLE Household (

  email varchar(100) NOT NULL,
  square_footage int NOT NULL,
  household_type varchar(15) NOT NULL,
  heating int NULL,
  cooling int NULL,
  postal_code varchar(5) NOT NULL,
  PRIMARY KEY (email),
  FOREIGN KEY (postal_code) REFERENCES Location(postal_code)
);

CREATE TABLE Utilities (

  email varchar(100) NOT NULL,
  public_utilities varchar(10) NOT NULL,
  PRIMARY KEY (email, public_utilities),
  FOREIGN KEY (email) REFERENCES Household(email)
);



CREATE TABLE PowerGenerator (

  email varchar(100) NOT NULL,
  generator_ID int NOT NULL,
  storage_KWH int NULL,
  monthly_KWH int NOT NULL,
  generator_type varchar(15) NOT NULL,
  PRIMARY KEY (email, generator_ID),
  FOREIGN KEY (email) REFERENCES Household(email)
);

CREATE TABLE Manufacturer (

  manufacturer_name varchar(50) NOT NULL,
  PRIMARY KEY (manufacturer_name)
);

CREATE TABLE WaterHeater (

  email varchar(100) NOT NULL,
  appliance_ID int NOT NULL,
  model_name varchar(50) NULL,
  BTU_rating int NOT NULL,
  capacity float(6) NOT NULL,
  temperature_setting int NULL,
  energy_source varchar(15) NOT NULL,
  manufacturer_name varchar(50) NOT NULL,
  PRIMARY KEY (email, appliance_ID),
  FOREIGN KEY (manufacturer_name) REFERENCES Manufacturer(manufacturer_name),
  FOREIGN KEY (email) REFERENCES Household(email)
);

CREATE TABLE AirHandler (

  email varchar(100) NOT NULL,
  appliance_ID int NOT NULL,
  model_name varchar(50) NULL,
  BTU_rating int NOT NULL,
  manufacturer_name varchar(50) NOT NULL,
  PRIMARY KEY (email, appliance_ID),
  FOREIGN KEY (manufacturer_name) REFERENCES Manufacturer(manufacturer_name),
  FOREIGN KEY (email) REFERENCES Household(email)
);

CREATE TABLE AirConditioner (

  email varchar(100) NOT NULL,
  appliance_ID int NOT NULL,
  EER float(5) NOT NULL,
  PRIMARY KEY (email, appliance_ID),
  FOREIGN KEY (email,appliance_ID) REFERENCES AirHandler(email,appliance_ID) ON DELETE CASCADE
);

CREATE TABLE Heater (

  email varchar(100) NOT NULL,
  appliance_ID int NOT NULL,
  energy_source varchar(15) NOT NULL,
  PRIMARY KEY (email, appliance_ID),
  FOREIGN KEY (email,appliance_ID) REFERENCES AirHandler(email,appliance_ID) ON DELETE CASCADE
);

CREATE TABLE HeatPump (

  email varchar(100) NOT NULL,
  appliance_ID int NOT NULL,
  SEER float(5) NOT NULL,
  HSPF float(5) NOT NULL,
  PRIMARY KEY (email, appliance_ID),
  FOREIGN KEY (email,appliance_ID) REFERENCES AirHandler(email,appliance_ID) ON DELETE CASCADE
);

