-- Q18: Complaint Management System
CREATE DATABASE IF NOT EXISTS complaint_mgmt_db;
USE complaint_mgmt_db;

CREATE TABLE IF NOT EXISTS complaints (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    organization ENUM('PMC','PMT','Hospital','University','Bank','Other') NOT NULL,
    complaint_type VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    status ENUM('Open','In Review','Resolved','Closed') DEFAULT 'Open',
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
