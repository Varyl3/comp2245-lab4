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
```[cite: 7, 8, 9, 10, 11, 12, 14]
