<form action="/admin.php/<?php echo $table_name ?>/create/" method="post">
    <h1>Добавление категории</h1>
    <div>
        <input type="text" name="name" value="<?php echo $data["name"] ?? "" ?>" placeholder="Название" required>
        <input type="submit" value="Добавить категорию">
    </div>
</form>