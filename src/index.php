<?php

$users = [];

// 데이터베이스에 접속
$dsn = 'mysql:host=db;port=3306;dbname=sample';
$username = 'root';
$password = 'secret';
try {
    $pdo = new PDO($dsn, $username, $password);

    // user 테이블 내용을 취득
    $statement = $pdo->query('select * from user');
    $statement->execute();
    while ($row = $statement->fetch()) {
        // 1줄씩 배열에 추가
        $users[] = $row;
    }

    // 접속 해제
    $pdo = null;
} catch (PDOException $e) {
    echo '데이터베이스에 접속하지 못했습니다.';
}

// 사용자 정보 출력
foreach ($users as $user) {
    echo '<p>id: ' . $user['id'] . ', name: ' . $user['name'] . '</p>';
}

// 메일 발송
$subject = '테스트 메일입니다.';
$message = 'Docker Hub는 여기 → https://hub.docker.com/';
foreach ($users as $user) {
    $success = mb_send_mail($user['email'], $subject, $message);
    if ($success) {
        echo '<p>' . $user['name'] . '에 메일을 송신했습니다.</p>';

    } else {
        echo '<p>메일 송신에 실패했습니다.</p>';
    }
}
