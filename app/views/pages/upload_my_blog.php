<section class="contactform-section">
    <h2>
        Загрузка сообщений блога
    </h2>
    <div class="info-container">
        <div class="contact-form">
            <form action="/web/web9/main/validateContact" method="POST" enctype="multipart/form-data" id="contactForm" style="position: sticky; z-index: 1000;">
                <!-- Загрузка файла -->
                <label for="file">Загрузка CSV-файла:</label>
                <input type="file" id="csv_file" name="csv_file" accept=".csv" required>
                
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