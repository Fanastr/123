<form action="/admin.php/<?php echo $table_name ?>/create/" method="post">
    <h1>Добавление типа товаров</h1>
    <div>
        <input type="text" name="name" value="<?php echo $data["name"] ?? "" ?>" placeholder="Название" required>
        <input type="text" name="url" value="<?php echo $data["url"] ?? "" ?>" placeholder="Название для ссылки" required>
        <input type="submit" value="Добавить тип">
    </div>
</form>