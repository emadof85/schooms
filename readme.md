# Schooms: A school management system

## Description
A comprehensive school management system designed to streamline administrative tasks, manage student records, track attendance, handle exams and grades, process payments, and more.

## Features
- Student and Staff Record Management
- Attendance Tracking
- Exam and Grade Management
- Payment Processing
- Timetable Scheduling
- Book and Library Management
- Dormitory Management
- User Authentication and Authorization
- Multi-language Support
- PDF Generation for Reports

## Installation

1. Clone the repository:
   ```
   git clone <repository-url>
   cd lav_sms-master
   ```

2. Install PHP dependencies:
   ```
   composer install
   ```

3. Copy the environment file and configure it:
   ```
   cp .env.example .env
   ```

4. Generate application key:
   ```
   php artisan key:generate
   ```

5. Run database migrations:
   ```
   php artisan migrate
   ```

6. Seed the database with initial data:
   ```
   php artisan db:seed
   ```

7. (Optional) Install Node.js dependencies and build assets:
   ```
   npm install
   npm run dev
   ```

## Usage

Start the development server:
```
php artisan serve
```

Access the application at `http://localhost:8000`.

## Requirements
- PHP ^7.2 or ^8.0
- Composer
- MySQL or compatible database
- Node.js and npm (for asset compilation)

## License
This project is licensed under the MIT License.
