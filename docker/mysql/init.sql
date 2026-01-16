-- MySQL Initialization Script
-- This script runs automatically when the container is first created

-- Ensure the database exists
CREATE DATABASE IF NOT EXISTS school_manager;

-- Grant all privileges to the user on the database
GRANT ALL PRIVILEGES ON school_manager.* TO 'school_user'@'%';

-- Flush privileges to ensure they take effect
FLUSH PRIVILEGES;

-- Show grants for verification
SHOW GRANTS FOR 'school_user'@'%';
