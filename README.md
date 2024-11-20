# Job Slider for WordPress

## 概要

Job Sliderは、APIベースの求人情報を動的に取得し、WordPress投稿内でスライダー形式で表示するプラグインです。企業ごとの求人情報を自動で取得・表示し、記事内容と関連する求人情報を効果的に提示できます。

## 特徴

- Engineer Factory APIとの連携による自動求人情報取得
- 記事ごとの求人情報管理
- Swiperベースのモダンなスライダー表示
- レスポンシブ対応
- ページネーション対応
- キャッシュ機能搭載

## 動作要件

- WordPress 5.8以上
- PHP 7.4以上
- MySQL 5.7以上
- Swiper 8.0以上（自動で読み込まれます）

## インストール方法

1. `job-slider.zip`をダウンロード
2. WordPress管理画面 → プラグイン → 新規追加 → プラグインのアップロード
3. `job-slider.zip`を選択してインストール
4. プラグインを有効化

## 基本的な使い方

### 1. APIの設定

管理画面で以下の設定を行います：

```php
// API設定例
corporation_id: "21838"  // 企業ID
page_size: 10           // 1ページあたりの表示件数
```

### 2. 記事への組み込み

1. 投稿/固定ページの編集画面で「求人スライダー」ボックスを使用
2. 企業IDを選択
3. 表示オプションを設定
4. プレビューで表示を確認

または、ショートコードを使用：

```bash
[job_slider corporation_id="21838"]
```

## データ構造

APIから取得される求人データの基本構造：

```json
{
    "data": [{
        "id": "job_id",
        "attributes": {
            "name": "求人タイトル",
            "duties": "職務内容",
            "wage_max": 80,
            "nearest_stations": "勤務地"
        },
        "relationships": {
            "skills": { /* スキル情報 */ },
            "features": { /* 特徴情報 */ }
        }
    }]
}
```

## カスタマイズ

### カードデザインのカスタマイズ

`assets/css/job-slider.css`でスタイルをカスタマイズ可能：

```css
.job-card {
    background: #fff;
    border-radius: 8px;
    padding: 20px;
    /* その他のスタイル */
}
```

### APIパラメータのカスタマイズ

```php
add_filter('job_slider_api_params', function($params) {
    // パラメータのカスタマイズ
    $params['filter']['status'] = 'active';
    return $params;
});
```

## トラブルシューティング

1. APIエラー
   - corporation_idの確認
   - ネットワーク接続の確認
   - レスポンスのデバッグ

2. 表示の問題
   - キャッシュのクリア
   - JavaScriptエラーの確認
   - CSSの競合チェック

## 開発者向け情報

### ディレクトリ構造

```bash
job-slider/
├── job-slider.php     # メインファイル
├── assets/
│   ├── css/
│   │   ├── job-slider.css
│   │   └── admin.css
│   └── js/
│       ├── front.js
│       └── admin.js
└── templates/
    ├── job-details-form.php
    ├── slider-settings.php
    └── slider-template.php
```

### フィルターフック

| フック名 | 説明 | パラメータ |
|----------|------|------------|
| `job_slider_api_response` | APIレスポンスの加工 | `$response` |
| `job_slider_card_html` | カードHTMLの加工 | `$html, $job` |

### アクションフック

| フック名 | 説明 | パラメータ |
|----------|------|------------|
| `job_slider_before_api_call` | API呼び出し前 | `$params` |
| `job_slider_after_api_call` | API呼び出し後 | `$response` |

## 今後の開発予定

- [ ] 複数企業の同時表示
- [ ] 高度な検索フィルター
- [ ] カスタムテンプレート機能
- [ ] REST API対応
