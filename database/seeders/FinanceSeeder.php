<?php
// database/seeders/FinanceSeeder.php

use Illuminate\Database\Seeder;
use App\Models\SalaryLevel;
use App\Models\IncomeCategory;
use App\Models\ExpenseCategory;

class FinanceSeeder extends Seeder
{
    public function run()
    {
        // Salary Levels
        $salaryLevels = [
            ['name' => 'Principal', 'user_type_id' => '1', 'base_salary' => 500000],
            ['name' => 'Senior Teacher', 'user_type_id' => '2', 'base_salary' => 300000],
            ['name' => 'Teacher', 'user_type_id' => '3', 'base_salary' => 200000],
            ['name' => 'Accountant', 'user_type_id' => '4', 'base_salary' => 250000],
            ['name' => 'Driver', 'user_type_id' => '5', 'base_salary' => 80000],
        ];
        
        foreach ($salaryLevels as $level) {
            SalaryLevel::create($level);
        }
        
        // Income Categories
        $incomeCategories = [
            ['name' => 'School Fees', 'code' => 'FEES'],
            ['name' => 'Admission Fees', 'code' => 'ADM'],
            ['name' => 'Exam Fees', 'code' => 'EXAM'],
            ['name' => 'Transport Fees', 'code' => 'TRANS'],
            ['name' => 'Donations', 'code' => 'DON'],
            ['name' => 'Grants', 'code' => 'GRANT'],
        ];
        
        foreach ($incomeCategories as $category) {
            IncomeCategory::create($category);
        }
        
        // Expense Categories
        $expenseCategories = [
            ['name' => 'Salaries', 'code' => 'SAL'],
            ['name' => 'Utilities', 'code' => 'UTIL'],
            ['name' => 'Maintenance', 'code' => 'MAINT'],
            ['name' => 'Stationery', 'code' => 'STAT'],
            ['name' => 'Sports Equipment', 'code' => 'SPORT'],
            ['name' => 'Library Books', 'code' => 'BOOK'],
        ];
        
        foreach ($expenseCategories as $category) {
            ExpenseCategory::create($category);
        }
    }
}