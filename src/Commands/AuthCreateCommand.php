<?php

namespace Jiangjiangdev\WmAuto\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AuthCreateCommand extends Command
{
    protected static $defaultName = 'auto:auth';
    protected static $defaultDescription = 'Auto create all auth files';

    private const STUB_PATH = __DIR__ . '/../stubs/auth/';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // 先檢查一下檔案是否存在
        $isModelExists = file_exists($filePath = base_path() . "/app/model/User.php");
        $isMiddlewareExists = file_exists($filePath = base_path() . "/app/middleware/AuthMiddleware.php");
        $isControllerExists = file_exists($filePath = base_path() . "/app/controller/AuthController.php");
        $isRepositoryExists = file_exists($filePath = base_path() . "/app/repository/UserRepository.php");
        if ($isMiddlewareExists || $isControllerExists || $isModelExists || $isRepositoryExists) {
            $output->writeln("<error>Files already exists.</error>");
            return self::FAILURE;
        }

        $output->writeln("<info>Creating auth files ... (Tailwindcss)</info>");

        // 創建 User Migration
        $this->createMigration();

        // 創建 User Model
        $this->createModel();

        // 創建 Auth Middleware
        $this->createMiddleware();

        // 創建 User Repository
        $this->createRepository();

        // 創建 ResponseFormat
        $this->createResponseFormat();

        // 創建 Auth Controller
        $this->createController();

        // 創建 Validation
        $this->createValidation();

        // 創建 Auth View
        $this->createView();

        // 在 route.php 中最後一行添加路徑
        $this->addRoute();

        $output->writeln("<info>Creating auth files ... V</info>");

        return self::SUCCESS;
    }

    // 創建 Migration
    private function createMigration(): bool
    {
        // 檔名
        $fileName = date('YmdHis') . "_create_user_table.php";
        // 模板內容
        $stubContent = file_get_contents(self::STUB_PATH . 'migration.stub');

        // 創建 Migration
        $targetFolderPath = base_path() . "/db/migrations/$fileName";
        $this->checkAndCreateFile($targetFolderPath, $stubContent);

        return true;
    }

    // 創建 Model
    private function createModel(): bool
    {
        // 檔名
        $fileName = "User.php";
        // 模板內容
        $stubContent = file_get_contents(self::STUB_PATH . 'model.stub');

        // 創建 Model
        $targetFolderPath = base_path() . "/app/model/$fileName";
        $this->checkAndCreateFile($targetFolderPath, $stubContent);

        return true;
    }

    // 創建 Repository
    private function createRepository(): bool
    {
        // 檔名
        $fileName = "UserRepository.php";
        // 模板內容
        $stubContent = file_get_contents(self::STUB_PATH . 'repository.stub');

        // 創建檔案
        $targetFolderPath = base_path() . "/app/repository/$fileName";
        $this->checkAndCreateFile($targetFolderPath, $stubContent);

        return true;
    }

    // 創建 Middleware
    private function createMiddleware(): bool
    {
        // 檔名
        $fileName = "AuthMiddleware.php";
        // 模板內容
        $stubContent = file_get_contents(self::STUB_PATH . 'middleware.stub');

        // 創建檔案
        $targetFolderPath = base_path() . "/app/middleware/$fileName";
        $this->checkAndCreateFile($targetFolderPath, $stubContent);

        return true;
    }

    // 創建 ResponseFormat
    private function createResponseFormat(): bool
    {
        // 檔名
        $fileName = "ResponseFormat.php";
        // 模板內容
        $stubContent = file_get_contents(self::STUB_PATH . 'responseFormat.stub');

        // 創建檔案
        $targetFolderPath = base_path() . "/app/presenter/format/$fileName";
        $this->checkAndCreateFile($targetFolderPath, $stubContent);

        return true;
    }

    // 創建 Controller
    private function createController(): bool
    {
        // 檔名
        $fileName = "AuthController.php";
        // 模板內容
        $stubContent = file_get_contents(self::STUB_PATH . 'controller.stub');

        // 創建檔案
        $targetFolderPath = base_path() . "/app/controller/$fileName";
        $this->checkAndCreateFile($targetFolderPath, $stubContent);

        return true;
    }

    // 創建 Validation
    private function createValidation(): bool
    {
        // 檔名
        $fileName = "LoginPostValidation.php";
        // 模板內容
        $stubContent = file_get_contents(self::STUB_PATH . 'loginPostValidation.stub');

        // 創建檔案
        $targetFolderPath = base_path() . "/app/validation/$fileName";
        $this->checkAndCreateFile($targetFolderPath, $stubContent);

        return true;
    }

    // 創建視圖
    private function createView(): bool
    {
        // 檔名
        $fileName = "login.blade.php";
        // 模板內容
        $stubContent = file_get_contents(self::STUB_PATH . 'loginPage.stub');
        // 創建檔案
        $targetFolderPath = base_path() . "/app/view/page/auth/$fileName";
        $this->checkAndCreateFile($targetFolderPath, $stubContent);

        // 檔名
        $fileName = "empty.blade.php";
        // 模板內容
        $stubContent = file_get_contents(self::STUB_PATH . 'emptyLayout.stub');
        // 創建檔案
        $targetFolderPath = base_path() . "/app/view/layout/$fileName";
        $this->checkAndCreateFile($targetFolderPath, $stubContent);

        return true;
    }

    // 在 route.php 中最後一行添加路徑
    private function addRoute(): bool
    {
        $routeContent = file_get_contents(base_path() . "/config/route.php");

        $routeContent .= "\nRoute::get('/login', [AuthController::class, 'loginPage'])->name('loginPage');";
        $routeContent .= "\nRoute::post('/login', [AuthController::class, 'login'])->name('login');";

        $fileHandle = fopen(base_path() . "/config/route.php", 'w');
        if ($fileHandle) {
            // 在 use Webman\Route; 這行的下一行插入 Class
            $useClass = "use app\\controller\\AuthController;";
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
