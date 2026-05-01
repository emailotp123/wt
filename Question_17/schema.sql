-- Q17: Waste Collection System
CREATE DATABASE IF NOT EXISTS waste_db;
USE waste_db;

CREATE TABLE IF NOT EXISTS waste_reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reporter_name VARCHAR(100) NOT NULL,
    location VARCHAR(255) NOT NULL,
    waste_type ENUM('Plastic','Paper','Metal','Organic','Mixed','Other') NOT NULL,
    description TEXT,
    status ENUM('Pending','Assigned','Collected') DEFAULT 'Pending',
    reported_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
