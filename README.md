# Student Attendance Management System

A simple **DBMS mini project** built with **HTML, CSS, JavaScript, PHP, and MySQL**. This project helps manage students, mark daily attendance, view attendance reports, calculate attendance percentage, and identify students with attendance below 75%.

## Features

- Dashboard with total students, today's present/absent count, and recent attendance records
- Add, edit, search, and delete students
- Student identification using **USN**
- Mark attendance as **Present** or **Absent**
- Mark all visible students as present/absent
- Search and filter students while marking attendance
- Attendance report with date and department filters
- Attendance graph showing present and absent records by date
- Attendance percentage calculation for each student
- Low attendance warning for students below **75%**
- Export current attendance report as CSV
- Import attendance from CSV
- MySQL database with foreign key relationship between students and attendance

## Tech Stack

- **Frontend:** HTML, CSS, JavaScript
- **Backend:** PHP
- **Database:** MySQL
- **Local Server:** XAMPP

## Project Structure

```text
attendance_system/
├── api/
│   ├── attendance.php          # API for marking and fetching attendance
│   ├── db.php                  # Database connection file
│   ├── import_attendance.php   # API for importing attendance CSV data
│   └── students.php            # API for student CRUD operations
├── attendance.html             # Mark attendance page
├── database.sql                # Database setup file
├── index.html                  # Dashboard page
├── report.html                 # Attendance report, graph, import/export page
├── students.html               # Student management page
└── style.css                   # Main styling file
```

## Requirements

Before running the project, install:

- [XAMPP](https://www.apachefriends.org/download.html)
- Web browser such as Chrome or Microsoft Edge

## How to Run Locally

### 1. Start XAMPP

Open XAMPP Control Panel and start:

- **Apache**
- **MySQL**

### 2. Move the Project Folder

Copy the `attendance_system` folder into:

```text
C:\xampp\htdocs\
```

Your final path should look like this:

```text
C:\xampp\htdocs\attendance_system
```

### 3. Import the Database

Open your browser and go to:

```text
http://localhost/phpmyadmin
```

Then follow these steps:

1. Click **Import**.
2. Click **Choose File**.
3. Select `database.sql` from:

```text
C:\xampp\htdocs\attendance_system\database.sql
```

4. Scroll down and click **Import**.

This will create the database named:

```text
attendance_db
```

### 4. Open the Project

Go to:

```text
http://localhost/attendance_system/index.html
```

## Database Details

The project uses a MySQL database named `attendance_db`.

### Tables

#### `students`

Stores student details.

| Column | Description |
|---|---|
| `student_id` | Internal auto-increment student ID |
| `usn` | Unique student USN |
| `name` | Student name |
| `department` | Student department |

#### `attendance`

Stores attendance records.

| Column | Description |
|---|---|
| `attendance_id` | Internal auto-increment attendance ID |
| `student_id` | Foreign key connected to students table |
| `date` | Attendance date |
| `status` | Present or Absent |

The attendance table uses a unique key on `student_id` and `date`, so one student can have only one attendance record per day. If attendance is marked again for the same student and date, the existing record is updated.

## CSV Import Format

To import attendance, use a CSV file with these columns:

```csv
USN,Date,Status
1SP24CS001,2026-05-17,Present
1SP24AI046,2026-05-17,Absent
```

Rules:

- Date must be in `YYYY-MM-DD` format.
- Status must be either `Present` or `Absent`.
- USN must already exist in the students table.
- Existing records for the same USN and date will be updated.

## Pages Overview

### Dashboard

The dashboard shows:

- Total students
- Present today
- Absent today
- Total attendance records
- Recent attendance records

### Students Page

The students page allows you to:

- Add new students
- Edit student details
- Delete students
- Search students by USN, name, or department

### Mark Attendance Page

The attendance page allows you to:

- Select a date
- Search students
- Filter students by department
- Mark individual students as present or absent
- Mark all visible students at once

### Report Page

The report page includes:

- Attendance records table
- Filter by available attendance dates
- Filter by available departments
- Attendance graph
- Attendance percentage table
- Below 75% warning
- CSV export
- CSV import

## Default Database Login

The database connection uses the default XAMPP MySQL settings:

```php
$host = "localhost";
$user = "root";
$password = "";
$database = "attendance_db";
```

If your MySQL password is different, update it in:

```text
api/db.php
```

## Common Issues and Fixes

### Page opens but data does not load

Make sure Apache and MySQL are running in XAMPP.

### Database connection error

Check that:

- MySQL is running
- `database.sql` was imported correctly
- Database name is `attendance_db`
- MySQL username/password in `api/db.php` are correct

### Add student or attendance gives server error

Make sure the project folder is inside:

```text
C:\xampp\htdocs\attendance_system
```

Also make sure you are opening the project using:

```text
http://localhost/attendance_system/index.html
```

Do not open the HTML files directly by double-clicking them, because PHP APIs need Apache to run.

## Future Improvements

- Login system for admin/teacher
- Separate teacher dashboard
- Monthly attendance report
- PDF report generation
- Student-wise detailed attendance history
- Better chart library integration
- Role-based access control

## Author

Developed by **Sourav / Spp**.

## License

This project is created for educational purposes as a DBMS mini project.
