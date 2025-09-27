<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class TranslationsReplaceStrings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translations:replace';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically replaces hardcoded strings in blade files with translation helpers.';

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
        $this->warn('!!! IMPORTANT !!!');
        $this->warn('This command will modify your blade files in place.');
        $this->warn('Please make sure your project is under version control (like Git) and that you have committed all your changes before proceeding.');
        
        if (!$this->confirm('Do you wish to continue?', false)) {
            $this->info('Operation cancelled.');
            return 0;
        }
        
        $langFilePath = lang_path('en\msg.php');
        if (!File::exists($langFilePath)) {
            $this->error("Language file not found at: {$langFilePath}");
            $this->warn("Please run 'php artisan translations:find' first to generate the file.");
            return 1;
        }
        
        // Require the file to get the array of translations
        $translations = require($langFilePath);
        
        // Sort translations by length descending to replace longer strings first
        // This prevents "My profile" from being replaced inside "View My profile"
        uksort($translations, function ($a, $b) {
            return strlen($b) - strlen($a);
        });
            
            $viewPath = resource_path('views');
            $bladeFiles = $this->getBladeFilesRecursively($viewPath);
            
            $totalReplacements = 0;
            $filesModified = 0;
            
            $progressBar = $this->output->createProgressBar(count($bladeFiles));
            $progressBar->start();
            
            foreach ($bladeFiles as $filePath) {
                $content = File::get($filePath);
                $originalContent = $content;
                
                foreach ($translations as $key => $value) {
                    if (empty(trim($value))) continue;
                    
                    $escapedValue = preg_quote($value, '/');
                    
                    // Pattern 1: Replace text between HTML tags >Text<
                    $pattern1 = "/>(\s*)" . $escapedValue . "(\s*)</";
                    $replacement1 = ">$1{{ __('msg.{$key}') }}$2<";
                    $content = preg_replace($pattern1, $replacement1, $content, -1, $count1);
                    
                    // Pattern 2: Replace text in placeholder, title, or alt attributes
                    $pattern2 = '/(placeholder|title|alt)=["\'](\s*)' . $escapedValue . '(\s*)["\']/';
                    $replacement2 = '\1="{{ __(\'msg.' . $key . '\') }}"';
                    $content = preg_replace($pattern2, $replacement2, $content, -1, $count2);
                    
                    $totalReplacements += ($count1 + $count2);
                }
                
                if ($content !== $originalContent) {
                    File::put($filePath, $content);
                    $filesModified++;
                }
                
                $progressBar->advance();
            }
            
            $progressBar->finish();
            $this->info("\n\nOperation Complete.");
            $this->info("Made a total of {$totalReplacements} replacements in {$filesModified} files.");
            
            return 0;
    }
    
    private function getBladeFilesRecursively(string $dir): array
    {
        $files = [];
        $items = scandir($dir);
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;
            $path = $dir . DIRECTORY_SEPARATOR . $item;
            if (is_dir($path)) {
                $files = array_merge($files, $this->getBladeFilesRecursively($path));
            } elseif (str_ends_with($path, '.blade.php')) {
                $files[] = $path;
            }
        }
        return $files;
    }
}
