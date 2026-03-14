# Early Warning Academic Monitoring System - Specification

## 1. Project Overview
- **Project Name**: Early Warning Academic Monitoring System (EWAMS)
- **Type**: Web-based Academic Monitoring Platform
- **Core Functionality**: Monitor student academic performance, identify at-risk students early, and enable timely intervention
- **Target Users**: Administrators, Faculty, Advisors, Students

## 2. Technology Stack
- **Backend**: PHP 8+ (MVC Architecture)
- **Frontend**: HTML5, CSS3, JavaScript
- **Database**: MySQL
- **Authentication**: bcrypt password hashing
- **Data Import**: CSV support
- **Notifications**: Dashboard alerts, Email

## 3. Database Schema

### Users Table
```sql
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('administrator', 'faculty', 'advisor', 'student') NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active TINYINT(1) DEFAULT 1
);
```

### Academic Terms Table
```sql
CREATE TABLE academic_terms (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    is_current TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Programs Table
```sql
CREATE TABLE programs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(20) UNIQUE NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Courses Table
```sql
CREATE TABLE courses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(20) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    credits INT NOT NULL,
    program_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (program_id) REFERENCES programs(id)
);
```

### Sections Table
```sql
CREATE TABLE sections (
    id INT PRIMARY KEY AUTO_INCREMENT,
    course_id INT NOT NULL,
    term_id INT NOT NULL,
    instructor_id INT,
    section_number VARCHAR(20) NOT NULL,
    capacity INT DEFAULT 30,
    FOREIGN KEY (course_id) REFERENCES courses(id),
    FOREIGN KEY (term_id) REFERENCES academic_terms(id),
    FOREIGN KEY (instructor_id) REFERENCES users(id)
);
```

### Enrollments Table
```sql
CREATE TABLE enrollments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    section_id INT NOT NULL,
    enrollment_date DATE NOT NULL,
    status ENUM('active', 'withdrawn', 'completed') DEFAULT 'active',
    FOREIGN KEY (student_id) REFERENCES users(id),
    FOREIGN KEY (section_id) REFERENCES sections(id)
);
```

### Assessment Types Table
```sql
CREATE TABLE assessment_types (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    abbreviation VARCHAR(10) NOT NULL,
    weight DECIMAL(5,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Assessments Table
```sql
CREATE TABLE assessments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    section_id INT NOT NULL,
    assessment_type_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    max_score DECIMAL(10,2) NOT NULL,
    due_date DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (section_id) REFERENCES sections(id),
    FOREIGN KEY (assessment_type_id) REFERENCES assessment_types(id)
);
```

### Grades Table
```sql
CREATE TABLE grades (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    assessment_id INT NOT NULL,
    score DECIMAL(10,2),
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_late TINYINT(1) DEFAULT 0,
    FOREIGN KEY (student_id) REFERENCES users(id),
    FOREIGN KEY (assessment_id) REFERENCES assessments(id)
);
```

### Attendance Table
```sql
CREATE TABLE attendance (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    section_id INT NOT NULL,
    date DATE NOT NULL,
    status ENUM('present', 'absent', 'late', 'excused') DEFAULT 'present',
    FOREIGN KEY (student_id) REFERENCES users(id),
    FOREIGN KEY (section_id) REFERENCES sections(id)
);
```

### Risk Assessments Table
```sql
CREATE TABLE risk_assessments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    term_id INT NOT NULL,
    risk_score DECIMAL(5,2) DEFAULT 0,
    risk_level ENUM('low', 'moderate', 'high') DEFAULT 'low',
    grade_score DECIMAL(5,2) DEFAULT 0,
    attendance_score DECIMAL(5,2) DEFAULT 100,
    assignment_score DECIMAL(5,2) DEFAULT 100,
    calculated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES users(id),
    FOREIGN KEY (term_id) REFERENCES academic_terms(id)
);
```

### Alerts Table
```sql
CREATE TABLE alerts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    type ENUM('risk_increase', 'low_performance', 'critical_event', 'improvement') NOT NULL,
    title VARCHAR(100) NOT NULL,
    message TEXT,
    severity ENUM('info', 'warning', 'critical') DEFAULT 'info',
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES users(id)
);
```

### Interventions Table
```sql
CREATE TABLE interventions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    advisor_id INT,
    type VARCHAR(50) NOT NULL,
    description TEXT,
    outcome TEXT,
    follow_up_date DATE,
    status ENUM('planned', 'in_progress', 'completed', 'cancelled') DEFAULT 'planned',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES users(id),
    FOREIGN KEY (advisor_id) REFERENCES users(id)
);
```

### Notifications Table
```sql
CREATE TABLE notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    type VARCHAR(50) NOT NULL,
    title VARCHAR(100) NOT NULL,
    message TEXT,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

## 4. UI/UX Design

### Color Scheme
- **Primary**: #1e3a5f (Deep Navy Blue)
- **Secondary**: #3498db (Bright Blue)
- **Accent**: #e74c3c (Alert Red)
- **Success**: #27ae60 (Green)
- **Warning**: #f39c12 (Orange/Yellow)
- **Background**: #f5f7fa (Light Gray)
- **Card Background**: #ffffff (White)
- **Text Primary**: #2c3e50 (Dark Gray)
- **Text Secondary**: #7f8c8d (Medium Gray)

### Risk Level Colors
- **Low Risk (Green)**: #27ae60
- **Moderate Risk (Yellow)**: #f39c12
- **High Risk (Red)**: #e74c3c

### Typography
- **Font Family**: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif
- **Headings**: Bold, various sizes
- **Body**: Regular, 14-16px

### Layout
- **Sidebar Navigation**: Fixed left sidebar (250px)
- **Header**: Fixed top with user info and notifications
- **Main Content**: Fluid width with padding
- **Cards**: Rounded corners (8px), subtle shadows

### Responsive Breakpoints
- **Desktop**: > 1200px
- **Tablet**: 768px - 1200px
- **Mobile**: < 768px

## 5. Core Features

### Authentication
- Login page with username/email and password
- bcrypt password hashing
- Session management
- Logout functionality

### Role-Based Access
- **Administrator**: Full system access, user management, reports
- **Faculty**: Course management, grade entry, student monitoring
- **Advisor**: Student caseload, interventions, communication
- **Student**: Personal dashboard, grades view, deadlines

### Student Management
- Individual student profiles
- Bulk import via CSV
- Academic program tracking
- Enrollment history

### Course Management
- Academic term configuration
- Course catalog
- Section creation and instructor assignment
- Student enrollment

### Grade Management
- Configurable assessment types with weights
- Weighted grade calculation
- Grade trend visualization
- Individual and bulk grade entry

### Risk Assessment Engine
- Automated risk score calculation
- Risk indicators: grades, attendance, assignment submissions
- Color-coded risk levels
- Historical risk tracking

### Alert System
- Automated alerts on risk changes
- Dashboard notifications
- Email notifications
- Alert history and tracking

### Dashboards
- **Faculty**: Course summaries, at-risk students, grade distribution
- **Advisor**: Caseload overview, risk prioritization, intervention tracking
- **Student**: Personal risk indicator, grades, deadlines
- **Administrator**: System overview, user management, reports

### Reports
- At-risk student reports
- Intervention summaries
- Course performance profiles
- Export to PDF/Excel

## 6. File Structure
```
/opencode-php
├── config/
│   ├── database.php
│   └── config.php
├── models/
│   ├── User.php
│   ├── Student.php
│   ├── Course.php
│   ├── Grade.php
│   ├── RiskAssessment.php
│   └── Alert.php
├── controllers/
│   ├── AuthController.php
│   ├── DashboardController.php
│   ├── StudentController.php
│   ├── CourseController.php
│   ├── GradeController.php
│   ├── RiskController.php
│   └── ReportController.php
├── views/
│   ├── layouts/
│   │   ├── header.php
│   │   ├── sidebar.php
│   │   └── footer.php
│   ├── auth/
│   │   ├── login.php
│   │   └── register.php
│   ├── dashboard/
│   │   ├── admin.php
│   │   ├── faculty.php
│   │   ├── advisor.php
│   │   └── student.php
│   ├── students/
│   ├── courses/
│   ├── grades/
│   └── reports/
├── public/
│   ├── css/
│   │   ├── style.css
│   │   └── charts.css
│   ├── js/
│   │   ├── main.js
│   │   └── charts.js
│   └── uploads/
├── index.php
├── .htaccess
└── README.md
```

## 7. Acceptance Criteria

### Authentication
- [ ] Users can register (admin only) and login
- [ ] Passwords are securely hashed with bcrypt
- [ ] Session persists across pages
- [ ] Role-based redirects after login

### Student Management
- [ ] Create, edit, deactivate student accounts
- [ ] Bulk import students via CSV
- [ ] View individual student profiles with academic history

### Course Management
- [ ] Create and manage academic terms
- [ ] Create courses and sections
- [ ] Assign instructors to sections
- [ ] Enroll students in sections

### Grade Management
- [ ] Create assessments with types and weights
- [ ] Enter and update grades
- [ ] Calculate weighted grades automatically
- [ ] Display grade trends

### Risk Assessment
- [ ] Calculate risk scores automatically
- [ ] Display color-coded risk levels
- [ ] Update risk on grade/attendance changes

### Alerts
- [ ] Generate alerts on risk changes
- [ ] Display alerts on dashboard
- [ ] Mark alerts as read

### Dashboards
- [ ] Role-specific dashboards load correctly
- [ ] Data visualizations render properly
- [ ] Navigation works for all roles
