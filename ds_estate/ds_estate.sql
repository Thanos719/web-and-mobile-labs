-- Active: 1718630965824@@127.0.0.1@3306@ds_estate
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    surname VARCHAR(255) NOT NULL,
    username VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE
);
CREATE TABLE listings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    photo VARCHAR(255),
    title VARCHAR(255) NOT NULL,
    area VARCHAR(255) NOT NULL,
    number_of_rooms INT NOT NULL,
    price_per_night INT NOT NULL,
    user_id INT,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
CREATE TABLE reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    listing_id INT,
    user_id INT,
    start_date DATE,
    end_date DATE,
    amount DECIMAL(10, 2),
    FOREIGN KEY (listing_id) REFERENCES listings(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);
