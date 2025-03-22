<section class="contactform-section">
    <h2>
        Загрузка сообщений
    </h2>
    <div class="info-container">
        <div class="contact-form">
            <form action="/web/main/validateContact" method="POST" enctype="" id="contactForm" style="position: sticky; z-index: 1000;">
                <!-- Загрузка файла -->
                <label for="file">Загрузка файла:</label>
                <input type="file" name="message_file" accept=".inc" required>
                
                <!-- Кнопка "Загрузить" -->
                <button type="submit">Загрузить</button>

                <?php if (isset($model['message']) && $model['message'] !== ''): ?>
                    <div class="success-message"><?php echo $model['message']; ?></div>
                <?php else: ?>
                    <div class="error-message"><?php echo $model['error']; ?></div>
                <?php endif; ?>
            </form>
        </div>
    </div>
</section>