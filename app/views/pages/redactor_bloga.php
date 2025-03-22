<section class="contactform-section">
    <h2>
        Редактор блога
    </h2>
    <div class="info-container">
        <div class="contact-form">
            <form action="/web/main/validateContact" method="POST" id="contactForm" style="position: sticky; z-index: 1000;">
                <!-- Фамилия -->
                <label for="msg_theme">Тема сообщения:</label>
                <input type="text" name="msg_theme" id="msg_theme" placeholder="Введите тему сообщения" class="contact_form-input">
                <?php if (isset($model['errors']['msg_theme'])): ?>
                    <?php echo $model['errors']['msg_theme']; ?>
                <?php endif; ?>

                <!-- Имя -->
                <label for="image">Изображение:</label>
                <input type="file" name="image" id="image" accept="image/*">

                <!-- Текст отзыва -->
                <label for="message">Текст отзыва:</label>
                <textarea name="message" id="message" rows="5" placeholder="Введите текст сообщения" class="contact_form-input"></textarea>
                <?php if (isset($model['errors']['message'])): ?>
                    <?php echo $model['errors']['message']; ?>
                <?php endif; ?>
                
                <!-- Кнопки "Отправить" и "Очистить форму" -->
                <div class="button-group">
                    <button type="submit">Отправить</button>
                    <button type="button" id="clear_contactform-btn">Очистить форму</button>
                </div>
                <?php if (isset($model['message']) && $model['message'] !== ''): ?>
                    <div class="success-message"><?php echo $model['message']; ?></div>
                <?php endif; ?>
            </form>
        </div>
    </div>
</section>

<section class="cur_view-section">
    <h2>
        Список записей
    </h2>
    <?php if (count($posts) > 0): ?>
        <?php foreach ($posts as $post): ?>
            <div>
                <h3><?php echo htmlspecialchars($post['topic']); ?></h3>
                <?php if ($post['image']): ?>
                    <img src="<?php echo htmlspecialchars($post['image']); ?>" alt="Изображение" width="200"><br>
                <?php endif; ?>
                <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
                <small>Дата: <?php echo $post['created_at']; ?></small>
            </div>
        <?php endforeach; ?>

        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?>">Предыдущая</a>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $model['totalPages']; $i++): ?>
                <a href="?page=<?php echo $i; ?>" <?php echo $i === $page ? 'style="font-weight: bold;"' : ''; ?>><?php echo $i; ?></a>
            <?php endfor; ?>
            <?php if ($page < $totalPages): ?>
                <a href="?page=<?php echo $page + 1; ?>">Следующая</a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <p>Записей пока нет</p>
    <?php endif; ?>
</section>