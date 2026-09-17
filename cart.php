<?php

require_once 'functions.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

/*
|--------------------------------------------------------------------------
| Add / Update / Remove Cart
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!verifyCsrf($_POST['csrf'] ?? '')) {
        flash('error', 'Invalid security token.');
        redirect('cart.php');
    }

    $action = $_POST['action'] ?? '';

    if ($action === 'add') {

        $bookId = (int)($_POST['book_id'] ?? 0);
        $quantity = max(1, (int)($_POST['quantity'] ?? 1));

        $stmt = $pdo->prepare(
            "SELECT id, stock FROM books WHERE id = ?"
        );

        $stmt->execute([$bookId]);

        $book = $stmt->fetch();

        if (!$book) {

            flash('error', 'Book not found.');

        } elseif ((int)$book['stock'] <= 0) {

            flash('error', 'This book is out of stock.');

        } else {

            $currentQuantity =
                $_SESSION['cart'][$bookId] ?? 0;

            $newQuantity =
                $currentQuantity + $quantity;

            $newQuantity = min(
                $newQuantity,
                (int)$book['stock']
            );

            $_SESSION['cart'][$bookId] = $newQuantity;

            flash(
                'success',
                'Book added to your cart.'
            );
        }

    } elseif ($action === 'update') {

        foreach ($_POST['quantities'] ?? [] as $bookId => $quantity) {

            $bookId = (int)$bookId;
            $quantity = (int)$quantity;

            if ($quantity <= 0) {

                unset($_SESSION['cart'][$bookId]);

                continue;
            }

            $stmt = $pdo->prepare(
                "SELECT stock FROM books WHERE id = ?"
            );

            $stmt->execute([$bookId]);

            $book = $stmt->fetch();

            if ($book) {

                $quantity = min(
                    $quantity,
                    (int)$book['stock']
                );

                $_SESSION['cart'][$bookId] = $quantity;
            }
        }

        flash(
            'success',
            'Cart updated successfully.'
        );

    } elseif ($action === 'remove') {

        $bookId = (int)($_POST['book_id'] ?? 0);

        unset($_SESSION['cart'][$bookId]);

        flash(
            'success',
            'Book removed from cart.'
        );
    }

    redirect('cart.php');
}

/*
|--------------------------------------------------------------------------
| Get Cart Books
|--------------------------------------------------------------------------
*/

$cart = $_SESSION['cart'];

$books = [];
$total = 0;

if ($cart) {

    $ids = array_keys($cart);

    $placeholders = implode(
        ',',
        array_fill(0, count($ids), '?')
    );

    $stmt = $pdo->prepare(
        "SELECT *
         FROM books
         WHERE id IN ($placeholders)
         ORDER BY title"
    );

    $stmt->execute($ids);

    $books = $stmt->fetchAll();

    foreach ($books as $book) {

        $quantity = $cart[$book['id']] ?? 0;

        $total +=
            (float)$book['price'] * $quantity;
    }
}

$pageTitle = 'Shopping Cart';

include 'partials/header.php';
?>

<h1>🛒 Your Shopping Cart</h1>

<?php if (!$books): ?>

    <div class="card empty-state">

        <h2>Your cart is empty</h2>

        <p class="muted">
            You haven't added any books yet.
        </p>

        <a class="btn" href="books.php">
            Browse Books
        </a>

    </div>

<?php else: ?>

    <form method="post">

        <input
            type="hidden"
            name="csrf"
            value="<?= e(csrfToken()) ?>"
        >

        <input
            type="hidden"
            name="action"
            value="update"
        >

        <div class="cart-list">

            <?php foreach ($books as $book): ?>

                <?php
                $quantity = $cart[$book['id']] ?? 1;
                $subtotal = $book['price'] * $quantity;
                ?>

                <div class="card cart-item">

                    <img
                        src="<?= e($book['cover_url']) ?>"
                        alt="<?= e($book['title']) ?>"
                    >

                    <div class="cart-info">

                        <h2>
                            <?= e($book['title']) ?>
                        </h2>

                        <p class="muted">
                            By <?= e($book['author']) ?>
                        </p>

                        <p class="price">
                            ₹<?= number_format(
                                (float)$book['price'],
                                2
                            ) ?>
                        </p>

                    </div>

                    <div class="cart-quantity">

                        <label>
                            Quantity
                        </label>

                        <input
                            class="form-control"
                            type="number"
                            name="quantities[<?= (int)$book['id'] ?>]"
                            value="<?= $quantity ?>"
                            min="1"
                            max="<?= (int)$book['stock'] ?>"
                        >

                    </div>

                    <div class="cart-subtotal">

                        <strong>
                            ₹<?= number_format(
                                (float)$subtotal,
                                2
                            ) ?>
                        </strong>

                        <button
                            class="btn danger"
                            type="submit"
                            formaction="cart.php"
                            name="action"
                            value="remove"
                            onclick="this.form.book_id.value='<?= (int)$book['id'] ?>';"
                        >
                            Remove
                        </button>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>

        <input
            type="hidden"
            name="book_id"
            value=""
        >

        <div class="cart-actions">

            <button class="btn" type="submit">
                Update Cart
            </button>

            <a class="btn secondary" href="books.php">
                Continue Shopping
            </a>

        </div>

    </form>

    <div class="card cart-summary">

        <h2>Order Summary</h2>

        <div class="summary-row">

            <span>Subtotal</span>

            <strong>
                ₹<?= number_format($total, 2) ?>
            </strong>

        </div>

        <div class="summary-row">

            <span>Delivery</span>

            <strong>Free</strong>

        </div>

        <hr>

        <div class="summary-row total">

            <span>Total</span>

            <strong>
                ₹<?= number_format($total, 2) ?>
            </strong>

        </div>

        <br>

        <?php if (isLoggedIn()): ?>

            <a
                class="btn"
                href="checkout.php"
            >
                Proceed to Checkout →
            </a>

        <?php else: ?>

            <p class="muted">
                Please login to continue with your order.
            </p>

            <a
                class="btn"
                href="login.php"
            >
                Login to Checkout
            </a>

        <?php endif; ?>

    </div>

<?php endif; ?>

<?php include 'partials/footer.php'; ?>