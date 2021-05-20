<?php

return [
    // 画像サイズ、ファイルサイズ上限定義
    'image' => [
        'max_width' => 320,   // 幅320px
        'max_height' => 240,  // 高さ240px
        'max_size' => 10240,  // ファイルサイズは10240KB(10MB)まで
    ],

    // 画像、添付ファイル保存場所定義
    'directory' => [
        'profile_p' => 'painter/profile/',    // 業者プロフィール画像保存場所
        'profile_u' => 'user/profile/',       // 個人プロフィール画像保存場所
        'property' => 'user/property/',       // 物件添付画像保存場所
        'request' => 'user/request/',         // 依頼添付画像保存場所
        'column' => 'painter/column/',        // コラム画像保存場所
        'example' => 'painter/example/',      // 施工事例画像保存場所
        'quotation' => 'painter/quotation/',  // 見積書保存場所
        'attach' => 'painter/attach/',        // 提案添付書類保存場所
        'contract' => 'painter/contract/',    // 契約書保存場所
    ],

    // 画像ファイルが存在しない場合のURL
    'no_image' => env('APP_URL').'/image/no_image.jpg',

    // ホーム画面のURL
    'home' => '/',

    // 画面URLプレフィックス定義
    'prefix' => [
        'admin' => '/admin',
        'painter' => '/painter',
        'user' => '/user',
    ],

    // テンプレートファイル名定義
    'template' => [
        'admin' => [
            // 管理者登録画面
            'entry' => '',
            // 管理者ログイン画面
            'login' => 'admin.worker',
            // 管理者一覧画面
            'admin_list' => '',
            // 管理者用個人会員一覧画面
            'user_list' => '',
            // 管理者用業者会員一覧画面
            'painter_list' => '',
            // 管理者用コラム一覧画面
            'column_list' => '',
            // 管理者用施工事例一覧画面
            'example_list' => '',
            // 管理者用口コミ・評価一覧画面
            'evaluation_list' => '',
            // 管理者用お知らせ一覧画面
            'notice_list' => '',
        ],

        'painter' => [
            // 業者会員登録画面
            'entry' => 'painter.entry',
            // 業者会員登録完了画面
            'complete' => 'painter.entry_complete',
            // 業者会員ログイン画面
            'login' => 'painter.login',
            // 業者会員トップページ
            'top' => 'painter.top',
            // 業者会員マイページ
            'mypage' => 'painter.mypage',
            // 業者会員プロフィール編集画面
            'edit' => 'painter.profile',
            // 業者会員退会画面
            'withdraw' => '',
            // 一般向け業者会員検索結果ページ
            'list' => '',
            // 一般向け業者会員詳細ページ
            'detail' => '',
            // 相談・商談一覧画面
            'workflow' => '',
            // 外部施工事例登録画面
            'exampleentry' => 'painter.exampleentry',
        ],

        'user' => [
            // 個人会員登録画面
            'entry' => 'user.entry',
            // 個人会員登録完了画面
            'complete' => 'user.entry_complete',
            // 個人会員ログイン画面
            'login' => 'user.login',
            // 個人会員トップページ
            'top' => 'user.top',
            // 個人会員マイページ
            'mypage' => 'user.mypage',
            // 個人会員プロフィール編集画面
            'edit' => 'user.profile',
            // 個人会員退会画面
            'withdraw' => '',
            // 業者向け個人会員詳細ページ
            'detail' => '',
            // 相談・商談一覧画面
            'workflow' => '',
            // 業者検索結果ページ
            'search_painter' => 'user.search_painter',
            'favorite' => 'favoritelist',
            // 施工事例一覧ページ
            'construction_case_list' => 'user.construction_case_list',
        ],

        'property' => [
            // 物件追加画面
            'create' => '',
            // 物件編集画面
            'edit' => '',
        ],

        'column' => [
            // コラム追加画面
            'create' => 'column.create',
            // コラム編集画面
            'edit' => '',
        ],

        'example' => [
            // 施工事例追加画面
            'create' => '',
            // 施工事例編集画面
            'edit' => '',
        ],

        // 依頼編集画面
        'request' => '',

        // 見積依頼入力画面
        'estimate' => 'user.bulk_quatation',

        // 見積依頼確認画面
        'estimate_conf' => 'user.bulk_quatation_confirm',

        // 見積応答入力画面
        'proposal' => '',

        // 契約編集画面
        'contract' => '',

        // 口コミ・評価登録画面
        'evaluation' => '',

        // お問い合わせ画面
        'contact' => '',

        // チャット画面
        'chat' => 'chat',

        'password' => [
            // パスワードリセット画面
            'reset' => '',
            // パスワード変更画面
            'modify' => '',
        ],

        'notice' => [
            // お知らせ追加画面
            'create' => '',
            // お知らせ表示画面
            'create' => '',
            // お知らせ編集画面
            'edit' => '',
        ],
    ],

    'select' => [
        // 都道府県
        'prefecture' => [
            '北海道',
            '青森県',
            '岩手県',
            '宮城県',
            '秋田県',
            '山形県',
            '福島県',
            '茨城県',
            '栃木県',
            '群馬県',
            '埼玉県',
            '千葉県',
            '東京都',
            '神奈川県',
            '新潟県',
            '富山県',
            '石川県',
            '福井県',
            '山梨県',
            '長野県',
            '岐阜県',
            '静岡県',
            '愛知県',
            '三重県',
            '滋賀県',
            '京都府',
            '大阪府',
            '兵庫県',
            '奈良県',
            '和歌山県',
            '鳥取県',
            '島根県',
            '岡山県',
            '広島県',
            '山口県',
            '徳島県',
            '香川県',
            '愛媛県',
            '高知県',
            '福岡県',
            '佐賀県',
            '長崎県',
            '熊本県',
            '大分県',
            '宮崎県',
            '鹿児島県',
            '沖縄県',
        ],

        // 年間売上
        'sales' => [
            '１００万円未満', 
            '１００万円～１０００万円未満', 
            '１０００万円～５０００万円未満', 
            '５０００万円以上', 
        ],

        // 業者、依頼、契約共通で使用するカテゴリー
        'category' => [
            '外壁塗装',
            '屋根塗装',
            '屋上防水',
            'ベランダ防水',
            'その他',
        ],

        // 物件のタイプ
        'property' => [
            '戸建',
            '集合住宅',
            'マンション',
            '工場',
        ],

        // 屋根の種類
        'roof' => [
            '切妻屋根',
            '寄棟屋根',
            '片流れ屋根',
            '陸屋根',
            '方形屋根',
            '入母屋根',
            '半切妻屋根',
            '差しかけ屋根',
            'のこぎり屋根',
        ],

        // 外壁の種類
        'wall' => [
            'ALC',
            'サイディング',
            '木造',
            'コンクリート',
        ],

        // コラムのカテゴリー
        'column' => [
            '日記',
            '雑学',
            '業務',
            'その他',
        ],

        // 工事に求めるもの
        'priority' => [
            'できるだけ工事を安くしたい',
            '保証内容や品質にこだわりたい',
            '完成時期や工事期間などにこだわりたい',
            '職人や営業の人柄や知識を求めている',
        ],

        // 工期予定
        'period' => [
            '３ヶ月以内',
            '６ヶ月以内',
            '１年以内',
            'できるだけ早く工事をしたい',
            '色々相談して考えていきたい',
            'まだまだ考えていない',
            'よくわからない',
        ],

        // 進捗ステータス
        'status' => [
            '新規',
            '相談中',
            '商談開始',
            '見積提出',
            '本契約',
            '工事終了',
            '完工',
        ],

        // 保証
        'Warranty' => [
            '',
            '1年',
            '2年',
            '3年',
            '4年',
            '5年',
            '6年',
            '7年',
            '8年',
            '9年',
            '10年',
        ],

        // 金額
        'amount' => [
            '',
            '50-100',
        ],

        // 工事期間
        'constructionperiod' => [
            '',
            '１週間',
        ],

        // 完工日（暫定:選択リストで機能が実装できるか？）
        'completedate' => [
            '',
            '2021年3月',
        ],
    ],

    // お問い合わせメール
    'contact' => [
        // お問い合わせの送信元、送信先
        'send_to' => 'sample@example.com',
        // お問い合わせの送信者名
        'sender' => '塗装道場',
    ],

    // パスワードリセット通知
    'password' => [
        // パスワードリセット通知の送信元
        'send_from' => 'sample@example.com',
        // パスワードリセット通知の送信者名
        'sender' => '塗装道場',
    ],

    // チャット画面の初期送信メッセージ
    'message' => '初めまして。よろしくお願いいたします。',
];
