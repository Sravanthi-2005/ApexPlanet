<?php

$pageTitle = 'Books';

require_once 'functions.php';


/*
|--------------------------------------------------------------------------
| Search
|--------------------------------------------------------------------------
*/

$q = trim($_GET['q'] ?? '');

$category = trim(
    $_GET['category'] ?? ''
);

$sort = $_GET['sort'] ?? 'newest';


/*
|--------------------------------------------------------------------------
| Pagination
|--------------------------------------------------------------------------
*/

$page = max(
    1,
    (int) ($_GET['page'] ?? 1)
);

$limit = 8;

$offset = ($page - 1) * $limit;


/*
|--------------------------------------------------------------------------
| Build WHERE Conditions
|--------------------------------------------------------------------------
*/

$where = [];

$params = [];


/*
|--------------------------------------------------------------------------
| Search Filter
|--------------------------------------------------------------------------
*/

if ($q !== '') {

    $where[] =
        '(title LIKE ?
        OR author LIKE ?
        OR category LIKE ?)';

    $searchTerm = "%{$q}%";

    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}


/*
|--------------------------------------------------------------------------
| Category Filter
|--------------------------------------------------------------------------
*/

if ($category !== '') {

    $where[] = 'category = ?';

    $params[] = $category;
}


$whereSql = '';

if (!empty($where)) {

    $whereSql =
        'WHERE ' .
        implode(
            ' AND ',
            $where
        );
}


/*
|--------------------------------------------------------------------------
| Sorting
|--------------------------------------------------------------------------
*/

$order = 'created_at DESC';

if ($sort === 'price_low') {

    $order = 'price ASC';

}

elseif ($sort === 'price_high') {

    $order = 'price DESC';

}

elseif ($sort === 'title') {

    $order = 'title ASC';

}


/*
|--------------------------------------------------------------------------
| Count Books
|--------------------------------------------------------------------------
*/

$countStmt = $pdo->prepare(
    "SELECT COUNT(*)
     FROM books
     $whereSql"
);

$countStmt->execute($params);

$totalBooks =
    (int) $countStmt->fetchColumn();


/*
|--------------------------------------------------------------------------
| Total Pages
|--------------------------------------------------------------------------
*/

$totalPages = max(
    1,
    (int) ceil(
        $totalBooks / $limit
    )
);


/*
|--------------------------------------------------------------------------
| Fetch Books
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare(

    "SELECT *
     FROM books

     $whereSql

     ORDER BY $order

     LIMIT $limit
     OFFSET $offset"

);

$stmt->execute($params);

$books = $stmt->fetchAll();


/*
|--------------------------------------------------------------------------
| Categories
|--------------------------------------------------------------------------
*/

$categoryStmt = $pdo->query(

    "SELECT DISTINCT category
     FROM books
     ORDER BY category"

);

$categories =
    $categoryStmt->fetchAll(
        PDO::FETCH_COLUMN
    );


include 'partials/header.php';

?>


<!-- =========================
     PAGE HEADER
========================= -->

<div class="section-title">

    <h1>
        Book Store 📚
    </h1>

    <span class="muted">

        <?= $totalBooks ?>
        books found

    </span>

</div>


<!-- =========================
     SEARCH & FILTER
========================= -->

<form
    method="get"
    class="toolbar"
>

    <input
        type="text"
        name="q"
        class="form-control"
        placeholder="Search title, author or category"
        value="<?= e($q) ?>"
    >


    <select
        name="category"
        class="form-control"
    >

        <option value="">
            All Categories
        </option>

        <?php foreach ($categories as $cat): ?>

            <option
                value="<?= e($cat) ?>"
                <?= $category === $cat
                    ? 'selected'
                    : '' ?>
            >

                <?= e($cat) ?>

            </option>

        <?php endforeach; ?>

    </select>


    <select
        name="sort"
        class="form-control"
    >

        <option
            value="newest"
            <?= $sort === 'newest'
                ? 'selected'
                : '' ?>
        >
            Newest
        </option>


        <option
            value="price_low"
            <?= $sort === 'price_low'
                ? 'selected'
                : '' ?>
        >
            Price: Low to High
        </option>


        <option
            value="price_high"
            <?= $sort === 'price_high'
                ? 'selected'
                : '' ?>
        >
            Price: High to Low
        </option>


        <option
            value="title"
            <?= $sort === 'title'
                ? 'selected'
                : '' ?>
        >
            Title A-Z
        </option>

    </select>


    <button
        type="submit"
        class="btn"
    >
        Search
    </button>


    <a
        href="books.php"
        class="btn secondary"
    >
        Reset
    </a>

</form>


<!-- =========================
     BOOK GRID
========================= -->

<div class="grid grid-4">

<?php if (!empty($books)): ?>

    <?php foreach ($books as $book): ?>

        <div class="card book-card">

            <img
                src="<?= e($book['cover_url']) ?>"
                alt="<?= e($book['title']) ?>"
            >


            <div class="book-info">

                <span class="badge">

                    <?= e($book['category']) ?>

                </span>


                <h3>

                    <?= e($book['title']) ?>

                </h3>


                <p class="muted">

                    By
                    <?= e($book['author']) ?>

                </p>


                <p class="price">

                    ₹<?= number_format(
                        (float) $book['price'],
                        2
                    ) ?>

                </p>


                <?php if ($book['stock'] > 0): ?>

                    <p class="muted">

                        <?= (int) $book['stock'] ?>
                        copies available

                    </p>

                <?php else: ?>

                    <p class="alert error">

                        Out of Stock

                    </p>

                <?php endif; ?>


                <a
                    href="book.php?id=<?= (int) $book['id'] ?>"
                    class="btn"
                >

                    View Details

                </a>

            </div>

        </div>

    <?php endforeach; ?>

<?php else: ?>

    <div class="card empty">

        <h2>
            No Books Found
        </h2>

        <p class="muted">

            Try another search or category.

        </p>

        <a
            href="books.php"
            class="btn"
        >
            View All Books
        </a>

    </div>

<?php endif; ?>

</div>


<!-- =========================
     PAGINATION
========================= -->

<?php if ($totalPages > 1): ?>

    <div class="pagination">

        <?php for (
            $i = 1;
            $i <= $totalPages;
            $i++
        ): ?>

            <?php

            $query =
                $_GET;

            $query['page'] =
                $i;

            ?>

            <a
                href="?<?= e(
                    http_build_query($query)
                ) ?>"
                class="<?= $i === $page
                    ? 'active'
                    : '' ?>"
            >

                <?= $i ?>

            </a>

        <?php endfor; ?>

    </div>

<?php endif; ?>


<?php

include 'partials/footer.php';

?>