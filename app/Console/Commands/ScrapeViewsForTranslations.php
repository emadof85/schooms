<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ScrapeViewsForTranslations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translations:scrape-views';
    
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scrapes all blade files for text and generates a language file array.';
    
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
        $this->info("Scanning for untranslated strings in: " . $viewPath);
        
        // Get all blade files using our reliable custom function
        $files = $this->getBladeFilesRecursively($viewPath);
        
        if (empty($files)) {
            $this->warn("No .blade.php files were found in the specified path.");
            return 1;
        }
        
        $allStrings = [];
        $pattern = '/(?<=>)([^<]+)(?=<\/)|(?:placeholder|title|alt)\s*=\s*["\']([^"\']+)["\']/';
        
        foreach ($files as $filePath) {
            $content = file_get_contents($filePath);
            
            // Ignore strings already in translation functions
            $content = preg_replace("/__\((['\"]).*?(['\"])\)/", '', $content);
            $content = preg_replace("/@lang\((['\"]).*?(['\"])\)/", '', $content);
            $content = preg_replace('/<!--.*?-->/s', '', $content);
            
            preg_match_all($pattern, $content, $matches);
            
            $foundStrings = array_merge($matches[1], $matches[2]);
            
            foreach ($foundStrings as $string) {
                $trimmedString = trim($string);
                
                if (
                    !empty($trimmedString) &&
                    !Str::contains($trimmedString, ['{{', '{!!', '@', '$', '=>']) &&
                    preg_match('/[a-zA-Z]/', $trimmedString)
                    ) {
                        $decodedString = html_entity_decode($trimmedString, ENT_QUOTES, 'UTF-8');
                        $normalizedString = preg_replace('/\s+/', ' ', $decodedString);
                        $allStrings[trim($normalizedString)] = true;
                    }
            }
        }
        
        if (empty($allStrings)) {
            $this->info("\nNo new untranslated strings found. Your views appear to be fully translated!");
            return 0;
        }
        
        ksort($allStrings);
        $outputArray = [];
        foreach (array_keys($allStrings) as $string) {
            $key = Str::slug($string, '_');
            $key = substr($key, 0, 60);
            
            if (isset($outputArray[$key])) {
                $key = $key . '_' . substr(md5($string), 0, 4);
            }
            
            $escapedString = str_replace("'", "\'", $string);
            $outputArray[$key] = $escapedString;
        }
        
        $this->line("\n// Add the following keys to your lang/en/messages.php file:");
        $this->line("// Then, replace the hardcoded text in your views with the __() helper.\n");
        
        foreach ($outputArray as $key => $value) {
            $this->line("    '{$key}' => '{$value}',");
        }
        
        $this->info("\n\nScraping complete! Add the generated array keys to your language file.");
        return 0;
    }
    
    /**
     * Recursively find all files with a .blade.php extension in a directory.
     *
     * @param string $dir
     * @return array
     */
    private function getBladeFilesRecursively(string $dir): array
    {
        $files = [];
        $items = scandir($dir);
        
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            
            $path = $dir . DIRECTORY_SEPARATOR . $item;
            
            if (is_dir($path)) {
                Log::info(print_r(['here 1'=>$path],true));
                $files = array_merge($files, $this->getBladeFilesRecursively($path));
            } elseif (str_ends_with($path, '.blade.php')) {
                Log::info(print_r(['here 2'=>$path],true));
                $files[] = $path;
            }
        }
        Log::info(print_r(['$files'=>$files],true));
        
        return $files;
    }
}