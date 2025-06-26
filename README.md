# 勤怠管理アプリ

## 概要

出勤・休憩・退勤の打刻機能に加えて、勤怠修正申請・承認、勤怠一覧表示、スタッフごとの月次勤怠確認、CSV出力などを備えた、Laravel製の勤怠管理アプリです。  
一般ユーザーと管理者の両方に対応した機能を提供しています。

---

## 環境構築

### Dockerビルド手順

1. `git clone git@github.com:furukawa-seiya530/management-app.git`
3. `docker-compose up -d --build`

> ※ MySQLが立ち上がらない場合、OSに応じて `docker-compose.yml` を調整してください。

---

### Laravelセットアップ

1. `docker-compose exec php bash`
2. `composer install`
3. `.env.example` をコピーして `.env` を作成
4. `.env` の環境変数を適切に編集
5. `php artisan key:generate`
6. `php artisan migrate`
7. `php artisan db:seed` （初期データ投入が必要な場合）

---

## 使用技術

- **PHP** 8.0  
- **Laravel** 10  
- **MySQL** 8.0  
- **Docker / Docker Compose**  
- **Bladeテンプレート** / **CSS（Sanitize + カスタムCSS）**  
- **Laravel Fortify（認証機能）**  
- **PHPUnit（機能テスト実装済）**  
- **dbdiagram.io** によるER図管理

---

## 主な機能

- ログイン / 会員登録（Fortify）
- 出勤 / 休憩 / 退勤の打刻機能
- 勤怠一覧（月別切替対応）
- 勤怠詳細確認・修正申請機能
- 勤怠修正申請一覧（承認待ち / 承認済み）
- 管理者による申請承認 / 勤怠直接修正
- スタッフ別勤怠閲覧（月単位・CSV出力機能あり）
- 管理者 / 一般ユーザーのアクセス制御
- レスポンシブ対応

---

## 作成したER図

![Untitled](https://github.com/user-attachments/assets/26b14902-ecff-4c4e-850a-c98ea8fc8b66)


---

## URL（開発用）

- フロントエンド: [http://localhost](http://localhost)  
- phpMyAdmin: [http://localhost:8080](http://localhost:8080)

---

## その他(重要)

- `.env` ファイルは以下のように編集してください（DB接続エラー防止のため）:

-DB_CONNECTION=mysql
-DB_HOST=mysql
-DB_PORT=3306
-DB_DATABASE=laravel_db
-DB_USERNAME=root
-DB_PASSWORD=root

## テスト実行

- `php artisan test`

Featureテスト・Unitテスト含む多数のテストケースを実装済み。

テストファイル例:
`tests/Feature/LoginTest.php`
`tests/Feature/AttendanceCorrectionTest.php`
`tests/Feature/AdminAttendanceListTest.php`
`tests/Unit/ExampleTest.php など`

##補足
出勤ボタンは「当日中は1回のみ有効」であり、翌日になると再び押せる仕様です。

修正申請は管理者承認後、自動的に勤怠データへ反映されます。
