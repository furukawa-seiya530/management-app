# 勤怠管理アプリ - Laravel製

## 📌 概要

本アプリは、LaravelとDockerを用いて開発した勤怠管理システムです。  
一般ユーザー・管理者で機能を分け、勤怠打刻・修正申請・申請承認などの業務をWeb上で完結できるよう設計されています。

---

## 🛠️ 環境構築

### ✅ クローン・Dockerビルド

```bash
git clone git@github.com:furukawa-seiya530/management-app.git
cd management-app
docker-compose up -d --build
※ MySQL が起動しない場合は、docker-compose.yml をOSに応じて調整してください。

✅ Laravelセットアップ
bash
コピーする
編集する
docker-compose exec php bash
composer install
cp .env.example .env
# .envファイルを編集（DB接続など）
php artisan key:generate
php artisan migrate
php artisan db:seed   # 初期ユーザー・ダミーデータを投入
⚙️ 使用技術
PHP 8.0

Laravel 10

MySQL 8.0

Docker / Docker Compose

Laravel Fortify（認証）

Bladeテンプレート / CSS（sanitize.css + カスタムCSS）

PHPUnit（Feature / Unitテスト実装済）

✨ 主な機能
🔓 認証機能（Fortify）
ユーザー登録 / ログイン / ログアウト（管理者・一般ユーザー共通）

👤 一般ユーザー機能
勤怠打刻（出勤 / 休憩 / 退勤）

勤怠一覧（月別切替、詳細リンクあり）

勤怠詳細編集（修正申請機能あり）

修正申請一覧（承認待ち / 承認済み）

ステータス管理（出勤中、休憩中、退勤済など）

🛠 管理者機能
ログイン（管理者のみアクセス可能）

スタッフ一覧（氏名・メールアドレス表示）

スタッフ別勤怠一覧（月次切替）

勤怠詳細の直接修正（承認不要）

修正申請一覧（承認 / 却下対応）

申請詳細画面（内容確認・承認機能）

🔗 アクセス
フロントエンド: http://localhost

phpMyAdmin: http://localhost:8080

🧪 テスト実行
bash
コピーする
編集する
php artisan test
Featureテスト・Unitテスト含む多数のテストケースを実装済み。

テストファイル例:

tests/Feature/LoginTest.php

tests/Feature/AttendanceCorrectionTest.php

tests/Feature/AdminAttendanceListTest.php

tests/Unit/ExampleTest.php など

📌 ER図（設計）
ER図は dbdiagram.io にて設計。
主要テーブル：

users

attendances

attendance_correction_requests

※希望があれば画像・リンクをここに掲載。

⚠️ 注意事項
.env 設定例（DB）:

ini
コピーする
編集する
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=root
DB_PASSWORD=root
php artisan migrate:fresh で初期化後、 php artisan db:seed を実行してください。

Dockerが起動していない状態で php artisan コマンドを実行しようとするとエラーになるため注意。
