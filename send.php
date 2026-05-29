<?php
// 文字化けを防ぐための日本語環境・エンコード設定
header("Content-Type: text/html; charset=UTF-8");
mb_language("Japanese");
mb_internal_encoding("UTF-8");

// フォームからPOST送信されてきた場合のみ処理を実行
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 入力データの取得と安全対策（サニタイズ処理）
    $purpose      = isset($_POST['purpose']) ? htmlspecialchars($_POST['purpose'], ENT_QUOTES, 'UTF-8') : '未選択';
    $company_name = isset($_POST['company_name']) ? htmlspecialchars($_POST['company_name'], ENT_QUOTES, 'UTF-8') : '（個人・未記入）';
    $name         = isset($_POST['name']) ? htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8') : '';
    $email        = isset($_POST['email']) ? htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8') : '';
    $tel          = isset($_POST['tel']) ? htmlspecialchars($_POST['tel'], ENT_QUOTES, 'UTF-8') : '';
    $message      = isset($_POST['message']) ? htmlspecialchars($_POST['message'], ENT_QUOTES, 'UTF-8') : '';

    // 送り先メールアドレス（ご指定のアドレス）
    $to = "sanwakatsu260201@gmail.com";

    // メールの件名
    $subject = "【HPお問い合わせ】{$purpose}（{$name}様）";

    // メール本文の組み立て
    $body = "ホームページからお問い合わせ・ご応募がありました。\n\n";
    $body .= "--------------------------------------------------\n";
    $body .= "【お問い合わせ目的】 {$purpose}\n";
    $body .= "【貴社名・団体名】   {$company_name}\n";
    $body .= "【お名前】           {$name} 様\n";
    $body .= "---------- お客様への連絡先 ----------\n";
    $body .= "【メールアドレス】   {$email}\n";
    $body .= "【電話番号】         {$tel}\n";
    $body .= "--------------------------------------------------\n\n";
    $body .= "【お問い合わせ内容】\n{$message}\n\n";
    $body .= "--------------------------------------------------\n";
    $body .= "※このメールにそのまま返信すると、お客様のアドレス（{$email}）に届きます。";

    // メールのヘッダー設定（送信元と返信先の指定）
    // レンタルサーバーの仕様に合わせ、送信元(From)は安全のため送り先と同じアドレスにしつつ、
    // メールソフトで「返信」を押した際は自動でお客様に返せるよう Reply-To を指定しています。
    $headers = "From: " . mb_encode_mimeheader("サンワ大阪HP") . " <" . $to . ">\r\n";
    $headers .= "Reply-To: " . $email . "\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    // メールの送信実行
    if (mb_send_mail($to, $subject, $body, $headers)) {
        // 【送信成功】サンクス画面を出力
        echo "
        <!DOCTYPE html>
        <html lang='ja'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>送信完了｜株式会社サンワ大阪</title>
            <style>
                body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; text-align: center; padding: 60px 20px; background: #f8fafc; color: #333; }
                .card { max-width: 550px; margin: 0 auto; background: #ffffff; padding: 40px 30px; border-radius: 8px; box-shadow: 0 4px 20px rgba(0,0,0,0.06); border-top: 4px solid #134074; }
                h1 { color: #0B2545; font-size: 1.6rem; margin-bottom: 20px; font-weight: 700; }
                p { line-height: 1.8; color: #475569; margin-bottom: 35px; font-size: 0.95rem; }
                .btn { display: inline-block; background: #134074; color: #ffffff; padding: 14px 40px; text-decoration: none; border-radius: 4px; font-weight: bold; transition: background 0.3s; }
                .btn:hover { background: #0B2545; }
            </style>
        </head>
        <body>
            <div class='card'>
                <h1>お問い合わせを送信いたしました</h1>
                <p>内容が正常に送信されました。<br>ご入力いただいた内容を確認の上、担当者より折り返しご連絡いたしますので、今しばらくお待ちください。</p>
                <a href='index.html' class='btn'>ホームページへ戻る</a>
            </div>
        </body>
        </html>
        ";
    } else {
        // 【送信失敗】エラーをアラートで出して前の画面に戻す
        echo "<script>alert('サーバーのエラーによりメールの送信に失敗しました。時間をおいて再度お試しいただくか、お電話にてご連絡ください。'); history.back();</script>";
    }
} else {
    // フォーム以外から（URL直叩きなど）アクセスされた場合はトップへ強制移動
    header("Location: index.html");
    exit;
}
?>