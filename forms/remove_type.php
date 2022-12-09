<form action="/admin.php/<?php echo $table_name ?>/remove/" method="post">
    <h1>Удаление категории</h1>
    <div>
        <input type="hidden" name="id" value="<?php echo $data["id"] ?? "" ?>">
        <input type="text" name="name" disabled value="<?php echo $data["name"] ?? "" ?>" placeholder="Название">
        <input type="text" name="url" disabled value="<?php echo $data["url"] ?? "" ?>" placeholder="Название для ссылки">
        <input type="submit" value="Удалить">
    </div>
</form>