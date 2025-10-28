<?php

namespace Jiangjiangdev\WmAuto\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ContentResetCommand extends Command
{
    protected static $defaultName = 'auto:reset';
    protected static $defaultDescription = 'Auto reset all config';

    private const STUB_PATH = __DIR__ . '/../stubs/';
    private const CONFIG_STUB_PATH = __DIR__ . '/../stubs/config/';

    protected function configure()
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'App name');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getArgument('name');

        // 接下來清空 app/controller、app/model、app/view 底下的所有檔案
        $this->clearFolder(base_path() . '/app/controller');
        $this->clearFolder(base_path() . '/app/model');
        $this->clearFolder(base_path() . '/app/middleware');
        $this->clearFolder(base_path() . '/app/view');
        $this->clearFolder(base_path() . '/app/repository');
        $this->clearFolder(base_path() . '/app/validation');
        $this->clearFolder(base_path() . '/app/listener');
        $this->clearFolder(base_path() . '/db');
        $this->clearFolder(base_path() . '/public/favicon.ico');
        $this->clearFolder(base_path() . '/public/version.txt');
        $this->clearFolder(base_path() . '/README.md');
        $this->clearFolder(base_path() . '/LICENSE');
        
        // Reset config
        // app.php
        $stubContent = file_get_contents(self::CONFIG_STUB_PATH . 'app.stub');
        $targetFolderPath = base_path() . "/config/app.php";
        $this->checkAndCreateFile($targetFolderPath, $stubContent, true);

        // autoload.php
        $stubContent = file_get_contents(self::CONFIG_STUB_PATH . 'autoload.stub');
        $targetFolderPath = base_path() . "/config/autoload.php";
        $this->checkAndCreateFile($targetFolderPath, $stubContent, true);

        // bootstrap.php
        $stubContent = file_get_contents(self::CONFIG_STUB_PATH . 'bootstrap.stub');
        $targetFolderPath = base_path() . "/config/bootstrap.php";
        $this->checkAndCreateFile($targetFolderPath, $stubContent, true);

        // container.php
        $stubContent = file_get_contents(self::CONFIG_STUB_PATH . 'container.stub');
        $targetFolderPath = base_path() . "/config/container.php";
        $this->checkAndCreateFile($targetFolderPath, $stubContent, true);

        // database.php
        $stubContent = file_get_contents(self::CONFIG_STUB_PATH . 'database.stub');
        $targetFolderPath = base_path() . "/config/database.php";
        $this->checkAndCreateFile($targetFolderPath, $stubContent, true);

        // dependence.php
        $stubContent = file_get_contents(self::CONFIG_STUB_PATH . 'dependence.stub');
        $targetFolderPath = base_path() . "/config/dependence.php";
        $this->checkAndCreateFile($targetFolderPath, $stubContent, true);

        // exception.php
        $stubContent = file_get_contents(self::CONFIG_STUB_PATH . 'exception.stub');
        $targetFolderPath = base_path() . "/config/exception.php";
        $this->checkAndCreateFile($targetFolderPath, $stubContent, true);

        // log.php
        $stubContent = file_get_contents(self::CONFIG_STUB_PATH . 'log.stub');
        $targetFolderPath = base_path() . "/config/log.php";
        $this->checkAndCreateFile($targetFolderPath, $stubContent, true);

        // middleware.php
        $stubContent = file_get_contents(self::CONFIG_STUB_PATH . 'middleware.stub');
        $targetFolderPath = base_path() . "/config/middleware.php";
        $this->checkAndCreateFile($targetFolderPath, $stubContent, true);

        // process.php
        $stubContent = file_get_contents(self::CONFIG_STUB_PATH . 'process.stub');
        $targetFolderPath = base_path() . "/config/process.php";
        $this->checkAndCreateFile($targetFolderPath, $stubContent, true);

        // redis.php
        $stubContent = file_get_contents(self::CONFIG_STUB_PATH . 'redis.stub');
        $targetFolderPath = base_path() . "/config/redis.php";
        $this->checkAndCreateFile($targetFolderPath, $stubContent, true);

        // server.php
        $stubContent = file_get_contents(self::CONFIG_STUB_PATH . 'server.stub');
        $targetFolderPath = base_path() . "/config/server.php";
        $this->checkAndCreateFile($targetFolderPath, $stubContent, true);

        // route.php
        $stubContent = file_get_contents(self::CONFIG_STUB_PATH . 'route.stub');
        $targetFolderPath = base_path() . "/config/route.php";
        $this->checkAndCreateFile($targetFolderPath, $stubContent, true);

        // cache.php
        $stubContent = file_get_contents(self::CONFIG_STUB_PATH . 'cache.stub');
        $targetFolderPath = base_path() . "/config/cache.php";
        $this->checkAndCreateFile($targetFolderPath, $stubContent, true);

        // session.php
        $stubContent = file_get_contents(self::CONFIG_STUB_PATH . 'session.stub');
        $targetFolderPath = base_path() . "/config/session.php";
        $this->checkAndCreateFile($targetFolderPath, $stubContent, true);

        // static.php
        $stubContent = file_get_contents(self::CONFIG_STUB_PATH . 'static.stub');
        $targetFolderPath = base_path() . "/config/static.php";
        $this->checkAndCreateFile($targetFolderPath, $stubContent, true);

        // translation.php
        $stubContent = file_get_contents(self::CONFIG_STUB_PATH . 'translation.stub');
        $targetFolderPath = base_path() . "/config/translation.php";
        $this->checkAndCreateFile($targetFolderPath, $stubContent, true);

        // view.php
        $stubContent = file_get_contents(self::CONFIG_STUB_PATH . 'view.stub');
        $targetFolderPath = base_path() . "/config/view.php";
        $this->checkAndCreateFile($targetFolderPath, $stubContent, true);

        // listener/StartListener
        $stubContent = file_get_contents(self::STUB_PATH . 'app/listener.stub');
        $targetFolderPath = base_path() . "/app/listener/StartListener.php";
        $this->checkAndCreateFile($targetFolderPath, $stubContent, true);

        // .env
        $stubContent = file_get_contents(self::STUB_PATH . 'env.stub');
        $targetFolderPath = base_path() . "/.env";
        // 替換名稱
        $stubContent = str_replace('{{name}}', $name, $stubContent);
        $this->checkAndCreateFile($targetFolderPath, $stubContent, true);

        // .gitignore
        $stubContent = file_get_contents(self::STUB_PATH . 'gitignore.stub');
        $targetFolderPath = base_path() . "/.gitignore";
        $this->checkAndCreateFile($targetFolderPath, $stubContent, true);

        // .bs-config.cjs
        $stubContent = file_get_contents(self::STUB_PATH . 'bs-config.stub');
        $targetFolderPath = base_path() . "/.bs-config.cjs";
        $this->checkAndCreateFile($targetFolderPath, $stubContent, true);

        // tailwind.config.cjs
        $stubContent = file_get_contents(self::STUB_PATH . 'tailwind.config.stub');
        $targetFolderPath = base_path() . "/tailwind.config.cjs";
        $this->checkAndCreateFile($targetFolderPath, $stubContent, true);

        // generate-version.cjs
        $stubContent = file_get_contents(self::STUB_PATH . 'generate-version.stub');
        $targetFolderPath = base_path() . "/generate-version.cjs";
        $this->checkAndCreateFile($targetFolderPath, $stubContent, true);

        // package.json
        $stubContent = file_get_contents(self::STUB_PATH . 'package.stub');
        $targetFolderPath = base_path() . "/package.json";
        $this->checkAndCreateFile($targetFolderPath, $stubContent, true);

        // resource/css/app.css
        $stubContent = file_get_contents(self::STUB_PATH . 'resource/css/app.stub');
        $targetFolderPath = base_path() . "/resource/css/app.css";
        $this->checkAndCreateFile($targetFolderPath, $stubContent, true);

        // public/version.txt
        $stubContent = file_get_contents(self::STUB_PATH . 'public/version.stub');
        $targetFolderPath = base_path() . "/public/version.txt";
        $this->checkAndCreateFile($targetFolderPath, $stubContent, true);

        // phinx.php
        $stubContent = file_get_contents(self::STUB_PATH . 'phinx.stub');
        $targetFolderPath = base_path() . "/phinx.php";
        $stubContent = str_replace('{{name}}', $name, $stubContent);
        $this->checkAndCreateFile($targetFolderPath, $stubContent, true);

        // app/function.php
        $stubContent = file_get_contents(self::STUB_PATH . 'app/functions.stub');
        $targetFolderPath = base_path() . "/app/functions.php";
        $this->checkAndCreateFile($targetFolderPath, $stubContent, true);

        return self::SUCCESS;
    }

    // 檢查並創建檔案
    private function checkAndCreateFile(string $fullPath, string $content, bool $force = false): bool
    {
        // 檢查檔案是否存在
        if (file_exists($fullPath) && !$force) {
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

    private function clearFolder(string $folderPath): void
    {
        // 如果是檔案的話，直接刪除
        if (is_file($folderPath)) {
            unlink($folderPath);
            return;
        }
        $files = glob($folderPath . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
                continue;
            }
            // 如果資料夾是空的，則刪除資料夾
            if (count(glob($folderPath . '/*')) === 0) {
                rmdir($folderPath);
                continue;
            }
            // 如果是資料夾，則遞迴刪除
            if (is_dir($file)) {
                $this->clearFolder($file);
                rmdir($file);
            }
        }
    }
}
