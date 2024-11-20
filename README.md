# Job Slider for WordPress

## 概要

Job Sliderは、企業別の求人情報をスライド形式で表示するWordPressプラグインです。APIから求人情報を取得し、記事内に企業ごとの求人カードをスライダーとして表示します。

## 特徴

- APIを使用した自動求人情報取得
- 企業ごとの求人情報管理
- 記事と求人スライダーの紐付け
- レスポンシブデザイン対応
- カスタマイズ可能なスライダーデザイン

## 動作要件

- WordPress 5.8以上
- PHP 7.4以上
- MySQL 5.7以上
- [Swiper](https://swiperjs.com/) 8.0以上（自動で読み込まれます）

## インストール方法

1. `job-slider.zip` をダウンロードします
2. WordPress管理画面 → プラグイン → 新規追加 → プラグインのアップロード
3. `job-slider.zip` を選択してインストール
4. プラグインを有効化

## 基本的な使い方

### 1. スライダーグループの作成

1. 管理画面メニュー「スライダー管理」→「新規追加」
2. スライダー名を入力（例：「株式会社AAA 求人情報」）
3. API設定に必要な情報を入力：
   - 企業名：「株式会社AAA」
   - Corporation ID：「21838」（APIから提供されたID）
4. 「公開」をクリック

### 2. 記事への紐付け

1. 通常の投稿記事作成画面を開く
2. 「求人スライダー設定」ボックスで作成したスライダーを選択
3. 記事を公開

### 3. 手動でのショートコード使用

必要に応じて、以下のショートコードを記事内に配置：

```
[job_slider id="スライダーID" corporation_id="企業ID"]
```

## 使用例

### ケース1：AAA社の企業紹介記事

1. スライダーグループ作成

   ```
   タイトル：AAA社求人情報スライダー
   企業名：株式会社AAA
   Corporation ID：21838
   ```

2. 記事作成

   ```
   タイトル：AAA社の技術力に迫る！エンジニアの働き方とは
   本文：（インタビュー記事）
   求人スライダー設定：AAA社求人情報スライダー
   ```

3. 結果
   - 記事末尾にAAA社の求人情報が自動表示
   - スライダーで複数の求人をブラウズ可能

### ケース2：特定の求人のみ表示

```
[job_slider id="123" corporation_id="21838" filter="position=engineer"]
```

## カスタマイズ

### スタイルのカスタマイズ

`assets/css/job-slider.css` を編集することで見た目を変更可能：

```css
.job-card {
    /* カードデザインのカスタマイズ */
    background: #fff;
    padding: 20px;
    border-radius: 8px;
}
```

### フィルタの使用

求人情報の表示をカスタマイズするフィルタフック：

```php
add_filter('job_slider_card_content', function($content, $job) {
    // カード内容のカスタマイズ
    return $content;
}, 10, 2);
```

## トラブルシューティング

1. スライダーが表示されない
   - APIの接続設定を確認
   - Corporation IDが正しいか確認
   - ブラウザのコンソールでエラーを確認

2. スタイルが崩れる
   - テーマのCSSと競合していないか確認
   - レスポンシブ設定を確認

## 開発者向け情報

### ディレクトリ構造

```bash
job-slider/
├── job-slider.php          # メインプラグインファイル
├── assets/
│   ├── css/
│   │   ├── job-slider.css  # フロントエンド用CSS
│   │   └── admin.css       # 管理画面用CSS
│   └── js/
│       ├── front.js        # フロントエンド用JS
│       └── admin.js        # 管理画面用JS
└── templates/              # 表示用テンプレート
    ├── job-details-form.php
    ├── slider-settings.php
    └── slider-template.php
```

### フィルターフック一覧

| フック名 | 説明 | パラメータ |
|----------|------|------------|
| `job_slider_card_content` | カード内容のカスタマイズ | `$content`, `$job` |
| `job_slider_api_params` | API呼び出しパラメータの修正 | `$params` |

### アクションフック一覧

| フック名 | 説明 | パラメータ |
|----------|------|------------|
| `job_slider_before_render` | スライダー描画前 | `$slider_id` |
| `job_slider_after_render` | スライダー描画後 | `$slider_id` |

## サポート

- イシューの報告：GitHubリポジトリのIssuesセクション
- 質問：WordPressフォーラム

## ライセンス

GPL v2 or later
