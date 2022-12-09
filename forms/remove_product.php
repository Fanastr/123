<form action="/admin.php/<?php echo $table_name ?>/remove/" method="post">
    <h1>Удаление товара</h1>
    <div>
        <input type="hidden" name="id" value="<?php echo $data["id"] ?? "" ?>">
        <input type="text" name="name" value="<?php echo $data["name"] ?? "" ?>" placeholder="Название" required>
        <textarea name="description" cols="30" rows="15" placeholder="Описание" required><?php echo $data["description"] ?? "" ?></textarea>
        <input type="number" name="price" value="<?php echo $data["price"] ?? "" ?>" placeholder="Цена" required>
        <div class="radio-container">
            <label>Тип</label>
            <?php
                $type_id = $data["type_id"] ?? -1;
                foreach($types as $value) {
                    if ($value["id"] == $type_id) {
                        echo <<< EOT
                        <label><input type="radio" checked value="{$value['id']}" name="type_id"><span>{$value['name']}</span></label>
                        EOT;
                        break;
                    };
                };
            ?>
        </div>
        <div class="radio-container">
            <label>Категория</label>
            <?php
                $category_id = $data["category_id"] ?? -1;
                foreach($categories as $category) {
                    if ($category["id"] == $category_id) {
                        echo <<< EOT
                        <label><input type="radio" checked value="{$category['id']}" name="category_id"><span>{$category['name']}</span></label>
                        EOT;
                        break;
                    };
                };
            ?>
        </div>
        <input type="submit" value="Удалить">
    </div>
</form>