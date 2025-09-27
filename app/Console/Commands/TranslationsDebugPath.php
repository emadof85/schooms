<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TranslationsDebugPath extends Command
{
    protected $signature = 'translations:debug-path';
    protected $description = 'Diagnoses read access to the resources/views directory.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $viewPath = resource_path('views');
        $this->line("--- Directory Access Diagnostic ---");
        $this->line("Checking path: " . $viewPath);
        
        // Check 1: Does the directory exist?
        if (!is_dir($viewPath)) {
            $this->error("Result: FAILED. The path is not a directory.");
            return 1;
        }
        $this->info("Result: OK. The path is a valid directory.");
        
        // Check 2: Is the directory readable?
        if (!is_readable($viewPath)) {
            $this->error("Result: FAILED. The directory is not readable by the PHP process.");
            $this->warn("This is likely a file system permissions issue. Please check the security settings of the folder.");
            return 1;
        }
        $this->info("Result: OK. The directory is readable.");
        
        // Check 3: Can we list the contents?
        $this->line("\nAttempting to list contents using scandir():");
        $contents = scandir($viewPath);
        
        if ($contents === false) {
            $this->error("Result: FAILED. scandir() could not read the directory contents.");
            return 1;
        }
        
        if (count($contents) <= 2) { // Only contains '.' and '..'
            $this->warn("Result: The directory appears to be empty.");
        } else {
            $this->info("Result: OK. Directory contents listed successfully:");
            print_r($contents);
        }
        
        $this->line("\n--- Diagnostic Complete ---");
        return 0;
    }
}
