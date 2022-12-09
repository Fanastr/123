<form action="/admin.php/<?php echo $table_name ?>/update/" method="post">
    <h1>Редактирование типа товаров</h1>
    <div>
        <input type="hidden" name="id" value="<?php echo $data["id"] ?? "" ?>">
        <input type="text" name="name" value="<?php echo $data["name"] ?? "" ?>" placeholder="Название" required>
        <input type="text" name="url" value="<?php echo $data["url"] ?? "" ?>" placeholder="Название для ссылки" required>
        <input type="submit" value="Изменить">
        <a href="/admin.php/<?php echo $table_name ?>/remove?id=<?php echo $data['id'] ?? "" ?>" class="remove-link">Удалить</a>
    </div>
</form>