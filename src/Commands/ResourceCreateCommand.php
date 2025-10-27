<?php

namespace Jiangjiangdev\WmAuto\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ResourceCreateCommand extends Command
{
    protected static $defaultName = 'auto:resource';
    protected static $defaultDescription = 'Auto create resource CRUD';

    private const STUB_PATH = __DIR__ . '/../stubs/app/';

    protected function configure()
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'Resource name');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getArgument('name');
        // 第一個字元大寫
        $modelName = ucfirst($name);

        $output->writeln("<info>Creating Resource $modelName ...</info>");
        // 先檢查一下模型檔案是否存在
        if (file_exists($filePath = base_path() . "/app/model/$modelName.php")) {
            $output->writeln("<error>Model $filePath already exists.</error>");
            return self::FAILURE;
        }

        // 創建 Migration
        $this->createMigration($modelName);

        // 創建 Model
        $this->createModel($modelName);

        // 創建 Repository
        $this->createRepository($modelName);

        // 創建 Controller
        $this->createController($modelName);

        // 在 route.php 中最後一行添加路徑
        $this->addRoute($modelName);

        return self::SUCCESS;
    }

    // 創建 Migration
    private function createMigration(string $modelName): bool
    {
        // 蛇形命名
        $snakeName = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $modelName));
        // 檔名
        $fileName = date('YmdHis') . "_create_" . $snakeName . "_table.php";
        // 模板內容
        $stubContent = file_get_contents(self::STUB_PATH . 'migration.stub');

        // 替換模板內容
        $stubContent = str_replace('{{modelName}}', $modelName, $stubContent);
        $stubContent = str_replace('{{tableName}}', $snakeName, $stubContent);
        $stubContent = str_replace('{{tableNames}}', $snakeName . 's', $stubContent);

        // 創建 Migration
        $targetFolderPath = base_path() . "/db/migrations/$fileName";
        $this->checkAndCreateFile($targetFolderPath, $stubContent);

        return true;
    }

    // 創建 Model
    private function createModel(string $modelName): bool
    {
        // 檔名
        $fileName = $modelName . ".php";
        // 蛇形命名
        $snakeName = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $modelName));
        // 模板內容
        $stubContent = file_get_contents(self::STUB_PATH . 'model.stub');

        // 替換模板內容
        $stubContent = str_replace('{{modelName}}', $modelName, $stubContent);
        $stubContent = str_replace('{{tableNames}}', $snakeName . 's', $stubContent);

        // 創建檔案
        $targetFolderPath = base_path() . "/app/model/$fileName";
        $this->checkAndCreateFile($targetFolderPath, $stubContent);
        return true;
    }

    // 創建 Repository
    private function createRepository(string $modelName): bool
    {
        // 檔名
        $fileName = $modelName . "Repository.php";
        // 模板內容
        $stubContent = file_get_contents(self::STUB_PATH . 'repository.stub');

        // 替換模板內容
        $stubContent = str_replace('{{modelName}}', $modelName, $stubContent);

        // 創建檔案
        $targetFolderPath = base_path() . "/app/repository/$fileName";
        $this->checkAndCreateFile($targetFolderPath, $stubContent);
        return true;
    }

    // 創建 Controller
    private function createController(string $modelName): bool
    {
        // 檔名
        $fileName = $modelName . "Controller.php";
        // 模板內容
        $stubContent = file_get_contents(self::STUB_PATH . 'controller.stub');

        // 替換模板內容
        $stubContent = str_replace('{{modelName}}', $modelName, $stubContent);
        $stubContent = str_replace('{{modelNameLower}}', $this->toCamelCase($modelName), $stubContent);

        // 創建檔案
        $targetFolderPath = base_path() . "/app/controller/$fileName";
        $this->checkAndCreateFile($targetFolderPath, $stubContent);
        return true;
    }

    // 在 route.php 中最後一行添加路徑
    private function addRoute(string $modelName): bool
    {
        $routeContent = file_get_contents(base_path() . "/config/route.php");

        $routeContent .= "\nRoute::resource('" . strtolower($modelName) . "', " . $modelName . "Controller::class);";

        $fileHandle = fopen(base_path() . "/config/route.php", 'w');
        if ($fileHandle) {
            // 在 use Webman\Route; 這行的下一行插入 Class
            $useClass = "use app\\controller\\" . $modelName . "Controller;";
            $routeContent = str_replace("use Webman\\Route;", "use Webman\\Route;\n" . $useClass, $routeContent);
            fwrite($fileHandle, $routeContent);
            fclose($fileHandle);
            echo "Route added successfully.\n";
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

    private function toCamelCase($string): string
    {
        // 將字串中的大寫字母前面加上空格
        $string = preg_replace('/([a-z])([A-Z])/', '$1 $2', $string);

        // 將字串轉換為小寫
        $string = strtolower($string);

        // 移除非字母數字的字符，並在它們之間加上空格
        $string = preg_replace('/[^a-z0-9]+/', ' ', $string);

        // 將字串分割成單詞陣列
        $words = explode(' ', $string);

        // 將每個單詞的首字母大寫，除了第一個單詞
        $camelCaseString = $words[0];
        for ($i = 1; $i < count($words); $i++) {
            $camelCaseString .= ucfirst($words[$i]);
        }

        return $camelCaseString;
    }
}
