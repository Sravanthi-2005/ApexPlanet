<?php

$pageTitle = 'Home';

require_once 'functions.php';

include 'partials/header.php';

?>

<!-- =========================
     HERO SECTION
========================= -->

<section class="hero">

    <h1>
        Find Your Next
        <br>
        Great Book 📚
    </h1>

    <p>
        Discover programming, data science,
        computer science, self-help and
        many more books at BookNest.
    </p>

    <a
        href="books.php"
        class="btn"
    >
        Explore Books →
    </a>

</section>


<!-- =========================
     FEATURES
========================= -->

<div class="section-title">

    <h2>
        Why Choose BookNest?
    </h2>

</div>


<div class="grid grid-3">

    <div class="card">

        <h3>
            📚 Large Collection
        </h3>

        <p class="muted">
            Browse books from programming,
            AI, data science, self-help and
            computer science.
        </p>

    </div>


    <div class="card">

        <h3>
            🔎 Easy Search
        </h3>

        <p class="muted">
            Quickly find books using title,
            author or category.
        </p>

    </div>


    <div class="card">

        <h3>
            🛒 Easy Ordering
        </h3>

        <p class="muted">
            Add your favorite books to the
            cart and place orders easily.
        </p>

    </div>

</div>


<!-- =========================
     FEATURED BOOKS
========================= -->

<div class="section-title">

    <h2>
        Featured Books
    </h2>

    <a
        href="books.php"
        class="btn secondary"
    >
        View All Books
    </a>

</div>


<div class="grid grid-3">

<?php

$stmt = $pdo->query(
    "SELECT *
     FROM books
     ORDER BY id DESC
     LIMIT 6"
);

$books = $stmt->fetchAll();

foreach ($books as $book):

?>

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


            <a
                href="book.php?id=<?= (int) $book['id'] ?>"
                class="btn"
            >

                View Details

            </a>

        </div>

    </div>

<?php endforeach; ?>

</div>


<!-- =========================
     CALL TO ACTION
========================= -->

<section class="hero">

    <h2>
        Ready to Start Reading?
    </h2>

    <p>
        Explore our collection and
        find the perfect book for you.
    </p>

    <a
        href="books.php"
        class="btn"
    >
        Browse All Books
    </a>

</section>


<?php

include 'partials/footer.php';

?>