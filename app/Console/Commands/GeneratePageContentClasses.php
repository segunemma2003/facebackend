<?php
// app/Console/Commands/GeneratePageContentClasses.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;

class GeneratePageContentClasses extends Command
{
    protected $signature = 'make:page-content-classes';
    protected $description = 'Generate all page content resource classes';

    protected $files;

    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    public function handle()
    {
        $pages = [
            'Homepage' => 'homepage',
            'Nominees' => 'nominees',
            'Categories' => 'categories',
            'PastWinners' => 'past_winners',
            'Gallery' => 'gallery',
            'Approach' => 'approach',
            'About' => 'about',
            'Contact' => 'contact',
            'Footer' => 'footer'
        ];

        foreach ($pages as $className => $pageName) {
            $this->generatePageClasses($className, $pageName);
        }

        $this->info('All page content classes generated successfully!');
    }

    protected function generatePageClasses($className, $pageName)
    {
        $resourceName = $className . 'Content';
        $directory = app_path("Filament/Resources/{$resourceName}Resource/Pages");

        // Create directory if it doesn't exist
        if (!$this->files->isDirectory($directory)) {
            $this->files->makeDirectory($directory, 0755, true);
        }

        // Generate List page
        $this->generateListPage($className, $resourceName, $directory);

        // Generate Create page
        $this->generateCreatePage($className, $resourceName, $directory);

        // Generate Edit page
        $this->generateEditPage($className, $resourceName, $directory);

        $this->info("Generated classes for {$className} page");
    }

    protected function generateListPage($className, $resourceName, $directory)
    {
        $content = "<?php

namespace App\\Filament\\Resources\\{$resourceName}Resource\\Pages;

use App\\Filament\\Resources\\{$resourceName}Resource;
use Filament\\Actions;
use Filament\\Resources\\Pages\\ListRecords;

class List{$className}Contents extends ListRecords
{
    protected static string \$resource = {$resourceName}Resource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\\CreateAction::make(),
        ];
    }
}";

        $this->files->put($directory . "/List{$className}Contents.php", $content);
    }

    protected function generateCreatePage($className, $resourceName, $directory)
    {
        $content = "<?php

namespace App\\Filament\\Resources\\{$resourceName}Resource\\Pages;

use App\\Filament\\Resources\\{$resourceName}Resource;
use Filament\\Resources\\Pages\\CreateRecord;

class Create{$className}Content extends CreateRecord
{
    protected static string \$resource = {$resourceName}Resource::class;
}";

        $this->files->put($directory . "/Create{$className}Content.php", $content);
    }

    protected function generateEditPage($className, $resourceName, $directory)
    {
        $content = "<?php

namespace App\\Filament\\Resources\\{$resourceName}Resource\\Pages;

use App\\Filament\\Resources\\{$resourceName}Resource;
use Filament\\Actions;
use Filament\\Resources\\Pages\\EditRecord;

class Edit{$className}Content extends EditRecord
{
    protected static string \$resource = {$resourceName}Resource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\\DeleteAction::make(),
        ];
    }
}";

        $this->files->put($directory . "/Edit{$className}Content.php", $content);
    }
}
