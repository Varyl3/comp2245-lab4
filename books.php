<?php
header('Content-Type: application/json; charset=UTF-8');
require_once __DIR__ . '/db.php';

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$subject = filter_input(INPUT_GET, 'subject', FILTER_VALIDATE_INT);
$available = isset($_GET['available']) ? $_GET['available'] : '';

$sql = "SELECT books.title, books.author, books.year, subjects.name AS subject, books.copies, 
               (books.copies - books.on_loan) AS available 
        FROM books 
        JOIN subjects ON books.subject_id = subjects.id 
        WHERE (books.title LIKE :q1 OR books.author LIKE :q2)";

$params = [
    'q1' => "%$q%",
    'q2' => "%$q%"
];

if ($subject !== false && $subject !== null && $subject >= 1) {
    $sql .= " AND books.subject_id = :subject";
    $params['subject'] = $subject;
}

if ($available === '1') {
    $sql .= " AND (books.copies - books.on_loan) > 0";
}

$sql .= " ORDER BY books.title ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($books);
