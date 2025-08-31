-- Database setup for Hexagon Trading Institute
-- Create database
CREATE DATABASE IF NOT EXISTS hexagon_trading CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE hexagon_trading;

-- Contact submissions table
CREATE TABLE IF NOT EXISTS contact_submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    phone VARCHAR(20),
    course_interest VARCHAR(50) NOT NULL,
    message TEXT,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('new', 'contacted', 'enrolled', 'closed') DEFAULT 'new',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Users table (for future user registration)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    phone VARCHAR(20),
    password_hash VARCHAR(255) NOT NULL,
    email_verified BOOLEAN DEFAULT FALSE,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Courses table
CREATE TABLE IF NOT EXISTS courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    duration_weeks INT NOT NULL,
    level ENUM('beginner', 'intermediate', 'advanced') NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Course enrollments table
CREATE TABLE IF NOT EXISTS course_enrollments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    course_id INT NOT NULL,
    enrollment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completion_date TIMESTAMP NULL,
    status ENUM('enrolled', 'in_progress', 'completed', 'dropped') DEFAULT 'enrolled',
    progress_percentage DECIMAL(5, 2) DEFAULT 0.00,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    UNIQUE KEY unique_enrollment (user_id, course_id)
);

-- Trading performance table (for tracking student results)
CREATE TABLE IF NOT EXISTS trading_performance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    trade_date DATE NOT NULL,
    symbol VARCHAR(20) NOT NULL,
    trade_type ENUM('buy', 'sell') NOT NULL,
    entry_price DECIMAL(10, 4) NOT NULL,
    exit_price DECIMAL(10, 4),
    quantity DECIMAL(15, 4) NOT NULL,
    profit_loss DECIMAL(15, 2),
    status ENUM('open', 'closed') DEFAULT 'open',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Newsletter subscribers table
CREATE TABLE IF NOT EXISTS newsletter_subscribers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(150) UNIQUE NOT NULL,
    name VARCHAR(100),
    subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE,
    unsubscribed_at TIMESTAMP NULL
);

-- Insert sample courses
INSERT INTO courses (title, description, price, duration_weeks, level) VALUES
('Beginner Trading Fundamentals', 'Learn the basics of trading, market analysis, and risk management', 299.00, 8, 'beginner'),
('Advanced Technical Analysis', 'Master advanced charting techniques and trading strategies', 599.00, 12, 'advanced'),
('Cryptocurrency Trading Mastery', 'Complete guide to trading Bitcoin, Ethereum, and altcoins', 499.00, 10, 'intermediate'),
('Forex Trading Bootcamp', 'Intensive course on foreign exchange trading', 699.00, 16, 'intermediate'),
('Risk Management & Psychology', 'Learn to manage risk and master trading psychology', 399.00, 6, 'beginner');

-- Insert sample success stories data (for display purposes)
CREATE TABLE IF NOT EXISTS success_stories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    role VARCHAR(100) NOT NULL,
    quote TEXT NOT NULL,
    roi_percentage DECIMAL(5, 2) NOT NULL,
    image_url VARCHAR(255),
    is_featured BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO success_stories (name, role, quote, roi_percentage, is_featured) VALUES
('John Smith', 'Professional Trader', 'Hexagon Trading Institute transformed my trading career. I went from losing money to consistent 15% monthly returns.', 247.00, TRUE),
('Sarah Johnson', 'Day Trader', 'The strategies I learned here are game-changing. My win rate improved from 40% to 78% in just 3 months.', 189.00, TRUE),
('Mike Chen', 'Swing Trader', 'From complete beginner to profitable trader in 6 months. The mentorship program is incredible.', 156.00, TRUE);

-- Create indexes for better performance
CREATE INDEX idx_contact_submissions_email ON contact_submissions(email);
CREATE INDEX idx_contact_submissions_status ON contact_submissions(status);
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_course_enrollments_user ON course_enrollments(user_id);
CREATE INDEX idx_trading_performance_user ON trading_performance(user_id);
CREATE INDEX idx_trading_performance_date ON trading_performance(trade_date);
