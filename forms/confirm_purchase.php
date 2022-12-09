<div id="confirm-purchase" <?php echo $product["id"] ?>>
    <p>Подтверждение покупки</p>
    <form action="" method="POST">
        <input type="hidden" name="id" value="<?php echo $product["id"] ?>">
        <p>Товар: <?php echo $product["name"] ?></p>
        <p>Цена: <?php echo $product["price"] ?> р.</p>
        <div>
            <button type="button" id="close-confirm-purchase">Отмена</button>
            <input type="submit" value="Купить" disabled>
        </div>
    </form>
</div>