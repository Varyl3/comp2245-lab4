<?php
require __DIR__ . '/config.php';

try {
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    error_log($e->getMessage());
    http_response_code(500);
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(["error" => "Could not connect to the database."]);
    exit;
}
```[cite: 7]

---

### 2. `subjects.php`
```php
<?php
header('Content-Type: application/json; charset=UTF-8');
require_once __DIR__ . '/db.php';

$stmt = $pdo->query("SELECT id, name FROM subjects ORDER BY name ASC");
$subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($subjects);
```[cite: 7]

---

### 3. `books.php`
```php
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
```[cite: 8, 9, 10]

---

### 4. `app.js`
```javascript
document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('form');
    const subjectSelect = document.querySelector('#subject');
    const availableCheckbox = document.querySelector('#available');
    const searchBtn = form.querySelector('button[type="submit"]');
    const statusDiv = document.querySelector('#status');
    const tbody = document.querySelector('#results tbody');

    fetch('subjects.php')
        .then(res => {
            if (!res.ok) throw new Error('Failed to load subjects');
            return res.json();
        })
        .then(subjects => {
            subjects.forEach(sub => {
                const option = document.createElement('option');
                option.value = sub.id;
                option.textContent = sub.name;
                subjectSelect.appendChild(option);
            });
        })
        .catch(err => console.error(err));

    function fetchBooks() {
        const params = new URLSearchParams(new FormData(form)).toString();
        searchBtn.disabled = true;
        statusDiv.textContent = 'Searching...';

        fetch(`books.php?${params}`)
            .then(res => {
                if (!res.ok) throw new Error('Failed to load books');
                return res.json();
            })
            .then(books => {
                tbody.replaceChildren();

                if (books.length === 0) {
                    statusDiv.textContent = 'No books match your search.';
                } else {
                    statusDiv.textContent = `${books.length} book${books.length === 1 ? '' : 's'} found.`;
                    
                    books.forEach(book => {
                        const tr = document.createElement('tr');
                        
                        const tdTitle = document.createElement('td');
                        tdTitle.textContent = book.title;
                        
                        const tdAuthor = document.createElement('td');
                        tdAuthor.textContent = book.author;
                        
                        const tdYear = document.createElement('td');
                        tdYear.textContent = book.year;
                        
                        const tdSubject = document.createElement('td');
                        tdSubject.textContent = book.subject;
                        
                        const tdShelf = document.createElement('td');
                        tdShelf.textContent = `${book.available} of ${book.copies}`;
                        if (book.available === 0) {
                            tdShelf.className = 'out';
                        }
                        
                        tr.append(tdTitle, tdAuthor, tdYear, tdSubject, tdShelf);
                        tbody.appendChild(tr);
                    });
                }
            })
            .catch(err => {
                console.error(err);
                tbody.replaceChildren();
                statusDiv.textContent = "Couldn't load the books. Please try again.";
            })
            .finally(() => {
                searchBtn.disabled = false;
            });
    }

    form.addEventListener('submit', e => {
        e.preventDefault();
        fetchBooks();
    });

    subjectSelect.addEventListener('change', fetchBooks);
    availableCheckbox.addEventListener('change', fetchBooks);

    fetchBooks();
});
```[cite: 10, 11, 12]
