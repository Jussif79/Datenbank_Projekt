CREATE DATABASE IF NOT EXISTS hotel_db;

SET
    SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

USE hotel_db;

CREATE TABLE bookings (
    user_id VARCHAR(20) NOT NULL,
    booking_id VARCHAR(20) NOT NULL,
    name VARCHAR(50) NOT NULL,
    email VARCHAR(50) NOT NULL,
    number VARCHAR(10) NOT NULL,
    rooms INT(1) NOT NULL,
    check_in VARCHAR(10) NOT NULL,
    check_out VARCHAR(10) NOT NULL,
    adults INT(1) NOT NULL,
    childs INT(1) NOT NULL
);

CREATE TABLE messages (
    id VARCHAR(20) NOT NULL,
    name VARCHAR(50) NOT NULL,
    email VARCHAR(50) NOT NULL,
    number VARCHAR(10) NOT NULL,
    message VARCHAR(1000) NOT NULL
);