<?php

namespace Jiangjiangdev\WmAuto\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AdminCreateCommand extends Command
{
    protected static $defaultName = 'auto:admin';
    protected static $defaultDescription = 'Auto create all admin files';

    private const STUB_PATH = __DIR__ . '/../stubs/admin/';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // 先檢查一下檔案是否存在
        $isControllerExists = file_exists(base_path() . "/app/controller/admin/AuthController.php");
        if ($isControllerExists) {
            $output->writeln("<error>Files already exists.</error>");
            return self::FAILURE;
        }

        $output->writeln("<info>Creating admin files ... (Tailwindcss、DaisyUI)</info>");

        // 創建 Admin Middleware
        $this->createMiddleware();

        // 創建 Admin Controller
        $this->createController();

        // 創建 Admin View
        $this->createView();

        // 在 route.php 中最後一行添加路徑
        $this->addRoute();

        $output->writeln("<info>Creating auth files ... (Tailwindcss、DaisyUI) V</info>");

        return self::SUCCESS;
    }

    // 創建 Middleware
    private function createMiddleware(): bool
    {
        // 檔名
        $fileName = "IsAdminMiddleware.php";
        // 模板內容
        $stubContent = file_get_contents(self::STUB_PATH . 'middleware.stub');

        // 創建檔案
        $targetFolderPath = base_path() . "/app/middleware/$fileName";
        $this->checkAndCreateFile($targetFolderPath, $stubContent);

        return true;
    }

    // 創建 Controller
    private function createController(): bool
    {
        // 檔名
        $fileName = "IndexController.php";
        // 模板內容
        $stubContent = file_get_contents(self::STUB_PATH . 'controller.stub');

        // 創建檔案
        $targetFolderPath = base_path() . "/app/controller/admin/$fileName";
        $this->checkAndCreateFile($targetFolderPath, $stubContent);

        return true;
    }

    // 創建視圖
    private function createView(): bool
    {
        // 檔名
        $fileName = "admin.blade.php";
        // 模板內容
        $stubContent = file_get_contents(self::STUB_PATH . 'layout.stub');
        // 創建檔案
        $targetFolderPath = base_path() . "/app/view/layout/$fileName";
        $this->checkAndCreateFile($targetFolderPath, $stubContent);

        // 檔名
        $fileName = "adminNavbar.blade.php";
        // 模板內容
        $stubContent = file_get_contents(self::STUB_PATH . 'navbar.stub');
        // 創建檔案
        $targetFolderPath = base_path() . "/app/view/layout/$fileName";
        $this->checkAndCreateFile($targetFolderPath, $stubContent);

        // 檔名
        $fileName = "adminScript.blade.php";
        // 模板內容
        $stubContent = file_get_contents(self::STUB_PATH . 'script.stub');
        // 創建檔案
        $targetFolderPath = base_path() . "/app/view/layout/$fileName";
        $this->checkAndCreateFile($targetFolderPath, $stubContent);

        // 檔名
        $fileName = "index.blade.php";
        // 模板內容
        $stubContent = file_get_contents(self::STUB_PATH . 'index.stub');
        // 創建檔案
        $targetFolderPath = base_path() . "/app/view/page/admin/$fileName";
        $this->checkAndCreateFile($targetFolderPath, $stubContent);

        return true;
    }

    // 在 route.php 中最後一行添加路徑
    private function addRoute(): bool
    {
        $routeContent = file_get_contents(base_path() . "/config/route.php");

        $routeContent .= "\nRoute::get('/admin', [AdminIndexController::class, 'index'])->middleware([app\middleware\AuthMiddleware::class,app\middleware\IsAdminMiddleware::class])->name('admin.index');";

        $fileHandle = fopen(base_path() . "/config/route.php", 'w');
        if ($fileHandle) {
            // 在 use Webman\Route; 這行的下一行插入 Class
            $useClass = "use app\\controller\\admin\\IndexController as AdminIndexController;";
            $routeContent = str_replace("use Webman\\Route;", "use Webman\\Route;\n" . $useClass, $routeContent);
            fwrite($fileHandle, $routeContent);
            fclose($fileHandle);
        } else {
            echo "Route added failed.\n";
        }

        return true;
    }

    // 檢查並創建檔案
    private function checkAndCreateFile(string $fullPath, string $content): bool
    {
        // 檢查檔案是否存在
        if (file_exists($fullPath)) {
            echo "File already exists: $fullPath\n";
            return false;
        }

        // 取得資料夾路徑
        $directoryPath = dirname($fullPath);

        // 檢查資料夾是否存在，如果不存在則創建資料夾
        if (!is_dir($directoryPath)) {
            mkdir($directoryPath, 0777, true);
            echo "資料夾已創建: $directoryPath\n";
        }

        // 創建空檔案
        $fileHandle = fopen($fullPath, 'w');
        if ($fileHandle) {
            fwrite($fileHandle, $content);
            fclose($fileHandle);
            echo "檔案已創建: $fullPath\n";
        } else {
            echo "檔案創建失敗: $fullPath\n";
        }

        return true;
    }
}
